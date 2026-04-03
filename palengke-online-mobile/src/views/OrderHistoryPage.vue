<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/activity"></ion-back-button>
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
            <div class="history-icon" :style="{ background: order.iconBg }">
              <span>{{ order.storeEmoji }}</span>
            </div>
            <div class="history-info">
              <h3>{{ order.storeName }}</h3>
              <p class="history-date">{{ order.date }}</p>
              <p class="history-items">{{ order.items }}</p>
            </div>
            <div class="history-right">
              <p class="history-amount">₱{{ order.total.toFixed(2) }}</p>
              <span class="history-status" :class="order.status">
                {{ order.status === 'completed' ? '✅' : '❌' }}
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
  IonButtons, IonBackButton, IonButton, IonLabel,
  IonSegment, IonSegmentButton, IonIcon,
  alertController, toastController
} from '@ionic/vue';

const router = useRouter();
const selectedFilter = ref('all');

const orders = ref([
  { id: 'ORD-001', storeName: "Juan's Fresh Market", storeEmoji: '🥦', iconBg: '#E8F5E9', date: '22 Mar 2026, 11:59 AM', items: 'Bawang/Garlic, Kamatis/Tomato', total: 207.00, status: 'completed' },
  { id: 'ORD-002', storeName: "Ana's Palengke", storeEmoji: '🍎', iconBg: '#FCE4EC', date: '20 Mar 2026, 06:48 PM', items: 'Apple Med. Size, Banana', total: 291.00, status: 'cancelled' },
  { id: 'ORD-003', storeName: "Rosa's Seafoods", storeEmoji: '🐟', iconBg: '#E3F2FD', date: '18 Mar 2026, 05:19 PM', items: 'Tuna Cubes, Pampano', total: 556.00, status: 'completed' },
  { id: 'ORD-004', storeName: "Mario's Meat Shop", storeEmoji: '🥩', iconBg: '#FFEBEE', date: '15 Mar 2026, 02:30 PM', items: 'Pork Belly, Pork Ribs', total: 597.00, status: 'completed' },
  { id: 'ORD-005', storeName: "Palengke Bundles", storeEmoji: '📦', iconBg: '#F3E5F5', date: '10 Mar 2026, 09:15 AM', items: 'Pinakbet Bundle', total: 257.00, status: 'completed' },
]);

const filteredOrders = computed(() => {
  if (selectedFilter.value === 'all') return orders.value;
  return orders.value.filter(o => o.status === selectedFilter.value);
});

const handleFilter = (e) => { selectedFilter.value = e.detail.value; };

const viewOrderDetail = (order) => {};

const reorder = async (order) => {
  const toast = await toastController.create({
    message: `Reordering from ${order.storeName}...`,
    duration: 2000, color: 'success', position: 'top'
  });
  await toast.present();
  router.push('/seller/1');
};

const viewReceipt = async (order) => {
  const alert = await alertController.create({
    header: `Receipt - ${order.id}`,
    message: `Store: ${order.storeName}\nItems: ${order.items}\nTotal: ₱${order.total.toFixed(2)}\nDate: ${order.date}`,
    buttons: ['Close']
  });
  await alert.present();
};
</script>

<style scoped>
.history-content { --background: #f8f9fa; }
ion-segment { padding: 8px 16px; }
ion-segment-button { --color: #666; --color-checked: #4CAF50; --indicator-color: #4CAF50; font-size: 13px; font-weight: 600; min-height: 36px; }

.empty-state { text-align: center; padding: 80px 40px; }
.empty-icon { font-size: 64px; margin-bottom: 16px; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0; }

.history-list { padding: 16px; display: flex; flex-direction: column; gap: 12px; }

.history-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

.history-main { display: flex; align-items: center; gap: 14px; padding: 16px; cursor: pointer; }

.history-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 28px; flex-shrink: 0; }

.history-info { flex: 1; }
.history-info h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.history-date { font-size: 12px; color: #999; margin: 0 0 3px 0; }
.history-items { font-size: 12px; color: #666; margin: 0; }

.history-right { text-align: right; }
.history-amount { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 6px 0; }
.history-status { font-size: 18px; }

.history-actions {
  display: flex; justify-content: space-between;
  padding: 8px 16px 12px;
  border-top: 1px solid #f5f5f5;
}
.reorder-btn { --padding-start: 0; font-size: 14px; font-weight: 700; text-transform: none; height: 36px; }
</style>