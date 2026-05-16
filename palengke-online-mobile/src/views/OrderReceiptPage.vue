<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Receipt</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="receipt-content">
      <div class="receipt-container">
        <!-- Order Info -->
        <div class="receipt-header">
          <img :src="order.storeImage" alt="Store" class="store-img">
          <h2>{{ order.storeName }}</h2>
          <p class="order-id">Order #{{ order.id }}</p>
          <p class="order-date">{{ order.date }}</p>
        </div>

        <!-- Items -->
        <div class="receipt-section">
          <h3>Items</h3>
          <div class="items-list">
            <div v-for="(item, index) in order.itemsList" :key="index" class="item-row">
              <span class="item-name">{{ item.name }}</span>
              <span class="item-qty">x{{ item.qty }}</span>
              <span class="item-price">₱{{ item.price.toFixed(2) }}</span>
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div class="receipt-section">
          <div class="summary-row">
            <span>Subtotal</span>
            <span>₱{{ order.subtotal.toFixed(2) }}</span>
          </div>
          <div class="summary-row">
            <span>Delivery Fee</span>
            <span>₱{{ order.deliveryFee.toFixed(2) }}</span>
          </div>
          <div class="summary-row">
            <span>Service Fee</span>
            <span>₱{{ order.serviceFee.toFixed(2) }}</span>
          </div>
          <div class="summary-row" v-if="order.discount > 0">
            <span>Discount</span>
            <span class="discount">-₱{{ order.discount.toFixed(2) }}</span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <span>₱{{ order.total.toFixed(2) }}</span>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="receipt-section">
          <h3>Payment</h3>
          <div class="payment-info">
            <span>Method</span>
            <span>{{ order.paymentMethod }}</span>
          </div>
        </div>

        <!-- Delivery Address -->
        <div class="receipt-section">
          <h3>Delivery Address</h3>
          <p class="address">{{ order.deliveryAddress }}</p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonButton, IonIcon
} from '@ionic/vue';
import { arrowBackOutline } from 'ionicons/icons';

const router = useRouter();
const route = useRoute();

import { order as mockOrder } from '@/mock/orderReceipt';

const order = ref({ ...mockOrder, id: route.params.id || 'ORD-001' });

const goBack = () => router.back();
</script>

<style scoped>
.receipt-content { --background: #f8f9fa; }

.receipt-container {
  padding: 20px;
  max-width: 500px;
  margin: 0 auto;
}

.receipt-header {
  background: white;
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  margin-bottom: 16px;
}

.store-img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 12px;
}

.receipt-header h2 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px;
}

.order-id {
  font-size: 14px;
  color: #666;
  margin: 0 0 4px;
}

.order-date {
  font-size: 13px;
  color: #999;
  margin: 0;
}

.receipt-section {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
}

.receipt-section h3 {
  font-size: 14px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.item-name {
  flex: 1;
  font-size: 14px;
  color: #333;
}

.item-qty {
  font-size: 13px;
  color: #666;
  margin: 0 12px;
}

.item-price {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  font-size: 14px;
  color: #666;
}

.summary-row.total {
  border-top: 1px solid #f0f0f0;
  margin-top: 8px;
  padding-top: 16px;
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
}

.discount {
  color: #4CAF50;
}

.payment-info {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
}

.payment-info span:first-child {
  color: #666;
}

.payment-info span:last-child {
  color: #1a1a1a;
  font-weight: 600;
}

.address {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.5;
}
</style>