<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Order History</ion-title>
      </ion-toolbar>
      <ion-toolbar>
        <ion-segment v-model="selectedFilter" @ionChange="handleFilter">
          <ion-segment-button value="all"><ion-label>All</ion-label></ion-segment-button>
          <ion-segment-button value="completed"><ion-label>Completed</ion-label></ion-segment-button>
          <ion-segment-button value="cancelled"><ion-label>Cancelled</ion-label></ion-segment-button>
        </ion-segment>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="history-content">

      <div v-if="filteredOrders.length === 0" class="empty-state">
        <div class="empty-icon">📦</div>
        <h3>No orders found</h3>
        <p>No {{ selectedFilter !== 'all' ? selectedFilter : '' }} orders yet</p>
      </div>

      <div v-else class="history-list">
        <div v-for="order in filteredOrders" :key="order.id" class="history-card">
          <div class="history-main" @click="viewOrderDetail(order)">
            <img :src="order.storeImage" class="history-icon-img">
            <div class="history-info">
              <h3>{{ order.storeName }}</h3>
              <p class="history-date">{{ order.date }}</p>
              <p class="history-items">{{ order.items }}</p>
            </div>
            <div class="history-right">
              <p class="history-amount">₱{{ order.total.toFixed(2) }}</p>
              <span class="history-status" :class="order.status">
                {{ order.status === 'completed' ? 'Completed' : 'Cancelled' }}
              </span>
            </div>
          </div>
          <div class="history-actions">
            <ion-button fill="clear" color="success" size="small"
              class="reorder-btn" @click="reorder(order)">
              {{ order.status === 'completed' ? 'Reorder →' : 'Rebook →' }}
            </ion-button>
            <ion-button fill="clear" color="medium" size="small"
              @click="viewReceipt(order)">
              View Receipt
            </ion-button>
            <ion-button v-if="order.status === 'completed'"
              fill="clear" color="danger" size="small"
              @click="fileDispute(order)">
              <ion-icon :icon="warningOutline" slot="start"></ion-icon>
              {{ getDisputeByOrderId(order.id) ? 'View Dispute' : 'Report Issue' }}
            </ion-button>
          </div>
        </div>
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonButton, IonLabel, IonSegment, IonSegmentButton, IonIcon
} from '@ionic/vue';
import { arrowBackOutline, warningOutline } from 'ionicons/icons';
import { getDisputeByOrderId } from '../services/disputeService';

const router = useRouter();
const selectedFilter = ref('all');

const orders = ref([
  { id: 'ORD-001', storeName: "Juan's Fresh Market", storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e', date: '22 Mar 2026, 11:59 AM', items: 'Bawang/Garlic, Kamatis/Tomato', total: 207.00, status: 'completed' },
  { id: 'ORD-002', storeName: "Ana's Palengke", storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e', date: '20 Mar 2026, 06:48 PM', items: 'Apple Med. Size, Banana', total: 291.00, status: 'cancelled' },
  { id: 'ORD-003', storeName: "Rosa's Seafoods", storeImage: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3', date: '18 Mar 2026, 05:19 PM', items: 'Tuna Cubes, Pampano', total: 556.00, status: 'completed' },
  { id: 'ORD-004', storeName: "Mario's Meat Shop", storeImage: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791', date: '15 Mar 2026, 02:30 PM', items: 'Pork Belly, Pork Ribs', total: 597.00, status: 'completed' },
  { id: 'ORD-005', storeName: "Palengke Bundles", storeImage: 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2', date: '10 Mar 2026, 09:15 AM', items: 'Fresh Vegetable Bundle', total: 257.00, status: 'completed' },
]);

const filteredOrders = computed(() => {
  if (selectedFilter.value === 'all') return orders.value;
  return orders.value.filter(o => o.status === selectedFilter.value);
});

const handleFilter = () => {};

const viewOrderDetail = (order) => {
  router.push(`/order-tracking/${order.id}`);
};

const reorder = (order) => {
  router.push(`/seller/${order.id}`);
};

const viewReceipt = (order) => {
  router.push(`/order-receipt/${order.id}`);
};

const fileDispute = (order) => {
  localStorage.setItem('lastOrder_' + order.id, JSON.stringify(order))
  router.push(`/file-dispute/${order.id}`)
}

const goBack = () => router.back();
</script>

<style scoped>
.history-content { --background: #f8f9fa; }
ion-segment { padding: 8px 16px; background: white; gap: 8px; }
ion-segment-button { --border-radius: 20px; }

.empty-state { text-align: center; padding: 80px 20px; }
.empty-icon { font-size: 64px; margin-bottom: 16px; }
.empty-state h3 { font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px; }
.empty-state p { font-size: 14px; color: #666; margin: 0; }

.history-list { padding: 16px; display: flex; flex-direction: column; gap: 12px; }

.history-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

.history-main { display: flex; gap: 14px; padding: 16px; cursor: pointer; }

.history-icon-img { width: 60px; height: 60px; border-radius: 14px; object-fit: cover; flex-shrink: 0; }

.history-info { flex: 1; min-width: 0; }
.history-info h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px; }
.history-date { font-size: 12px; color: #999; margin: 0 0 4px; }
.history-items { font-size: 13px; color: #666; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.history-right { text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
.history-amount { font-size: 16px; font-weight: 800; color: #1a1a1a; margin: 0; }
.history-status { font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 12px; white-space: nowrap; }
.history-status.completed { background: #E8F5E9; color: #4CAF50; }
.history-status.cancelled { background: #FFEBEE; color: #f44336; }

.history-actions { display: flex; justify-content: space-between; padding: 8px 16px; border-top: 1px solid #f0f0f0; }
.reorder-btn { font-size: 13px; font-weight: 600; }
</style>