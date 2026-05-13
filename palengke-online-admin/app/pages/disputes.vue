<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <select v-model="statusFilter" class="filter-select">
          <option value="">All Status</option>
          <option value="open">Open</option>
          <option value="investigating">Investigating</option>
          <option value="resolved">Resolved</option>
        </select>
      </div>
      <span class="result-count">{{ filteredDisputes.length }} disputes</span>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr><th>ID</th><th>Order</th><th>Reported By</th><th>Against</th><th>Reason</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="d in filteredDisputes" :key="d.id">
            <td class="id-cell">#{{ d.id }}</td>
            <td class="id-cell">#{{ d.orderId }}</td>
            <td><span class="name-cell">{{ d.reportedBy }}</span></td>
            <td>{{ d.against }}</td>
            <td>{{ d.reason }}</td>
            <td><span class="priority-badge" :class="d.priority">{{ d.priority }}</span></td>
            <td><span class="status-badge" :class="d.status">{{ d.status }}</span></td>
            <td class="date-cell">{{ d.date }}</td>
            <td><button class="action-btn">...</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const statusFilter = ref('')

const disputes = ref([
  { id: 1, orderId: 'ORD-1038', reportedBy: 'Liza Morales', against: "Juan's Farm", reason: 'Wrong items delivered', priority: 'high', status: 'open', date: '2026-05-14' },
  { id: 2, orderId: 'ORD-1035', reportedBy: 'Ben Torres', against: 'Pedro Merchants', reason: 'Missing items', priority: 'medium', status: 'investigating', date: '2026-05-13' },
  { id: 3, orderId: 'ORD-1032', reportedBy: 'Maria Santos', against: "Ana's Store", reason: 'Damaged goods', priority: 'high', status: 'resolved', date: '2026-05-11' },
  { id: 4, orderId: 'ORD-1029', reportedBy: 'Carlo Bautista', against: 'FreshMart', reason: 'Late delivery', priority: 'low', status: 'open', date: '2026-05-10' },
  { id: 5, orderId: 'ORD-1027', reportedBy: 'Rosa Villanueva', against: "Liza's Shop", reason: 'Overcharged', priority: 'medium', status: 'resolved', date: '2026-05-08' },
  { id: 6, orderId: 'ORD-1025', reportedBy: 'Diana Rivera', against: "Juan's Farm", reason: 'Item not fresh', priority: 'high', status: 'investigating', date: '2026-05-06' },
])

const filteredDisputes = computed(() => {
  if (!statusFilter.value) return disputes.value
  return disputes.value.filter(d => d.status === statusFilter.value)
})
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; }
.filter-select { padding: 8px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #374151; background: #fff; cursor: pointer; outline: none; }
.filter-select:focus { border-color: #059669; }
.result-count { font-size: 13px; color: #9ca3af; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.id-cell { font-weight: 600; color: #059669; }
.name-cell { font-weight: 600; color: #111827; }
.date-cell { color: #9ca3af; }
.action-btn { background: none; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 10px; cursor: pointer; color: #9ca3af; font-size: 16px; transition: all 0.15s; }
.action-btn:hover { border-color: #059669; color: #059669; }
.priority-badge { padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
.priority-badge.high { background: #fef2f2; color: #dc2626; }
.priority-badge.medium { background: #fef3c7; color: #d97706; }
.priority-badge.low { background: #f3f4f6; color: #6b7280; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.open { background: #fef2f2; color: #dc2626; }
.status-badge.investigating { background: #fef3c7; color: #d97706; }
.status-badge.resolved { background: #f0fdf4; color: #059669; }
</style>
