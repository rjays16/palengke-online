<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <div class="search-box">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <input v-model="search" placeholder="Search admin users..." />
        </div>
      </div>
      <div class="toolbar-right">
        <span class="result-count">{{ filtered.length }} users</span>
        <button class="add-btn" @click="openAdd">+ Add Admin</button>
      </div>
    </div>

    <div class="table-card">
      <div v-if="store.isLoading && store.admins.length === 0" class="loading-state">
        Loading admin users...
      </div>
      <div v-else-if="store.error" class="error-state">
        {{ store.error }}
      </div>
      <table v-else class="data-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="a in paginated" :key="a.id">
            <td>
              <div class="cell-user">
                <div class="user-avatar">{{ a.name.charAt(0) }}</div>
                <span class="name-cell">{{ a.name }}</span>
              </div>
            </td>
            <td>{{ a.email }}</td>
            <td>
              <span class="role-badge" :class="a.role">{{
                a.role === "superadmin" ? "Super Admin" : "Admin"
              }}</span>
            </td>
            <td>
              <span class="status-badge" :class="a.status">{{
                a.status === "active" ? "Active" : "Inactive"
              }}</span>
            </td>
            <td class="date-cell">{{ a.createdAt }}</td>
            <td>
              <div class="row-actions" v-if="a.role !== 'superadmin'">
                <button class="row-btn edit" @click="openEdit(a)" title="Edit">
                  ✎
                </button>
                <button
                  class="row-btn delete"
                  @click="confirmDelete(a)"
                  title="Delete"
                >
                  ✕
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <PaginationBar
        v-model="page"
        :total="filtered.length"
        :perPage="perPage"
      />
    </div>

    <div class="modal-overlay" v-if="showForm" @click.self="closeForm">
      <div class="modal-card" @click.stop>
        <div class="modal-top">
          <span class="modal-title">{{
            editing ? "Edit Admin" : "Add Admin"
          }}</span>
          <button class="modal-close" @click="closeForm">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body form-body">
          <div class="form-group">
            <label>Full Name <span class="req">*</span></label>
            <input
              v-model="form.name"
              type="text"
              placeholder="e.g. Juan Cruz"
              class="form-input"
            />
          </div>
          <div class="form-group">
            <label>Email Address <span class="req">*</span></label>
            <input
              v-model="form.email"
              type="email"
              placeholder="e.g. juan@palengke.com"
              class="form-input"
            />
          </div>
          <div class="form-row">
            <div class="form-group flex-1">
              <label
                >{{
                  editing ? "New Password (leave blank to keep)" : "Password"
                }}
                <span class="req" v-if="!editing">*</span></label
              >
              <input
                v-model="form.password"
                type="password"
                placeholder="Enter password"
                class="form-input"
              />
            </div>
            <div class="form-group flex-1">
              <label>Role <span class="req">*</span></label>
              <select v-model="form.role" class="form-input">
                <option value="admin">Admin</option>
                <option value="superadmin">Super Admin</option>
              </select>
            </div>
          </div>
          <div class="form-group" v-if="editing">
            <label>Status</label>
            <select v-model="form.status" class="form-input">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="closeForm" :disabled="isSaving">Cancel</button>
          <button
            class="modal-btn primary"
            :disabled="!formValid || isSaving"
            @click="saveUser"
          >
            {{ isSaving ? "Saving..." : (editing ? "Update" : "Add") }} Admin
          </button>
        </div>
      </div>
    </div>

    <div
      class="modal-overlay"
      v-if="showDelete"
      @click.self="showDelete = false"
    >
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">Delete Admin</span>
          <button class="modal-close" @click="showDelete = false">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <p class="confirm-text">
            Are you sure you want to delete
            <strong>{{ deleteTarget?.name }}</strong
            >?
          </p>
          <p class="confirm-hint">This action cannot be undone.</p>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="showDelete = false" :disabled="isDeleting">
            Cancel
          </button>
          <button class="modal-btn danger" @click="handleDelete" :disabled="isDeleting">
            {{ isDeleting ? "Deleting..." : "Delete" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "default",
});

const auth = useAuthStore();
auth.restore();
onMounted(() => {
  if (auth.user?.role !== "superadmin") {
    navigateTo("/dashboard");
  }
});

const store = useAdminUsersStore();

// Fetch admins on mount
onMounted(() => {
  store.fetchAdmins();
});

const search = ref("");
const filtered = computed(() => {
  if (!search.value) return store.admins;
  const q = search.value.toLowerCase();
  return store.admins.filter(
    (a) =>
      a.name.toLowerCase().includes(q) || a.email.toLowerCase().includes(q),
  );
});

const page = ref(1);
const perPage = ref(10);
const paginated = computed(() => {
  const start = (page.value - 1) * perPage.value;
  return filtered.value.slice(start, start + perPage.value);
});
watch(search, () => {
  page.value = 1;
});

const showForm = ref(false);
const editing = ref(null);
const form = ref({
  name: "",
  email: "",
  password: "",
  role: "admin",
  status: "active",
});

function resetForm() {
  form.value = {
    name: "",
    email: "",
    password: "",
    role: "admin",
    status: "active",
  };
}

function openAdd() {
  editing.value = null;
  resetForm();
  showForm.value = true;
}

function openEdit(a) {
  editing.value = a;
  form.value = {
    name: a.name,
    email: a.email,
    password: "",
    role: a.role,
    status: a.status,
  };
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  editing.value = null;
  resetForm();
}

const formValid = computed(() => {
  if (!form.value.name || !form.value.email) return false;
  if (!editing.value && !form.value.password) return false;
  return true;
});

const isSaving = ref(false);

async function saveUser() {
  if (!formValid.value) return;
  isSaving.value = true;

  let success = false;
  if (editing.value) {
    const data = {
      name: form.value.name,
      email: form.value.email,
      role: form.value.role,
      status: form.value.status,
    };
    if (form.value.password) data.password = form.value.password;
    success = await store.updateAdmin(editing.value.id, data);
  } else {
    success = await store.addAdmin({
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      role: form.value.role,
    });
  }

  isSaving.value = false;
  if (success) {
    closeForm();
  }
}

const showDelete = ref(false);
const deleteTarget = ref(null);
const isDeleting = ref(false);

function confirmDelete(a) {
  deleteTarget.value = a;
  showDelete.value = true;
}

async function handleDelete() {
  if (!deleteTarget.value) return;
  isDeleting.value = true;
  const success = await store.deleteAdmin(deleteTarget.value.id);
  isDeleting.value = false;
  if (success) {
    showDelete.value = false;
    deleteTarget.value = null;
  }
}
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.page-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}
.toolbar-left {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.toolbar-right {
  display: flex;
  align-items: center;
  gap: 12px;
}
.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  padding: 8px 12px;
  min-width: 260px;
}
.search-box:focus-within {
  border-color: #059669;
}
.search-box svg {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  flex-shrink: 0;
}
.search-box input {
  border: none;
  background: transparent;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #111827;
  outline: none;
  width: 100%;
}
.result-count {
  font-size: 13px;
  color: #9ca3af;
  white-space: nowrap;
}
.add-btn {
  padding: 9px 18px;
  background: #059669;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  font-family: "Inter", sans-serif;
  cursor: pointer;
  transition: background 0.15s;
  white-space: nowrap;
}
.add-btn:hover {
  background: #047857;
}
.table-card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: auto;
}
.data-table {
  width: 100%;
  border-collapse: collapse;
}
.data-table th {
  text-align: left;
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
  white-space: nowrap;
}
.data-table td {
  padding: 14px 16px;
  font-size: 13px;
  color: #374151;
  border-bottom: 1px solid #f9fafb;
}
.cell-user {
  display: flex;
  align-items: center;
  gap: 10px;
}
.user-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #059669, #047857);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}
.name-cell {
  font-weight: 600;
  color: #111827;
}
.date-cell {
  color: #9ca3af;
  white-space: nowrap;
}
.role-badge {
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
}
.role-badge.superadmin {
  background: #fef3c7;
  color: #d97706;
}
.role-badge.admin {
  background: #eff6ff;
  color: #3b82f6;
}
.status-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.status-badge.active {
  background: #f0fdf4;
  color: #059669;
}
.status-badge.inactive {
  background: #f3f4f6;
  color: #6b7280;
}
.row-actions {
  display: flex;
  gap: 4px;
}
.row-btn {
  width: 28px;
  height: 28px;
  border-radius: 5px;
  border: 1px solid #e5e7eb;
  background: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  transition: all 0.15s;
}
.row-btn.edit {
  color: #059669;
}
.row-btn.edit:hover {
  border-color: #059669;
  background: #f0fdf4;
}
.row-btn.delete {
  color: #dc2626;
}
.row-btn.delete:hover {
  border-color: #dc2626;
  background: #fef2f2;
}
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 32px;
  backdrop-filter: blur(2px);
}
.modal-card {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 460px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}
.modal-card.modal-sm {
  max-width: 400px;
}
.modal-top {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px 24px;
  border-bottom: 1px solid #f3f4f6;
}
.modal-title {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  flex: 1;
}
.modal-close {
  width: 32px;
  height: 32px;
  border: none;
  background: none;
  cursor: pointer;
  color: #9ca3af;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.modal-close:hover {
  background: #f3f4f6;
  color: #374151;
}
.modal-close svg {
  width: 18px;
  height: 18px;
}
.modal-body {
  padding: 20px 24px;
}
.form-body {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.form-group.flex-1 {
  flex: 1;
}
.form-group label {
  font-size: 12px;
  font-weight: 600;
  color: #374151;
}
.req {
  color: #dc2626;
}
.form-input {
  padding: 10px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #111827;
  background: #fff;
  outline: none;
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
}
.form-input:focus {
  border-color: #059669;
}
select.form-input {
  cursor: pointer;
}
.form-row {
  display: flex;
  gap: 12px;
}
.modal-actions {
  display: flex;
  gap: 10px;
  padding: 16px 24px 20px;
  border-top: 1px solid #f3f4f6;
}
.modal-btn {
  flex: 1;
  padding: 11px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  font-family: "Inter", sans-serif;
  cursor: pointer;
  transition: all 0.15s;
  border: none;
}
.modal-btn.primary {
  background: #059669;
  color: #fff;
}
.modal-btn.primary:hover {
  background: #047857;
}
.modal-btn.primary:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.modal-btn.secondary {
  background: #f3f4f6;
  color: #6b7280;
}
.modal-btn.secondary:hover {
  background: #e5e7eb;
}
.modal-btn.danger {
  background: #dc2626;
  color: #fff;
}
.modal-btn.danger:hover {
  background: #b91c1c;
}
.confirm-text {
  font-size: 14px;
  color: #374151;
  margin: 0 0 8px;
  line-height: 1.5;
}
.confirm-hint {
  font-size: 12px;
  color: #9ca3af;
  margin: 0;
}
.loading-state,
.error-state {
  padding: 40px;
  text-align: center;
  font-size: 14px;
  color: #6b7280;
}
.error-state {
  color: #dc2626;
}
@media (max-width: 768px) {
  .data-table {
    min-width: 700px;
  }
  .search-box {
    min-width: 160px;
  }
}
@media (max-width: 600px) {
  .form-row {
    flex-direction: column;
  }
}
@media (max-width: 480px) {
  .page-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  .toolbar-left {
    flex-direction: column;
  }
  .search-box {
    min-width: 0;
    width: 100%;
  }
  .add-btn {
    text-align: center;
  }
  .data-table {
    min-width: 640px;
  }
}
</style>
