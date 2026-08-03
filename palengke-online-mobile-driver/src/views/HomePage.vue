<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Driver Dashboard</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="openSettings">
            <ion-icon :icon="settingsOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="dashboard-content">
      <div class="dashboard-container">

        <!-- Welcome + Active/Inactive Section -->
        <div class="welcome-section">
          <div class="welcome-header">
            <div>
              <h1 class="welcome-title">Welcome back!</h1>
              <p class="driver-name">{{ driverName }}</p>
              <div class="rating-row">
                <ion-icon :icon="starOutline" class="star-icon"></ion-icon>
                <span class="rating-value">{{ driverRating }}</span>
                <span class="rating-label">({{ totalTrips }} trips)</span>
              </div>
            </div>
            <img src="/assets/images/palengke-driver.png" alt="Driver" class="welcome-avatar">
          </div>

          <!-- Active/Inactive Toggle -->
          <div class="online-toggle-card" :class="{ online: isActive, offline: !isActive }">
            <div class="toggle-info">
              <div class="toggle-status-dot" :class="{ online: isActive }"></div>
              <div>
                <p class="toggle-status-text">{{ isActive ? "You're Active" : "You're Inactive" }}</p>
                <p class="toggle-status-sub">{{ isActive ? 'Finding orders near you...' : 'Go active to receive orders' }}</p>
              </div>
            </div>
            <button class="toggle-btn" :class="{ online: isActive }" @click="toggleActiveStatus">
              <ion-icon :icon="powerOutline"></ion-icon>
              <span>{{ isActive ? 'Go Inactive' : 'Go Active' }}</span>
            </button>
          </div>
        </div>

        <!-- Palengke Licence Banner -->
        <div class="licence-banner" :class="licenceBannerClass" v-if="licenceStore.showLicenceFeature.value && !licenceStore.state.isPaid && !licenceStore.state.isFree">
          <div class="licence-banner-left">
            <ion-icon :icon="licenceBannerIcon" class="licence-banner-icon"></ion-icon>
            <div>
              <p class="licence-banner-title">{{ licenceBannerTitle }}</p>
              <p class="licence-banner-sub">{{ licenceBannerSub }}</p>
            </div>
          </div>
          <ion-button
            v-if="licenceStore.trialExpired.value"
            size="small"
            color="danger"
            fill="solid"
            class="licence-banner-btn"
            @click="goToLicencePayment"
          >
            Pay Now
          </ion-button>
        </div>

        <!-- Stats Cards -->
        <div class="stats-section">
          <div class="stat-card green">
            <div class="stat-icon">
              <ion-icon :icon="cashOutline"></ion-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">₱{{ todayEarnings.toLocaleString() }}</div>
              <div class="stat-label">Today's Earnings</div>
            </div>
          </div>

          <div class="stat-card blue">
            <div class="stat-icon">
              <ion-icon :icon="bicycleOutline"></ion-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ todayTrips }}</div>
              <div class="stat-label">Today's Trips</div>
            </div>
          </div>

          <div class="stat-card orange">
            <div class="stat-icon">
              <ion-icon :icon="timeOutline"></ion-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ activeHours }}h</div>
              <div class="stat-label">Active Hours</div>
            </div>
          </div>

          <div class="stat-card purple">
            <div class="stat-icon">
              <ion-icon :icon="trendingUpOutline"></ion-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">₱{{ weekEarnings.toLocaleString() }}</div>
              <div class="stat-label">This Week</div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="actions-section">
          <h2 class="section-title">Quick Actions</h2>
          <div class="actions-grid">
            <div class="action-card" @click="goToMessages">
              <div class="action-icon orange">
                <ion-icon :icon="chatbubblesOutline"></ion-icon>
              </div>
              <div class="action-text">Messages</div>
              <span class="action-badge" v-if="unreadMessages > 0">{{ unreadMessages }}</span>
            </div>

            <div class="action-card" @click="goToHistory">
              <div class="action-icon purple">
                <ion-icon :icon="receiptOutline"></ion-icon>
              </div>
              <div class="action-text">History</div>
            </div>

            <div class="action-card" @click="showNotifications">
              <div class="action-icon red">
                <ion-icon :icon="notificationsOutline"></ion-icon>
              </div>
              <div class="action-text">Notifications</div>
              <span class="action-badge" v-if="notificationCount > 0">{{ notificationCount }}</span>
            </div>

            <div class="action-card" @click="goToHelp">
              <div class="action-icon teal">
                <ion-icon :icon="helpCircleOutline"></ion-icon>
              </div>
              <div class="action-text">Help & Support</div>
            </div>
          </div>
        </div>

        <!-- Active Delivery (shows when driver has active order) -->
        <div class="active-delivery-section" v-if="activeDelivery">
          <div class="section-header">
            <h2 class="section-title">Active Delivery</h2>
            <span class="live-badge">● LIVE</span>
          </div>
          <div class="active-delivery-card" @click="goToActiveDelivery">
            <div class="delivery-status-bar">
              <div class="delivery-step" :class="{ active: activeDelivery.status === 'pickup', done: activeDelivery.status !== 'pickup' }">
                <ion-icon :icon="storefrontOutline"></ion-icon>
                <span>Pickup</span>
              </div>
              <div class="delivery-line" :class="{ done: activeDelivery.status === 'delivering' || activeDelivery.status === 'delivered' }"></div>
              <div class="delivery-step" :class="{ active: activeDelivery.status === 'delivering', done: activeDelivery.status === 'delivered' }">
                <ion-icon :icon="bicycleOutline"></ion-icon>
                <span>Delivering</span>
              </div>
              <div class="delivery-line" :class="{ done: activeDelivery.status === 'delivered' }"></div>
              <div class="delivery-step" :class="{ active: activeDelivery.status === 'delivered' }">
                <ion-icon :icon="homeOutline"></ion-icon>
                <span>Delivered</span>
              </div>
            </div>
            <div class="delivery-info">
              <div class="delivery-detail">
                <ion-icon :icon="personOutline"></ion-icon>
                <span>{{ activeDelivery.customerName }}</span>
              </div>
              <div class="delivery-detail">
                <ion-icon :icon="locationOutline"></ion-icon>
                <span>{{ activeDelivery.address }}</span>
              </div>
              <div class="delivery-footer">
                <span class="delivery-amount">₱{{ activeDelivery.amount.toLocaleString() }}</span>
                <ion-button size="small" color="success" @click.stop="goToActiveDelivery">
                  View Map
                  <ion-icon slot="end" :icon="navigateOutline"></ion-icon>
                </ion-button>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Deliveries -->
        <div class="recent-section">
          <div class="section-header">
            <h2 class="section-title">Recent Deliveries</h2>
            <ion-button fill="clear" size="small" @click="goToHistory">View All</ion-button>
          </div>

          <div class="delivery-card" v-for="delivery in recentDeliveries" :key="delivery.id"
            @click="viewDeliveryDetails(delivery)">
            <div class="delivery-header">
              <div class="delivery-id">Order #{{ delivery.id }}</div>
              <div class="delivery-status" :class="delivery.status">{{ delivery.status }}</div>
            </div>
            <div class="delivery-details">
              <div class="delivery-customer">
                <ion-icon :icon="personOutline"></ion-icon>
                <span>{{ delivery.customerName }}</span>
              </div>
              <div class="delivery-location">
                <ion-icon :icon="locationOutline"></ion-icon>
                <span>{{ delivery.address }}</span>
              </div>
            </div>
            <div class="delivery-footer-row">
              <div class="delivery-time">{{ delivery.time }}</div>
              <div class="delivery-earn">
                <span class="earn-amount">₱{{ delivery.amount.toLocaleString() }}</span>
                <span class="tip-badge" v-if="delivery.tipped"><font-awesome-icon :icon="['fas', 'money-bill']" /> Tipped</span>
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div class="empty-state" v-if="recentDeliveries.length === 0">
            <ion-icon :icon="bicycleOutline" class="empty-icon"></ion-icon>
            <p>No deliveries yet</p>
            <ion-button size="small" @click="toggleActiveStatus" v-if="!isActive">
              Go Active to Start
            </ion-button>
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
          <div class="nav-item" @click="goToOrders">
            <ion-icon :icon="cubeOutline"></ion-icon>
            <span>Orders</span>
            <span class="nav-badge" v-if="availableOrders > 0">{{ availableOrders }}</span>
          </div>
          <div class="nav-item" @click="goToEarnings">
            <ion-icon :icon="cashOutline"></ion-icon>
            <span>Earnings</span>
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
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonFooter, IonButton, IonButtons, IonIcon,
  alertController, actionSheetController, toastController
} from '@ionic/vue';
import {
  notificationsOutline, settingsOutline, starOutline,
  powerOutline, cashOutline, bicycleOutline, timeOutline,
  trendingUpOutline, cubeOutline, chatbubblesOutline,
  receiptOutline, homeOutline, personOutline, locationOutline,
  navigateOutline, storefrontOutline, logOutOutline,
  helpCircleOutline, informationCircleOutline,
  closeCircleOutline, cardOutline
} from 'ionicons/icons';
import { useLicenceStore } from '@/composables/useLicenceStore';


const router = useRouter();
const licenceStore = useLicenceStore();
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';
let activeDeliveryInterval = null;
let notificationCountInterval = null;

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('token');
}

onMounted(async () => {
  await licenceStore.checkLicenceEnabled();
  licenceStore.fetchLicence();
  fetchRecentDeliveries();
  fetchActiveDelivery();
  fetchDashboardStats();
  fetchNotificationCount();
  activeDeliveryInterval = setInterval(fetchActiveDelivery, 30000);
  notificationCountInterval = setInterval(fetchNotificationCount, 15000);
});

onUnmounted(() => {
  if (activeDeliveryInterval) clearInterval(activeDeliveryInterval);
  if (notificationCountInterval) clearInterval(notificationCountInterval);
});

// Driver Data
const licenceBannerClass = computed(() => {
  if (licenceStore.trialExpired.value) return 'expired'
  if (licenceStore.trialDaysRemaining.value <= 5) return 'warning'
  return ''
})

const licenceBannerIcon = computed(() => {
  if (licenceStore.trialExpired.value) return closeCircleOutline
  return timeOutline
})

const licenceBannerTitle = computed(() => {
  if (licenceStore.trialExpired.value) return 'Trial Expired'
  return `${licenceStore.trialDaysRemaining.value} days trial remaining`
})

const licenceBannerSub = computed(() => {
  if (licenceStore.trialExpired.value) return `Pay ₱${licenceStore.state.amount.toLocaleString()} to continue delivering`
  return `Pay ₱${licenceStore.state.amount.toLocaleString()} after trial to keep delivering`
})

const goToLicencePayment = () => router.push('/licence-payment')

const driverName = ref('');
const driverRating = ref(0);
const totalTrips = ref(0);
const notificationCount = ref(0);
const unreadMessages = ref(0);
const availableOrders = ref(0);

// Active Status
const isActive = ref(false);

// Stats
const todayEarnings = ref(0);
const todayTrips = ref(0);
const activeHours = ref(0);
const weekEarnings = ref(0);

// Active Delivery (null if no active delivery)
const activeDelivery = ref(null);

const fetchActiveDelivery = async () => {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/orders`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      availableOrders.value = (data.available || []).length;
      const activeOrders = data.active || [];
      if (activeOrders.length > 0) {
        const order = activeOrders[0];
        activeDelivery.value = {
          id: order.id,
          customerName: order.customerName,
          address: order.deliveryAddress,
          amount: order.totalAmount,
          status: 'delivering',
        };
      } else {
        activeDelivery.value = null;
      }
    }
  } catch {}
};

const fetchDashboardStats = async () => {
  const token = getToken();
  if (!token) return;
  try {
    const [earningsRes, profileRes] = await Promise.all([
      fetch(`${API_BASE}/api/driver/earnings`, {
        headers: { Authorization: `Bearer ${token}` },
      }),
      fetch(`${API_BASE}/api/driver/profile`, {
        headers: { Authorization: `Bearer ${token}` },
      }),
    ]);
    if (earningsRes.ok) {
      const data = await earningsRes.json();
      todayEarnings.value = data.today?.netEarnings || 0;
      todayTrips.value = data.today?.jobs || 0;
      activeHours.value = data.today?.onlineHours || 0;
      weekEarnings.value = data.week?.netEarnings || 0;
    }
    if (profileRes.ok) {
      const data = await profileRes.json();
      driverName.value = data.name || '';
      driverRating.value = data.rating || 0;
      totalTrips.value = data.totalTrips || 0;
      isActive.value = data.availabilityStatus === 'active';
    }
  } catch {}
};

// Recent Deliveries
const recentDeliveries = ref([]);

const fetchRecentDeliveries = async () => {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/recent-deliveries`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      recentDeliveries.value = data.deliveries || [];
    }
  } catch {}
};

const fetchNotificationCount = async () => {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/notifications/unread-count`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      notificationCount.value = data.unread_count || 0;
    }
  } catch {}
};

// Toggle Active/Inactive
const toggleActiveStatus = async () => {
  // Check licence before going active
  if (!isActive.value && !licenceStore.canDeliver.value) {
    const alert = await alertController.create({
      cssClass: 'alert-licence',
      header: ' ',
      message: '',
      buttons: [
        {
          text: 'Pay Now',
          cssClass: 'alert-button-primary',
          handler: () => router.push('/licence-payment')
        },
        { text: 'Later', role: 'cancel', cssClass: 'alert-button-cancel' }
      ]
    });
    await alert.present();
    const alertEl = document.querySelector('.alert-licence');
    if (alertEl) {
      const msgEl = alertEl.querySelector('.alert-message');
      if (msgEl) {
        msgEl.innerHTML = `
          <div class="custom-alert-body">
            <div class="custom-alert-icon icon-error">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
            </div>
            <div class="custom-alert-title">Licence Expired</div>
            <div class="custom-alert-text">Your 30-day trial has ended. Pay ₱${licenceStore.state.amount.toLocaleString()} to continue delivering.</div>
          </div>`;
      }
    }
    return;
  }

  if (isActive.value) {
    // Going inactive - confirm
    const alert = await alertController.create({
      header: 'Go Inactive?',
      message: 'You will stop receiving new orders.',
      buttons: [
        { text: 'Cancel', role: 'cancel' },
        {
          text: 'Go Inactive',
          role: 'destructive',
          handler: async () => {
            isActive.value = false;
            const token = getToken();
            if (token) {
              try {
                await fetch(`${API_BASE}/api/driver/status`, {
                  method: 'PUT',
                  headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'application/json',
                  },
                  body: JSON.stringify({ status: 'offline' }),
                });
              } catch {
                // status update failure shouldn't block going inactive
              }
            }
            const toast = await toastController.create({
              message: 'You are now inactive',
              duration: 2000,
              color: 'medium',
              position: 'top'
            });
            await toast.present();
          }
        }
      ]
    });
    await alert.present();
  } else {
    // Going active
    isActive.value = true;
    const token = getToken();
    if (token) {
      try {
        await fetch(`${API_BASE}/api/driver/status`, {
          method: 'PUT',
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ status: 'active' }),
        });
      } catch {
        // status update failure shouldn't block going active
      }
    }
    const toast = await toastController.create({
      message: 'You are now active! Finding orders...',
      duration: 2500,
      color: 'medium',
      position: 'top'
    });
    await toast.present();
  }
};

// Settings
const openSettings = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Settings',
    buttons: [
      {
        text: 'About',
        icon: informationCircleOutline,
        handler: () => router.push('/about')
      },
      {
        text: 'Logout',
        icon: logOutOutline,
        role: 'destructive',
        handler: () => handleLogout()
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
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Logout',
        role: 'destructive',
        handler: () => {
          localStorage.removeItem('isAuthenticated');
          localStorage.removeItem('token');
          localStorage.removeItem('pendingAuth');
          router.push('/login');
        }
      }
    ]
  });
  await alert.present();
};

// Navigation
const showNotifications = () => router.push('/notifications');
const goToHome = () => {};
const goToOrders = () => router.push('/orders');
const goToEarnings = () => router.push('/earnings');
const goToMessages = () => router.push('/messages');
const goToHistory = () => router.push('/history');
const goToHelp = () => router.push('/help');
const goToProfile = () => router.push('/profile');
const goToActiveDelivery = () => router.push('/active-delivery');
const viewDeliveryDetails = (delivery) => {
  const jobData = encodeURIComponent(JSON.stringify(delivery));
  router.push(`/history/${jobData}`);
};
</script>

<style scoped>
.dashboard-content { 
  --background: var(--color-bg-secondary); 
}

/* Fix header buttons */
ion-header ion-toolbar ion-buttons ion-button {
  margin: 0 !important;
  --padding-start: 8px;
  --padding-end: 8px;
}

.notification-btn { position: relative; }
.notification-badge {
  position: absolute; top: -2px; right: -2px;
  background: #f44336; color: white; border-radius: 50%;
  min-width: 12px; height: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 8px; font-weight: 700; z-index: 10;
  box-shadow: 0 1px 2px rgba(0,0,0,0.3);
  border: 1px solid #4CAF50;
}

.dashboard-container { padding-bottom: 80px; }

/* ─── Welcome Section ────────────────────────── */
.welcome-section {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  padding: 20px 20px 24px;
  color: white;
}

.welcome-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.welcome-title { font-size: 22px; font-weight: 700; margin: 0 0 4px 0; }
.driver-name { font-size: 15px; margin: 0 0 6px 0; opacity: 0.95; }

.rating-row {
  display: flex; align-items: center; gap: 4px;
}
.star-icon { font-size: 16px; color: #FFD700; }
.rating-value { font-size: 15px; font-weight: 700; }
.rating-label { font-size: 12px; opacity: 0.8; }

.welcome-avatar { width: 90px; height: auto; }

/* Active/Inactive Toggle */
.online-toggle-card {
  background: rgba(0,0,0,0.2);
  border-radius: 16px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  border: 2px solid rgba(255,255,255,0.2);
  transition: all 0.3s;
}

.online-toggle-card.online {
  background: rgba(76, 175, 80, 0.3);
  border-color: rgba(76, 175, 80, 0.6);
}

.toggle-info { display: flex; align-items: center; gap: 12px; }

.toggle-status-dot {
  width: 12px; height: 12px;
  border-radius: 50%;
  background: #f44336;
  flex-shrink: 0;
  box-shadow: 0 0 0 3px rgba(244,67,54,0.3);
}

.toggle-status-dot.online {
  background: #4CAF50;
  box-shadow: 0 0 0 3px rgba(76,175,80,0.4);
  animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
  0%, 100% { box-shadow: 0 0 0 3px rgba(76,175,80,0.4); }
  50% { box-shadow: 0 0 0 8px rgba(76,175,80,0.15); }
}

.toggle-status-text { font-size: 15px; font-weight: 700; color: white; margin: 0 0 2px 0; }
.toggle-status-sub { font-size: 12px; color: rgba(255,255,255,0.8); margin: 0; }

.toggle-btn {
  display: flex; flex-direction: column;
  align-items: center; gap: 4px;
  padding: 12px 16px;
  background: rgba(0,0,0,0.25);
  border: 2px solid rgba(255,255,255,0.3);
  border-radius: 14px;
  color: white;
  cursor: pointer;
  font-family: inherit;
  font-size: 12px;
  font-weight: 700;
  transition: all 0.3s;
  flex-shrink: 0;
}

.toggle-btn ion-icon { font-size: 22px; }

.toggle-btn.online {
  background: rgba(244,67,54,0.3);
  border-color: rgba(244,67,54,0.5);
}

.toggle-btn:active { transform: scale(0.95); }

/* ─── Stats Section ──────────────────────────── */
.stats-section {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  padding: 20px;
}

.stat-card {
  background: var(--color-card-bg);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 2px 8px var(--color-card-shadow);
}

.stat-icon {
  width: 48px; height: 48px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; flex-shrink: 0;
}

.stat-card.green .stat-icon { background: var(--color-success-bg); color: var(--color-success); }
.stat-card.blue .stat-icon { background: var(--color-info-bg); color: var(--color-info); }
.stat-card.orange .stat-icon { background: var(--color-warning-bg); color: var(--color-warning); }
.stat-card.purple .stat-icon { background: #F3E5F5; color: #9C27B0; }

.stat-value { font-size: 18px; font-weight: 700; color: var(--color-text-primary); margin-bottom: 2px; }
.stat-label { font-size: 12px; color: var(--color-text-secondary); }

/* ─── Actions Section ────────────────────────── */
.actions-section { padding: 0 20px 20px; }
.section-title { font-size: 18px; font-weight: 700; color: var(--color-text-primary); margin: 0 0 16px 0; }

.actions-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.action-card {
  background: var(--color-card-bg);
  border-radius: 16px;
  padding: 20px 16px;
  text-align: center;
  cursor: pointer;
  position: relative;
  box-shadow: 0 2px 8px var(--color-card-shadow);
  transition: transform 0.2s;
}

.action-card:active { transform: scale(0.97); }

.action-icon {
  width: 52px; height: 52px;
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 10px;
  font-size: 24px;
}

.action-icon.green { background: var(--color-success-bg); color: var(--color-success); }
.action-icon.blue { background: var(--color-info-bg); color: var(--color-info); }
.action-icon.orange { background: var(--color-warning-bg); color: var(--color-warning); }
.action-icon.purple { background: #F3E5F5; color: #9C27B0; }
.action-icon.red { background: #FFEBEE; color: #f44336; }
.action-icon.teal { background: #E0F2F1; color: #009688; }

.action-text { font-size: 13px; color: var(--color-text-primary); font-weight: 600; line-height: 1.3; }

.action-badge {
  position: absolute; top: -4px; right: 4px;
  background: #f44336; color: white;
  border-radius: 10px; padding: 2px 6px;
  font-size: 10px; font-weight: 700;
}

/* ─── Active Delivery Section ────────────────── */
.active-delivery-section { padding: 0 20px 20px; }

.section-header {
  display: flex; justify-content: space-between;
  align-items: center; margin-bottom: 16px;
}

.live-badge {
  background: #f44336; color: white;
  padding: 4px 10px; border-radius: 12px;
  font-size: 11px; font-weight: 700;
  animation: blink 1.5s infinite;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.6; }
}

.active-delivery-card {
  background: var(--color-card-bg);
  border-radius: 16px;
  padding: 16px;
  box-shadow: 0 2px 12px var(--color-card-shadow);
  border: 2px solid var(--color-primary);
  cursor: pointer;
}

/* Delivery Status Bar */
.delivery-status-bar {
  display: flex; align-items: center;
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--color-border);
}

.delivery-step {
  display: flex; flex-direction: column;
  align-items: center; gap: 4px;
  flex: 1;
  opacity: 0.4;
}

.delivery-step.active { opacity: 1; }
.delivery-step.done { opacity: 1; }

.delivery-step ion-icon {
  font-size: 24px;
  color: var(--color-text-tertiary);
  background: var(--color-bg-tertiary);
  padding: 8px;
  border-radius: 50%;
}

.delivery-step.active ion-icon {
  color: white;
  background: var(--color-primary);
  animation: pulse-step 2s infinite;
}

.delivery-step.done ion-icon {
  color: white;
  background: var(--color-primary);
}

@keyframes pulse-step {
  0%, 100% { box-shadow: 0 0 0 0 rgba(76,175,80,0.4); }
  50% { box-shadow: 0 0 0 8px rgba(76,175,80,0); }
}

.delivery-step span { font-size: 11px; font-weight: 600; color: var(--color-text-primary); }

.delivery-line {
  flex: 1; height: 3px;
  background: var(--color-border);
  margin: 0 4px;
  border-radius: 2px;
  margin-bottom: 20px;
}

.delivery-line.done { background: var(--color-primary); }

/* Delivery Info */
.delivery-info { display: flex; flex-direction: column; gap: 8px; }

.delivery-detail {
  display: flex; align-items: center; gap: 8px;
  font-size: 14px; color: var(--color-text-secondary);
}

.delivery-detail ion-icon { font-size: 18px; color: var(--color-primary); flex-shrink: 0; }

.delivery-footer {
  display: flex; justify-content: space-between;
  align-items: center; margin-top: 8px;
  padding-top: 12px;
  border-top: 1px solid var(--color-border);
}

.delivery-amount { font-size: 20px; font-weight: 700; color: var(--color-primary); }

/* ─── Recent Deliveries Section ──────────────── */
.recent-section { padding: 0 20px 20px; }

.delivery-card {
  background: var(--color-card-bg);
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 12px;
  box-shadow: 0 2px 8px var(--color-card-shadow);
  cursor: pointer;
  transition: transform 0.2s;
}

.delivery-card:active { transform: scale(0.98); }

.delivery-header {
  display: flex; justify-content: space-between;
  align-items: center; margin-bottom: 12px;
}

.delivery-id { font-size: 14px; font-weight: 700; color: var(--color-text-primary); }

.delivery-status {
  padding: 4px 12px; border-radius: 12px;
  font-size: 11px; font-weight: 600;
  text-transform: uppercase;
}

.delivery-status.completed { background: var(--color-success-bg); color: var(--color-success); }
.delivery-status.pending { background: var(--color-warning-bg); color: var(--color-warning); }
.delivery-status.cancelled { background: var(--color-error-bg); color: var(--color-error); }

.delivery-details { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; }

.delivery-customer, .delivery-location {
  display: flex; align-items: center; gap: 6px;
  font-size: 13px; color: var(--color-text-secondary);
}

.delivery-customer ion-icon,
.delivery-location ion-icon { font-size: 16px; color: var(--color-primary); }

.delivery-footer-row {
  display: flex; justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid var(--color-border);
}

.delivery-time { font-size: 12px; color: var(--color-text-tertiary); }

.delivery-earn { display: flex; align-items: center; gap: 8px; }
.earn-amount { font-size: 16px; font-weight: 700; color: var(--color-primary); }

.tip-badge {
  background: var(--color-warning-bg);
  color: var(--color-warning);
  padding: 3px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
}

/* Empty State */
.empty-state { text-align: center; padding: 40px 20px; }
.empty-icon { font-size: 64px; color: var(--color-text-tertiary); margin-bottom: 16px; }
.empty-state p { color: var(--color-text-secondary); margin-bottom: 16px; }

/* ─── Licence Banner ──────────────────────────── */
.licence-banner {
  margin: 0 20px 16px;
  padding: 14px 16px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  background: #E8F5E9;
  border: 1px solid #4CAF50;
}

.licence-banner.warning {
  background: #FFF8E1;
  border-color: #FF9800;
}

.licence-banner.expired {
  background: #FFEBEE;
  border-color: #f44336;
}

.licence-banner-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.licence-banner-icon { font-size: 24px; color: #4CAF50; flex-shrink: 0; }
.licence-banner.warning .licence-banner-icon { color: #FF9800; }
.licence-banner.expired .licence-banner-icon { color: #f44336; }

.licence-banner-title { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0 0 2px 0; }
.licence-banner-sub { font-size: 12px; color: #666; margin: 0; }

.licence-banner-btn {
  --border-radius: 10px; height: 34px;
  font-size: 12px; font-weight: 700; text-transform: none;
  flex-shrink: 0;
}

/* ─── Bottom Navigation ───────────────────────── */
.bottom-nav {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  height: 60px;
}

.nav-item {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 4px; cursor: pointer; color: var(--color-text-tertiary);
  transition: color 0.2s;
  position: relative;
}

.nav-item ion-icon { font-size: 24px; }
.nav-item span { font-size: 11px; font-weight: 500; }
.nav-item.active { color: var(--color-primary); }
.nav-item:active { transform: scale(0.95); }

.nav-badge {
  position: absolute; top: 6px; right: 12px;
  background: #f44336; color: white;
  border-radius: 10px; padding: 1px 5px;
  font-size: 10px; font-weight: 700;
}

/* ─── Responsive ──────────────────────────────── */
@media (max-width: 360px) {
  .stats-section { gap: 8px; }
  .stat-value { font-size: 16px; }
  .actions-grid { gap: 8px; }
  .action-icon { width: 48px; height: 48px; font-size: 22px; }
}
</style>