<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <div class="search-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input v-model="search" placeholder="Search drivers..." />
        </div>
      </div>
      <span class="result-count">{{ filteredDrivers.length }} drivers</span>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr><th>Driver</th><th>Vehicle</th><th>Plate #</th><th>Deliveries</th><th>Rating</th><th>Earnings</th><th>Status</th><th>Joined</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="d in paginatedDrivers" :key="d.id">
            <td>
              <div class="cell-driver">
                <div class="driver-avatar">{{ d.name.charAt(0) }}</div>
                <div><div class="name-cell">{{ d.name }}</div><div class="sub-cell">{{ d.phone }}</div></div>
              </div>
            </td>
            <td>{{ d.vehicle }}</td>
            <td>{{ d.plate }}</td>
            <td>{{ d.deliveries }}</td>
            <td><span class="stars">★</span> {{ d.rating }}</td>
            <td>₱{{ d.earnings.toLocaleString() }}</td>
            <td><span class="status-badge" :class="d.status">{{ d.status === 'active' ? 'Active' : d.status === 'idle' ? 'Idle' : 'Offline' }}</span></td>
            <td class="date-cell">{{ d.joined }}</td>
            <td><button class="action-btn" @click="goToDriver(d.id)">...</button></td>
          </tr>
        </tbody>
      </table>
      <PaginationBar v-model="driverPage" :total="filteredDrivers.length" :perPage="driverPerPage" />
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
const driversStore = useDriversStore()

const search = ref('')

const filteredDrivers = computed(() => {
  if (!search.value) return driversStore.drivers
  const q = search.value.toLowerCase()
  return driversStore.drivers.filter(d => d.name.toLowerCase().includes(q) || d.plate.toLowerCase().includes(q))
})

const driverPage = ref(1)
const driverPerPage = ref(10)
const paginatedDrivers = computed(() => {
  const start = (driverPage.value - 1) * driverPerPage.value
  return filteredDrivers.value.slice(start, start + driverPerPage.value)
})
watch(search, () => { driverPage.value = 1 })

const goToDriver = (id) => navigateTo('/drivers/' + id)
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.search-box { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; min-width: 260px; }
.search-box:focus-within { border-color: #059669; }
.search-box svg { width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0; }
.search-box input { border: none; background: transparent; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; width: 100%; }
.result-count { font-size: 13px; color: #9ca3af; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.cell-driver { display: flex; align-items: center; gap: 10px; }
.driver-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #2563eb); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: white; flex-shrink: 0; }
.name-cell { font-weight: 600; color: #111827; }
.sub-cell { font-size: 12px; color: #9ca3af; }
.date-cell { color: #9ca3af; }
.stars { color: #f59e0b; }
.action-btn { background: none; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 10px; cursor: pointer; color: #9ca3af; font-size: 16px; transition: all 0.15s; }
.action-btn:hover { border-color: #059669; color: #059669; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.active { background: #f0fdf4; color: #059669; }
.status-badge.idle { background: #fef3c7; color: #d97706; }
.status-badge.offline { background: #f3f4f6; color: #6b7280; }

@media (max-width: 768px) {
  .data-table { min-width: 750px; }
  .search-box { min-width: 160px; }
}
@media (max-width: 480px) {
  .page-toolbar { flex-direction: column; align-items: stretch; }
  .search-box { min-width: 0; width: 100%; }
  .data-table { min-width: 680px; }
}
</style>
