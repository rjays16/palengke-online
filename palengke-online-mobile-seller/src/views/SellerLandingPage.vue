<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Seller Dashboard</ion-title>
        <ion-buttons slot="end">
          <div class="wallet-pill" @click="goToWallet">
            <ion-icon :icon="walletOutline" class="wallet-pill-icon"></ion-icon>
            <span class="wallet-pill-balance">₱{{ walletBalance.toLocaleString() }}</span>
          </div>
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
            <img src="/assets/images/palengke-seller-boy.png" alt="Seller" class="welcome-avatar" />
          </div>

          <!-- Trial / Subscription Badge -->
          <div
            class="trial-badge"
            :class="{ expired, active: subActive && !subLow }"
            v-if="isTrialActive || subActive || expired"
            :style="{ cursor: isTrialActive || expired || subLow ? 'pointer' : 'default' }"
            @click="isTrialActive || expired || subLow ? goToPlans() : null"
          >
            <ion-icon
              :icon="expired ? alertCircleOutline : subActive ? checkmarkCircleOutline : timeOutline"
            ></ion-icon>
            <span v-if="expired">Trial expired — tap to subscribe</span>
            <span v-else-if="subActive">Plan Active: {{ remainingText }}</span>
            <span v-else>{{ remainingText }} in free trial</span>
            <ion-icon
              :icon="chevronForwardOutline"
              class="badge-arrow"
              v-if="isTrialActive || expired || subLow"
            ></ion-icon>
          </div>

          <!-- Verify Now Banner -->
          <div v-if="needsVerification" class="verify-banner" @click="goToVerification">
            <div class="verify-banner-icon">
              <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
            </div>
            <div class="verify-banner-text">
              <span class="verify-banner-title">Verify Your Account</span>
              <span class="verify-banner-sub">Complete ID verification to unlock all features</span>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="verify-banner-arrow"></ion-icon>
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
            <div class="action-card" :class="{ disabled: expired }" @click="addProduct">
              <div class="action-icon add">
                <ion-icon :icon="addCircleOutline"></ion-icon>
              </div>
              <div class="action-text">Add Product</div>
              <div v-if="expired" class="action-expired-tag">Expired</div>
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

          <div class="banner-request-row" v-if="canRequestBanner" @click="openBannerRequest">
            <div class="banner-request-card">
              <div class="action-icon banner">
                <ion-icon :icon="megaphoneOutline"></ion-icon>
              </div>
              <div class="banner-request-text">
                <span class="banner-request-title">Request Promo Banner</span>
                <span class="banner-request-sub">Get featured on the buyer app</span>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="badge-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="orders-section">
          <div class="section-header">
            <h2 class="section-title">Recent Orders</h2>
            <ion-button fill="clear" size="small" @click="viewAllOrders"> View All </ion-button>
          </div>

          <div class="order-card" v-for="order in recentOrders" :key="order.id" @click="viewOrderDetails(order.id)">
            <div class="order-header">
              <div class="order-id">Order #{{ order.id }}</div>
              <div class="order-status" :class="order.status">{{ order.status }}</div>
            </div>
            <div class="order-details">
              <div class="order-customer">
                <ion-icon :icon="personOutline"></ion-icon>
                <span>{{ (order as any).customer }}</span>
              </div>
              <div class="order-items">{{ (order as any).items }} items</div>
            </div>
            <div class="order-footer">
              <div class="order-time">{{ (order as any).time }}</div>
              <div class="order-amount">₱{{ (order as any).amount.toLocaleString() }}</div>
            </div>
          </div>

          <div class="empty-state" v-if="recentOrders.length === 0">
            <ion-icon :icon="cartOutline" class="empty-icon"></ion-icon>
            <p>No orders yet</p>
            <ion-button size="small" @click="addProduct" :disabled="expired">Add Products</ion-button>
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

    <!-- Notice Modal -->
    <NoticeModal :is-open="showNoticeModal" :notice="activeNotice" @close="closeNoticeModal" />

    <!-- Banner Request Modal -->
    <BannerRequestModal :is-open="showBannerModal" @close="showBannerModal = false" @submit="submitBannerRequest" />

    <!-- Bottom Navigation -->
    <BottomNav active="home" />
  </ion-page>
</template>

<script setup lang="ts">
import { api } from '@/utils/apiClient';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { onIonViewWillEnter } from '@ionic/vue';
import { getSubscription, daysLeft, isExpired, refreshSubscription } from '@/services/subscriptionService';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButton,
  IonButtons,
  IonIcon,
  alertController,
  actionSheetController,
  toastController,
} from '@ionic/vue';
import {
  notificationsOutline,
  settingsOutline,
  walletOutline,
  timeOutline,
  cartOutline,
  cashOutline,
  cubeOutline,
  trendingUpOutline,
  addCircleOutline,
  statsChartOutline,
  personOutline,
  addOutline,
  logOutOutline,
  shareSocialOutline,
  chatbubblesOutline,
  alertCircleOutline,
  checkmarkCircleOutline,
  chevronForwardOutline,
  megaphoneOutline,
  shieldCheckmarkOutline,
} from 'ionicons/icons';
import { type Order, type Notice, type BannerRequest } from '@/types';
import BottomNav from '@/components/BottomNav.vue';
import NoticeModal from '@/components/modals/NoticeModal.vue';
import BannerRequestModal from '@/components/modals/BannerRequestModal.vue';
import { useSuccessModal } from '@/components/modals/useSuccessModal';

const router = useRouter();
const auth = useAuthStore();
const { showSuccessModal } = useSuccessModal();

// Seller Data
const sellerName = ref('Loading...');
const notificationCount = ref(3);
const selectedCategories = ref([] as string[]);
const availableCategories = ref([] as any[]);
const sub = ref(getSubscription());
const walletBalance = ref(0);
const verificationStatus = ref('');
const verificationChoice = ref('');

async function fetchWalletBalance() {
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  try {
    const data = await api.get(`/seller/wallet/balance?email=${encodeURIComponent(email)}`);
    walletBalance.value = data.balance || 0;
  } catch {
    /* silent */
  }
}

async function refreshSub() {
  sub.value = await refreshSubscription();
}

const loadProfile = async () => {
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  try {
    const data = await api.get(`/seller/profile?email=${encodeURIComponent(email)}`);
    if (data.user) {
      const u = data.user;
      sellerName.value = u.firstName + ' ' + u.lastName;
      selectedCategories.value = u.categories || [];
      verificationStatus.value = u.verificationStatus || '';
      verificationChoice.value = u.verificationChoice || '';
    }
  } catch {
    // keep defaults
  }
};

const loadCategories = async () => {
  try {
    const data = await api.get('/categories');
    availableCategories.value = data || [];
  } catch {
    // silent
  }
};

// Notice banner
const DISMISS_KEY = 'palengke_seller_dismissed_notices';
const dismissedNotices = ref(JSON.parse(localStorage.getItem(DISMISS_KEY) || '[]'));
const allNotices = ref([] as Notice[]);
const showNoticeModal = ref(false);

const activeNotice = computed(() => {
  return allNotices.value.find((n) => n.active && (n.target === 'all' || n.target === 'seller')) || null;
});

function dismissNotice(id: any) {
  if (!dismissedNotices.value.includes(id)) {
    dismissedNotices.value.push(id);
    localStorage.setItem(DISMISS_KEY, JSON.stringify(dismissedNotices.value));
  }
}

function checkNotice() {
  const val = activeNotice.value;
  if (val && !dismissedNotices.value.includes(val.id)) {
    showNoticeModal.value = true;
  }
}

function closeNoticeModal() {
  if (activeNotice.value) dismissNotice(activeNotice.value.id);
  showNoticeModal.value = false;
}

const loadNotices = async () => {
  try {
    const data = await api.get('/notices?target=seller');
    if (data.notices) {
      allNotices.value = data.notices;
    }
  } catch {
    // silent
  }
};

onIonViewWillEnter(async () => {
  refreshSub();
  loadProfile();
  loadCategories();
  loadOrders();
  await loadNotices();
  checkNotice();
  fetchWalletBalance();
});

const remaining = computed(() => daysLeft(sub.value));
const expired = computed(() => isExpired(sub.value));
const subActive = computed(() => sub.value.status === 'active' && !expired.value);
const subLow = computed(() => subActive.value && remaining.value <= 15);
const isTrialActive = computed(() => sub.value.status === 'trial' && !expired.value);
const remainingText = computed(() => {
  const days = remaining.value;
  const months = Math.floor(days / 30);
  const d = days % 30;
  let text = '';
  if (months > 0) text += `${months} month${months > 1 ? 's' : ''}`;
  if (months > 0 && d > 0) text += ' and ';
  if (d > 0 || months === 0) text += `${d} day${d !== 1 ? 's' : ''}`;
  return text + ' left';
});

const goToPlans = () => router.push('/plans');
const goToWallet = () => router.push('/wallet');
const canRequestBanner = computed(() => subActive.value && sub.value.planId === '8m');

const needsVerification = computed(() => {
  return verificationStatus.value === 'not_verified' || verificationChoice.value === 'skip';
});

const goToVerification = () => {
  localStorage.setItem('verificationRedirect', '/home');
  router.push('/register/step5');
};

const showBannerModal = ref(false);

function openBannerRequest() {
  showBannerModal.value = true;
}

async function submitBannerRequest(form: BannerRequest) {
  if (!form.title) {
    const toast = await toastController.create({
      message: 'Please enter a banner title',
      duration: 2000,
      color: 'warning',
      position: 'top',
    });
    return toast.present();
  }

  const requests = JSON.parse(localStorage.getItem('palengke_banner_requests') || '[]');
  requests.push({
    id: Date.now(),
    seller: sellerName.value,
    ...form,
    status: 'pending',
    submittedAt: new Date().toISOString().slice(0, 10),
  });
  localStorage.setItem('palengke_banner_requests', JSON.stringify(requests));

  showBannerModal.value = false;

  const toast = await toastController.create({
    message: 'Banner request submitted! An admin will review it.',
    duration: 3000,
    color: 'success',
    position: 'top',
  });
  toast.present();
}

// Stats
const todayOrders = ref(0);
const todaySales = ref(0);
const totalProducts = ref(0);
const weekSales = ref(0);

// Recent Orders
const recentOrders = ref([] as Order[]);

const loadOrders = async () => {
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  try {
    const [ordersData, statsData] = await Promise.all([
      api.get(`/seller/orders?email=${encodeURIComponent(email)}`),
      api.get(`/seller/dashboard/stats?email=${encodeURIComponent(email)}`),
    ]);

    if (ordersData.orders) {
      recentOrders.value = ordersData.orders.map((o: any) => ({
        id: o.id,
        customer: o.customer_name,
        items: o.item_count,
        amount: o.total,
        time: o.time || 'recent',
        status: o.status,
      }));
      if (ordersData.stats) {
        todayOrders.value = ordersData.stats.today_orders;
        todaySales.value = ordersData.stats.today_sales;
      }
    }

    if (statsData.stats) {
      totalProducts.value = statsData.stats.total_products;
      weekSales.value = statsData.stats.week_sales;
    }
  } catch {
    // keep defaults
  }
};

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
        },
      },
      {
        text: 'Logout',
        icon: logOutOutline,
        role: 'destructive',
        handler: () => {
          handleLogout();
        },
      },
    ],
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
        role: 'cancel',
      },
      {
        text: 'Logout',
        role: 'destructive',
        handler: () => {
          auth.clearAuth();
          showSuccessModal({ message: 'Logged out successfully', autoDismissMs: 1500 });
          setTimeout(() => {
            router.push('/login');
          }, 1500);
        },
      },
    ],
  });
  await alert.present();
};

const addProduct = async () => {
  if (expired.value) {
    const toast = await toastController.create({
      message: 'Your plan has expired. Subscribe to a plan to add products.',
      duration: 3000,
      color: 'warning',
      position: 'top',
    });
    await toast.present();
    router.push('/plans');
    return;
  }
  router.push('/products/add');
};

const openChat = () => {
  router.push('/messages');
};

const getShopUrl = () => {
  const slug = sellerName.value
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '');
  return `https://palengke-online.com/shop/${slug}`;
};

// Share shop with actual sharing
const shareShop = async () => {
  const shopUrl = getShopUrl();
  const shareText = `Check out my shop on Palengke Online — ${sellerName.value}!`;

  const actionSheet = await actionSheetController.create({
    header: 'Share Your Shop',
    subHeader: 'Share your shop link with customers via:',
    cssClass: 'share-shop-action-sheet',
    buttons: [
      {
        text: 'Copy Link',
        handler: async () => {
          try {
            await navigator.clipboard.writeText(shopUrl);
            await showSuccessModal('Shop link copied!');
          } catch {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = shopUrl;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            await showSuccessModal('Shop link copied!');
          }
        },
      },
      {
        text: 'Share via Facebook',
        handler: async () => {
          const fbUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shopUrl)}&quote=${encodeURIComponent(shareText)}`;
          window.open(fbUrl, '_blank', 'noopener,noreferrer');
        },
      },
      {
        text: 'Share via Messenger',
        handler: async () => {
          const messengerUrl = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(shopUrl)}&app_id=&redirect_uri=${encodeURIComponent(shopUrl)}`;
          window.open(messengerUrl, '_blank', 'noopener,noreferrer');
        },
      },
      {
        text: 'More...',
        handler: async () => {
          if (navigator.share) {
            try {
              await navigator.share({
                title: sellerName.value,
                text: shareText,
                url: shopUrl,
              });
            } catch {
              // User cancelled or share failed — ignore
            }
          } else {
            // Fallback: copy link
            try {
              await navigator.clipboard.writeText(shopUrl);
              await showSuccessModal('Shop link copied!');
            } catch {
              await showToast('Copy the link manually: ' + shopUrl, 'dark');
            }
          }
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

const showToast = async (message: string, color = 'dark') => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color,
    position: 'top',
  });
  await toast.present();
};

const viewAllOrders = () => {
  router.push('/orders');
};

const viewOrderDetails = (orderId: any) => {
  router.push(`/orders/${orderId}`);
};

const manageCategories = async () => {
  const inputs = availableCategories.value.map((c: any) => ({
    type: 'checkbox' as const,
    label: c.name,
    value: c.name,
    checked: selectedCategories.value.includes(c.name),
  }));

  if (inputs.length === 0) {
    inputs.push(
      { type: 'checkbox' as const, label: 'Fish', value: 'Fish', checked: false },
      { type: 'checkbox' as const, label: 'Shellfish', value: 'Shellfish', checked: false },
      { type: 'checkbox' as const, label: 'Squid & Octopus', value: 'Squid', checked: false },
      { type: 'checkbox' as const, label: 'Pork', value: 'Pork', checked: false },
      { type: 'checkbox' as const, label: 'Chicken', value: 'Chicken', checked: false },
      { type: 'checkbox' as const, label: 'Vegetables', value: 'Vegetables', checked: true },
      { type: 'checkbox' as const, label: 'Fruits', value: 'Fruits', checked: true },
      { type: 'checkbox' as const, label: 'Condiments', value: 'Condiments', checked: false },
      { type: 'checkbox' as const, label: 'Dairy & Goods', value: 'Dairy', checked: false },
      { type: 'checkbox' as const, label: 'Rice & Grains', value: 'Rice', checked: false },
    );
  }

  const alert = await alertController.create({
    header: 'Manage Categories',
    message: 'Select the types of products you sell',
    inputs,
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Save',
        handler: async (data) => {
          selectedCategories.value = data;

          const email = localStorage.getItem('userEmail');
          if (!email) return;

          try {
            await api.post('/seller/profile', { email, categories: data });
          } catch {
            // silent
          }
        },
      },
    ],
  });
  await alert.present();
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

.wallet-pill {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.25);
  cursor: pointer;
  margin-right: 4px;
}
.wallet-pill-icon {
  font-size: 14px;
  color: white;
}
.wallet-pill-balance {
  font-size: 13px;
  font-weight: 600;
  color: white;
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
  border: 1px solid #4caf50;
}

.dashboard-container {
  padding-bottom: 80px;
}

/* Welcome Section */
.welcome-section {
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
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
  cursor: pointer;
  transition: opacity 0.2s;
}
.trial-badge:active {
  opacity: 0.8;
}

.trial-badge.expired {
  background: rgba(244, 67, 54, 0.2);
}

.trial-badge ion-icon {
  font-size: 20px;
}

.badge-arrow {
  margin-left: auto;
  font-size: 16px !important;
  opacity: 0.7;
}

/* Verify Now Banner */
.verify-banner {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #fff7ed;
  border: 1px solid #fdba74;
  padding: 12px 16px;
  border-radius: 12px;
  cursor: pointer;
  transition: opacity 0.2s;
  margin-top: 8px;
}

.verify-banner:active {
  opacity: 0.8;
}

.verify-banner-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #fed7aa;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.verify-banner-icon ion-icon {
  font-size: 20px;
  color: #ea580c;
}

.verify-banner-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.verify-banner-title {
  font-size: 14px;
  font-weight: 700;
  color: #9a3412;
}

.verify-banner-sub {
  font-size: 12px;
  color: #c2410c;
}

.verify-banner-arrow {
  font-size: 16px;
  color: #c2410c;
  opacity: 0.6;
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
  background: #e8f5e9;
  color: #4caf50;
}

.stat-card.blue .stat-icon {
  background: #e3f2fd;
  color: #2196f3;
}

.stat-card.orange .stat-icon {
  background: #fff3e0;
  color: #ff9800;
}

.stat-card.purple .stat-icon {
  background: #f3e5f5;
  color: #9c27b0;
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

.banner-request-row {
  margin-top: 12px;
}
.banner-request-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #f3e5f5;
  border: 2px dashed #ce93d8;
  border-radius: 14px;
  padding: 14px 16px;
  cursor: pointer;
  transition: opacity 0.2s;
}
.banner-request-card:active {
  opacity: 0.8;
}
.banner-request-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.banner-request-title {
  font-size: 14px;
  font-weight: 700;
  color: #7b1fa2;
}
.banner-request-sub {
  font-size: 12px;
  color: #9c27b0;
  opacity: 0.8;
}
.banner-request-row .badge-arrow {
  color: #7b1fa2;
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

.action-card.disabled {
  opacity: 0.5;
  pointer-events: none;
}

.action-expired-tag {
  font-size: 10px;
  font-weight: 700;
  color: #c62828;
  background: #ffebee;
  padding: 2px 8px;
  border-radius: 6px;
  margin-top: 4px;
  display: inline-block;
}

.action-icon.add {
  background: #e8f5e9;
  color: #4caf50;
}

.action-icon.chat {
  background: #fff3e0;
  color: #ff9800;
}

.action-icon.share {
  background: #e3f2fd;
  color: #2196f3;
}

.action-icon.banner {
  background: #f3e5f5;
  color: #9c27b0;
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
  background: #fff3e0;
  color: #ff9800;
}

.order-status.processing {
  background: #e3f2fd;
  color: #2196f3;
}

.order-status.completed {
  background: #e8f5e9;
  color: #4caf50;
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
  color: #4caf50;
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
  background: #e8f5e9;
  border-color: #4caf50;
  color: #4caf50;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
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

:deep(.settings-action-sheet button[role='destructive']) {
  color: #f44336;
  font-weight: 600;
}

:deep(.settings-action-sheet button[role='cancel']) {
  display: none;
}

/* Share Shop Action Sheet Styling */
:deep(.share-shop-action-sheet .action-sheet-wrapper) {
  max-width: 500px;
  margin: 0 auto;
}

:deep(.share-shop-action-sheet .action-sheet-group) {
  border-radius: 16px 16px 0 0;
}

:deep(.share-shop-action-sheet .action-sheet-title) {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  text-align: center;
}

:deep(.share-shop-action-sheet button.action-sheet-button) {
  font-size: 15px;
  padding: 16px;
  text-align: left !important;
  justify-content: flex-start !important;
}

:deep(.share-shop-action-sheet button.action-sheet-button .action-sheet-button-inner) {
  justify-content: flex-start !important;
}
</style>
