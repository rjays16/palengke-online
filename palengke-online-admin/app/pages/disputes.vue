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
          <tr v-for="d in paginatedDisputes" :key="d.id">
            <td class="id-cell">#{{ d.id }}</td>
            <td class="id-cell">#{{ d.orderId }}</td>
            <td><span class="name-cell">{{ d.reportedBy }}</span></td>
            <td>{{ d.against }}</td>
            <td>{{ d.reason }}</td>
            <td><span class="priority-badge" :class="d.priority">{{ d.priority }}</span></td>
            <td><span class="status-badge" :class="d.status">{{ d.status }}</span></td>
            <td class="date-cell">{{ d.date }}</td>
            <td><button class="action-btn" @click="viewDispute(d)">...</button></td>
          </tr>
        </tbody>
      </table>
      <PaginationBar v-model="disputePage" :total="filteredDisputes.length" :perPage="disputePerPage" />
    </div>

    <div class="modal-overlay" v-if="selected" @click="selected = null">
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">#{{ selected.id }} · Order #{{ selected.orderId }}</span>
          <span class="status-badge" :class="selected.status">{{ selected.status }}</span>
          <button class="modal-close" @click="selected = null">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="detail-grid">
            <div class="detail-item"><span class="dl">Reported By</span><span class="dv">{{ selected.reportedBy }}</span></div>
            <div class="detail-item"><span class="dl">Against</span><span class="dv">{{ selected.against }}</span></div>
            <div class="detail-item"><span class="dl">Reason</span><span class="dv">{{ selected.reason }}</span></div>
            <div class="detail-item"><span class="dl">Priority</span><span class="dv" :style="{ color: selected.priority === 'high' ? '#dc2626' : selected.priority === 'medium' ? '#d97706' : '#6b7280' }">{{ selected.priority }}</span></div>
            <div class="detail-item"><span class="dl">Date</span><span class="dv">{{ selected.date }}</span></div>
            <div class="detail-item"><span class="dl">Status</span><span class="dv">{{ selected.status }}</span></div>
          </div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="selected = null">Close</button>
          <button v-if="selected.status === 'open'" class="modal-btn primary" @click="updateStatus(selected.id, 'investigating')">Investigate</button>
          <button v-if="selected.status !== 'resolved'" class="modal-btn success" @click="updateStatus(selected.id, 'resolved')">Resolve</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const statusFilter = ref('')
const selected = ref(null)

function viewDispute(d) {
  selected.value = d
}

function updateStatus(id, status) {
  const d = disputes.value.find(d => d.id === id)
  if (d) d.status = status
  selected.value = null
}

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

const disputePage = ref(1)
const disputePerPage = ref(10)
const paginatedDisputes = computed(() => {
  const start = (disputePage.value - 1) * disputePerPage.value
  return filteredDisputes.value.slice(start, start + disputePerPage.value)
})
watch(statusFilter, () => { disputePage.value = 1 })
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; }
.filter-select { padding: 8px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #374151; background: #fff; cursor: pointer; outline: none; }
.filter-select:focus { border-color: #059669; }
.result-count { font-size: 13px; color: #9ca3af; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: auto; }
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

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 32px; backdrop-filter: blur(2px); }
.modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 420px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.modal-card.modal-sm { max-width: 420px; }
.modal-top { display: flex; align-items: center; gap: 10px; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; }
.modal-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; color: #111827; flex: 1; }
.modal-close { width: 32px; height: 32px; border: none; background: none; cursor: pointer; color: #9ca3af; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-close:hover { background: #f3f4f6; color: #374151; }
.modal-close svg { width: 18px; height: 18px; }
.modal-body { padding: 20px 24px; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.detail-item { display: flex; flex-direction: column; gap: 2px; }
.dl { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.3px; }
.dv { font-size: 14px; font-weight: 600; color: #111827; }
.modal-actions { display: flex; gap: 10px; padding: 16px 24px 20px; border-top: 1px solid #f3f4f6; }
.modal-btn { flex: 1; padding: 11px; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.15s; border: none; }
.modal-btn.secondary { background: #f3f4f6; color: #6b7280; }
.modal-btn.secondary:hover { background: #e5e7eb; }
.modal-btn.primary { background: #059669; color: #fff; }
.modal-btn.primary:hover { background: #047857; }
.modal-btn.success { background: #3b82f6; color: #fff; }
.modal-btn.success:hover { background: #2563eb; }

@media (max-width: 768px) {
  .data-table { min-width: 750px; }
}
@media (max-width: 480px) {
  .page-toolbar { flex-direction: column; align-items: stretch; }
  .filter-select { width: 100%; }
}
</style>
