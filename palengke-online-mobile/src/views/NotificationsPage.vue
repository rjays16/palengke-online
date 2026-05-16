<template>
<!-- NotificationsPage.vue -->
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start"><ion-back-button default-href="/profile"></ion-back-button></ion-buttons>
        <ion-title>Notifications</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="markAllRead" v-if="unreadCount > 0">
            <ion-icon :icon="checkmarkDoneOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>
    <ion-content class="notif-page-content">
      <div v-if="notifications.length === 0" class="empty-state">
        <div class="empty-icon">🔔</div>
        <h3>No Notifications</h3>
        <p>You're all caught up!</p>
      </div>
      <div v-else class="notif-page-list">
        <div v-for="notif in notifications" :key="notif.id"
          class="notif-page-item" :class="{ unread: !notif.read }"
          @click="handleNotif(notif)">
          <img v-if="notif.image" :src="notif.image" class="notif-page-img">
          <div v-else class="notif-page-icon" :class="notif.type">{{ getIcon(notif.type) }}</div>
          <div class="notif-page-body">
            <h4>{{ notif.title }}</h4>
            <p>{{ notif.message }}</p>
            <span class="notif-page-time">{{ notif.time }}</span>
          </div>
          <div class="unread-dot" v-if="!notif.read"></div>
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
  toastController
} from '@ionic/vue';
import { checkmarkDoneOutline } from 'ionicons/icons';

const router = useRouter();

import { notifications as mockNotifications } from '@/mock/notifications';

const notifications = ref(mockNotifications);

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const getIcon = (type) => {
  if (type === 'order') return '';
  if (type === 'promo') return '';
  return '';
};

const handleNotif = (notif) => {
  notif.read = true;
  if (notif.type === 'order') {
    router.push('/activity');
  } else if (notif.type === 'promo') {
    router.push('/browse');
  } else if (notif.type === 'system') {
    router.push('/home');
  }
};

const markAllRead = async () => {
  notifications.value.forEach(n => n.read = true);
  const toast = await toastController.create({
    message: 'All marked as read', duration: 2000, color: 'success', position: 'top'
  });
  await toast.present();
};
</script>

<style scoped>
.notif-page-content { --background: #f8f9fa; overflow-y: auto; }
.empty-state { text-align: center; padding: 80px 40px; }
.empty-icon { font-size: 64px; margin-bottom: 16px; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0; }
.notif-page-list { padding: 8px 16px; display: flex; flex-direction: column; gap: 10px; margin-top: 8px; }
.notif-page-item { display: flex; align-items: flex-start; gap: 14px; background: white; border-radius: 14px; padding: 14px; cursor: pointer; position: relative; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.notif-page-item.unread { background: #F1F8E9; border-left: 4px solid #4CAF50; }
.notif-page-icon { width: 44px; height: 44px; border-radius: 12px; background: #E8F5E9; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.notif-page-body { flex: 1; }
.notif-page-body h4 { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.notif-page-body p { font-size: 13px; color: #666; margin: 0 0 6px 0; line-height: 1.4; }
.notif-page-time { font-size: 11px; color: #999; }
.unread-dot { width: 10px; height: 10px; background: #4CAF50; border-radius: 50%; flex-shrink: 0; margin-top: 6px; }
.notif-page-img { width: 44px; height: 44px; border-radius: 12px; object-fit: cover; flex-shrink: 0; }
</style>