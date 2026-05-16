<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <div class="tab-switch">
          <button class="tab-btn" :class="{ active: activeTab === 'list' }" @click="activeTab = 'list'">Disputes</button>
          <button class="tab-btn" :class="{ active: activeTab === 'analytics' }" @click="activeTab = 'analytics'">Analytics</button>
        </div>
        <div class="search-box" v-if="activeTab === 'list'">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input v-model="searchQuery" placeholder="Search disputes..." />
        </div>
        <select v-model="statusFilter" class="filter-select" v-if="activeTab === 'list'">
          <option value="">All Status</option>
          <option value="open">Open</option>
          <option value="investigating">Investigating</option>
          <option value="resolved">Resolved</option>
        </select>
      </div>
      <span class="result-count">{{ filteredDisputes.length }} disputes</span>
    </div>

    <!-- Analytics Tab -->
    <div v-if="activeTab === 'analytics'" class="analytics-section">
      <div class="stats-grid">
        <div class="stat-card"><span class="stat-value">{{ analytics.total }}</span><span class="stat-label">Total Disputes</span></div>
        <div class="stat-card"><span class="stat-value" style="color:#dc2626">{{ analytics.open }}</span><span class="stat-label">Open</span></div>
        <div class="stat-card"><span class="stat-value" style="color:#d97706">{{ analytics.investigating }}</span><span class="stat-label">Investigating</span></div>
        <div class="stat-card"><span class="stat-value" style="color:#059669">{{ analytics.resolved }}</span><span class="stat-label">Resolved</span></div>
      </div>
      <div class="charts-row">
        <div class="chart-card">
          <h4>By Priority</h4>
          <div class="chart-bars">
            <div class="chart-bar-item">
              <span class="chart-bar-label">High</span>
              <div class="chart-bar-bg"><div class="chart-bar-fill" style="width:0%;background:#dc2626" :style="{ width: (analytics.highPriority / analytics.total * 100) + '%' }"></div></div>
              <span class="chart-bar-val">{{ analytics.highPriority }}</span>
            </div>
            <div class="chart-bar-item">
              <span class="chart-bar-label">Medium</span>
              <div class="chart-bar-bg"><div class="chart-bar-fill" style="width:0%;background:#d97706" :style="{ width: (analytics.mediumPriority / analytics.total * 100) + '%' }"></div></div>
              <span class="chart-bar-val">{{ analytics.mediumPriority }}</span>
            </div>
            <div class="chart-bar-item">
              <span class="chart-bar-label">Low</span>
              <div class="chart-bar-bg"><div class="chart-bar-fill" style="width:0%;background:#6b7280" :style="{ width: (analytics.lowPriority / analytics.total * 100) + '%' }"></div></div>
              <span class="chart-bar-val">{{ analytics.lowPriority }}</span>
            </div>
          </div>
        </div>
        <div class="chart-card">
          <h4>By Reason</h4>
          <div class="reason-list">
            <div v-for="(count, reason) in analytics.byReason" :key="reason" class="reason-item">
              <span class="reason-name">{{ reason }}</span>
              <span class="reason-count">{{ count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- List Tab -->
    <div class="table-card" v-if="activeTab === 'list'">
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

    <!-- Detail Modal -->
    <div class="modal-overlay" v-if="selected" @click="closeModal">
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">#{{ selected.id }} · Order #{{ selected.orderId }}</span>
          <span class="status-badge" :class="selected.status">{{ selected.status }}</span>
          <button class="modal-close" @click="closeModal">
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
          <button class="modal-btn secondary" @click="closeModal">Close</button>
          <button v-if="selected.status === 'open'" class="modal-btn primary" @click="store.updateStatus(selected.id, 'investigating'); closeModal()">Investigate</button>
          <button v-if="selected.status !== 'resolved'" class="modal-btn success" @click="store.updateStatus(selected.id, 'resolved'); closeModal()">Resolve</button>
          <button v-if="selected.status !== 'resolved'" class="modal-btn danger" @click="store.deleteDispute(selected.id); closeModal()">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const store = useDisputesStore()
const { disputes, analytics } = storeToRefs(store)

const activeTab = ref('list')
const statusFilter = ref('')
const searchQuery = ref('')
const selected = ref(null)

const disputePage = ref(1)
const disputePerPage = ref(10)

const filteredDisputes = computed(() => {
  let result = disputes.value
  if (statusFilter.value) result = result.filter(d => d.status === statusFilter.value)
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(d =>
      d.reportedBy.toLowerCase().includes(q) ||
      d.against.toLowerCase().includes(q) ||
      d.reason.toLowerCase().includes(q) ||
      d.orderId.toLowerCase().includes(q)
    )
  }
  return result
})

const paginatedDisputes = computed(() => {
  const start = (disputePage.value - 1) * disputePerPage.value
  return filteredDisputes.value.slice(start, start + disputePerPage.value)
})

watch(statusFilter, () => { disputePage.value = 1 })
watch(searchQuery, () => { disputePage.value = 1 })

function viewDispute(d) { selected.value = d }
function closeModal() { selected.value = null }
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.tab-switch { display: flex; gap: 0; background: #f3f4f6; border-radius: 8px; padding: 3px; }
.tab-btn { padding: 7px 16px; border: none; background: transparent; border-radius: 6px; font-size: 12px; font-weight: 600; color: #6b7280; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
.tab-btn.active { background: #fff; color: #059669; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.search-box { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; min-width: 220px; }
.search-box:focus-within { border-color: #059669; }
.search-box svg { width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0; }
.search-box input { border: none; background: transparent; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; width: 100%; }
.filter-select { padding: 8px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #374151; background: #fff; cursor: pointer; outline: none; }
.filter-select:focus { border-color: #059669; }
.result-count { font-size: 13px; color: #9ca3af; }

.analytics-section { display: flex; flex-direction: column; gap: 20px; }
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.stat-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 4px; }
.stat-value { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 28px; font-weight: 700; color: #111827; }
.stat-label { font-size: 12px; color: #9ca3af; font-weight: 500; }
.charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.chart-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; }
.chart-card h4 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 16px; }
.chart-bars { display: flex; flex-direction: column; gap: 12px; }
.chart-bar-item { display: flex; align-items: center; gap: 12px; }
.chart-bar-label { font-size: 12px; color: #6b7280; width: 60px; flex-shrink: 0; }
.chart-bar-bg { flex: 1; height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden; }
.chart-bar-fill { height: 100%; border-radius: 4px; transition: width 0.3s ease; }
.chart-bar-val { font-size: 12px; font-weight: 600; color: #111827; width: 24px; text-align: right; }
.reason-list { display: flex; flex-direction: column; gap: 8px; }
.reason-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
.reason-item:last-child { border-bottom: none; }
.reason-name { font-size: 13px; color: #374151; }
.reason-count { font-size: 13px; font-weight: 600; color: #059669; }

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
.modal-btn.danger { background: #dc2626; color: #fff; }
.modal-btn.danger:hover { background: #b91c1c; }

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .charts-row { grid-template-columns: 1fr; }
  .data-table { min-width: 750px; }
}
@media (max-width: 480px) {
  .page-toolbar { flex-direction: column; align-items: stretch; }
  .filter-select { width: 100%; }
}
</style>
