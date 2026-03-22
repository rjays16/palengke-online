<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Available Orders</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="showFilterOptions">
            <ion-icon :icon="filterOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>

      <!-- Status Tabs -->
      <ion-toolbar>
        <ion-segment v-model="selectedTab" @ionChange="handleTabChange">
          <ion-segment-button value="available">
            <ion-label>Available ({{ availableCount }})</ion-label>
          </ion-segment-button>
          <ion-segment-button value="active">
            <ion-label>Active ({{ activeCount }})</ion-label>
          </ion-segment-button>
          <ion-segment-button value="completed">
            <ion-label>Completed ({{ completedCount }})</ion-label>
          </ion-segment-button>
        </ion-segment>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="orders-content">

      <!-- Offline Warning -->
      <div class="offline-warning" v-if="!isOnline && selectedTab === 'available'">
        <ion-icon :icon="powerOutline"></ion-icon>
        <div>
          <strong>You're Offline</strong>
          <p>Go online to see available orders</p>
        </div>
        <ion-button size="small" color="success" @click="goOnline">Go Online</ion-button>
      </div>

      <!-- Empty State -->
      <div v-if="filteredOrders.length === 0" class="empty-state">
        <div class="empty-icon-wrap">
          <ion-icon :icon="cubeOutline"></ion-icon>
        </div>
        <h3>{{ emptyTitle }}</h3>
        <p>{{ emptyMessage }}</p>
      </div>

      <!-- Orders List -->
      <div v-else class="orders-list">
        <div
          v-for="order in filteredOrders"
          :key="order.id"
          class="order-card"
          :class="order.status"
        >
          <!-- Order Header -->
          <div class="order-header">
            <div class="order-info">
              <h3 class="order-id">Order #{{ order.id }}</h3>
              <p class="order-time">{{ order.time }}</p>
            </div>
            <div class="order-badges">
              <span class="payment-badge" :class="order.paymentMethod">
                {{ order.paymentMethod === 'cash' ? '💵 Cash' : '📱 GCash' }}
              </span>
              <span class="status-badge" :class="order.status">
                {{ getStatusLabel(order.status) }}
              </span>
            </div>
          </div>

          <!-- Seller Info -->
          <div class="location-section">
            <div class="location-item pickup">
              <div class="location-dot green"></div>
              <div class="location-content">
                <span class="location-label">Pickup from Seller</span>
                <span class="location-name">{{ order.sellerName }}</span>
                <span class="location-address">{{ order.pickupAddress }}</span>
              </div>
            </div>
            <div class="location-line"></div>
            <div class="location-item dropoff">
              <div class="location-dot red"></div>
              <div class="location-content">
                <span class="location-label">Deliver to Buyer</span>
                <span class="location-name">{{ order.customerName }}</span>
                <span class="location-address">{{ order.deliveryAddress }}</span>
              </div>
            </div>
          </div>

          <!-- Order Details -->
          <div class="order-details-row">
            <div class="detail-item">
              <ion-icon :icon="navigateOutline"></ion-icon>
              <span>{{ order.distance }} km</span>
            </div>
            <div class="detail-item">
              <ion-icon :icon="timeOutline"></ion-icon>
              <span>~{{ order.estimatedTime }} min</span>
            </div>
            <div class="detail-item">
              <ion-icon :icon="cubeOutline"></ion-icon>
              <span>{{ order.itemCount }} items</span>
            </div>
          </div>

          <!-- Earnings Preview -->
          <div class="earnings-preview">
            <div class="earn-info">
              <span class="earn-label">Your Earnings</span>
              <span class="earn-amount">₱{{ order.driverEarnings.toLocaleString() }}</span>
            </div>
            <div class="order-value">
              <span class="earn-label">Order Value</span>
              <span class="order-amount">₱{{ order.totalAmount.toLocaleString() }}</span>
            </div>
          </div>

          <!-- Action Buttons (Available orders only) -->
          <div class="action-buttons" v-if="order.status === 'available'">
            <ion-button
              expand="block"
              color="danger"
              fill="outline"
              class="decline-btn"
              @click="declineOrder(order)"
            >
              <ion-icon slot="start" :icon="closeCircleOutline"></ion-icon>
              Decline
            </ion-button>
            <ion-button
              expand="block"
              color="success"
              class="accept-btn"
              @click="acceptOrder(order)"
            >
              <ion-icon slot="start" :icon="checkmarkCircleOutline"></ion-icon>
              Accept
            </ion-button>
          </div>

          <!-- Active Order Actions -->
          <div class="active-actions" v-if="order.status === 'active'">
            <ion-button expand="block" color="success" @click="viewActiveDelivery(order)">
              <ion-icon slot="start" :icon="navigateOutline"></ion-icon>
              View on Map
            </ion-button>
          </div>

          <!-- Completed Order Info -->
          <div class="completed-info" v-if="order.status === 'completed'">
            <ion-icon :icon="checkmarkCircleOutline" class="done-icon"></ion-icon>
            <span>Delivered {{ order.completedTime }}</span>
            <span class="tip-tag" v-if="order.tipped">💰 Tipped</span>
          </div>

        </div>
      </div>

    </ion-content>

    <!-- Bottom Navigation -->
    <ion-footer>
      <ion-toolbar>
        <div class="bottom-nav">
          <div class="nav-item" @click="goToHome">
            <ion-icon :icon="homeOutline"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item active">
            <ion-icon :icon="cubeOutline"></ion-icon>
            <span>Orders</span>
          </div>
          <div class="nav-item" @click="goToEarnings">
            <ion-icon :icon="cashOutline"></ion-icon>
            <span>Earnings</span>
          </div>
          <div class="nav-item" @click="goToProfile">
            <ion-icon :icon="personOutline"></ion-icon>
            <span>Profile</span>
          </div>
        </div>
      </ion-toolbar>
    </ion-footer>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonFooter, IonButton, IonButtons, IonIcon,
  IonSegment, IonSegmentButton, IonLabel,
  alertController, actionSheetController, toastController
} from '@ionic/vue';
import {
  filterOutline, cubeOutline, navigateOutline, timeOutline,
  checkmarkCircleOutline, closeCircleOutline, powerOutline,
  homeOutline, cashOutline, personOutline
} from 'ionicons/icons';

const router = useRouter();

const selectedTab = ref('available');
const isOnline = ref(true);

// Mock Orders Data
const orders = ref([
  {
    id: '1234',
    status: 'available',
    sellerName: "Juan's Fresh Market",
    pickupAddress: 'Stall #15, CDO City Palengke',
    customerName: 'Maria Santos',
    deliveryAddress: 'Brgy. Carmen, CDO City',
    distance: 2.3,
    estimatedTime: 15,
    itemCount: 5,
    totalAmount: 850,
    driverEarnings: 85,
    paymentMethod: 'cash',
    time: '2 mins ago',
    tipped: false,
    completedTime: null
  },
  {
    id: '1235',
    status: 'available',
    sellerName: "Ana's Palengke",
    pickupAddress: 'Stall #8, Divisoria Market',
    customerName: 'Pedro Cruz',
    deliveryAddress: 'Brgy. Lapasan, CDO City',
    distance: 3.5,
    estimatedTime: 20,
    itemCount: 3,
    totalAmount: 450,
    driverEarnings: 65,
    paymentMethod: 'gcash',
    time: '5 mins ago',
    tipped: false,
    completedTime: null
  },
  {
    id: '1233',
    status: 'active',
    sellerName: "Rosa's Veggies",
    pickupAddress: 'Stall #22, Cogon Market',
    customerName: 'Ana Lopez',
    deliveryAddress: 'Brgy. Macasandig, CDO City',
    distance: 1.8,
    estimatedTime: 10,
    itemCount: 7,
    totalAmount: 620,
    driverEarnings: 75,
    paymentMethod: 'cash',
    time: '15 mins ago',
    tipped: false,
    completedTime: null
  },
  {
    id: '1232',
    status: 'completed',
    sellerName: "Mario's Fresh Fish",
    pickupAddress: 'Stall #5, CDO City Palengke',
    customerName: 'Jose Reyes',
    deliveryAddress: 'Brgy. Bulua, CDO City',
    distance: 4.2,
    estimatedTime: 25,
    itemCount: 2,
    totalAmount: 380,
    driverEarnings: 60,
    paymentMethod: 'gcash',
    time: '1 hour ago',
    tipped: true,
    completedTime: '1 hour ago'
  }
]);

// Computed
const filteredOrders = computed(() => {
  return orders.value.filter(o => o.status === selectedTab.value);
});

const availableCount = computed(() => orders.value.filter(o => o.status === 'available').length);
const activeCount = computed(() => orders.value.filter(o => o.status === 'active').length);
const completedCount = computed(() => orders.value.filter(o => o.status === 'completed').length);

const emptyTitle = computed(() => {
  if (selectedTab.value === 'available') return isOnline.value ? 'No Available Orders' : "You're Offline";
  if (selectedTab.value === 'active') return 'No Active Orders';
  return 'No Completed Orders';
});

const emptyMessage = computed(() => {
  if (selectedTab.value === 'available') return isOnline.value ? 'New orders will appear here' : 'Go online to receive orders';
  if (selectedTab.value === 'active') return 'Accept an order to start delivering';
  return 'Your completed deliveries will appear here';
});

// Methods
const handleTabChange = (e) => { selectedTab.value = e.detail.value; };

const getStatusLabel = (status) => {
  if (status === 'available') return 'New Order';
  if (status === 'active') return 'Active';
  if (status === 'completed') return 'Completed';
  return status;
};

const acceptOrder = async (order) => {
  const alert = await alertController.create({
    header: 'Accept Order?',
    message: `Pick up from ${order.sellerName} and deliver to ${order.customerName}.\n\nYour earnings: ₱${order.driverEarnings}`,
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Accept',
        handler: async () => {
          order.status = 'active';
          const toast = await toastController.create({
            message: '✅ Order accepted! Head to pickup location.',
            duration: 2500,
            color: 'success',
            position: 'top'
          });
          await toast.present();
          selectedTab.value = 'active';
          router.push('/active-delivery');
        }
      }
    ]
  });
  await alert.present();
};

const declineOrder = async (order) => {
  const alert = await alertController.create({
    header: 'Decline Order?',
    message: 'Are you sure you want to decline this order?',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Decline',
        role: 'destructive',
        handler: async () => {
          orders.value = orders.value.filter(o => o.id !== order.id);
          const toast = await toastController.create({
            message: 'Order declined',
            duration: 2000,
            color: 'dark',
            position: 'top'
          });
          await toast.present();
        }
      }
    ]
  });
  await alert.present();
};

const viewActiveDelivery = (order) => { router.push('/active-delivery'); };

const goOnline = async () => {
  isOnline.value = true;
  const toast = await toastController.create({
    message: '🟢 You are now online!',
    duration: 2000,
    color: 'success',
    position: 'top'
  });
  await toast.present();
};

const showFilterOptions = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Sort Orders',
    buttons: [
      { text: 'Nearest First', handler: () => {} },
      { text: 'Highest Earnings', handler: () => {} },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

// Navigation
const goToHome = () => router.push('/home');
const goToEarnings = () => router.push('/earnings');
const goToProfile = () => router.push('/profile');
</script>

<style scoped>
.orders-content { --background: #f8f9fa; }

/* Offline Warning */
.offline-warning {
  display: flex; align-items: center; gap: 12px;
  background: #FFF3E0; padding: 16px 20px;
  border-bottom: 2px solid #FF9800;
}
.offline-warning ion-icon { font-size: 28px; color: #FF9800; flex-shrink: 0; }
.offline-warning strong { display: block; font-size: 14px; color: #1a1a1a; margin-bottom: 2px; }
.offline-warning p { font-size: 12px; color: #666; margin: 0; }

/* Segment */
ion-segment { padding: 8px 16px; }
ion-segment-button {
  --color: #666; --color-checked: #4CAF50;
  --indicator-color: #4CAF50;
  font-size: 12px; font-weight: 600; min-height: 36px;
}

/* Orders List */
.orders-list { padding: 16px; }

.order-card {
  background: white; border-radius: 16px;
  padding: 16px; margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-left: 4px solid transparent;
}

.order-card.available { border-left-color: #4CAF50; }
.order-card.active { border-left-color: #2196F3; }
.order-card.completed { border-left-color: #9E9E9E; }

/* Order Header */
.order-header {
  display: flex; justify-content: space-between;
  align-items: flex-start; margin-bottom: 16px;
}

.order-id { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.order-time { font-size: 12px; color: #999; margin: 0; }

.order-badges { display: flex; flex-direction: column; gap: 4px; align-items: flex-end; }

.payment-badge, .status-badge {
  padding: 3px 10px; border-radius: 10px;
  font-size: 11px; font-weight: 700;
}

.payment-badge.cash { background: #E8F5E9; color: #4CAF50; }
.payment-badge.gcash { background: #E3F2FD; color: #2196F3; }

.status-badge.available { background: #E8F5E9; color: #4CAF50; }
.status-badge.active { background: #E3F2FD; color: #2196F3; }
.status-badge.completed { background: #f5f5f5; color: #666; }

/* Location Section */
.location-section { margin-bottom: 16px; }

.location-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 8px 0;
}

.location-dot {
  width: 12px; height: 12px; border-radius: 50%;
  flex-shrink: 0; margin-top: 4px;
}
.location-dot.green { background: #4CAF50; }
.location-dot.red { background: #f44336; }

.location-content { display: flex; flex-direction: column; gap: 2px; }
.location-label { font-size: 11px; color: #999; font-weight: 600; text-transform: uppercase; }
.location-name { font-size: 14px; font-weight: 700; color: #1a1a1a; }
.location-address { font-size: 13px; color: #666; }

.location-line {
  width: 2px; height: 20px;
  background: #e0e0e0; margin-left: 5px;
}

/* Order Details Row */
.order-details-row {
  display: flex; gap: 16px;
  padding: 12px 0;
  border-top: 1px solid #f0f0f0;
  border-bottom: 1px solid #f0f0f0;
  margin-bottom: 12px;
}

.detail-item {
  display: flex; align-items: center; gap: 6px;
  font-size: 13px; color: #555; font-weight: 600;
}
.detail-item ion-icon { font-size: 18px; color: #4CAF50; }

/* Earnings Preview */
.earnings-preview {
  display: flex; justify-content: space-between;
  background: #f8f9fa; border-radius: 12px;
  padding: 12px 16px; margin-bottom: 16px;
}

.earn-info, .order-value {
  display: flex; flex-direction: column; gap: 2px;
}

.earn-label { font-size: 11px; color: #999; font-weight: 600; }
.earn-amount { font-size: 20px; font-weight: 800; color: #4CAF50; }
.order-amount { font-size: 20px; font-weight: 800; color: #1a1a1a; }

/* Action Buttons */
.action-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.decline-btn, .accept-btn {
  --border-radius: 12px; height: 48px;
  font-size: 14px; font-weight: 700;
  text-transform: none;
}

/* Active Actions */
.active-actions ion-button {
  --border-radius: 12px; --background: #2196F3;
  height: 48px; font-weight: 700; text-transform: none;
}

/* Completed Info */
.completed-info {
  display: flex; align-items: center; gap: 8px;
  padding-top: 12px;
  border-top: 1px solid #f0f0f0;
  font-size: 13px; color: #4CAF50; font-weight: 600;
}

.done-icon { font-size: 20px; }

.tip-tag {
  background: #FFF3E0; color: #FF9800;
  padding: 3px 10px; border-radius: 10px;
  font-size: 11px; font-weight: 700;
}

/* Empty State */
.empty-state {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 60px 40px; text-align: center;
}

.empty-icon-wrap {
  width: 100px; height: 100px;
  background: #f0f0f0; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 24px;
}

.empty-icon-wrap ion-icon { font-size: 48px; color: #999; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0; }

/* Bottom Nav */
.bottom-nav {
  display: grid; grid-template-columns: repeat(4, 1fr); height: 60px;
}
.nav-item {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 4px; cursor: pointer; color: #999;
}
.nav-item ion-icon { font-size: 24px; }
.nav-item span { font-size: 11px; font-weight: 500; }
.nav-item.active { color: #4CAF50; }
.nav-item:active { transform: scale(0.95); }
</style>