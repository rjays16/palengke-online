<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <span class="result-count">{{ filteredRequests.length }} requests</span>
      <select v-model="statusFilter" class="filter-select">
        <option value="">All</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr><th>Seller</th><th>Title</th><th>Subtitle</th><th>Link</th><th>Submitted</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="r in filteredRequests" :key="r.id">
            <td><span class="name-cell">{{ r.seller }}</span></td>
            <td>{{ r.title }}</td>
            <td class="sub-cell">{{ r.subtitle }}</td>
            <td class="link-cell">{{ r.link }}</td>
            <td class="date-cell">{{ r.submittedAt }}</td>
            <td><span class="status-badge" :class="r.status">{{ r.status }}</span></td>
            <td>
              <div class="row-actions">
                <button v-if="r.status === 'pending'" class="icon-btn success" @click="approve(r.id)" title="Approve">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                </button>
                <button v-if="r.status === 'pending'" class="icon-btn danger" @click="reject(r.id)" title="Reject">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <button class="icon-btn" @click="viewRequest(r)" title="View">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </button>
                <button class="icon-btn danger" @click="confirmDelete(r)" title="Delete">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="modal-overlay" v-if="selected" @click="selected = null">
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">{{ selected.title }}</span>
          <span class="status-badge" :class="selected.status">{{ selected.status }}</span>
          <button class="modal-close" @click="selected = null">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="detail-grid">
            <div class="detail-item"><span class="dl">Seller</span><span class="dv">{{ selected.seller }}</span></div>
            <div class="detail-item"><span class="dl">Title</span><span class="dv">{{ selected.title }}</span></div>
            <div class="detail-item"><span class="dl">Subtitle</span><span class="dv">{{ selected.subtitle || '—' }}</span></div>
            <div class="detail-item"><span class="dl">Link</span><span class="dv">{{ selected.link || '—' }}</span></div>
            <div class="detail-item"><span class="dl">Image</span><span class="dv">{{ selected.image || '—' }}</span></div>
            <div class="detail-item"><span class="dl">Submitted</span><span class="dv">{{ selected.submittedAt }}</span></div>
          </div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="selected = null">Close</button>
          <button v-if="selected.status === 'pending'" class="modal-btn success" @click="approve(selected.id); selected = null">Approve</button>
          <button v-if="selected.status === 'pending'" class="modal-btn danger" @click="reject(selected.id); selected = null">Reject</button>
        </div>
      </div>
    </div>

    <div class="modal-overlay" v-if="showDeleteModal" @click.self="showDeleteModal = false">
      <div class="modal-card modal-sm">
        <div class="modal-top">
          <span class="modal-title">Delete Request</span>
          <button class="modal-close" @click="showDeleteModal = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <p style="font-size:14px;color:#374151;line-height:1.5">Delete banner request from {{ deleting?.seller }}?</p>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="showDeleteModal = false">Cancel</button>
          <button class="modal-btn danger" @click="doDelete">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const KEY = 'palengke_banner_requests'

const requests = ref(JSON.parse(localStorage.getItem(KEY) || '[]'))
const statusFilter = ref('')
const selected = ref(null)
const showDeleteModal = ref(false)
const deleting = ref(null)

function save() {
  localStorage.setItem(KEY, JSON.stringify(requests.value))
}

const filteredRequests = computed(() => {
  if (!statusFilter.value) return requests.value
  return requests.value.filter(r => r.status === statusFilter.value)
})

function approve(id) {
  const r = requests.value.find(r => r.id === id)
  if (r) r.status = 'approved'
  save()
  selected.value = null
}

function reject(id) {
  const r = requests.value.find(r => r.id === id)
  if (r) r.status = 'rejected'
  save()
  selected.value = null
}

function viewRequest(r) { selected.value = r }
function confirmDelete(r) { deleting.value = r; showDeleteModal.value = true }
function doDelete() {
  if (deleting.value) {
    requests.value = requests.value.filter(r => r.id !== deleting.value.id)
    save()
  }
  showDeleteModal.value = false
  deleting.value = null
}
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.filter-select { padding: 8px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #374151; background: #fff; cursor: pointer; outline: none; }
.filter-select:focus { border-color: #059669; }
.result-count { font-size: 13px; color: #9ca3af; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.name-cell { font-weight: 600; color: #111827; }
.sub-cell { color: #6b7280; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.link-cell { color: #059669; max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.date-cell { color: #9ca3af; }
.row-actions { display: flex; gap: 4px; }
.icon-btn { width: 30px; height: 30px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #9ca3af; transition: all 0.15s; }
.icon-btn:hover { border-color: #059669; color: #059669; }
.icon-btn.success:hover { border-color: #059669; color: #059669; }
.icon-btn.danger:hover { border-color: #dc2626; color: #dc2626; }
.icon-btn svg { width: 14px; height: 14px; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.pending { background: #fef3c7; color: #d97706; }
.status-badge.approved { background: #f0fdf4; color: #059669; }
.status-badge.rejected { background: #fef2f2; color: #dc2626; }

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
.modal-btn.success { background: #059669; color: #fff; }
.modal-btn.success:hover { background: #047857; }
.modal-btn.danger { background: #dc2626; color: #fff; }
.modal-btn.danger:hover { background: #b91c1c; }

@media (max-width: 768px) { .data-table { min-width: 650px; } }
</style>
