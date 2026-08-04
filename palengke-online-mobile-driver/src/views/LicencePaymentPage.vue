<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>Palengke Online Licence</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="payment-content">
      <div class="payment-container">

        <!-- Header -->
        <div class="header-section">
          <div class="header-icon">
            <ion-icon :icon="cardOutline"></ion-icon>
          </div>
          <h1 class="header-title">Palengke Online Licence</h1>
          <p class="header-sub">Pay to continue delivering with Palengke Online</p>
        </div>

        <!-- Current Status -->
        <div class="status-card" :style="{ borderColor: licenceStore.getStatusColor() }">
          <div class="status-row">
            <ion-icon :icon="licenceStatusIcon" :style="{ color: licenceStore.getStatusColor() }"></ion-icon>
            <div>
              <p class="status-label">Licence ID</p>
              <p class="status-id">{{ licenceStore.getLicenceDisplay() }}</p>
            </div>
          </div>
          <div class="status-row">
            <ion-icon :icon="timeOutline" style="color: #FF9800"></ion-icon>
            <div>
              <p class="status-label">Status</p>
              <p class="status-value" :style="{ color: licenceStore.getStatusColor() }">{{ licenceStore.getStatusText() }}</p>
            </div>
          </div>
          <div v-if="!licenceStore.state.isPaid" class="status-row">
            <ion-icon :icon="calendarOutline" style="color: #2196F3"></ion-icon>
            <div>
              <p class="status-label">Days Used</p>
              <p class="status-value">{{ licenceStore.getDaysUsed() }} / 30</p>
            </div>
          </div>
        </div>

        <!-- Free Licence Message -->
        <div class="free-card" v-if="licenceStore.state.isFree">
          <ion-icon :icon="checkmarkCircleOutline" style="font-size: 40px; color: #3b82f6;"></ion-icon>
          <h3 style="margin: 10px 0 4px; color: #111827;">Licence is Free</h3>
          <p style="font-size: 13px; color: #6b7280; margin: 0;">This driver has been marked as free. No payment needed.</p>
        </div>

        <!-- Payment Plan Card -->
        <div class="plan-card" v-if="!licenceStore.state.isPaid && !licenceStore.state.isFree">
          <div class="plan-header">
            <h3>Licence Fee</h3>
            <span class="plan-badge">Required</span>
          </div>
          <div class="plan-price">
            <span class="plan-currency">₱</span>
            <span class="plan-amount">{{ licenceStore.state.amount.toLocaleString() }}</span>
          </div>
          <p class="plan-desc">One-time payment to continue delivering after 30-day trial period.</p>
          <ul class="plan-features">
            <li>
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Unlimited deliveries</span>
            </li>
            <li>
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Full earnings & tips</span>
            </li>
            <li>
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Priority order matching</span>
            </li>
            <li>
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>No expiry after payment</span>
            </li>
          </ul>
        </div>

        <!-- Already Paid State -->
        <div class="paid-card" v-if="licenceStore.state.isPaid">
          <div class="paid-icon">
            <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
          </div>
          <h3>Licence is Active</h3>
          <p>Your Palengke Online Licence is paid and active. You can continue delivering without interruption.</p>
        </div>

        <!-- Payment Methods -->
        <div class="payment-section" v-if="!licenceStore.state.isPaid">
          <h3 class="section-title">Payment Method</h3>

          <div
            class="payment-option"
            v-for="method in paymentMethods"
            :key="method.id"
            :class="{ selected: selectedPayment === method.id }"
            @click="selectedPayment = method.id"
          >
            <div class="payment-radio">
              <div class="radio-circle" :class="{ filled: selectedPayment === method.id }"></div>
            </div>
            <div class="payment-icon-wrap" :style="{ background: method.color + '20' }">
              <ion-icon :icon="method.icon" :style="{ color: method.color }"></ion-icon>
            </div>
            <div class="payment-info">
              <p class="payment-name">{{ method.name }}</p>
              <p class="payment-desc">{{ method.description }}</p>
            </div>
          </div>

          <ion-button
            expand="block"
            class="pay-button"
            :disabled="!selectedPayment || isPaying"
            @click="handlePayment"
          >
            <span v-if="!isPaying">Pay ₱{{ licenceStore.state.amount.toLocaleString() }}</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>

          <p class="secure-text">
            <ion-icon :icon="lockClosedOutline"></ion-icon>
            Secure payment. Your information is protected.
          </p>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonButton, IonIcon, IonSpinner,
  alertController
} from '@ionic/vue';
import {
  cardOutline, timeOutline, calendarOutline,
  checkmarkCircleOutline, lockClosedOutline,
  walletOutline,
  checkmarkCircleOutline as checkIcon,
  timeOutline as timeIcon,
  closeCircleOutline as closeIcon,
} from 'ionicons/icons';
import { useLicenceStore } from '@/composables/useLicenceStore';

const router = useRouter();
const licenceStore = useLicenceStore();
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

const selectedPayment = ref(null);
const isPaying = ref(false);

onMounted(async () => {
  await licenceStore.checkLicenceEnabled();
  licenceStore.fetchLicence();
  fetchPaymentMethods();
});

const licenceStatusIcon = computed(() => {
  if (licenceStore.state.isPaid) return checkIcon
  if (licenceStore.trialExpired.value) return closeIcon
  return timeIcon
})

const paymentMethods = ref([]);

function getMethodIcon(name) {
  const n = (name || '').toLowerCase();
  if (n.includes('card') || n.includes('stripe') || n.includes('credit') || n.includes('debit')) return cardOutline;
  return walletOutline;
}

async function fetchPaymentMethods() {
  try {
    const res = await fetch(`${API_BASE}/api/payment-methods`);
    if (res.ok) {
      const data = await res.json();
      paymentMethods.value = (data || [])
        .filter(m => !m.coming_soon && m.name !== 'Cash on Delivery' && m.name !== 'Palengke Wallet')
        .map(m => ({ ...m, icon: getMethodIcon(m.name) }));
      if (paymentMethods.value.length > 0) {
        selectedPayment.value = paymentMethods.value[0].id;
      }
    }
  } catch {}
}

const handlePayment = async () => {
  if (!selectedPayment.value || isPaying.value) return;
  isPaying.value = true;

  const success = await licenceStore.payLicence(selectedPayment.value);

  if (!success) {
    isPaying.value = false;
    const errAlert = await alertController.create({
      header: 'Payment Failed',
      message: 'We could not process your payment. Please try again.',
      buttons: ['OK']
    });
    await errAlert.present();
    return;
  }

  isPaying.value = false;

  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: '',
    buttons: [{
      text: 'Continue',
      handler: () => router.push('/home')
    }]
  });
  await alert.present();

  const alertEl = document.querySelector('.success-modal .alert-message');
  if (alertEl) {
    alertEl.innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;text-align:center;padding:8px 0;">
        <img src="/assets/images/checkmark_success.png" style="width:80px;height:80px;margin:0 auto 16px auto;display:block;" />
        <div style="font-size:18px;font-weight:600;color:#1a1a1a;margin-bottom:8px;">Payment Successful!</div>
        <div style="font-size:14px;color:#666;">Your Palengke Online Licence is now active. You can continue delivering.</div>
        <div style="margin-top:12px;padding:10px 16px;background:#E8F5E9;border-radius:10px;font-size:14px;font-weight:600;color:#4CAF50;font-family:monospace;">${licenceStore.getLicenceDisplay()}</div>
      </div>`;
  }
};
</script>

<style scoped>
.payment-content { --background: #f8f9fa; }
.payment-container { padding: 24px 16px 40px; }

/* Header */
.header-section { text-align: center; margin-bottom: 24px; }
.header-icon {
  width: 80px; height: 80px;
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  border-radius: 50%; display: inline-flex;
  align-items: center; justify-content: center;
  margin-bottom: 16px; box-shadow: 0 4px 20px rgba(76,175,80,0.3);
}
.header-icon ion-icon { font-size: 40px; color: white; }
.header-title { font-size: 24px; font-weight: 800; color: #1a1a1a; margin: 0 0 8px 0; }
.header-sub { font-size: 14px; color: #666; margin: 0; }

/* Status Card */
.status-card {
  background: white; border-radius: 16px;
  padding: 20px; margin-bottom: 16px;
  border: 2px solid #4CAF50;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  display: flex; flex-direction: column; gap: 14px;
}

.status-row { display: flex; align-items: center; gap: 14px; }
.status-row ion-icon { font-size: 24px; flex-shrink: 0; }
.status-label { font-size: 12px; color: #999; margin: 0 0 2px 0; }
.status-id { font-size: 16px; font-weight: 700; color: #1a1a1a; font-family: monospace; letter-spacing: 0.5px; margin: 0; }
.status-value { font-size: 14px; font-weight: 600; margin: 0; }

/* Plan Card */
.free-card {
  background: #eff6ff;
  border: 1.5px solid #93c5fd;
  border-radius: 12px;
  padding: 24px 20px;
  text-align: center;
}
.plan-card {
  background: white; border-radius: 16px;
  padding: 20px; margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  border: 2px solid #4CAF50;
}

.plan-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.plan-header h3 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0; }
.plan-badge {
  background: #4CAF50; color: white;
  padding: 4px 12px; border-radius: 12px;
  font-size: 11px; font-weight: 700;
}

.plan-price { margin-bottom: 12px; }
.plan-currency { font-size: 20px; font-weight: 700; color: #1a1a1a; vertical-align: super; }
.plan-amount { font-size: 42px; font-weight: 900; color: #1a1a1a; letter-spacing: -2px; }
.plan-desc { font-size: 13px; color: #666; margin: 0 0 16px 0; line-height: 1.5; }

.plan-features { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
.plan-features li { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #333; }
.plan-features li ion-icon { font-size: 20px; color: #4CAF50; flex-shrink: 0; }

/* Paid Card */
.paid-card {
  background: white; border-radius: 16px;
  padding: 32px 20px; margin-bottom: 16px;
  text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  border: 2px solid #4CAF50;
}
.paid-icon { font-size: 64px; color: #4CAF50; margin-bottom: 12px; }
.paid-card h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.paid-card p { font-size: 14px; color: #666; margin: 0; line-height: 1.5; }

/* Payment Section */
.payment-section { margin-top: 8px; }
.section-title { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 14px 0; }

.payment-option {
  display: flex; align-items: center; gap: 14px;
  background: white; border-radius: 14px;
  padding: 16px; margin-bottom: 10px;
  border: 2px solid #f0f0f0;
  cursor: pointer; transition: all 0.2s;
}
.payment-option:active { transform: scale(0.98); }
.payment-option.selected { border-color: #4CAF50; background: #F1F8E9; }

.payment-radio { flex-shrink: 0; }
.radio-circle {
  width: 22px; height: 22px; border-radius: 50%;
  border: 2px solid #ccc; display: flex;
  align-items: center; justify-content: center;
  transition: all 0.2s;
}
.radio-circle.filled {
  border-color: #4CAF50;
  background: #4CAF50;
  box-shadow: inset 0 0 0 4px white;
}

.payment-icon-wrap {
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.payment-icon-wrap ion-icon { font-size: 22px; }

.payment-info { flex: 1; }
.payment-name { font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 0 0 2px 0; }
.payment-desc { font-size: 12px; color: #999; margin: 0; }

.pay-button {
  --background: #4CAF50; --background-hover: #45a049;
  --border-radius: 12px; height: 54px;
  font-size: 16px; font-weight: 700; text-transform: none;
  margin-top: 16px;
}

.pay-button[disabled] { --background: #e0e0e0; --color: #999; }

.secure-text {
  text-align: center; font-size: 12px; color: #999;
  margin-top: 16px; display: flex; align-items: center;
  justify-content: center; gap: 6px;
}
.secure-text ion-icon { font-size: 14px; }

/* Success Modal */
:deep(.success-modal .alert-wrapper) {
  border-radius: 20px; max-width: 340px; background: white;
}

:deep(.success-modal .alert-head) { display: none; }

:deep(.success-modal .alert-message) {
  padding: 24px 24px 8px; text-align: center;
}

:deep(.success-modal .alert-button-group) {
  padding: 8px 24px 24px; justify-content: center;
}

:deep(.success-modal .alert-button) {
  border-radius: 12px; height: 46px;
  font-size: 14px; font-weight: 600; text-transform: none;
  background: #4CAF50; color: white !important;
  min-width: 120px;
}

@media (max-width: 360px) {
  .payment-container { padding: 20px 12px 32px; }
  .header-icon { width: 64px; height: 64px; }
  .header-icon ion-icon { font-size: 32px; }
  .plan-amount { font-size: 34px; }
}
</style>
