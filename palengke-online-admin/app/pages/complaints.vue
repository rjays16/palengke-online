<template>
  <div class="page-shell">
    <div class="tabs-nav">
      <button
        class="tab-btn"
        :class="{ active: activeTab === 'submissions' }"
        @click="activeTab = 'submissions'"
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
          <line x1="16" y1="13" x2="8" y2="13" />
          <line x1="16" y1="17" x2="8" y2="17" />
        </svg>
        Complaints
        <span class="tab-badge" v-if="store.pendingHighPriority.length > 0">{{
          store.pendingHighPriority.length
        }}</span>
      </button>
      <button
        class="tab-btn"
        :class="{ active: activeTab === 'analytics' }"
        @click="activeTab = 'analytics'"
      >
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="18" y1="20" x2="18" y2="10" />
          <line x1="12" y1="20" x2="12" y2="4" />
          <line x1="6" y1="20" x2="6" y2="14" />
        </svg>
        Analytics
      </button>
    </div>

    <div v-if="activeTab === 'submissions'" class="submissions-section">
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
            <input v-model="search" placeholder="Search complaints..." />
          </div>
          <select v-model="statusFilter" class="filter-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="investigating">Investigating</option>
            <option value="resolved">Resolved</option>
            <option value="dismissed">Dismissed</option>
          </select>
          <select v-model="categoryFilter" class="filter-select">
            <option value="">All Categories</option>
            <option value="product_quality">Product Quality</option>
            <option value="delivery">Delivery</option>
            <option value="pricing">Pricing</option>
            <option value="service">Service</option>
            <option value="behavior">Behavior</option>
            <option value="other">Other</option>
          </select>
          <select v-model="priorityFilter" class="filter-select">
            <option value="">All Priority</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
        <span class="result-count">{{ filtered.length }} complaints</span>
      </div>

      <div class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Complainant</th>
              <th>Respondent</th>
              <th>Subject</th>
              <th>Category</th>
              <th>Priority</th>
              <th>Status</th>
              <th>Date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="c in paginatedComplaints"
              :key="c.id"
              class="clickable-row"
              @click="openDetail(c)"
            >
              <td class="id-cell">{{ c.id }}</td>
              <td>
                <span class="name-cell">{{ c.complainant }}</span>
                <span class="type-label" :class="c.complainantType">{{
                  label(c.complainantType)
                }}</span>
              </td>
              <td>
                <span class="name-cell">{{ c.respondent }}</span>
              </td>
              <td class="subject-cell">{{ c.subject }}</td>
              <td>
                <span class="cat-badge" :class="c.category">{{
                  categoryLabel(c.category)
                }}</span>
              </td>
              <td>
                <span class="priority-badge" :class="c.priority">{{
                  c.priority
                }}</span>
              </td>
              <td>
                <span class="status-badge" :class="c.status">{{
                  statusLabel(c.status)
                }}</span>
              </td>
              <td class="date-cell">{{ c.date }}</td>
              <td>
                <div
                  class="actions-drop"
                  v-if="c.status === 'pending' || c.status === 'investigating'"
                >
                  <button
                    class="action-btn small resolve"
                    @click.stop="store.updateStatus(c.id, 'resolved')"
                    title="Resolve"
                  >
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                  </button>
                  <button
                    class="action-btn small dismiss"
                    @click.stop="store.updateStatus(c.id, 'dismissed')"
                    title="Dismiss"
                  >
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
                <span v-else class="resolved-label">{{
                  c.status === "resolved" ? "Resolved" : "Dismissed"
                }}</span>
              </td>
            </tr>
          </tbody>
        </table>
        <PaginationBar
          v-model="complaintPage"
          :total="filtered.length"
          :perPage="complaintPerPage"
        />
      </div>
    </div>

    <div v-if="activeTab === 'analytics'" class="analytics-section">
      <div class="analytics-header">
        <h2>Complaint Analytics</h2>
        <button class="export-btn" @click="exportCSV">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7 10 12 15 17 10" />
            <line x1="12" y1="15" x2="12" y2="3" />
          </svg>
          Export CSV
        </button>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <span class="stat-label">Total Complaints</span
          ><span class="stat-value">{{ a.total }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Pending</span
          ><span class="stat-value pending">{{ a.pending }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Investigating</span
          ><span class="stat-value progress">{{ a.investigating }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Resolved</span
          ><span class="stat-value resolved">{{ a.resolved }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Dismissed</span
          ><span class="stat-value dismissed">{{ a.dismissed }}</span>
        </div>
      </div>

      <div class="analytics-grid-2">
        <div class="analytics-card">
          <h3>By Category</h3>
          <div class="bar-list">
            <div class="bar-item">
              <span class="bar-label">Product Quality</span>
              <div class="bar-track">
                <div
                  class="bar-fill quality"
                  :style="{
                    width: (a.byCategory.product_quality / a.total) * 100 + '%',
                  }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byCategory.product_quality }}</span>
            </div>
            <div class="bar-item">
              <span class="bar-label">Delivery</span>
              <div class="bar-track">
                <div
                  class="bar-fill delivery"
                  :style="{
                    width: (a.byCategory.delivery / a.total) * 100 + '%',
                  }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byCategory.delivery }}</span>
            </div>
            <div class="bar-item">
              <span class="bar-label">Pricing</span>
              <div class="bar-track">
                <div
                  class="bar-fill pricing"
                  :style="{
                    width: (a.byCategory.pricing / a.total) * 100 + '%',
                  }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byCategory.pricing }}</span>
            </div>
            <div class="bar-item">
              <span class="bar-label">Service</span>
              <div class="bar-track">
                <div
                  class="bar-fill service"
                  :style="{
                    width: (a.byCategory.service / a.total) * 100 + '%',
                  }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byCategory.service }}</span>
            </div>
            <div class="bar-item">
              <span class="bar-label">Behavior</span>
              <div class="bar-track">
                <div
                  class="bar-fill behavior"
                  :style="{
                    width: (a.byCategory.behavior / a.total) * 100 + '%',
                  }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byCategory.behavior }}</span>
            </div>
            <div class="bar-item">
              <span class="bar-label">Other</span>
              <div class="bar-track">
                <div
                  class="bar-fill other"
                  :style="{ width: (a.byCategory.other / a.total) * 100 + '%' }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byCategory.other }}</span>
            </div>
          </div>
        </div>
        <div class="analytics-card">
          <h3>By Priority</h3>
          <div class="bar-list">
            <div class="bar-item">
              <span class="bar-label">High</span>
              <div class="bar-track">
                <div
                  class="bar-fill high"
                  :style="{ width: (a.byPriority.high / a.total) * 100 + '%' }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byPriority.high }}</span>
            </div>
            <div class="bar-item">
              <span class="bar-label">Medium</span>
              <div class="bar-track">
                <div
                  class="bar-fill medium"
                  :style="{
                    width: (a.byPriority.medium / a.total) * 100 + '%',
                  }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byPriority.medium }}</span>
            </div>
            <div class="bar-item">
              <span class="bar-label">Low</span>
              <div class="bar-track">
                <div
                  class="bar-fill low"
                  :style="{ width: (a.byPriority.low / a.total) * 100 + '%' }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byPriority.low }}</span>
            </div>
          </div>
          <h3 style="margin-top: 24px">Resolution Rate</h3>
          <div class="resolution-ring">
            <svg viewBox="0 0 100 100" class="ring-svg">
              <circle cx="50" cy="50" r="40" class="ring-bg" />
              <circle
                cx="50"
                cy="50"
                r="40"
                class="ring-fill"
                :stroke-dasharray="circumference"
                :stroke-dashoffset="ringOffset"
              />
            </svg>
            <div class="ring-center">
              <span class="ring-pct">{{ resolutionRate }}%</span>
              <span class="ring-label">Resolved</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-overlay" v-if="selected" @click="selected = null">
      <div class="modal-card" @click.stop>
        <div class="modal-top">
          <div class="modal-id">{{ selected.id }}</div>
          <span class="status-badge" :class="selected.status">{{
            statusLabel(selected.status)
          }}</span>
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
          <h3 class="modal-subject">{{ selected.subject }}</h3>

          <div class="modal-meta-grid">
            <div class="modal-meta-item">
              <span class="meta-label">Complainant</span>
              <span class="meta-value">{{ selected.complainant }}</span>
              <span class="type-label" :class="selected.complainantType">{{
                label(selected.complainantType)
              }}</span>
            </div>
            <div class="modal-meta-item">
              <span class="meta-label">Respondent</span>
              <span class="meta-value">{{ selected.respondent }}</span>
              <span class="type-label" :class="selected.respondentType">{{
                label(selected.respondentType)
              }}</span>
            </div>
            <div class="modal-meta-item">
              <span class="meta-label">Category</span>
              <span class="cat-badge" :class="selected.category">{{
                categoryLabel(selected.category)
              }}</span>
            </div>
            <div class="modal-meta-item">
              <span class="meta-label">Priority</span>
              <span class="priority-badge" :class="selected.priority">{{
                selected.priority
              }}</span>
            </div>
            <div class="modal-meta-item">
              <span class="meta-label">Filed On</span>
              <span class="meta-value">{{ selected.date }}</span>
            </div>
          </div>

          <div class="modal-section">
            <h4>Description</h4>
            <p class="modal-desc">{{ selected.description }}</p>
          </div>

          <div class="modal-section" v-if="selected.resolution">
            <h4>Resolution Notes</h4>
            <p class="modal-desc resolved-text">{{ selected.resolution }}</p>
          </div>

          <div
            class="modal-section"
            v-if="
              selected.status === 'pending' ||
              selected.status === 'investigating'
            "
          >
            <h4>Resolution Notes</h4>
            <textarea
              v-model="resolutionNotes"
              class="resolution-input"
              placeholder="Add resolution notes before resolving or dismissing..."
              rows="3"
            ></textarea>
          </div>
        </div>

        <div
          class="modal-actions"
          v-if="
            selected.status === 'pending' || selected.status === 'investigating'
          "
        >
          <button class="modal-btn dismiss-btn" @click="handleDismiss">
            Dismiss
          </button>
          <button class="modal-btn resolve-btn" @click="handleResolve">
            Resolve
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });
const store = useComplaintsStore();
const a = store.analytics;

const activeTab = ref("submissions");
const search = ref("");
const statusFilter = ref("");
const categoryFilter = ref("");
const priorityFilter = ref("");
const selected = ref(null);
const resolutionNotes = ref("");

const filtered = computed(() => {
  return store.complaints.filter((c) => {
    const q = search.value.toLowerCase();
    const matchSearch =
      !q ||
      c.id.toLowerCase().includes(q) ||
      c.complainant.toLowerCase().includes(q) ||
      c.respondent.toLowerCase().includes(q) ||
      c.subject.toLowerCase().includes(q);
    const matchStatus = !statusFilter.value || c.status === statusFilter.value;
    const matchCategory =
      !categoryFilter.value || c.category === categoryFilter.value;
    const matchPriority =
      !priorityFilter.value || c.priority === priorityFilter.value;
    return matchSearch && matchStatus && matchCategory && matchPriority;
  });
});

const complaintPage = ref(1);
const complaintPerPage = ref(10);
const paginatedComplaints = computed(() => {
  const start = (complaintPage.value - 1) * complaintPerPage.value;
  return filtered.value.slice(start, start + complaintPerPage.value);
});
watch([search, statusFilter, categoryFilter, priorityFilter], () => {
  complaintPage.value = 1;
});

const circumference = 2 * Math.PI * 40;

const resolutionRate = computed(() => {
  if (a.total === 0) return 0;
  return Math.round(
    (a.resolved / (a.resolved + a.dismissed + a.investigating + a.pending)) *
      100,
  );
});

const ringOffset = computed(() => {
  return circumference - (resolutionRate.value / 100) * circumference;
});

function label(type) {
  const map = {
    customer: "Customer",
    seller: "Seller",
    driver: "Driver",
    system: "System",
  };
  return map[type] || type;
}

function categoryLabel(cat) {
  const map = {
    product_quality: "Product Quality",
    delivery: "Delivery",
    pricing: "Pricing",
    service: "Service",
    behavior: "Behavior",
    other: "Other",
  };
  return map[cat] || cat;
}

function statusLabel(s) {
  const map = {
    pending: "Pending",
    investigating: "Investigating",
    resolved: "Resolved",
    dismissed: "Dismissed",
  };
  return map[s] || s;
}

function openDetail(c) {
  selected.value = c;
  resolutionNotes.value = "";
}

function handleResolve() {
  if (resolutionNotes.value.trim()) {
    store.updateResolution(selected.value.id, resolutionNotes.value);
  }
  store.updateStatus(selected.value.id, "resolved");
  selected.value = null;
  resolutionNotes.value = "";
}

function handleDismiss() {
  if (resolutionNotes.value.trim()) {
    store.updateResolution(selected.value.id, resolutionNotes.value);
  }
  store.updateStatus(selected.value.id, "dismissed");
  selected.value = null;
  resolutionNotes.value = "";
}

function exportCSV() {
  const headers = [
    "ID",
    "Complainant",
    "Complainant Type",
    "Respondent",
    "Respondent Type",
    "Subject",
    "Description",
    "Category",
    "Status",
    "Priority",
    "Date",
    "Resolution",
  ];
  const rows = store.complaints.map((c) => [
    c.id,
    c.complainant,
    c.complainantType,
    c.respondent,
    c.respondentType,
    c.subject,
    c.description,
    c.category,
    c.status,
    c.priority,
    c.date,
    c.resolution,
  ]);
  const csv = [headers, ...rows]
    .map((row) => row.map((cell) => `"${cell}"`).join(","))
    .join("\n");
  const blob = new Blob([csv], { type: "text/csv" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `complaints-export-${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
  URL.revokeObjectURL(url);
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
}
.name-cell {
  font-weight: 500;
  color: #111827;
}
.subject-cell {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #6b7280;
}
.date-cell {
  color: #9ca3af;
  white-space: nowrap;
}

.type-label {
  display: inline-block;
  padding: 2px 7px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 600;
  margin-left: 6px;
  vertical-align: middle;
}
.type-label.customer {
  background: #f5f3ff;
  color: #7c3aed;
}
.type-label.seller {
  background: #f0fdf4;
  color: #059669;
}
.type-label.driver {
  background: #eff6ff;
  color: #3b82f6;
}
.type-label.system {
  background: #f3f4f6;
  color: #6b7280;
}

.cat-badge {
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
}
.cat-badge.product_quality {
  background: #f0fdf4;
  color: #059669;
}
.cat-badge.delivery {
  background: #eff6ff;
  color: #3b82f6;
}
.cat-badge.pricing {
  background: #fef3c7;
  color: #d97706;
}
.cat-badge.service {
  background: #f5f3ff;
  color: #7c3aed;
}
.cat-badge.behavior {
  background: #fef2f2;
  color: #dc2626;
}
.cat-badge.other {
  background: #f3f4f6;
  color: #6b7280;
}

.priority-badge {
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.priority-badge.high {
  background: #fef2f2;
  color: #dc2626;
}
.priority-badge.medium {
  background: #fef3c7;
  color: #d97706;
}
.priority-badge.low {
  background: #f3f4f6;
  color: #6b7280;
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
.status-badge.investigating {
  background: #eff6ff;
  color: #3b82f6;
}
.status-badge.resolved {
  background: #f0fdf4;
  color: #059669;
}
.status-badge.dismissed {
  background: #f3f4f6;
  color: #6b7280;
}

.actions-drop {
  display: flex;
  gap: 4px;
}
.action-btn.small {
  padding: 4px 6px;
  border-radius: 5px;
  border: 1px solid #e5e7eb;
  background: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
}
.action-btn.small svg {
  width: 14px;
  height: 14px;
}
.action-btn.resolve {
  color: #059669;
}
.action-btn.resolve:hover {
  border-color: #059669;
  background: #f0fdf4;
}
.action-btn.dismiss {
  color: #dc2626;
}
.action-btn.dismiss:hover {
  border-color: #dc2626;
  background: #fef2f2;
}
.resolved-label {
  font-size: 12px;
  color: #9ca3af;
  font-style: italic;
}

.analytics-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.analytics-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.analytics-header h2 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}
.export-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 9px 16px;
  background: #059669;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  font-family: "Inter", sans-serif;
  cursor: pointer;
  transition: background 0.15s;
}
.export-btn:hover {
  background: #047857;
}
.export-btn svg {
  width: 16px;
  height: 16px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
}
.stat-card {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  border: 1px solid #e5e7eb;
  text-align: center;
}
.stat-label {
  display: block;
  font-size: 12px;
  color: #9ca3af;
  font-weight: 500;
  margin-bottom: 6px;
}
.stat-value {
  display: block;
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 28px;
  font-weight: 700;
  color: #111827;
}
.stat-value.pending {
  color: #d97706;
}
.stat-value.progress {
  color: #3b82f6;
}
.stat-value.resolved {
  color: #059669;
}
.stat-value.dismissed {
  color: #6b7280;
}

.analytics-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.analytics-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #e5e7eb;
}
.analytics-card h3 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 16px;
}

.bar-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.bar-item {
  display: flex;
  align-items: center;
  gap: 12px;
}
.bar-label {
  font-size: 13px;
  color: #374151;
  min-width: 100px;
  font-weight: 500;
}
.bar-track {
  flex: 1;
  height: 8px;
  background: #f3f4f6;
  border-radius: 4px;
  overflow: hidden;
}
.bar-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.4s;
}
.bar-fill.quality {
  background: #059669;
}
.bar-fill.delivery {
  background: #3b82f6;
}
.bar-fill.pricing {
  background: #d97706;
}
.bar-fill.service {
  background: #7c3aed;
}
.bar-fill.behavior {
  background: #dc2626;
}
.bar-fill.other {
  background: #6b7280;
}
.bar-fill.high {
  background: #dc2626;
}
.bar-fill.medium {
  background: #d97706;
}
.bar-fill.low {
  background: #6b7280;
}
.bar-count {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  min-width: 24px;
  text-align: right;
}

.resolution-ring {
  position: relative;
  width: 140px;
  height: 140px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
}
.ring-svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}
.ring-bg {
  fill: none;
  stroke: #f3f4f6;
  stroke-width: 8;
}
.ring-fill {
  fill: none;
  stroke: #059669;
  stroke-width: 8;
  stroke-linecap: round;
  transition: stroke-dashoffset 0.6s;
}
.ring-center {
  position: absolute;
  text-align: center;
}
.ring-pct {
  display: block;
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 26px;
  font-weight: 800;
  color: #059669;
}
.ring-label {
  font-size: 11px;
  color: #9ca3af;
  font-weight: 500;
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
  max-width: 600px;
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
  font-size: 16px;
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
.modal-subject {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 17px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 16px;
}
.modal-meta-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 20px;
}
.modal-meta-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.modal-meta-item .meta-label {
  font-size: 11px;
  color: #9ca3af;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.modal-meta-item .meta-value {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
}
.modal-section {
  margin-bottom: 16px;
}
.modal-section h4 {
  font-size: 12px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 0 0 8px;
}
.modal-desc {
  font-size: 14px;
  color: #374151;
  line-height: 1.6;
  margin: 0;
  background: #f9fafb;
  padding: 14px 16px;
  border-radius: 8px;
  border-left: 3px solid #e5e7eb;
}
.modal-desc.resolved-text {
  border-left-color: #059669;
  background: #f0fdf4;
}
.resolution-input {
  width: 100%;
  padding: 12px 14px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #374151;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  outline: none;
  resize: vertical;
  background: #f9fafb;
}
.resolution-input:focus {
  border-color: #059669;
  background: #fff;
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
.resolve-btn {
  background: #059669;
  color: #fff;
}
.resolve-btn:hover {
  background: #047857;
}
.dismiss-btn {
  background: #f3f4f6;
  color: #6b7280;
}
.dismiss-btn:hover {
  background: #fef2f2;
  color: #dc2626;
}

@media (max-width: 768px) {
  .data-table {
    min-width: 700px;
  }
  .search-box {
    min-width: 160px;
  }
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .analytics-grid-2 {
    grid-template-columns: 1fr;
  }
  .tabs-nav {
    width: 100%;
  }
  .tab-btn {
    flex: 1;
    justify-content: center;
  }
  .modal-meta-grid {
    grid-template-columns: 1fr;
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
