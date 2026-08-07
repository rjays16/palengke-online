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
        <div class="custom-tabs">
          <div
            class="custom-tab"
            :class="{ active: selectedTab === 'available' }"
            @click="selectedTab = 'available'"
          >
            Available ({{ availableCount }})
          </div>
          <div
            class="custom-tab"
            :class="{ active: selectedTab === 'active' }"
            @click="selectedTab = 'active'"
          >
            Active ({{ activeCount }})
          </div>
          <div
            class="custom-tab"
            :class="{ active: selectedTab === 'completed' }"
            @click="selectedTab = 'completed'"
          >
            Completed ({{ completedCount }})
          </div>
        </div>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="orders-content">

      <!-- Inactive Warning -->
      <div class="offline-warning" v-if="!isActive && selectedTab === 'available'">
        <ion-icon :icon="powerOutline"></ion-icon>
        <div>
          <strong>You're Inactive</strong>
          <p>Go active to see available orders</p>
        </div>
        <ion-button size="small" color="success" @click="goActive">Go Active</ion-button>
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
                {{ order.paymentMethod === 'cash' ? 'Cash' : 'GCash' }}
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
            <span class="tip-tag" v-if="order.tipped"><font-awesome-icon :icon="['fas', 'money-bill']" /> Tipped</span>
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
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonFooter, IonButton, IonButtons, IonIcon,
  alertController, actionSheetController, toastController
} from '@ionic/vue';
import {
  filterOutline, cubeOutline, navigateOutline, timeOutline,
  checkmarkCircleOutline, closeCircleOutline, powerOutline,
  homeOutline, cashOutline, personOutline
} from 'ionicons/icons';

const router = useRouter();
import { API_BASE } from '@/services/api';

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('token');
}

const selectedTab = ref('available');
const isActive = ref(true);

const orders = ref([]);

onMounted(() => {
  fetchOrders();
  fetchAvailability();
});

async function fetchAvailability() {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/profile`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      isActive.value = data.availabilityStatus === 'active';
    }
  } catch {}
}

async function fetchOrders() {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/orders`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      orders.value = [
        ...(data.available || []),
        ...(data.active || []),
        ...(data.completed || []),
      ];
    }
  } catch {}
}

// Computed
const filteredOrders = computed(() => {
  return orders.value.filter(o => o.status === selectedTab.value);
});

const availableCount = computed(() => orders.value.filter(o => o.status === 'available').length);
const activeCount = computed(() => orders.value.filter(o => o.status === 'active').length);
const completedCount = computed(() => orders.value.filter(o => o.status === 'completed').length);

const emptyTitle = computed(() => {
  if (selectedTab.value === 'available') return isActive.value ? 'No Available Orders' : "You're Inactive";
  if (selectedTab.value === 'active') return 'No Active Orders';
  return 'No Completed Orders';
});

const emptyMessage = computed(() => {
  if (selectedTab.value === 'available') return isActive.value ? 'New orders will appear here' : 'Go active to receive orders';
  if (selectedTab.value === 'active') return 'Accept an order to start delivering';
  return 'Your completed deliveries will appear here';
});

// Methods
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
          const token = getToken();
          if (!token) return;
          try {
            const res = await fetch(`${API_BASE}/api/driver/orders/${order.id}/accept`, {
              method: 'POST',
              headers: {
                Authorization: `Bearer ${token}`,
                'Content-Type': 'application/json',
              },
            });
            if (!res.ok) {
              const toast = await toastController.create({
                message: 'Failed to accept order. It may no longer be available.',
                duration: 3000,
                color: 'danger',
                position: 'top',
              });
              await toast.present();
              await fetchOrders();
              return;
            }
          } catch {
            const toast = await toastController.create({
              message: 'Network error. Please try again.',
              duration: 3000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            return;
          }
          await fetchOrders();
          await showSuccessModal('Order accepted!\nHead to pickup location.');
          selectedTab.value = 'active';
          router.push('/active-delivery');
        }
      }
    ]
  });
  await alert.present();
};

const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      textDiv.style.whiteSpace = 'pre-line';
      
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  await alert.onDidDismiss();
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
            color: 'medium',
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

const goActive = async () => {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/status`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ status: 'active' }),
    });
    if (!res.ok) {
      const toast = await toastController.create({
        message: 'Failed to go active. Please try again.',
        duration: 3000,
        color: 'danger',
        position: 'top',
      });
      await toast.present();
      return;
    }
  } catch {
    const toast = await toastController.create({
      message: 'Network error. Please try again.',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
    return;
  }
  isActive.value = true;
  await fetchOrders();
  const toast = await toastController.create({
    message: 'You are now active!',
    duration: 2000,
    color: 'medium',
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

/* Custom Tabs */
.custom-tabs {
  display: flex; gap: 8px;
  padding: 8px 16px;
  overflow-x: auto;
}
.custom-tabs::-webkit-scrollbar { display: none; }

.custom-tab {
  padding: 8px 20px;
  border-radius: 20px;
  font-size: 13px; font-weight: 600;
  color: #666;
  background: #f0f0f0;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
  user-select: none;
}
.custom-tab.active {
  background: #4CAF50;
  color: white;
}
.custom-tab:active { transform: scale(0.95); }

/* Inactive Warning */
.offline-warning {
  display: flex; align-items: center; gap: 12px;
  background: #FFF3E0; padding: 16px 20px;
  border-bottom: 2px solid #FF9800;
}
.offline-warning ion-icon { font-size: 28px; color: #FF9800; flex-shrink: 0; }
.offline-warning strong { display: block; font-size: 14px; color: #1a1a1a; margin-bottom: 2px; }
.offline-warning p { font-size: 12px; color: #666; margin: 0; }

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