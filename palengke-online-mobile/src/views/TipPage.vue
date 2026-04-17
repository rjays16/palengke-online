<template>
  <ion-page>
    <ion-content :fullscreen="true" class="tip-content">

      <!-- Header -->
      <div class="tip-header">
        <div class="back-btn" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>
        <div class="header-right">
          <span class="get-help" @click="getHelp">Get help</span>
          <ion-icon :icon="shareOutline" @click="shareOrder" class="share-icon"></ion-icon>
        </div>
      </div>

      <!-- Tip Section -->
      <div class="tip-section">
        <div class="tip-title-row">
          <div>
            <h2 class="tip-title">Show your appreciation by adding a tip.</h2>
            <p class="tip-subtitle">100% of the tip goes to drivers.</p>
          </div>
          <ion-icon
            :icon="showTipInfo ? chevronUpOutline : chevronDownOutline"
            class="tip-toggle"
            @click="showTipInfo = !showTipInfo"
          ></ion-icon>
        </div>

        <!-- Tip Info -->
        <div class="tip-info" v-if="showTipInfo">
          <p>Your tip goes directly to the driver who delivers your order. It's a great way to show appreciation for great service!</p>
        </div>

        <!-- Tip Amounts Grid -->
        <div class="tip-grid">
          <div
            v-for="amount in tipAmounts"
            :key="amount"
            class="tip-amount-btn"
            :class="{ selected: selectedTip === amount && !isCustom }"
            @click="selectTip(amount)"
          >
            ₱{{ amount.toFixed(2) }}
          </div>
        </div>

        <!-- Custom Amount -->
        <div class="custom-amount-row">
          <span class="custom-link" @click="toggleCustom">
            {{ isCustom ? 'Use preset amount' : 'Enter custom amount' }}
          </span>
        </div>

        <!-- Custom Input -->
        <div class="custom-input-wrap" v-if="isCustom">
          <span class="peso-sign">₱</span>
          <input
            v-model="customAmount"
            type="number"
            placeholder="0.00"
            class="custom-input"
            min="1"
          >
        </div>

        <!-- No Tip Option -->
        <div
          class="no-tip-btn"
          :class="{ selected: selectedTip === 0 && !isCustom }"
          @click="selectNoTip"
        >
          No tip this time
        </div>
      </div>

      <!-- Store Info Card -->
      <div class="store-info-card">
        <img :src="orderData.storeImage" class="store-avatar-img">
        <div class="store-details">
          <h4>{{ orderData.storeName }}</h4>
          <div class="save-fav" @click="toggleFavorite">
            <ion-icon :icon="isFavorite ? heart : heartOutline"
              :style="{ color: isFavorite ? '#f44336' : '#4CAF50' }"></ion-icon>
            <span>{{ isFavorite ? 'Saved to Favourites' : 'Save this store to Favourites' }}</span>
          </div>
        </div>
      </div>

      <!-- Payment Info -->
      <div class="payment-info-card">
        <div class="payment-row">
          <img :src="orderData.paymentImage" class="payment-icon-img">
          <span class="payment-text">{{ orderData.paymentLabel }}</span>
        </div>
        <div class="total-row">
          <span class="total-label">Total</span>
          <span class="total-amount">₱{{ grandTotal.toFixed(2) }}</span>
        </div>
        <div class="view-summary" @click="viewOrderSummary">
          <span>View order summary</span>
          <ion-icon :icon="chevronForwardOutline"></ion-icon>
        </div>
      </div>

      <!-- Delivery Route -->
      <div class="delivery-route-card">
        <div class="route-item">
          <div class="route-dot pickup"></div>
          <div class="route-info">
            <p class="route-name">{{ orderData.storeName }}</p>
            <p class="route-address">{{ orderData.storeAddress }}</p>
          </div>
        </div>
        <div class="route-line"></div>
        <div class="route-item">
          <div class="route-dot delivery"></div>
          <div class="route-info">
            <p class="route-name">{{ orderData.deliveryAddress }}</p>
            <p class="route-address">{{ orderData.deliveryAddressSub }}</p>
          </div>
        </div>
        <!-- Floor/Unit -->
        <div class="floor-row">
          <span class="floor-placeholder">Floor / unit no.</span>
          <span class="floor-add" @click="addFloor">Add</span>
        </div>
      </div>

      <div style="height: 100px;"></div>

    </ion-content>

    <!-- Place Order Bottom Bar -->
    <div class="place-order-bar">
      <ion-button
        expand="block"
        class="place-order-btn"
        @click="confirmOrder"
        :disabled="isPlacing"
      >
        <span v-if="!isPlacing">Place Order</span>
        <ion-spinner v-else name="crescent"></ion-spinner>
      </ion-button>
    </div>

    <!-- Success Modal -->
    <div class="custom-modal-overlay" v-if="showSuccessModal" @click="closeSuccessModal">
      <div class="custom-modal" @click.stop>
        <img src="/assets/images/checkmark_success.png" alt="Success" class="success-img">
        <h3>Order placed successfully!</h3>
        <p>Your order has been confirmed</p>
        <ion-button expand="block" class="modal-btn" @click="closeSuccessModal">OK</ion-button>
      </div>
    </div>

  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonContent, IonButton, IonIcon, IonSpinner,
  alertController, toastController
} from '@ionic/vue';
import {
  arrowBackOutline, shareOutline, chevronUpOutline,
  chevronDownOutline, chevronForwardOutline,
  heartOutline, heart
} from 'ionicons/icons';

const router = useRouter();

const showTipInfo = ref(false);
const selectedTip = ref(50);
const isCustom = ref(false);
const customAmount = ref('');
const isFavorite = ref(false);
const isPlacing = ref(false);
const showSuccessModal = ref(false);

const tipAmounts = [25, 50, 100, 150, 200, 250];

const orderData = ref({
  storeName: "Juan's Fresh Market",
  storeEmoji: '',
  storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e',
  storeAddress: 'Stall #15, CDO City Palengke',
  paymentIcon: '',
  paymentLabel: 'GCash',
  paymentImage: '/assets/images/payment-methods-logo/gcash.png',
  subtotal: 162.00,
  deliveryFee: 45.00,
  deliveryAddress: 'Brgy. Carmen, CDO City',
  deliveryAddressSub: 'Cagayan de Oro City, Misamis Oriental'
});

const grandTotal = computed(() => {
  const tip = isCustom.value ? (parseFloat(customAmount.value) || 0) : selectedTip.value;
  return orderData.value.subtotal + orderData.value.deliveryFee + tip;
});

const selectTip = (amount) => {
  selectedTip.value = amount;
  isCustom.value = false;
};

const selectNoTip = () => {
  selectedTip.value = 0;
  isCustom.value = false;
};

const toggleCustom = () => {
  isCustom.value = !isCustom.value;
  if (!isCustom.value) selectedTip.value = 50;
};

const toggleFavorite = () => { isFavorite.value = !isFavorite.value; };

const viewOrderSummary = async () => {
  const alert = await alertController.create({
    header: 'Order Summary',
    message: `Subtotal: ₱${orderData.value.subtotal.toFixed(2)}\nDelivery Fee: ₱${orderData.value.deliveryFee.toFixed(2)}\nTip: ₱${selectedTip.value.toFixed(2)}\nTotal: ₱${grandTotal.value.toFixed(2)}`,
    buttons: ['Close']
  });
  await alert.present();
};

const addFloor = async () => {
  const alert = await alertController.create({
    header: 'Floor / Unit No.',
    inputs: [{ name: 'floor', type: 'text', placeholder: 'e.g. Unit 4B, Floor 2' }],
    buttons: [{ text: 'Cancel', role: 'cancel' }, { text: 'Add', handler: () => {} }]
  });
  await alert.present();
};

const getHelp = () => router.push('/help');
const shareOrder = () => {};

const confirmOrder = async () => {
  isPlacing.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 2000));
    showSuccessModal.value = true;
  } finally {
    isPlacing.value = false;
  }
};

const closeSuccessModal = () => {
  showSuccessModal.value = false;
  router.push('/order-tracking/1234');
};

const goBack = () => router.back();
</script>

<style scoped>
.tip-content { --background: #f8f9fa; }

/* Header */
.tip-header {
  background: white; padding: 52px 20px 16px;
  display: flex; justify-content: space-between; align-items: center;
  border-bottom: 1px solid #f0f0f0;
}
.back-btn {
  width: 36px; height: 36px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
}
.back-btn ion-icon { font-size: 26px; color: #1a1a1a; }
.header-right { display: flex; align-items: center; gap: 16px; }
.get-help { font-size: 14px; color: #4CAF50; font-weight: 600; cursor: pointer; }
.share-icon { font-size: 22px; color: #1a1a1a; cursor: pointer; }

/* Tip Section */
.tip-section {
  background: white; padding: 20px;
  border-bottom: 1px solid #f0f0f0;
  margin-bottom: 8px;
}

.tip-title-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
.tip-title { font-size: 17px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; line-height: 1.4; }
.tip-subtitle { font-size: 13px; color: #666; margin: 0; }
.tip-toggle { font-size: 22px; color: #666; cursor: pointer; flex-shrink: 0; margin-top: 4px; }

.tip-info {
  background: #f8f9fa; border-radius: 12px;
  padding: 12px 14px; margin-bottom: 16px;
}
.tip-info p { font-size: 13px; color: #555; margin: 0; line-height: 1.5; }

/* Tip Grid */
.tip-grid {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 10px; margin-bottom: 12px;
}

.tip-amount-btn {
  padding: 14px 8px; border: 2px solid #e0e0e0;
  border-radius: 12px; text-align: center;
  font-size: 15px; font-weight: 700; color: #1a1a1a;
  cursor: pointer; transition: all 0.2s;
  background: white;
}
.tip-amount-btn:active { transform: scale(0.97); }
.tip-amount-btn.selected {
  border-color: #1a1a1a; background: #1a1a1a; color: white;
}

/* Custom Amount */
.custom-amount-row { text-align: center; margin-bottom: 12px; }
.custom-link { font-size: 14px; color: #4CAF50; font-weight: 600; cursor: pointer; }

.custom-input-wrap {
  display: flex; align-items: center; gap: 8px;
  background: #f5f5f5; border-radius: 12px;
  padding: 12px 16px; margin-bottom: 12px;
  border: 2px solid #4CAF50;
}
.peso-sign { font-size: 18px; font-weight: 700; color: #1a1a1a; }
.custom-input {
  flex: 1; background: transparent; border: none;
  font-size: 18px; font-weight: 700; font-family: inherit;
  color: #1a1a1a !important; -webkit-text-fill-color: #1a1a1a !important;
}
.custom-input:focus { outline: none; }
.custom-input::placeholder { color: #999; font-weight: 400; }

/* No Tip */
.no-tip-btn {
  text-align: center; padding: 14px;
  border: 2px solid #e0e0e0; border-radius: 12px;
  font-size: 14px; font-weight: 600; color: #666;
  cursor: pointer; transition: all 0.2s; background: white;
}
.no-tip-btn.selected { border-color: #1a1a1a; color: #1a1a1a; background: #f5f5f5; }

/* Store Info Card */
.store-info-card {
  background: white; padding: 20px;
  margin-bottom: 8px;
  border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;
  display: flex; align-items: flex-start; gap: 14px;
}
.store-avatar {
  width: 56px; height: 56px; background: #E8F5E9;
  border-radius: 14px; display: flex; align-items: center;
  justify-content: center; font-size: 30px; flex-shrink: 0;
}
.store-avatar-img {
  width: 56px; height: 56px; border-radius: 14px; object-fit: cover;
}
.store-details { flex: 1; }
.store-details h4 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.save-fav { display: flex; align-items: center; gap: 8px; cursor: pointer; }
.save-fav ion-icon { font-size: 20px; }
.save-fav span { font-size: 13px; color: #4CAF50; font-weight: 600; }

/* Payment Info Card */
.payment-info-card {
  background: white; padding: 20px;
  margin-bottom: 8px;
  border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;
}
.payment-row { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.payment-icon-wrap { font-size: 28px; }
.payment-icon-img { width: 40px; height: 28px; object-fit: contain; border-radius: 6px; }
.payment-text { font-size: 15px; font-weight: 600; color: #1a1a1a; }
.total-row { display: flex; justify-content: space-between; margin-bottom: 14px; }
.total-label { font-size: 16px; color: #555; }
.total-amount { font-size: 20px; font-weight: 800; color: #1a1a1a; }
.view-summary {
  display: flex; align-items: center; justify-content: space-between;
  padding-top: 14px; border-top: 1px solid #f0f0f0;
  cursor: pointer;
}
.view-summary span { font-size: 14px; color: #4CAF50; font-weight: 600; }
.view-summary ion-icon { font-size: 18px; color: #4CAF50; }

/* Delivery Route */
.delivery-route-card {
  background: white; padding: 20px;
  border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;
}
.route-item { display: flex; align-items: flex-start; gap: 12px; }
.route-dot {
  width: 14px; height: 14px; border-radius: 50%;
  flex-shrink: 0; margin-top: 3px;
}
.route-dot.pickup { background: #4CAF50; }
.route-dot.delivery { background: #f44336; }
.route-info { flex: 1; }
.route-name { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.route-address { font-size: 13px; color: #666; margin: 0; line-height: 1.4; }
.route-line {
  width: 2px; height: 24px; background: #e0e0e0;
  margin: 6px 0 6px 6px;
}
.floor-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px; background: #f8f9fa; border-radius: 12px;
  margin-top: 14px; border: 1px solid #e0e0e0;
}
.floor-placeholder { font-size: 14px; color: #999; }
.floor-add { font-size: 14px; color: #4CAF50; font-weight: 700; cursor: pointer; }

/* Place Order Bar */
.place-order-bar {
  position: fixed; bottom: 0; left: 0; right: 0;
  background: white; padding: 16px 20px;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.1); z-index: 1000;
}
.place-order-btn {
  --background: #4CAF50; --border-radius: 16px;
  height: 56px; font-size: 16px; font-weight: 700; text-transform: none;
}

.success-alert .alert-message {
  text-align: center;
  padding: 20px;
}

/* Custom Modal */
.custom-modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.custom-modal {
  background: white;
  border-radius: 20px;
  padding: 40px 30px;
  text-align: center;
  width: 80%;
  max-width: 320px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.success-img {
  width: 80px;
  height: 80px;
  margin-bottom: 16px;
}

.custom-modal h3 {
  margin: 0 0 8px;
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
}

.custom-modal p {
  margin: 0 0 24px;
  font-size: 14px;
  color: #666;
}

.modal-btn {
  --background: #4CAF50;
  --border-radius: 12px;
  height: 48px;
  font-weight: 600;
  text-transform: none;
}
</style>