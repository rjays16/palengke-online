<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <span class="result-count"
        >{{ faqs.length }} FAQ{{ faqs.length !== 1 ? "s" : "" }}</span
      >
      <button class="add-btn" @click="openAddModal">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add FAQ
      </button>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Question</th>
            <th>Answer</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(f, i) in faqs" :key="f.id">
            <td class="order-cell">{{ i + 1 }}</td>
            <td>
              <span class="name-cell">{{ f.question }}</span>
            </td>
            <td class="msg-cell">{{ f.answer }}</td>
            <td>
              <span class="status-badge" :class="f.active ? 'active' : 'inactive'">{{ f.active ? 'Active' : 'Inactive' }}</span>
            </td>
            <td>
              <div class="row-actions">
                <button class="icon-btn" @click="toggleActive(f)" :title="f.active ? 'Deactivate' : 'Activate'">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" v-if="!f.active">
                    <polygon points="5 3 19 12 5 21 5 3" />
                  </svg>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" v-else>
                    <rect x="6" y="4" width="4" height="16" />
                    <rect x="14" y="4" width="4" height="16" />
                  </svg>
                </button>
                <button class="icon-btn" @click="openEditModal(f)" title="Edit">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                  </svg>
                </button>
                <button class="icon-btn danger" @click="confirmDelete(f)" title="Delete">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="empty-state" v-if="faqs.length === 0">
        <p>No FAQs yet. Create one to help sellers get started.</p>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" v-if="showModal" @click.self="showModal = false">
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ editing ? 'Edit FAQ' : 'New FAQ' }}</h3>
          <button class="modal-close" @click="showModal = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="modal-form">
            <div class="form-group">
              <label>Question</label>
              <input v-model="form.question" class="form-input" placeholder="e.g. How do I add a new product?" />
            </div>
            <div class="form-group">
              <label>Answer</label>
              <textarea v-model="form.answer" class="form-input form-textarea" placeholder="e.g. Go to the Products page..." rows="4"></textarea>
            </div>
            <div class="form-group">
              <label>Active</label>
              <div class="toggle-wrap">
                <label class="toggle-label">
                  <input type="checkbox" v-model="form.active" class="toggle-input" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="reset-btn" @click="showModal = false">Cancel</button>
          <button class="save-btn" @click="saveFaq">{{ editing ? 'Update' : 'Create' }}</button>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal-overlay" v-if="showDeleteModal" @click.self="showDeleteModal = false">
      <div class="modal-card modal-sm">
        <div class="modal-top">
          <span class="modal-title">Delete FAQ</span>
          <button class="modal-close" @click="showDeleteModal = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <p style="font-size: 14px; color: #374151; line-height: 1.5">Delete this FAQ?</p>
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
definePageMeta({ layout: 'default' });

const config = useRuntimeConfig();
const apiBaseUrl = config.public.apiBaseUrl || 'http://localhost:8000';

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('palengke_auth_token');
}

const faqs = ref([]);
const loading = ref(true);

async function fetchFaqs() {
  loading.value = true;
  try {
    const token = getToken();
    const data = await $fetch(`${apiBaseUrl}/api/admin/faqs`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    faqs.value = data.faqs;
  } catch {
    // silent
  } finally {
    loading.value = false;
  }
}

fetchFaqs();

const showModal = ref(false);
const editing = ref(null);
const showDeleteModal = ref(false);
const deleting = ref(null);

const form = ref({ question: '', answer: '', active: true });

function openAddModal() {
  editing.value = null;
  form.value = { question: '', answer: '', active: true };
  showModal.value = true;
}

function openEditModal(f) {
  editing.value = f;
  form.value = { question: f.question, answer: f.answer, active: f.active };
  showModal.value = true;
}

async function saveFaq() {
  if (!form.value.question || !form.value.answer) return;
  const token = getToken();
  try {
    if (editing.value) {
      const data = await $fetch(`${apiBaseUrl}/api/admin/faqs/${editing.value.id}`, {
        method: 'PUT',
        headers: { Authorization: `Bearer ${token}` },
        body: { ...form.value },
      });
      Object.assign(editing.value, data.faq);
    } else {
      const data = await $fetch(`${apiBaseUrl}/api/admin/faqs`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token}` },
        body: { ...form.value },
      });
      faqs.value.push(data.faq);
    }
  } catch {
    // silent
  }
  showModal.value = false;
  editing.value = null;
}

async function toggleActive(f) {
  const token = getToken();
  try {
    const data = await $fetch(`${apiBaseUrl}/api/admin/faqs/${f.id}`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: { question: f.question, answer: f.answer, active: !f.active },
    });
    f.active = data.faq.active;
  } catch {
    // silent
  }
}

function confirmDelete(f) {
  deleting.value = f;
  showDeleteModal.value = true;
}

async function doDelete() {
  if (!deleting.value) return;
  const token = getToken();
  try {
    await $fetch(`${apiBaseUrl}/api/admin/faqs/${deleting.value.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` },
    });
    faqs.value = faqs.value.filter((f) => f.id !== deleting.value.id);
  } catch {
    // silent
  }
  showDeleteModal.value = false;
  deleting.value = null;
}
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.result-count { font-size: 13px; color: #9ca3af; }
.add-btn { display: flex; align-items: center; gap: 6px; padding: 10px 18px; background: #059669; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; }
.add-btn:hover { background: #047857; }
.add-btn svg { width: 16px; height: 16px; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.order-cell { color: #9ca3af; width: 40px; }
.name-cell { font-weight: 600; color: #111827; }
.msg-cell { color: #6b7280; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.row-actions { display: flex; gap: 4px; }
.icon-btn { width: 30px; height: 30px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #9ca3af; transition: all 0.15s; }
.icon-btn:hover { border-color: #059669; color: #059669; }
.icon-btn.danger:hover { border-color: #dc2626; color: #dc2626; }
.icon-btn svg { width: 14px; height: 14px; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.active { background: #f0fdf4; color: #059669; }
.status-badge.inactive { background: #f3f4f6; color: #9ca3af; }
.empty-state { padding: 40px 20px; text-align: center; color: #9ca3af; font-size: 13px; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; z-index: 200; backdrop-filter: blur(2px); }
.modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 16px 48px rgba(0,0,0,0.15); animation: modalIn 0.15s ease; }
.modal-card.modal-sm { max-width: 420px; }
@keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(8px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0; }
.modal-header h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 700; color: #111827; }
.modal-top { display: flex; align-items: center; gap: 10px; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; }
.modal-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; color: #111827; flex: 1; }
.modal-close { width: 30px; height: 30px; border: none; background: none; cursor: pointer; color: #9ca3af; display: flex; align-items: center; justify-content: center; border-radius: 6px; }
.modal-close:hover { background: #f3f4f6; color: #374151; }
.modal-close svg { width: 18px; height: 18px; }
.modal-body { padding: 20px 24px; }
.modal-form { display: flex; flex-direction: column; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 13px; font-weight: 600; color: #374151; }
.form-input { padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; }
.form-input:focus { border-color: #059669; }
.form-textarea { resize: vertical; min-height: 80px; line-height: 1.5; }
.toggle-wrap { padding-top: 4px; }
.toggle-label { position: relative; display: inline-block; width: 40px; height: 22px; cursor: pointer; }
.toggle-input { display: none; }
.toggle-slider { position: absolute; inset: 0; background: #d1d5db; border-radius: 22px; transition: 0.2s; }
.toggle-slider::before { content: ''; position: absolute; left: 2px; bottom: 2px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: 0.2s; }
.toggle-input:checked + .toggle-slider { background: #059669; }
.toggle-input:checked + .toggle-slider::before { transform: translateX(18px); }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 0 24px 20px; }
.modal-actions { display: flex; gap: 10px; padding: 16px 24px 20px; border-top: 1px solid #f3f4f6; }
.modal-btn { flex: 1; padding: 11px; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.15s; border: none; }
.modal-btn.secondary { background: #f3f4f6; color: #6b7280; }
.modal-btn.secondary:hover { background: #e5e7eb; }
.modal-btn.danger { background: #dc2626; color: #fff; }
.modal-btn.danger:hover { background: #b91c1c; }
.save-btn { padding: 10px 20px; background: #059669; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
.save-btn:hover { background: #047857; }
.reset-btn { padding: 10px 20px; background: #fff; color: #374151; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; }
.reset-btn:hover { border-color: #9ca3af; }

@media (max-width: 768px) {
  .data-table { min-width: 500px; }
}
</style>
