<template>
  <ion-page>
    <ion-content :fullscreen="true" class="checkout-content">

      <!-- Header -->
      <div class="checkout-header">
        <div class="back-btn" @click="goBack">
          <ion-icon :icon="closeOutline"></ion-icon>
        </div>
        <div class="header-info">
          <h2>{{ storeData.name }}</h2>
          <p>Delivery fee calculated at {{ currentTime }}</p>
        </div>
      </div>

      <!-- ─── Order Summary ──────────────────── -->
      <div class="section-card">
        <div class="section-top">
          <h3>Order summary</h3>
          <span class="add-items-btn" @click="goBackToStore">Add items</span>
        </div>

        <div class="order-items">
          <div v-for="item in orderItems" :key="item.id" class="order-item">
            <img :src="item.image" :alt="item.name" class="item-img">
            <div class="item-info">
              <p class="item-name">{{ item.name }}</p>
              <p class="item-variant" v-if="item.variant">{{ item.variant }}</p>
              <span class="edit-btn" @click="editItem(item)">Edit</span>
            </div>
            <div class="item-right">
              <p class="item-price">{{ item.price.toFixed(2) }}</p>
              <div class="item-qty-circle">{{ item.quantity }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── People Also Ordered ───────────── -->
      <div class="section-card">
        <h3>People also ordered</h3>
        <div class="suggestions-scroll">
          <div
            v-for="item in suggestedItems"
            :key="item.id"
            class="suggestion-card"
          >
            <img :src="item.image" :alt="item.name" class="suggestion-img" @error="handleImageError($event)">
            <div class="suggestion-add" @click="addSuggestion(item)">
              <ion-icon :icon="addOutline"></ion-icon>
            </div>
            <p class="suggestion-name">{{ item.name }}</p>
            <p class="suggestion-price">{{ item.price.toFixed(2) }}</p>
          </div>
        </div>
      </div>

      <!-- ─── Delivery / Pickup Toggle ──────── -->
      <div class="section-card">
        <div class="delivery-toggle">
          <div
            class="toggle-option"
            :class="{ active: deliveryMode === 'delivery' }"
            @click="deliveryMode = 'delivery'"
          >
            Delivery
          </div>
          <div
            class="toggle-option"
            :class="{ active: deliveryMode === 'pickup' }"
            @click="deliveryMode = 'pickup'"
          >
            Pickup
          </div>
        </div>

        <!-- Delivery Address -->
        <div v-if="deliveryMode === 'delivery'" class="address-section">
          <div class="address-row" @click="changeAddress">
            <div class="address-dot">📍</div>
            <div class="address-info">
              <p class="address-main">{{ deliveryAddress.main }}</p>
              <p class="address-sub">{{ deliveryAddress.sub }}</p>
            </div>
            <ion-icon :icon="chevronForwardOutline"></ion-icon>
          </div>
          <div class="floor-input" @click="addFloorUnit">
            <span class="floor-placeholder">Floor / unit no.</span>
            <span class="floor-hint">Helps with delivery</span>
            <span class="floor-add">Add</span>
          </div>
        </div>

        <!-- Pickup Info -->
        <div v-if="deliveryMode === 'pickup'" class="pickup-section">
          <div class="pickup-row">
            <div class="address-dot">📍</div>
            <div class="address-info">
              <p class="address-main">{{ storeData.address }}</p>
              <p class="address-sub">{{ storeData.distance }} away</p>
            </div>
            <ion-icon :icon="chevronForwardOutline"></ion-icon>
          </div>
          <div class="pickup-notice">
            <span class="pickup-time-icon"><ion-icon :icon="timeOutline"></ion-icon></span>
            <strong>Pick up in {{ storeData.pickupTime }} mins</strong>
          </div>
          <div class="pickup-reminder">
            <div class="pickup-reminder-icon">📦</div>
            <div>
              <p><strong>This is a pickup</strong></p>
              <p>Remember to collect your food at the store when it's ready!</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── Delivery Options ───────────────── -->
      <div class="section-card" v-if="deliveryMode === 'delivery'">
        <div class="delivery-options-header">
          <span class="delivery-icon-wrap"><ion-icon :icon="bicycleOutline"></ion-icon></span>
          <div>
            <p class="delivery-options-title">Delivery options</p>
            <p class="delivery-options-sub">Distance from you: {{ storeData.distance }}</p>
          </div>
        </div>

        <div class="delivery-options">
          <div
            v-for="option in deliveryOptions"
            :key="option.id"
            class="delivery-option"
            :class="{ selected: selectedDelivery === option.id }"
            @click="selectDelivery(option)"
          >
            <div class="option-left">
              <p class="option-name">{{ option.name }}</p>
              <p class="option-time">• {{ option.time }} mins
                <span class="info-icon" @click.stop="showDeliveryInfo(option)">ⓘ</span>
              </p>
              <p class="option-promise" v-if="option.promise">{{ option.promise }}</p>
            </div>
            <p class="option-price">{{ option.price === 0 ? 'Free' : option.price.toFixed(2) }}</p>
          </div>
        </div>
      </div>

      <!-- ─── Payment Details ────────────────── -->
      <div class="section-card">
        <div class="payment-header">
          <h3>Payment details</h3>
          <span class="see-all-btn" @click="goToPaymentMethods">See all</span>
        </div>
        <div class="payment-method" @click="goToPaymentMethods">
          <img :src="selectedPayment.image" alt="Payment" class="payment-icon-img">
          <ion-icon :icon="chevronForwardOutline" class="payment-arrow"></ion-icon>
        </div>
      </div>

      <!-- ─── Use Offers ─────────────────────── -->
      <div class="section-card">
        <h3>Use offers and more</h3>
        <div class="offer-row" @click="goToOffers">
          <span class="offer-icon"><ion-icon :icon="flagOutline"></ion-icon></span>
          <span class="offer-label">Offers</span>
          <ion-icon :icon="chevronForwardOutline"></ion-icon>
        </div>
        <div class="offer-row" v-if="appliedPromo">
          <span class="offer-icon"></span>
          <span class="offer-label promo-applied">{{ appliedPromo }}</span>
          <span class="remove-promo" @click="appliedPromo = ''">Remove</span>
        </div>
      </div>

      <!-- ─── Order Total ────────────────────── -->
      <div class="section-card totals-card">
        <div class="total-row">
          <span>Subtotal</span>
          <span>₱{{ subtotal.toFixed(2) }}</span>
        </div>
        <div class="total-row" v-if="deliveryMode === 'delivery'">
          <span>Delivery fee</span>
          <span :class="{ free: currentDeliveryFee === 0 }">
            {{ currentDeliveryFee === 0 ? 'Free' : '₱' + currentDeliveryFee.toFixed(2) }}
          </span>
        </div>
        <div class="total-row discount" v-if="discount > 0">
          <span>Discount</span>
          <span>- ₱{{ discount.toFixed(2) }}</span>
        </div>
        <div class="total-divider"></div>
        <div class="total-row grand-total">
          <span>Total</span>
          <span>₱{{ grandTotal.toFixed(2) }}</span>
        </div>
      </div>

      <div style="height: 100px;"></div>

    </ion-content>

    <!-- ─── Place Order Button ─────────────── -->
    <div class="place-order-bar">
      <ion-button
        expand="block"
        class="place-order-btn"
        @click="placeOrder"
        :disabled="isPlacingOrder"
      >
        <span v-if="!isPlacingOrder">
          {{ deliveryMode === 'pickup' ? 'Place Order - Pickup' : 'Place Order' }}
        </span>
        <ion-spinner v-else name="crescent"></ion-spinner>
      </ion-button>
    </div>

  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage, IonContent, IonButton, IonIcon, IonSpinner,
  alertController, toastController
} from '@ionic/vue';
import {
  closeOutline, addOutline, chevronForwardOutline, timeOutline, bicycleOutline, flagOutline
} from 'ionicons/icons';

const router = useRouter();
const route = useRoute();

const deliveryMode = ref('delivery');
const selectedDelivery = ref('standard');
const isPlacingOrder = ref(false);
const appliedPromo = ref('');

const currentTime = ref(new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' }));

// Store Data
const storeData = ref({
  id: route.params.id || 1,
  name: "Juan's Fresh Market",
  address: 'Stall #15, CDO City Palengke',
  distance: '2.3 km',
  pickupTime: '13',
  image: 'https://images.unsplash.com/photo-1542838132-92c53300491e'
});

// Order Items
const orderItems = ref([
  { id: 1, name: 'Bawang/Garlic', emoji: '', image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999', price: 66.00, quantity: 1, variant: '250g' },
  { id: 2, name: 'Kamatis/Tomato', emoji: '', image: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea', price: 48.00, quantity: 2, variant: '250g' },
]);

// Suggested Items
const suggestedItems = ref([
  { id: 5, name: 'Sibuyas/Onion', emoji: '', image: 'https://images.unsplash.com/photo-1607305387299-a3d9611cd469', price: 110.00 },
  { id: 6, name: 'Luya/Ginger', emoji: '', image: 'https://images.unsplash.com/photo-1615485500704-8e990f9900f7', price: 73.00 },
  { id: 7, name: 'Pechay', emoji: '', image: 'https://images.unsplash.com/photo-1582284540020-8acbe03f4924', price: 66.00 },
]);

// Delivery Address
const deliveryAddress = ref({
  main: 'Brgy. Carmen, CDO City',
  sub: 'Cagayan de Oro City, Misamis Oriental'
});

// Delivery Options
const deliveryOptions = ref([
  { id: 'standard', name: 'Standard', time: '25-35', price: 45, promise: null },
  { id: 'express', name: 'Express', time: '15-20', price: 75, promise: '⚡ Faster delivery' },
  { id: 'saver', name: 'Saver', time: '40-55', price: 30, promise: null },
]);

// Payment
const selectedPayment = ref({ icon: 'card', label: 'GCash', image: '/assets/images/payment-methods-logo/gcash.png' });

// Computed
const subtotal = computed(() =>
  orderItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
);

const currentDeliveryFee = computed(() => {
  if (deliveryMode.value === 'pickup') return 0;
  const opt = deliveryOptions.value.find(o => o.id === selectedDelivery.value);
  return opt ? opt.price : 45;
});

const discount = computed(() => appliedPromo.value ? 50 : 0);

const grandTotal = computed(() =>
  subtotal.value + currentDeliveryFee.value - discount.value
);

// Methods
const selectDelivery = (option) => { selectedDelivery.value = option.id; };

const handleImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/110x90?text=Image';
};

const showDeliveryInfo = async (option) => {
  const messages = {
    express: 'Express delivery gets your order faster with priority handling.',
    saver: 'Saver delivery saves on delivery fees but takes a little longer.',
    standard: 'Standard delivery at regular speed and price.'
  };
  const alert = await alertController.create({
    header: `${option.name} Delivery`,
    message: messages[option.id],
    buttons: ['Got it!']
  });
  await alert.present();
};

const editItem = async (item) => {
  const alert = await alertController.create({
    header: item.name,
    inputs: [{ name: 'qty', type: 'number', value: item.quantity, min: 1 }],
    buttons: [
      { text: 'Remove', role: 'destructive', handler: () => {
        orderItems.value = orderItems.value.filter(i => i.id !== item.id);
      }},
      { text: 'Cancel', role: 'cancel' },
      { text: 'Update', handler: (data) => {
        item.quantity = parseInt(data.qty) || 1;
      }}
    ]
  });
  await alert.present();
};

const addSuggestion = async (item) => {
  const existing = orderItems.value.find(i => i.id === item.id);
  if (existing) { existing.quantity++; }
  else { orderItems.value.push({ ...item, quantity: 1, variant: '' }); }
  const toast = await toastController.create({
    message: `${item.name} added!`,
    duration: 1500, color: 'success', position: 'top'
  });
  await toast.present();
};

const changeAddress = () => router.push('/addresses');

const addFloorUnit = async () => {
  const alert = await alertController.create({
    header: 'Floor / Unit No.',
    inputs: [{ name: 'floor', type: 'text', placeholder: 'e.g. Unit 4B, Floor 2' }],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Add', handler: () => {} }
    ]
  });
  await alert.present();
};

const placeOrder = async () => {
  if (orderItems.value.length === 0) return;
  isPlacingOrder.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 2000));
    router.push('/tip');
  } finally {
    isPlacingOrder.value = false;
  }
};

const goBack = () => router.back();
const goBackToStore = () => router.push(`/seller/${storeData.value.id}`);
const goToPaymentMethods = () => router.push('/payment-methods');
const goToOffers = () => router.push('/offers');
</script>

<style scoped>
.checkout-content { --background: #f8f9fa; }

/* Header */
.checkout-header {
  background: white; padding: 52px 16px 16px;
  display: flex; align-items: center; gap: 14px;
  border-bottom: 1px solid #f0f0f0;
}
.back-btn {
  width: 40px; height: 40px; background: #f5f5f5;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; cursor: pointer; flex-shrink: 0;
}
.back-btn ion-icon { font-size: 22px; color: #1a1a1a; }
.header-info h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.header-info p { font-size: 12px; color: #999; margin: 0; }

/* Section Card */
.section-card {
  background: white; margin: 8px 0; padding: 20px;
  border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;
}
.section-card h3 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }

/* Section Top */
.section-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.section-top h3 { margin: 0; }
.add-items-btn { font-size: 14px; color: #4CAF50; font-weight: 600; cursor: pointer; }

/* Order Items */
.order-items { display: flex; flex-direction: column; gap: 16px; }
.order-item { display: flex; align-items: flex-start; gap: 14px; }
.order-item .item-img {
  width: 56px; height: 56px;
  border-radius: 12px; object-fit: cover;
  flex-shrink: 0;
  border: 1px solid #f0f0f0;
}
.item-info { flex: 1; }
.item-name { font-size: 15px; font-weight: 600; color: #1a1a1a; margin: 0 0 3px 0; }
.item-variant { font-size: 13px; color: #999; margin: 0 0 4px 0; }
.edit-btn { font-size: 13px; color: #4CAF50; font-weight: 600; cursor: pointer; }
.item-right { text-align: right; }
.item-price { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 6px 0; }
.item-qty-circle {
  width: 28px; height: 28px; border: 2px solid #1a1a1a;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; font-size: 13px; font-weight: 700;
  margin-left: auto;
}

/* Suggestions */
.suggestions-scroll { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 4px; }
.suggestions-scroll::-webkit-scrollbar { display: none; }
.suggestion-card { flex-shrink: 0; width: 110px; position: relative; }
.suggestion-img {
  width: 110px; height: 90px;
  border-radius: 12px; object-fit: cover;
  margin-bottom: 6px;
  border: 1px solid #f0f0f0;
  background-color: #f5f5f5;
  display: block;
}
.suggestion-add {
  position: absolute; top: 56px; right: 4px;
  width: 28px; height: 28px; background: #4CAF50;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; cursor: pointer;
  box-shadow: 0 2px 8px rgba(76,175,80,0.4);
}
.suggestion-add ion-icon { font-size: 18px; color: white; }
.suggestion-name { font-size: 12px; font-weight: 600; color: #1a1a1a; margin: 0 0 2px 0; }
.suggestion-price { font-size: 13px; font-weight: 800; color: #1a1a1a; margin: 0; }

/* Delivery Toggle */
.delivery-toggle {
  display: flex; background: #f0f0f0; border-radius: 12px;
  padding: 4px; margin-bottom: 16px; gap: 4px;
}
.toggle-option {
  flex: 1; text-align: center; padding: 10px;
  border-radius: 10px; font-size: 14px; font-weight: 700;
  color: #666; cursor: pointer; transition: all 0.2s;
}
.toggle-option.active { background: white; color: #1a1a1a; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

/* Address */
.address-row {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 12px 0; cursor: pointer;
  border-bottom: 1px solid #f5f5f5;
}
.address-dot { font-size: 22px; flex-shrink: 0; }
.address-info { flex: 1; }
.address-main { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.address-sub { font-size: 13px; color: #666; margin: 0; }
.address-row ion-icon { font-size: 20px; color: #ccc; margin-top: 4px; }

.floor-input {
  display: flex; align-items: center; gap: 8px;
  padding: 14px; background: #f8f9fa;
  border-radius: 12px; margin-top: 12px; cursor: pointer;
  border: 1px solid #e0e0e0;
}
.floor-placeholder { font-size: 14px; color: #999; flex: 1; }
.floor-hint { font-size: 12px; color: #4CAF50; font-weight: 600; }
.floor-add { font-size: 14px; color: #4CAF50; font-weight: 700; margin-left: 8px; }

/* Pickup */
.pickup-row {
  display: flex; align-items: flex-start; gap: 12px;
  padding-bottom: 12px; border-bottom: 1px solid #f5f5f5; cursor: pointer;
}
.pickup-notice {
  display: flex; align-items: center; gap: 8px;
  padding: 14px 0; font-size: 15px; font-weight: 700; color: #1a1a1a;
}
.pickup-time-icon { font-size: 20px; }
.pickup-reminder {
  display: flex; gap: 12px; background: #FFF3E0;
  border-radius: 14px; padding: 14px; margin-top: 4px;
}
.pickup-reminder-icon { font-size: 32px; flex-shrink: 0; }
.pickup-reminder p { font-size: 13px; color: #E65100; margin: 0 0 3px 0; line-height: 1.4; }
.pickup-reminder p:first-child { margin-bottom: 4px; }

/* Delivery Options */
.delivery-options-header {
  display: flex; align-items: center; gap: 12px; margin-bottom: 16px;
}
.delivery-icon-wrap { font-size: 28px; }
.delivery-options-title { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0 0 2px 0; }
.delivery-options-sub { font-size: 12px; color: #666; margin: 0; }

.delivery-options { display: flex; flex-direction: column; gap: 10px; }
.delivery-option {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px 16px; border-radius: 12px; border: 2px solid #e0e0e0;
  cursor: pointer; transition: all 0.2s;
}
.delivery-option.selected { border-color: #4CAF50; background: #F1F8E9; }
.option-left { flex: 1; }
.option-name { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.option-time { font-size: 13px; color: #666; margin: 0 0 3px 0; display: flex; align-items: center; gap: 6px; }
.info-icon { color: #4CAF50; font-size: 14px; cursor: pointer; }
.option-promise { font-size: 12px; color: #4CAF50; font-weight: 600; margin: 0; }
.option-price { font-size: 16px; font-weight: 700; color: #1a1a1a; }

/* Payment */
.payment-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.payment-header h3 { margin: 0; }
.see-all-btn { font-size: 14px; color: #4CAF50; font-weight: 600; cursor: pointer; }
.payment-method { display: flex; align-items: center; justify-content: space-between; gap: 12px; cursor: pointer; padding: 12px 0; }
.payment-icon-img { width: 140px; height: 60px; object-fit: contain; border-radius: 10px; background: #f5f5f5; padding: 8px; }
.payment-icon { font-size: 28px; }
.payment-label { flex: 1; font-size: 15px; font-weight: 600; color: #1a1a1a; }
.payment-arrow { font-size: 18px; color: #ccc; }

/* Offers */
.offer-row {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 0; border-bottom: 1px solid #f5f5f5; cursor: pointer;
}
.offer-row:last-child { border-bottom: none; }
.offer-icon { font-size: 24px; }
.offer-label { flex: 1; font-size: 15px; font-weight: 600; color: #1a1a1a; }
.offer-label.promo-applied { color: #4CAF50; }
.remove-promo { font-size: 13px; color: #f44336; font-weight: 600; cursor: pointer; }
.offer-row ion-icon { font-size: 18px; color: #ccc; }

/* Totals */
.totals-card { }
.total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 15px; color: #555; }
.total-row .free { color: #4CAF50; font-weight: 600; }
.total-row.discount { color: #4CAF50; }
.total-divider { height: 1px; background: #f0f0f0; margin: 8px 0; }
.total-row.grand-total { font-size: 18px; font-weight: 800; color: #1a1a1a; padding-top: 12px; }

/* Place Order */
.place-order-bar {
  position: fixed; bottom: 0; left: 0; right: 0;
  background: white; padding: 16px 20px;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.1); z-index: 1000;
}
.place-order-btn {
  --background: #4CAF50; --border-radius: 16px;
  height: 56px; font-size: 16px; font-weight: 700; text-transform: none;
}
</style>