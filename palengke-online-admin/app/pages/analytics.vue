<template>
  <div class="page-shell">
    <div class="stats-grid">
      <div class="stat-card" v-for="s in stats" :key="s.label">
        <span class="stat-label">{{ s.label }}</span>
        <span class="stat-value">{{ s.value }}</span>
        <span class="stat-sub">{{ s.sub }}</span>
      </div>
    </div>

    <div class="dash-card">
      <div class="card-head">
        <h3>Monthly Revenue</h3>
        <span class="card-badge">2026</span>
      </div>
      <div class="chart-placeholder">
        <div class="bar-chart">
          <div v-for="(b, i) in monthlyRevenue" :key="i" class="bar-wrap">
            <div class="bar" :style="{ height: b.pct + '%' }" :class="{ highlight: b.highlight }"></div>
            <span class="bar-lbl">{{ b.label }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="dash-grid-3">
      <div class="dash-card">
        <div class="card-head"><h3>Top Sellers</h3></div>
        <div v-for="(s, i) in topSellers" :key="s.name" class="rank-row">
          <span class="rank-num">{{ i + 1 }}</span>
          <div class="rank-info">
            <span class="rank-name">{{ s.name }}</span>
            <span class="rank-revenue">₱{{ s.revenue.toLocaleString() }}</span>
          </div>
          <span class="rank-pct">{{ s.pct }}%</span>
        </div>
      </div>
      <div class="dash-card">
        <div class="card-head"><h3>Top Drivers</h3></div>
        <div v-for="(d, i) in topDrivers" :key="d.name" class="rank-row">
          <span class="rank-num">{{ i + 1 }}</span>
          <div class="rank-info">
            <span class="rank-name">{{ d.name }}</span>
            <span class="rank-revenue">{{ d.deliveries }} deliveries</span>
          </div>
          <span class="rank-pct">★{{ d.rating }}</span>
        </div>
      </div>
      <div class="dash-card">
        <div class="card-head"><h3>Popular Categories</h3></div>
        <div v-for="c in categories" :key="c.name" class="cat-row">
          <div class="cat-head">
            <span class="cat-name">{{ c.name }}</span>
            <span class="cat-count">{{ c.count }}%</span>
          </div>
          <div class="cat-bar-bg">
            <div class="cat-bar-fill" :style="{ width: c.count + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const stats = ref([
  { label: 'Total Revenue', value: '₱2.84M', sub: 'Year to date' },
  { label: 'Avg Order Value', value: '₱425', sub: 'This month' },
  { label: 'Total Orders', value: '3,847', sub: 'All time' },
  { label: 'Growth', value: '+24.8%', sub: 'vs last year' },
])

const monthlyRevenue = ref([
  { label: 'Jan', pct: 45, highlight: false },
  { label: 'Feb', pct: 52, highlight: false },
  { label: 'Mar', pct: 48, highlight: false },
  { label: 'Apr', pct: 65, highlight: false },
  { label: 'May', pct: 78, highlight: true },
  { label: 'Jun', pct: 72, highlight: false },
  { label: 'Jul', pct: 85, highlight: false },
  { label: 'Aug', pct: 80, highlight: false },
  { label: 'Sep', pct: 92, highlight: false },
  { label: 'Oct', pct: 88, highlight: false },
  { label: 'Nov', pct: 95, highlight: false },
  { label: 'Dec', pct: 100, highlight: false },
])

const topSellers = ref([
  { name: "Juan's Farm", revenue: 452000, pct: 28 },
  { name: 'Pedro Merchants', revenue: 384000, pct: 24 },
  { name: 'FreshMart', revenue: 321000, pct: 20 },
  { name: "Ana's Store", revenue: 256000, pct: 16 },
])

const topDrivers = ref([
  { name: 'Tina Fernandez', deliveries: 415, rating: 4.8 },
  { name: 'Mark Reyes', deliveries: 342, rating: 4.9 },
  { name: 'Nina Salvador', deliveries: 287, rating: 4.7 },
  { name: 'Wendy Pascual', deliveries: 273, rating: 4.6 },
])

const categories = ref([
  { name: 'Vegetables', count: 35 },
  { name: 'Meat & Poultry', count: 25 },
  { name: 'Fruits', count: 20 },
  { name: 'Seafood', count: 12 },
  { name: 'Bundles', count: 8 },
])
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 24px; }
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.stat-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.stat-label { display: block; font-size: 13px; font-weight: 500; color: #6b7280; margin-bottom: 6px; }
.stat-value { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 4px; }
.stat-sub { font-size: 12px; color: #9ca3af; }
.dash-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.card-head h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 600; color: #111827; }
.card-badge { font-size: 11px; font-weight: 500; color: #059669; background: #f0fdf4; padding: 4px 10px; border-radius: 6px; }
.chart-placeholder { height: 200px; display: flex; align-items: flex-end; }
.bar-chart { display: flex; align-items: flex-end; gap: 8px; width: 100%; height: 100%; padding-top: 20px; }
.bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end; }
.bar { width: 100%; max-width: 28px; background: #e5e7eb; border-radius: 4px 4px 0 0; min-height: 4px; }
.bar.highlight { background: #059669; }
.bar-lbl { font-size: 10px; color: #9ca3af; margin-top: 6px; }
.dash-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.rank-row, .cat-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f9fafb; }
.rank-row:last-child, .cat-row:last-child { border-bottom: none; }
.rank-num { width: 22px; height: 22px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #6b7280; flex-shrink: 0; }
.rank-info { flex: 1; }
.rank-name { display: block; font-size: 13px; font-weight: 600; color: #111827; }
.rank-revenue { display: block; font-size: 12px; color: #9ca3af; }
.rank-pct { font-size: 13px; font-weight: 600; color: #059669; }
.cat-head { display: flex; justify-content: space-between; width: 100%; margin-bottom: 4px; }
.cat-name { font-size: 13px; font-weight: 500; color: #374151; }
.cat-count { font-size: 13px; font-weight: 600; color: #059669; }
.cat-bar-bg { width: 100%; height: 6px; background: #f3f4f6; border-radius: 3px; overflow: hidden; }
.cat-bar-fill { height: 100%; background: #059669; border-radius: 3px; }

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .dash-grid-3 { grid-template-columns: 1fr; }
}
</style>
