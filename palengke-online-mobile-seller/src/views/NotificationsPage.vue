<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>Notifications</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="markAllAsRead" v-if="unreadCount > 0">
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
      <!-- Empty State -->
      <div v-if="filteredNotifications.length === 0" class="empty-state">
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

<script setup>
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
  actionSheetController,
  alertController,
  toastController
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
  closeCircleOutline
} from 'ionicons/icons';

const router = useRouter();

// State
const selectedFilter = ref('all');

// Mock Notifications Data (TODO: Replace with API)
const notifications = ref([
  {
    id: 1,
    type: 'order',
    title: 'New Order Received',
    message: 'Maria Santos placed an order for ₱1,250.50',
    time: '2026-01-03T14:30:00',
    read: false,
    orderId: '001'
  },
  {
    id: 2,
    type: 'message',
    title: 'New Message',
    message: 'Juan: "Is the Bangus still available?"',
    time: '2026-01-03T14:15:00',
    read: false,
    customerId: 'customer_002'
  },
  {
    id: 3,
    type: 'payment',
    title: 'Payment Received',
    message: 'Payment of ₱850.00 received for Order #002',
    time: '2026-01-03T13:45:00',
    read: true,
    orderId: '002'
  },
  {
    id: 4,
    type: 'order',
    title: 'Order Completed',
    message: 'Order #003 has been delivered successfully',
    time: '2026-01-03T12:30:00',
    read: true,
    orderId: '003'
  },
  {
    id: 5,
    type: 'warning',
    title: 'Low Stock Alert',
    message: 'Pork Belly is running low (8 kg left)',
    time: '2026-01-03T11:00:00',
    read: false,
    productId: 'prod_002'
  },
  {
    id: 6,
    type: 'message',
    title: 'New Message',
    message: 'Ana: "Thank you for the fresh vegetables!"',
    time: '2026-01-03T10:15:00',
    read: true,
    customerId: 'customer_003'
  },
  {
    id: 7,
    type: 'order',
    title: 'Order Cancelled',
    message: 'Order #004 was cancelled by customer',
    time: '2026-01-03T09:30:00',
    read: true,
    orderId: '004'
  },
  {
    id: 8,
    type: 'info',
    title: 'Weekly Sales Report',
    message: 'Your sales this week: ₱18,750 (+12.5%)',
    time: '2026-01-02T08:00:00',
    read: true
  },
  {
    id: 9,
    type: 'order',
    title: 'New Order Received',
    message: 'Lisa Ramos placed an order for ₱980.00',
    time: '2026-01-02T16:45:00',
    read: true,
    orderId: '005'
  },
  {
    id: 10,
    type: 'warning',
    title: 'Low Stock Alert',
    message: 'Banana (Lakatan) is running low (5 kg left)',
    time: '2026-01-02T14:20:00',
    read: true,
    productId: 'prod_005'
  }
]);

// Computed
const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.read).length;
});

const filteredNotifications = computed(() => {
  let filtered = notifications.value;

  if (selectedFilter.value === 'unread') {
    filtered = filtered.filter(n => !n.read);
  } else if (selectedFilter.value === 'orders') {
    filtered = filtered.filter(n => n.type === 'order');
  } else if (selectedFilter.value === 'messages') {
    filtered = filtered.filter(n => n.type === 'message');
  }

  return filtered;
});

// ✨ NEW: Success Modal Function
const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  // Add the image after modal is presented
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      // Clear the text content
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      // Create wrapper for centering
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      // Create and add the image
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      // Create and add the text
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      
      // Append to wrapper
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      
      // Append wrapper to alert
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  // Auto dismiss after 2 seconds
  setTimeout(() => {
    alert.dismiss();
  }, 2000);
};

// ✨ NEW: Loading Modal Function
const showLoadingModal = async (message = 'Loading...') => {
  const alert = await alertController.create({
    cssClass: 'loading-modal-simple',
    message: message,
    buttons: []
  });
  
  await alert.present();
  
  // Auto dismiss after 1.5 seconds
  setTimeout(() => {
    alert.dismiss();
  }, 1500);
};

// Methods
const handleFilterChange = (event) => {
  selectedFilter.value = event.detail.value;
};

const getNotificationIcon = (type) => {
  const icons = {
    order: cartOutline,
    message: chatbubbleEllipsesOutline,
    payment: cashOutline,
    warning: warningOutline,
    info: informationCircleOutline,
    success: checkmarkCircleOutline,
    error: closeCircleOutline
  };
  return icons[type] || informationCircleOutline;
};

const formatTime = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;
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

const handleNotificationClick = (notification) => {
  // Mark as read
  notification.read = true;

  // Navigate based on notification type
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
        role: 'cancel'
      },
      {
        text: 'Mark All',
        handler: async () => {
          notifications.value.forEach(n => n.read = true);
          await showSuccessModal('All notifications marked as read');
        }
      }
    ]
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
        }
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
                role: 'cancel'
              },
              {
                text: 'Clear',
                role: 'destructive',
                handler: async () => {
                  notifications.value = [];
                  await showSuccessModal('All notifications cleared');
                }
              }
            ]
          });
          await confirmAlert.present();
        }
      },
      {
        text: 'Notification Settings',
        icon: alertCircleOutline,
        handler: async () => {
          await showLoadingModal('Opening settings...');
        }
      },
      {
        text: 'Cancel',
        role: 'cancel'
      }
    ]
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
  --color-checked: #4CAF50;
  --indicator-color: #4CAF50;
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
  transition: transform 0.2s, box-shadow 0.2s;
  position: relative;
}

.notification-card:active {
  transform: scale(0.98);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

.notification-card.unread {
  background: #F0F9F0;
  border-left: 4px solid #4CAF50;
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
  background: #E8F5E9;
  color: #4CAF50;
}

.notification-icon.message {
  background: #E3F2FD;
  color: #2196F3;
}

.notification-icon.payment {
  background: #FFF3E0;
  color: #FF9800;
}

.notification-icon.warning {
  background: #FFF3E0;
  color: #FF9800;
}

.notification-icon.info {
  background: #F3E5F5;
  color: #9C27B0;
}

.notification-icon.success {
  background: #E8F5E9;
  color: #4CAF50;
}

.notification-icon.error {
  background: #FFEBEE;
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
  background: #4CAF50;
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

/* ✨ NEW: Success Modal Styling */
:deep(.success-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 320px;
  padding: 30px 20px;
}

:deep(.success-modal .alert-head) {
  padding: 0;
  min-height: 0;
}

:deep(.success-modal .alert-message) {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.success-modal .alert-button-group) {
  padding: 10px 0 0 0;
}

:deep(.success-modal button) {
  background: #4CAF50;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
}

/* ✨ NEW: Loading Modal Styling */
:deep(.loading-modal-simple .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 280px;
  padding: 50px 30px;
  background: white;
}

:deep(.loading-modal-simple .alert-head) {
  display: none;
}

:deep(.loading-modal-simple .alert-message) {
  padding: 0;
  position: relative;
  padding-top: 70px;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

:deep(.loading-modal-simple .alert-message::before) {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  margin-left: -25px;
  width: 50px;
  height: 50px;
  border: 5px solid #f3f3f3;
  border-top: 5px solid #1877F2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

:deep(.loading-modal-simple .alert-button-group) {
  display: none;
}
</style>