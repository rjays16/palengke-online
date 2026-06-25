<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div class="settings-grid">
      <div class="settings-section">
        <div class="section-header">
          <div>
            <h3>Frequently Asked Questions</h3>
            <p class="section-desc">
              Manage FAQs for {{ isDriver ? 'driver' : 'seller' }} app. Add, edit, or remove questions and answers.
            </p>
          </div>
          <button class="add-btn" @click="openAdd">+ Add FAQ</button>
        </div>

        <div class="filter-tabs">
          <button class="filter-tab active">
            <svg v-if="isDriver" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><circle cx="6" cy="16" r="3"/><circle cx="18" cy="16" r="3"/><path d="M9 16h9"/><path d="M4 9h5l2 4"/><path d="M18 5l-3 2v9"/><rect x="13" y="6" width="5" height="4" rx="1"/></svg>
            <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            {{ isDriver ? 'Driver' : 'Seller' }} FAQs
          </button>
        </div>

        <div v-if="isLoading" class="loading-state">Loading FAQs...</div>
        <div v-else-if="error" class="error-state">{{ error }}</div>
        <div v-else-if="filteredFaqs.length === 0" class="empty-state">No FAQs for this role yet.</div>
        <div v-else class="faq-list">
          <div v-for="faq in filteredFaqs" :key="faq.id" class="faq-row">
            <div class="faq-content">
              <div class="faq-question">{{ faq.question }}</div>
              <div class="faq-answer">{{ faq.answer }}</div>
              <div v-if="faq.topic" class="faq-topic">{{ faq.topic }}</div>
            </div>
            <div class="status-badge" :class="faq.active ? 'active' : 'inactive'">
              {{ faq.active ? 'Active' : 'Inactive' }}
            </div>
            <div class="row-actions">
              <button class="row-btn edit" @click="openEdit(faq)" title="Edit">
                ✎
              </button>
              <button class="row-btn delete" @click="confirmDelete(faq)" title="Delete">
                ✕
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Summary</h3>
        <div class="summary-grid">
          <div class="summary-card">
            <span class="summary-label">Total</span>
            <span class="summary-value">{{ filteredFaqs.length }}</span>
          </div>
          <div class="summary-card">
            <span class="summary-label">Active</span>
            <span class="summary-value">{{ activeCount }}</span>
          </div>
          <div class="summary-card">
            <span class="summary-label">Inactive</span>
            <span class="summary-value">{{ inactiveCount }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-overlay" v-if="showForm" @click.self="closeForm">
      <div class="modal-card" @click.stop>
        <div class="modal-top">
          <span class="modal-title">{{ editing ? 'Edit FAQ' : 'Add FAQ' }}</span>
          <button class="modal-close" @click="closeForm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body form-body">
          <div class="form-group">
            <label>Question <span class="req">*</span></label>
            <input v-model="form.question" type="text" placeholder="Enter the question" class="form-input" />
          </div>
          <div class="form-group">
            <label>Answer <span class="req">*</span></label>
            <textarea v-model="form.answer" placeholder="Enter the answer" class="form-input form-textarea" rows="4"></textarea>
          </div>
          <div class="form-group">
            <label>Topic</label>
            <select v-model="form.topic" class="form-input">
              <option :value="null">No topic (shows in All)</option>
              <option v-for="t in availableTopics" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group flex-1">
              <label>Sort Order</label>
              <input v-model.number="form.sort_order" type="number" min="0" class="form-input" />
            </div>
            <div class="form-group flex-1">
              <label>Status</label>
              <select v-model="form.active" class="form-input">
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="closeForm" :disabled="isSaving">Cancel</button>
          <button class="modal-btn primary" :disabled="!formValid || isSaving" @click="saveFaq">
            {{ isSaving ? 'Saving...' : (editing ? 'Update' : 'Add') }}
          </button>
        </div>
      </div>
    </div>

    <div class="modal-overlay" v-if="showDelete" @click.self="showDelete = false">
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">Delete FAQ</span>
          <button class="modal-close" @click="showDelete = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <p class="confirm-text">Are you sure you want to delete <strong>{{ deleteTarget?.question }}</strong>?</p>
          <p class="confirm-hint">This action cannot be undone.</p>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="showDelete = false" :disabled="isDeleting">Cancel</button>
          <button class="modal-btn danger" @click="handleDelete" :disabled="isDeleting">
            {{ isDeleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">✓ FAQ saved successfully</div>
    <div class="error-toast" v-if="toastError">{{ toastError }}</div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });

const auth = useAuthStore();
const { isDriver, isSeller } = useSettingsRole();
auth.restore();
onMounted(() => {
  if (auth.user?.role !== "superadmin") navigateTo("/dashboard");
  fetchFaqs();
});

const route = useRoute();

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}
function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem("palengke_auth_token");
}

const faqs = ref([]);
const isLoading = ref(false);
const error = ref("");
const success = ref(false);
const toastError = ref("");

const currentRole = computed(() => {
  const r = route.query.role;
  if (r && ['driver', 'seller'].includes(r)) return r;
  return 'driver';
});

const filteredFaqs = computed(() =>
  faqs.value.filter((f) => f.role === currentRole.value)
);

const activeCount = computed(() => filteredFaqs.value.filter((f) => f.active).length);
const inactiveCount = computed(() => filteredFaqs.value.filter((f) => !f.active).length);

const availableTopics = computed(() => {
  const topics = new Set(faqs.value.filter((f) => f.role === currentRole.value && f.topic).map((f) => f.topic));
  return [...topics].sort();
});

async function fetchFaqs() {
  isLoading.value = true;
  error.value = "";
  try {
    const token = getToken();
    const res = await $fetch(`${getApiBaseUrl()}/api/admin/faqs`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    faqs.value = res.faqs;
  } catch (err) {
    error.value = err?.data?.message || "Failed to load FAQs";
  } finally {
    isLoading.value = false;
  }
}

const showForm = ref(false);
const editing = ref(null);
const form = ref({ question: "", answer: "", topic: null, sort_order: 0, active: true });

function resetForm() {
  form.value = { question: "", answer: "", topic: null, sort_order: 0, active: true };
}

function openAdd() {
  editing.value = null;
  resetForm();
  showForm.value = true;
}

function openEdit(faq) {
  editing.value = faq;
  form.value = {
    question: faq.question,
    answer: faq.answer,
    topic: faq.topic ?? null,
    sort_order: faq.sort_order,
    active: faq.active,
  };
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  editing.value = null;
  resetForm();
}

const formValid = computed(() => form.value.question && form.value.answer);

const isSaving = ref(false);

async function saveFaq() {
  if (!formValid.value) return;
  isSaving.value = true;
  toastError.value = "";
  success.value = false;

  try {
    const token = getToken();
    const body = {
      question: form.value.question,
      answer: form.value.answer,
      role: currentRole.value,
      topic: form.value.topic || null,
      sort_order: form.value.sort_order,
      active: form.value.active,
    };

    if (editing.value) {
      await $fetch(`${getApiBaseUrl()}/api/admin/faqs/${editing.value.id}`, {
        method: "PUT",
        headers: { Authorization: `Bearer ${token}` },
        body,
      });
    } else {
      await $fetch(`${getApiBaseUrl()}/api/admin/faqs`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
        body,
      });
    }

    await fetchFaqs();
    closeForm();
    success.value = true;
    setTimeout(() => { success.value = false; }, 2500);
  } catch (err) {
    toastError.value = err?.data?.message || "Failed to save FAQ";
  } finally {
    isSaving.value = false;
  }
}

const showDelete = ref(false);
const deleteTarget = ref(null);
const isDeleting = ref(false);

function confirmDelete(faq) {
  deleteTarget.value = faq;
  showDelete.value = true;
}

async function handleDelete() {
  if (!deleteTarget.value) return;
  isDeleting.value = true;
  toastError.value = "";

  try {
    const token = getToken();
    await $fetch(`${getApiBaseUrl()}/api/admin/faqs/${deleteTarget.value.id}`, {
      method: "DELETE",
      headers: { Authorization: `Bearer ${token}` },
    });
    await fetchFaqs();
    showDelete.value = false;
    deleteTarget.value = null;
    success.value = true;
    setTimeout(() => { success.value = false; }, 2500);
  } catch (err) {
    toastError.value = err?.data?.message || "Failed to delete FAQ";
  } finally {
    isDeleting.value = false;
  }
}
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 24px; max-width: 900px; }
.settings-grid { display: flex; flex-direction: column; gap: 20px; }
.settings-section { background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; }
.settings-section h3 { font-family: "Plus Jakarta Sans", sans-serif; font-size: 15px; font-weight: 600; color: #111827; margin: 0; }
.section-desc { font-size: 13px; color: #9ca3af; margin: 6px 0 0; }
.section-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
.add-btn { padding: 9px 18px; background: #059669; color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: "Inter", sans-serif; cursor: pointer; transition: background 0.15s; white-space: nowrap; flex-shrink: 0; }
.add-btn:hover { background: #047857; }
.filter-tabs { display: flex; gap: 6px; margin-bottom: 16px; padding: 4px; background: #f3f4f6; border-radius: 8px; width: fit-content; }
.filter-tab { display: flex; align-items: center; gap: 5px; background: none; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #6b7280; cursor: pointer; font-family: "Inter", sans-serif; }
.filter-tab.active { background: white; color: #111827; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.tab-icon { width: 14px; height: 14px; }
.loading-state, .error-state, .empty-state { padding: 40px; text-align: center; font-size: 14px; color: #6b7280; }
.error-state { color: #dc2626; }
.faq-list { display: flex; flex-direction: column; gap: 2px; }
.faq-row { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 8px; transition: background 0.1s; }
.faq-row:hover { background: #f9fafb; }
.faq-content { flex: 1; min-width: 0; }
.faq-question { font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 2px; }
.faq-answer { font-size: 12px; color: #6b7280; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.faq-topic { display: inline-block; margin-top: 4px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #059669; background: #f0fdf4; padding: 2px 8px; border-radius: 4px; }
.status-badge { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 6px; white-space: nowrap; flex-shrink: 0; }
.status-badge.active { background: #f0fdf4; color: #059669; }
.status-badge.inactive { background: #f3f4f6; color: #6b7280; }
.row-actions { display: flex; gap: 4px; flex-shrink: 0; }
.row-btn { width: 28px; height: 28px; border-radius: 5px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px; transition: all 0.15s; }
.row-btn.edit { color: #059669; }
.row-btn.edit:hover { border-color: #059669; background: #f0fdf4; }
.row-btn.delete { color: #dc2626; }
.row-btn.delete:hover { border-color: #dc2626; background: #fef2f2; }
.summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 12px; }
.summary-card { background: #f9fafb; border-radius: 10px; padding: 16px; text-align: center; }
.summary-label { display: block; font-size: 12px; color: #9ca3af; margin-bottom: 6px; }
.summary-value { display: block; font-family: "Plus Jakarta Sans", sans-serif; font-size: 20px; font-weight: 700; color: #111827; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 32px; backdrop-filter: blur(2px); }
.modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.modal-card.modal-sm { max-width: 400px; }
.modal-top { display: flex; align-items: center; gap: 10px; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; }
.modal-title { font-family: "Plus Jakarta Sans", sans-serif; font-size: 16px; font-weight: 700; color: #111827; flex: 1; }
.modal-close { width: 32px; height: 32px; border: none; background: none; cursor: pointer; color: #9ca3af; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-close:hover { background: #f3f4f6; color: #374151; }
.modal-close svg { width: 18px; height: 18px; }
.modal-body { padding: 20px 24px; }
.form-body { display: flex; flex-direction: column; gap: 14px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.flex-1 { flex: 1; }
.form-group label { font-size: 12px; font-weight: 600; color: #374151; }
.req { color: #dc2626; }
.form-input { padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: "Inter", sans-serif; color: #111827; background: #fff; outline: none; transition: border-color 0.2s; width: 100%; box-sizing: border-box; }
.form-input:focus { border-color: #059669; }
.form-textarea { resize: vertical; min-height: 80px; }
select.form-input { cursor: pointer; }
.form-row { display: flex; gap: 12px; }
.modal-actions { display: flex; gap: 10px; padding: 16px 24px 20px; border-top: 1px solid #f3f4f6; }
.modal-btn { flex: 1; padding: 11px; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: "Inter", sans-serif; cursor: pointer; transition: all 0.15s; border: none; }
.modal-btn.primary { background: #059669; color: #fff; }
.modal-btn.primary:hover { background: #047857; }
.modal-btn.primary:disabled { opacity: 0.4; cursor: not-allowed; }
.modal-btn.secondary { background: #f3f4f6; color: #6b7280; }
.modal-btn.secondary:hover { background: #e5e7eb; }
.modal-btn.danger { background: #dc2626; color: #fff; }
.modal-btn.danger:hover { background: #b91c1c; }
.confirm-text { font-size: 14px; color: #374151; margin: 0 0 8px; line-height: 1.5; }
.confirm-hint { font-size: 12px; color: #9ca3af; margin: 0; }
.save-toast { padding: 10px 16px; background: #f0fdf4; color: #059669; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; font-weight: 600; }
.error-toast { padding: 10px 16px; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 8px; font-size: 13px; font-weight: 600; }
@media (max-width: 600px) { .summary-grid { grid-template-columns: 1fr; } .section-header { flex-direction: column; } .form-row { flex-direction: column; } }
</style>
