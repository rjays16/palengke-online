<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Seller Dashboard</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="showNotifications" class="notification-btn">
            <span v-if="notificationCount > 0" class="notification-badge">{{ notificationCount }}</span>
            <ion-icon :icon="notificationsOutline"></ion-icon>
          </ion-button>
          <ion-button @click="openSettings">
            <ion-icon :icon="settingsOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="dashboard-content">
      <div class="dashboard-container">

        <!-- Welcome Section -->
        <div class="welcome-section">
          <div class="welcome-header">
            <div>
              <h1 class="welcome-title">Welcome back!</h1>
              <p class="seller-name">{{ sellerName }}</p>
            </div>
            <img src="/assets/images/palengke-seller.png" alt="Seller" class="welcome-avatar">
          </div>
          
          <!-- Trial Badge -->
          <div class="trial-badge" v-if="isTrialActive">
            <ion-icon :icon="timeOutline"></ion-icon>
            <span>{{ trialDaysLeft }} days left in free trial</span>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-section">
          <div class="stat-card green">
            <div class="stat-icon">
              <ion-icon :icon="cartOutline"></ion-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ todayOrders }}</div>
              <div class="stat-label">Today's Orders</div>
            </div>
          </div>

          <div class="stat-card blue">
            <div class="stat-icon">
              <ion-icon :icon="cashOutline"></ion-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">₱{{ todaySales.toLocaleString() }}</div>
              <div class="stat-label">Today's Sales</div>
            </div>
          </div>

          <div class="stat-card orange">
            <div class="stat-icon">
              <ion-icon :icon="cubeOutline"></ion-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ totalProducts }}</div>
              <div class="stat-label">Total Products</div>
            </div>
          </div>

          <div class="stat-card purple">
            <div class="stat-icon">
              <ion-icon :icon="trendingUpOutline"></ion-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">₱{{ weekSales.toLocaleString() }}</div>
              <div class="stat-label">This Week</div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="actions-section">
          <h2 class="section-title">Quick Actions</h2>
          
          <div class="actions-grid">
            <div class="action-card" @click="addProduct">
              <div class="action-icon add">
                <ion-icon :icon="addCircleOutline"></ion-icon>
              </div>
              <div class="action-text">Add Product</div>
            </div>

            <div class="action-card" @click="openChat">
              <div class="action-icon chat">
                <ion-icon :icon="chatbubblesOutline"></ion-icon>
              </div>
              <div class="action-text">Messages</div>
            </div>

            <div class="action-card" @click="shareShop">
              <div class="action-icon share">
                <ion-icon :icon="shareSocialOutline"></ion-icon>
              </div>
              <div class="action-text">Share Shop</div>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="orders-section">
          <div class="section-header">
            <h2 class="section-title">Recent Orders</h2>
            <ion-button fill="clear" size="small" @click="viewAllOrders">
              View All
            </ion-button>
          </div>

          <div class="order-card" v-for="order in recentOrders" :key="order.id" @click="viewOrderDetails(order.id)">
            <div class="order-header">
              <div class="order-id">Order #{{ order.id }}</div>
              <div class="order-status" :class="order.status">{{ order.status }}</div>
            </div>
            <div class="order-details">
              <div class="order-customer">
                <ion-icon :icon="personOutline"></ion-icon>
                <span>{{ order.customer }}</span>
              </div>
              <div class="order-items">{{ order.items }} items</div>
            </div>
            <div class="order-footer">
              <div class="order-time">{{ order.time }}</div>
              <div class="order-amount">₱{{ order.amount.toLocaleString() }}</div>
            </div>
          </div>

          <div class="empty-state" v-if="recentOrders.length === 0">
            <ion-icon :icon="cartOutline" class="empty-icon"></ion-icon>
            <p>No orders yet</p>
            <ion-button size="small" @click="addProduct">Add Products</ion-button>
          </div>
        </div>

        <!-- Categories Section -->
        <div class="categories-section">
          <h2 class="section-title">Your Categories</h2>
          
          <div class="categories-chips">
            <div class="category-chip" v-for="category in selectedCategories" :key="category">
              {{ category }}
            </div>
            <div class="category-chip add" @click="manageCategories">
              <ion-icon :icon="addOutline"></ion-icon> Add More
            </div>
          </div>
        </div>

      </div>
    </ion-content>

    <!-- Bottom Navigation -->
    <ion-footer>
      <ion-toolbar>
        <div class="bottom-nav">
          <div class="nav-item active" @click="goToHome">
            <ion-icon :icon="homeOutline"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item" @click="goToProducts">
            <ion-icon :icon="cubeOutline"></ion-icon>
            <span>Products</span>
          </div>
          <div class="nav-item" @click="goToOrders">
            <ion-icon :icon="receiptOutline"></ion-icon>
            <span>Orders</span>
          </div>
          <div class="nav-item" @click="goToProfile">
            <ion-icon :icon="personOutline"></ion-icon>
            <span>Profile</span>
          </div>
        </div>
      </ion-toolbar>
    </ion-footer>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonFooter,
  IonButton,
  IonButtons,
  IonIcon,
  IonBadge,
  alertController,
  actionSheetController,
  toastController
} from '@ionic/vue';
import {
  notificationsOutline,
  settingsOutline,
  timeOutline,
  cartOutline,
  cashOutline,
  cubeOutline,
  trendingUpOutline,
  addCircleOutline,
  receiptOutline,
  statsChartOutline,
  personOutline,
  addOutline,
  homeOutline,
  logOutOutline,
  helpCircleOutline,
  informationCircleOutline,
  closeOutline,
  shareSocialOutline,
  chatbubblesOutline
} from 'ionicons/icons';

const router = useRouter();

// Seller Data
const sellerName = ref('Juan Dela Cruz');
const notificationCount = ref(3);
const isTrialActive = ref(true);
const trialDaysLeft = ref(25);

// Stats
const todayOrders = ref(12);
const todaySales = ref(3450);
const totalProducts = ref(28);
const weekSales = ref(18750);

// Categories
const selectedCategories = ref(['Fish', 'Vegetables', 'Fruits']);

// Recent Orders
const recentOrders = ref([
  {
    id: '1234',
    customer: 'Maria Santos',
    items: 3,
    amount: 450,
    time: '10 mins ago',
    status: 'pending'
  },
  {
    id: '1233',
    customer: 'Pedro Cruz',
    items: 5,
    amount: 890,
    time: '1 hour ago',
    status: 'processing'
  },
  {
    id: '1232',
    customer: 'Ana Lopez',
    items: 2,
    amount: 320,
    time: '2 hours ago',
    status: 'completed'
  }
]);

// Methods
const showNotifications = () => {
  router.push('/notifications');
};

const openSettings = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Settings',
    cssClass: 'settings-action-sheet',
    buttons: [
      {
        text: 'Analytics',
        icon: statsChartOutline,
        handler: () => {
          router.push('/analytics');
        }
      },
      {
        text: 'Account Settings',
        icon: personOutline,
        handler: () => {
          console.log('Account Settings');
          // TODO: Navigate to account settings
        }
      },
      {
        text: 'Notification Settings',
        icon: notificationsOutline,
        handler: () => {
          console.log('Notification Settings');
          // TODO: Navigate to notification settings
        }
      },
      {
        text: 'Help & Support',
        icon: helpCircleOutline,
        handler: () => {
          router.push('/help');
        }
      },
      {
        text: 'About',
        icon: informationCircleOutline,
        handler: () => {
          router.push('/about');
        }
      },
      {
        text: 'Logout',
        icon: logOutOutline,
        role: 'destructive',
        handler: () => {
          handleLogout();
        }
      }
    ]
  });
  
  await actionSheet.present();
};

const handleLogout = async () => {
  const alert = await alertController.create({
    header: 'Logout',
    message: 'Are you sure you want to logout?',
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Logout',
        role: 'destructive',
        handler: () => {
          localStorage.removeItem('isAuthenticated');
          showSuccessLogout();
          setTimeout(() => {
            router.push('/login');
          }, 1500);
        }
      }
    ]
  });
  await alert.present();
};

const showSuccessLogout = async () => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: '✅ Logged out successfully',
    buttons: ['OK']
  });
  
  await alert.present();
  
  setTimeout(() => {
    alert.dismiss();
  }, 1500);
};

const addProduct = () => {
  router.push('/products/add');
};

const openChat = () => {
  router.push('/messages');
};

const shareShop = async () => {
  const alert = await alertController.create({
    header: 'Share Your Shop',
    message: 'Share your shop link with customers via:',
    buttons: [
      {
        text: 'Copy Link',
        handler: () => {
          // TODO: Copy shop URL to clipboard
          const shopUrl = `https://palengke-online.com/shop/${sellerName.value}`;
          navigator.clipboard.writeText(shopUrl);
          showToast('✅ Shop link copied!', 'success');
        }
      },
      {
        text: 'Share via Facebook',
        handler: () => {
          // TODO: Share to Facebook
          showToast('Opening Facebook...', 'primary');
        }
      },
      {
        text: 'Share via Messenger',
        handler: () => {
          // TODO: Share to Messenger
          showToast('Opening Messenger...', 'primary');
        }
      },
      {
        text: 'Cancel',
        role: 'cancel'
      }
    ]
  });
  await alert.present();
};

const showToast = async (message, color = 'dark') => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color,
    position: 'top'
  });
  await toast.present();
};

const viewAnalytics = () => {
  router.push('/analytics');
};

const viewAllOrders = () => {
  router.push('/orders');
};

const viewOrderDetails = (orderId) => {
  router.push(`/orders/${orderId}`);
};

const manageCategories = async () => {
  const alert = await alertController.create({
    header: 'Manage Categories',
    message: 'Select the types of products you sell',
    inputs: [
      { type: 'checkbox', label: 'Fish', value: 'Fish', checked: true },
      { type: 'checkbox', label: 'Shellfish', value: 'Shellfish' },
      { type: 'checkbox', label: 'Squid & Octopus', value: 'Squid' },
      { type: 'checkbox', label: 'Pork', value: 'Pork' },
      { type: 'checkbox', label: 'Chicken', value: 'Chicken' },
      { type: 'checkbox', label: 'Vegetables', value: 'Vegetables', checked: true },
      { type: 'checkbox', label: 'Fruits', value: 'Fruits', checked: true },
      { type: 'checkbox', label: 'Condiments', value: 'Condiments' },
      { type: 'checkbox', label: 'Dairy & Goods', value: 'Dairy' },
      { type: 'checkbox', label: 'Rice & Grains', value: 'Rice' }
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Save',
        handler: (data) => {
          selectedCategories.value = data;
        }
      }
    ]
  });
  await alert.present();
};

// Bottom Nav
const goToHome = () => {
  // Already on home
};

const goToProducts = () => {
  router.push('/products');
};

const goToOrders = () => {
  router.push('/orders');
};

const goToProfile = () => {
  router.push('/profile');
};
</script>

<style scoped>
.dashboard-content {
  --background: #f8f9fa;
}

/* Fix header button spacing */
ion-header ion-toolbar ion-buttons {
  gap: 0 !important;
}

ion-header ion-toolbar ion-buttons ion-button {
  margin: 0 !important;
  --padding-start: 8px;
  --padding-end: 8px;
}

.notification-btn {
  position: relative;
}

.notification-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  background: #f44336;
  color: white;
  border-radius: 50%;
  min-width: 12px;
  height: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 8px;
  font-weight: 700;
  padding: 0px;
  z-index: 10;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
  border: 1px solid #4CAF50;
}

.dashboard-container {
  padding-bottom: 80px;
}

/* Welcome Section */
.welcome-section {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  padding: 24px 20px;
  color: white;
}

.welcome-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.welcome-title {
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 4px 0;
}

.seller-name {
  font-size: 16px;
  margin: 0;
  opacity: 0.95;
}

.welcome-avatar {
  width: 80px;
  height: auto;
}

.trial-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.2);
  padding: 10px 16px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
}

.trial-badge ion-icon {
  font-size: 20px;
}

/* Stats Section */
.stats-section {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  padding: 20px;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.stat-card.green .stat-icon {
  background: #E8F5E9;
  color: #4CAF50;
}

.stat-card.blue .stat-icon {
  background: #E3F2FD;
  color: #2196F3;
}

.stat-card.orange .stat-icon {
  background: #FFF3E0;
  color: #FF9800;
}

.stat-card.purple .stat-icon {
  background: #F3E5F5;
  color: #9C27B0;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 2px;
}

.stat-label {
  font-size: 12px;
  color: #666;
}

/* Actions Section */
.actions-section {
  padding: 0 20px 20px 20px;
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.action-card {
  text-align: center;
  cursor: pointer;
}

.action-icon {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 8px auto;
  font-size: 28px;
  transition: transform 0.2s;
}

.action-card:active .action-icon {
  transform: scale(0.95);
}

.action-icon.add {
  background: #E8F5E9;
  color: #4CAF50;
}

.action-icon.chat {
  background: #FFF3E0;
  color: #FF9800;
}

.action-icon.share {
  background: #E3F2FD;
  color: #2196F3;
}

.action-text {
  font-size: 12px;
  color: #333;
  font-weight: 500;
}

/* Orders Section */
.orders-section {
  padding: 0 20px 20px 20px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.order-card {
  background: white;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s;
}

.order-card:active {
  transform: scale(0.98);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.order-id {
  font-size: 14px;
  font-weight: 700;
  color: #1a1a1a;
}

.order-status {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.order-status.pending {
  background: #FFF3E0;
  color: #FF9800;
}

.order-status.processing {
  background: #E3F2FD;
  color: #2196F3;
}

.order-status.completed {
  background: #E8F5E9;
  color: #4CAF50;
}

.order-details {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 13px;
  color: #666;
}

.order-customer {
  display: flex;
  align-items: center;
  gap: 6px;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid #f0f0f0;
}

.order-time {
  font-size: 12px;
  color: #999;
}

.order-amount {
  font-size: 16px;
  font-weight: 700;
  color: #4CAF50;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
}

.empty-icon {
  font-size: 64px;
  color: #ccc;
  margin-bottom: 16px;
}

.empty-state p {
  color: #666;
  margin-bottom: 16px;
}

/* Categories Section */
.categories-section {
  padding: 0 20px 20px 20px;
}

.categories-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.category-chip {
  background: white;
  border: 2px solid #e0e0e0;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  color: #333;
}

.category-chip.add {
  background: #E8F5E9;
  border-color: #4CAF50;
  color: #4CAF50;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Bottom Navigation */
.bottom-nav {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  height: 60px;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  cursor: pointer;
  color: #999;
  transition: color 0.2s;
}

.nav-item ion-icon {
  font-size: 24px;
}

.nav-item span {
  font-size: 11px;
  font-weight: 500;
}

.nav-item.active {
  color: #4CAF50;
}

.nav-item:active {
  transform: scale(0.95);
}

/* Responsive */
@media (max-width: 360px) {
  .stats-section {
    gap: 8px;
  }
  
  .stat-value {
    font-size: 18px;
  }
  
  .actions-grid {
    gap: 8px;
  }
  
  .action-icon {
    width: 50px;
    height: 50px;
    font-size: 24px;
  }
}

/* Success Modal Styling */
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

/* Settings Action Sheet Styling */
:deep(.settings-action-sheet .action-sheet-wrapper) {
  max-width: 500px;
  margin: 0 auto;
}

:deep(.settings-action-sheet .action-sheet-group) {
  border-radius: 16px 16px 0 0;
}

:deep(.settings-action-sheet .action-sheet-title) {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  position: relative;
  padding-right: 48px;
}

:deep(.settings-action-sheet button.action-sheet-button) {
  font-size: 15px;
  padding: 16px;
}

:deep(.settings-action-sheet button[role="destructive"]) {
  color: #f44336;
  font-weight: 600;
}

:deep(.settings-action-sheet button[role="cancel"]) {
  display: none;
}
</style>