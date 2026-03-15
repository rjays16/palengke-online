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
          <div 
            class="period-tab" 
            :class="{ active: selectedPeriod === 'today' }"
            @click="selectedPeriod = 'today'"
          >
            Today
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
          <div 
            class="period-tab" 
            :class="{ active: selectedPeriod === 'year' }"
            @click="selectedPeriod = 'year'"
          >
            This Year
          </div>
        </div>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div class="analytics-container">

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
                <div class="revenue-change negative">
                  <ion-icon :icon="arrowDownOutline"></ion-icon>
                  <span>2.5%</span>
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
              <line x1="0" y1="60" x2="800" y2="60" stroke="#f0f0f0" stroke-width="1"/>
              <line x1="0" y1="120" x2="800" y2="120" stroke="#f0f0f0" stroke-width="1"/>
              <line x1="0" y1="180" x2="800" y2="180" stroke="#f0f0f0" stroke-width="1"/>
              <line x1="0" y1="240" x2="800" y2="240" stroke="#f0f0f0" stroke-width="1"/>
              
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
              <text x="50" y="290" font-size="12" fill="#999" text-anchor="middle">Mon</text>
              <text x="166" y="290" font-size="12" fill="#999" text-anchor="middle">Tue</text>
              <text x="282" y="290" font-size="12" fill="#999" text-anchor="middle">Wed</text>
              <text x="398" y="290" font-size="12" fill="#999" text-anchor="middle">Thu</text>
              <text x="514" y="290" font-size="12" fill="#999" text-anchor="middle">Fri</text>
              <text x="630" y="290" font-size="12" fill="#999" text-anchor="middle">Sat</text>
              <text x="746" y="290" font-size="12" fill="#999" text-anchor="middle">Sun</text>
            </svg>
          </div>
        </div>

        <!-- Top Products -->
        <div class="section">
          <h2 class="section-title">Top Selling Products</h2>
          
          <div class="products-list">
            <div 
              v-for="(product, index) in topProducts" 
              :key="product.id"
              class="product-item"
            >
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
              <div class="product-percentage">
                {{ product.percentage }}%
              </div>
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
            <div 
              v-for="category in categories" 
              :key="category.name"
              class="category-bar"
            >
              <div class="category-header">
                <span class="category-name">{{ category.name }}</span>
                <span class="category-amount">₱{{ category.sales.toLocaleString() }}</span>
              </div>
              <div class="category-progress">
                <div 
                  class="category-fill" 
                  :class="category.color"
                  :style="{ width: category.percentage + '%' }"
                ></div>
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
            <div 
              v-for="(activity, index) in recentActivity" 
              :key="index"
              class="activity-item"
            >
              <div class="activity-icon" :class="activity.type">
                <ion-icon :icon="activity.icon"></ion-icon>
              </div>
              <div class="activity-content">
                <p class="activity-text">{{ activity.text }}</p>
                <p class="activity-time">{{ activity.time }}</p>
              </div>
              <div class="activity-amount" v-if="activity.amount">
                ₱{{ activity.amount.toLocaleString() }}
              </div>
            </div>
          </div>
        </div>

        <!-- Goals -->
        <div class="section">
          <h2 class="section-title">Monthly Goals</h2>
          
          <div class="goals-list">
            <div class="goal-item">
              <div class="goal-header">
                <span class="goal-name">Sales Target</span>
                <span class="goal-progress-text">₱{{ currentStats.totalSales.toLocaleString() }} / ₱50,000</span>
              </div>
              <div class="goal-bar">
                <div 
                  class="goal-fill green" 
                  :style="{ width: (currentStats.totalSales / 50000 * 100) + '%' }"
                ></div>
              </div>
              <p class="goal-status">{{ Math.round(currentStats.totalSales / 50000 * 100) }}% achieved</p>
            </div>

            <div class="goal-item">
              <div class="goal-header">
                <span class="goal-name">Orders Target</span>
                <span class="goal-progress-text">{{ currentStats.totalOrders }} / 100</span>
              </div>
              <div class="goal-bar">
                <div 
                  class="goal-fill blue" 
                  :style="{ width: (currentStats.totalOrders / 100 * 100) + '%' }"
                ></div>
              </div>
              <p class="goal-status">{{ Math.round(currentStats.totalOrders / 100 * 100) }}% achieved</p>
            </div>

            <div class="goal-item">
              <div class="goal-header">
                <span class="goal-name">Customer Satisfaction</span>
                <span class="goal-progress-text">{{ currentStats.rating }} / 5.0</span>
              </div>
              <div class="goal-bar">
                <div 
                  class="goal-fill orange" 
                  :style="{ width: (currentStats.rating / 5 * 100) + '%' }"
                ></div>
              </div>
              <p class="goal-status">{{ Math.round(currentStats.rating / 5 * 100) }}% achieved</p>
            </div>
          </div>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
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
  toastController
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
  closeCircleOutline
} from 'ionicons/icons';

const router = useRouter();

// State
const selectedPeriod = ref('week');

// Mock Stats Data
const currentStats = ref({
  totalSales: 18750,
  salesGrowth: 12.5,
  totalOrders: 68,
  ordersGrowth: 8.3,
  avgOrderValue: 275,
  completionRate: 94,
  avgDeliveryTime: 45,
  rating: 4.8,
  customers: 142
});

// Chart Data (7 days)
const salesData = ref([
  { day: 'Mon', sales: 2500, orders: 12 },
  { day: 'Tue', sales: 3200, orders: 15 },
  { day: 'Wed', sales: 2800, orders: 11 },
  { day: 'Thu', sales: 3500, orders: 14 },
  { day: 'Fri', sales: 3100, orders: 13 },
  { day: 'Sat', sales: 1900, orders: 8 },
  { day: 'Sun', sales: 1750, orders: 7 }
]);

// Computed chart points
const salesChartPoints = computed(() => {
  return salesData.value
    .map((data, index) => {
      const x = 50 + (index * 116);
      const y = 240 - (data.sales / 40);
      return `${x},${y}`;
    })
    .join(' ');
});

const ordersChartPoints = computed(() => {
  return salesData.value
    .map((data, index) => {
      const x = 50 + (index * 116);
      const y = 240 - (data.orders * 10);
      return `${x},${y}`;
    })
    .join(' ');
});

const salesPoints = computed(() => {
  return salesData.value.map((data, index) => ({
    x: 50 + (index * 116),
    y: 240 - (data.sales / 40)
  }));
});

// Top Products
const topProducts = ref([
  { id: 1, name: 'Tomatoes', sold: 45, revenue: 3600, percentage: 100 },
  { id: 2, name: 'Onions', sold: 38, revenue: 3040, percentage: 84 },
  { id: 3, name: 'Potatoes', sold: 32, revenue: 2880, percentage: 71 },
  { id: 4, name: 'Carrots', sold: 28, revenue: 2240, percentage: 62 },
  { id: 5, name: 'Cabbage', sold: 22, revenue: 1760, percentage: 49 }
]);

// Categories
const categories = ref([
  { name: 'Vegetables', sales: 8500, orders: 35, percentage: 45, color: 'green' },
  { name: 'Fruits', sales: 6200, orders: 18, percentage: 33, color: 'orange' },
  { name: 'Meat', sales: 2800, orders: 10, percentage: 15, color: 'red' },
  { name: 'Seafood', sales: 1250, orders: 5, percentage: 7, color: 'blue' }
]);

// Recent Activity
const recentActivity = ref([
  { 
    type: 'sale', 
    icon: cartOutline, 
    text: 'New order from Maria Santos', 
    time: '5 mins ago',
    amount: 450
  },
  { 
    type: 'payment', 
    icon: cashOutline, 
    text: 'Payment received for Order #1234', 
    time: '15 mins ago',
    amount: 890
  },
  { 
    type: 'sale', 
    icon: cartOutline, 
    text: 'Order #1235 completed', 
    time: '1 hour ago',
    amount: 320
  },
  { 
    type: 'warning', 
    icon: alertCircleOutline, 
    text: 'Low stock: Tomatoes (5kg remaining)', 
    time: '2 hours ago'
  },
  { 
    type: 'cancelled', 
    icon: closeCircleOutline, 
    text: 'Order #1230 cancelled by customer', 
    time: '3 hours ago'
  }
]);

// Methods
const showDateFilter = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Select Period',
    buttons: [
      {
        text: 'Today',
        handler: () => {
          selectedPeriod.value = 'today';
        }
      },
      {
        text: 'This Week',
        handler: () => {
          selectedPeriod.value = 'week';
        }
      },
      {
        text: 'This Month',
        handler: () => {
          selectedPeriod.value = 'month';
        }
      },
      {
        text: 'This Year',
        handler: () => {
          selectedPeriod.value = 'year';
        }
      },
      {
        text: 'Custom Range',
        handler: () => {
          showCustomDatePicker();
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

const showCustomDatePicker = async () => {
  const alert = await alertController.create({
    header: 'Custom Date Range',
    message: 'Select custom date range for analytics',
    buttons: ['Cancel', 'Apply']
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
            position: 'top'
          });
          await toast.present();
        }
      },
      {
        text: 'Export as Excel',
        icon: downloadOutline,
        handler: async () => {
          const toast = await toastController.create({
            message: '<font-awesome-icon icon="chart-bar" /> Exporting Excel report...',
            duration: 2000,
            color: 'success',
            position: 'top'
          });
          await toast.present();
        }
      },
      {
        text: 'Share Report',
        icon: downloadOutline,
        handler: async () => {
          const toast = await toastController.create({
            message: '<font-awesome-icon icon="share-alt" /> Opening share options...',
            duration: 2000,
            color: 'primary',
            position: 'top'
          });
          await toast.present();
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
  color: #4CAF50;
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
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
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
  background: #E3F2FD;
  color: #2196F3;
}

.revenue-icon.tertiary {
  background: #FFF3E0;
  color: #FF9800;
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
  color: #4CAF50;
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
  background: #4CAF50;
}

.legend-dot.orders {
  background: #2196F3;
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
  background: linear-gradient(135deg, #FFD700, #FFA500);
  color: white;
}

.product-rank.rank-2 {
  background: linear-gradient(135deg, #C0C0C0, #A9A9A9);
  color: white;
}

.product-rank.rank-3 {
  background: linear-gradient(135deg, #CD7F32, #A0522D);
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
  background: linear-gradient(90deg, #4CAF50, #45a049);
  border-radius: 3px;
  transition: width 0.5s ease;
}

.product-percentage {
  font-size: 14px;
  font-weight: 700;
  color: #4CAF50;
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
  background: #E8F5E9;
  color: #4CAF50;
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
  color: #4CAF50;
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
  background: linear-gradient(90deg, #4CAF50, #45a049);
}

.category-fill.orange {
  background: linear-gradient(90deg, #FF9800, #F57C00);
}

.category-fill.red {
  background: linear-gradient(90deg, #f44336, #d32f2f);
}

.category-fill.blue {
  background: linear-gradient(90deg, #2196F3, #1976D2);
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
  background: #E8F5E9;
  color: #4CAF50;
}

.activity-icon.payment {
  background: #E3F2FD;
  color: #2196F3;
}

.activity-icon.warning {
  background: #FFF3E0;
  color: #FF9800;
}

.activity-icon.cancelled {
  background: #FFEBEE;
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
  color: #4CAF50;
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
  background: linear-gradient(90deg, #4CAF50, #45a049);
}

.goal-fill.blue {
  background: linear-gradient(90deg, #2196F3, #1976D2);
}

.goal-fill.orange {
  background: linear-gradient(90deg, #FF9800, #F57C00);
}

.goal-status {
  font-size: 12px;
  color: #999;
  margin: 0;
}

/* Responsive */
@media (max-width: 360px) {
  .metrics-grid {
    grid-template-columns: 1fr;
  }
  
  .revenue-amount {
    font-size: 24px;
  }
}
</style>