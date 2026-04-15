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

const notifications = ref([
  { id: 1, type: 'system', title: 'Welcome to Palengke Online!', message: 'Start ordering fresh produce from your local market.', time: 'Just now', read: false, image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=100&h=100&fit=crop' },
  { id: 2, type: 'promo', title: 'Flash Sale!', message: 'Get 20% off on all vegetables today only!', time: '1 hour ago', read: false, image: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=100&h=100&fit=crop' },
  { id: 3, type: 'order', title: 'Order Delivered!', message: 'Your order from Rosa\'s Seafoods has been successfully delivered.', time: 'Yesterday', read: true, image: 'https://images.unsplash.com/photo-1534482421-64566f976cfa?w=100&h=100&fit=crop' },
  { id: 4, type: 'promo', title: 'New Promo!', message: 'Use code PALENGKE50 for off your next order.', time: '2 days ago', read: true, image: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=100&h=100&fit=crop' },
  { id: 5, type: 'order', title: 'Order Confirmed!', message: 'Your order from Ana\'s Palengke has been confirmed.', time: '3 days ago', read: true, image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=100&h=100&fit=crop' },
]);

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const getIcon = (type) => {
  if (type === 'order') return '';
  if (type === 'promo') return '';
  return '';
};

const handleNotif = (notif) => {
  notif.read = true;
  if (notif.type === 'order') router.push('/activity');
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