<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div class="settings-grid">
      <div class="settings-section">
        <div class="section-header">
          <div>
            <h3>Operating Countries</h3>
            <p class="section-desc">
              Enable or disable countries where the platform operates. Only enabled countries will appear in the seller registration dropdown.
            </p>
          </div>
          <div class="bulk-actions">
            <button class="text-btn" @click="enableAll">Enable All</button>
            <button class="text-btn" @click="disableAll">Disable All</button>
          </div>
        </div>

        <div v-if="isLoading" class="loading-state">Loading countries...</div>
        <div v-else-if="error" class="error-state">{{ error }}</div>
        <div v-else class="countries-list">
          <div v-for="country in countries" :key="country.id">
            <div class="country-row">
              <label class="toggle-label">
                <input
                  type="checkbox"
                  v-model="country.enabled"
                  @change="onToggle(country)"
                />
                <span class="checkmark"></span>
                <span class="country-name">{{ country.name }}</span>
                <span class="country-code">{{ country.code }}</span>
                <span class="dial-code">{{ country.dial_code }}</span>
              </label>
              <span class="status-badge" :class="country.enabled ? 'active' : 'inactive'">
                {{ country.enabled ? 'Enabled' : 'Disabled' }}
              </span>
              <button
                v-if="country.code === 'PH'"
                class="expand-btn"
                @click="toggleRegions(country)"
              >
                <span v-if="country._expanded">&#9660;</span>
                <span v-else>&#9654;</span>
              </button>
            </div>
            <div v-if="country.code === 'PH' && country._expanded" class="regions-list">
              <div v-if="country._loadingRegions" class="loading-state">Loading regions...</div>
              <div
                v-for="region in country._regions"
                :key="region.id"
                class="region-row"
              >
                <label class="toggle-label region-toggle">
                  <input
                    type="checkbox"
                    v-model="region.enabled"
                    @change="onRegionToggle(country, region)"
                  />
                  <span class="checkmark"></span>
                  <span class="region-name">{{ region.name }}</span>
                </label>
                <span class="status-badge" :class="region.enabled ? 'active' : 'inactive'">
                  {{ region.enabled ? 'Enabled' : 'Disabled' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Summary</h3>
        <div class="summary-grid">
          <div class="summary-card">
            <span class="summary-label">Total Countries</span>
            <span class="summary-value">{{ totalCountries }}</span>
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
      ✓ Country settings saved successfully
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

const countries = ref<any[]>([]);
const isLoading = ref(false);
const success = ref(false);
const error = ref("");
const expandedRegions = ref<Set<number>>(new Set());

const totalCountries = computed(() => countries.value.length);
const enabledCount = computed(() => countries.value.filter((c) => c.enabled).length);
const disabledCount = computed(() => countries.value.filter((c) => !c.enabled).length);

async function fetchCountries() {
  isLoading.value = true;
  error.value = "";

  try {
    const token = getToken();
    const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/countries`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    countries.value = response.countries.map((c: any) => ({
      ...c,
      _expanded: false,
      _loadingRegions: false,
      _regions: [],
    }));
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to load countries";
  } finally {
    isLoading.value = false;
  }
}

async function onToggle(country: any) {
  error.value = "";
  success.value = false;

  try {
    const token = getToken();
    await $fetch(`${getApiBaseUrl()}/api/admin/countries/${country.id}/toggle`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
      },
      body: { enabled: country.enabled },
    });

    success.value = true;
    setTimeout(() => {
      success.value = false;
    }, 2500);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to update country";
    country.enabled = !country.enabled;
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
    const payload = countries.value.map((c) => ({
      id: c.id,
      enabled,
    }));

    await $fetch(`${getApiBaseUrl()}/api/admin/countries/bulk`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${token}`,
      },
      body: { countries: payload },
    });

    countries.value.forEach((c) => (c.enabled = enabled));
    success.value = true;
    setTimeout(() => {
      success.value = false;
    }, 2500);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to update countries";
  }
}

async function toggleRegions(country: any) {
  if (!country._expanded) {
    country._expanded = true;
    country._loadingRegions = true;
    try {
      const token = getToken();
      const response: any = await $fetch(
        `${getApiBaseUrl()}/api/admin/countries/${country.id}/regions`,
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );
      country._regions = response.regions;
    } catch {
      country._regions = [];
    } finally {
      country._loadingRegions = false;
    }
  } else {
    country._expanded = false;
  }
}

async function onRegionToggle(country: any, region: any) {
  try {
    const token = getToken();
    const response: any = await $fetch(
      `${getApiBaseUrl()}/api/admin/countries/${country.id}/regions/${region.id}/toggle`,
      {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    region.enabled = response.region.enabled;
    success.value = true;
    setTimeout(() => { success.value = false; }, 2500);
  } catch {
    region.enabled = !region.enabled;
  }
}

onMounted(() => {
  fetchCountries();
});
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 900px;
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
  margin-bottom: 20px;
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
.countries-list {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.country-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-radius: 8px;
  transition: background 0.1s;
}
.country-row:hover {
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
.country-name {
  font-size: 14px;
  font-weight: 500;
  color: #111827;
}
.country-code {
  font-size: 12px;
  color: #9ca3af;
  background: #f3f4f6;
  padding: 2px 8px;
  border-radius: 4px;
}
.dial-code {
  font-size: 12px;
  color: #6b7280;
  background: #e5e7eb;
  padding: 2px 8px;
  border-radius: 4px;
  font-weight: 600;
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
.expand-btn {
  background: none;
  border: none;
  font-size: 12px;
  cursor: pointer;
  padding: 4px 8px;
  color: #6b7280;
  font-family: "Inter", sans-serif;
  transition: color 0.15s;
}
.expand-btn:hover {
  color: #111827;
}
.regions-list {
  padding: 0 16px 8px 48px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.region-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 12px;
  border-radius: 6px;
  transition: background 0.1s;
}
.region-row:hover {
  background: #f9fafb;
}
.region-toggle {
  gap: 10px;
}
.region-name {
  font-size: 13px;
  font-weight: 500;
  color: #374151;
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
