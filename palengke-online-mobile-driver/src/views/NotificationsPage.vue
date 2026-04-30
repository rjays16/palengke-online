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
          <ion-segment-button value="earnings">
            <ion-label>Earnings</ion-label>
          </ion-segment-button>
        </ion-segment>
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
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonButton, IonIcon,
  IonSegment, IonSegmentButton, IonLabel,
  alertController, actionSheetController, toastController
} from '@ionic/vue';
import {
  checkmarkDoneOutline, ellipsisVerticalOutline,
  notificationsOffOutline, cartOutline, cashOutline,
  warningOutline, informationCircleOutline, trophyOutline,
  starOutline, closeCircleOutline
} from 'ionicons/icons';

const router = useRouter();
const selectedFilter = ref('all');

const notifications = ref([
  { id: 1, type: 'order', title: 'New Order Available!', message: 'Order #1234 from Juan\'s Fresh Market. Earnings: ₱85', time: '2 mins ago', read: false, category: 'orders' },
  { id: 2, type: 'tip', title: 'You Got a Tip!', message: 'Maria Santos gave you ₱50 tip for Order #1233!', time: '1 hour ago', read: false, category: 'earnings' },
  { id: 3, type: 'payment', title: 'Payment Received', message: 'You received ₱250 for Order #1232', time: '2 hours ago', read: false, category: 'earnings' },
  { id: 4, type: 'order', title: 'Order Completed', message: 'Order #1231 has been delivered successfully', time: '3 hours ago', read: true, category: 'orders' },
  { id: 5, type: 'warning', title: 'Low Acceptance Rate', message: 'Your acceptance rate is at 75%. Try to accept more orders.', time: 'Yesterday', read: true, category: 'orders' },
  { id: 6, type: 'achievement', title: 'New Badge Earned!', message: 'You earned the "Top Rated" badge! Keep it up!', time: '2 days ago', read: true, category: 'all' },
  { id: 7, type: 'info', title: 'Weekly Earnings Report', message: 'You earned ₱11,196 this week with 67 jobs completed!', time: '3 days ago', read: true, category: 'earnings' },
  { id: 8, type: 'order', title: 'Order Cancelled', message: 'Order #1228 was cancelled by the seller', time: '4 days ago', read: true, category: 'orders' },
]);

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const filteredNotifications = computed(() => {
  if (selectedFilter.value === 'unread') return notifications.value.filter(n => !n.read);
  if (selectedFilter.value === 'orders') return notifications.value.filter(n => n.category === 'orders');
  if (selectedFilter.value === 'earnings') return notifications.value.filter(n => n.category === 'earnings');
  return notifications.value;
});

const handleFilterChange = (e) => { selectedFilter.value = e.detail.value; };

const getNotifIcon = (type) => {
  const icons = {
    order: cartOutline, payment: cashOutline,
    tip: cashOutline, warning: warningOutline,
    info: informationCircleOutline, achievement: trophyOutline
  };
  return icons[type] || informationCircleOutline;
};

const handleNotifClick = (notif) => {
  notif.read = true;
  if (notif.type === 'tip') router.push('/tip-received');
  if (notif.type === 'order') router.push('/orders');
};

const markAllRead = async () => {
  notifications.value.forEach(n => n.read = true);
  const toast = await toastController.create({ message: 'All marked as read', duration: 2000, color: 'medium', position: 'top' });
  await toast.present();
};

const showOptions = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Notification Options',
    buttons: [
      { text: 'Mark All as Read', handler: () => markAllRead() },
      { text: 'Clear All', role: 'destructive', handler: async () => {
        notifications.value = [];
        const toast = await toastController.create({ message: 'All notifications cleared', duration: 2000, color: 'medium', position: 'top' });
        await toast.present();
      }},
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};
</script>

<style scoped>
.notif-content { --background: #f8f9fa; }

ion-segment { padding: 8px 16px; background: white; gap: 8px; }
ion-segment-button {
  --color: #666; --color-checked: white;
  --indicator-color: #4CAF50;
  --background-checked: #4CAF50;
  --border-radius: 20px;
  font-size: 12px; font-weight: 600; min-height: 36px;
}

.notif-list { padding: 8px 16px 16px; }

.notif-card {
  background: white; border-radius: 16px;
  padding: 16px; margin-bottom: 12px;
  display: flex; align-items: flex-start; gap: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  cursor: pointer; position: relative; transition: transform 0.2s;
}
.notif-card.unread { background: #F0F9F0; border-left: 4px solid #4CAF50; }
.notif-card:active { transform: scale(0.98); }

.notif-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
.notif-icon.order { background: #E8F5E9; color: #4CAF50; }
.notif-icon.payment { background: #E3F2FD; color: #2196F3; }
.notif-icon.tip { background: #FFF3E0; color: #FF9800; }
.notif-icon.warning { background: #FFEBEE; color: #f44336; }
.notif-icon.info { background: #F3E5F5; color: #9C27B0; }
.notif-icon.achievement { background: #FFF8E1; color: #FFD700; }

.notif-body { flex: 1; min-width: 0; }
.notif-body h4 { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0 0 5px 0; }
.notif-body p { font-size: 13px; color: #555; margin: 0 0 8px 0; line-height: 1.4; }

.notif-footer { display: flex; align-items: center; gap: 8px; }
.notif-time { font-size: 12px; color: #999; }
.notif-badge { background: #4CAF50; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; }

.unread-indicator { width: 10px; height: 10px; background: #4CAF50; border-radius: 50%; flex-shrink: 0; margin-top: 6px; }

.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 40px; text-align: center; }
.empty-icon-wrap { width: 100px; height: 100px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; }
.empty-icon-wrap ion-icon { font-size: 48px; color: #999; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0; }
</style>