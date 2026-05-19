<template>
  <div class="detail-page">
    <button class="back-btn" @click="goBack">
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
      >
        <polyline points="15 18 9 12 15 6" />
      </svg>
      Back to Drivers
    </button>

    <div v-if="driver" class="detail-content">
      <div class="detail-header">
        <div
          class="detail-avatar"
          style="background: linear-gradient(135deg, #3b82f6, #2563eb)"
        >
          {{ driver.name.charAt(0) }}
        </div>
        <div class="detail-title">
          <h1>{{ driver.name }}</h1>
          <span class="detail-subtitle"
            >{{ driver.vehicle }} · {{ driver.plate }}</span
          >
        </div>
        <div class="detail-actions">
          <span class="status-badge-lg" :class="driver.status">{{
            driver.status === "active"
              ? "Active"
              : driver.status === "idle"
                ? "Idle"
                : "Offline"
          }}</span>
        </div>
      </div>

      <div class="detail-grid">
        <div class="info-card">
          <h3>Driver Information</h3>
          <div class="info-rows">
            <div class="info-row">
              <span class="info-label">Phone</span
              ><span class="info-value">{{ driver.phone }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Vehicle</span
              ><span class="info-value">{{ driver.vehicle }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Plate #</span
              ><span class="info-value">{{ driver.plate }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Deliveries</span
              ><span class="info-value">{{ driver.deliveries }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Rating</span
              ><span class="info-value stars">★ {{ driver.rating }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Earnings</span
              ><span class="info-value"
                >₱{{ driver.earnings.toLocaleString() }}</span
              >
            </div>
            <div class="info-row">
              <span class="info-label">Joined</span
              ><span class="info-value">{{ driver.joined }}</span>
            </div>
          </div>
        </div>

        <div class="info-card">
          <h3>Documents</h3>
          <div class="doc-list">
            <div v-for="doc in docs" :key="doc.name" class="doc-row">
              <div class="doc-info">
                <span class="doc-name">{{ doc.name }}</span>
                <span class="doc-status" :class="doc.status">{{
                  doc.status === "verified"
                    ? "Verified"
                    : doc.status === "pending"
                      ? "Pending"
                      : "Rejected"
                }}</span>
              </div>
              <button
                v-if="doc.status === 'verified'"
                class="doc-btn verified"
                @click="viewDoc(doc)"
              >
                View
              </button>
              <button v-else class="doc-btn" @click="reviewDoc(doc)">
                Review
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="tabs-card">
        <div class="tabs-header">
          <button
            class="tab"
            :class="{ active: activeTab === 'earnings' }"
            @click="activeTab = 'earnings'"
          >
            Earnings
          </button>
          <button
            class="tab"
            :class="{ active: activeTab === 'history' }"
            @click="activeTab = 'history'"
          >
            Delivery History
          </button>
        </div>

        <div v-if="activeTab === 'earnings'" class="tab-content">
          <div class="earnings-grid">
            <div class="mini-card">
              <span class="mini-label">This Week</span>
              <span class="mini-value"
                >₱{{ weeklyEarnings.toLocaleString() }}</span
              >
              <div class="mini-bar-bg">
                <div class="mini-bar-fill" style="width: 65%"></div>
              </div>
            </div>
            <div class="mini-card">
              <span class="mini-label">This Month</span>
              <span class="mini-value"
                >₱{{ monthlyEarnings.toLocaleString() }}</span
              >
              <div class="mini-bar-bg">
                <div class="mini-bar-fill" style="width: 80%"></div>
              </div>
            </div>
            <div class="mini-card">
              <span class="mini-label">All Time</span>
              <span class="mini-value"
                >₱{{ driver.earnings.toLocaleString() }}</span
              >
              <div class="mini-bar-bg">
                <div
                  class="mini-bar-fill"
                  style="width: 100%; background: #059669"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="activeTab === 'history'" class="tab-content">
          <div class="table-card">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Pickup</th>
                  <th>Delivery</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="d in deliveryHistory" :key="d.id">
                  <td class="id-cell">#{{ d.id }}</td>
                  <td>{{ d.customer }}</td>
                  <td>{{ d.pickup }}</td>
                  <td>{{ d.delivery }}</td>
                  <td>
                    <span class="status-badge" :class="d.status">{{
                      d.status
                    }}</span>
                  </td>
                  <td class="date-cell">{{ d.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="not-found">
      <h2>Driver not found</h2>
      <p>The driver you're looking for doesn't exist.</p>
    </div>

    <div
      class="modal-overlay bottom"
      v-if="viewingDoc"
      @click.self="viewingDoc = null"
    >
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">{{ viewingDoc?.name }}</span>
          <button class="modal-close" @click="viewingDoc = null">
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
          <div class="doc-preview">
            <div class="doc-preview-icon">
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
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
              </svg>
            </div>
            <span class="doc-preview-name">{{ viewingDoc?.name }}</span>
            <span class="doc-preview-status" :class="viewingDoc?.status">
              {{
                viewingDoc?.status === "verified"
                  ? "Verified"
                  : viewingDoc?.status === "pending"
                    ? "Pending"
                    : "Rejected"
              }}
            </span>
            <p class="doc-preview-note">
              This document has been verified and approved.
            </p>
          </div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="viewingDoc = null">
            Close
          </button>
        </div>
      </div>
    </div>

    <div
      class="modal-overlay bottom"
      v-if="reviewingDoc"
      @click.self="reviewingDoc = null"
    >
      <div class="modal-card" @click.stop>
        <div class="modal-top">
          <span class="modal-title">Review: {{ reviewingDoc?.name }}</span>
          <button class="modal-close" @click="reviewingDoc = null">
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
          <div class="review-status">
            <span class="review-label">Current Status</span>
            <span class="doc-preview-status" :class="reviewingDoc?.status">
              {{
                reviewingDoc?.status === "verified"
                  ? "Verified"
                  : reviewingDoc?.status === "pending"
                    ? "Pending"
                    : "Rejected"
              }}
            </span>
          </div>
          <div class="form-group" style="margin-top: 16px">
            <label>Review Notes</label>
            <textarea
              v-model="reviewNotes"
              class="form-input"
              rows="3"
              placeholder="Add notes about this document..."
            ></textarea>
          </div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="reviewingDoc = null">
            Cancel
          </button>
          <button class="modal-btn danger" @click="rejectDoc">Reject</button>
          <button class="modal-btn primary" @click="approveDoc">Approve</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });
const route = useRoute();
const driversStore = useDriversStore();

const driver = computed(() =>
  driversStore.getDriverById(Number(route.params.id)),
);
const activeTab = ref("earnings");

const weeklyEarnings = computed(() =>
  Math.round((driver.value?.earnings || 0) * 0.12),
);
const monthlyEarnings = computed(() =>
  Math.round((driver.value?.earnings || 0) * 0.45),
);

const docs = ref(driversStore.getDocuments());

const viewingDoc = ref(null);
const reviewingDoc = ref(null);
const reviewNotes = ref("");

function viewDoc(doc) {
  viewingDoc.value = doc;
}

function reviewDoc(doc) {
  reviewingDoc.value = doc;
  reviewNotes.value = "";
}

function approveDoc() {
  if (!reviewingDoc.value) return;
  const d = docs.value.find((d) => d.name === reviewingDoc.value.name);
  if (d) d.status = "verified";
  reviewingDoc.value = null;
}

function rejectDoc() {
  if (!reviewingDoc.value) return;
  const d = docs.value.find((d) => d.name === reviewingDoc.value.name);
  if (d) d.status = "rejected";
  reviewingDoc.value = null;
}

const deliveryHistory = computed(() => {
  if (!driver.value) return [];
  return driversStore.getDeliveryHistory(driver.value.id);
});

const goBack = () => navigateTo("/drivers");
</script>

<style scoped>
.detail-page {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  font-size: 13px;
  font-weight: 500;
  color: #059669;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  padding: 0;
  width: fit-content;
}
.back-btn svg {
  width: 16px;
  height: 16px;
}
.back-btn:hover {
  color: #047857;
}
.detail-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.detail-header {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  border: 1px solid #e5e7eb;
}
.detail-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}
.detail-title {
  flex: 1;
}
.detail-title h1 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}
.detail-subtitle {
  font-size: 13px;
  color: #9ca3af;
}
.detail-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}
.status-badge-lg {
  padding: 5px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
}
.status-badge-lg.active {
  background: #f0fdf4;
  color: #059669;
}
.status-badge-lg.idle {
  background: #fef3c7;
  color: #d97706;
}
.status-badge-lg.offline {
  background: #f3f4f6;
  color: #6b7280;
}
.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.info-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #e5e7eb;
}
.info-card h3 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 16px;
}
.info-rows {
  display: flex;
  flex-direction: column;
}
.info-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #f9fafb;
  font-size: 13px;
}
.info-row:last-child {
  border-bottom: none;
}
.info-label {
  color: #9ca3af;
  font-weight: 500;
}
.info-value {
  color: #111827;
  font-weight: 600;
}
.info-value.stars {
  color: #f59e0b;
}
.doc-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.doc-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 12px;
  background: #f9fafb;
  border-radius: 8px;
}
.doc-info {
  display: flex;
  align-items: center;
  gap: 10px;
}
.doc-name {
  font-size: 13px;
  font-weight: 500;
  color: #374151;
}
.doc-status {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 4px;
}
.doc-status.verified {
  background: #f0fdf4;
  color: #059669;
}
.doc-status.pending {
  background: #fef3c7;
  color: #d97706;
}
.doc-status.rejected {
  background: #fef2f2;
  color: #dc2626;
}
.doc-btn {
  padding: 5px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  background: #fff;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  color: #374151;
}
.doc-btn:hover {
  border-color: #059669;
  color: #059669;
}
.doc-btn.verified {
  border-color: #bbf7d0;
  color: #059669;
}
.tabs-card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
}
.tabs-header {
  display: flex;
  border-bottom: 1px solid #e5e7eb;
}
.tab {
  padding: 12px 20px;
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  background: none;
  border: none;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  border-bottom: 2px solid transparent;
  transition: all 0.15s;
}
.tab:hover {
  color: #374151;
}
.tab.active {
  color: #059669;
  border-bottom-color: #059669;
  font-weight: 600;
}
.tab-content {
  padding: 16px;
}
.table-card {
  overflow: hidden;
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
  padding: 10px 12px;
  border-bottom: 1px solid #f3f4f6;
}
.data-table td {
  padding: 12px;
  font-size: 13px;
  color: #374151;
  border-bottom: 1px solid #f9fafb;
}
.id-cell {
  font-weight: 600;
  color: #059669;
}
.date-cell {
  color: #9ca3af;
}
.status-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.status-badge.delivered {
  background: #f0fdf4;
  color: #059669;
}
.status-badge.cancelled {
  background: #fef2f2;
  color: #dc2626;
}
.earnings-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}
.mini-card {
  padding: 16px;
  background: #f9fafb;
  border-radius: 10px;
}
.mini-label {
  display: block;
  font-size: 12px;
  color: #9ca3af;
  margin-bottom: 4px;
}
.mini-value {
  display: block;
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 20px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 12px;
}
.mini-bar-bg {
  height: 6px;
  background: #e5e7eb;
  border-radius: 3px;
  overflow: hidden;
}
.mini-bar-fill {
  height: 100%;
  background: #3b82f6;
  border-radius: 3px;
}
.not-found {
  text-align: center;
  padding: 60px 20px;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}
.not-found h2 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 20px;
  color: #111827;
  margin-bottom: 8px;
}
.not-found p {
  color: #9ca3af;
}
.doc-preview {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 20px 0;
  text-align: center;
}
.doc-preview-icon svg {
  width: 48px;
  height: 48px;
  color: #9ca3af;
}
.doc-preview-name {
  font-size: 15px;
  font-weight: 600;
  color: #111827;
}
.doc-preview-status {
  font-size: 11px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 6px;
}
.doc-preview-status.verified {
  background: #f0fdf4;
  color: #059669;
}
.doc-preview-status.pending {
  background: #fef3c7;
  color: #d97706;
}
.doc-preview-status.rejected {
  background: #fef2f2;
  color: #dc2626;
}
.doc-preview-note {
  font-size: 13px;
  color: #9ca3af;
  margin-top: 4px;
}
.review-status {
  display: flex;
  align-items: center;
  gap: 10px;
}
.review-label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
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
  width: 100%;
  box-sizing: border-box;
  padding: 10px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #111827;
  background: #fff;
  outline: none;
}
.form-input:focus {
  border-color: #059669;
}
textarea.form-input {
  resize: vertical;
  font-family: "Inter", sans-serif;
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
.modal-overlay.bottom {
  align-items: flex-end;
  padding: 0;
}
.modal-overlay.bottom .modal-card {
  border-radius: 16px 16px 0 0;
  max-width: 100%;
  max-height: 70vh;
}
.modal-card {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 400px;
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

@media (max-width: 768px) {
  .detail-grid {
    grid-template-columns: 1fr;
  }
  .earnings-grid {
    grid-template-columns: 1fr;
  }
  .detail-header {
    flex-wrap: wrap;
  }
}
</style>
