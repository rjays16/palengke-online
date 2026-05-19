<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <select v-model="activeFilter" class="filter-select">
          <option value="">All</option>
          <option value="true">Active</option>
          <option value="false">Inactive</option>
        </select>
      </div>
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
        Add Promo
      </button>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Discount</th>
            <th>Type</th>
            <th>Min Spend</th>
            <th>Usage</th>
            <th>Valid Until</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in filteredPromos" :key="p.id">
            <td>
              <span class="promo-code"> {{ p.code }}</span>
            </td>
            <td class="amount-cell">
              {{ p.type === "percent" ? p.discount + "%" : "₱" + p.discount }}
            </td>
            <td>{{ p.type === "percent" ? "Percentage" : "Flat Rate" }}</td>
            <td>
              {{ p.minSpend ? "₱" + p.minSpend.toLocaleString() : "None" }}
            </td>
            <td>{{ p.used }} / {{ p.maxUses }}</td>
            <td class="date-cell">{{ p.validUntil }}</td>
            <td>
              <span
                class="status-badge"
                :class="p.active ? 'active' : 'inactive'"
                >{{ p.active ? "Active" : "Inactive" }}</span
              >
            </td>
            <td>
              <div class="row-actions">
                <button
                  class="icon-btn"
                  @click="toggleActive(p)"
                  :title="p.active ? 'Deactivate' : 'Activate'"
                >
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    v-if="!p.active"
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
                <button class="icon-btn" @click="openEditModal(p)" title="Edit">
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
                  @click="confirmDelete(p)"
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
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" v-if="showModal" @click.self="showModal = false">
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ editingPromo ? "Edit Promo" : "New Promo Code" }}</h3>
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
              <label>Promo Code</label>
              <input
                v-model="form.code"
                class="form-input"
                placeholder="e.g. SUMMER30"
              />
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Type</label>
                <select v-model="form.type" class="form-input">
                  <option value="percent">Percentage</option>
                  <option value="flat">Flat Rate</option>
                </select>
              </div>
              <div class="form-group">
                <label>Discount</label>
                <input
                  v-model.number="form.discount"
                  class="form-input"
                  type="number"
                  min="1"
                />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Min Spend (₱)</label>
                <input
                  v-model.number="form.minSpend"
                  class="form-input"
                  type="number"
                  min="0"
                />
              </div>
              <div class="form-group">
                <label>Max Uses</label>
                <input
                  v-model.number="form.maxUses"
                  class="form-input"
                  type="number"
                  min="1"
                />
              </div>
            </div>
            <div class="form-group">
              <label>Valid Until</label>
              <input v-model="form.validUntil" class="form-input" type="date" />
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
          <button class="save-btn" @click="savePromo">
            {{ editingPromo ? "Update" : "Create" }}
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
          <span class="modal-title">Delete Promo</span>
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
            Delete promo code "{{ deletingPromo?.code }}"?
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

const store = usePromosStore();
const { promos } = storeToRefs(store);

const activeFilter = ref("");
const showModal = ref(false);
const editingPromo = ref(null);
const showDeleteModal = ref(false);
const deletingPromo = ref(null);

const form = ref({
  code: "",
  type: "percent",
  discount: 10,
  minSpend: 0,
  maxUses: 100,
  validUntil: "",
  active: true,
});

const filteredPromos = computed(() => {
  let result = promos.value;
  if (activeFilter.value !== "") {
    result = result.filter((p) => p.active === (activeFilter.value === "true"));
  }
  return result;
});

function openAddModal() {
  editingPromo.value = null;
  form.value = {
    code: "",
    type: "percent",
    discount: 10,
    minSpend: 0,
    maxUses: 100,
    validUntil: "",
    active: true,
  };
  showModal.value = true;
}
function openEditModal(p) {
  editingPromo.value = p;
  form.value = {
    code: p.code,
    type: p.type,
    discount: p.discount,
    minSpend: p.minSpend,
    maxUses: p.maxUses,
    validUntil: p.validUntil,
    active: p.active,
  };
  showModal.value = true;
}
function toggleActive(p) {
  store.toggleActive(p.id);
}
function confirmDelete(p) {
  deletingPromo.value = p;
  showDeleteModal.value = true;
}
function doDelete() {
  if (deletingPromo.value) store.deletePromo(deletingPromo.value.id);
  showDeleteModal.value = false;
  deletingPromo.value = null;
}

function savePromo() {
  if (!form.value.code) return;
  if (editingPromo.value) {
    store.updatePromo(editingPromo.value.id, { ...form.value });
  } else {
    store.addPromo({ ...form.value, used: 0 });
  }
  showModal.value = false;
  editingPromo.value = null;
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
}
.filter-select {
  padding: 8px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #374151;
  background: #fff;
  cursor: pointer;
  outline: none;
}
.filter-select:focus {
  border-color: #059669;
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
.promo-code {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-weight: 700;
  font-size: 13px;
  color: #059669;
  letter-spacing: 0.5px;
}
.amount-cell {
  font-weight: 600;
  color: #111827;
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
  max-width: 440px;
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
.form-input[type="number"] {
  width: 100%;
}
.form-row {
  display: flex;
  gap: 16px;
  align-items: flex-end;
}
.form-row .form-group {
  flex: 1;
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
    min-width: 650px;
  }
}
</style>
