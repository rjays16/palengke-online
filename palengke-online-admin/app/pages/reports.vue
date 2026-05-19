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
          <polyline points="10 9 9 9 8 9" />
        </svg>
        Submissions
        <span class="tab-badge" v-if="reportsStore.analytics.pending > 0">{{
          reportsStore.analytics.pending
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
            <input v-model="search" placeholder="Search reports..." />
          </div>
          <select v-model="typeFilter" class="filter-select">
            <option value="">All Types</option>
            <option value="seller">Seller</option>
            <option value="driver">Driver</option>
            <option value="customer">Customer</option>
          </select>
          <select v-model="statusFilter" class="filter-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="resolved">Resolved</option>
            <option value="dismissed">Dismissed</option>
          </select>
        </div>
        <span class="result-count">{{ filtered.length }} reports</span>
      </div>

      <div class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Type</th>
              <th>Submitted By</th>
              <th>Subject</th>
              <th>Priority</th>
              <th>Status</th>
              <th>Date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in paginatedReports" :key="r.id">
              <td class="id-cell">{{ r.id }}</td>
              <td>
                <span class="type-badge" :class="r.type">{{
                  r.type === "seller"
                    ? "Seller"
                    : r.type === "driver"
                      ? "Driver"
                      : "Customer"
                }}</span>
              </td>
              <td>
                <span class="name-cell">{{ r.submittedBy }}</span>
              </td>
              <td class="subject-cell">{{ r.subject }}</td>
              <td>
                <span class="priority-badge" :class="r.priority">{{
                  r.priority
                }}</span>
              </td>
              <td>
                <span class="status-badge" :class="r.status">{{
                  statusLabel(r.status)
                }}</span>
              </td>
              <td class="date-cell">{{ r.date }}</td>
              <td>
                <div
                  class="actions-drop"
                  v-if="r.status === 'pending' || r.status === 'in_progress'"
                >
                  <button
                    class="action-btn small resolve"
                    @click="reportsStore.updateStatus(r.id, 'resolved')"
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
                    @click="reportsStore.updateStatus(r.id, 'dismissed')"
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
                  r.status === "resolved" ? "Resolved" : "Dismissed"
                }}</span>
              </td>
            </tr>
          </tbody>
        </table>
        <PaginationBar
          v-model="reportPage"
          :total="filtered.length"
          :perPage="reportPerPage"
        />
      </div>
    </div>

    <div v-if="activeTab === 'analytics'" class="analytics-section">
      <div class="analytics-header">
        <h2>Report Analytics</h2>
        <button class="export-btn" @click="reportsStore.exportCSV">
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
          <span class="stat-label">Total Reports</span
          ><span class="stat-value">{{ a.total }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Pending</span
          ><span class="stat-value pending">{{ a.pending }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">In Progress</span
          ><span class="stat-value progress">{{ a.inProgress }}</span>
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
          <h3>By Type</h3>
          <div class="bar-list">
            <div class="bar-item">
              <span class="bar-label">Sellers</span>
              <div class="bar-track">
                <div
                  class="bar-fill seller"
                  :style="{ width: (a.byType.seller / a.total) * 100 + '%' }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byType.seller }}</span>
            </div>
            <div class="bar-item">
              <span class="bar-label">Drivers</span>
              <div class="bar-track">
                <div
                  class="bar-fill driver"
                  :style="{ width: (a.byType.driver / a.total) * 100 + '%' }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byType.driver }}</span>
            </div>
            <div class="bar-item">
              <span class="bar-label">Customers</span>
              <div class="bar-track">
                <div
                  class="bar-fill customer"
                  :style="{ width: (a.byType.customer / a.total) * 100 + '%' }"
                ></div>
              </div>
              <span class="bar-count">{{ a.byType.customer }}</span>
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
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });
const reportsStore = useReportsStore();
const a = reportsStore.analytics;

const activeTab = ref("submissions");
const search = ref("");
const typeFilter = ref("");
const statusFilter = ref("");

const filtered = computed(() => {
  return reportsStore.submissions.filter((r) => {
    const q = search.value.toLowerCase();
    const matchSearch =
      !q ||
      r.id.toLowerCase().includes(q) ||
      r.submittedBy.toLowerCase().includes(q) ||
      r.subject.toLowerCase().includes(q);
    const matchType = !typeFilter.value || r.type === typeFilter.value;
    const matchStatus = !statusFilter.value || r.status === statusFilter.value;
    return matchSearch && matchType && matchStatus;
  });
});

const reportPage = ref(1);
const reportPerPage = ref(10);
const paginatedReports = computed(() => {
  const start = (reportPage.value - 1) * reportPerPage.value;
  return filtered.value.slice(start, start + reportPerPage.value);
});
watch([search, typeFilter, statusFilter], () => {
  reportPage.value = 1;
});

function statusLabel(s) {
  return s === "in_progress"
    ? "In Progress"
    : s.charAt(0).toUpperCase() + s.slice(1);
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
  max-width: 240px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #6b7280;
}
.date-cell {
  color: #9ca3af;
  white-space: nowrap;
}

.type-badge {
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.type-badge.seller {
  background: #f0fdf4;
  color: #059669;
}
.type-badge.driver {
  background: #eff6ff;
  color: #3b82f6;
}
.type-badge.customer {
  background: #f5f3ff;
  color: #7c3aed;
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
.status-badge.in_progress {
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
  min-width: 72px;
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
.bar-fill.seller {
  background: #059669;
}
.bar-fill.driver {
  background: #3b82f6;
}
.bar-fill.customer {
  background: #7c3aed;
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
