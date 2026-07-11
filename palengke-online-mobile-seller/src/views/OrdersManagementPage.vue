<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Orders</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="showFilterOptions">
            <ion-icon :icon="filterOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>

      <!-- Status Tabs -->
      <ion-toolbar>
        <ion-segment v-model="selectedTab" @ionChange="handleTabChange">
          <ion-segment-button value="all">
            <ion-label>All ({{ orderStats.all }})</ion-label>
          </ion-segment-button>
          <ion-segment-button value="pending">
            <ion-label>Pending ({{ orderStats.pending }})</ion-label>
          </ion-segment-button>
          <ion-segment-button value="processing">
            <ion-label>Processing ({{ orderStats.processing }})</ion-label>
          </ion-segment-button>
          <ion-segment-button value="completed">
            <ion-label>Completed ({{ orderStats.completed }})</ion-label>
          </ion-segment-button>
        </ion-segment>
      </ion-toolbar>

      <!-- Search Bar -->
      <ion-toolbar>
        <ion-searchbar
          v-model="searchQuery"
          placeholder="Search by order ID or customer name"
          @ionInput="handleSearch"
        ></ion-searchbar>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <ion-icon :icon="receiptOutline" class="loading-icon"></ion-icon>
        <p>Loading orders...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredOrders.length === 0" class="empty-state">
        <div class="empty-icon">
          <ion-icon :icon="receiptOutline"></ion-icon>
        </div>
        <h3>No Orders Found</h3>
        <p>{{ emptyStateMessage }}</p>
      </div>

      <!-- Orders List -->
      <div v-else class="orders-list">
        <div v-for="order in filteredOrders" :key="order.id" class="order-card" @click="viewOrderDetails(order.id)">
          <!-- Order Header -->
          <div class="order-header">
            <div class="order-info">
              <h3 class="order-id">Order #{{ order.id }}</h3>
              <p class="order-date">{{ formatDate(order.date) }}</p>
            </div>
            <ion-badge :color="getStatusColor(order.status)" class="status-badge">
              {{ order.status.toUpperCase() }}
            </ion-badge>
          </div>

          <!-- Customer Info -->
          <div class="customer-info">
            <ion-icon :icon="personOutline"></ion-icon>
            <span>{{ order.customerName }}</span>
          </div>

          <!-- Order Items Summary -->
          <div class="order-items">
            <ion-icon :icon="cartOutline"></ion-icon>
            <span>{{ order.itemCount }} item{{ order.itemCount > 1 ? 's' : '' }}</span>
          </div>

          <!-- Order Footer -->
          <div class="order-footer">
            <div class="order-total">
              <span class="label">Total:</span>
              <span class="amount">₱{{ formatPrice(order.total) }}</span>
            </div>
            <ion-button size="small" :color="getActionColor(order.status)" @click.stop="quickAction(order)">
              {{ getActionText(order.status) }}
            </ion-button>
          </div>
        </div>
      </div>
    </ion-content>

    <!-- Bottom Navigation -->
    <BottomNav active="orders" />
  </ion-page>
</template>

<script setup lang="ts">
import { api } from '@/utils/apiClient';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { onIonViewWillEnter } from '@ionic/vue';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonButton,
  IonIcon,
  IonSegment,
  IonSegmentButton,
  IonLabel,
  IonSearchbar,
  IonBadge,
  actionSheetController,
  alertController,
  toastController,
} from '@ionic/vue';
import {
  filterOutline,
  receiptOutline,
  personOutline,
  cartOutline,
  checkmarkCircleOutline,
  timeOutline,
  closeCircleOutline,
} from 'ionicons/icons';
import { type Order } from '@/types';
import BottomNav from '@/components/BottomNav.vue';
import { useSuccessModal } from '@/composables/useSuccessModal';
import { useModals } from '@/composables/useModals';

const router = useRouter();
const { showSuccessModal } = useSuccessModal();
const { showLoadingModal } = useModals();

// State
const selectedTab = ref('all');
const searchQuery = ref('');
const loading = ref(false);

const orders = ref([] as Order[]);

const loadOrders = async () => {
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  loading.value = true;
  try {
    const data = await api.get(`/seller/orders?email=${encodeURIComponent(email)}`);
    if (data.orders) {
      orders.value = data.orders.map((o: any) => ({
        id: o.id,
        customerName: o.customer_name,
        customerPhone: o.customer_phone,
        deliveryAddress: o.delivery_address,
        items: o.items || [],
        itemCount: o.item_count,
        total: o.total,
        status: o.status,
        date: o.created_at,
      }));
    }
  } catch {
    // keep empty
  } finally {
    loading.value = false;
  }
};

onIonViewWillEnter(() => {
  loadOrders();
});

// Computed
const orderStats = computed(() => {
  return {
    all: orders.value.length,
    pending: orders.value.filter((o) => o.status === 'pending').length,
    processing: orders.value.filter((o) => o.status === 'processing').length,
    completed: orders.value.filter((o) => o.status === 'completed').length,
  };
});

const filteredOrders = computed(() => {
  let filtered = orders.value;

  // Filter by tab
  if (selectedTab.value !== 'all') {
    filtered = filtered.filter((order) => order.status === selectedTab.value);
  }

  // Filter by search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (order) => String(order.id).toLowerCase().includes(query) || order.customerName.toLowerCase().includes(query),
    );
  }

  return filtered;
});

const emptyStateMessage = computed(() => {
  if (searchQuery.value) {
    return 'No orders match your search';
  }
  if (selectedTab.value === 'pending') {
    return 'No pending orders at the moment';
  }
  if (selectedTab.value === 'processing') {
    return 'No orders being processed';
  }
  if (selectedTab.value === 'completed') {
    return 'No completed orders yet';
  }
  return 'No orders yet. Orders will appear here once customers place them.';
});

// Methods
const handleTabChange = (event: any) => {
  selectedTab.value = event.detail.value;
};

const handleSearch = () => {
  // Search is handled by computed property
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now.getTime() - date.getTime();
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);

  if (minutes < 60) {
    return `${minutes} min${minutes !== 1 ? 's' : ''} ago`;
  } else if (hours < 24) {
    return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
  } else if (days < 7) {
    return `${days} day${days !== 1 ? 's' : ''} ago`;
  } else {
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  }
};

const formatPrice = (price: any) => {
  return price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const getStatusColor = (status: string) => {
  const colors: Record<string, any> = {
    pending: 'warning',
    processing: 'primary',
    completed: 'success',
    cancelled: 'danger',
  };
  return colors[status] || 'medium';
};

const getActionColor = (status: string) => {
  if (status === 'pending') return 'success';
  if (status === 'processing') return 'primary';
  return 'medium';
};

const getActionText = (status: string) => {
  if (status === 'pending') return 'Accept';
  if (status === 'processing') return 'Complete';
  if (status === 'completed') return 'View';
  return 'View';
};

const viewOrderDetails = (orderId: any) => {
  router.push(`/orders/${orderId}`);
};

const quickAction = async (order: any) => {
  if (order.status === 'pending') {
    await acceptOrder(order);
  } else if (order.status === 'processing') {
    await completeOrder(order);
  } else {
    viewOrderDetails(order.id);
  }
};

const updateOrderStatus = async (orderId: any, newStatus: any) => {
  const email = localStorage.getItem('userEmail');
  if (!email) return false;
  try {
    await api.post(`/seller/orders/${orderId}/status`, { email, status: newStatus });
    return true;
  } catch {
    return false;
  }
};

const acceptOrder = async (order: any) => {
  const alert = await alertController.create({
    header: 'Accept Order',
    message: `Accept order #${order.id} from ${order.customerName}?`,
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Accept',
        handler: async () => {
          const loading = await showLoadingModal();
          const ok = await updateOrderStatus(order.id, 'processing');
          await loading.dismiss();
          if (ok) {
            order.status = 'processing';
            await showSuccessModal('Order accepted!');
          } else {
            showToast('Failed to accept order', 'danger');
          }
        },
      },
    ],
  });
  await alert.present();
};

const completeOrder = async (order: any) => {
  const alert = await alertController.create({
    header: 'Complete Order',
    message: `Mark order #${order.id} as completed?`,
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Complete',
        handler: async () => {
          const loading = await showLoadingModal();
          const ok = await updateOrderStatus(order.id, 'completed');
          await loading.dismiss();
          if (ok) {
            order.status = 'completed';
            await showSuccessModal('Order completed successfully!');
          } else {
            showToast('Failed to complete order', 'danger');
          }
        },
      },
    ],
  });
  await alert.present();
};

const showFilterOptions = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Sort Orders',
    buttons: [
      {
        text: 'Newest First',
        icon: timeOutline,
        handler: () => {
          orders.value.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());
          showToast('Sorted by newest first');
        },
      },
      {
        text: 'Oldest First',
        icon: timeOutline,
        handler: () => {
          orders.value.sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
          showToast('Sorted by oldest first');
        },
      },
      {
        text: 'Highest Amount',
        icon: cartOutline,
        handler: () => {
          orders.value.sort((a, b) => b.total - a.total);
          showToast('Sorted by highest amount');
        },
      },
      {
        text: 'Lowest Amount',
        icon: cartOutline,
        handler: () => {
          orders.value.sort((a, b) => a.total - b.total);
          showToast('Sorted by lowest amount');
        },
      },
      {
        text: 'Cancel',
        role: 'cancel',
      },
    ],
  });
  await actionSheet.present();
};

const showToast = async (message: string, color = 'dark') => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color,
    position: 'top',
  });
  await toast.present();
};


</script>

<style scoped>
/* Segment Tabs */
ion-segment {
  padding: 8px 16px;
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  text-align: center;
}

.loading-icon {
  font-size: 60px;
  color: #4caf50;
  margin-bottom: 16px;
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 1;
    transform: scale(1.1);
  }
}

.loading-state p {
  font-size: 15px;
  color: #666;
  margin: 0;
}

ion-segment-button {
  --color: #666;
  --color-checked: #4caf50;
  --indicator-color: #4caf50;
  font-size: 13px;
  font-weight: 600;
  min-height: 36px;
}

/* Orders List */
.orders-list {
  padding: 16px;
}

.order-card {
  background: white;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition:
    transform 0.2s,
    box-shadow 0.2s;
}

.order-card:active {
  transform: scale(0.98);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

/* Order Header */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid #f0f0f0;
}

.order-info {
  flex: 1;
}

.order-id {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.order-date {
  font-size: 13px;
  color: #999;
  margin: 0;
}

.status-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 6px 12px;
  border-radius: 8px;
}

/* Customer & Items Info */
.customer-info,
.order-items {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #666;
  margin-bottom: 8px;
}

.customer-info ion-icon,
.order-items ion-icon {
  font-size: 18px;
  color: #4caf50;
}

/* Order Footer */
.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid #f0f0f0;
}

.order-total {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.order-total .label {
  font-size: 12px;
  color: #999;
}

.order-total .amount {
  font-size: 18px;
  font-weight: 700;
  color: #4caf50;
}

.order-footer ion-button {
  --border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-transform: none;
  min-width: 90px;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 40px;
  text-align: center;
  min-height: 400px;
}

.empty-icon {
  width: 100px;
  height: 100px;
  background: #f0f0f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
}

.empty-icon ion-icon {
  font-size: 48px;
  color: #999;
}

.empty-state h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.empty-state p {
  font-size: 14px;
  color: #999;
  margin: 0;
  max-width: 300px;
}

/* Responsive */
@media (max-width: 480px) {
  ion-segment-button {
    font-size: 11px;
    padding: 0 4px;
  }

  .order-card {
    padding: 14px;
  }

  .order-id {
    font-size: 15px;
  }

  .order-total .amount {
    font-size: 16px;
  }
}

/* Decline Modal Styling */
:deep(.decline-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 320px;
  padding: 30px 20px;
}

:deep(.decline-modal .alert-head) {
  padding: 0;
  min-height: 0;
}

:deep(.decline-modal .alert-message) {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.decline-modal .alert-button-group) {
  padding: 10px 0 0 0;
}

:deep(.decline-modal button) {
  background: #f44336;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
}
</style>
