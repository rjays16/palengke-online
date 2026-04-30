<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/earnings"></ion-back-button>
        </ion-buttons>
        <ion-title>History</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="showHelp">
            <ion-icon :icon="helpCircleOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="history-content">

      <!-- Daily / Weekly Toggle -->
      <div class="view-toggle">
        <div
          class="toggle-option"
          :class="{ active: viewMode === 'daily' }"
          @click="viewMode = 'daily'"
        >
          Daily
        </div>
        <div
          class="toggle-option"
          :class="{ active: viewMode === 'weekly' }"
          @click="viewMode = 'weekly'"
        >
          Weekly
        </div>
        <div class="month-label">{{ currentMonthLabel }}</div>
      </div>

      <!-- Daily Calendar -->
      <div class="calendar-section" v-if="viewMode === 'daily'">
        <div class="calendar-days">
          <div
            v-for="day in weekDays"
            :key="day.date"
            class="calendar-day"
            :class="{
              active: selectedDate === day.date,
              today: day.isToday,
              has_earnings: day.earnings > 0
            }"
            @click="selectDate(day.date)"
          >
            <span class="day-name">{{ day.name }}</span>
            <span class="day-number">{{ day.number }}</span>
            <div class="day-dot" v-if="day.earnings > 0"></div>
          </div>
        </div>
      </div>

      <!-- Weekly Calendar -->
      <div class="calendar-section" v-if="viewMode === 'weekly'">
        <div class="week-list">
          <div
            v-for="week in weekList"
            :key="week.id"
            class="week-item"
            :class="{ active: selectedWeek === week.id }"
            @click="selectedWeek = week.id"
          >
            <span class="week-label">{{ week.label }}</span>
            <span class="week-range">{{ week.range }}</span>
          </div>
        </div>
      </div>

      <div class="history-container">

        <!-- Earnings Summary for Selected Period -->
        <div class="period-summary">
          <div class="summary-left">
            <p class="summary-label">Your net earnings</p>
            <h2 class="summary-amount">₱{{ currentPeriodEarnings.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</h2>
          </div>
          <div class="summary-right" v-if="viewMode === 'weekly'">
            <!-- Mini bar chart for weekly -->
            <div class="mini-chart">
              <div
                v-for="(bar, i) in miniChartData"
                :key="i"
                class="mini-bar"
                :style="{ height: getMiniBarHeight(bar) + '%' }"
                :class="{ active: bar.isToday }"
              ></div>
            </div>
            <div class="mini-chart-labels">
              <span v-for="(bar, i) in miniChartData" :key="i">{{ bar.label }}</span>
            </div>
          </div>
        </div>

        <!-- Jobs Completed Count -->
        <div class="jobs-completed-header">
          <span class="jobs-count">{{ currentJobs.length }} JOBS COMPLETED</span>
        </div>

        <!-- Jobs List -->
        <div v-if="currentJobs.length > 0" class="jobs-list">
          <div
            v-for="job in currentJobs"
            :key="job.id"
            class="job-card"
            @click="viewJobDetails(job)"
          >
            <div class="job-left">
              <div class="job-time">{{ job.time }}</div>
              <div class="job-name">{{ job.sellerName }}</div>
              <div class="job-address">{{ job.deliveryAddress }}</div>
              <div class="job-tags">
                <span class="payment-tag" :class="job.paymentMethod">
                  {{ job.paymentMethod === 'cash' ? 'Cash' : 'GCash' }}
                </span>
                <span class="tipped-tag" v-if="job.tipped">
                  <font-awesome-icon :icon="['fas', 'money-bill']" /> Tipped
                </span>
                <span class="bonus-tag" v-if="job.bonus > 0">
                  <font-awesome-icon :icon="['fas', 'bolt']" /> Bonus
                </span>
              </div>
            </div>
            <div class="job-right">
              <span class="job-amount">₱{{ job.amount.toFixed(2) }}</span>
              <ion-icon :icon="chevronForwardOutline" class="job-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <div class="empty-icon-wrap">
            <ion-icon :icon="bicycleOutline"></ion-icon>
          </div>
          <h3>No jobs recorded</h3>
          <p>{{ viewMode === 'daily' ? 'No jobs on this day' : 'No jobs this week' }}</p>
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
          <div class="nav-item active" @click="goToEarnings">
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
  helpCircleOutline, chevronForwardOutline,
  bicycleOutline, homeOutline, cubeOutline,
  cashOutline, personOutline
} from 'ionicons/icons';

const router = useRouter();

const viewMode = ref('daily');
const selectedDate = ref(23); // today
const selectedWeek = ref('week13');
const currentMonthLabel = ref('March / April');

// Week days for daily view
const weekDays = ref([
  { name: 'Sun', number: 31, date: 31, isToday: false, earnings: 0 },
  { name: 'Mon', number: 1, date: 1, isToday: false, earnings: 1200 },
  { name: 'Tue', number: 2, date: 2, isToday: false, earnings: 1687 },
  { name: 'Wed', number: 3, date: 3, isToday: false, earnings: 1850 },
  { name: 'Thu', number: 4, date: 4, isToday: false, earnings: 2100 },
  { name: 'Fri', number: 5, date: 5, isToday: false, earnings: 1950 },
  { name: 'Sat', number: 6, date: 6, isToday: true, earnings: 850 },
]);

// Week list for weekly view
const weekList = ref([
  { id: 'week10', label: 'Week 10', range: '11 - 17' },
  { id: 'week11', label: 'Week 11', range: '18 - 24' },
  { id: 'week12', label: 'Week 12', range: '25 - 31' },
  { id: 'week13', label: 'Week 13', range: '1 - 7' },
]);

// Mini chart data for weekly view
const miniChartData = ref([
  { label: 'Mon', amount: 1200, isToday: false },
  { label: 'Tue', amount: 1687, isToday: false },
  { label: 'Wed', amount: 1850, isToday: false },
  { label: 'Thu', amount: 2100, isToday: false },
  { label: 'Fri', amount: 1950, isToday: false },
  { label: 'Sat', amount: 850, isToday: true },
  { label: 'Sun', amount: 0, isToday: false },
]);

const maxMiniBar = computed(() => Math.max(...miniChartData.value.map(d => d.amount)));
const getMiniBarHeight = (bar) => {
  if (maxMiniBar.value === 0) return 0;
  return Math.max((bar.amount / maxMiniBar.value) * 100, bar.amount > 0 ? 10 : 0);
};

// All jobs data
const allJobs = {
  2: [ // Tuesday Apr 2
    { id: 'j1', time: '5:31 PM', sellerName: 'Greenwich - SM CDO', deliveryAddress: 'Brgy. Carmen, CDO', amount: 230.00, paymentMethod: 'gcash', tipped: true, bonus: 0 },
    { id: 'j2', time: '5:00 PM', sellerName: 'Admiral Bay Suites', deliveryAddress: 'Brgy. Lapasan, CDO', amount: 156.00, paymentMethod: 'cash', tipped: false, bonus: 0 },
    { id: 'j3', time: '4:46 PM', sellerName: "Juan's Fresh Market", deliveryAddress: 'Brgy. Macasandig, CDO', amount: 320.00, paymentMethod: 'cash', tipped: true, bonus: 50 },
    { id: 'j4', time: '3:20 PM', sellerName: "Ana's Palengke", deliveryAddress: 'Brgy. Bulua, CDO', amount: 180.00, paymentMethod: 'gcash', tipped: false, bonus: 0 },
    { id: 'j5', time: '2:15 PM', sellerName: "Rosa's Veggies", deliveryAddress: 'Brgy. Cogon, CDO', amount: 95.00, paymentMethod: 'cash', tipped: false, bonus: 0 },
  ],
  6: [ // Today Sat Apr 6
    { id: 'j6', time: '2:30 PM', sellerName: "Mario's Fresh Fish", deliveryAddress: 'Brgy. Agusan, CDO', amount: 250.00, paymentMethod: 'cash', tipped: true, bonus: 0 },
    { id: 'j7', time: '1:00 PM', sellerName: "Pedro's Meat Shop", deliveryAddress: 'Brgy. Kauswagan, CDO', amount: 180.00, paymentMethod: 'gcash', tipped: false, bonus: 0 },
    { id: 'j8', time: '11:30 AM', sellerName: "Fresh Fruits Stand", deliveryAddress: 'Brgy. Camaman-an, CDO', amount: 150.00, paymentMethod: 'cash', tipped: true, bonus: 30 },
    { id: 'j9', time: '10:00 AM', sellerName: "CDO Palengke Stall 15", deliveryAddress: 'Brgy. Carmen, CDO', amount: 120.00, paymentMethod: 'cash', tipped: false, bonus: 0 },
    { id: 'j10', time: '8:45 AM', sellerName: "Mama's Veggies", deliveryAddress: 'Brgy. Balulang, CDO', amount: 150.00, paymentMethod: 'gcash', tipped: false, bonus: 0 },
  ]
};

const currentJobs = computed(() => {
  if (viewMode.value === 'daily') {
    return allJobs[selectedDate.value] || [];
  }
  // For weekly, combine all jobs
  return Object.values(allJobs).flat();
});

const currentPeriodEarnings = computed(() => {
  if (viewMode.value === 'daily') {
    const day = weekDays.value.find(d => d.date === selectedDate.value);
    return day ? day.earnings : 0;
  }
  return 10896.73; // weekly total
});

// Methods
const selectDate = (date) => { selectedDate.value = date; };

const viewJobDetails = (job) => {
  const jobData = encodeURIComponent(JSON.stringify(job));
  router.push(`/history/${jobData}`);
};

const showHelp = async () => {
  const alert = await alertController.create({
    header: 'History Help',
    message: 'View your daily and weekly delivery history. Tap any job to see full details.',
    buttons: ['OK']
  });
  await alert.present();
};

// Navigation
const goToHome = () => router.push('/home');
const goToOrders = () => router.push('/orders');
const goToEarnings = () => router.push('/earnings');
const goToProfile = () => router.push('/profile');
</script>

<style scoped>
.history-content { --background: #f8f9fa; }

/* View Toggle */
.view-toggle {
  display: flex; align-items: center;
  background: white; padding: 12px 20px;
  border-bottom: 1px solid #f0f0f0; gap: 8px;
}

.toggle-option {
  padding: 8px 20px; border-radius: 20px;
  font-size: 14px; font-weight: 700;
  color: #999; cursor: pointer;
  transition: all 0.2s;
}

.toggle-option.active {
  background: #4CAF50; color: white;
}

.month-label {
  margin-left: auto; font-size: 14px;
  font-weight: 600; color: #333;
}

/* Calendar */
.calendar-section {
  background: white;
  border-bottom: 1px solid #f0f0f0;
}

/* Daily Calendar */
.calendar-days {
  display: flex; padding: 12px 16px 16px;
  gap: 4px;
}

.calendar-day {
  flex: 1; display: flex; flex-direction: column;
  align-items: center; gap: 6px;
  padding: 8px 4px; border-radius: 12px;
  cursor: pointer; transition: all 0.2s;
  position: relative;
}

.calendar-day.active {
  background: #4CAF50;
}

.calendar-day.today:not(.active) {
  background: #E8F5E9;
}

.day-name {
  font-size: 11px; color: #999; font-weight: 600;
}

.calendar-day.active .day-name,
.calendar-day.active .day-number { color: white; }

.day-number {
  font-size: 16px; font-weight: 800; color: #1a1a1a;
}

.calendar-day.today .day-number { color: #4CAF50; }
.calendar-day.active .day-number { color: white; }

.day-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: #4CAF50;
}
.calendar-day.active .day-dot { background: white; }

/* Weekly Calendar */
.week-list {
  display: flex; gap: 8px;
  padding: 12px 16px 16px; overflow-x: auto;
}
.week-list::-webkit-scrollbar { display: none; }

.week-item {
  display: flex; flex-direction: column;
  align-items: center; gap: 4px;
  padding: 10px 16px; border-radius: 12px;
  background: #f8f9fa; cursor: pointer;
  white-space: nowrap; transition: all 0.2s;
  min-width: 80px;
}

.week-item.active { background: #4CAF50; }

.week-label {
  font-size: 13px; font-weight: 700; color: #333;
}
.week-item.active .week-label { color: white; }

.week-range {
  font-size: 12px; color: #999;
}
.week-item.active .week-range { color: rgba(255,255,255,0.8); }

/* History Container */
.history-container { padding: 16px; padding-bottom: 80px; }

/* Period Summary */
.period-summary {
  background: white; border-radius: 16px;
  padding: 20px; margin-bottom: 16px;
  display: flex; justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.summary-label { font-size: 13px; color: #666; margin: 0 0 6px 0; }
.summary-amount { font-size: 32px; font-weight: 800; color: #1a1a1a; margin: 0; }

/* Mini Chart */
.mini-chart {
  display: flex; align-items: flex-end;
  gap: 4px; height: 60px;
}

.mini-bar {
  width: 12px; background: #C8E6C9;
  border-radius: 3px; transition: height 0.3s;
  min-height: 4px;
}
.mini-bar.active { background: #4CAF50; }

.mini-chart-labels {
  display: flex; gap: 4px; margin-top: 4px;
}
.mini-chart-labels span { width: 12px; font-size: 9px; color: #999; text-align: center; }

/* Jobs Completed Header */
.jobs-completed-header {
  padding: 12px 0 8px;
}
.jobs-count {
  font-size: 13px; font-weight: 800;
  color: #999; letter-spacing: 0.5px;
}

/* Jobs List */
.jobs-list { display: flex; flex-direction: column; gap: 12px; }

.job-card {
  background: white; border-radius: 14px;
  padding: 16px; display: flex;
  justify-content: space-between;
  align-items: flex-start; gap: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  cursor: pointer; transition: transform 0.2s;
}
.job-card:active { transform: scale(0.98); }

.job-left { flex: 1; min-width: 0; }

.job-time { font-size: 13px; color: #999; margin-bottom: 4px; }
.job-name { font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
.job-address {
  font-size: 13px; color: #666; margin-bottom: 8px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.job-tags { display: flex; gap: 6px; flex-wrap: wrap; }

.payment-tag, .tipped-tag, .bonus-tag {
  padding: 3px 10px; border-radius: 10px;
  font-size: 11px; font-weight: 700;
}

.payment-tag.cash { background: #E8F5E9; color: #4CAF50; }
.payment-tag.gcash { background: #E3F2FD; color: #2196F3; }

.tipped-tag { background: #FFF3E0; color: #FF9800; }
.bonus-tag { background: #F3E5F5; color: #9C27B0; }

.job-right {
  display: flex; align-items: center; gap: 4px;
  flex-shrink: 0;
}

.job-amount { font-size: 16px; font-weight: 800; color: #1a1a1a; }
.job-arrow { font-size: 18px; color: #ccc; }

/* Empty State */
.empty-state {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 60px 20px; text-align: center;
}

.empty-icon-wrap {
  width: 80px; height: 80px;
  background: #f0f0f0; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 16px;
}
.empty-icon-wrap ion-icon { font-size: 40px; color: #999; }
.empty-state h3 { font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0; }

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