<template>
  <ion-page>
    <ion-content :fullscreen="true" class="activity-content">

      <!-- Header -->
      <div class="activity-header">
        <h1 class="header-title">Activity</h1>
        <div class="history-btn" @click="goToHistory">
          <ion-icon :icon="timeOutline"></ion-icon>
          <span>History</span>
        </div>
      </div>

      <!-- Active Order (if any) -->
      <div class="active-order-section" v-if="activeOrder">
        <div class="active-order-card" @click="trackOrder">
          <div class="active-badge">● LIVE</div>
          <div class="active-order-info">
            <img :src="activeOrder.storeImage" class="active-store-icon">
            <div class="active-details">
              <h3>{{ activeOrder.storeName }}</h3>
              <p class="active-status">{{ activeOrder.status }}</p>
              <p class="active-eta">{{ activeOrder.eta }}</p>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="active-arrow"></ion-icon>
          </div>
          <div class="active-progress">
            <div class="progress-step" :class="{ done: true }">🏪</div>
            <div class="progress-line done"></div>
            <div class="progress-step" :class="{ active: true }">🏍️</div>
            <div class="progress-line"></div>
            <div class="progress-step">🏠</div>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="recent-section">
        <h2 class="section-title">Recent</h2>

        <div v-if="recentOrders.length === 0" class="empty-state">
          <div class="empty-icon">📋</div>
          <h3>No orders yet</h3>
          <p>Your recent orders will appear here</p>
          <ion-button color="success" @click="goToHome" class="shop-btn">
            Start Shopping
          </ion-button>
        </div>

        <div v-else class="orders-list">
          <div
            v-for="order in recentOrders"
            :key="order.id"
            class="order-card"
          >
            <div class="order-main" @click="viewOrder(order)">
              <img :src="order.storeImage" class="store-icon-img">
              <div class="order-info">
                <h3 class="store-name">{{ order.storeName }}</h3>
                <p class="order-date">{{ order.date }}</p>
                <div class="order-status-row">
                  <span class="order-status" :class="order.status">
                    {{ getStatusLabel(order.status) }}
                  </span>
                </div>
              </div>
              <div class="order-right">
                <p class="order-amount">₱{{ order.total.toFixed(2) }}</p>
                <ion-icon :icon="chevronForwardOutline" class="order-arrow"></ion-icon>
              </div>
            </div>

            <!-- Reorder Button -->
            <div class="reorder-row" v-if="order.status === 'completed'">
              <ion-button
                fill="clear"
                color="success"
                size="small"
                class="reorder-btn"
                @click="reorder(order)"
              >
                Reorder →
              </ion-button>
            </div>

            <!-- Cancel Button -->
            <div class="cancel-row" v-if="order.status === 'pending'">
              <ion-button
                fill="outline"
                color="danger"
                size="small"
                class="cancel-btn"
                @click="cancelOrder(order)"
              >
                Cancel Order
              </ion-button>
            </div>

            <!-- Rebook Button -->
            <div class="reorder-row" v-if="order.status === 'cancelled'">
              <ion-button
                fill="clear"
                color="medium"
                size="small"
                class="reorder-btn"
                @click="reorder(order)"
              >
                Rebook →
              </ion-button>
            </div>

          </div>
        </div>

        <!-- View History Link -->
        <div class="view-history-link" v-if="recentOrders.length > 0" @click="goToHistory">
          <span>View History →</span>
        </div>
      </div>

      <div style="height: 80px;"></div>
    </ion-content>

    <!-- Bottom Nav -->
    <ion-footer>
      <ion-toolbar class="bottom-nav-toolbar">
        <div class="bottom-nav">
          <div class="nav-item" @click="goToHome">
            <ion-icon :icon="homeOutline"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item" @click="goToBrowse">
            <ion-icon :icon="searchOutline"></ion-icon>
            <span>Browse</span>
          </div>
          <div class="nav-item" @click="goToCart">
            <ion-icon :icon="cartOutline"></ion-icon>
            <span>Cart</span>
          </div>
          <div class="nav-item active">
            <ion-icon :icon="receiptOutline"></ion-icon>
            <span>Activity</span>
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonContent, IonFooter, IonToolbar,
  IonButton, IonIcon, toastController, alertController
} from '@ionic/vue';
import {
  timeOutline, chevronForwardOutline,
  homeOutline, searchOutline, cartOutline,
  receiptOutline, personOutline
} from 'ionicons/icons';

const router = useRouter();

const activeOrder = ref({
  id: 'ORD-001',
  storeName: "Juan's Fresh Market",
  storeEmoji: '',
  storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e',
  status: 'Picking up from Palengke',
  eta: 'Arriving by 1:05 PM'
});

const recentOrders = ref([
  {
    id: 'ORD-006', storeName: "Ana's Palengke - Cogon",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e', type: 'fruits',
    date: 'Today, 11:59 AM',
    total: 291.00, status: 'pending',
    items: ['Apple', 'Banana']
  },
  {
    id: 'ORD-005', storeName: "Juan's Fresh Market",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e', type: 'market',
    date: 'Today, 10:30 AM',
    total: 207.00, status: 'delivering',
    items: ['Bawang/Garlic', 'Kamatis/Tomato']
  },
  {
    id: 'ORD-004', storeName: "Mario's Meat Shop",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791', type: 'meat',
    date: 'Today, 09:30 AM',
    total: 450.00, status: 'completed',
    items: ['Pork Belly', 'Chicken']
  },
  {
    id: 'ORD-003', storeName: "Rosa's Seafoods",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3', type: 'seafood',
    date: 'Yesterday, 06:48 PM',
    total: 320.00, status: 'cancelled',
    items: ['Tilapia', 'Shrimp']
  },
  {
    id: 'ORD-002', storeName: "Lily's Vegetables",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1540420773420-3366772f4999', type: 'vegetables',
    date: 'Mar 18, 2026, 05:19 PM',
    total: 180.00, status: 'completed',
    items: ['Kangkong', 'Spinach']
  },
  {
    id: 'ORD-001', storeName: "Tito's Variety Store",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9', type: 'variety',
    date: 'Mar 15, 2026, 02:30 PM',
    total: 250.00, status: 'completed',
    items: ['Sugar', 'Salt']
  },
]);

const getStatusLabel = (status) => {
  if (status === 'completed') return 'Delivered';
  if (status === 'cancelled') return 'Cancelled';
  if (status === 'delivering') return 'Delivering';
  if (status === 'pending') return 'Pending';
  return status;
};

const getStatusIcon = (status) => {
  if (status === 'completed') return 'checkmark-circle';
  if (status === 'cancelled') return 'close-circle';
  if (status === 'delivering') return 'bicycle';
  return 'time';
};

const getStatusColor = (status) => {
  if (status === 'completed') return 'success';
  if (status === 'cancelled') return 'danger';
  if (status === 'delivering') return 'warning';
  return 'medium';
};

const trackOrder = () => router.push('/order-tracking/1234');

const viewOrder = (order) => {
  if (order.status === 'completed') {
    router.push(`/order-rating/${order.id}`);
  } else if (order.status === 'delivering' || order.status === 'pending') {
    router.push(`/order-tracking/${order.id}`);
  }
};

const reorder = async (order) => {
  const toast = await toastController.create({
    message: `Reordering from ${order.storeName}...`,
    duration: 2000, color: 'success', position: 'top'
  });
  await toast.present();
  router.push(`/seller/1`);
};

const cancelOrder = async (order) => {
  const alert = await alertController.create({
    header: 'Cancel Order?',
    message: `Are you sure you want to cancel your order from ${order.storeName}? Your refund will be processed within 3-5 business days.`,
    buttons: [
      { text: 'No, Keep Order', role: 'cancel' },
      { 
        text: 'Yes, Cancel', 
        handler: async () => {
          // Find and update the order status
          const idx = recentOrders.value.findIndex(o => o.id === order.id);
          if (idx !== -1) {
            recentOrders.value[idx].status = 'cancelled';
          }
          
          const toast = await toastController.create({
            message: 'Order cancelled. Refund will be processed in 1-2 days.',
            duration: 3000, color: 'success', position: 'top'
          });
          await toast.present();
        }
      }
    ]
  });
  await alert.present();
};

const goToHome = () => router.push('/home');
const goToBrowse = () => router.push('/browse');
const goToCart = () => router.push('/cart');
const goToHistory = () => router.push('/order-history');
const goToProfile = () => router.push('/profile');
</script>

<style scoped>
.activity-content { --background: #f8f9fa; }

/* Header */
.activity-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 52px 20px 16px; background: white;
  border-bottom: 1px solid #f0f0f0;
}
.header-title { font-size: 28px; font-weight: 800; color: #1a1a1a; margin: 0; }
.history-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 16px; background: #f5f5f5;
  border-radius: 20px; cursor: pointer;
  font-size: 14px; font-weight: 600; color: #555;
}
.history-btn ion-icon { font-size: 18px; }

/* Active Order */
.active-order-section { padding: 16px; }
.active-order-card {
  background: white; border-radius: 18px;
  padding: 16px; box-shadow: 0 2px 12px rgba(76,175,80,0.2);
  border: 2px solid #4CAF50; cursor: pointer;
}
.active-badge {
  display: inline-block; background: #f44336; color: white;
  padding: 3px 10px; border-radius: 10px;
  font-size: 11px; font-weight: 700; margin-bottom: 12px;
  animation: blink 1.5s infinite;
}
@keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0.6; } }

.active-order-info { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
.active-store-icon {
  width: 48px; height: 48px; background: #E8F5E9;
  border-radius: 14px; display: flex; align-items: center;
  justify-content: center; font-size: 26px; flex-shrink: 0;
}
.active-store-icon, .store-icon-img {
  width: 48px; height: 48px; border-radius: 14px; object-fit: cover;
}
.active-details { flex: 1; }
.active-details h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.active-status { font-size: 13px; color: #4CAF50; font-weight: 600; margin: 0 0 2px 0; }
.active-eta { font-size: 12px; color: #666; margin: 0; }
.active-arrow { font-size: 20px; color: #ccc; }

.active-progress {
  display: flex; align-items: center;
  background: #f8f9fa; border-radius: 12px; padding: 10px 16px;
}
.progress-step {
  font-size: 20px; opacity: 0.4;
}
.progress-step.done, .progress-step.active { opacity: 1; }
.progress-step.active { animation: bounce 1s infinite; }
@keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
.progress-line {
  flex: 1; height: 3px; background: #e0e0e0; border-radius: 2px; margin: 0 4px;
}
.progress-line.done { background: #4CAF50; }

/* Recent Section */
.recent-section { padding: 0 16px; }
.section-title { font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 16px 0 12px 0; }

/* Empty State */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 64px; margin-bottom: 16px; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0 0 24px 0; }
.shop-btn { --border-radius: 14px; height: 48px; font-weight: 600; text-transform: none; }

/* Orders List */
.orders-list { display: flex; flex-direction: column; gap: 12px; }

.order-card {
  background: white; border-radius: 16px;
  overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.order-main {
  display: flex; align-items: center; gap: 14px;
  padding: 16px; cursor: pointer;
}

.store-icon-wrap {
  width: 52px; height: 52px; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 28px; flex-shrink: 0;
}
.store-icon-wrap.market { background: #E8F5E9; }
.store-icon-wrap.fruits { background: #FCE4EC; }
.store-icon-wrap.seafood { background: #E3F2FD; }
.store-icon-wrap.pork { background: #FFF3E0; }
.store-icon-wrap.vegetables { background: #E8F5E9; }
.store-icon-wrap.variety { background: #F3E5F5; }
.store-icon-img { width: 52px; height: 52px; border-radius: 14px; object-fit: cover; flex-shrink: 0; }

.order-info { flex: 1; }
.store-name { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.order-date { font-size: 12px; color: #999; margin: 0 0 6px 0; }
.order-status-row { display: flex; }
.order-status { 
  font-size: 12px; font-weight: 600; 
  padding: 4px 10px; border-radius: 20px;
  display: inline-flex; align-items: center; gap: 4px;
}
.order-status.completed { 
  background: #E8F5E9; color: #2E7D32; 
}
.order-status.cancelled { 
  background: #FFEBEE; color: #C62828; 
}
.order-status.delivering { 
  background: #FFF3E0; color: #E65100; 
}
.order-status.pending { 
  background: #E3F2FD; color: #1565C0; 
}

.order-right { text-align: right; }
.order-amount { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 6px 0; }
.order-arrow { font-size: 18px; color: #ccc; }

.reorder-row {
  padding: 0 16px 12px;
  border-top: 1px solid #f5f5f5;
  padding-top: 8px;
}
.reorder-btn { --padding-start: 0; font-size: 14px; font-weight: 700; text-transform: none; height: 36px; }

.cancel-row {
  padding: 8px 16px 12px;
  border-top: 1px solid #f5f5f5;
}
.cancel-btn {
  --border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-transform: none;
  height: 36px;
  --border-color: #f44336;
  --color: #f44336;
}

.view-history-link {
  text-align: center; padding: 20px;
  color: #4CAF50; font-size: 15px; font-weight: 700; cursor: pointer;
}

/* Bottom Nav */
.bottom-nav-toolbar { --background: white; box-shadow: 0 -2px 12px rgba(0,0,0,0.08); }
.bottom-nav { display: grid; grid-template-columns: repeat(5, 1fr); height: 60px; }
.nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; cursor: pointer; color: #999; }
.nav-item ion-icon { font-size: 22px; }
.nav-item span { font-size: 10px; font-weight: 600; }
.nav-item.active { color: #4CAF50; }
.nav-item:active { transform: scale(0.95); }
</style>