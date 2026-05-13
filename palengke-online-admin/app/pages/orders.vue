<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <div class="search-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input v-model="search" placeholder="Search orders..." />
        </div>
        <select v-model="statusFilter" class="filter-select">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="preparing">Preparing</option>
          <option value="transit">In Transit</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
      <span class="result-count">{{ filteredOrders.length }} orders</span>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr><th>Order ID</th><th>Customer</th><th>Seller</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="o in filteredOrders" :key="o.id">
            <td class="id-cell">#{{ o.id }}</td>
            <td><span class="name-cell">{{ o.customer }}</span></td>
            <td>{{ o.seller }}</td>
            <td>{{ o.items }}</td>
            <td>₱{{ o.total.toLocaleString() }}</td>
            <td><span class="method-badge" :class="o.payment">{{ o.payment === 'cod' ? 'COD' : 'GCash' }}</span></td>
            <td><span class="status-badge" :class="o.status">{{ o.status }}</span></td>
            <td class="date-cell">{{ o.date }}</td>
            <td><button class="action-btn">...</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const search = ref('')
const statusFilter = ref('')

const orders = ref([
  { id: 'ORD-1042', customer: 'Maria Santos', seller: "Juan's Farm", items: 4, total: 1280, payment: 'gcash', status: 'delivered', date: '2026-05-14' },
  { id: 'ORD-1041', customer: 'Juan dela Cruz', seller: "Ana's Store", items: 2, total: 540, payment: 'cod', status: 'transit', date: '2026-05-14' },
  { id: 'ORD-1040', customer: 'Ana Gonzales', seller: 'Pedro Merchants', items: 6, total: 2150, payment: 'gcash', status: 'preparing', date: '2026-05-14' },
  { id: 'ORD-1039', customer: 'Pedro Reyes', seller: "Liza's Shop", items: 1, total: 180, payment: 'cod', status: 'delivered', date: '2026-05-13' },
  { id: 'ORD-1038', customer: 'Liza Morales', seller: "Juan's Farm", items: 3, total: 960, payment: 'gcash', status: 'cancelled', date: '2026-05-13' },
  { id: 'ORD-1037', customer: 'Carlo Bautista', seller: 'FreshMart', items: 5, total: 1640, payment: 'cod', status: 'pending', date: '2026-05-13' },
  { id: 'ORD-1036', customer: 'Rosa Villanueva', seller: "Ana's Store", items: 2, total: 320, payment: 'gcash', status: 'delivered', date: '2026-05-12' },
  { id: 'ORD-1035', customer: 'Ben Torres', seller: 'Pedro Merchants', items: 8, total: 2890, payment: 'cod', status: 'transit', date: '2026-05-12' },
  { id: 'ORD-1034', customer: 'Diana Rivera', seller: "Juan's Farm", items: 3, total: 750, payment: 'gcash', status: 'delivered', date: '2026-05-12' },
  { id: 'ORD-1033', customer: 'Mark Lim', seller: 'FreshMart', items: 4, total: 1120, payment: 'cod', status: 'preparing', date: '2026-05-11' },
])

const filteredOrders = computed(() => {
  return orders.value.filter(o => {
    const matchSearch = !search.value || o.id.toLowerCase().includes(search.value.toLowerCase()) || o.customer.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = !statusFilter.value || o.status === statusFilter.value
    return matchSearch && matchStatus
  })
})
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.search-box { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; min-width: 260px; }
.search-box:focus-within { border-color: #059669; }
.search-box svg { width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0; }
.search-box input { border: none; background: transparent; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; width: 100%; }
.filter-select { padding: 8px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #374151; background: #fff; outline: none; cursor: pointer; }
.filter-select:focus { border-color: #059669; }
.result-count { font-size: 13px; color: #9ca3af; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.id-cell { font-weight: 600; color: #059669; }
.name-cell { font-weight: 500; color: #111827; }
.date-cell { color: #9ca3af; }
.action-btn { background: none; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 10px; cursor: pointer; color: #9ca3af; font-size: 16px; transition: all 0.15s; }
.action-btn:hover { border-color: #059669; color: #059669; }
.method-badge { padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.method-badge.cod { background: #f3f4f6; color: #374151; }
.method-badge.gcash { background: #f0fdf4; color: #059669; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.delivered { background: #f0fdf4; color: #059669; }
.status-badge.transit { background: #eff6ff; color: #3b82f6; }
.status-badge.preparing { background: #fef3c7; color: #d97706; }
.status-badge.pending { background: #f3f4f6; color: #6b7280; }
.status-badge.cancelled { background: #fef2f2; color: #dc2626; }
</style>
