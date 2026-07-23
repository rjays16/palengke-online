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

        <!-- Earnings Chart -->
        <div class="chart-card">
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
        <div class="wallet-card">
          <div class="wallet-header" @click="showWalletInfo">
            <div class="wallet-icon">
              <ion-icon :icon="walletOutline"></ion-icon>
            </div>
            <div class="wallet-info">
              <h3>Cash Wallet</h3>
              <p class="wallet-balance">₱{{ cashWallet.toLocaleString() }}</p>
            </div>
          </div>
          <ion-button size="small" color="success" @click.stop="showCashoutModal">
            Cash Out
            <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
          </ion-button>
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
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonFooter, IonButton, IonButtons, IonBackButton, IonIcon,
  alertController
} from '@ionic/vue';
import {
  helpCircleOutline, cashOutline, chevronDownOutline,
  chevronUpOutline, checkmarkCircleOutline,
  starOutline, flashOutline, bicycleOutline, walletOutline,
  homeOutline, cubeOutline, personOutline, arrowForwardOutline
} from 'ionicons/icons';

const router = useRouter();
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('token');
}

const selectedPeriod = ref('today');
const showBreakdown = ref(false);
const cashWallet = ref(0);

const statsData = ref({
  today: { netEarnings: 0, jobs: 0, tips: 0, onlineHours: 0, avgPerJob: 0, deliveryFee: 0, commission: 0, completionRate: 0, rating: 0, acceptRate: 0, totalKm: 0 },
  yesterday: { netEarnings: 0, jobs: 0, tips: 0, onlineHours: 0, avgPerJob: 0, deliveryFee: 0, commission: 0, completionRate: 0, rating: 0, acceptRate: 0, totalKm: 0 },
  week: { netEarnings: 0, jobs: 0, tips: 0, onlineHours: 0, avgPerJob: 0, deliveryFee: 0, commission: 0, completionRate: 0, rating: 0, acceptRate: 0, totalKm: 0 },
  month: { netEarnings: 0, jobs: 0, tips: 0, onlineHours: 0, avgPerJob: 0, deliveryFee: 0, commission: 0, completionRate: 0, rating: 0, acceptRate: 0, totalKm: 0 },
});

const currentStats = computed(() => statsData.value[selectedPeriod.value]);

const periodLabel = computed(() => {
  if (selectedPeriod.value === 'today') return "Today's Earnings";
  if (selectedPeriod.value === 'yesterday') return "Yesterday's Earnings";
  if (selectedPeriod.value === 'week') return "This Week's Earnings";
  return "This Month's Earnings";
});

const chartData = ref([]);

const maxAmount = computed(() => Math.max(...chartData.value.map(d => d.amount), 0));

const getBarHeight = (amount) => {
  if (maxAmount.value === 0) return 0;
  return Math.max((amount / maxAmount.value) * 100, amount > 0 ? 5 : 0);
};

onMounted(() => {
  fetchEarnings();
});

async function fetchEarnings() {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/earnings`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      statsData.value = {
        today: data.today,
        yesterday: data.yesterday,
        week: data.week,
        month: data.month,
      };
      chartData.value = data.chart || [];
    }
  } catch {}
}

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

const showCashoutModal = async () => {
  const alert = await alertController.create({
    header: 'Cash Out',
    message: `How much do you want to cash out?\n\nAvailable balance: ₱${cashWallet.value.toLocaleString()}`,
    inputs: [{ name: 'amount', type: 'number', placeholder: 'Enter amount', min: 50, max: cashWallet.value }],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Cash Out',
        handler: async (data) => {
          if (data.amount && data.amount > 0 && data.amount <= cashWallet.value) {
            cashWallet.value -= parseFloat(data.amount);
            await showSuccessModal(`₱${parseFloat(data.amount).toLocaleString()} cashed out successfully!`);
          } else if (data.amount > cashWallet.value) {
            await alertController.create({
              header: 'Error',
              message: 'Amount exceeds your available balance.',
              buttons: ['OK']
            }).then(a => a.present());
          }
        }
      }
    ]
  });
  await alert.present();
};

const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      textDiv.style.whiteSpace = 'pre-line';
      
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  await alert.onDidDismiss();
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
  justify-content: space-between; gap: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.wallet-card:active { transform: scale(0.98); }

.wallet-header { display: flex; align-items: center; gap: 14px; cursor: pointer; flex: 1; }
.wallet-header:active { opacity: 0.7; }
.wallet-icon {
  width: 48px; height: 48px;
  background: #E8F5E9; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
}
.wallet-icon ion-icon { font-size: 26px; color: #4CAF50; }
.wallet-info h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.wallet-balance { font-size: 18px; font-weight: 800; color: #4CAF50; margin: 0; }
.wallet-card ion-button { --border-radius: 10px; height: 36px; font-size: 13px; font-weight: 600; text-transform: none; flex-shrink: 0; }

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