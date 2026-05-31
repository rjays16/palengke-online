<template>
  <div class="page-shell">
    <div class="tabs-nav">
      <button
        class="tab-btn"
        :class="{ active: tab === 'pending' }"
        @click="tab = 'pending'"
      >
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          <line x1="12" y1="9" x2="12" y2="13" />
          <line x1="12" y1="17" x2="12.01" y2="17" />
        </svg>
        Pending Review
        <span class="tab-badge" v-if="store.pending.length > 0">{{
          store.pending.length
        }}</span>
      </button>
      <button
        class="tab-btn"
        :class="{ active: tab === 'history' }"
        @click="tab = 'history'"
      >
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <circle cx="12" cy="12" r="10" />
          <polyline points="12 6 12 12 16 14" />
        </svg>
        Review History
      </button>
    </div>

    <div v-if="tab === 'pending'" class="pending-section">
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
            <input
              v-model="search"
              placeholder="Search by name, store, or ID..."
            />
          </div>
          <select v-model="roleFilter" class="filter-select">
            <option value="">All Roles</option>
            <option value="seller">Seller</option>
            <option value="driver">Driver</option>
          </select>
        </div>
        <span class="result-count">{{ filtered.length }} pending</span>
      </div>

      <div class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Type</th>
              <th>Applicant</th>
              <th>Store / Vehicle</th>
              <th>Submitted</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="r in paginatedPending"
              :key="r.id"
              class="clickable-row"
              @click="openDetail(r)"
            >
              <td class="id-cell">{{ r.id }}</td>
              <td>
                <span class="role-badge" :class="r.role">{{
                  r.role === "seller" ? "Seller" : "Driver"
                }}</span>
              </td>
              <td>
                <div class="name-cell">{{ r.firstName }} {{ r.lastName }}</div>
                <div class="sub-cell">{{ r.email }}</div>
              </td>
              <td>
                <div v-if="r.role === 'seller'">
                  <div class="name-cell">{{ r.storeName }}</div>
                  <div class="sub-cell">{{ r.category }}</div>
                </div>
                <div v-else>
                  <div class="name-cell">
                    {{ r.vehicleType }} · {{ r.plateNumber }}
                  </div>
                  <div class="sub-cell">{{ areaLabel(r.serviceArea) }}</div>
                </div>
              </td>
              <td class="date-cell">{{ r.submittedAt }}</td>
              <td>
                <button class="action-btn small" @click.stop="openDetail(r)">
                  Review
                </button>
              </td>
            </tr>
            <tr v-if="filtered.length === 0">
              <td colspan="6" class="empty-cell">No pending applications</td>
            </tr>
          </tbody>
        </table>
        <PaginationBar
          v-model="pendingPage"
          :total="filtered.length"
          :perPage="pendingPerPage"
        />
      </div>
    </div>

    <div v-if="tab === 'history'" class="history-section">
      <div class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Type</th>
              <th>Applicant</th>
              <th>Decision</th>
              <th>Reviewed By</th>
              <th>Date</th>
              <th>Reason</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in paginatedHistory" :key="r.id">
              <td class="id-cell">{{ r.id }}</td>
              <td>
                <span class="role-badge" :class="r.role">{{
                  r.role === "seller" ? "Seller" : "Driver"
                }}</span>
              </td>
              <td class="name-cell">{{ r.firstName }} {{ r.lastName }}</td>
              <td>
                <span class="decision-badge" :class="r.status">{{
                  r.status
                }}</span>
              </td>
              <td>{{ r.reviewedBy }}</td>
              <td class="date-cell">{{ r.reviewedAt }}</td>
              <td class="reason-cell">{{ r.rejectReason || "—" }}</td>
            </tr>
            <tr v-if="store.history.length === 0">
              <td colspan="7" class="empty-cell">No review history</td>
            </tr>
          </tbody>
        </table>
        <PaginationBar
          v-model="historyPage"
          :total="store.history.length"
          :perPage="historyPerPage"
        />
      </div>
    </div>

    <div class="modal-overlay" v-if="selected" @click="selected = null">
      <div class="modal-card" @click.stop>
        <div class="modal-top">
          <div class="modal-id">{{ selected.id }}</div>
          <span class="role-badge" :class="selected.role">{{
            selected.role === "seller" ? "Seller" : "Driver"
          }}</span>
          <span class="status-badge pending">Pending Review</span>
          <button class="modal-close" @click="selected = null">
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
          <div class="detail-section" v-if="selected.role === 'seller'">
            <h4>Store Information</h4>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="dl">Store Name</span
                ><span class="dv">{{ selected.storeName }}</span>
              </div>
              <div class="detail-item">
                <span class="dl">Category</span
                ><span class="dv">{{ selected.category }}</span>
              </div>
              <div class="detail-item">
                <span class="dl">Address</span
                ><span class="dv">{{ selected.address }}</span>
              </div>
              <div class="detail-item">
                <span class="dl">Contact</span
                ><span class="dv">{{ selected.contact }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section" v-if="selected.role === 'driver'">
            <h4>Vehicle Information</h4>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="dl">Vehicle Type</span
                ><span class="dv">{{ selected.vehicleType }}</span>
              </div>
              <div class="detail-item">
                <span class="dl">Plate Number</span
                ><span class="dv">{{ selected.plateNumber }}</span>
              </div>
              <div class="detail-item" v-if="selected.vehicleModel">
                <span class="dl">Make/Model</span
                ><span class="dv">{{ selected.vehicleModel }}</span>
              </div>
              <div class="detail-item">
                <span class="dl">Service Area</span
                ><span class="dv">{{ areaLabel(selected.serviceArea) }}</span>
              </div>
              <div class="detail-item">
                <span class="dl">Contact</span
                ><span class="dv">{{ selected.contact }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h4>Applicant Information</h4>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="dl">Name</span
                ><span class="dv"
                  >{{ selected.firstName }} {{ selected.lastName }}</span
                >
              </div>
              <div class="detail-item">
                <span class="dl">Email</span
                ><span class="dv">{{ selected.email }}</span>
              </div>
              <div class="detail-item">
                <span class="dl">Submitted</span
                ><span class="dv">{{ selected.submittedAt }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h4>Submitted Documents</h4>
            <div class="docs-grid">
              <div class="doc-card" v-if="selected.validIdPhoto">
                <img :src="selected.validIdPhoto" class="doc-img" />
                <span class="doc-label">Valid ID</span>
              </div>
              <div class="doc-card placeholder" v-else>
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                  />
                  <polyline points="14 2 14 8 20 8" />
                </svg>
                <span class="doc-label">Valid ID (mock)</span>
              </div>
              <div class="doc-card" v-if="selected.selfiePhoto">
                <img :src="selected.selfiePhoto" class="doc-img" />
                <span class="doc-label">Selfie w/ ID</span>
              </div>
              <div
                class="doc-card placeholder"
                v-if="selected.role === 'seller' && !selected.selfiePhoto"
              >
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                  />
                  <polyline points="14 2 14 8 20 8" />
                </svg>
                <span class="doc-label">Selfie w/ ID (mock)</span>
              </div>
              <div class="doc-card" v-if="selected.businessPermitPhoto">
                <img :src="selected.businessPermitPhoto" class="doc-img" />
                <span class="doc-label">Business Permit</span>
              </div>
              <div class="doc-card" v-if="selected.licensePhoto">
                <img :src="selected.licensePhoto" class="doc-img" />
                <span class="doc-label">Driver's License</span>
              </div>
              <div class="doc-card" v-if="selected.vehicleRegPhoto">
                <img :src="selected.vehicleRegPhoto" class="doc-img" />
                <span class="doc-label">Vehicle Registration</span>
              </div>
            </div>
          </div>

          <div class="detail-section" v-if="rejectMode">
            <h4>Rejection Reason</h4>
            <textarea
              v-model="rejectReason"
              class="reject-input"
              placeholder="Explain why this application is being rejected..."
              rows="3"
            ></textarea>
          </div>
        </div>

        <div class="modal-actions">
          <button
            v-if="!rejectMode"
            class="modal-btn reject"
            :disabled="isProcessing"
            @click="rejectMode = true"
          >
            Reject
          </button>
          <template v-else>
            <button
              class="modal-btn cancel"
              :disabled="isProcessing"
              @click="
                rejectMode = false;
                rejectReason = '';
              "
            >
              Cancel
            </button>
            <button
              class="modal-btn reject"
              :disabled="!rejectReason.trim() || isProcessing"
              @click="handleReject"
            >
              {{ isProcessing ? 'Processing...' : 'Confirm Reject' }}
            </button>
          </template>
          <button
            class="modal-btn approve"
            :disabled="isProcessing"
            @click="handleApprove"
          >
            {{ isProcessing ? 'Processing...' : 'Approve' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "default",
  middleware: [
    (to, from) => {
      const auth = useAuthStore();
      if (!auth.canApprove) return navigateTo("/dashboard");
    },
  ],
});
const store = useRegistrationsStore();

onMounted(() => {
  store.fetchPending();
  store.fetchHistory();
});

const tab = ref("pending");
const search = ref("");
const roleFilter = ref("");
const selected = ref(null);
const rejectMode = ref(false);
const rejectReason = ref("");
const isProcessing = ref(false);

const filtered = computed(() => {
  return store.pending.filter((r) => {
    const q = search.value.toLowerCase();
    const matchSearch =
      !q ||
      r.id.toLowerCase().includes(q) ||
      r.firstName.toLowerCase().includes(q) ||
      r.lastName.toLowerCase().includes(q) ||
      (r.storeName && r.storeName.toLowerCase().includes(q));
    const matchRole = !roleFilter.value || r.role === roleFilter.value;
    return matchSearch && matchRole;
  });
});

const pendingPage = ref(1);
const pendingPerPage = ref(10);
const paginatedPending = computed(() => {
  const start = (pendingPage.value - 1) * pendingPerPage.value;
  return filtered.value.slice(start, start + pendingPerPage.value);
});
watch([search, roleFilter], () => {
  pendingPage.value = 1;
});

const historyPage = ref(1);
const historyPerPage = ref(10);
const paginatedHistory = computed(() => {
  const start = (historyPage.value - 1) * historyPerPage.value;
  return store.history.slice(start, start + historyPerPage.value);
});

function areaLabel(area) {
  const map = {
    cdo: "Cagayan de Oro",
    iloilo: "Iloilo City",
    davao: "Davao City",
    cebu: "Cebu City",
    manila: "Metro Manila",
  };
  return map[area] || area;
}

function openDetail(r) {
  selected.value = r;
  rejectMode.value = false;
  rejectReason.value = "";
}

async function handleApprove() {
  isProcessing.value = true;
  await store.approve(selected.value.id);
  isProcessing.value = false;
  selected.value = null;
}

async function handleReject() {
  isProcessing.value = true;
  await store.reject(selected.value.id, rejectReason.value);
  isProcessing.value = false;
  selected.value = null;
}
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.tabs-nav {
  display: flex;
  gap: 4px;
  background: #fff;
  border-radius: 12px;
  padding: 4px;
  border: 1px solid #e5e7eb;
  width: fit-content;
}
.tab-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 18px;
  border: none;
  background: transparent;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  font-family: "Inter", sans-serif;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.15s;
}
.tab-btn:hover {
  color: #374151;
}
.tab-btn.active {
  background: #059669;
  color: #fff;
}
.tab-btn svg {
  width: 16px;
  height: 16px;
}
.tab-badge {
  background: #fff;
  color: #059669;
  font-size: 10px;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 4px;
  min-width: 18px;
  text-align: center;
}
.tab-btn.active .tab-badge {
  background: rgba(255, 255, 255, 0.25);
  color: #fff;
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
.filter-select {
  padding: 8px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #374151;
  background: #fff;
  outline: none;
  cursor: pointer;
}
.filter-select:focus {
  border-color: #059669;
}
.result-count {
  font-size: 13px;
  color: #9ca3af;
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
.clickable-row {
  cursor: pointer;
  transition: background 0.1s;
}
.clickable-row:hover {
  background: #f9fafb;
}
.id-cell {
  font-weight: 600;
  color: #059669;
  white-space: nowrap;
  font-size: 12px;
}
.name-cell {
  font-weight: 500;
  color: #111827;
}
.sub-cell {
  font-size: 11px;
  color: #9ca3af;
  margin-top: 2px;
}
.date-cell {
  color: #9ca3af;
  white-space: nowrap;
}
.empty-cell {
  text-align: center;
  color: #9ca3af;
  padding: 40px 16px !important;
  font-style: italic;
}
.reason-cell {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #9ca3af;
  font-size: 12px;
}

.role-badge {
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
}
.role-badge.seller {
  background: #f0fdf4;
  color: #059669;
}
.role-badge.driver {
  background: #eff6ff;
  color: #3b82f6;
}

.action-btn.small {
  padding: 6px 14px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  background: #fff;
  cursor: pointer;
  font-size: 12px;
  font-family: "Inter", sans-serif;
  color: #059669;
  font-weight: 500;
  transition: all 0.15s;
}
.action-btn.small:hover {
  border-color: #059669;
  background: #f0fdf4;
}

.decision-badge {
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  text-transform: capitalize;
}
.decision-badge.approved {
  background: #f0fdf4;
  color: #059669;
}
.decision-badge.rejected {
  background: #fef2f2;
  color: #dc2626;
}

.status-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.status-badge.pending {
  background: #fef3c7;
  color: #d97706;
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
  max-width: 640px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}
.modal-top {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px 24px;
  border-bottom: 1px solid #f3f4f6;
}
.modal-id {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 14px;
  font-weight: 700;
  color: #059669;
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
.detail-section {
  margin-bottom: 20px;
}
.detail-section h4 {
  font-size: 12px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 0 0 10px;
}
.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
.detail-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.dl {
  font-size: 11px;
  color: #9ca3af;
}
.dv {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
}

.docs-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}
.doc-card {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}
.doc-card.placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 24px 12px;
  background: #f9fafb;
  color: #9ca3af;
  font-size: 11px;
}
.doc-card.placeholder svg {
  width: 28px;
  height: 28px;
}
.doc-img {
  width: 100%;
  height: 100px;
  object-fit: cover;
  display: block;
}
.doc-label {
  display: block;
  font-size: 10px;
  color: #6b7280;
  padding: 6px 8px;
  background: #f9fafb;
  font-weight: 500;
}

.reject-input {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #374151;
  outline: none;
  resize: vertical;
}
.reject-input:focus {
  border-color: #dc2626;
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
.modal-btn.approve {
  background: #059669;
  color: #fff;
}
.modal-btn.approve:hover {
  background: #047857;
}
.modal-btn.reject {
  background: #fef2f2;
  color: #dc2626;
}
.modal-btn.reject:hover {
  background: #fecaca;
}
.modal-btn.reject:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.modal-btn.cancel {
  background: #f3f4f6;
  color: #6b7280;
}
.modal-btn.cancel:hover {
  background: #e5e7eb;
}

@media (max-width: 768px) {
  .data-table {
    min-width: 700px;
  }
  .search-box {
    min-width: 160px;
  }
  .tabs-nav {
    width: 100%;
  }
  .tab-btn {
    flex: 1;
    justify-content: center;
  }
  .detail-grid {
    grid-template-columns: 1fr;
  }
  .docs-grid {
    grid-template-columns: 1fr 1fr;
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
  .filter-select {
    width: 100%;
  }
  .data-table {
    min-width: 640px;
  }
}
</style>
