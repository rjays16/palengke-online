<template>
  <ion-page>
    <ion-content :fullscreen="true" class="tip-content">
      <div class="tip-container">

        <!-- Confetti Animation -->
        <div class="confetti-wrapper">
          <div
            v-for="i in 20"
            :key="i"
            class="confetti"
            :style="getConfettiStyle(i)"
          ></div>
        </div>

        <!-- Main Content -->
        <div class="tip-main">

          <!-- Icon -->
          <div class="tip-icon-wrapper">
            <div class="tip-icon-outer">
              <div class="tip-icon-inner">
                <font-awesome-icon :icon="['fas', 'money-bill']" class="tip-emoji" />
              </div>
            </div>
            <!-- Pulse rings -->
            <div class="pulse-ring ring1"></div>
            <div class="pulse-ring ring2"></div>
            <div class="pulse-ring ring3"></div>
          </div>

          <!-- Text -->
          <h1 class="tip-title">You Got a Tip!</h1>
          <p class="tip-subtitle">A customer appreciated your service</p>

          <!-- Tip Amount -->
          <div class="tip-amount-card">
            <p class="tip-amount-label">Tip Amount</p>
            <h2 class="tip-amount">₱{{ tipData.amount.toFixed(2) }}</h2>
          </div>

          <!-- From Customer -->
          <div class="from-customer">
            <div class="customer-avatar">
              <ion-icon :icon="personOutline"></ion-icon>
            </div>
            <div class="customer-info">
              <p class="customer-label">From</p>
              <p class="customer-name">{{ tipData.customerName }}</p>
            </div>
          </div>

          <!-- Message (if any) -->
          <div class="tip-message" v-if="tipData.message">
            <ion-icon :icon="chatbubbleOutline"></ion-icon>
            <p>"{{ tipData.message }}"</p>
          </div>

          <!-- Order Info -->
          <div class="order-info-card">
            <div class="order-info-row">
              <span class="info-label">Order</span>
              <span class="info-value">#{{ tipData.orderId }}</span>
            </div>
            <div class="order-info-row">
              <span class="info-label">Delivery Fee</span>
              <span class="info-value">₱{{ tipData.deliveryFee.toFixed(2) }}</span>
            </div>
            <div class="order-info-row">
              <span class="info-label">Tip</span>
              <span class="info-value tip-green">+ ₱{{ tipData.amount.toFixed(2) }}</span>
            </div>
            <div class="order-info-divider"></div>
            <div class="order-info-row total">
              <span class="info-label">Total Earned</span>
              <span class="info-value total">₱{{ (tipData.deliveryFee + tipData.amount).toFixed(2) }}</span>
            </div>
          </div>

          <!-- Today's Tips Summary -->
          <div class="today-tips-card">
            <ion-icon :icon="trophyOutline"></ion-icon>
            <div>
              <p class="today-label">Total Tips Today</p>
              <p class="today-amount">₱{{ totalTipsToday.toFixed(2) }}</p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <ion-button
              expand="block"
              class="continue-btn"
              @click="continueDelivering"
            >
              <ion-icon slot="start" :icon="bicycleOutline"></ion-icon>
              Continue Delivering
            </ion-button>

            <ion-button
              expand="block"
              fill="outline"
              color="medium"
              class="view-earnings-btn"
              @click="viewEarnings"
            >
              <ion-icon slot="start" :icon="cashOutline"></ion-icon>
              View Earnings
            </ion-button>
          </div>

        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage, IonContent, IonButton, IonIcon
} from '@ionic/vue';
import {
  personOutline, chatbubbleOutline, trophyOutline,
  bicycleOutline, cashOutline
} from 'ionicons/icons';
const router = useRouter();
const route = useRoute();

import { API_BASE } from '@/services/api';

const tipData = ref({
  amount: 0,
  customerName: '',
  message: '',
  orderId: '',
  deliveryFee: 0,
});

const totalTipsToday = ref(0);

const confettiColors = ['#4CAF50', '#FFD700', '#FF9800', '#2196F3', '#E91E63', '#9C27B0'];

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('token');
}

const qNum = (val) => {
  const n = parseFloat(String(val ?? ''));
  return isNaN(n) ? null : n;
};

const qStr = (val) => (val != null && val !== '' ? String(val) : null);

const applyRouteParams = () => {
  const amount = qNum(route.query.amount);
  const deliveryFee = qNum(route.query.deliveryFee);
  const customerName = qStr(route.query.customerName);
  const message = qStr(route.query.message);
  const orderId = qStr(route.query.orderId);

  if (amount != null) tipData.value.amount = amount;
  if (deliveryFee != null) tipData.value.deliveryFee = deliveryFee;
  if (customerName) tipData.value.customerName = customerName;
  if (message) tipData.value.message = message;
  if (orderId) tipData.value.orderId = orderId;
};

const fetchLatestTip = async () => {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/tips/latest`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      const tip = data.tip || {};
      tipData.value = {
        amount: qNum(tip.amount) ?? 0,
        customerName: tip.customer_name || '',
        message: tip.message || '',
        orderId: tip.order_id != null ? String(tip.order_id) : '',
        deliveryFee: qNum(tip.delivery_fee) ?? 0,
      };
      totalTipsToday.value = qNum(data.total_tips_today) ?? 0;
    }
  } catch { /* ignore */ }
};

const getConfettiStyle = (i) => {
  const color = confettiColors[i % confettiColors.length];
  const left = Math.random() * 100;
  const delay = Math.random() * 3;
  const duration = 2 + Math.random() * 3;
  const size = 6 + Math.random() * 8;
  return {
    left: left + '%',
    background: color,
    width: size + 'px',
    height: size + 'px',
    animationDelay: delay + 's',
    animationDuration: duration + 's',
    borderRadius: Math.random() > 0.5 ? '50%' : '0'
  };
};

// Navigation
const continueDelivering = () => router.push('/home');
const viewEarnings = () => router.push('/earnings');

onMounted(async () => {
  await fetchLatestTip();
  applyRouteParams();
});
</script>

<style scoped>
.tip-content {
  --background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
}

.tip-container {
  min-height: 100vh;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
}

/* Confetti */
.confetti-wrapper {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 100%;
  pointer-events: none;
  z-index: 0;
}

.confetti {
  position: absolute;
  top: -20px;
  animation: confettiFall linear infinite;
}

@keyframes confettiFall {
  0% { transform: translateY(-20px) rotate(0deg); opacity: 1; }
  100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
}

/* Main Content */
.tip-main {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 400px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

/* Tip Icon */
.tip-icon-wrapper {
  position: relative;
  margin-bottom: 32px;
}

.tip-icon-outer {
  width: 100px; height: 100px;
  background: linear-gradient(135deg, #FFD700, #FF9800);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 8px 32px rgba(255,215,0,0.4);
  animation: bounceIn 0.6s ease-out;
}

.tip-icon-inner {
  width: 80px; height: 80px;
  background: white;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}

.tip-emoji { font-size: 44px; color: #4CAF50; }

@keyframes bounceIn {
  0% { transform: scale(0); opacity: 0; }
  50% { transform: scale(1.2); }
  100% { transform: scale(1); opacity: 1; }
}

/* Pulse Rings */
.pulse-ring {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  border-radius: 50%;
  border: 3px solid rgba(255, 215, 0, 0.4);
  animation: pulseRing 2s ease-out infinite;
}

.ring1 { width: 120px; height: 120px; animation-delay: 0s; }
.ring2 { width: 150px; height: 150px; animation-delay: 0.5s; }
.ring3 { width: 180px; height: 180px; animation-delay: 1s; }

@keyframes pulseRing {
  0% { transform: translate(-50%, -50%) scale(0.8); opacity: 1; }
  100% { transform: translate(-50%, -50%) scale(1.2); opacity: 0; }
}

/* Text */
.tip-title {
  font-size: 32px; font-weight: 800;
  color: white; margin: 0 0 8px 0;
  animation: fadeUp 0.5s ease-out 0.3s both;
}

.tip-subtitle {
  font-size: 16px; color: rgba(255,255,255,0.7);
  margin: 0 0 24px 0;
  animation: fadeUp 0.5s ease-out 0.4s both;
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Tip Amount Card */
.tip-amount-card {
  background: linear-gradient(135deg, #FFD700, #FF9800);
  border-radius: 20px;
  padding: 20px 40px;
  margin-bottom: 20px;
  box-shadow: 0 8px 24px rgba(255,215,0,0.3);
  animation: fadeUp 0.5s ease-out 0.5s both;
}

.tip-amount-label {
  font-size: 13px; color: rgba(0,0,0,0.6);
  font-weight: 600; margin: 0 0 4px 0;
  text-transform: uppercase; letter-spacing: 1px;
}

.tip-amount {
  font-size: 44px; font-weight: 900;
  color: #1a1a1a; margin: 0;
  letter-spacing: -1px;
}

/* From Customer */
.from-customer {
  display: flex; align-items: center; gap: 12px;
  background: rgba(255,255,255,0.1);
  border-radius: 14px; padding: 12px 20px;
  margin-bottom: 16px; width: 100%;
  animation: fadeUp 0.5s ease-out 0.6s both;
}

.customer-avatar {
  width: 44px; height: 44px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}
.customer-avatar ion-icon { font-size: 24px; color: white; }

.customer-label { font-size: 12px; color: rgba(255,255,255,0.6); margin: 0 0 2px 0; }
.customer-name { font-size: 16px; font-weight: 700; color: white; margin: 0; }

/* Tip Message */
.tip-message {
  display: flex; align-items: flex-start; gap: 10px;
  background: rgba(255,255,255,0.1);
  border-radius: 14px; padding: 14px 16px;
  margin-bottom: 16px; width: 100%;
  animation: fadeUp 0.5s ease-out 0.65s both;
}

.tip-message ion-icon { font-size: 20px; color: rgba(255,255,255,0.6); flex-shrink: 0; margin-top: 2px; }
.tip-message p { font-size: 14px; color: rgba(255,255,255,0.85); margin: 0; font-style: italic; line-height: 1.5; text-align: left; }

/* Order Info Card */
.order-info-card {
  background: rgba(255,255,255,0.1);
  border-radius: 16px; padding: 16px;
  margin-bottom: 16px; width: 100%;
  animation: fadeUp 0.5s ease-out 0.7s both;
}

.order-info-row {
  display: flex; justify-content: space-between;
  align-items: center; padding: 8px 0;
}

.info-label { font-size: 14px; color: rgba(255,255,255,0.7); }
.info-value { font-size: 14px; font-weight: 700; color: white; }
.info-value.tip-green { color: #69F0AE; }

.order-info-divider {
  height: 1px; background: rgba(255,255,255,0.2); margin: 4px 0;
}

.order-info-row.total .info-label { font-size: 15px; font-weight: 700; color: white; }
.order-info-row.total .info-value.total { font-size: 18px; color: #FFD700; }

/* Today's Tips */
.today-tips-card {
  display: flex; align-items: center; gap: 14px;
  background: rgba(255,215,0,0.15);
  border: 2px solid rgba(255,215,0,0.3);
  border-radius: 16px; padding: 14px 20px;
  margin-bottom: 24px; width: 100%;
  animation: fadeUp 0.5s ease-out 0.75s both;
}

.today-tips-card ion-icon { font-size: 32px; color: #FFD700; flex-shrink: 0; }
.today-label { font-size: 13px; color: rgba(255,255,255,0.7); margin: 0 0 4px 0; }
.today-amount { font-size: 22px; font-weight: 800; color: #FFD700; margin: 0; }

/* Action Buttons */
.action-buttons {
  display: flex; flex-direction: column;
  gap: 12px; width: 100%;
  animation: fadeUp 0.5s ease-out 0.8s both;
}

.continue-btn {
  --background: #4CAF50;
  --border-radius: 16px;
  --box-shadow: 0 6px 20px rgba(76,175,80,0.4);
  height: 56px; font-size: 16px;
  font-weight: 700; text-transform: none;
}

.view-earnings-btn {
  --border-radius: 16px;
  --border-color: rgba(255,255,255,0.3);
  --color: white;
  height: 52px; font-size: 15px;
  font-weight: 600; text-transform: none;
}
</style>