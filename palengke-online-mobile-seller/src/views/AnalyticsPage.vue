<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>Analytics</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="showDateFilter">
            <ion-icon :icon="calendarOutline"></ion-icon>
          </ion-button>
          <ion-button @click="exportReport">
            <ion-icon :icon="downloadOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>

      <!-- Period Selector -->
      <ion-toolbar>
        <div class="period-selector">
          <div class="period-tab" :class="{ active: selectedPeriod === 'today' }" @click="selectedPeriod = 'today'">
            Today
          </div>
          <div class="period-tab" :class="{ active: selectedPeriod === 'week' }" @click="selectedPeriod = 'week'">
            This Week
          </div>
          <div class="period-tab" :class="{ active: selectedPeriod === 'month' }" @click="selectedPeriod = 'month'">
            This Month
          </div>
          <div class="period-tab" :class="{ active: selectedPeriod === 'year' }" @click="selectedPeriod = 'year'">
            This Year
          </div>
        </div>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div v-if="loading" class="loading-state">
        <ion-spinner name="crescent" color="success" style="width: 48px; height: 48px"></ion-spinner>
        <h3>Loading analytics...</h3>
      </div>

      <div v-else class="analytics-container">
        <!-- Revenue Overview -->
        <div class="section">
          <h2 class="section-title">Revenue Overview</h2>

          <div class="revenue-cards">
            <div class="revenue-card primary">
              <div class="revenue-icon">
                <ion-icon :icon="trendingUpOutline"></ion-icon>
              </div>
              <div class="revenue-content">
                <p class="revenue-label">Total Sales</p>
                <h3 class="revenue-amount">₱{{ currentStats.totalSales.toLocaleString() }}</h3>
                <div class="revenue-change positive">
                  <ion-icon :icon="arrowUpOutline"></ion-icon>
                  <span>{{ currentStats.salesGrowth }}% vs last period</span>
                </div>
              </div>
            </div>

            <div class="revenue-card">
              <div class="revenue-icon secondary">
                <ion-icon :icon="receiptOutline"></ion-icon>
              </div>
              <div class="revenue-content">
                <p class="revenue-label">Orders</p>
                <h3 class="revenue-amount">{{ currentStats.totalOrders }}</h3>
                <div class="revenue-change positive">
                  <ion-icon :icon="arrowUpOutline"></ion-icon>
                  <span>{{ currentStats.ordersGrowth }}%</span>
                </div>
              </div>
            </div>

            <div class="revenue-card">
              <div class="revenue-icon tertiary">
                <ion-icon :icon="walletOutline"></ion-icon>
              </div>
              <div class="revenue-content">
                <p class="revenue-label">Avg. Order Value</p>
                <h3 class="revenue-amount">₱{{ currentStats.avgOrderValue.toLocaleString() }}</h3>
                <div class="revenue-change" :class="avgOrderValueGrowth >= 0 ? 'positive' : 'negative'">
                  <ion-icon :icon="avgOrderValueGrowth >= 0 ? arrowUpOutline : arrowDownOutline"></ion-icon>
                  <span>{{ Math.abs(avgOrderValueGrowth) }}% vs last period</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sales Chart -->
        <div class="section">
          <div class="section-header">
            <h2 class="section-title">Sales Trend</h2>
            <div class="chart-legend">
              <div class="legend-item">
                <span class="legend-dot sales"></span>
                <span>Sales</span>
              </div>
              <div class="legend-item">
                <span class="legend-dot orders"></span>
                <span>Orders</span>
              </div>
            </div>
          </div>

          <div class="chart-container">
            <!-- Simple SVG Chart -->
            <svg viewBox="0 0 800 300" class="sales-chart">
              <!-- Grid lines -->
              <line x1="0" y1="60" x2="800" y2="60" stroke="#f0f0f0" stroke-width="1" />
              <line x1="0" y1="120" x2="800" y2="120" stroke="#f0f0f0" stroke-width="1" />
              <line x1="0" y1="180" x2="800" y2="180" stroke="#f0f0f0" stroke-width="1" />
              <line x1="0" y1="240" x2="800" y2="240" stroke="#f0f0f0" stroke-width="1" />

              <!-- Sales line -->
              <polyline
                :points="salesChartPoints"
                fill="none"
                stroke="#4CAF50"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
              />

              <!-- Orders line -->
              <polyline
                :points="ordersChartPoints"
                fill="none"
                stroke="#2196F3"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-dasharray="5,5"
              />

              <!-- Data points -->
              <circle
                v-for="(point, index) in salesPoints"
                :key="'sales-' + index"
                :cx="point.x"
                :cy="point.y"
                r="5"
                fill="#4CAF50"
              />

              <!-- Labels -->
              <text
                v-for="(data, index) in salesData"
                :key="'label-' + index"
                :x="50 + index * 116"
                y="290"
                font-size="12"
                fill="#999"
                text-anchor="middle"
              >
                {{ data.day }}
              </text>
            </svg>
          </div>
        </div>

        <!-- Top Products -->
        <div class="section">
          <h2 class="section-title">Top Selling Products</h2>

          <div class="products-list">
            <div v-for="(product, index) in topProducts" :key="product.id" class="product-item">
              <div class="product-rank" :class="'rank-' + (index + 1)">
                {{ index + 1 }}
              </div>
              <div class="product-info">
                <h4 class="product-name">{{ product.name }}</h4>
                <p class="product-meta">{{ product.sold }} sold • ₱{{ product.revenue.toLocaleString() }}</p>
              </div>
              <div class="product-chart">
                <div class="mini-bar" :style="{ width: product.percentage + '%' }"></div>
              </div>
              <div class="product-percentage">{{ product.percentage }}%</div>
            </div>
          </div>
        </div>

        <!-- Performance Metrics -->
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-icon">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
            </div>
            <div class="metric-content">
              <p class="metric-value">{{ currentStats.completionRate }}%</p>
              <p class="metric-label">Completion Rate</p>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon">
              <ion-icon :icon="timeOutline"></ion-icon>
            </div>
            <div class="metric-content">
              <p class="metric-value">{{ currentStats.avgDeliveryTime }}m</p>
              <p class="metric-label">Avg. Delivery</p>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon">
              <ion-icon :icon="starOutline"></ion-icon>
            </div>
            <div class="metric-content">
              <p class="metric-value">{{ currentStats.rating }}</p>
              <p class="metric-label">Customer Rating</p>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon">
              <ion-icon :icon="peopleOutline"></ion-icon>
            </div>
            <div class="metric-content">
              <p class="metric-value">{{ currentStats.customers }}</p>
              <p class="metric-label">Total Customers</p>
            </div>
          </div>
        </div>

        <!-- Sales by Category -->
        <div class="section">
          <h2 class="section-title">Sales by Category</h2>

          <div class="category-chart">
            <div v-for="category in categories" :key="category.name" class="category-bar">
              <div class="category-header">
                <span class="category-name">{{ category.name }}</span>
                <span class="category-amount">₱{{ category.sales.toLocaleString() }}</span>
              </div>
              <div class="category-progress">
                <div class="category-fill" :class="category.color" :style="{ width: category.percentage + '%' }"></div>
              </div>
              <div class="category-meta">
                <span>{{ category.orders }} orders</span>
                <span>{{ category.percentage }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="section">
          <h2 class="section-title">Recent Activity</h2>

          <div class="activity-timeline">
            <div v-for="(activity, index) in recentActivity" :key="index" class="activity-item">
              <div class="activity-icon" :class="activity.type">
                <ion-icon :icon="activity.icon"></ion-icon>
              </div>
              <div class="activity-content">
                <p class="activity-text">{{ activity.text }}</p>
                <p class="activity-time">{{ activity.time }}</p>
              </div>
              <div class="activity-amount" v-if="activity.amount">₱{{ activity.amount.toLocaleString() }}</div>
            </div>
          </div>
        </div>

        <!-- Goals -->
        <div class="section">
          <h2 class="section-title">Monthly Goals</h2>

          <div class="goals-list">
            <div class="goal-item">
              <div class="goal-header">
                <span class="goal-name">
                  Sales Target
                  <ion-icon :icon="createOutline" class="edit-goal-icon" @click="editGoal('sales')"></ion-icon>
                </span>
                <span class="goal-progress-text"
                  >₱{{ monthlyGoals.currentSales.toLocaleString() }} / ₱{{
                    monthlyGoals.salesTarget.toLocaleString()
                  }}</span
                >
              </div>
              <div class="goal-bar">
                <div
                  class="goal-fill green"
                  :style="{ width: (monthlyGoals.currentSales / monthlyGoals.salesTarget) * 100 + '%' }"
                ></div>
              </div>
              <p class="goal-status">
                {{
                  monthlyGoals.salesTarget > 0
                    ? Math.round((monthlyGoals.currentSales / monthlyGoals.salesTarget) * 100)
                    : 0
                }}% achieved
              </p>
            </div>

            <div class="goal-item">
              <div class="goal-header">
                <span class="goal-name">
                  Orders Target
                  <ion-icon :icon="createOutline" class="edit-goal-icon" @click="editGoal('orders')"></ion-icon>
                </span>
                <span class="goal-progress-text"
                  >{{ monthlyGoals.currentOrders }} / {{ monthlyGoals.ordersTarget }}</span
                >
              </div>
              <div class="goal-bar">
                <div
                  class="goal-fill blue"
                  :style="{ width: (monthlyGoals.currentOrders / monthlyGoals.ordersTarget) * 100 + '%' }"
                ></div>
              </div>
              <p class="goal-status">
                {{
                  monthlyGoals.ordersTarget > 0
                    ? Math.round((monthlyGoals.currentOrders / monthlyGoals.ordersTarget) * 100)
                    : 0
                }}% achieved
              </p>
            </div>

            <div class="goal-item">
              <div class="goal-header">
                <span class="goal-name">Customer Satisfaction</span>
                <span class="goal-progress-text">{{ currentStats.rating }} / 5.0</span>
              </div>
              <div class="goal-bar">
                <div class="goal-fill orange" :style="{ width: (currentStats.rating / 5) * 100 + '%' }"></div>
              </div>
              <p class="goal-status">{{ Math.round((currentStats.rating / 5) * 100) }}% achieved</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Date Range Modal -->
      <ion-modal :is-open="showDateModal" @did-dismiss="showDateModal = false">
        <ion-header>
          <ion-toolbar color="success">
            <ion-title>Custom Date Range</ion-title>
            <ion-buttons slot="end">
              <ion-button @click="showDateModal = false">Close</ion-button>
            </ion-buttons>
          </ion-toolbar>
        </ion-header>
        <ion-content>
          <div class="date-range-modal">
            <div class="date-field">
              <label class="date-label">Start Date</label>
              <input type="date" class="date-input" v-model="startDate" />
            </div>
            <div class="date-field">
              <label class="date-label">End Date</label>
              <input type="date" class="date-input" v-model="endDate" />
            </div>
            <ion-button expand="block" color="success" @click="applyCustomRange" class="apply-btn">
              Apply Range
            </ion-button>
          </div>
        </ion-content>
      </ion-modal>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { api } from '@/utils/apiClient';
import { ref, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonButton,
  IonBackButton,
  IonIcon,
  actionSheetController,
  alertController,
  toastController,
  IonSpinner,
  IonModal,
} from '@ionic/vue';
import {
  calendarOutline,
  downloadOutline,
  trendingUpOutline,
  receiptOutline,
  walletOutline,
  arrowUpOutline,
  arrowDownOutline,
  checkmarkCircleOutline,
  timeOutline,
  starOutline,
  peopleOutline,
  cartOutline,
  cashOutline,
  alertCircleOutline,
  closeCircleOutline,
  informationCircleOutline,
  createOutline,
} from 'ionicons/icons';
import { type AnalyticsStats, type ChartDataPoint, type TopProduct, type CategorySales, type Activity, type MonthlyGoals } from '@/types';
const router = useRouter();

// State
const selectedPeriod = ref('week');
const loading = ref(true);

const currentStats = ref({
  totalSales: 0,
  salesGrowth: 0,
  totalOrders: 0,
  ordersGrowth: 0,
  avgOrderValue: 0,
  avgOrderValueGrowth: 0,
  completionRate: 0,
  avgDeliveryTime: 0,
  rating: 0,
  customers: 0,
} as AnalyticsStats);

const avgOrderValueGrowth = computed(() => currentStats.value.avgOrderValueGrowth);

const salesData = ref([] as ChartDataPoint[]);
const topProducts = ref([] as TopProduct[]);
const categories = ref([] as CategorySales[]);
const recentActivity = ref([] as Activity[]);
const monthlyGoals = ref({
  salesTarget: 50000,
  ordersTarget: 100,
  satisfactionTarget: 5.0,
  currentSales: 0,
  currentOrders: 0,
} as MonthlyGoals);

const showDateModal = ref(false);
const startDate = ref('');
const endDate = ref('');

const loadAnalytics = async () => {
  loading.value = true;
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  try {
    let url = `/seller/analytics?email=${encodeURIComponent(email)}`;
    if (startDate.value && endDate.value) {
      url += `&start_date=${startDate.value}&end_date=${endDate.value}`;
    } else {
      url += `&period=${selectedPeriod.value}`;
    }
    const data = await api.get(url);
    currentStats.value = data.stats;
    salesData.value = data.chartData;
    topProducts.value = data.topProducts;
    categories.value = data.categorySales;
    recentActivity.value = data.recentActivity.map((a: any) => ({
      ...a,
      icon: getActivityIcon(a.type as string),
    }));
    monthlyGoals.value = data.monthlyGoals;
  } catch {
    // silent
  } finally {
    loading.value = false;
  }
};

loadAnalytics();

watch(selectedPeriod, () => {
  loadAnalytics();
});

function getActivityIcon(type: string) {
  const map: Record<string, any> = {
    sale: cartOutline,
    payment: cashOutline,
    warning: alertCircleOutline,
    cancelled: closeCircleOutline,
  };
  return map[type] || informationCircleOutline;
}

// Computed chart points
const salesChartPoints = computed(() => {
  return salesData.value
    .map((data, index) => {
      const x = 50 + index * 116;
      const y = 240 - data.sales / 40;
      return `${x},${y}`;
    })
    .join(' ');
});

const ordersChartPoints = computed(() => {
  return salesData.value
    .map((data, index) => {
      const x = 50 + index * 116;
      const y = 240 - data.orders * 10;
      return `${x},${y}`;
    })
    .join(' ');
});

const salesPoints = computed(() => {
  return salesData.value.map((data, index) => ({
    x: 50 + index * 116,
    y: 240 - data.sales / 40,
  }));
});

// Methods
const showDateFilter = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Select Period',
    buttons: [
      {
        text: 'Custom Range',
        handler: () => {
          showCustomDatePicker();
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

const showCustomDatePicker = () => {
  const today = new Date().toISOString().split('T')[0];
  const sevenDaysAgo = new Date(Date.now() - 7 * 86400000).toISOString().split('T')[0];
  startDate.value = sevenDaysAgo;
  endDate.value = today;
  showDateModal.value = true;
};

const applyCustomRange = async () => {
  if (!startDate.value || !endDate.value) {
    const toast = await toastController.create({
      message: 'Please select both start and end dates',
      duration: 2000,
      color: 'warning',
      position: 'top',
    });
    await toast.present();
    return;
  }
  if (startDate.value > endDate.value) {
    const toast = await toastController.create({
      message: 'Start date must be before end date',
      duration: 2000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
    return;
  }
  showDateModal.value = false;
  await loadAnalytics();
};

const editGoal = async (type: string) => {
  const isSales = type === 'sales';
  const current = isSales ? monthlyGoals.value.salesTarget : monthlyGoals.value.ordersTarget;
  const label = isSales ? 'Monthly Sales Target (₱)' : 'Monthly Orders Target';
  const alert = await alertController.create({
    header: `Edit ${label}`,
    inputs: [
      {
        type: 'number',
        name: 'value',
        value: current,
        min: 0,
        placeholder: isSales ? 'e.g. 50000' : 'e.g. 100',
      },
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Save',
        handler: async (data) => {
          const val = parseFloat(data.value);
          if (isNaN(val) || val < 0) {
            const toast = await toastController.create({
              message: 'Please enter a valid number',
              duration: 2000,
              color: 'warning',
              position: 'top',
            });
            await toast.present();
            return false;
          }
          const email = localStorage.getItem('userEmail');
          if (!email) return false;
          const body = isSales
            ? { email, monthly_sales_target: val, monthly_orders_target: monthlyGoals.value.ordersTarget }
            : { email, monthly_sales_target: monthlyGoals.value.salesTarget, monthly_orders_target: val };
          try {
            await api.post('/seller/goals', body);
            const toast = await toastController.create({
              message: 'Goals updated successfully!',
              duration: 2000,
              color: 'success',
              position: 'top',
            });
            await toast.present();
            await loadAnalytics();
          } catch {
            // silent
          }
          return true;
        },
      },
    ],
  });
  await alert.present();
};

const exportReport = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Export Report',
    buttons: [
      {
        text: 'Export as PDF',
        icon: downloadOutline,
        handler: async () => {
          const toast = await toastController.create({
            message: '<font-awesome-icon icon="file-alt" /> Exporting PDF report...',
            duration: 2000,
            color: 'success',
            position: 'top',
          });
          await toast.present();
        },
      },
      {
        text: 'Export as Excel',
        icon: downloadOutline,
        handler: async () => {
          const toast = await toastController.create({
            message: '<font-awesome-icon icon="chart-bar" /> Exporting Excel report...',
            duration: 2000,
            color: 'success',
            position: 'top',
          });
          await toast.present();
        },
      },
      {
        text: 'Share Report',
        icon: downloadOutline,
        handler: async () => {
          const toast = await toastController.create({
            message: '<font-awesome-icon icon="share-alt" /> Opening share options...',
            duration: 2000,
            color: 'primary',
            position: 'top',
          });
          await toast.present();
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
</script>

<style scoped>
/* Container */
.analytics-container {
  padding: 16px;
  background: #f8f9fa;
  padding-bottom: 40px;
}

/* Period Selector */
.period-selector {
  display: flex;
  padding: 8px 16px;
  gap: 8px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.period-tab {
  flex: 1;
  min-width: fit-content;
  text-align: center;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  color: #666;
  background: rgba(255, 255, 255, 0.5);
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s;
  white-space: nowrap;
}

.period-tab.active {
  background: white;
  color: #4caf50;
  box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
}

/* Section */
.section {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

/* Revenue Cards */
.revenue-cards {
  display: grid;
  gap: 12px;
}

.revenue-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 16px;
  display: flex;
  gap: 16px;
  align-items: center;
}

.revenue-card.primary {
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  color: white;
}

.revenue-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.revenue-icon.secondary {
  background: #e3f2fd;
  color: #2196f3;
}

.revenue-icon.tertiary {
  background: #fff3e0;
  color: #ff9800;
}

.revenue-content {
  flex: 1;
}

.revenue-label {
  font-size: 13px;
  opacity: 0.9;
  margin: 0 0 4px 0;
}

.revenue-amount {
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 6px 0;
}

.revenue-card:not(.primary) .revenue-amount {
  color: #1a1a1a;
}

.revenue-change {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  font-weight: 600;
}

.revenue-change.positive {
  color: rgba(255, 255, 255, 0.9);
}

.revenue-card:not(.primary) .revenue-change.positive {
  color: #4caf50;
}

.revenue-change.negative {
  color: rgba(255, 255, 255, 0.7);
}

.revenue-card:not(.primary) .revenue-change.negative {
  color: #f44336;
}

/* Chart */
.chart-container {
  margin: 16px 0;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.sales-chart {
  width: 100%;
  height: auto;
  min-width: 600px;
}

.chart-legend {
  display: flex;
  gap: 16px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #666;
}

.legend-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.legend-dot.sales {
  background: #4caf50;
}

.legend-dot.orders {
  background: #2196f3;
}

/* Top Products */
.products-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.product-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 12px;
}

.product-rank {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 700;
  background: #e0e0e0;
  color: #666;
}

.product-rank.rank-1 {
  background: linear-gradient(135deg, #ffd700, #ffa500);
  color: white;
}

.product-rank.rank-2 {
  background: linear-gradient(135deg, #c0c0c0, #a9a9a9);
  color: white;
}

.product-rank.rank-3 {
  background: linear-gradient(135deg, #cd7f32, #a0522d);
  color: white;
}

.product-info {
  flex: 1;
  min-width: 0;
}

.product-name {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.product-meta {
  font-size: 12px;
  color: #999;
  margin: 0;
}

.product-chart {
  flex: 1;
  height: 6px;
  background: #e0e0e0;
  border-radius: 3px;
  overflow: hidden;
}

.mini-bar {
  height: 100%;
  background: linear-gradient(90deg, #4caf50, #45a049);
  border-radius: 3px;
  transition: width 0.5s ease;
}

.product-percentage {
  font-size: 14px;
  font-weight: 700;
  color: #4caf50;
  min-width: 45px;
  text-align: right;
}

/* Metrics Grid */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 16px;
}

.metric-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.metric-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #e8f5e9;
  color: #4caf50;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  margin-bottom: 12px;
}

.metric-value {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.metric-label {
  font-size: 13px;
  color: #666;
  margin: 0;
}

/* Category Chart */
.category-chart {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.category-bar {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.category-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.category-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.category-amount {
  font-size: 14px;
  font-weight: 700;
  color: #4caf50;
}

.category-progress {
  height: 10px;
  background: #f0f0f0;
  border-radius: 5px;
  overflow: hidden;
}

.category-fill {
  height: 100%;
  border-radius: 5px;
  transition: width 0.5s ease;
}

.category-fill.green {
  background: linear-gradient(90deg, #4caf50, #45a049);
}

.category-fill.orange {
  background: linear-gradient(90deg, #ff9800, #f57c00);
}

.category-fill.red {
  background: linear-gradient(90deg, #f44336, #d32f2f);
}

.category-fill.blue {
  background: linear-gradient(90deg, #2196f3, #1976d2);
}

.category-meta {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #999;
}

/* Activity Timeline */
.activity-timeline {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 12px;
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.activity-icon.sale {
  background: #e8f5e9;
  color: #4caf50;
}

.activity-icon.payment {
  background: #e3f2fd;
  color: #2196f3;
}

.activity-icon.warning {
  background: #fff3e0;
  color: #ff9800;
}

.activity-icon.cancelled {
  background: #ffebee;
  color: #f44336;
}

.activity-content {
  flex: 1;
  min-width: 0;
}

.activity-text {
  font-size: 14px;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.activity-time {
  font-size: 12px;
  color: #999;
  margin: 0;
}

.activity-amount {
  font-size: 16px;
  font-weight: 700;
  color: #4caf50;
}

/* Goals */
.goals-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.goal-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.goal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.goal-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  display: flex;
  align-items: center;
  gap: 6px;
}

.edit-goal-icon {
  font-size: 16px;
  color: #999;
  cursor: pointer;
  transition: color 0.2s;
}

.edit-goal-icon:hover {
  color: #4caf50;
}

.goal-progress-text {
  font-size: 13px;
  color: #666;
}

.goal-bar {
  height: 12px;
  background: #f0f0f0;
  border-radius: 6px;
  overflow: hidden;
}

.goal-fill {
  height: 100%;
  border-radius: 6px;
  transition: width 0.5s ease;
}

.goal-fill.green {
  background: linear-gradient(90deg, #4caf50, #45a049);
}

.goal-fill.blue {
  background: linear-gradient(90deg, #2196f3, #1976d2);
}

.goal-fill.orange {
  background: linear-gradient(90deg, #ff9800, #f57c00);
}

.goal-status {
  font-size: 12px;
  color: #999;
  margin: 0;
}

/* Responsive */
/* Date Range Modal */
.date-range-modal {
  padding: 24px 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.date-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.date-label {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.date-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 16px;
  font-family: inherit;
  color: #333;
  background: #f8f9fa;
  outline: none;
  transition: border-color 0.3s;
}

.date-input:focus {
  border-color: #4caf50;
}

.apply-btn {
  margin-top: 8px;
  --border-radius: 12px;
  font-weight: 600;
  height: 48px;
}

@media (max-width: 360px) {
  .metrics-grid {
    grid-template-columns: 1fr;
  }

  .revenue-amount {
    font-size: 24px;
  }
}
</style>
