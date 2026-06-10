<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Palengke Online</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="logout">
            <ion-icon :icon="logOutOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="home-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
          <div class="logo-container">
            <img src="/assets/images/logo.png" alt="Palengke Online" class="logo">
          </div>
          <h1>Welcome to Palengke Online! <font-awesome-icon icon="carrot" /><font-awesome-icon icon="carrot" /><font-awesome-icon icon="seedling" /></h1>
          <p class="subtitle">Fresh market delivered to your door</p>
        </div>

        <!-- Subscription Banner -->
        <div class="sub-banner" :class="subStatus" @click="goToPlans">
          <div class="sub-banner-left">
            <ion-icon :icon="subIcon" class="sub-banner-icon"></ion-icon>
            <div class="sub-banner-text">
              <p class="sub-banner-title">{{ subTitle }}</p>
              <p class="sub-banner-sub">{{ subDesc }}</p>
            </div>
          </div>
          <ion-icon :icon="chevronForwardOutline" class="sub-banner-arrow"></ion-icon>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <ion-icon :icon="cashOutline" class="stat-icon"></ion-icon>
            <div class="stat-value">₱{{ formatPrice(stats.today_sales) }}</div>
            <div class="stat-label">Today's Sale</div>
          </div>
          
          <div class="stat-card">
            <ion-icon :icon="cubeOutline" class="stat-icon"></ion-icon>
            <div class="stat-value">{{ stats.total_products }}</div>
            <div class="stat-label">Total Products</div>
          </div>
          
          <div class="stat-card">
            <ion-icon :icon="cartOutline" class="stat-icon"></ion-icon>
            <div class="stat-value">{{ stats.total_orders }}</div>
            <div class="stat-label">Total Orders</div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-section">
          <ion-button expand="block" class="action-btn">
            <ion-icon :icon="searchOutline" slot="start"></ion-icon>
            Browse Products
          </ion-button>
          
          <ion-button expand="block" fill="outline" class="action-btn">
            <ion-icon :icon="personOutline" slot="start"></ion-icon>
            View Profile
          </ion-button>
          
          <ion-button expand="block" color="danger" @click="logout">
            <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
            Logout
          </ion-button>
        </div>

        <!-- Info Text -->
        <p class="info-text">
          You are successfully logged in! <font-awesome-icon icon="glass-cheers" />
        </p>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { 
  IonPage, 
  IonHeader, 
  IonToolbar, 
  IonTitle, 
  IonContent, 
  IonButton,
  IonButtons,
  IonIcon,
  alertController
} from '@ionic/vue';
import { computed, ref } from 'vue';
import { onIonViewWillEnter } from '@ionic/vue';
import { 
  logOutOutline,
  cartOutline,
  cashOutline,
  cubeOutline,
  searchOutline,
  personOutline,
  chevronForwardOutline,
  timeOutline,
  alertCircleOutline,
  checkmarkCircleOutline
} from 'ionicons/icons';
import { getSubscription, daysLeft, isExpired } from '@/services/subscriptionService';

const router = useRouter();

const stats = ref({
  today_sales: 0,
  today_orders: 0,
  total_products: 0,
  total_orders: 0,
});

const sub = ref(getSubscription())

function refreshSub() {
  sub.value = getSubscription()
}

async function loadStats() {
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  try {
    const res = await fetch(`http://localhost:8000/api/seller/dashboard/stats?email=${encodeURIComponent(email)}`, {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (res.ok && data.stats) {
      stats.value = data.stats;
    }
  } catch (e) {
    console.error('Failed to load stats:', e);
  }
}

onIonViewWillEnter(() => {
  refreshSub();
  loadStats();
});

const remaining = computed(() => daysLeft(sub.value))
const expired = computed(() => isExpired(sub.value))

const subStatus = computed(() => {
  if (expired.value) return 'expired'
  if (sub.value.status === 'active') return 'active'
  return 'trial'
})

const subIcon = computed(() => {
  if (expired.value) return alertCircleOutline
  if (sub.value.status === 'active') return checkmarkCircleOutline
  return timeOutline
})

const subTitle = computed(() => {
  if (expired.value) return 'Trial Expired'
  if (sub.value.status === 'active') return 'Plan Active'
  return 'Free Trial'
})

const subDesc = computed(() => {
  if (expired.value) return 'Choose a plan to continue selling'
  if (remaining.value <= 3) return `${remaining.value} day${remaining.value > 1 ? 's' : ''} left — renew soon!`
  return `${remaining.value} days remaining in your trial`
})

const formatPrice = (price) => {
  return (price || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const goToPlans = () => router.push('/plans')

const logout = async () => {
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
        handler: () => {
          // Clear authentication
          localStorage.removeItem('isAuthenticated');
          localStorage.removeItem('token');
          
          // Navigate to login
          router.push('/login');
        }
      }
    ]
  });
  
  await alert.present();
};
</script>

<style scoped>
.home-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px 0;
}

/* Dark mode background for ion-content */
:global(html.dark) ion-page ion-content,
:global(html.dark) ion-content {
  --background: var(--color-bg-primary);
}

:global(html.dark) .dashboard-content {
  --background: var(--color-bg-secondary);
}

/* Welcome Section */
.welcome-section {
  text-align: center;
  margin-bottom: 40px;
}

.logo-container {
  margin-bottom: 20px;
}

.logo {
  width: 120px;
  height: auto;
  animation: float 3s ease-in-out infinite;
}

h1 {
  font-size: 28px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 16px;
  color: var(--color-text-secondary);
  margin: 0;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 32px;
}

.stat-card {
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-hover) 100%);
  border-radius: 16px;
  padding: 20px 12px;
  text-align: center;
  color: var(--color-text-inverse);
  box-shadow: 0 4px 12px var(--color-card-shadow);
}

.stat-icon {
  font-size: 32px;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 12px;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Action Buttons */
.action-section {
  margin-bottom: 32px;
}

.action-btn {
  --border-radius: 14px;
  margin-bottom: 12px;
  height: 52px;
  font-weight: 600;
  text-transform: none;
}

.action-btn::part(native) {
  font-size: 16px;
}

/* Subscription Banner */
.sub-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-radius: 14px;
  margin-bottom: 24px;
  cursor: pointer;
  transition: opacity 0.2s;
}
.sub-banner:active { opacity: 0.8; }

.sub-banner.trial { background: #E3F2FD; }
.sub-banner.active { background: #E8F5E9; }
.sub-banner.expired { background: #FFEBEE; }

.sub-banner-left { display: flex; align-items: center; gap: 12px; flex: 1; }
.sub-banner-icon { font-size: 24px; }
.sub-banner.trial .sub-banner-icon { color: #1565C0; }
.sub-banner.active .sub-banner-icon { color: #2E7D32; }
.sub-banner.expired .sub-banner-icon { color: #c62828; }

.sub-banner-text { flex: 1; }
.sub-banner-title { font-size: 14px; font-weight: 700; margin: 0 0 2px; }
.sub-banner.trial .sub-banner-title { color: #1565C0; }
.sub-banner.active .sub-banner-title { color: #2E7D32; }
.sub-banner.expired .sub-banner-title { color: #c62828; }

.sub-banner-sub { font-size: 12px; margin: 0; }
.sub-banner.trial .sub-banner-sub { color: #5a7fa0; }
.sub-banner.active .sub-banner-sub { color: #558b5a; }
.sub-banner.expired .sub-banner-sub { color: #9a5555; }

.sub-banner-arrow { font-size: 18px; color: #999; }

/* Info Text */
.info-text {
  text-align: center;
  color: var(--color-text-secondary);
  font-size: 14px;
  margin-top: 40px;
}

/* Animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

/* Responsive */
@media (max-width: 360px) {
  h1 {
    font-size: 24px;
  }
  
  .stats-grid {
    gap: 8px;
  }
  
  .stat-card {
    padding: 16px 8px;
  }
  
  .stat-icon {
    font-size: 28px;
  }
  
  .stat-value {
    font-size: 20px;
  }
}
</style>