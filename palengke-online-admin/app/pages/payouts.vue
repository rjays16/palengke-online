<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <select v-model="statusFilter" class="filter-select">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="completed">Completed</option>
          <option value="failed">Failed</option>
        </select>
      </div>
      <span class="result-count">{{ filteredPayouts.length }} payouts</span>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr><th>Payout ID</th><th>Recipient</th><th>Type</th><th>Amount</th><th>Account</th><th>Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="p in paginatedPayouts" :key="p.id">
            <td class="id-cell">#{{ p.id }}</td>
            <td><span class="name-cell">{{ p.recipient }}</span></td>
            <td>{{ p.type }}</td>
            <td class="amount-cell">₱{{ p.amount.toLocaleString() }}</td>
            <td>{{ p.account }}</td>
            <td><span class="status-badge" :class="p.status">{{ p.status }}</span></td>
            <td class="date-cell">{{ p.date }}</td>
            <td><button class="action-btn" @click="viewPayout(p)">...</button></td>
          </tr>
        </tbody>
      </table>
      <PaginationBar v-model="payoutPage" :total="filteredPayouts.length" :perPage="payoutPerPage" />
    </div>

    <div class="modal-overlay" v-if="selected" @click="selected = null">
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">#{{ selected.id }}</span>
          <span class="status-badge" :class="selected.status">{{ selected.status }}</span>
          <button class="modal-close" @click="selected = null">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="detail-grid">
            <div class="detail-item"><span class="dl">Recipient</span><span class="dv">{{ selected.recipient }}</span></div>
            <div class="detail-item"><span class="dl">Type</span><span class="dv">{{ selected.type }}</span></div>
            <div class="detail-item"><span class="dl">Amount</span><span class="dv">₱{{ selected.amount.toLocaleString() }}</span></div>
            <div class="detail-item"><span class="dl">Account</span><span class="dv">{{ selected.account }}</span></div>
            <div class="detail-item"><span class="dl">Date</span><span class="dv">{{ selected.date }}</span></div>
            <div class="detail-item"><span class="dl">Status</span><span class="dv">{{ selected.status }}</span></div>
          </div>
        </div>
        <div class="modal-actions-single">
          <button class="modal-btn secondary" @click="selected = null">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const statusFilter = ref('')
const selected = ref(null)

function viewPayout(p) {
  selected.value = p
}

const payouts = ref([
  { id: 'PO-001', recipient: "Juan's Farm", type: 'Seller', amount: 45200, account: 'GCash - ****1234', status: 'completed', date: '2026-05-14' },
  { id: 'PO-002', recipient: 'Mark Reyes', type: 'Driver', amount: 12500, account: 'GCash - ****5678', status: 'completed', date: '2026-05-14' },
  { id: 'PO-003', recipient: "Ana's Store", type: 'Seller', amount: 28400, account: 'Bank - ****9012', status: 'pending', date: '2026-05-13' },
  { id: 'PO-004', recipient: 'Nina Salvador', type: 'Driver', amount: 9800, account: 'GCash - ****3456', status: 'completed', date: '2026-05-13' },
  { id: 'PO-005', recipient: 'Pedro Merchants', type: 'Seller', amount: 52100, account: 'Bank - ****7890', status: 'failed', date: '2026-05-12' },
  { id: 'PO-006', recipient: 'Tina Fernandez', type: 'Driver', amount: 15600, account: 'GCash - ****2345', status: 'completed', date: '2026-05-12' },
  { id: 'PO-007', recipient: 'FreshMart', type: 'Seller', amount: 36200, account: 'GCash - ****6789', status: 'pending', date: '2026-05-11' },
  { id: 'PO-008', recipient: "Liza's Shop", type: 'Seller', amount: 19500, account: 'Bank - ****0123', status: 'completed', date: '2026-05-10' },
])

const filteredPayouts = computed(() => {
  if (!statusFilter.value) return payouts.value
  return payouts.value.filter(p => p.status === statusFilter.value)
})

const payoutPage = ref(1)
const payoutPerPage = ref(10)
const paginatedPayouts = computed(() => {
  const start = (payoutPage.value - 1) * payoutPerPage.value
  return filteredPayouts.value.slice(start, start + payoutPerPage.value)
})
watch(statusFilter, () => { payoutPage.value = 1 })
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
.amount-cell { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600; color: #111827; }
.date-cell { color: #9ca3af; }
.action-btn { background: none; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 10px; cursor: pointer; color: #9ca3af; font-size: 16px; transition: all 0.15s; }
.action-btn:hover { border-color: #059669; color: #059669; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.completed { background: #f0fdf4; color: #059669; }
.status-badge.pending { background: #fef3c7; color: #d97706; }
.status-badge.failed { background: #fef2f2; color: #dc2626; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 32px; backdrop-filter: blur(2px); }
.modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 420px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.modal-top { display: flex; align-items: center; gap: 10px; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; }
.modal-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 700; color: #111827; flex: 1; }
.modal-close { width: 32px; height: 32px; border: none; background: none; cursor: pointer; color: #9ca3af; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-close:hover { background: #f3f4f6; color: #374151; }
.modal-close svg { width: 18px; height: 18px; }
.modal-body { padding: 20px 24px; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.detail-item { display: flex; flex-direction: column; gap: 2px; }
.dl { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.3px; }
.dv { font-size: 14px; font-weight: 600; color: #111827; }
.modal-actions-single { display: flex; gap: 10px; padding: 16px 24px 20px; border-top: 1px solid #f3f4f6; }
.modal-btn { flex: 1; padding: 11px; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.15s; border: none; }
.modal-btn.secondary { background: #f3f4f6; color: #6b7280; }
.modal-btn.secondary:hover { background: #e5e7eb; }

@media (max-width: 768px) {
  .data-table { min-width: 700px; }
}
@media (max-width: 480px) {
  .page-toolbar { flex-direction: column; align-items: stretch; }
  .filter-select { width: 100%; }
}
</style>
