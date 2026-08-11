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
      <div class="payment-container" v-if="!paymentSuccess">
        <!-- Header -->
        <div class="header-section">
          <div class="header-icon">
            <ion-icon :icon="cardOutline"></ion-icon>
          </div>
          <h1 class="header-title">Palengke Online Licence</h1>
          <p class="header-sub">Annual licence to keep delivering with Palengke Online</p>
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
            <ion-icon :icon="timeOutline" style="color: #ff9800"></ion-icon>
            <div>
              <p class="status-label">Status</p>
              <p class="status-value" :style="{ color: licenceStore.getStatusColor() }">
                {{ licenceStore.getStatusText() }}
              </p>
            </div>
          </div>
          <div v-if="licenceStore.licenceActive.value" class="status-row">
            <ion-icon :icon="calendarOutline" style="color: #4caf50"></ion-icon>
            <div>
              <p class="status-label">Valid Until</p>
              <p class="status-value" style="color: #4caf50">{{ licenceStore.getPaidUntilDisplay() }}</p>
            </div>
          </div>
          <div v-else-if="!licenceStore.state.isPaid" class="status-row">
            <ion-icon :icon="calendarOutline" style="color: #2196f3"></ion-icon>
            <div>
              <p class="status-label">Days Used</p>
              <p class="status-value">{{ licenceStore.getDaysUsed() }} / 30</p>
            </div>
          </div>
        </div>

        <!-- Free Licence Message -->
        <div class="free-card" v-if="licenceStore.state.isFree">
          <ion-icon :icon="checkmarkCircleOutline" style="font-size: 40px; color: #3b82f6"></ion-icon>
          <h3 style="margin: 10px 0 4px; color: #111827">Licence is Free</h3>
          <p style="font-size: 13px; color: #6b7280; margin: 0">
            This driver has been marked as free. No payment needed.
          </p>
        </div>

        <!-- Payment Plan Card -->
        <div class="plan-card" v-if="!licenceStore.licenceActive.value && !licenceStore.state.isFree">
          <div class="plan-header">
            <h3>Annual Licence Fee</h3>
            <span class="plan-badge">{{ licenceStore.licenceExpired.value ? 'Renewal' : 'Required' }}</span>
          </div>
          <div class="plan-price">
            <span class="plan-currency">₱</span>
            <span class="plan-amount">{{ licenceStore.state.amount.toLocaleString() }}</span>
          </div>
          <p class="plan-desc">Annual payment to keep your licence active. Renew once a year to continue delivering.</p>
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
              <span>Valid for 1 year — renewable annually</span>
            </li>
          </ul>
        </div>

        <!-- Already Paid State -->
        <div class="paid-card" v-if="licenceStore.licenceActive.value">
          <div class="paid-icon">
            <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
          </div>
          <h3>Licence is Active</h3>
          <p>
            Your Palengke Online Licence is active until {{ licenceStore.getPaidUntilDisplay() }}. You can continue
            delivering without interruption.
          </p>
        </div>

        <!-- Expired State -->
        <div class="expired-card" v-if="licenceStore.state.isPaid && licenceStore.licenceExpired.value">
          <div class="paid-icon" style="color: #f44336">
            <ion-icon :icon="closeIcon"></ion-icon>
          </div>
          <h3>Licence Expired</h3>
          <p>
            Your licence expired on {{ licenceStore.getPaidUntilDisplay() }}. Renew to continue delivering without
            interruption.
          </p>
        </div>

        <!-- Payment Methods -->
        <div class="payment-section" v-if="!licenceStore.licenceActive.value">
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
              <p
                v-if="isWalletMethod(method)"
                class="wallet-balance"
                :class="{ insufficient: walletBalance < licenceStore.state.amount }"
              >
                Balance: ₱{{ walletBalance.toLocaleString() }}
              </p>
            </div>
          </div>

          <div v-if="isWalletSelected && walletInsufficient" class="insufficient-box">
            <p class="insufficient-text">Your wallet balance is not enough to cover the licence fee.</p>
            <ion-button expand="block" fill="outline" color="success" class="topup-btn" @click="goToWalletTopUp">
              <ion-icon :icon="walletOutline" slot="start"></ion-icon>
              Top Up Wallet
            </ion-button>
          </div>

          <ion-button
            expand="block"
            class="pay-button"
            :disabled="!selectedPayment || isPaying || (isWalletSelected && walletInsufficient)"
            @click="handlePayment"
          >
            <span v-if="!isPaying && licenceStore.licenceExpired.value"
              >Renew ₱{{ licenceStore.state.amount.toLocaleString() }} for 1 Year</span
            >
            <span v-else-if="!isPaying">Pay ₱{{ licenceStore.state.amount.toLocaleString() }} for 1 Year</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>

          <p class="secure-text">
            <ion-icon :icon="lockClosedOutline"></ion-icon>
            Secure payment. Your information is protected.
          </p>
        </div>
      </div>

      <!-- Success -->
      <div class="center-state success" v-else>
        <div class="success-icon">
          <ion-icon :icon="checkmarkCircle"></ion-icon>
        </div>
        <h3>Payment Successful!</h3>
        <p class="success-sub">Your Palengke Online Licence is now active for 1 year.</p>
        <div class="success-details">
          <div class="success-row">
            <span>Licence ID</span>
            <span class="success-txn">{{ licenceStore.getLicenceDisplay() }}</span>
          </div>
          <div class="success-row">
            <span>Amount Paid</span>
            <span class="success-price">₱{{ paidAmount.toLocaleString() }}</span>
          </div>
          <div class="success-row">
            <span>Payment Method</span>
            <span>{{ paidMethod }}</span>
          </div>
          <div class="success-row">
            <span>Valid Until</span>
            <span>{{ paidUntil }}</span>
          </div>
          <div class="success-row">
            <span>Date</span>
            <span>{{ paidDate }}</span>
          </div>
        </div>
        <div class="receipt-actions">
          <button class="receipt-btn download" @click="handleDownloadReceipt">
            <ion-icon :icon="downloadOutline"></ion-icon>
            Download Receipt
          </button>
          <button class="receipt-btn share" @click="handleShareReceipt">
            <ion-icon :icon="shareSocialOutline"></ion-icon>
            Share Receipt
          </button>
        </div>
        <ion-button expand="block" class="done-btn" @click="goHome"> Continue </ion-button>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonButton,
  IonIcon,
  IonSpinner,
  alertController,
  toastController,
} from '@ionic/vue';
import {
  cardOutline,
  timeOutline,
  calendarOutline,
  checkmarkCircleOutline,
  lockClosedOutline,
  walletOutline,
  checkmarkCircle,
  downloadOutline,
  shareSocialOutline,
  checkmarkCircleOutline as checkIcon,
  timeOutline as timeIcon,
  closeCircleOutline as closeIcon,
} from 'ionicons/icons';
import { useLicenceStore } from '@/composables/useLicenceStore';
import { downloadReceipt, shareReceiptWithFallback } from '@/utils/receiptGenerator';

const router = useRouter();
const licenceStore = useLicenceStore();
import { API_BASE } from '@/services/api';

const selectedPayment = ref(null);
const isPaying = ref(false);
const walletBalance = ref(0);
const paymentSuccess = ref(false);
const paidAmount = ref(0);
const paidMethod = ref('');
const paidUntil = ref('');
const paidDate = ref('');

const receiptData = computed(() => {
  if (!paymentSuccess.value) return null;
  return {
    transactionId: licenceStore.getLicenceDisplay(),
    title: 'Licence Payment Receipt',
    amount: paidAmount.value,
    date: paidDate.value,
    rows: [
      { label: 'Type', value: 'Annual Licence Fee' },
      { label: 'Payment Method', value: paidMethod.value },
      { label: 'Valid Until', value: paidUntil.value },
    ],
    account: getEmail() || '',
    footer: 'Thank you for using Palengke Online!',
  };
});

const handleDownloadReceipt = async () => {
  if (!receiptData.value) return;
  try {
    await downloadReceipt(receiptData.value);
    const toast = await toastController.create({
      message: 'Receipt downloaded!',
      duration: 2000,
      color: 'success',
      position: 'top',
    });
    await toast.present();
  } catch {
    const toast = await toastController.create({
      message: 'Failed to download receipt.',
      duration: 2000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  }
};

const handleShareReceipt = async () => {
  if (!receiptData.value) return;
  await shareReceiptWithFallback(receiptData.value);
};

function getEmail() {
  return localStorage.getItem('userEmail');
}

function goHome() {
  router.push('/home');
}

onMounted(async () => {
  await licenceStore.checkLicenceEnabled();
  licenceStore.fetchLicence();
  fetchPaymentMethods();
  fetchWalletBalance();
});

const isWalletMethod = (method) => {
  return (method?.name || '').toLowerCase() === 'palengke wallet';
};

const selectedMethod = computed(() => {
  return paymentMethods.value.find((m) => m.id === selectedPayment.value) || null;
});

const isWalletSelected = computed(() => isWalletMethod(selectedMethod.value));

const walletInsufficient = computed(() => {
  return walletBalance.value < licenceStore.state.amount;
});

const licenceStatusIcon = computed(() => {
  if (licenceStore.licenceActive.value) return checkIcon;
  if (licenceStore.licenceExpired.value) return closeIcon;
  if (licenceStore.trialExpired.value) return closeIcon;
  return timeIcon;
});

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
        .filter((m) => !m.coming_soon && m.name !== 'Cash on Delivery')
        .map((m) => ({ ...m, icon: getMethodIcon(m.name) }));
      if (paymentMethods.value.length > 0) {
        selectedPayment.value = paymentMethods.value[0].id;
      }
    }
  } catch {
    /* ignore */
  }
}

async function fetchWalletBalance() {
  const token = localStorage.getItem('driver_token');
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/wallet/balance`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      walletBalance.value = data.balance || 0;
    }
  } catch {
    /* ignore */
  }
}

const goToWalletTopUp = () => router.push('/wallet');

const handlePayment = async () => {
  if (!selectedPayment.value || isPaying.value) return;
  if (isWalletSelected.value && walletInsufficient.value) return;
  isPaying.value = true;

  const result = await licenceStore.payLicence(selectedPayment.value);

  if (!result.success) {
    isPaying.value = false;
    const errAlert = await alertController.create({
      header: 'Payment Failed',
      message: result.message || 'We could not process your payment. Please try again.',
      buttons: ['OK'],
    });
    await errAlert.present();
    return;
  }

  if (typeof result.balance === 'number') {
    walletBalance.value = result.balance;
  }

  paidAmount.value = licenceStore.state.amount;
  paidMethod.value = selectedMethod.value?.name || '';
  paidUntil.value = licenceStore.getPaidUntilDisplay();
  paidDate.value = new Date().toLocaleDateString('en-PH', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
  isPaying.value = false;
  paymentSuccess.value = true;
};
</script>

<style scoped>
.payment-content {
  --background: #f8f9fa;
}
.payment-container {
  padding: 24px 16px 40px;
}

/* Header */
.header-section {
  text-align: center;
  margin-bottom: 24px;
}
.header-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  box-shadow: 0 4px 20px rgba(76, 175, 80, 0.3);
}
.header-icon ion-icon {
  font-size: 40px;
  color: white;
}
.header-title {
  font-size: 24px;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}
.header-sub {
  font-size: 14px;
  color: #666;
  margin: 0;
}

/* Status Card */
.status-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  border: 2px solid #4caf50;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.status-row {
  display: flex;
  align-items: center;
  gap: 14px;
}
.status-row ion-icon {
  font-size: 24px;
  flex-shrink: 0;
}
.status-label {
  font-size: 12px;
  color: #999;
  margin: 0 0 2px 0;
}
.status-id {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  font-family: monospace;
  letter-spacing: 0.5px;
  margin: 0;
}
.status-value {
  font-size: 14px;
  font-weight: 600;
  margin: 0;
}

/* Plan Card */
.free-card {
  background: #eff6ff;
  border: 1.5px solid #93c5fd;
  border-radius: 12px;
  padding: 24px 20px;
  text-align: center;
}
.plan-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 2px solid #4caf50;
}

.plan-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.plan-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}
.plan-badge {
  background: #4caf50;
  color: white;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
}

.plan-price {
  margin-bottom: 12px;
}
.plan-currency {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  vertical-align: super;
}
.plan-amount {
  font-size: 42px;
  font-weight: 900;
  color: #1a1a1a;
  letter-spacing: -2px;
}
.plan-desc {
  font-size: 13px;
  color: #666;
  margin: 0 0 16px 0;
  line-height: 1.5;
}

.plan-features {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.plan-features li {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: #333;
}
.plan-features li ion-icon {
  font-size: 20px;
  color: #4caf50;
  flex-shrink: 0;
}

/* Paid Card */
.paid-card {
  background: white;
  border-radius: 16px;
  padding: 32px 20px;
  margin-bottom: 16px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 2px solid #4caf50;
}
.paid-icon {
  font-size: 64px;
  color: #4caf50;
  margin-bottom: 12px;
}
.paid-card h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}
.paid-card p {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.5;
}

/* Expired Card */
.expired-card {
  background: white;
  border-radius: 16px;
  padding: 32px 20px;
  margin-bottom: 16px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 2px solid #f44336;
}
.expired-card h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}
.expired-card p {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.5;
}

/* Payment Section */
.payment-section {
  margin-top: 8px;
}
.section-title {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 14px 0;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 14px;
  background: white;
  border-radius: 14px;
  padding: 16px;
  margin-bottom: 10px;
  border: 2px solid #f0f0f0;
  cursor: pointer;
  transition: all 0.2s;
}
.payment-option:active {
  transform: scale(0.98);
}
.payment-option.selected {
  border-color: #4caf50;
  background: #f1f8e9;
}

.payment-radio {
  flex-shrink: 0;
}
.radio-circle {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 2px solid #ccc;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.radio-circle.filled {
  border-color: #4caf50;
  background: #4caf50;
  box-shadow: inset 0 0 0 4px white;
}

.payment-icon-wrap {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.payment-icon-wrap ion-icon {
  font-size: 22px;
}

.payment-info {
  flex: 1;
}
.payment-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 2px 0;
}
.payment-desc {
  font-size: 12px;
  color: #999;
  margin: 0;
}
.wallet-balance {
  font-size: 12px;
  font-weight: 700;
  color: #059669;
  margin: 4px 0 0 0;
}
.wallet-balance.insufficient {
  color: #f44336;
}

.insufficient-box {
  background: #fef2f2;
  border: 1.5px solid #fecaca;
  border-radius: 12px;
  padding: 14px 16px;
  margin-top: 4px;
}
.insufficient-text {
  font-size: 13px;
  color: #b91c1c;
  margin: 0 0 10px 0;
  line-height: 1.4;
}
.topup-btn {
  --border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  text-transform: none;
  height: 44px;
}

.pay-button {
  --background: #4caf50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 54px;
  font-size: 16px;
  font-weight: 700;
  text-transform: none;
  margin-top: 16px;
}

.pay-button[disabled] {
  --background: #e0e0e0;
  --color: #999;
}

.secure-text {
  text-align: center;
  font-size: 12px;
  color: #999;
  margin-top: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}
.secure-text ion-icon {
  font-size: 14px;
}

/* Success */
.center-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 24px 40px;
  text-align: center;
}
.center-state h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 16px 0 4px;
}
.center-state p {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
}
.center-state.success .success-icon {
  font-size: 56px;
  color: #4caf50;
}
.center-state.success .success-sub {
  margin-bottom: 20px;
}

.success-details {
  background: #f0fdf4;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 24px;
  text-align: left;
  width: 100%;
}
.success-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  padding: 6px 0;
  color: #374151;
}
.success-row + .success-row {
  border-top: 1px solid #d1fae5;
}
.success-price {
  font-weight: 700;
  color: #059669;
}
.success-txn {
  font-family: 'Courier New', monospace;
  font-size: 12px !important;
  letter-spacing: 0.5px;
}

.receipt-actions {
  display: flex;
  gap: 10px;
  width: 100%;
  margin-bottom: 16px;
}
.receipt-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 8px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
  font-family: inherit;
}
.receipt-btn:active {
  transform: scale(0.97);
}
.receipt-btn.download {
  background: #f0fdf4;
  color: #059669;
  border: 2px solid #d1fae5;
}
.receipt-btn.download:hover {
  background: #dcfce7;
}
.receipt-btn.share {
  background: #eff6ff;
  color: #2563eb;
  border: 2px solid #dbeafe;
}
.receipt-btn.share:hover {
  background: #dbeafe;
}
.receipt-btn ion-icon {
  font-size: 18px;
}

.done-btn {
  --background: #4caf50;
  --border-radius: 12px;
  font-weight: 600;
  margin: 0;
  height: 50px;
  font-size: 15px;
  text-transform: none;
}

@media (prefers-color-scheme: dark) {
  .success-details {
    background: rgba(76, 175, 80, 0.1);
  }
  .success-row {
    color: #d1d5db;
  }
  .center-state h3 {
    color: #e5e5e5;
  }
  .receipt-btn.download {
    background: rgba(76, 175, 80, 0.1);
    border-color: rgba(76, 175, 80, 0.3);
    color: #4caf50;
  }
  .receipt-btn.share {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
    color: #60a5fa;
  }
}

@media (max-width: 360px) {
  .payment-container {
    padding: 20px 12px 32px;
  }
  .header-icon {
    width: 64px;
    height: 64px;
  }
  .header-icon ion-icon {
    font-size: 32px;
  }
  .plan-amount {
    font-size: 34px;
  }
}
</style>
