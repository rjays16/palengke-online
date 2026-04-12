<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start"><ion-back-button default-href="/home"></ion-back-button></ion-buttons>
        <ion-title>Messages</ion-title>
      </ion-toolbar>
      <ion-toolbar>
        <ion-segment v-model="selectedTab">
          <ion-segment-button value="notifications"><ion-label>Notifications</ion-label></ion-segment-button>
          <ion-segment-button value="chats"><ion-label>Chats</ion-label></ion-segment-button>
        </ion-segment>
      </ion-toolbar>
    </ion-header>

    <ion-content class="msg-content">

      <!-- Notifications Tab -->
      <div v-if="selectedTab === 'notifications'">
        <div v-if="notifications.length === 0" class="empty-state">
          <div class="empty-icon">🔔</div>
          <h3>No notifications</h3>
          <p>You're all caught up!</p>
        </div>
        <div v-else class="notif-list">
          <div v-for="notif in notifications" :key="notif.id"
            class="notif-item" :class="{ unread: !notif.read }"
            @click="notif.read = true">
            <div class="notif-icon" :class="notif.type">{{ getIcon(notif.type) }}</div>
            <div class="notif-body">
              <h4>{{ notif.title }}</h4>
              <p>{{ notif.message }}</p>
              <span class="notif-time">{{ notif.time }}</span>
            </div>
            <div class="unread-dot" v-if="!notif.read"></div>
          </div>
        </div>
      </div>

      <!-- Chats Tab -->
      <div v-if="selectedTab === 'chats'">
        <div v-if="chats.length === 0" class="empty-state">
          <div class="empty-icon">💬</div>
          <h3>No chats yet</h3>
          <p>Messages from sellers and drivers will appear here</p>
        </div>
        <div v-else class="chat-list">
          <div v-for="chat in chats" :key="chat.id" class="chat-item" @click="openChat(chat)">
            <div class="chat-avatar" v-if="false">{{ chat.emoji }}</div>
            <div class="chat-info">
              <div class="chat-header">
                <h4>{{ chat.name }}</h4>
                <span class="chat-time">{{ chat.time }}</span>
              </div>
              <div class="chat-preview">
                <p>{{ chat.lastMessage }}</p>
                <span class="unread-badge" v-if="chat.unread > 0">{{ chat.unread }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonSegment, IonSegmentButton, IonLabel
} from '@ionic/vue';

const router = useRouter();
const selectedTab = ref('notifications');

const notifications = ref([
  { id: 1, type: 'order', title: 'Driver is on the way!', message: 'Your order from Juan\'s Fresh Market is being delivered.', time: '5 mins ago', read: false },
  { id: 2, type: 'promo', title: 'New Promo Available!', message: 'Get off on your next order. Use code PALENGKE50.', time: '1 hour ago', read: false },
  { id: 3, type: 'order', title: 'Order Delivered!', message: 'Your order from Rosa\'s Seafoods has been delivered.', time: 'Yesterday', read: true },
]);

const chats = ref([
  { id: 1, name: "Juan's Fresh Market", emoji: '', lastMessage: 'Your order is ready for pickup!', time: '5 mins ago', unread: 2 },
  { id: 2, name: 'Juan Dela Cruz (Driver)', emoji: '', lastMessage: 'I am on my way to your location', time: '10 mins ago', unread: 1 },
]);

const getIcon = (type) => {
  if (type === 'order') return '';
  if (type === 'promo') return '';
  return '';
};

const openChat = (chat) => {};
</script>

<style scoped>
.msg-content { --background: #f8f9fa; }
ion-segment { padding: 8px 16px; }
ion-segment-button { --color: #666; --color-checked: #4CAF50; --indicator-color: #4CAF50; font-size: 13px; font-weight: 600; }

.empty-state { text-align: center; padding: 80px 40px; }
.empty-icon { font-size: 64px; margin-bottom: 16px; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0; }

.notif-list { padding: 8px 0; }
.notif-item { display: flex; align-items: flex-start; gap: 14px; padding: 16px 20px; background: white; border-bottom: 1px solid #f0f0f0; cursor: pointer; position: relative; }
.notif-item.unread { background: #F1F8E9; border-left: 4px solid #4CAF50; }
.notif-icon { width: 44px; height: 44px; border-radius: 12px; background: #E8F5E9; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.notif-body { flex: 1; }
.notif-body h4 { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.notif-body p { font-size: 13px; color: #666; margin: 0 0 6px 0; line-height: 1.4; }
.notif-time { font-size: 11px; color: #999; }
.unread-dot { width: 10px; height: 10px; background: #4CAF50; border-radius: 50%; flex-shrink: 0; margin-top: 6px; }

.chat-list { padding: 8px 0; }
.chat-item { display: flex; align-items: center; gap: 14px; padding: 14px 20px; background: white; border-bottom: 1px solid #f0f0f0; cursor: pointer; }
.chat-avatar { width: 52px; height: 52px; border-radius: 50%; background: #E8F5E9; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; }
.chat-info { flex: 1; }
.chat-header { display: flex; justify-content: space-between; margin-bottom: 4px; }
.chat-header h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0; }
.chat-time { font-size: 12px; color: #999; }
.chat-preview { display: flex; justify-content: space-between; align-items: center; }
.chat-preview p { font-size: 13px; color: #666; margin: 0; flex: 1; }
.unread-badge { background: #4CAF50; color: white; border-radius: 10px; padding: 2px 7px; font-size: 11px; font-weight: 700; }
</style>