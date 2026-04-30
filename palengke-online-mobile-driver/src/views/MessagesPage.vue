<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>Messages</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="markAllRead" v-if="unreadCount > 0">
            <ion-icon :icon="checkmarkDoneOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>

      <!-- Tabs -->
      <ion-toolbar>
        <ion-segment v-model="selectedTab" @ionChange="handleTabChange">
          <ion-segment-button value="notifications">
            <ion-label>Notifications</ion-label>
          </ion-segment-button>
          <ion-segment-button value="chats">
            <ion-label>Chats</ion-label>
          </ion-segment-button>
        </ion-segment>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="messages-content">

      <!-- NOTIFICATIONS TAB -->
      <div v-if="selectedTab === 'notifications'">

        <!-- Empty -->
        <div v-if="notifications.length === 0" class="empty-state">
          <div class="empty-icon-wrap">
            <ion-icon :icon="notificationsOffOutline"></ion-icon>
          </div>
          <h3>No Notifications</h3>
          <p>You're all caught up!</p>
        </div>

        <!-- List -->
        <div v-else class="notif-list">
          <div
            v-for="notif in notifications"
            :key="notif.id"
            class="notif-item"
            :class="{ unread: !notif.read }"
            @click="readNotification(notif)"
          >
            <div class="notif-icon" :class="notif.type">
              <ion-icon :icon="getNotifIcon(notif.type)"></ion-icon>
            </div>
            <div class="notif-content">
              <h4>{{ notif.title }}</h4>
              <p>{{ notif.message }}</p>
              <span class="notif-time">{{ notif.time }}</span>
            </div>
            <div class="unread-dot" v-if="!notif.read"></div>
          </div>
        </div>
      </div>

      <!-- CHATS TAB -->
      <div v-if="selectedTab === 'chats'">

        <!-- Empty -->
        <div v-if="chats.length === 0" class="empty-state">
          <div class="empty-icon-wrap">
            <ion-icon :icon="chatbubblesOutline"></ion-icon>
          </div>
          <h3>No Chats Yet</h3>
          <p>Customer messages will appear here</p>
        </div>

        <!-- Chat List -->
        <div v-else class="chat-list">
          <div
            v-for="chat in chats"
            :key="chat.id"
            class="chat-item"
            :class="{ unread: chat.unread > 0 }"
            @click="openChat(chat)"
          >
            <div class="chat-avatar" :class="chat.type">
              <ion-icon :icon="chat.type === 'seller' ? storefrontOutline : personOutline"></ion-icon>
            </div>
            <div class="chat-content">
              <div class="chat-header">
                <h4>{{ chat.name }}</h4>
                <span class="chat-time">{{ chat.time }}</span>
              </div>
              <div class="chat-preview">
                <p>{{ chat.lastMessage }}</p>
                <span class="unread-badge" v-if="chat.unread > 0">{{ chat.unread }}</span>
              </div>
              <span class="order-ref" v-if="chat.orderId">#{{ chat.orderId }}</span>
            </div>
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
  IonButtons, IonBackButton, IonButton, IonIcon,
  IonSegment, IonSegmentButton, IonLabel,
  alertController, toastController
} from '@ionic/vue';
import {
  checkmarkDoneOutline, notificationsOffOutline,
  chatbubblesOutline, storefrontOutline, personOutline,
  cartOutline, cashOutline, warningOutline,
  informationCircleOutline, trophyOutline, starOutline
} from 'ionicons/icons';

const router = useRouter();
const selectedTab = ref('notifications');

const notifications = ref([
  { id: 1, type: 'order', title: 'New Order Available!', message: 'Order #1234 from Juan\'s Fresh Market - ₱85 earnings', time: '2 mins ago', read: false },
  { id: 2, type: 'payment', title: 'Payment Received', message: 'You received ₱250 for Order #1233', time: '1 hour ago', read: false },
  { id: 3, type: 'tip', title: 'You Got a Tip!', message: 'Maria Santos gave you a ₱50 tip. Thank you!', time: '2 hours ago', read: true },
  { id: 4, type: 'warning', title: 'Low Acceptance Rate', message: 'Your acceptance rate dropped to 75%. Try to accept more orders.', time: 'Yesterday', read: true },
  { id: 5, type: 'info', title: 'Weekly Earnings Report', message: 'You earned ₱11,196 this week! Great job!', time: '2 days ago', read: true },
  { id: 6, type: 'achievement', title: 'New Badge Earned!', message: 'You earned the "Fast Delivery" badge!', time: '3 days ago', read: true },
]);

const chats = ref([
  { id: 1, name: 'Maria Santos', type: 'customer', lastMessage: 'Thank you kuya! Very fast!', time: '5 mins ago', unread: 2, orderId: '1234' },
  { id: 2, name: "Juan's Fresh Market", type: 'seller', lastMessage: 'Order is ready for pickup!', time: '30 mins ago', unread: 1, orderId: '1234' },
  { id: 3, name: 'Pedro Cruz', type: 'customer', lastMessage: 'How long will it take?', time: '1 hour ago', unread: 0, orderId: '1233' },
  { id: 4, name: "Ana's Palengke", type: 'seller', lastMessage: 'Please come to stall 8', time: 'Yesterday', unread: 0, orderId: '1232' },
]);

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const handleTabChange = (e) => { selectedTab.value = e.detail.value; };

const getNotifIcon = (type) => {
  const icons = {
    order: cartOutline, payment: cashOutline,
    tip: cashOutline, warning: warningOutline,
    info: informationCircleOutline, achievement: trophyOutline
  };
  return icons[type] || informationCircleOutline;
};

const readNotification = (notif) => {
  notif.read = true;
  if (notif.type === 'tip') router.push('/tip-received');
};

const markAllRead = async () => {
  notifications.value.forEach(n => n.read = true);
  const toast = await toastController.create({ message: 'All marked as read', duration: 2000, color: 'medium', position: 'top' });
  await toast.present();
};

const openChat = (chat) => {
  // Navigate to chat detail
};
</script>

<style scoped>
.messages-content { --background: #f8f9fa; }

ion-segment { padding: 8px 16px; background: white; gap: 8px; }
ion-segment-button {
  --color: #666; --color-checked: white;
  --indicator-color: #4CAF50;
  --background-checked: #4CAF50;
  --border-radius: 20px;
  font-size: 13px; font-weight: 600; min-height: 36px;
}

/* Notifications */
.notif-list { padding: 8px 0; }

.notif-item {
  display: flex; align-items: flex-start; gap: 14px;
  padding: 16px 20px; background: white;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer; position: relative; transition: background 0.2s;
}
.notif-item.unread { background: #F0F9F0; border-left: 4px solid #4CAF50; }
.notif-item:active { background: #f5f5f5; }

.notif-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
.notif-icon.order { background: #E8F5E9; color: #4CAF50; }
.notif-icon.payment { background: #E3F2FD; color: #2196F3; }
.notif-icon.tip { background: #FFF3E0; color: #FF9800; }
.notif-icon.warning { background: #FFEBEE; color: #f44336; }
.notif-icon.info { background: #F3E5F5; color: #9C27B0; }
.notif-icon.achievement { background: #FFF8E1; color: #FFD700; }

.notif-content { flex: 1; min-width: 0; }
.notif-content h4 { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.notif-content p { font-size: 13px; color: #666; margin: 0 0 6px 0; line-height: 1.4; }
.notif-time { font-size: 11px; color: #999; }

.unread-dot { width: 10px; height: 10px; background: #4CAF50; border-radius: 50%; flex-shrink: 0; margin-top: 6px; }

/* Chats */
.chat-list { padding: 8px 0; }

.chat-item {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 20px; background: white;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer; transition: background 0.2s;
}
.chat-item.unread { background: #F0F9F0; }
.chat-item:active { background: #f5f5f5; }

.chat-avatar { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; }
.chat-avatar.customer { background: #E8F5E9; color: #4CAF50; }
.chat-avatar.seller { background: #E3F2FD; color: #2196F3; }

.chat-content { flex: 1; min-width: 0; }
.chat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.chat-header h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0; }
.chat-time { font-size: 12px; color: #999; }

.chat-preview { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.chat-preview p { font-size: 13px; color: #666; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1; }

.unread-badge { background: #4CAF50; color: white; border-radius: 10px; padding: 2px 7px; font-size: 11px; font-weight: 700; flex-shrink: 0; margin-left: 8px; }

.order-ref { font-size: 11px; color: #4CAF50; font-weight: 600; }

/* Empty State */
.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 40px; text-align: center; }
.empty-icon-wrap { width: 100px; height: 100px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; }
.empty-icon-wrap ion-icon { font-size: 48px; color: #999; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0; }
</style>