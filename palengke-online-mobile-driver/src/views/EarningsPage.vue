<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>Earnings</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="showHelp">
            <ion-icon :icon="helpCircleOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="earnings-content">

      <!-- Period Tabs -->
      <div class="period-tabs">
        <div
          class="period-tab"
          :class="{ active: selectedPeriod === 'today' }"
          @click="selectedPeriod = 'today'"
        >
          Today
        </div>
        <div
          class="period-tab"
          :class="{ active: selectedPeriod === 'yesterday' }"
          @click="selectedPeriod = 'yesterday'"
        >
          Yesterday
        </div>
        <div
          class="period-tab"
          :class="{ active: selectedPeriod === 'week' }"
          @click="selectedPeriod = 'week'"
        >
          This Week
        </div>
        <div
          class="period-tab"
          :class="{ active: selectedPeriod === 'month' }"
          @click="selectedPeriod = 'month'"
        >
          This Month
        </div>
      </div>

      <div class="earnings-container">

        <!-- Main Earnings Card -->
        <div class="earnings-hero-card">
          <div class="hero-header">
            <div>
              <p class="hero-label">{{ periodLabel }}</p>
              <h1 class="hero-amount">₱{{ currentStats.netEarnings.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</h1>
              <p class="hero-jobs">{{ currentStats.jobs }} Jobs Completed</p>
            </div>
            <div class="hero-icon">
              <ion-icon :icon="cashOutline"></ion-icon>
            </div>
          </div>

          <div class="hero-divider"></div>

          <div class="hero-stats">
            <div class="hero-stat">
              <span class="hero-stat-value">₱{{ currentStats.tips.toLocaleString() }}</span>
              <span class="hero-stat-label">Tips</span>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
              <span class="hero-stat-value">{{ currentStats.onlineHours }}h</span>
              <span class="hero-stat-label">Active</span>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
              <span class="hero-stat-value">₱{{ currentStats.avgPerJob.toLocaleString() }}</span>
              <span class="hero-stat-label">Avg/Job</span>
            </div>
          </div>

          <button class="view-breakdown-btn" @click="showBreakdown = !showBreakdown">
            View Breakdown
            <ion-icon :icon="showBreakdown ? chevronUpOutline : chevronDownOutline"></ion-icon>
          </button>

          <!-- Breakdown -->
          <div class="breakdown-section" v-if="showBreakdown">
            <div class="breakdown-item">
              <span class="breakdown-label">Delivery Fee</span>
              <span class="breakdown-value">₱{{ currentStats.deliveryFee.toLocaleString() }}</span>
            </div>
            <div class="breakdown-item">
              <span class="breakdown-label">Tips Received</span>
              <span class="breakdown-value tip">+ ₱{{ currentStats.tips.toLocaleString() }}</span>
            </div>
            <div class="breakdown-item">
              <span class="breakdown-label">Commission Deduction</span>
              <span class="breakdown-value deduction">- ₱{{ currentStats.commission.toLocaleString() }}</span>
            </div>
            <div class="breakdown-divider"></div>
            <div class="breakdown-item total">
              <span class="breakdown-label">Net Earnings</span>
              <span class="breakdown-value total">₱{{ currentStats.netEarnings.toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <!-- Bookings Completed -->
        <div class="bookings-card" @click="goToHistory">
          <div class="bookings-info">
            <h3>Bookings completed</h3>
            <p>{{ currentStats.jobs }} bookings</p>
          </div>
          <ion-icon :icon="chevronForwardOutline"></ion-icon>
        </div>

        <!-- Weekly Chart (only for week/month view) -->
        <div class="chart-card" v-if="selectedPeriod === 'week' || selectedPeriod === 'month'">
          <h3 class="chart-title">Earnings Chart</h3>
          <div class="bar-chart">
            <div
              v-for="(day, index) in chartData"
              :key="index"
              class="bar-item"
            >
              <div class="bar-container">
                <div
                  class="bar-fill"
                  :style="{ height: getBarHeight(day.amount) + '%' }"
                  :class="{ active: day.isToday }"
                ></div>
              </div>
              <span class="bar-label">{{ day.label }}</span>
              <span class="bar-amount" v-if="day.amount > 0">₱{{ day.amount }}</span>
            </div>
          </div>
        </div>

        <!-- Performance Stats -->
        <div class="performance-card">
          <h3 class="section-title">Performance</h3>
          <div class="perf-grid">
            <div class="perf-item">
              <div class="perf-icon green">
                <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              </div>
              <div class="perf-info">
                <span class="perf-value">{{ currentStats.completionRate }}%</span>
                <span class="perf-label">Completion Rate</span>
              </div>
            </div>
            <div class="perf-item">
              <div class="perf-icon blue">
                <ion-icon :icon="starOutline"></ion-icon>
              </div>
              <div class="perf-info">
                <span class="perf-value">{{ currentStats.rating }}</span>
                <span class="perf-label">Avg Rating</span>
              </div>
            </div>
            <div class="perf-item">
              <div class="perf-icon orange">
                <ion-icon :icon="flashOutline"></ion-icon>
              </div>
              <div class="perf-info">
                <span class="perf-value">{{ currentStats.acceptRate }}%</span>
                <span class="perf-label">Accept Rate</span>
              </div>
            </div>
            <div class="perf-item">
              <div class="perf-icon purple">
                <ion-icon :icon="bicycleOutline"></ion-icon>
              </div>
              <div class="perf-info">
                <span class="perf-value">{{ currentStats.totalKm }} km</span>
                <span class="perf-label">Distance</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Wallet Section -->
        <div class="wallet-card" @click="showWalletInfo">
          <div class="wallet-header">
            <div class="wallet-icon">
              <ion-icon :icon="walletOutline"></ion-icon>
            </div>
            <div class="wallet-info">
              <h3>Cash Wallet</h3>
              <p class="wallet-balance">₱{{ cashWallet.toLocaleString() }}</p>
            </div>
          </div>
          <ion-icon :icon="chevronForwardOutline" class="wallet-arrow"></ion-icon>
        </div>

      </div>
    </ion-content>

    <!-- Bottom Navigation -->
    <ion-footer>
      <ion-toolbar>
        <div class="bottom-nav">
          <div class="nav-item" @click="goToHome">
            <ion-icon :icon="homeOutline"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item" @click="goToOrders">
            <ion-icon :icon="cubeOutline"></ion-icon>
            <span>Orders</span>
          </div>
          <div class="nav-item active">
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
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonFooter, IonButton, IonButtons, IonBackButton, IonIcon,
  alertController
} from '@ionic/vue';
import {
  helpCircleOutline, cashOutline, chevronDownOutline,
  chevronUpOutline, chevronForwardOutline, checkmarkCircleOutline,
  starOutline, flashOutline, bicycleOutline, walletOutline,
  homeOutline, cubeOutline, personOutline
} from 'ionicons/icons';

const router = useRouter();

const selectedPeriod = ref('today');
const showBreakdown = ref(false);
const cashWallet = ref(438.59);

// Stats per period
const statsData = {
  today: {
    netEarnings: 850.00,
    jobs: 5,
    tips: 120,
    onlineHours: 4.5,
    avgPerJob: 170,
    deliveryFee: 850,
    commission: 120,
    completionRate: 100,
    rating: 5.0,
    acceptRate: 80,
    totalKm: 18.5
  },
  yesterday: {
    netEarnings: 1687.00,
    jobs: 12,
    tips: 250,
    onlineHours: 8,
    avgPerJob: 140,
    deliveryFee: 1850,
    commission: 163,
    completionRate: 95,
    rating: 4.9,
    acceptRate: 85,
    totalKm: 42.3
  },
  week: {
    netEarnings: 11196.73,
    jobs: 67,
    tips: 1200,
    onlineHours: 45,
    avgPerJob: 167,
    deliveryFee: 12500,
    commission: 1303.27,
    completionRate: 96,
    rating: 4.9,
    acceptRate: 83,
    totalKm: 245.8
  },
  month: {
    netEarnings: 42500.00,
    jobs: 280,
    tips: 4800,
    onlineHours: 180,
    avgPerJob: 151,
    deliveryFee: 48000,
    commission: 5500,
    completionRate: 95,
    rating: 4.9,
    acceptRate: 82,
    totalKm: 980.5
  }
};

const currentStats = computed(() => statsData[selectedPeriod.value]);

const periodLabel = computed(() => {
  if (selectedPeriod.value === 'today') return "Today's Earnings";
  if (selectedPeriod.value === 'yesterday') return "Yesterday's Earnings";
  if (selectedPeriod.value === 'week') return "This Week's Earnings";
  return "This Month's Earnings";
});

// Weekly chart data
const chartData = ref([
  { label: 'Mon', amount: 1200, isToday: false },
  { label: 'Tue', amount: 1687, isToday: false },
  { label: 'Wed', amount: 1850, isToday: false },
  { label: 'Thu', amount: 2100, isToday: false },
  { label: 'Fri', amount: 1950, isToday: false },
  { label: 'Sat', amount: 850, isToday: true },
  { label: 'Sun', amount: 0, isToday: false }
]);

const maxAmount = computed(() => Math.max(...chartData.value.map(d => d.amount)));

const getBarHeight = (amount) => {
  if (maxAmount.value === 0) return 0;
  return Math.max((amount / maxAmount.value) * 100, amount > 0 ? 5 : 0);
};

// Methods
const showHelp = async () => {
  const alert = await alertController.create({
    header: 'Earnings Help',
    message: 'Your net earnings are calculated after deducting platform commission. Tips are added on top of your delivery fee.',
    buttons: ['OK']
  });
  await alert.present();
};

const showWalletInfo = async () => {
  const alert = await alertController.create({
    header: 'Cash Wallet',
    message: `Your current cash wallet balance is ₱${cashWallet.value.toLocaleString()}. This includes cash collected from COD orders.`,
    buttons: ['OK']
  });
  await alert.present();
};

// Navigation
const goToHome = () => router.push('/home');
const goToOrders = () => router.push('/orders');
const goToHistory = () => router.push('/history');
const goToProfile = () => router.push('/profile');
</script>

<style scoped>
.earnings-content { --background: #f8f9fa; }

/* Period Tabs */
.period-tabs {
  display: flex; background: white;
  border-bottom: 1px solid #f0f0f0;
  overflow-x: auto; padding: 0 8px;
}
.period-tabs::-webkit-scrollbar { display: none; }

.period-tab {
  flex: 1; min-width: fit-content;
  padding: 14px 16px;
  text-align: center;
  font-size: 13px; font-weight: 600;
  color: #999; cursor: pointer;
  border-bottom: 3px solid transparent;
  white-space: nowrap;
  transition: all 0.2s;
}

.period-tab.active {
  color: #4CAF50;
  border-bottom-color: #4CAF50;
}

/* Container */
.earnings-container { padding: 16px; padding-bottom: 80px; }

/* Hero Card */
.earnings-hero-card {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 16px;
  color: white;
  box-shadow: 0 4px 20px rgba(76,175,80,0.3);
}

.hero-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 20px;
}

.hero-label { font-size: 14px; opacity: 0.85; margin: 0 0 6px 0; }
.hero-amount {
  font-size: 36px; font-weight: 800;
  margin: 0 0 6px 0; letter-spacing: -1px;
}
.hero-jobs { font-size: 14px; opacity: 0.85; margin: 0; }

.hero-icon {
  width: 56px; height: 56px;
  background: rgba(255,255,255,0.2);
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
}
.hero-icon ion-icon { font-size: 32px; color: white; }

.hero-divider {
  height: 1px; background: rgba(255,255,255,0.2);
  margin-bottom: 16px;
}

.hero-stats { display: flex; justify-content: space-around; margin-bottom: 16px; }

.hero-stat { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.hero-stat-value { font-size: 18px; font-weight: 800; }
.hero-stat-label { font-size: 11px; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; }

.hero-stat-divider { width: 1px; background: rgba(255,255,255,0.2); }

.view-breakdown-btn {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  width: 100%; padding: 10px;
  background: rgba(255,255,255,0.15);
  border: none; border-radius: 12px;
  color: white; font-size: 14px; font-weight: 600;
  cursor: pointer; font-family: inherit;
  transition: background 0.2s;
}
.view-breakdown-btn:active { background: rgba(255,255,255,0.25); }
.view-breakdown-btn ion-icon { font-size: 18px; }

/* Breakdown */
.breakdown-section {
  margin-top: 16px; padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.2);
}

.breakdown-item {
  display: flex; justify-content: space-between;
  align-items: center; padding: 8px 0;
}

.breakdown-label { font-size: 14px; opacity: 0.9; }
.breakdown-value { font-size: 14px; font-weight: 700; }
.breakdown-value.tip { color: #FFD700; }
.breakdown-value.deduction { color: #FF8A80; }

.breakdown-divider { height: 1px; background: rgba(255,255,255,0.2); margin: 8px 0; }

.breakdown-item.total .breakdown-label { font-size: 15px; font-weight: 700; }
.breakdown-item.total .breakdown-value { font-size: 18px; }

/* Bookings Card */
.bookings-card {
  background: white; border-radius: 16px;
  padding: 16px 20px; margin-bottom: 16px;
  display: flex; align-items: center;
  justify-content: space-between;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  cursor: pointer;
}
.bookings-card:active { transform: scale(0.98); }
.bookings-info h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.bookings-info p { font-size: 13px; color: #666; margin: 0; }
.bookings-card ion-icon { font-size: 20px; color: #ccc; }

/* Chart Card */
.chart-card {
  background: white; border-radius: 16px;
  padding: 20px; margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.chart-title { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 20px 0; }

.bar-chart {
  display: flex; align-items: flex-end;
  gap: 8px; height: 140px;
}

.bar-item {
  flex: 1; display: flex;
  flex-direction: column; align-items: center;
  gap: 4px; height: 100%;
}

.bar-container {
  flex: 1; width: 100%;
  display: flex; align-items: flex-end;
  background: #f5f5f5; border-radius: 6px;
  overflow: hidden;
}

.bar-fill {
  width: 100%; background: #C8E6C9;
  border-radius: 6px; transition: height 0.5s ease;
}

.bar-fill.active { background: #4CAF50; }

.bar-label { font-size: 11px; color: #999; font-weight: 600; }
.bar-amount { font-size: 10px; color: #4CAF50; font-weight: 700; }

/* Performance Card */
.performance-card {
  background: white; border-radius: 16px;
  padding: 20px; margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.section-title { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }

.perf-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }

.perf-item {
  display: flex; align-items: center; gap: 12px;
  background: #f8f9fa; border-radius: 12px; padding: 12px;
}

.perf-icon {
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; flex-shrink: 0;
}
.perf-icon.green { background: #E8F5E9; color: #4CAF50; }
.perf-icon.blue { background: #E3F2FD; color: #2196F3; }
.perf-icon.orange { background: #FFF3E0; color: #FF9800; }
.perf-icon.purple { background: #F3E5F5; color: #9C27B0; }

.perf-info { display: flex; flex-direction: column; gap: 2px; }
.perf-value { font-size: 16px; font-weight: 800; color: #1a1a1a; }
.perf-label { font-size: 11px; color: #666; }

/* Wallet Card */
.wallet-card {
  background: white; border-radius: 16px;
  padding: 16px 20px; margin-bottom: 16px;
  display: flex; align-items: center;
  justify-content: space-between;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  cursor: pointer;
}
.wallet-card:active { transform: scale(0.98); }

.wallet-header { display: flex; align-items: center; gap: 14px; }
.wallet-icon {
  width: 48px; height: 48px;
  background: #E8F5E9; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
}
.wallet-icon ion-icon { font-size: 26px; color: #4CAF50; }
.wallet-info h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.wallet-balance { font-size: 18px; font-weight: 800; color: #4CAF50; margin: 0; }
.wallet-arrow { font-size: 20px; color: #ccc; }

/* Bottom Nav */
.bottom-nav { display: grid; grid-template-columns: repeat(4, 1fr); height: 60px; }
.nav-item {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 4px; cursor: pointer; color: #999;
}
.nav-item ion-icon { font-size: 24px; }
.nav-item span { font-size: 11px; font-weight: 500; }
.nav-item.active { color: #4CAF50; }
.nav-item:active { transform: scale(0.95); }
</style>