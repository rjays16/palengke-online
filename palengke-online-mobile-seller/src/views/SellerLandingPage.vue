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
            <img src="/assets/images/palengke-seller-boy.png" alt="Seller" class="welcome-avatar">
          </div>
          
          <!-- Trial / Subscription Badge -->
          <div class="trial-badge" :class="{ expired, active: subActive && !subLow }" v-if="isTrialActive || subActive || expired" :style="{ cursor: (isTrialActive || expired || subLow) ? 'pointer' : 'default' }" @click="(isTrialActive || expired || subLow) ? goToPlans() : null">
            <ion-icon :icon="expired ? alertCircleOutline : subActive ? checkmarkCircleOutline : timeOutline"></ion-icon>
            <span v-if="expired">Trial expired — tap to subscribe</span>
            <span v-else-if="subActive">Plan Active: {{ remainingText }}</span>
            <span v-else>{{ remainingText }} in free trial</span>
            <ion-icon :icon="chevronForwardOutline" class="badge-arrow" v-if="isTrialActive || expired || subLow"></ion-icon>
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

    <!-- Notice Modal -->
    <ion-modal :is-open="showNoticeModal" @did-dismiss="closeNoticeModal" css-class="notice-modal">
      <ion-header>
        <ion-toolbar color="warning">
          <ion-title>Notice</ion-title>
          <ion-buttons slot="end">
            <ion-button @click="closeNoticeModal">
              <ion-icon :icon="closeOutline"></ion-icon>
            </ion-button>
          </ion-buttons>
        </ion-toolbar>
      </ion-header>
      <ion-content class="ion-padding">
        <div class="notice-modal-body" v-if="activeNotice">
          <div class="notice-modal-icon">
            <ion-icon :icon="megaphoneOutline"></ion-icon>
          </div>
          <h2 class="notice-modal-title">{{ activeNotice.title }}</h2>
          <p class="notice-modal-msg">{{ activeNotice.message }}</p>
          <ion-button expand="block" class="notice-modal-btn" @click="closeNoticeModal">Got it</ion-button>
        </div>
      </ion-content>
    </ion-modal>

    <!-- Banner Request Modal -->
    <ion-modal :is-open="showBannerModal" @did-dismiss="showBannerModal = false" css-class="banner-modal">
      <ion-header>
        <ion-toolbar>
          <ion-buttons slot="start">
            <ion-button @click="showBannerModal = false">
              <ion-icon :icon="closeOutline"></ion-icon>
            </ion-button>
          </ion-buttons>
          <ion-title>Request Promo Banner</ion-title>
        </ion-toolbar>
      </ion-header>
      <ion-content class="ion-padding">
        <div class="banner-form">
          <ion-item>
            <ion-label position="stacked">Banner Title</ion-label>
            <ion-input v-model="bannerForm.title" placeholder="e.g. Fresh Seafood Sale"></ion-input>
          </ion-item>
          <ion-item>
            <ion-label position="stacked">Subtitle</ion-label>
            <ion-input v-model="bannerForm.subtitle" placeholder="e.g. Up to 30% off all seafood"></ion-input>
          </ion-item>
          <ion-item>
            <ion-label position="stacked">Link</ion-label>
            <ion-input v-model="bannerForm.link" placeholder="e.g. /category/seafood"></ion-input>
          </ion-item>
          <ion-item>
            <ion-label position="stacked">Image URL (optional)</ion-label>
            <ion-input v-model="bannerForm.image" placeholder="/images/banner.jpg"></ion-input>
          </ion-item>

          <div class="banner-preview-box" v-if="bannerForm.title || bannerForm.subtitle">
            <p class="preview-label">Preview</p>
            <div class="preview-banner">
              <span class="preview-title">{{ bannerForm.title || 'Banner Title' }}</span>
              <span class="preview-sub">{{ bannerForm.subtitle || 'Banner subtitle' }}</span>
            </div>
          </div>

          <ion-button expand="block" class="submit-banner-btn" @click="submitBannerRequest">
            Submit Request
          </ion-button>
        </div>
      </ion-content>
    </ion-modal>

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
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { onIonViewWillEnter } from '@ionic/vue';
import { getSubscription, daysLeft, isExpired } from '@/services/subscriptionService';
import { SELLER_NOTICES } from '@/mock/notices';
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
  IonModal,
  IonInput,
  IonLabel,
  IonItem,
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
  chatbubblesOutline,
  alertCircleOutline,
  checkmarkCircleOutline,
  chevronForwardOutline,
  megaphoneOutline
} from 'ionicons/icons';

const router = useRouter();

// Seller Data
const sellerName = ref('Loading...');
const notificationCount = ref(3);
const selectedCategories = ref([]);
const availableCategories = ref([]);
const sub = ref(getSubscription())

function refreshSub() {
  sub.value = getSubscription()
}

const loadProfile = async () => {
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  try {
    const response = await fetch(`http://localhost:8000/api/seller/profile?email=${encodeURIComponent(email)}`, {
      headers: { 'Accept': 'application/json' },
    });
    const data = await response.json();
    if (response.ok && data.user) {
      const u = data.user;
      sellerName.value = u.firstName + ' ' + u.lastName;
      selectedCategories.value = u.categories || [];
    }
  } catch {
    // keep defaults
  }
};

const loadCategories = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/categories', {
      headers: { 'Accept': 'application/json' },
    });
    const data = await response.json();
    if (response.ok) {
      availableCategories.value = data || [];
    }
  } catch {
    // silent
  }
};

onIonViewWillEnter(() => {
  refreshSub();
  loadProfile();
  loadCategories();
  loadOrders();
});

// Notice banner
const NOTICE_KEY = 'palengke_notices'
const DISMISS_KEY = 'palengke_seller_dismissed_notices'
const dismissedNotices = ref(JSON.parse(localStorage.getItem(DISMISS_KEY) || '[]'))

function seedNotices() {
  const existing = JSON.parse(localStorage.getItem(NOTICE_KEY) || '[]')
  if (existing.length) return
  localStorage.setItem(NOTICE_KEY, JSON.stringify(SELLER_NOTICES))
}

seedNotices()

const activeNotice = computed(() => {
  const all = JSON.parse(localStorage.getItem(NOTICE_KEY) || '[]')
  return all.find(n => n.active && (n.target === 'all' || n.target === 'seller')) || null
})

function dismissNotice(id) {
  if (!dismissedNotices.value.includes(id)) {
    dismissedNotices.value.push(id)
    localStorage.setItem(DISMISS_KEY, JSON.stringify(dismissedNotices.value))
  }
}

const showNoticeModal = ref(false)

function checkNotice() {
  const all = JSON.parse(localStorage.getItem(NOTICE_KEY) || '[]')
  const val = all.find(n => n.active && (n.target === 'all' || n.target === 'seller')) || null
  if (val && !dismissedNotices.value.includes(val.id)) {
    showNoticeModal.value = true
  }
}

onIonViewWillEnter(checkNotice)

function closeNoticeModal() {
  if (activeNotice.value) dismissNotice(activeNotice.value.id)
  showNoticeModal.value = false
}

const remaining = computed(() => daysLeft(sub.value))
const expired = computed(() => isExpired(sub.value))
const subActive = computed(() => sub.value.status === 'active' && !expired.value)
const subLow = computed(() => subActive.value && remaining.value <= 15)
const isTrialActive = computed(() => sub.value.status === 'trial' && !expired.value)
const trialDaysLeft = computed(() => remaining.value)

const remainingText = computed(() => {
  const days = remaining.value
  const months = Math.floor(days / 30)
  const d = days % 30
  let text = ''
  if (months > 0) text += `${months} month${months > 1 ? 's' : ''}`
  if (months > 0 && d > 0) text += ' and '
  if (d > 0 || months === 0) text += `${d} day${d !== 1 ? 's' : ''}`
  return text + ' left'
})

const goToPlans = () => router.push('/plans')
const canRequestBanner = computed(() => subActive.value && sub.value.planId === '8m')

const showBannerModal = ref(false)
const bannerForm = ref({ title: '', subtitle: '', link: '', image: '' })

function openBannerRequest() {
  bannerForm.value = { title: '', subtitle: '', link: '', image: '' }
  showBannerModal.value = true
}

async function submitBannerRequest() {
  if (!bannerForm.value.title) {
    const toast = await toastController.create({
      message: 'Please enter a banner title',
      duration: 2000,
      color: 'warning',
      position: 'top',
    })
    return toast.present()
  }

  const requests = JSON.parse(localStorage.getItem('palengke_banner_requests') || '[]')
  requests.push({
    id: Date.now(),
    seller: sellerName.value,
    ...bannerForm.value,
    status: 'pending',
    submittedAt: new Date().toISOString().slice(0, 10),
  })
  localStorage.setItem('palengke_banner_requests', JSON.stringify(requests))

  showBannerModal.value = false

  const toast = await toastController.create({
    message: 'Banner request submitted! An admin will review it.',
    duration: 3000,
    color: 'success',
    position: 'top',
  })
  toast.present()
}

// Stats
const todayOrders = ref(0);
const todaySales = ref(0);
const totalProducts = ref(0);
const weekSales = ref(0);

// Recent Orders
const recentOrders = ref([]);

const loadOrders = async () => {
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  try {
    const [ordersRes, statsRes] = await Promise.all([
      fetch(`http://localhost:8000/api/seller/orders?email=${encodeURIComponent(email)}`, {
        headers: { 'Accept': 'application/json' },
      }),
      fetch(`http://localhost:8000/api/seller/dashboard/stats?email=${encodeURIComponent(email)}`, {
        headers: { 'Accept': 'application/json' },
      }),
    ]);

    const ordersData = await ordersRes.json();
    if (ordersRes.ok && ordersData.orders) {
      recentOrders.value = ordersData.orders.map(o => ({
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

    const statsData = await statsRes.json();
    if (statsRes.ok && statsData.stats) {
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
    message: 'Logged out successfully',
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
  
  setTimeout(() => {
    alert.dismiss();
  }, 1500);
};

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

// ✨ NEW: Show loading with text only (no HTML)
const showLoadingWithLogo = async (message) => {
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

const addProduct = () => {
  router.push('/products/add');
};

const openChat = () => {
  router.push('/messages');
};

// ✨ UPDATED: Share shop with loading animations
const shareShop = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Share Your Shop',
    subHeader: 'Share your shop link with customers via:',
    cssClass: 'share-shop-action-sheet',
    buttons: [
      {
        text: 'Copy Link',
        handler: async () => {
          // Copy shop URL to clipboard
          const shopUrl = `https://palengke-online.com/shop/${sellerName.value}`;
          navigator.clipboard.writeText(shopUrl);
          await showSuccessModal('Shop link copied!');
        }
      },
      {
        text: 'Share via Facebook',
        handler: async () => {
          // Show loading
          await showLoadingWithLogo('Opening Facebook...');
          // Share to Facebook
          setTimeout(() => {
            window.open('https://www.facebook.com/sharer/sharer.php', '_blank');
          }, 1500);
        }
      },
      {
        text: 'Share via Messenger',
        handler: async () => {
          // Show loading
          await showLoadingWithLogo('Opening Messenger...');
          // Share to Messenger  
          setTimeout(() => {
            window.open('https://www.messenger.com', '_blank');
          }, 1500);
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
  const inputs = availableCategories.value.map(c => ({
    type: 'checkbox',
    label: c.name,
    value: c.name,
    checked: selectedCategories.value.includes(c.name),
  }));

  if (inputs.length === 0) {
    inputs.push(
      { type: 'checkbox', label: 'Fish', value: 'Fish' },
      { type: 'checkbox', label: 'Shellfish', value: 'Shellfish' },
      { type: 'checkbox', label: 'Squid & Octopus', value: 'Squid' },
      { type: 'checkbox', label: 'Pork', value: 'Pork' },
      { type: 'checkbox', label: 'Chicken', value: 'Chicken' },
      { type: 'checkbox', label: 'Vegetables', value: 'Vegetables', checked: true },
      { type: 'checkbox', label: 'Fruits', value: 'Fruits', checked: true },
      { type: 'checkbox', label: 'Condiments', value: 'Condiments' },
      { type: 'checkbox', label: 'Dairy & Goods', value: 'Dairy' },
      { type: 'checkbox', label: 'Rice & Grains', value: 'Rice' },
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
            await fetch('http://localhost:8000/api/seller/profile', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
              body: JSON.stringify({ email, categories: data }),
            });
          } catch {
            // silent
          }
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
  cursor: pointer;
  transition: opacity 0.2s;
}
.trial-badge:active { opacity: 0.8; }

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

/* Notice Modal */
.notice-modal-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 32px 16px 16px;
  gap: 12px;
}
.notice-modal-icon {
  font-size: 48px;
  color: #F57F17;
  margin-bottom: 8px;
}
.notice-modal-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}
.notice-modal-msg {
  font-size: 14px;
  color: #666;
  line-height: 1.6;
  margin: 0 0 8px;
}
.notice-modal-btn {
  --border-radius: 12px;
  height: 48px;
  font-weight: 600;
  --background: #F57F17;
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

.banner-request-row { margin-top: 12px; }
.banner-request-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #F3E5F5;
  border: 2px dashed #CE93D8;
  border-radius: 14px;
  padding: 14px 16px;
  cursor: pointer;
  transition: opacity 0.2s;
}
.banner-request-card:active { opacity: 0.8; }
.banner-request-text { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.banner-request-title { font-size: 14px; font-weight: 700; color: #7B1FA2; }
.banner-request-sub { font-size: 12px; color: #9C27B0; opacity: 0.8; }
.banner-request-row .badge-arrow { color: #7B1FA2; }

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

.action-icon.banner {
  background: #F3E5F5;
  color: #9C27B0;
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

/* Banner Request Modal */
.banner-form { padding-top: 8px; }
.banner-form ion-item { --padding-start: 0; margin-bottom: 8px; }
.banner-form ion-label { font-weight: 600; font-size: 14px; margin-bottom: 4px; }
.banner-preview-box { margin: 20px 0; }
.preview-label { font-size: 12px; color: #999; margin-bottom: 8px; }
.preview-banner { background: linear-gradient(135deg, #4CAF50, #2E7D32); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 4px; }
.preview-title { font-size: 16px; font-weight: 700; color: #fff; }
.preview-sub { font-size: 12px; color: rgba(255,255,255,0.8); }
.submit-banner-btn { --border-radius: 12px; height: 48px; font-weight: 600; margin-top: 12px; --background: #9C27B0; }

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

/* ✨ NEW: Loading Modal Styling - Simple Version */
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