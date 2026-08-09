<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Wallet</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="wallet-container" v-if="!topUpSuccess">
        <!-- Balance Card -->
        <div class="balance-card">
          <div class="balance-icon">
            <ion-icon :icon="walletOutline"></ion-icon>
          </div>
          <span class="balance-label">Current Balance</span>
          <span class="balance-amount">₱{{ balance.toLocaleString() }}</span>
        </div>

        <!-- Transaction History Link -->
        <div class="history-link" @click="goToHistory">
          <span>Transaction History</span>
          <ion-icon :icon="chevronForwardOutline"></ion-icon>
        </div>

        <!-- Top Up Form -->
        <div class="topup-section">
          <h4 class="section-title">Add Funds</h4>

          <!-- Amount -->
          <div class="amount-section">
            <label class="input-label">Amount</label>
            <div class="amount-input-wrap">
              <span class="currency-symbol">₱</span>
              <input type="number" v-model.number="amount" min="1" class="amount-input" placeholder="0" />
            </div>
            <div class="quick-amounts">
              <button
                v-for="quick in quickAmounts"
                :key="quick"
                class="quick-chip"
                :class="{ active: amount === quick }"
                @click="amount = quick"
              >
                ₱{{ quick.toLocaleString() }}
              </button>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="pm-section">
            <label class="input-label">Payment Method</label>
            <div
              v-for="pm in paymentMethods"
              :key="pm.id"
              class="pm-item"
              :class="{ selected: selectedPM === pm.id }"
              @click="selectedPM = pm.id"
            >
              <img v-if="pmLogo(pm.name)" :src="pmLogo(pm.name)" :alt="pm.name" class="pm-img" />
              <div v-else class="pm-dot" :style="{ background: pm.color }"></div>
              <span class="pm-name">{{ pm.name }}</span>
              <div class="pm-radio" :class="{ selected: selectedPM === pm.id }"></div>
            </div>
          </div>

          <!-- Error -->
          <div class="error-box" v-if="error">{{ error }}</div>

          <!-- Top Up Button -->
          <ion-button expand="block" class="topup-btn" :disabled="!canSubmit" @click="processTopUp">
            <ion-icon :icon="walletOutline" slot="start"></ion-icon>
            Add ₱{{ amount.toLocaleString() }}
          </ion-button>
        </div>
      </div>

      <!-- Inline Processing -->
      <div class="center-state" v-if="processing">
        <div class="spinner"></div>
        <h3>Processing...</h3>
        <p>Please wait while we add funds to your wallet.</p>
      </div>

      <!-- Success -->
      <div class="center-state success" v-if="topUpSuccess">
        <div class="success-icon">
          <ion-icon :icon="checkmarkCircle"></ion-icon>
        </div>
        <h3>Funds Added!</h3>
        <p class="success-sub">₱{{ topUpAmount }} added to your wallet via {{ topUpMethod }}</p>
        <div class="success-details">
          <div class="success-row">
            <span>Amount Added</span>
            <span class="success-price">₱{{ topUpAmount.toLocaleString() }}</span>
          </div>
          <div class="success-row">
            <span>Payment Method</span>
            <span class="success-pm">
              <img v-if="pmLogo(topUpMethod)" :src="pmLogo(topUpMethod)" class="success-pm-img" />
              {{ topUpMethod }}
            </span>
          </div>
          <div class="success-row">
            <span>Transaction ID</span>
            <span class="success-txn">{{ receiptData?.transactionId || 'WLT-' + String(topUpTransactionId).padStart(6, '0') }}</span>
          </div>
          <div class="success-row">
            <span>Date</span>
            <span>{{ receiptDate }}</span>
          </div>
          <div class="success-row">
            <span>New Balance</span>
            <span class="success-price">₱{{ newBalance.toLocaleString() }}</span>
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
        <ion-button expand="block" class="done-btn" @click="goBack"> Done </ion-button>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButton, IonButtons, IonIcon, toastController } from '@ionic/vue';
import { arrowBackOutline, walletOutline, checkmarkCircle, chevronForwardOutline, downloadOutline, shareSocialOutline } from 'ionicons/icons';
import { api } from '@/utils/apiClient';
import { type PaymentMethod } from '@/types';
import { downloadReceipt, shareReceiptWithFallback, type ReceiptData } from '@/utils/receiptGenerator';

const router = useRouter();

function goToHistory() {
  router.push('/wallet/transactions');
}

const balance = ref(0);
const amount = ref(100);
const selectedPM = ref<number | null>(null);
const paymentMethods = ref([] as PaymentMethod[]);
const processing = ref(false);
const topUpSuccess = ref(false);
const topUpAmount = ref(0);
const topUpMethod = ref('');
const newBalance = ref(0);
const error = ref<string | null>(null);
const topUpTransactionId = ref(0);
const receiptDate = ref('');

const receiptData = computed<ReceiptData | null>(() => {
  if (!topUpSuccess.value || !topUpTransactionId.value) return null;
  return {
    transactionId: 'WLT-' + String(topUpTransactionId.value).padStart(6, '0'),
    title: 'Wallet Top-Up Receipt',
    amount: topUpAmount.value,
    date: receiptDate.value,
    rows: [
      { label: 'Type', value: 'Wallet Top-Up' },
      { label: 'Payment Method', value: topUpMethod.value },
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

const quickAmounts = [100, 200, 500, 1000];

const canSubmit = computed(() => amount.value > 0 && selectedPM.value);

function getEmail() {
  return localStorage.getItem('userEmail');
}

function pmLogo(name: string) {
  const logos: Record<string, string | undefined> = {
    GCash: '/assets/payment-methods-logo/gcash.png',
    PayPal: '/assets/payment-methods-logo/paypal.png',
    Stripe: '/assets/payment-methods-logo/cards.png',
  };
  return logos[name];
}

async function fetchBalance() {
  const email = getEmail();
  if (!email) return;
  try {
    const data = await api.get(`/seller/wallet/balance?email=${encodeURIComponent(email)}`);
    balance.value = data.balance || 0;
  } catch {
    /* silent */
  }
}

async function fetchPaymentMethods() {
  try {
    const json = await api.get('/payment-methods');
    const methods = Array.isArray(json) ? json : json.methods || [];
    const exclude = ['cash on delivery', 'cod', 'palengke wallet'];
    paymentMethods.value = methods.filter((m: any) => !exclude.includes(m.name.toLowerCase()));
  } catch {
    paymentMethods.value = [
      { id: 1, name: 'GCash', color: '#059669' },
      { id: 2, name: 'PayPal', color: '#2563eb' },
      { id: 3, name: 'Stripe', color: '#7c3aed' },
    ];
  }
}

async function processTopUp() {
  const email = getEmail();
  if (!email || !canSubmit.value) return;

  processing.value = true;
  error.value = null;

  try {
    const json = await api.post('/seller/wallet/top-up', {
      email,
      amount: amount.value,
      payment_method_id: selectedPM.value,
    });
    topUpAmount.value = json.amount_added;
    topUpMethod.value = json.payment_method;
    newBalance.value = json.balance;
    balance.value = json.balance;
    topUpTransactionId.value = json.transaction_id;
    receiptDate.value = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    topUpSuccess.value = true;
    processing.value = false;
  } catch (e: any) {
    error.value = e.message || 'Network error.';
    processing.value = false;
  }
}

function goBack() {
  router.back();
}

onMounted(() => {
  fetchBalance();
  fetchPaymentMethods();
});
</script>

<style scoped>
.wallet-container {
  display: flex;
  flex-direction: column;
  gap: 24px;
  padding-bottom: 24px;
}
.balance-card {
  background: linear-gradient(135deg, #059669, #047857);
  border-radius: 16px;
  padding: 28px 20px;
  text-align: center;
  color: white;
  margin-top: 8px;
}
.balance-icon {
  font-size: 32px;
  margin-bottom: 8px;
  opacity: 0.9;
}
.balance-label {
  font-size: 13px;
  opacity: 0.8;
  display: block;
}
.balance-amount {
  font-size: 36px;
  font-weight: 700;
  display: block;
  margin-top: 4px;
}
.section-title {
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 16px;
}
.amount-section {
  margin-bottom: 24px;
}
.input-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 8px;
}
.amount-input-wrap {
  display: flex;
  align-items: center;
  gap: 4px;
  background: #fff;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  padding: 4px 16px;
  transition: border-color 0.15s;
}
.amount-input-wrap:focus-within {
  border-color: #059669;
}
.currency-symbol {
  font-size: 20px;
  font-weight: 700;
  color: #9ca3af;
}
.amount-input {
  flex: 1;
  border: none;
  font-size: 24px;
  font-weight: 700;
  color: #111827;
  outline: none;
  padding: 10px 0;
  width: 100%;
  font-family: inherit;
}
.quick-amounts {
  display: flex;
  gap: 8px;
  margin-top: 12px;
  flex-wrap: wrap;
}
.quick-chip {
  padding: 8px 16px;
  border-radius: 20px;
  border: 1.5px solid #e5e7eb;
  background: #fff;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  transition: all 0.15s;
  font-family: inherit;
}
.quick-chip:hover {
  border-color: #059669;
}
.quick-chip.active {
  background: #f0fdf4;
  border-color: #059669;
  color: #059669;
}
.pm-section {
  margin-bottom: 16px;
}
.pm-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  margin-bottom: 8px;
  cursor: pointer;
  transition: all 0.15s;
}
.pm-item:hover {
  border-color: #059669;
}
.pm-item.selected {
  border-color: #059669;
  background: #f0fdf4;
}
.pm-img {
  width: 32px;
  height: 32px;
  object-fit: contain;
}
.pm-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  flex-shrink: 0;
}
.pm-name {
  flex: 1;
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}
.pm-radio {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #d1d5db;
  transition: all 0.15s;
  flex-shrink: 0;
}
.pm-radio.selected {
  border-color: #059669;
  background: #059669;
  box-shadow: inset 0 0 0 4px #fff;
}
.error-box {
  background: #fef2f2;
  color: #dc2626;
  font-size: 13px;
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 12px;
}
.topup-btn {
  --background: #059669;
  --border-radius: 12px;
  font-weight: 600;
  font-size: 15px;
  margin: 0;
}
.topup-btn:disabled {
  opacity: 0.5;
}
.center-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}
.center-state h3 {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin: 16px 0 4px;
}
.center-state p {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
}
.center-state.success .success-icon {
  font-size: 56px;
  color: #059669;
}
.center-state.success .success-sub {
  margin-bottom: 16px;
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
.success-pm {
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 600;
}
.success-pm-img {
  width: 22px;
  height: 22px;
  border-radius: 4px;
  object-fit: contain;
}
.success-txn {
  font-family: 'Courier New', monospace;
  font-size: 12px !important;
  letter-spacing: 0.5px;
}
.done-btn {
  --background: #059669;
  --border-radius: 12px;
  font-weight: 600;
  margin: 0;
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
.history-link {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  padding: 14px 16px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  color: #059669;
  transition: border-color 0.15s;
}
.history-link:hover {
  border-color: #059669;
}
.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top-color: #059669;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
