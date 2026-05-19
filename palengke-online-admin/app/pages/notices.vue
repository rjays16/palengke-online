<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <span class="result-count"
        >{{ notices.length }} notice{{ notices.length !== 1 ? "s" : "" }}</span
      >
      <button class="add-btn" @click="openAddModal">
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Notice
      </button>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Message</th>
            <th>Target</th>
            <th>Status</th>
            <th>Created</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="n in notices" :key="n.id">
            <td>
              <span class="name-cell">{{ n.title }}</span>
            </td>
            <td class="msg-cell">{{ n.message }}</td>
            <td>
              <span class="target-badge" :class="n.target">{{
                n.target === "all" ? "All Apps" : n.target
              }}</span>
            </td>
            <td>
              <span
                class="status-badge"
                :class="n.active ? 'active' : 'inactive'"
                >{{ n.active ? "Active" : "Inactive" }}</span
              >
            </td>
            <td class="date-cell">{{ n.createdAt }}</td>
            <td>
              <div class="row-actions">
                <button
                  class="icon-btn"
                  @click="toggleActive(n)"
                  :title="n.active ? 'Deactivate' : 'Activate'"
                >
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    v-if="!n.active"
                  >
                    <polygon points="5 3 19 12 5 21 5 3" />
                  </svg>
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    v-else
                  >
                    <rect x="6" y="4" width="4" height="16" />
                    <rect x="14" y="4" width="4" height="16" />
                  </svg>
                </button>
                <button class="icon-btn" @click="openEditModal(n)" title="Edit">
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"
                    />
                    <path
                      d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"
                    />
                  </svg>
                </button>
                <button
                  class="icon-btn danger"
                  @click="confirmDelete(n)"
                  title="Delete"
                >
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <polyline points="3 6 5 6 21 6" />
                    <path
                      d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                    />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="empty-state" v-if="notices.length === 0">
        <p>
          No notices yet. Create one to broadcast to seller, driver, or customer
          apps.
        </p>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" v-if="showModal" @click.self="showModal = false">
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ editing ? "Edit Notice" : "New Notice" }}</h3>
          <button class="modal-close" @click="showModal = false">
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
          <div class="modal-form">
            <div class="form-group">
              <label>Title</label>
              <input
                v-model="form.title"
                class="form-input"
                placeholder="e.g. System Maintenance"
              />
            </div>
            <div class="form-group">
              <label>Message</label>
              <textarea
                v-model="form.message"
                class="form-input form-textarea"
                placeholder="e.g. The app will be down from 2AM to 4AM for maintenance."
                rows="3"
              ></textarea>
            </div>
            <div class="form-group">
              <label>Target App</label>
              <div class="target-options">
                <label
                  v-for="t in targets"
                  :key="t.value"
                  class="target-radio"
                  :class="{ selected: form.target === t.value }"
                >
                  <input type="radio" v-model="form.target" :value="t.value" />
                  <span class="target-label">{{ t.label }}</span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label>Active</label>
              <div class="toggle-wrap">
                <label class="toggle-label">
                  <input
                    type="checkbox"
                    v-model="form.active"
                    class="toggle-input"
                  />
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="reset-btn" @click="showModal = false">Cancel</button>
          <button class="save-btn" @click="saveNotice">
            {{ editing ? "Update" : "Create" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div
      class="modal-overlay"
      v-if="showDeleteModal"
      @click.self="showDeleteModal = false"
    >
      <div class="modal-card modal-sm">
        <div class="modal-top">
          <span class="modal-title">Delete Notice</span>
          <button class="modal-close" @click="showDeleteModal = false">
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
          <p style="font-size: 14px; color: #374151; line-height: 1.5">
            Delete "{{ deleting?.title }}"?
          </p>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="showDeleteModal = false">
            Cancel
          </button>
          <button class="modal-btn danger" @click="doDelete">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });

const KEY = "palengke_notices";

const notices = ref([]);
let nextId = 1;

if (import.meta.client) {
  const raw = localStorage.getItem(KEY);
  if (raw) {
    notices.value = JSON.parse(raw);
  } else {
    const mock = [
      {
        id: 1,
        title: "System Maintenance Tonight",
        message:
          "The app will be down from 2AM to 4AM for scheduled maintenance. Please plan your orders accordingly.",
        target: "all",
        active: true,
        createdAt: "2026-05-17",
      },
      {
        id: 2,
        title: "Seller Portal Updates",
        message:
          "New product analytics and bulk edit features are now available on your seller dashboard.",
        target: "seller",
        active: true,
        createdAt: "2026-05-16",
      },
      {
        id: 3,
        title: "Delivery Schedule Change",
        message:
          "Due to the holiday, deliveries will run until 5PM only on May 20.",
        target: "customer",
        active: true,
        createdAt: "2026-05-15",
      },
    ];
    localStorage.setItem(KEY, JSON.stringify(mock));
    notices.value = mock;
  }
  nextId = notices.value.length
    ? Math.max(...notices.value.map((n) => n.id)) + 1
    : 1;
}

const showModal = ref(false);
const editing = ref(null);
const showDeleteModal = ref(false);
const deleting = ref(null);

const targets = [
  { value: "all", label: "All Apps" },
  { value: "customer", label: "Customer App" },
  { value: "seller", label: "Seller App" },
  { value: "driver", label: "Driver App" },
];

const form = ref({ title: "", message: "", target: "all", active: true });

function save() {
  localStorage.setItem(KEY, JSON.stringify(notices.value));
}

function openAddModal() {
  editing.value = null;
  form.value = { title: "", message: "", target: "all", active: true };
  showModal.value = true;
}

function openEditModal(n) {
  editing.value = n;
  form.value = {
    title: n.title,
    message: n.message,
    target: n.target,
    active: n.active,
  };
  showModal.value = true;
}

function saveNotice() {
  if (!form.value.title || !form.value.message) return;
  if (editing.value) {
    Object.assign(editing.value, { ...form.value });
  } else {
    notices.value.push({
      id: nextId++,
      ...form.value,
      createdAt: new Date().toISOString().slice(0, 10),
    });
  }
  save();
  showModal.value = false;
  editing.value = null;
}

function toggleActive(n) {
  n.active = !n.active;
  save();
}

function confirmDelete(n) {
  deleting.value = n;
  showDeleteModal.value = true;
}
function doDelete() {
  if (deleting.value) {
    notices.value = notices.value.filter((n) => n.id !== deleting.value.id);
    save();
  }
  showDeleteModal.value = false;
  deleting.value = null;
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
.result-count {
  font-size: 13px;
  color: #9ca3af;
}
.add-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 18px;
  background: #059669;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  transition: background 0.15s;
}
.add-btn:hover {
  background: #047857;
}
.add-btn svg {
  width: 16px;
  height: 16px;
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
}
.data-table td {
  padding: 14px 16px;
  font-size: 13px;
  color: #374151;
  border-bottom: 1px solid #f9fafb;
}
.name-cell {
  font-weight: 600;
  color: #111827;
}
.msg-cell {
  color: #6b7280;
  max-width: 240px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.date-cell {
  color: #9ca3af;
}
.row-actions {
  display: flex;
  gap: 4px;
}
.icon-btn {
  width: 30px;
  height: 30px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  transition: all 0.15s;
}
.icon-btn:hover {
  border-color: #059669;
  color: #059669;
}
.icon-btn.danger:hover {
  border-color: #dc2626;
  color: #dc2626;
}
.icon-btn svg {
  width: 14px;
  height: 14px;
}
.target-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  text-transform: capitalize;
}
.target-badge.all {
  background: #f0fdf4;
  color: #059669;
}
.target-badge.customer {
  background: #eff6ff;
  color: #2563eb;
}
.target-badge.seller {
  background: #fef3c7;
  color: #d97706;
}
.target-badge.driver {
  background: #f3e8ff;
  color: #7c3aed;
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
  color: #9ca3af;
}
.empty-state {
  padding: 40px 20px;
  text-align: center;
  color: #9ca3af;
  font-size: 13px;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
  backdrop-filter: blur(2px);
}
.modal-card {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 460px;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
  animation: modalIn 0.15s ease;
}
.modal-card.modal-sm {
  max-width: 420px;
}
@keyframes modalIn {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(8px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px 0;
}
.modal-header h3 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #111827;
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
  font-size: 15px;
  font-weight: 700;
  color: #111827;
  flex: 1;
}
.modal-close {
  width: 30px;
  height: 30px;
  border: none;
  background: none;
  cursor: pointer;
  color: #9ca3af;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
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
.modal-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.form-group label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
}
.form-input {
  padding: 10px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #111827;
  outline: none;
}
.form-input:focus {
  border-color: #059669;
}
.form-textarea {
  resize: vertical;
  min-height: 70px;
  line-height: 1.5;
}
.target-options {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.target-radio {
  padding: 8px 14px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
  transition: all 0.15s;
}
.target-radio input {
  display: none;
}
.target-radio.selected {
  border-color: #059669;
  background: #f0fdf4;
  color: #059669;
  font-weight: 600;
}
.toggle-wrap {
  padding-top: 4px;
}
.toggle-label {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 22px;
  cursor: pointer;
}
.toggle-input {
  display: none;
}
.toggle-slider {
  position: absolute;
  inset: 0;
  background: #d1d5db;
  border-radius: 22px;
  transition: 0.2s;
}
.toggle-slider::before {
  content: "";
  position: absolute;
  left: 2px;
  bottom: 2px;
  width: 18px;
  height: 18px;
  background: #fff;
  border-radius: 50%;
  transition: 0.2s;
}
.toggle-input:checked + .toggle-slider {
  background: #059669;
}
.toggle-input:checked + .toggle-slider::before {
  transform: translateX(18px);
}
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 0 24px 20px;
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
.save-btn {
  padding: 10px 20px;
  background: #059669;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: "Inter", sans-serif;
}
.save-btn:hover {
  background: #047857;
}
.reset-btn {
  padding: 10px 20px;
  background: #fff;
  color: #374151;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  font-family: "Inter", sans-serif;
}
.reset-btn:hover {
  border-color: #9ca3af;
}

@media (max-width: 768px) {
  .data-table {
    min-width: 600px;
  }
}
</style>
