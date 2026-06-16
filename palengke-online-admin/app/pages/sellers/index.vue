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
          <input v-model="search" placeholder="Search sellers..." />
        </div>
      </div>
      <div class="toolbar-right">
        <span class="result-count">{{ filteredSellers.length }} sellers</span>
        <button class="add-btn" @click="openAdd">+ Add Seller</button>
      </div>
    </div>

    <div v-if="sellersStore.loading" class="loading-state">
      <span>Loading sellers...</span>
    </div>
    <div v-else-if="sellersStore.error" class="error-state">
      <span>Failed to load sellers: {{ sellersStore.error }}</span>
    </div>
    <div v-else class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Store</th>
            <th>Owner</th>
            <th>Category</th>
            <th>Products</th>
            <th>Revenue</th>
            <th>Rating</th>
            <th>Subscription</th>
            <th>Verification</th>
            <th>Status</th>
            <th>Joined</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="s in paginatedSellers" :key="s.id">
            <td>
              <div class="cell-store">
                <div class="store-avatar">{{ s.name.charAt(0) }}</div>
                <div>
                  <div class="name-cell">{{ s.name }}</div>
                  <div class="sub-cell">{{ s.location }}</div>
                </div>
              </div>
            </td>
            <td>{{ s.owner }}</td>
            <td>{{ s.category }}</td>
            <td>{{ s.products }}</td>
            <td>₱{{ s.revenue.toLocaleString() }}</td>
            <td><span class="stars">★</span> {{ s.rating }}</td>
            <td>
              <div class="sub-col">
                <span class="sub-plan">{{ s.subscription.plan }}</span>
                <span
                  class="status-badge sub-badge"
                  :class="s.subscription.status"
                  >{{ s.subscription.status }}</span
                >
              </div>
            </td>
            <td>
              <div class="sub-col">
                <span
                  class="status-badge"
                  :class="verBadgeClass(s)"
                >{{ verBadgeLabel(s) }}</span>
                <span class="ver-choice-hint">{{ verHint(s) }}</span>
              </div>
            </td>
            <td>
              <span class="status-badge" :class="s.status">{{
                s.status === "active" ? "Active" : "Inactive"
              }}</span>
            </td>
            <td class="date-cell">{{ s.joined }}</td>
            <td>
              <div class="row-actions">
                <button
                  class="row-btn edit"
                  @click="goToSeller(s.id)"
                  title="View"
                >
                  👁
                </button>
                <button class="row-btn edit" @click="openEdit(s)" title="Edit">
                  ✎
                </button>
                <button
                  class="row-btn delete"
                  @click="confirmDelete(s)"
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
        v-model="sellerPage"
        :total="filteredSellers.length"
        :perPage="sellerPerPage"
      />
    </div>

    <div class="modal-overlay" v-if="showForm" @click.self="closeForm">
      <div class="modal-card" @click.stop>
        <div class="modal-top">
          <span class="modal-title">{{
            editing ? "Edit Seller" : "Add Seller"
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
          <div class="form-row">
            <div class="form-group flex-1">
              <label>Store Name <span class="req">*</span></label>
              <input
                v-model="form.name"
                type="text"
                placeholder="e.g. Juan's Farm"
                class="form-input"
              />
            </div>
            <div class="form-group flex-1">
              <label>Owner <span class="req">*</span></label>
              <input
                v-model="form.owner"
                type="text"
                placeholder="e.g. Juan dela Cruz"
                class="form-input"
              />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group flex-1">
              <label>Category <span class="req">*</span></label>
              <select v-model="form.category" class="form-input">
                <option value="">Select category...</option>
                <option value="Vegetables">Vegetables</option>
                <option value="Fruits">Fruits</option>
                <option value="Seafood">Seafood</option>
                <option value="Meat & Poultry">Meat & Poultry</option>
                <option value="Bundles">Bundles</option>
              </select>
            </div>
            <div class="form-group flex-1">
              <label>Location <span class="req">*</span></label>
              <input
                v-model="form.location"
                type="text"
                placeholder="e.g. Brgy. San Isidro"
                class="form-input"
              />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group flex-1">
              <label>Phone</label>
              <input
                v-model="form.phone"
                type="text"
                placeholder="e.g. 0917-111-2233"
                class="form-input"
              />
            </div>
            <div class="form-group flex-1">
              <label>Email</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="e.g. store@email.com"
                class="form-input"
              />
            </div>
          </div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="closeForm">Cancel</button>
          <button
            class="modal-btn primary"
            :disabled="!formValid"
            @click="saveSeller"
          >
            {{ editing ? "Update" : "Add" }} Seller
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
          <span class="modal-title">Delete Seller</span>
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
          <button class="modal-btn secondary" @click="showDelete = false">
            Cancel
          </button>
          <button class="modal-btn danger" @click="handleDelete">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });
const sellersStore = useSellersStore();

onMounted(() => sellersStore.fetchSellers());

const search = ref("");

const filteredSellers = computed(() => {
  if (!search.value) return sellersStore.sellers;
  const q = search.value.toLowerCase();
  return sellersStore.sellers.filter(
    (s) =>
      s.name.toLowerCase().includes(q) ||
      s.owner.toLowerCase().includes(q) ||
      s.category.toLowerCase().includes(q),
  );
});

const sellerPage = ref(1);
const sellerPerPage = ref(10);
const paginatedSellers = computed(() => {
  const start = (sellerPage.value - 1) * sellerPerPage.value;
  return filteredSellers.value.slice(start, start + sellerPerPage.value);
});
watch(search, () => {
  sellerPage.value = 1;
});

const goToSeller = (id) => navigateTo("/sellers/" + id);

const showForm = ref(false);
const editing = ref(null);
const form = ref({
  name: "",
  owner: "",
  category: "",
  location: "",
  phone: "",
  email: "",
});

function resetForm() {
  form.value = {
    name: "",
    owner: "",
    category: "",
    location: "",
    phone: "",
    email: "",
  };
}

function openAdd() {
  editing.value = null;
  resetForm();
  showForm.value = true;
}

function openEdit(s) {
  editing.value = s;
  form.value = {
    name: s.name,
    owner: s.owner,
    category: s.category,
    location: s.location,
    phone: s.contact?.phone || "",
    email: s.contact?.email || "",
  };
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  editing.value = null;
  resetForm();
}

const formValid = computed(() => {
  return (
    form.value.name &&
    form.value.owner &&
    form.value.category &&
    form.value.location
  );
});

function saveSeller() {
  if (!formValid.value) return;
  if (editing.value) {
    sellersStore.updateSeller(editing.value.id, form.value);
  } else {
    sellersStore.addSeller(form.value);
  }
  closeForm();
}

const showDelete = ref(false);
const deleteTarget = ref(null);
function confirmDelete(s) {
  deleteTarget.value = s;
  showDelete.value = true;
}
function handleDelete() {
  if (deleteTarget.value) sellersStore.deleteSeller(deleteTarget.value.id);
  showDelete.value = false;
  deleteTarget.value = null;
}

function verBadgeClass(s) {
  if (s.verificationStatus === "verified") return "ver-verified";
  if (s.verificationChoice === "complete") return "ver-submitted";
  return "ver-skipped";
}
function verBadgeLabel(s) {
  if (s.verificationStatus === "verified") return "Verified";
  if (s.verificationChoice === "complete") return "ID Submitted";
  return "Basic (Skipped)";
}
function verHint(s) {
  if (s.verificationStatus === "verified") return "Fully verified";
  if (s.verificationChoice === "complete") return "Awaiting review";
  if (s.verificationChoice === "skip") return "Skipped verification";
  return "";
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
}
.loading-state,
.error-state {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  padding: 40px 24px;
  text-align: center;
  font-size: 14px;
  color: #6b7280;
}
.error-state {
  color: #dc2626;
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
.cell-store {
  display: flex;
  align-items: center;
  gap: 10px;
}
.store-avatar {
  width: 34px;
  height: 34px;
  border-radius: 8px;
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
.sub-cell {
  font-size: 12px;
  color: #9ca3af;
}
.date-cell {
  color: #9ca3af;
}
.stars {
  color: #f59e0b;
}
.action-btn {
  background: none;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 4px 10px;
  cursor: pointer;
  color: #9ca3af;
  font-size: 16px;
  transition: all 0.15s;
}
.action-btn:hover {
  border-color: #059669;
  color: #059669;
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
.status-badge.ver-verified {
  background: #f0fdf4;
  color: #059669;
}
.status-badge.ver-not_verified {
  background: #fffbeb;
  color: #d97706;
}
.status-badge.ver-pending {
  background: #eff6ff;
  color: #3b82f6;
}
.status-badge.ver-submitted {
  background: #eff6ff;
  color: #3b82f6;
}
.status-badge.ver-skipped {
  background: #fffbeb;
  color: #d97706;
}
.ver-choice-hint {
  font-size: 11px;
  color: #9ca3af;
  white-space: nowrap;
}
.sub-col {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.sub-plan {
  font-weight: 600;
  font-size: 12px;
  color: #111827;
}
.sub-badge {
  width: fit-content;
}
.status-badge.trial {
  background: #eff6ff;
  color: #3b82f6;
}
.status-badge.expired {
  background: #fef2f2;
  color: #dc2626;
}
.status-badge.cancelled {
  background: #f3f4f6;
  color: #6b7280;
}
.toolbar-right {
  display: flex;
  align-items: center;
  gap: 12px;
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
  max-width: 520px;
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
.form-row {
  display: flex;
  gap: 12px;
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

@media (max-width: 768px) {
  .data-table {
    min-width: 750px;
  }
  .search-box {
    min-width: 160px;
  }
}
@media (max-width: 480px) {
  .page-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  .search-box {
    min-width: 0;
    width: 100%;
  }
  .data-table {
    min-width: 680px;
  }
}
</style>
