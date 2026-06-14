<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Subscription Plans</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="plans-container">
        <!-- Status Banner -->
        <div class="status-banner" :class="sub.status">
          <div class="status-icon">
            <ion-icon :icon="sub.status === 'trial' ? timeOutline : sub.status === 'active' ? checkmarkCircleOutline : alertCircleOutline"></ion-icon>
          </div>
          <div class="status-info">
            <p class="status-title" v-if="sub.status === 'trial'">Trial Period</p>
            <p class="status-title" v-else-if="sub.status === 'active'">Active Plan</p>
            <p class="status-title" v-else>Trial Expired</p>
            <p class="status-days" v-if="remaining > 0">{{ remaining }} day{{ remaining > 1 ? 's' : '' }} remaining</p>
            <p class="status-days" v-else>Choose a plan to continue selling</p>
          </div>
        </div>

        <!-- Plans Grid -->
        <div class="plans-grid">
          <div
            v-for="plan in plans"
            :key="plan.id"
            class="plan-card"
            :class="{ popular: plan.popular, selected: selected === plan.id }"
            @click="selected = plan.id"
          >
            <div class="plan-badge" v-if="plan.popular">Best Value</div>
            <div class="plan-header">
              <h3 class="plan-name">{{ plan.name }}</h3>
              <div class="plan-price">
                <span class="price-symbol">₱</span>
                <span class="price-value">{{ plan.price.toLocaleString() }}</span>
              </div>
              <p class="plan-per" v-if="plan.savings">{{ plan.savings }}</p>
            </div>
            <div class="plan-features">
              <div class="feature" v-for="feature in plan.features" :key="feature">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
                <span>{{ feature }}</span>
              </div>
            </div>
            <ion-button expand="block" class="select-btn" :fill="plan.popular ? 'solid' : 'outline'" @click.stop="selected = plan.id">
              {{ selected === plan.id ? 'Selected' : 'Select' }}
            </ion-button>
          </div>
        </div>

        <!-- Payment Methods -->
        <div class="payment-section" v-if="selected">
          <h4 class="payment-label">Select Payment Method</h4>
          <div class="payment-methods">
            <div
              v-for="pm in paymentMethods"
              :key="pm.id"
              class="payment-item"
              :class="{ selected: selectedPayment === pm.id, disabled: pm.coming_soon }"
              @click="selectPayment(pm)"
            >
              <img
                v-if="pmLogo(pm.name)"
                :src="pmLogo(pm.name)"
                :alt="pm.name"
                class="payment-img"
              >
              <div v-else class="payment-dot" :style="{ background: pm.color }"></div>
              <div class="pm-info">
                <span class="pm-name">{{ pm.name }}</span>
                <span class="pm-desc">{{ pm.coming_soon ? 'Coming Soon' : pm.description }}</span>
                <span class="pm-balance" v-if="pm.name === 'Palengke Wallet'">Balance • ₱{{ walletBalance.toLocaleString() }}</span>
              </div>
              <div class="radio-btn" :class="{ selected: selectedPayment === pm.id }"></div>
              <span class="coming-tag" v-if="pm.coming_soon">Coming Soon</span>
            </div>
          </div>
        </div>

        <!-- Subscribe Button -->
        <ion-button expand="block" class="subscribe-btn" :disabled="!selected || !selectedPayment" @click="openPaymentModal">
          Subscribe Now — ₱{{ selectedPlan?.price.toLocaleString() }}
        </ion-button>
        <p class="secure-note">
          <ion-icon :icon="lockClosedOutline"></ion-icon>
          Secure payment. Your info is protected.
        </p>

        <!-- Logout -->
        <div class="logout-section">
          <ion-button expand="block" fill="clear" class="logout-btn" @click="handleLogout">
            <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
            Logout
          </ion-button>
        </div>
      </div>
    </ion-content>

    <!-- Payment Modal -->
    <div class="modal-overlay" v-if="showPaymentModal" @click.self="closePaymentModal">
      <div class="payment-modal">
        <div v-if="paymentStep === 'confirm'">
          <div class="modal-header">
            <h3>Complete Payment</h3>
            <button class="modal-close" @click="closePaymentModal">&times;</button>
          </div>

          <div class="modal-plan-summary">
            <div class="summary-row">
              <span class="summary-label">Plan</span>
              <span class="summary-value">{{ selectedPlan?.name }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Amount</span>
              <span class="summary-value price">₱{{ selectedPlan?.price.toLocaleString() }}</span>
            </div>
          </div>

          <div class="modal-pm-section">
            <span class="modal-pm-label">Paying with</span>
            <div class="modal-pm-card">
              <img
                v-if="pmLogo(selectedPM?.name)"
                :src="pmLogo(selectedPM?.name)"
                :alt="selectedPM?.name"
                class="modal-pm-img"
              >
              <div v-else class="modal-pm-dot" :style="{ background: selectedPM?.color }"></div>
              <div class="modal-pm-info">
                <span class="modal-pm-name">{{ selectedPM?.name }}</span>
                <span class="modal-pm-desc">{{ selectedPM?.description }}</span>
              </div>
            </div>
          </div>

          <button class="pay-now-btn" @click="processPayment">
            <ion-icon :icon="lockClosedOutline"></ion-icon>
            Pay ₱{{ selectedPlan?.price.toLocaleString() }}
          </button>
          <button class="cancel-btn" @click="closePaymentModal">Cancel</button>
        </div>

        <div v-else-if="paymentStep === 'processing'">
          <div class="processing-state">
            <div class="spinner"></div>
            <h3>Processing Payment</h3>
            <p>Please wait while we process your payment...</p>
          </div>
        </div>

        <div v-else-if="paymentStep === 'success'">
          <div class="success-state">
            <div class="success-icon">
              <ion-icon :icon="checkmarkCircle"></ion-icon>
            </div>
            <h3>Payment Successful!</h3>
            <p class="success-sub">{{ selectedPlan?.name }} plan activated via {{ selectedPM?.name }}</p>
            <div class="success-details">
              <div class="success-row">
                <span>Amount Paid</span>
                <span class="success-price">₱{{ selectedPlan?.price.toLocaleString() }}</span>
              </div>
              <div class="success-row">
                <span>Valid Until</span>
                <span>{{ sub.planEnd || sub.trialEnd }}</span>
              </div>
            </div>
            <button class="done-btn" @click="goHome">Done</button>
          </div>
        </div>

        <div v-else-if="paymentStep === 'insufficient'">
          <div class="insufficient-state">
            <div class="insufficient-icon">
              <ion-icon :icon="alertCircleOutline"></ion-icon>
            </div>
            <h3>Insufficient Balance</h3>
            <p class="insufficient-sub">Your {{ selectedPM?.name }} balance is not enough to complete this payment.</p>
            <div class="insufficient-details">
              <div class="insufficient-row">
                <span>Wallet Balance</span>
                <span class="insufficient-amount">₱{{ walletBalance.toLocaleString() }}</span>
              </div>
              <div class="insufficient-row">
                <span>Payment Required</span>
                <span class="insufficient-amount required">₱{{ selectedPlan?.price.toLocaleString() }}</span>
              </div>
              <div class="insufficient-row shortfall-row">
                <span>Shortfall</span>
                <span class="insufficient-amount shortfall">-₱{{ (shortfall).toLocaleString() }}</span>
              </div>
            </div>
            <p class="insufficient-hint">Please top up your wallet or choose a different payment method.</p>
            <button class="change-pm-btn" @click="backToConfirm">
              <ion-icon :icon="arrowBackOutline"></ion-icon>
              Choose Another Method
            </button>
            <button class="cancel-btn" @click="closePaymentModal">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButton, IonButtons, IonIcon, alertController, toastController
} from '@ionic/vue'
import {
  arrowBackOutline, timeOutline, checkmarkCircleOutline,
  alertCircleOutline, checkmarkOutline, lockClosedOutline, logOutOutline,
  checkmarkCircle
} from 'ionicons/icons'
import { PLANS, getSubscription, daysLeft, refreshSubscription, subscribePlan } from '@/services/subscriptionService'
import { API_BASE } from '@/config/api'

const router = useRouter()
const sub = ref(getSubscription())
const remaining = ref(daysLeft(sub.value))
const plans = PLANS
const selected = ref('')
const selectedPayment = ref(null)
const paymentMethods = ref([])
const subscribing = ref(false)

const showPaymentModal = ref(false)
const paymentStep = ref('confirm')
const walletBalance = ref(0)
const shortfall = ref(0)

const selectedPlan = computed(() => plans.find(p => p.id === selected.value))
const selectedPM = computed(() => paymentMethods.value.find(p => p.id === selectedPayment.value))

async function fetchWalletBalance() {
  const email = localStorage.getItem('userEmail')
  if (!email) return
  try {
    const res = await fetch(`${API_BASE}/seller/wallet/balance?email=${encodeURIComponent(email)}`, {
      headers: { 'Accept': 'application/json' },
    })
    if (res.ok) {
      const data = await res.json()
      walletBalance.value = data.balance || 0
    }
  } catch { /* silent */ }
}

const goBack = () => router.back()

refreshSubscription().then(s => {
  sub.value = s
  remaining.value = daysLeft(s)
})

onMounted(async () => {
  try {
    const res = await fetch(`${API_BASE}/payment-methods`, {
      headers: { 'Accept': 'application/json' },
    })
    if (res.ok) {
      const all = await res.json()
      paymentMethods.value = all.filter(p => p.name !== 'Cash on Delivery')
      const first = paymentMethods.value.find(p => !p.coming_soon)
      if (first) selectedPayment.value = first.id
    }
  } catch { /* silent */ }
  fetchWalletBalance()
})

const pmLogo = (name) => {
  const map = {
    'Palengke Wallet': '/assets/payment-methods-logo/palengke-wallet.png',
    'GCash': '/assets/payment-methods-logo/gcash.png',
    'PayPal': '/assets/payment-methods-logo/paypal.png',
    'Stripe': '/assets/payment-methods-logo/cards.png',
    'Cash on Delivery': null,
  }
  return map[name] || null
}

const selectPayment = (pm) => {
  if (pm.coming_soon) return
  selectedPayment.value = pm.id
}

const openPaymentModal = async () => {
  if (!selected.value || !selectedPayment.value) return
  // Pre-check wallet balance client-side for instant feedback
  const pm = paymentMethods.value.find(p => p.id === selectedPayment.value)
  if (pm && pm.name === 'Palengke Wallet') {
    await fetchWalletBalance()
    const price = selectedPlan.value?.price || 0
    if (walletBalance.value < price) {
      shortfall.value = price - walletBalance.value
      paymentStep.value = 'insufficient'
      showPaymentModal.value = true
      return
    }
  }
  paymentStep.value = 'confirm'
  showPaymentModal.value = true
}

const closePaymentModal = () => {
  if (paymentStep.value === 'processing') return
  showPaymentModal.value = false
}

const backToConfirm = () => {
  paymentStep.value = 'confirm'
}

const processPayment = async () => {
  paymentStep.value = 'processing'
  subscribing.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 2000))
    await subscribePlan(selected.value, selectedPayment.value)
    sub.value = await refreshSubscription()
    remaining.value = daysLeft(sub.value)
    walletBalance.value = sub.value.planId ? 0 : walletBalance.value
    paymentStep.value = 'success'
  } catch (err) {
    if (err && err.code === 'insufficient_balance') {
      shortfall.value = err.shortfall || 0
      walletBalance.value = err.balance || 0
      paymentStep.value = 'insufficient'
    } else {
      const toast = await toastController.create({
        message: 'Payment failed. Please try again.',
        duration: 2000, color: 'danger', position: 'top',
      })
      await toast.present()
      closePaymentModal()
    }
  } finally {
    subscribing.value = false
  }
}

const goHome = () => {
  showPaymentModal.value = false
  router.push('/home')
}

const handleLogout = async () => {
  const alert = await alertController.create({
    header: 'Logout',
    message: 'Are you sure you want to logout?',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Logout', role: 'destructive',
        handler: async () => {
          const email = localStorage.getItem('userEmail')
          try {
            await fetch(`${API_BASE}/seller/devices/logout-current`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email }),
            })
          } catch { /* silent */ }
          ;['isAuthenticated', 'token', 'pendingAuth'].forEach(k => localStorage.removeItem(k))
          const toast = await toastController.create({
            message: 'Logged out successfully.',
            duration: 1500, color: 'success', position: 'top',
          })
          await toast.present()
          setTimeout(() => router.push('/login'), 500)
        }
      }
    ]
  })
  await alert.present()
}
</script>

<style scoped>
ion-toolbar { --background: transparent; --border-width: 0; }
ion-toolbar ion-title { font-size: 18px; font-weight: 700; }
ion-toolbar ion-button { --color: var(--color-text-secondary); }

.plans-container {
  max-width: 500px;
  margin: 0 auto;
  padding-bottom: 40px;
}

/* Status Banner */
.status-banner {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px;
  border-radius: 14px;
  margin-bottom: 24px;
}
.status-banner.trial { background: #E3F2FD; color: #1565C0; }
.status-banner.active { background: #E8F5E9; color: #2E7D32; }
.status-banner.expired { background: #FFEBEE; color: #c62828; }
.status-icon ion-icon { font-size: 28px; }
.status-title { font-size: 15px; font-weight: 700; margin: 0 0 2px; }
.status-days { font-size: 13px; margin: 0; opacity: 0.85; }

/* Plans Grid */
.plans-grid { display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px; }
.plan-card {
  position: relative;
  background: var(--color-card-bg, #fff);
  border: 2px solid var(--color-border, #e0e0e0);
  border-radius: 16px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.2s;
}
.plan-card.popular { border-color: #4CAF50; }
.plan-card.selected { border-color: #4CAF50; background: #f0fdf4; }
.plan-card:active { transform: scale(0.98); }
.plan-badge {
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translateX(-50%);
  background: #4CAF50;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 14px;
  border-radius: 20px;
  white-space: nowrap;
}
.plan-header { text-align: center; margin-bottom: 16px; padding-top: 4px; }
.plan-name {
  font-size: 16px;
  font-weight: 600;
  color: var(--color-text-primary, #1a1a1a);
  margin: 0 0 8px;
}
.plan-price { display: flex; align-items: baseline; justify-content: center; gap: 2px; }
.price-symbol { font-size: 18px; font-weight: 600; color: var(--color-text-primary, #1a1a1a); }
.price-value { font-size: 32px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--color-text-primary, #1a1a1a); }
.plan-per { font-size: 12px; color: #4CAF50; font-weight: 500; margin: 4px 0 0; }
.plan-features { display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px; }
.feature {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--color-text-secondary, #666);
}
.feature ion-icon { font-size: 16px; color: #4CAF50; flex-shrink: 0; }
.select-btn {
  --border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  text-transform: none;
  height: 42px;
}

/* Subscribe Button */
.subscribe-btn {
  --border-radius: 14px;
  height: 54px;
  font-size: 16px;
  font-weight: 700;
  text-transform: none;
  --background: #4CAF50;
  margin-bottom: 12px;
}
.secure-note {
  text-align: center;
  font-size: 12px;
  color: var(--color-text-tertiary, #999);
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}
.secure-note ion-icon { font-size: 14px; }

/* Payment Methods */
.payment-section { margin-bottom: 20px; }
.payment-label { font-size: 14px; font-weight: 600; color: var(--color-text-primary, #1a1a1a); margin: 0 0 10px; }
.payment-methods { display: flex; flex-direction: column; }
.payment-item {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 16px;
  border: 2px solid var(--color-border, #e0e0e0);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s;
  margin-bottom: 10px;
}
.payment-item.selected { border-color: #4CAF50; background: #f0fdf4; }
.payment-item.disabled { opacity: 0.5; cursor: default; }
.payment-item:active:not(.disabled) { transform: scale(0.98); }
.payment-img {
  width: 48px; height: 48px; border-radius: 14px;
  object-fit: contain; background: #f5f5f5; padding: 4px;
  flex-shrink: 0;
}
.payment-dot { width: 48px; height: 48px; border-radius: 14px; flex-shrink: 0; }
.pm-info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.pm-name { font-size: 15px; font-weight: 700; color: var(--color-text-primary, #1a1a1a); }
.pm-desc { font-size: 13px; color: var(--color-text-tertiary, #999); }
.pm-balance { font-size: 12px; color: #059669; font-weight: 600; margin-top: 2px; }
.radio-btn {
  width: 22px; height: 22px; border-radius: 50%;
  border: 2px solid #e0e0e0; flex-shrink: 0; transition: all 0.2s;
}
.radio-btn.selected {
  border-color: #4CAF50; background: #4CAF50;
  box-shadow: 0 0 0 3px rgba(76,175,80,0.2);
}
.coming-tag {
  font-size: 10px; font-weight: 700; color: #f59e0b;
  background: #fef3c7; padding: 3px 8px; border-radius: 6px;
  white-space: nowrap; flex-shrink: 0;
}

/* Payment Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  z-index: 9999;
  animation: fadeIn 0.2s;
}
.payment-modal {
  background: #fff;
  width: 100%;
  max-width: 500px;
  border-radius: 20px 20px 0 0;
  padding: 24px 20px 32px;
  animation: slideUp 0.3s ease;
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}
.modal-header h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}
.modal-close {
  background: none;
  border: none;
  font-size: 28px;
  color: #999;
  cursor: pointer;
  padding: 0;
  line-height: 1;
}

/* Plan Summary */
.modal-plan-summary {
  background: #f9fafb;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 20px;
}
.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 0;
}
.summary-row + .summary-row { border-top: 1px solid #e5e7eb; }
.summary-label { font-size: 14px; color: #6b7280; }
.summary-value { font-size: 14px; font-weight: 600; color: #1a1a1a; }
.summary-value.price { color: #059669; font-size: 18px; font-weight: 800; }

/* Payment Method in Modal */
.modal-pm-section { margin-bottom: 24px; }
.modal-pm-label { font-size: 13px; color: #6b7280; margin-bottom: 8px; display: block; }
.modal-pm-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  background: #fff;
}
.modal-pm-dot { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; }
.modal-pm-img {
  width: 40px; height: 40px; border-radius: 10px;
  object-fit: contain; background: #f5f5f5; padding: 3px;
  flex-shrink: 0;
}
.modal-pm-info { flex: 1; }
.modal-pm-name { font-size: 14px; font-weight: 600; color: #1a1a1a; display: block; }
.modal-pm-desc { font-size: 12px; color: #9ca3af; }

/* Pay Button */
.pay-now-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 16px;
  background: #4CAF50;
  color: #fff;
  border: none;
  border-radius: 14px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  margin-bottom: 10px;
  transition: background 0.2s;
}
.pay-now-btn:hover { background: #43A047; }
.pay-now-btn:active { transform: scale(0.98); }
.cancel-btn {
  width: 100%;
  padding: 12px;
  background: none;
  border: none;
  font-size: 14px;
  color: #9ca3af;
  cursor: pointer;
  font-weight: 600;
}

/* Processing State */
.processing-state {
  text-align: center;
  padding: 40px 0;
}
.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #e5e7eb;
  border-top-color: #4CAF50;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 20px;
}
.processing-state h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px;
}
.processing-state p {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

/* Success State */
.success-state {
  text-align: center;
  padding: 20px 0;
}
.success-icon ion-icon {
  font-size: 64px;
  color: #4CAF50;
  margin-bottom: 16px;
}
.success-state h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 6px;
}
.success-sub {
  font-size: 14px;
  color: #6b7280;
  margin: 0 0 24px;
}
.success-details {
  background: #f0fdf4;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 24px;
  text-align: left;
}
.success-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  padding: 6px 0;
  color: #374151;
}
.success-row + .success-row { border-top: 1px solid #d1fae5; }
.success-price { font-weight: 700; color: #059669; }
.done-btn {
  width: 100%;
  padding: 16px;
  background: #4CAF50;
  color: #fff;
  border: none;
  border-radius: 14px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
}

/* Insufficient Balance State */
.insufficient-state {
  text-align: center;
  padding: 10px 0;
}
.insufficient-icon ion-icon {
  font-size: 56px;
  color: #f59e0b;
  margin-bottom: 12px;
}
.insufficient-state h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 6px;
}
.insufficient-sub {
  font-size: 14px;
  color: #6b7280;
  margin: 0 0 20px;
  line-height: 1.4;
}
.insufficient-details {
  background: #fffbeb;
  border: 1px solid #fde68a;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
  text-align: left;
}
.insufficient-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  padding: 6px 0;
  color: #374151;
}
.insufficient-row + .insufficient-row { border-top: 1px solid #fde68a; }
.insufficient-amount { font-weight: 600; color: #1a1a1a; }
.insufficient-amount.required { color: #dc2626; }
.shortfall-row { background: #fef2f2; margin: 4px -8px 0; padding: 6px 8px; border-radius: 6px; }
.insufficient-amount.shortfall { color: #dc2626; font-weight: 700; }
.insufficient-hint {
  font-size: 13px;
  color: #6b7280;
  margin: 0 0 20px;
  line-height: 1.4;
}
.change-pm-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 16px;
  background: #f59e0b;
  color: #fff;
  border: none;
  border-radius: 14px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  margin-bottom: 10px;
  transition: background 0.2s;
}
.change-pm-btn:hover { background: #d97706; }
.change-pm-btn:active { transform: scale(0.98); }

/* Logout */
.logout-section { margin-top: 32px; border-top: 1px solid var(--color-border, #e0e0e0); padding-top: 20px; }
.logout-btn {
  --color: var(--ion-color-danger, #c62828);
  font-size: 14px;
  font-weight: 600;
  text-transform: none;
}

/* Animations */
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes spin { to { transform: rotate(360deg); } }

/* Dark mode overrides */
@media (prefers-color-scheme: dark) {
  .plan-card { background: #2d2d2d; border-color: #404040; }
  .plan-card.selected { background: rgba(76, 175, 80, 0.1); border-color: #66BB6A; }
  .plan-badge { background: #66BB6A; }
  .payment-item { background: #2d2d2d; border-color: #404040; }
  .payment-item.selected { background: rgba(76, 175, 80, 0.1); border-color: #66BB6A; }
  .payment-img { background: #3d3d3d; }
  .pm-balance { color: #4CAF50; }
  .payment-modal { background: #1e1e1e; }
  .modal-header h3 { color: #e5e5e5; }
  .modal-plan-summary { background: #2d2d2d; }
  .summary-label { color: #9ca3af; }
  .summary-value { color: #e5e5e5; }
  .modal-pm-card { background: #2d2d2d; border-color: #404040; }
  .modal-pm-name { color: #e5e5e5; }
  .modal-pm-img { background: #3d3d3d; }
  .success-details { background: rgba(76, 175, 80, 0.1); }
  .success-row { color: #d1d5db; }
  .success-state h3 { color: #e5e5e5; }
  .processing-state h3 { color: #e5e5e5; }
  .insufficient-state h3 { color: #e5e5e5; }
  .insufficient-details { background: #2d2d2d; border-color: #f59e0b; }
  .insufficient-row { color: #d1d5db; }
  .insufficient-row + .insufficient-row { border-top-color: #404040; }
  .shortfall-row { background: rgba(220, 38, 38, 0.1); }
}
</style>
