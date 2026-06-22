<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div v-if="isDriver" class="role-banner driver-banner">
      <span class="role-banner-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="16" r="3"/><circle cx="18" cy="16" r="3"/><path d="M9 16h9"/><path d="M4 9h5l2 4"/><path d="M18 5l-3 2v9"/><rect x="13" y="6" width="5" height="4" rx="1"/></svg></span> Driver Help Topics
    </div>
    <div v-if="isSeller" class="role-banner seller-banner">
      <span class="role-banner-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span> Seller Help Topics
    </div>

    <div class="settings-grid">
      <div class="settings-section">
        <div class="section-header">
          <div>
            <h3>Help Topics</h3>
            <p class="section-desc">
              Enable or disable help topics. Only enabled topics will appear in the driver and seller app Help & Support pages.
            </p>
          </div>
          <div class="bulk-actions">
            <button class="text-btn" @click="enableAll">Enable All</button>
            <button class="text-btn" @click="disableAll">Disable All</button>
          </div>
        </div>

        <div class="filter-tabs">
          <button
            class="filter-tab"
            :class="{ active: filterRole === 'all' }"
            @click="filterRole = 'all'"
          >All</button>
          <button
            class="filter-tab"
            :class="{ active: filterRole === 'driver' }"
            @click="filterRole = 'driver'"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><circle cx="6" cy="16" r="3"/><circle cx="18" cy="16" r="3"/><path d="M9 16h9"/><path d="M4 9h5l2 4"/><path d="M18 5l-3 2v9"/><rect x="13" y="6" width="5" height="4" rx="1"/></svg>
            Driver
          </button>
          <button
            class="filter-tab"
            :class="{ active: filterRole === 'seller' }"
            @click="filterRole = 'seller'"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Seller
          </button>
        </div>

        <div v-if="isLoading" class="loading-state">Loading help topics...</div>
        <div v-else-if="error" class="error-state">{{ error }}</div>
        <div v-else class="topics-list">
          <div v-for="topic in filteredTopics" :key="topic.id" class="topic-row">
            <label class="toggle-label">
              <input
                type="checkbox"
                v-model="topic.enabled"
                @change="onToggle(topic)"
              />
              <span class="checkmark"></span>
              <div class="topic-info">
                <div class="topic-name-row">
                  <span class="topic-name">{{ topic.name }}</span>
                  <span class="topic-role" :class="topic.role">{{ topic.role }}</span>
                </div>
                <span class="topic-key">{{ topic.key }}</span>
              </div>
            </label>
            <span class="status-badge" :class="topic.enabled ? 'active' : 'inactive'">
              {{ topic.enabled ? 'Enabled' : 'Disabled' }}
            </span>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Summary</h3>
        <div class="summary-grid">
          <div class="summary-card">
            <span class="summary-label">Total Topics</span>
            <span class="summary-value">{{ totalTopics }}</span>
          </div>
          <div class="summary-card">
            <span class="summary-label">Enabled</span>
            <span class="summary-value">{{ enabledCount }}</span>
          </div>
          <div class="summary-card">
            <span class="summary-label">Disabled</span>
            <span class="summary-value">{{ disabledCount }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">
      ✓ Help topic settings saved successfully
    </div>

    <div class="error-toast" v-if="error && !isLoading">
      ✕ {{ error }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import SettingsSubnav from "~/components/SettingsSubnav.vue";

definePageMeta({
  layout: "default",
});

const auth = useAuthStore();
const { isDriver, isSeller } = useSettingsRole();
auth.restore();
onMounted(() => {
  if (auth.user?.role !== "superadmin") {
    navigateTo("/dashboard");
  }
});

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem("palengke_auth_token");
}

const topics = ref<any[]>([]);
const isLoading = ref(false);
const success = ref(false);
const error = ref("");
const filterRole = ref("all");

const filteredTopics = computed(() => {
  if (filterRole.value === "all") return topics.value;
  return topics.value.filter((t) => t.role === filterRole.value);
});

const totalTopics = computed(() => filteredTopics.value.length);
const enabledCount = computed(() => filteredTopics.value.filter((t) => t.enabled).length);
const disabledCount = computed(() => filteredTopics.value.filter((t) => !t.enabled).length);

async function fetchTopics() {
  isLoading.value = true;
  error.value = "";

  try {
    const token = getToken();
    const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/help-topics`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    topics.value = response.topics;
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to load help topics";
  } finally {
    isLoading.value = false;
  }
}

async function onToggle(topic: any) {
  error.value = "";
  success.value = false;

  try {
    const token = getToken();
    await $fetch(`${getApiBaseUrl()}/api/admin/help-topics`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${token}`,
      },
      body: {
        topics: [{ id: topic.id, enabled: topic.enabled }],
      },
    });

    success.value = true;
    setTimeout(() => {
      success.value = false;
    }, 2500);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to update help topic";
    topic.enabled = !topic.enabled;
  }
}

async function enableAll() {
  await updateAll(true);
}

async function disableAll() {
  await updateAll(false);
}

async function updateAll(enabled: boolean) {
  error.value = "";
  success.value = false;

  try {
    const token = getToken();
    const payload = filteredTopics.value.map((t) => ({
      id: t.id,
      enabled,
    }));

    await $fetch(`${getApiBaseUrl()}/api/admin/help-topics`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${token}`,
      },
      body: { topics: payload },
    });

    filteredTopics.value.forEach((t) => (t.enabled = enabled));
    success.value = true;
    setTimeout(() => {
      success.value = false;
    }, 2500);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to update help topics";
  }
}

onMounted(() => {
  fetchTopics();
});
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 900px;
}
.role-banner {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
}
.role-banner-icon {
  display: inline-flex;
  align-items: center;
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}
.role-banner-icon svg {
  width: 100%;
  height: 100%;
}
.seller-banner {
  background: #f0fdf4;
  color: #059669;
  border: 1px solid #bbf7d0;
}
.driver-banner {
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
}
.settings-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.settings-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  border: 1px solid #e5e7eb;
}
.settings-section h3 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 15px;
  font-weight: 600;
  color: #111827;
  margin: 0;
}
.section-desc {
  font-size: 13px;
  color: #9ca3af;
  margin: 6px 0 0;
}
.section-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}
.bulk-actions {
  display: flex;
  gap: 10px;
  flex-shrink: 0;
}
.text-btn {
  background: none;
  border: none;
  font-size: 12px;
  font-weight: 600;
  color: #059669;
  cursor: pointer;
  padding: 6px 10px;
  border-radius: 6px;
  font-family: "Inter", sans-serif;
  transition: background 0.15s;
}
.text-btn:hover {
  background: #f0fdf4;
}
.filter-tabs {
  display: flex;
  gap: 6px;
  margin-bottom: 16px;
  padding: 4px;
  background: #f3f4f6;
  border-radius: 8px;
  width: fit-content;
}
.filter-tab {
  display: flex;
  align-items: center;
  gap: 5px;
  background: none;
  border: none;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  transition: all 0.15s;
}
.filter-tab.active {
  background: white;
  color: #111827;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.tab-icon {
  width: 14px;
  height: 14px;
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
.topics-list {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.topic-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-radius: 8px;
  transition: background 0.1s;
}
.topic-row:hover {
  background: #f9fafb;
}
.toggle-label {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  flex: 1;
  min-width: 0;
}
.toggle-label input {
  display: none;
}
.checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid #d1d5db;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.15s;
  position: relative;
}
.toggle-label input:checked + .checkmark {
  background: #059669;
  border-color: #059669;
}
.toggle-label input:checked + .checkmark::after {
  content: "";
  width: 5px;
  height: 9px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}
.topic-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.topic-name-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.topic-name {
  font-size: 14px;
  font-weight: 500;
  color: #111827;
}
.topic-role {
  font-size: 10px;
  font-weight: 600;
  padding: 1px 6px;
  border-radius: 4px;
  text-transform: uppercase;
}
.topic-role.driver {
  background: #eff6ff;
  color: #2563eb;
}
.topic-role.seller {
  background: #f0fdf4;
  color: #059669;
}
.topic-key {
  font-size: 12px;
  color: #9ca3af;
}
.status-badge {
  font-size: 11px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 6px;
  white-space: nowrap;
}
.status-badge.active {
  background: #f0fdf4;
  color: #059669;
}
.status-badge.inactive {
  background: #f3f4f6;
  color: #6b7280;
}
.summary-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}
.summary-card {
  background: #f9fafb;
  border-radius: 10px;
  padding: 16px;
  text-align: center;
}
.summary-label {
  display: block;
  font-size: 12px;
  color: #9ca3af;
  margin-bottom: 6px;
}
.summary-value {
  display: block;
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 20px;
  font-weight: 700;
  color: #111827;
}
.save-toast {
  padding: 10px 16px;
  background: #f0fdf4;
  color: #059669;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}
.error-toast {
  padding: 10px 16px;
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}
@media (max-width: 600px) {
  .summary-grid {
    grid-template-columns: 1fr;
  }
  .section-header {
    flex-direction: column;
  }
}
</style>
