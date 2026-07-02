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
        <p class="success-sub">₱{{ topUpAmount }} added via {{ topUpMethod }}</p>
        <div class="success-balance">New Balance: ₱{{ newBalance.toLocaleString() }}</div>
        <ion-button expand="block" class="done-btn" @click="goBack"> Done </ion-button>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButton, IonButtons, IonIcon } from '@ionic/vue';
import { arrowBackOutline, walletOutline, checkmarkCircle, chevronForwardOutline } from 'ionicons/icons';
import { API_BASE } from '@/config/api';
import { type PaymentMethod } from '@/types';

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
    const res = await fetch(`${API_BASE}/seller/wallet/balance?email=${encodeURIComponent(email)}`, {
      headers: { Accept: 'application/json' },
    });
    if (res.ok) {
      const data = await res.json();
      balance.value = data.balance || 0;
    }
  } catch {
    /* silent */
  }
}

async function fetchPaymentMethods() {
  try {
    const res = await fetch(`${API_BASE}/payment-methods`, {
      headers: { Accept: 'application/json' },
    });
    if (res.ok) {
      const json = await res.json();
      const methods = Array.isArray(json) ? json : json.methods || [];
      const exclude = ['cash on delivery', 'cod', 'palengke wallet'];
      paymentMethods.value = methods.filter((m: any) => !exclude.includes(m.name.toLowerCase()));
    }
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
    const res = await fetch(`${API_BASE}/seller/wallet/top-up`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({
        email,
        amount: amount.value,
        payment_method_id: selectedPM.value,
      }),
    });
    const json = await res.json();
    if (!res.ok) {
      error.value = json.message || 'Top up failed.';
      processing.value = false;
      return;
    }
    topUpAmount.value = json.amount_added;
    topUpMethod.value = json.payment_method;
    newBalance.value = json.balance;
    balance.value = json.balance;
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
.success-balance {
  font-size: 20px;
  font-weight: 700;
  color: #059669;
  margin-bottom: 24px;
}
.done-btn {
  --background: #059669;
  --border-radius: 12px;
  font-weight: 600;
  margin: 0;
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
