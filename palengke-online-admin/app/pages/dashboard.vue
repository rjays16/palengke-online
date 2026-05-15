<template>
  <div class="dashboard">
    <div class="stats-grid">
      <div class="stat-card" v-for="s in stats" :key="s.label">
        <div class="stat-header">
          <span class="stat-label">{{ s.label }}</span>
          <span class="stat-change" :class="s.up ? 'up' : 'down'">{{ s.change }}</span>
        </div>
        <span class="stat-value">{{ s.value }}</span>
        <span class="stat-period">{{ s.period }}</span>
      </div>
    </div>

    <div class="dash-grid-2">
      <div class="dash-card">
        <div class="card-head">
          <h3>Revenue Overview</h3>
          <span class="card-badge">This Month</span>
        </div>
        <div class="chart-placeholder">
          <div class="bar-chart">
            <div v-for="(b, i) in barData" :key="i" class="bar-wrap">
              <div class="bar" :style="{ height: b.pct + '%' }" :class="b.highlight ? 'highlight' : ''"></div>
              <span class="bar-lbl">{{ b.label }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="dash-card">
        <div class="card-head">
          <h3>Order Status</h3>
          <span class="card-badge">Today</span>
        </div>
        <div class="donut-placeholder">
          <div class="donut-ring">
            <svg viewBox="0 0 36 36">
              <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e5e7eb" stroke-width="3"/>
              <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#059669" stroke-width="3" stroke-dasharray="65, 100"/>
            </svg>
          </div>
          <div class="donut-legend">
            <div v-for="item in orderStatusData" :key="item.label" class="legend-item">
              <span class="legend-dot" :style="{ background: item.color }"></span>
              <span class="legend-lbl">{{ item.label }}</span>
              <span class="legend-val">{{ item.count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="dash-card">
      <div class="card-head">
        <h3>Recent Orders</h3>
        <NuxtLink to="/orders" class="card-link">View All</NuxtLink>
      </div>
      <table class="dash-table">
        <thead>
          <tr><th>Order ID</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
          <tr v-for="o in paginatedRecent" :key="o.id">
            <td class="order-id">#{{ o.id }}</td>
            <td>{{ o.customer }}</td>
            <td>{{ o.items }}</td>
            <td>₱{{ o.total.toLocaleString() }}</td>
            <td><span class="status-badge" :class="o.status">{{ o.status }}</span></td>
            <td class="date-cell">{{ o.date }}</td>
          </tr>
        </tbody>
      </table>
      <PaginationBar v-model="dashPage" :total="recentOrders.length" :perPage="dashPerPage" />
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
const { stats, barData, orderStatusData, recentOrders } = storeToRefs(useDashboardStore())

const dashPage = ref(1)
const dashPerPage = ref(5)
const paginatedRecent = computed(() => {
  const start = (dashPage.value - 1) * dashPerPage.value
  return recentOrders.value.slice(start, start + dashPerPage.value)
})
</script>

<style scoped>
.dashboard { display: flex; flex-direction: column; gap: 24px; }

.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.stat-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.stat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.stat-label { font-size: 13px; font-weight: 500; color: #6b7280; }
.stat-change { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 6px; }
.stat-change.up { background: #f0fdf4; color: #059669; }
.stat-change.down { background: #fef2f2; color: #dc2626; }
.stat-value { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 4px; }
.stat-period { font-size: 12px; color: #9ca3af; }

.dash-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.dash-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.card-head h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 600; color: #111827; }
.card-badge { font-size: 11px; font-weight: 500; color: #059669; background: #f0fdf4; padding: 4px 10px; border-radius: 6px; }
.card-link { font-size: 13px; font-weight: 500; color: #059669; text-decoration: none; }
.card-link:hover { text-decoration: underline; }

.chart-placeholder { height: 180px; display: flex; align-items: flex-end; }
.bar-chart { display: flex; align-items: flex-end; gap: 12px; width: 100%; height: 100%; padding-top: 20px; }
.bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end; }
.bar { width: 100%; max-width: 36px; background: #e5e7eb; border-radius: 4px 4px 0 0; transition: height 0.3s; min-height: 4px; }
.bar.highlight { background: #059669; }
.bar-lbl { font-size: 10px; color: #9ca3af; margin-top: 6px; }

.donut-placeholder { display: flex; align-items: center; gap: 32px; padding: 8px 0; }
.donut-ring { width: 120px; height: 120px; }
.donut-ring svg { width: 100%; height: 100%; }
.donut-legend { display: flex; flex-direction: column; gap: 10px; }
.legend-item { display: flex; align-items: center; gap: 8px; }
.legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.legend-lbl { font-size: 13px; color: #374151; flex: 1; }
.legend-val { font-size: 13px; font-weight: 600; color: #111827; }

.dash-table { width: 100%; border-collapse: collapse; }
.dash-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 12px; border-bottom: 1px solid #f3f4f6; }
.dash-table td { padding: 12px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.order-id { font-weight: 600; color: #059669; }
.date-cell { color: #9ca3af; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.delivered { background: #f0fdf4; color: #059669; }
.status-badge.transit { background: #eff6ff; color: #3b82f6; }
.status-badge.preparing { background: #fef3c7; color: #d97706; }
.status-badge.cancelled { background: #fef2f2; color: #dc2626; }

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .dash-grid-2 { grid-template-columns: 1fr; }
  .dash-card { overflow-x: auto; }
  .dash-table { min-width: 500px; }
}
@media (max-width: 480px) {
  .stats-grid { grid-template-columns: 1fr; }
  .stat-value { font-size: 22px; }
  .donut-placeholder { flex-direction: column; gap: 16px; }
}
</style>
