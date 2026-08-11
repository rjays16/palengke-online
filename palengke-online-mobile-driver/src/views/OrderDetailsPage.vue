<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/history"></ion-back-button>
        </ion-buttons>
        <ion-title>Order Details</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="details-content">
      <div class="details-container">
        <!-- Order Header -->
        <div class="order-header">
          <div class="order-id">Order #{{ String(job?.id ?? '').toUpperCase() }}</div>
          <div class="order-status completed">Completed</div>
        </div>

        <!-- Customer & Seller Info -->
        <div class="info-card">
          <h3>Delivery Information</h3>
          <div class="info-row">
            <ion-icon :icon="storefrontOutline"></ion-icon>
            <div>
              <span class="label">From</span>
              <span class="value">{{ job?.sellerName }}</span>
            </div>
          </div>
          <div class="location-line"></div>
          <div class="info-row">
            <ion-icon :icon="personOutline"></ion-icon>
            <div>
              <span class="label">To</span>
              <span class="value">{{ job?.deliveryAddress }}</span>
            </div>
          </div>
          <div class="location-line"></div>
          <div class="info-row">
            <ion-icon :icon="timeOutline"></ion-icon>
            <div>
              <span class="label">Completed at</span>
              <span class="value">{{ job?.time }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Details -->
        <div class="payment-card">
          <h3>Payment</h3>
          <div class="payment-row">
            <span class="payment-label">Earnings</span>
            <span class="payment-amount">₱{{ job?.amount?.toFixed(2) }}</span>
          </div>
          <div class="payment-row">
            <span class="payment-label">Payment Method</span>
            <span class="payment-method" :class="job?.paymentMethod">
              {{ job?.paymentMethod === 'cash' ? 'Cash' : 'GCash' }}
            </span>
          </div>
          <div class="payment-row tipped" v-if="job?.tipped">
            <span class="payment-label"> <font-awesome-icon :icon="['fas', 'money-bill']" /> Customer Tip </span>
            <span class="payment-tip">+ ₱{{ job?.tipAmount || '50' }}</span>
          </div>
          <div class="payment-row bonus" v-if="job?.bonus > 0">
            <span class="payment-label"> <font-awesome-icon :icon="['fas', 'bolt']" /> Bonus </span>
            <span class="payment-bonus">+ ₱{{ job?.bonus?.toFixed(2) }}</span>
          </div>
          <div class="payment-divider"></div>
          <div class="payment-row total">
            <span class="payment-label">Total</span>
            <span class="payment-total">₱{{ totalAmount.toFixed(2) }}</span>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonBackButton, IonIcon } from '@ionic/vue';
import { storefrontOutline, personOutline, timeOutline } from 'ionicons/icons';

const route = useRoute();

const job = computed(() => {
  const raw = route.params.job;
  if (!raw) return {};

  let parsed;
  try {
    parsed = JSON.parse(decodeURIComponent(raw));
  } catch {
    parsed = {};
  }

  if (!parsed || typeof parsed !== 'object') {
    return { id: raw };
  }

  if (parsed.address && !parsed.deliveryAddress) {
    parsed.deliveryAddress = parsed.address;
  }

  return parsed;
});

const totalAmount = computed(() => {
  if (!job.value) return 0;
  let total = job.value.amount || 0;
  if (job.value.tipped) total += job.value.tipAmount || 50;
  if (job.value.bonus > 0) total += job.value.bonus;
  return total;
});
</script>

<style scoped>
.details-content {
  --background: #f8f9fa;
}

.details-container {
  padding: 16px;
}

/* Order Header */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.order-id {
  font-size: 18px;
  font-weight: 800;
  color: #1a1a1a;
}

.order-status {
  padding: 6px 14px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
}
.order-status.completed {
  background: #e8f5e9;
  color: #4caf50;
}

/* Info Card */
.info-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.info-card h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
  padding-bottom: 12px;
  border-bottom: 1px solid #f0f0f0;
}

.info-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 10px 0;
}

.info-row ion-icon {
  font-size: 22px;
  color: #4caf50;
  flex-shrink: 0;
  margin-top: 2px;
}

.info-row .label {
  display: block;
  font-size: 11px;
  color: #999;
  font-weight: 600;
  text-transform: uppercase;
}

.info-row .value {
  display: block;
  font-size: 14px;
  color: #1a1a1a;
  font-weight: 600;
  margin-top: 2px;
}

.location-line {
  width: 2px;
  height: 16px;
  background: #e0e0e0;
  margin-left: 10px;
}

/* Payment Card */
.payment-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.payment-card h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
  padding-bottom: 12px;
  border-bottom: 1px solid #f0f0f0;
}

.payment-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
}

.payment-label {
  font-size: 14px;
  color: #666;
  display: flex;
  align-items: center;
  gap: 6px;
}

.payment-amount {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
}

.payment-method {
  padding: 4px 12px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 700;
}
.payment-method.cash {
  background: #e8f5e9;
  color: #4caf50;
}
.payment-method.gcash {
  background: #e3f2fd;
  color: #2196f3;
}

.payment-tip {
  font-size: 14px;
  font-weight: 700;
  color: #ff9800;
}
.payment-bonus {
  font-size: 14px;
  font-weight: 700;
  color: #9c27b0;
}

.payment-divider {
  height: 1px;
  background: #f0f0f0;
  margin: 8px 0;
}

.payment-row.total {
  padding-top: 12px;
}

.payment-label.total {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
}
.payment-total {
  font-size: 20px;
  font-weight: 800;
  color: #4caf50;
}
</style>
