<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button default-href="/checkout"></ion-back-button>
        </ion-buttons>
        <ion-title>Offers</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="offers-content">

      <!-- Promo Code Input -->
      <div class="promo-input-section">
        <div class="promo-input-wrap">
          <input
            v-model="promoCode"
            type="text"
            placeholder="Enter promo or gift code here"
            class="promo-input"
            @keyup.enter="applyPromo"
          >
          <ion-button
            v-if="promoCode"
            fill="clear"
            color="success"
            @click="applyPromo"
          >Apply</ion-button>
        </div>
      </div>

      <!-- Offers List -->
      <div class="offers-list">

        <!-- Gift Card -->
        <div class="offer-item gift-card" @click="useGift">
          <div class="offer-icon-wrap gift"><ion-icon :icon="giftOutline"></ion-icon></div>
          <div class="offer-info">
            <h4>Use Palengke Gift Card</h4>
            <p>Have a gift code? Redeem and use here</p>
          </div>
          <ion-icon :icon="chevronForwardOutline"></ion-icon>
        </div>

        <!-- Available Offers -->
        <div
          v-for="offer in offers"
          :key="offer.id"
          class="offer-item"
          :class="{ disabled: !offer.available, selected: selectedOffer === offer.id }"
          @click="selectOffer(offer)"
        >
          <div class="offer-icon-wrap" :style="{ background: offer.color }">
            <span v-if="false">{{ offer.emoji }}</span>
          </div>
          <div class="offer-info">
            <h4>{{ offer.title }}</h4>
            <p>{{ offer.description }}</p>
          </div>
          <div class="offer-right">
            <div class="offer-radio" :class="{ selected: selectedOffer === offer.id }"></div>
          </div>
        </div>

      </div>

    </ion-content>

    <!-- Bottom Button -->
    <div class="offers-bottom">
      <ion-button
        expand="block"
        fill="outline"
        color="medium"
        class="continue-btn"
        @click="continueWithout"
      >
        Continue without Offer
      </ion-button>
    </div>

  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonButton, IonIcon,
  toastController
} from '@ionic/vue';
import { chevronForwardOutline, giftOutline } from 'ionicons/icons';

const router = useRouter();
const promoCode = ref('');
const selectedOffer = ref(null);

const offers = ref([
  {
    id: 1, title: '₱70 OFF', color: '#FFF3E0',
    description: '₱70 off — choose Fri-Sun 11:00AM-11:59PM delivery to enjoy this',
    available: true
  },
  {
    id: 2, title: '₱50 OFF', color: '#E8F5E9',
    description: '₱50 off — choose Mon-Sun 08:00PM-11:59PM delivery to enjoy this',
    available: true
  },
  {
    id: 3, title: '₱50 OFF', color: '#E3F2FD',
    description: '₱50 off — choose Mon-Sun 08:00AM-10:59AM delivery to enjoy this',
    available: false
  },
  {
    id: 4, title: 'Earn 200 Points (₱20 OFF)', color: '#F3E5F5',
    description: 'Earn PalengkePoints and save on your next order',
    available: true
  },
]);

const applyPromo = async () => {
  if (!promoCode.value) return;
  const toast = await toastController.create({
    message: promoCode.value.toUpperCase() === 'PALENGKE20'
      ? 'Promo code applied! discount'
      : 'Invalid promo code',
    duration: 2000,
    color: promoCode.value.toUpperCase() === 'PALENGKE20' ? 'success' : 'danger',
    position: 'top'
  });
  await toast.present();
  if (promoCode.value.toUpperCase() === 'PALENGKE20') router.back();
};

const selectOffer = (offer) => {
  if (!offer.available) return;
  selectedOffer.value = selectedOffer.value === offer.id ? null : offer.id;
};

const useGift = async () => {};
const continueWithout = () => router.back();
</script>

<style scoped>
.offers-content { --background: #f8f9fa; }

.promo-input-section {
  background: white; padding: 16px;
  border-bottom: 1px solid #f0f0f0;
}
.promo-input-wrap {
  display: flex; align-items: center; gap: 8px;
  background: #f5f5f5; border-radius: 12px;
  padding: 4px 4px 4px 16px;
}
.promo-input {
  flex: 1; background: transparent; border: none;
  font-size: 15px; font-family: inherit; padding: 10px 0;
  color: #1a1a1a !important; -webkit-text-fill-color: #1a1a1a !important;
}
.promo-input:focus { outline: none; }
.promo-input::placeholder { color: #999; }

.offers-list { padding: 8px 0; }

.offer-item {
  display: flex; align-items: center; gap: 14px;
  padding: 16px 20px; background: white;
  border-bottom: 1px solid #f5f5f5; cursor: pointer;
  transition: background 0.2s;
}
.offer-item:active { background: #f8f9fa; }
.offer-item.disabled { opacity: 0.4; cursor: not-allowed; }
.offer-item.selected { background: #F1F8E9; }

.offer-icon-wrap {
  width: 48px; height: 48px; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; flex-shrink: 0;
}
.offer-icon-wrap.gift { background: #FFF8E1; }

.offer-info { flex: 1; }
.offer-info h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.offer-info p { font-size: 12px; color: #666; margin: 0; line-height: 1.4; }

.offer-right { flex-shrink: 0; }
.offer-radio {
  width: 20px; height: 20px; border-radius: 50%;
  border: 2px solid #e0e0e0; transition: all 0.2s;
}
.offer-radio.selected { border-color: #4CAF50; background: #4CAF50; }

.offer-item ion-icon { font-size: 20px; color: #ccc; }

.offers-bottom {
  background: white; padding: 16px 20px;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
}
.continue-btn { --border-radius: 14px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none; }
</style>