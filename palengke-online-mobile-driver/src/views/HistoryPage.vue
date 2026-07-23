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
            v-for="(week, index) in weekList"
            :key="week.id"
            class="week-item"
            :class="{ active: selectedWeek === index }"
            @click="selectedWeek = index"
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
import { ref, computed, onMounted, watch } from 'vue';
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
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('token');
}

const viewMode = ref('daily');
const selectedDate = ref('');
const selectedWeek = ref(0);
const currentMonthLabel = ref('');

const dailyEarnings = ref({});
const jobsByDate = ref({});

const weekDays = ref([]);
const weekList = ref([]);
const miniChartData = ref([]);

const today = new Date();
const todayStr = today.toISOString().slice(0, 10);

onMounted(async () => {
  setSelectedDateToToday();
  await fetchHistory();
});

function setSelectedDateToToday() {
  const now = new Date();
  const dayOfWeek = now.getDay();
  const startOfWeek = new Date(now);
  startOfWeek.setDate(now.getDate() - dayOfWeek);

  const months = ['January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'];
  currentMonthLabel.value = months[now.getMonth()];

  const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const week = [];
  for (let i = 0; i < 7; i++) {
    const d = new Date(startOfWeek);
    d.setDate(startOfWeek.getDate() + i);
    const dateStr = d.toISOString().slice(0, 10);
    week.push({
      name: dayNames[i],
      number: d.getDate(),
      date: dateStr,
      isToday: dateStr === todayStr,
      earnings: 0,
    });
  }
  weekDays.value = week;
  selectedDate.value = todayStr;

  const weeksInMonth = [];
  const currentMonth = now.getMonth();
  const firstDay = new Date(now.getFullYear(), currentMonth, 1);
  const lastDay = new Date(now.getFullYear(), currentMonth + 1, 0);
  let weekStart = new Date(firstDay);
  let weekNum = 1;
  while (weekStart <= lastDay) {
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekStart.getDate() + 6);
    const cappedEnd = weekEnd > lastDay ? lastDay : weekEnd;
    weeksInMonth.push({
      id: `week${currentMonth}-${weekNum}`,
      label: `Week ${weekNum}`,
      range: `${weekStart.getDate()} - ${cappedEnd.getDate()}`,
      startDate: weekStart.toISOString().slice(0, 10),
      endDate: cappedEnd.toISOString().slice(0, 10),
    });
    weekStart = new Date(weekEnd);
    weekStart.setDate(weekStart.getDate() + 1);
    weekNum++;
  }
  weekList.value = weeksInMonth;

  const currentWeekIdx = weeksInMonth.findIndex(w =>
    todayStr >= w.startDate && todayStr <= w.endDate
  );
  selectedWeek.value = currentWeekIdx >= 0 ? currentWeekIdx : 0;
}

async function fetchHistory() {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/history`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      dailyEarnings.value = data.dailyEarnings || {};
      jobsByDate.value = data.jobsByDate || {};
      updateWeekDaysEarnings();
      updateMiniChart();
    }
  } catch {}
}

function updateWeekDaysEarnings() {
  weekDays.value = weekDays.value.map(day => ({
    ...day,
    earnings: dailyEarnings.value[day.date] || 0,
  }));
}

function updateMiniChart() {
  const week = weekList.value[selectedWeek.value];
  if (!week) {
    miniChartData.value = [];
    return;
  }
  const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const start = new Date(week.startDate + 'T00:00:00');
  const chart = [];
  for (let i = 0; i < 7; i++) {
    const d = new Date(start);
    d.setDate(start.getDate() + i);
    const dateStr = d.toISOString().slice(0, 10);
    chart.push({
      label: dayNames[i],
      amount: dailyEarnings.value[dateStr] || 0,
      isToday: dateStr === todayStr,
    });
  }
  miniChartData.value = chart;
}

const maxMiniBar = computed(() => Math.max(...miniChartData.value.map(d => d.amount)));
const getMiniBarHeight = (bar) => {
  if (maxMiniBar.value === 0) return 0;
  return Math.max((bar.amount / maxMiniBar.value) * 100, bar.amount > 0 ? 10 : 0);
};

const currentJobs = computed(() => {
  if (viewMode.value === 'daily') {
    return jobsByDate.value[selectedDate.value] || [];
  }
  const week = weekList.value[selectedWeek.value];
  if (!week) return [];
  const jobs = [];
  const start = new Date(week.startDate + 'T00:00:00');
  for (let i = 0; i < 7; i++) {
    const d = new Date(start);
    d.setDate(start.getDate() + i);
    const dateStr = d.toISOString().slice(0, 10);
    if (jobsByDate.value[dateStr]) {
      jobs.push(...jobsByDate.value[dateStr]);
    }
  }
  return jobs;
});

const currentPeriodEarnings = computed(() => {
  if (viewMode.value === 'daily') {
    return dailyEarnings.value[selectedDate.value] || 0;
  }
  const week = weekList.value[selectedWeek.value];
  if (!week) return 0;
  let total = 0;
  const start = new Date(week.startDate + 'T00:00:00');
  for (let i = 0; i < 7; i++) {
    const d = new Date(start);
    d.setDate(start.getDate() + i);
    const dateStr = d.toISOString().slice(0, 10);
    total += dailyEarnings.value[dateStr] || 0;
  }
  return total;
});

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

const goToHome = () => router.push('/home');
const goToOrders = () => router.push('/orders');
const goToEarnings = () => router.push('/earnings');
const goToProfile = () => router.push('/profile');

watch(selectedWeek, () => {
  updateMiniChart();
});
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