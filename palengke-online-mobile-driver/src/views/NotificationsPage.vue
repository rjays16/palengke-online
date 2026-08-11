<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>Notifications</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="markAllRead" v-if="unreadCount > 0">
            <ion-icon :icon="checkmarkDoneOutline"></ion-icon>
          </ion-button>
          <ion-button @click="showOptions">
            <ion-icon :icon="ellipsisVerticalOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>

      <!-- Filter Tabs -->
      <ion-toolbar>
        <div class="custom-tabs">
          <div class="custom-tab" :class="{ active: selectedFilter === 'all' }" @click="selectedFilter = 'all'">
            All ({{ notifications.length }})
          </div>
          <div class="custom-tab" :class="{ active: selectedFilter === 'unread' }" @click="selectedFilter = 'unread'">
            Unread ({{ unreadCount }})
          </div>
          <div class="custom-tab" :class="{ active: selectedFilter === 'orders' }" @click="selectedFilter = 'orders'">
            Orders
          </div>
          <div
            class="custom-tab"
            :class="{ active: selectedFilter === 'earnings' }"
            @click="selectedFilter = 'earnings'"
          >
            Earnings
          </div>
          <div class="custom-tab" :class="{ active: selectedFilter === 'wallet' }" @click="selectedFilter = 'wallet'">
            Wallet
          </div>
        </div>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="notif-content">
      <!-- Empty -->
      <div v-if="filteredNotifications.length === 0" class="empty-state">
        <div class="empty-icon-wrap">
          <ion-icon :icon="notificationsOffOutline"></ion-icon>
        </div>
        <h3>No Notifications</h3>
        <p>{{ selectedFilter === 'unread' ? "You're all caught up!" : 'No notifications in this category' }}</p>
      </div>

      <!-- List -->
      <div v-else class="notif-list">
        <div
          v-for="notif in filteredNotifications"
          :key="notif.id"
          class="notif-card"
          :class="{ unread: !notif.read }"
          @click="handleNotifClick(notif)"
        >
          <div class="notif-icon" :class="notif.type">
            <ion-icon :icon="getNotifIcon(notif.type)"></ion-icon>
          </div>
          <div class="notif-body">
            <h4>{{ notif.title }}</h4>
            <p>{{ notif.message }}</p>
            <div class="notif-footer">
              <span class="notif-time">{{ notif.time }}</span>
              <span class="notif-badge" v-if="!notif.read">New</span>
            </div>
          </div>
          <div class="unread-indicator" v-if="!notif.read"></div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonButton,
  IonIcon,
  actionSheetController,
  toastController,
} from '@ionic/vue';
import {
  checkmarkDoneOutline,
  ellipsisVerticalOutline,
  notificationsOffOutline,
  cartOutline,
  cashOutline,
  warningOutline,
  informationCircleOutline,
  trophyOutline,
  walletOutline,
} from 'ionicons/icons';

const router = useRouter();
import { API_BASE } from '@/services/api';
let pollInterval = null;

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('driver_token');
}

const selectedFilter = ref('all');
const notifications = ref([]);

const unreadCount = computed(() => notifications.value.filter((n) => !n.read).length);

const filteredNotifications = computed(() => {
  if (selectedFilter.value === 'unread') return notifications.value.filter((n) => !n.read);
  if (selectedFilter.value === 'orders') return notifications.value.filter((n) => n.category === 'orders');
  if (selectedFilter.value === 'earnings') return notifications.value.filter((n) => n.category === 'earnings');
  if (selectedFilter.value === 'wallet') return notifications.value.filter((n) => n.category === 'wallet');
  return notifications.value;
});

const getNotifIcon = (type) => {
  const icons = {
    order: cartOutline,
    payment: cashOutline,
    tip: cashOutline,
    warning: warningOutline,
    info: informationCircleOutline,
    achievement: trophyOutline,
    wallet_top_up: walletOutline,
  };
  return icons[type] || informationCircleOutline;
};

const fetchNotifications = async () => {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/notifications`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      notifications.value = data.notifications || [];
    }
  } catch {
    /* ignore */
  }
};

const handleNotifClick = async (notif) => {
  if (!notif.read) {
    const token = getToken();
    if (token) {
      try {
        await fetch(`${API_BASE}/api/driver/notifications/${notif.id}/read`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${token}` },
        });
      } catch {
        /* ignore */
      }
    }
    notif.read = true;
  }
  if (notif.type === 'tip') {
    router.push({
      path: '/tip-received',
      query: notif.data
        ? {
            orderId: notif.data.order_id,
            amount: notif.data.amount,
            customerName: notif.data.customer_name,
            message: notif.data.message,
            deliveryFee: notif.data.delivery_fee,
          }
        : {},
    });
  }
  if (notif.type === 'order') router.push('/orders');
  if (notif.type === 'wallet_top_up') router.push('/wallet/transactions');
};

const markAllRead = async () => {
  const token = getToken();
  if (token) {
    try {
      await fetch(`${API_BASE}/api/driver/notifications/read-all`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token}` },
      });
    } catch {
      /* ignore */
    }
  }
  notifications.value.forEach((n) => (n.read = true));
  const toast = await toastController.create({
    message: 'All marked as read',
    duration: 2000,
    color: 'medium',
    position: 'top',
  });
  await toast.present();
};

const showOptions = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Notification Options',
    buttons: [
      { text: 'Mark All as Read', handler: () => markAllRead() },
      {
        text: 'Clear All',
        role: 'destructive',
        handler: async () => {
          const token = getToken();
          if (token) {
            try {
              await fetch(`${API_BASE}/api/driver/notifications`, {
                method: 'DELETE',
                headers: { Authorization: `Bearer ${token}` },
              });
            } catch {
              /* ignore */
            }
          }
          notifications.value = [];
          const toast = await toastController.create({
            message: 'All notifications cleared',
            duration: 2000,
            color: 'medium',
            position: 'top',
          });
          await toast.present();
        },
      },
      { text: 'Cancel', role: 'cancel' },
    ],
  });
  await actionSheet.present();
};

onMounted(() => {
  fetchNotifications();
  pollInterval = setInterval(fetchNotifications, 15000);
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
});
</script>

<style scoped>
.notif-content {
  --background: #f8f9fa;
}

/* Custom Tabs */
.custom-tabs {
  display: flex;
  gap: 8px;
  padding: 8px 16px;
  overflow-x: auto;
}
.custom-tabs::-webkit-scrollbar {
  display: none;
}

.custom-tab {
  padding: 8px 20px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  color: #666;
  background: #f0f0f0;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
  user-select: none;
}
.custom-tab.active {
  background: #4caf50;
  color: white;
}
.custom-tab:active {
  transform: scale(0.95);
}

/* Notifications */

.notif-list {
  padding: 8px 16px 16px;
}

.notif-card {
  background: white;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 12px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  cursor: pointer;
  position: relative;
  transition: transform 0.2s;
}
.notif-card.unread {
  background: #f0f9f0;
  border-left: 4px solid #4caf50;
}
.notif-card:active {
  transform: scale(0.98);
}

.notif-icon {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}
.notif-icon.order {
  background: #e8f5e9;
  color: #4caf50;
}
.notif-icon.payment {
  background: #e3f2fd;
  color: #2196f3;
}
.notif-icon.tip {
  background: #fff3e0;
  color: #ff9800;
}
.notif-icon.warning {
  background: #ffebee;
  color: #f44336;
}
.notif-icon.info {
  background: #f3e5f5;
  color: #9c27b0;
}
.notif-icon.achievement {
  background: #fff8e1;
  color: #ffd700;
}
.notif-icon.wallet_top_up {
  background: #e8f5e9;
  color: #4caf50;
}

.notif-body {
  flex: 1;
  min-width: 0;
}
.notif-body h4 {
  font-size: 14px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 5px 0;
}
.notif-body p {
  font-size: 13px;
  color: #555;
  margin: 0 0 8px 0;
  line-height: 1.4;
}

.notif-footer {
  display: flex;
  align-items: center;
  gap: 8px;
}
.notif-time {
  font-size: 12px;
  color: #999;
}
.notif-badge {
  background: #4caf50;
  color: white;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 700;
}

.unread-indicator {
  width: 10px;
  height: 10px;
  background: #4caf50;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 6px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 40px;
  text-align: center;
}
.empty-icon-wrap {
  width: 100px;
  height: 100px;
  background: #f0f0f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
}
.empty-icon-wrap ion-icon {
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
}
</style>
