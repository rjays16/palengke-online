<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>Notifications</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="markAllAsRead" v-if="unreadCount > 0 && !loading">
            <ion-icon :icon="checkmarkDoneOutline"></ion-icon>
          </ion-button>
          <ion-button @click="showOptions">
            <ion-icon :icon="ellipsisVerticalOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>

      <!-- Filter Tabs -->
      <ion-toolbar>
        <ion-segment v-model="selectedFilter" @ionChange="handleFilterChange">
          <ion-segment-button value="all">
            <ion-label>All ({{ notifications.length }})</ion-label>
          </ion-segment-button>
          <ion-segment-button value="unread">
            <ion-label>Unread ({{ unreadCount }})</ion-label>
          </ion-segment-button>
          <ion-segment-button value="orders">
            <ion-label>Orders</ion-label>
          </ion-segment-button>
          <ion-segment-button value="messages">
            <ion-label>Messages</ion-label>
          </ion-segment-button>
        </ion-segment>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <!-- Loading State -->
      <div v-if="loading" class="empty-state">
        <ion-spinner name="crescent" color="success" style="width: 48px; height: 48px"></ion-spinner>
        <h3>Loading...</h3>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredNotifications.length === 0" class="empty-state">
        <div class="empty-icon">
          <ion-icon :icon="notificationsOffOutline"></ion-icon>
        </div>
        <h3>No Notifications</h3>
        <p v-if="selectedFilter === 'unread'">You're all caught up!</p>
        <p v-else-if="selectedFilter === 'orders'">No order notifications</p>
        <p v-else-if="selectedFilter === 'messages'">No message notifications</p>
        <p v-else>You don't have any notifications yet</p>
      </div>

      <!-- Notifications List -->
      <div v-else class="notifications-list">
        <div
          v-for="notification in filteredNotifications"
          :key="notification.id"
          class="notification-card"
          :class="{ unread: !notification.read }"
          @click="handleNotificationClick(notification)"
        >
          <!-- Notification Icon -->
          <div class="notification-icon" :class="notification.type">
            <ion-icon :icon="getNotificationIcon(notification.type)"></ion-icon>
          </div>

          <!-- Notification Content -->
          <div class="notification-content">
            <h3 class="notification-title">{{ notification.title }}</h3>
            <p class="notification-message">{{ notification.message }}</p>
            <div class="notification-footer">
              <span class="notification-time">{{ formatTime(notification.time) }}</span>
              <ion-badge v-if="!notification.read" color="success" class="unread-badge">New</ion-badge>
            </div>
          </div>

          <!-- Unread Indicator -->
          <div v-if="!notification.read" class="unread-dot"></div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { api } from '@/utils/apiClient';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonButton,
  IonBackButton,
  IonIcon,
  IonSegment,
  IonSegmentButton,
  IonLabel,
  IonBadge,
  IonSpinner,
  actionSheetController,
  alertController,
  toastController,
} from '@ionic/vue';
import {
  notificationsOffOutline,
  checkmarkDoneOutline,
  ellipsisVerticalOutline,
  cartOutline,
  chatbubbleEllipsesOutline,
  cashOutline,
  alertCircleOutline,
  informationCircleOutline,
  checkmarkCircleOutline,
  warningOutline,
  closeCircleOutline,
} from 'ionicons/icons';
import { type Notification } from '@/types';
import { useSuccessModal } from '@/composables/useSuccessModal';
import { useModals } from '@/composables/useModals';
const router = useRouter();
const { showSuccessModal } = useSuccessModal();
const { showLoadingModal } = useModals();

// State
const selectedFilter = ref('all');
const notifications = ref([] as Notification[]);
const loading = ref(true);

const loadNotifications = async () => {
  loading.value = true;
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  try {
    const data = await api.get(`/seller/notifications?email=${encodeURIComponent(email)}`);
    if (data.notifications) {
      notifications.value = data.notifications;
    }
  } catch {
    // silent
  } finally {
    loading.value = false;
  }
};

loadNotifications();

// Computed
const unreadCount = computed(() => {
  return notifications.value.filter((n) => !n.read).length;
});

const filteredNotifications = computed(() => {
  let filtered = notifications.value;

  if (selectedFilter.value === 'unread') {
    filtered = filtered.filter((n) => !n.read);
  } else if (selectedFilter.value === 'orders') {
    filtered = filtered.filter((n) => n.type === 'order');
  } else if (selectedFilter.value === 'messages') {
    filtered = filtered.filter((n) => n.type === 'message');
  }

  return filtered;
});

// Methods
const handleFilterChange = (event: any) => {
  selectedFilter.value = event.detail.value;
};

const getNotificationIcon = (type: string) => {
  const icons: Record<string, any> = {
    order: cartOutline,
    message: chatbubbleEllipsesOutline,
    payment: cashOutline,
    warning: warningOutline,
    info: informationCircleOutline,
    success: checkmarkCircleOutline,
    error: closeCircleOutline,
  };
  return icons[type] || informationCircleOutline;
};

const formatTime = (dateString: string) => {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now.getTime() - date.getTime();
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);

  if (minutes < 1) {
    return 'Just now';
  } else if (minutes < 60) {
    return `${minutes} min${minutes !== 1 ? 's' : ''} ago`;
  } else if (hours < 24) {
    return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
  } else if (days < 7) {
    return `${days} day${days !== 1 ? 's' : ''} ago`;
  } else {
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  }
};

const handleNotificationClick = async (notification: any) => {
  notification.read = true;

  const email = localStorage.getItem('userEmail');
  if (email) {
    try {
      await api.post(`/seller/notifications/${notification.id}/read?email=${encodeURIComponent(email)}`);
    } catch {
      // silent
    }
  }

  if (notification.type === 'order' && notification.orderId) {
    router.push(`/orders/${notification.orderId}`);
  } else if (notification.type === 'message' && notification.customerId) {
    router.push('/messages');
  } else if (notification.type === 'payment' && notification.orderId) {
    router.push(`/orders/${notification.orderId}`);
  } else if (notification.type === 'warning' && notification.productId) {
    router.push('/products');
  }
};

const markAllAsRead = async () => {
  const alert = await alertController.create({
    header: 'Mark All as Read',
    message: 'Mark all notifications as read?',
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Mark All',
        handler: async () => {
          const email = localStorage.getItem('userEmail');
          if (email) {
            try {
              await api.post(`/seller/notifications/read-all?email=${encodeURIComponent(email)}`);
            } catch {
              // silent
            }
          }
          notifications.value.forEach((n) => (n.read = true));
          await showSuccessModal('All notifications marked as read');
        },
      },
    ],
  });
  await alert.present();
};

const showOptions = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Notification Options',
    buttons: [
      {
        text: 'Mark All as Read',
        icon: checkmarkDoneOutline,
        handler: () => {
          markAllAsRead();
        },
      },
      {
        text: 'Clear All Notifications',
        icon: closeCircleOutline,
        role: 'destructive',
        handler: async () => {
          const confirmAlert = await alertController.create({
            header: 'Clear All',
            message: 'Are you sure you want to clear all notifications?',
            buttons: [
              {
                text: 'Cancel',
                role: 'cancel',
              },
              {
                text: 'Clear',
                role: 'destructive',
                handler: async () => {
                  const email = localStorage.getItem('userEmail');
                  if (email) {
                    try {
                      await api.delete(`/seller/notifications?email=${encodeURIComponent(email)}`);
                    } catch {
                      // silent
                    }
                  }
                  notifications.value = [];
                  await showSuccessModal('All notifications cleared');
                },
              },
            ],
          });
          await confirmAlert.present();
        },
      },
      {
        text: 'Notification Settings',
        icon: alertCircleOutline,
        handler: async () => {
          await showLoadingModal('Opening settings...');
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
</script>

<style scoped>
/* Segment Tabs */
ion-segment {
  padding: 8px 16px;
}

ion-segment-button {
  --color: #666;
  --color-checked: #4caf50;
  --indicator-color: #4caf50;
  font-size: 12px;
  font-weight: 600;
  min-height: 36px;
}

/* Notifications List */
.notifications-list {
  padding: 8px 16px 16px 16px;
}

.notification-card {
  background: white;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: flex-start;
  gap: 12px;
  cursor: pointer;
  transition:
    transform 0.2s,
    box-shadow 0.2s;
  position: relative;
}

.notification-card:active {
  transform: scale(0.98);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

.notification-card.unread {
  background: #f0f9f0;
  border-left: 4px solid #4caf50;
}

/* Notification Icon */
.notification-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}

.notification-icon.order {
  background: #e8f5e9;
  color: #4caf50;
}

.notification-icon.message {
  background: #e3f2fd;
  color: #2196f3;
}

.notification-icon.payment {
  background: #fff3e0;
  color: #ff9800;
}

.notification-icon.warning {
  background: #fff3e0;
  color: #ff9800;
}

.notification-icon.info {
  background: #f3e5f5;
  color: #9c27b0;
}

.notification-icon.success {
  background: #e8f5e9;
  color: #4caf50;
}

.notification-icon.error {
  background: #ffebee;
  color: #f44336;
}

/* Notification Content */
.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-title {
  font-size: 15px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.notification-message {
  font-size: 14px;
  color: #666;
  margin: 0 0 8px 0;
  line-height: 1.4;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.notification-footer {
  display: flex;
  align-items: center;
  gap: 8px;
}

.notification-time {
  font-size: 12px;
  color: #999;
}

.unread-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
}

/* Unread Indicator Dot */
.unread-dot {
  width: 10px;
  height: 10px;
  background: #4caf50;
  border-radius: 50%;
  position: absolute;
  top: 16px;
  right: 16px;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 40px;
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

  .notification-icon {
    width: 40px;
    height: 40px;
    font-size: 20px;
  }

  .notification-title {
    font-size: 14px;
  }

  .notification-message {
    font-size: 13px;
  }
}


</style>
