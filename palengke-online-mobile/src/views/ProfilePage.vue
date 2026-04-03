<template>
  <ion-page>
    <ion-content :fullscreen="true" class="profile-content">

      <!-- Profile Header -->
      <div class="profile-header">
        <div class="avatar-wrap" @click="changeAvatar">
          <div class="avatar">{{ profileData.name.charAt(0) }}</div>
          <div class="avatar-edit">✏️</div>
        </div>
        <h2 class="profile-name">{{ profileData.name }}</h2>
        <p class="profile-email">{{ profileData.email }}</p>
        <p class="profile-phone">{{ profileData.phone }}</p>
      </div>

      <!-- Stats Row -->
      <div class="stats-row">
        <div class="stat-item">
          <h3>{{ profileData.totalOrders }}</h3>
          <p>Orders</p>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <h3>{{ profileData.points }}</h3>
          <p>Points</p>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <h3>{{ profileData.favoriteCount }}</h3>
          <p>Favorites</p>
        </div>
      </div>

      <!-- Menu Sections -->
      <div class="menu-section">
        <h3 class="menu-title">Account</h3>
        <div class="menu-list">
          <div class="menu-item" @click="editProfile">
            <div class="menu-icon green"><ion-icon :icon="personOutline"></ion-icon></div>
            <div class="menu-label"><p>Personal Information</p></div>
            <ion-icon :icon="chevronForwardOutline" class="menu-arrow"></ion-icon>
          </div>
          <div class="menu-item" @click="goToAddresses">
            <div class="menu-icon blue"><ion-icon :icon="locationOutline"></ion-icon></div>
            <div class="menu-label"><p>Saved Addresses</p></div>
            <ion-icon :icon="chevronForwardOutline" class="menu-arrow"></ion-icon>
          </div>
          <div class="menu-item" @click="goToPayment">
            <div class="menu-icon orange"><ion-icon :icon="cardOutline"></ion-icon></div>
            <div class="menu-label"><p>Payment Methods</p></div>
            <ion-icon :icon="chevronForwardOutline" class="menu-arrow"></ion-icon>
          </div>
        </div>
      </div>

      <div class="menu-section">
        <h3 class="menu-title">Orders</h3>
        <div class="menu-list">
          <div class="menu-item" @click="goToActivity">
            <div class="menu-icon purple"><ion-icon :icon="receiptOutline"></ion-icon></div>
            <div class="menu-label"><p>My Orders</p></div>
            <ion-icon :icon="chevronForwardOutline" class="menu-arrow"></ion-icon>
          </div>
          <div class="menu-item" @click="goToFavorites">
            <div class="menu-icon red"><ion-icon :icon="heartOutline"></ion-icon></div>
            <div class="menu-label"><p>Favorite Sellers</p></div>
            <ion-icon :icon="chevronForwardOutline" class="menu-arrow"></ion-icon>
          </div>
        </div>
      </div>

      <div class="menu-section">
        <h3 class="menu-title">Support</h3>
        <div class="menu-list">
          <div class="menu-item" @click="goToNotifications">
            <div class="menu-icon blue"><ion-icon :icon="notificationsOutline"></ion-icon></div>
            <div class="menu-label"><p>Notifications</p></div>
            <ion-icon :icon="chevronForwardOutline" class="menu-arrow"></ion-icon>
          </div>
          <div class="menu-item" @click="goToMessages">
            <div class="menu-icon green"><ion-icon :icon="chatbubblesOutline"></ion-icon></div>
            <div class="menu-label"><p>Messages</p></div>
            <ion-icon :icon="chevronForwardOutline" class="menu-arrow"></ion-icon>
          </div>
          <div class="menu-item" @click="goToHelp">
            <div class="menu-icon orange"><ion-icon :icon="helpCircleOutline"></ion-icon></div>
            <div class="menu-label"><p>Help & Support</p></div>
            <ion-icon :icon="chevronForwardOutline" class="menu-arrow"></ion-icon>
          </div>
          <div class="menu-item" @click="rateApp">
            <div class="menu-icon purple"><ion-icon :icon="starOutline"></ion-icon></div>
            <div class="menu-label"><p>Rate Our App</p></div>
            <ion-icon :icon="chevronForwardOutline" class="menu-arrow"></ion-icon>
          </div>
        </div>
      </div>

      <!-- Logout -->
      <div class="logout-section">
        <ion-button expand="block" color="danger" @click="handleLogout" class="logout-btn">
          <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
          Logout
        </ion-button>
      </div>

      <div class="app-version">
        <p>Palengke Online v1.0.0</p>
        <p>© 2026 Palengke Online Inc.</p>
      </div>

      <div style="height: 80px;"></div>
    </ion-content>

    <!-- Bottom Nav -->
    <ion-footer>
      <ion-toolbar class="bottom-nav-toolbar">
        <div class="bottom-nav">
          <div class="nav-item" @click="goToHome"><ion-icon :icon="homeOutline"></ion-icon><span>Home</span></div>
          <div class="nav-item" @click="goToBrowse"><ion-icon :icon="searchOutline"></ion-icon><span>Browse</span></div>
          <div class="nav-item" @click="goToCart"><ion-icon :icon="cartOutline"></ion-icon><span>Cart</span></div>
          <div class="nav-item" @click="goToActivity"><ion-icon :icon="receiptOutline"></ion-icon><span>Activity</span></div>
          <div class="nav-item active"><ion-icon :icon="personOutline"></ion-icon><span>Profile</span></div>
        </div>
      </ion-toolbar>
    </ion-footer>

  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonContent, IonFooter, IonToolbar, IonButton, IonIcon,
  alertController, actionSheetController
} from '@ionic/vue';
import {
  personOutline, locationOutline, cardOutline, receiptOutline,
  heartOutline, notificationsOutline, chatbubblesOutline,
  helpCircleOutline, starOutline, logOutOutline, chevronForwardOutline,
  homeOutline, searchOutline, cartOutline
} from 'ionicons/icons';

const router = useRouter();

const profileData = ref({
  name: 'Maria Santos',
  email: 'maria.santos@email.com',
  phone: '+63 912 345 6789',
  totalOrders: 28,
  points: 350,
  favoriteCount: 5
});

const changeAvatar = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Change Profile Photo',
    buttons: [
      { text: 'Take Photo', handler: () => {} },
      { text: 'Choose from Gallery', handler: () => {} },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const editProfile = async () => {
  const alert = await alertController.create({
    header: 'Edit Profile',
    inputs: [
      { name: 'name', type: 'text', value: profileData.value.name, placeholder: 'Full Name' },
      { name: 'phone', type: 'tel', value: profileData.value.phone, placeholder: 'Phone Number' },
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Save', handler: (data) => {
        if (data.name) profileData.value.name = data.name;
        if (data.phone) profileData.value.phone = data.phone;
      }}
    ]
  });
  await alert.present();
};

const handleLogout = async () => {
  const alert = await alertController.create({
    header: 'Logout',
    message: 'Are you sure you want to logout?',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Logout', role: 'destructive', handler: () => {
        localStorage.removeItem('isAuthenticated');
        router.push('/login');
      }}
    ]
  });
  await alert.present();
};

const rateApp = async () => {
  const alert = await alertController.create({
    header: 'Rate Our App ⭐',
    message: 'Thank you for using Palengke Online!',
    buttons: [{ text: 'Maybe Later', role: 'cancel' }, { text: 'Rate Now ⭐', handler: () => {} }]
  });
  await alert.present();
};

const goToHome = () => router.push('/home');
const goToBrowse = () => router.push('/browse');
const goToCart = () => router.push('/cart');
const goToActivity = () => router.push('/activity');
const goToAddresses = () => router.push('/addresses');
const goToPayment = () => router.push('/payment-methods');
const goToNotifications = () => router.push('/notifications');
const goToMessages = () => router.push('/messages');
const goToHelp = () => router.push('/help');
const goToFavorites = () => {};
</script>

<style scoped>
.profile-content { --background: #f8f9fa; }

.profile-header {
  background: linear-gradient(135deg, #4CAF50, #388E3C);
  padding: 52px 20px 32px; text-align: center; color: white;
}
.avatar-wrap { position: relative; display: inline-block; margin-bottom: 14px; cursor: pointer; }
.avatar {
  width: 84px; height: 84px; border-radius: 50%;
  background: rgba(255,255,255,0.3); border: 3px solid white;
  display: flex; align-items: center; justify-content: center;
  font-size: 36px; font-weight: 700; color: white;
}
.avatar-edit {
  position: absolute; bottom: 0; right: 0;
  width: 28px; height: 28px; background: white;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; font-size: 14px;
}
.profile-name { font-size: 22px; font-weight: 700; margin: 0 0 4px 0; }
.profile-email { font-size: 14px; opacity: 0.85; margin: 0 0 2px 0; }
.profile-phone { font-size: 14px; opacity: 0.85; margin: 0; }

.stats-row {
  background: white; display: flex;
  padding: 16px 0; margin-bottom: 8px;
  border-bottom: 1px solid #f0f0f0;
}
.stat-item { flex: 1; text-align: center; }
.stat-item h3 { font-size: 22px; font-weight: 800; color: #1a1a1a; margin: 0 0 4px 0; }
.stat-item p { font-size: 12px; color: #666; margin: 0; }
.stat-divider { width: 1px; background: #f0f0f0; }

.menu-section { background: white; margin-bottom: 8px; padding: 16px 20px; }
.menu-title { font-size: 13px; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 12px 0; }
.menu-list { display: flex; flex-direction: column; }
.menu-item { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f5f5f5; cursor: pointer; }
.menu-item:last-child { border-bottom: none; }
.menu-item:active { opacity: 0.7; }
.menu-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.menu-icon.green { background: #E8F5E9; color: #4CAF50; }
.menu-icon.blue { background: #E3F2FD; color: #2196F3; }
.menu-icon.orange { background: #FFF3E0; color: #FF9800; }
.menu-icon.purple { background: #F3E5F5; color: #9C27B0; }
.menu-icon.red { background: #FFEBEE; color: #f44336; }
.menu-label { flex: 1; }
.menu-label p { font-size: 15px; font-weight: 600; color: #1a1a1a; margin: 0; }
.menu-arrow { font-size: 18px; color: #ccc; }

.logout-section { padding: 16px; }
.logout-btn { --border-radius: 12px; height: 48px; font-weight: 600; text-transform: none; }
.app-version { text-align: center; padding: 0 0 8px; }
.app-version p { font-size: 12px; color: #999; margin: 2px 0; }

.bottom-nav-toolbar { --background: white; box-shadow: 0 -2px 12px rgba(0,0,0,0.08); }
.bottom-nav { display: grid; grid-template-columns: repeat(5, 1fr); height: 60px; }
.nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; cursor: pointer; color: #999; }
.nav-item ion-icon { font-size: 22px; }
.nav-item span { font-size: 10px; font-weight: 600; }
.nav-item.active { color: #4CAF50; }
</style>