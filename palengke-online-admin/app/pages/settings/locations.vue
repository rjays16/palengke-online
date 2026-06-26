<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div class="settings-grid">
      <div class="settings-section">
        <div class="section-header">
          <div>
            <h3>Service Locations</h3>
            <p class="section-desc">
              Enable or disable areas where the platform operates (Customer, Seller, and Driver apps).
            </p>
          </div>
          <div class="bulk-actions">
            <button class="text-btn" @click="expandAll">Expand All</button>
            <button class="text-btn" @click="collapseAll">Collapse All</button>
          </div>
        </div>

        <div v-if="isLoading || countries.length === 0" class="loading-state">{{ isLoading ? 'Loading locations...' : 'Loading countries...' }}</div>
        <div v-else class="countries-wrap">
          <div v-for="country in countries.filter(c => c.enabled)" :key="country.id" class="country-section">
            <div class="country-header" @click="toggleCountry(country.id)">
              <span class="country-arrow">{{ country._expanded ? '&#9660;' : '&#9654;' }}</span>
              <span class="country-name">{{ country.name }}</span>
              <span class="country-code">{{ country.code }}</span>
            </div>
            <div v-if="country._expanded" class="country-body">
              <div v-if="country._loadingRegions" class="loading-state">Loading regions...</div>
              <div v-else>
                <div class="region-status-bar">
                  <span v-for="region in country._regions" :key="region.id" class="region-chip" :class="region.enabled ? 'on' : 'off'" @click="toggleRegionChip(country, region)">
                    {{ region.name }}
                  </span>
                </div>
                <div v-if="country._regions.length === 0" class="empty-state">
                  No location data configured for this country yet.
                </div>
                <LocationTree
                  v-if="country.code === 'PH'"
                  v-model="locations"
                  v-model:open-ids="openIds"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Summary</h3>
        <div class="summary-grid">
          <div class="summary-card">
            <span class="summary-label">Regions Enabled</span>
            <span class="summary-value">{{ enabledRegions }} / {{ totalRegions }}</span>
          </div>
          <div class="summary-card">
            <span class="summary-label">Cities Enabled</span>
            <span class="summary-value">{{ enabledCities }} / {{ totalCities }}</span>
          </div>
          <div class="summary-card">
            <span class="summary-label">Barangays Enabled</span>
            <span class="summary-value">{{ enabledBarangays }} / {{ totalBarangays }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">
      ✓ Location settings saved successfully
    </div>

    <div class="error-toast" v-if="error">
      ✕ {{ error }}
    </div>

    <div class="settings-actions" v-if="!isLoading">
      <button class="save-btn" @click="saveLocations" :disabled="isSaving">
        {{ isSaving ? "Saving..." : "Save Changes" }}
      </button>
      <button class="reset-btn" @click="resetLocations" :disabled="isSaving">
        Reset to Default
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import type { Region } from "./locations-data";
import { locationDefaults } from "./locations-data";
import SettingsSubnav from "~/components/SettingsSubnav.vue";
import LocationTree from "./LocationTree.vue";

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

const OPEN_KEY = "palengke_settings_locations_open";

const openIds = ref<Set<string>>(new Set());
const locations = ref<Region[]>(structuredClone(locationDefaults));
const success = ref(false);
const isLoading = ref(false);
const isSaving = ref(false);
const error = ref("");
const countries = ref<any[]>([]);

// ===== COMPUTED =====
const enabledRegions = computed(() => locations.value.filter((r) => r.checked).length);
const totalRegions = computed(() => locations.value.length);

const enabledCities = computed(() =>
  locations.value.reduce((sum, r) => sum + r.cities.filter((c) => c.checked).length, 0)
);
const totalCities = computed(() =>
  locations.value.reduce((sum, r) => sum + r.cities.length, 0)
);

const enabledBarangays = computed(() =>
  locations.value.reduce(
    (sum, r) =>
      sum + r.cities.reduce((cs, c) => cs + c.barangays.filter((b) => b.checked).length, 0),
    0
  )
);
const totalBarangays = computed(() =>
  locations.value.reduce(
    (sum, r) => sum + r.cities.reduce((cs, c) => cs + c.barangays.length, 0),
    0
  )
);

// ===== LOCAL STORAGE (UI state only) =====
function safeSetItem(key: string, value: string): void {
  try {
    localStorage.setItem(key, value);
  } catch (err) {
    console.warn(`Failed to save to localStorage (${key}):`, err);
  }
}

function safeGetItem(key: string): string | null {
  try {
    return localStorage.getItem(key);
  } catch (err) {
    console.warn(`Failed to read from localStorage (${key}):`, err);
    return null;
  }
}

function loadOpenState(): void {
  const raw = safeGetItem(OPEN_KEY);
  if (raw) {
    try {
      openIds.value = new Set(JSON.parse(raw));
    } catch (err) {
      console.warn("Failed to parse open state:", err);
    }
  }
}

function saveOpenState(): void {
  safeSetItem(OPEN_KEY, JSON.stringify([...openIds.value]));
}

// ===== API =====
async function loadLocations(): Promise<void> {
  isLoading.value = true;
  error.value = "";

  try {
    const token = getToken();
    const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/locations`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (response.locations) {
      locations.value = response.locations;
    } else {
      locations.value = structuredClone(locationDefaults);
    }
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to load locations";
    locations.value = structuredClone(locationDefaults);
  } finally {
    isLoading.value = false;
  }
}

async function saveLocations(): Promise<void> {
  isSaving.value = true;
  error.value = "";

  try {
    const token = getToken();
    await $fetch(`${getApiBaseUrl()}/api/admin/locations`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${token}`,
      },
      body: { locations: locations.value },
    });

    success.value = true;
    setTimeout(() => {
      success.value = false;
    }, 2500);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to save locations";
  } finally {
    isSaving.value = false;
  }
}

// ===== BULK ACTIONS =====
function expandAll(): void {
  locations.value.forEach((r) => {
    openIds.value.add(r.id);
    r.cities.forEach((c) => openIds.value.add(c.id));
  });
  saveOpenState();
}

function collapseAll(): void {
  openIds.value.clear();
  saveOpenState();
}

function resetLocations(): void {
  locations.value = structuredClone(locationDefaults);
  saveLocations();
}

function toggleCountry(countryId: number) {
  const country = countries.value.find((c) => c.id === countryId);
  if (!country) return;

  if (!country._expanded) {
    country._expanded = true;
    country._loadingRegions = true;

    const token = getToken();
    $fetch(`${getApiBaseUrl()}/api/admin/countries/${countryId}/regions`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((res: any) => {
        country._regions = res.regions;
      })
      .catch(() => {
        country._regions = [];
      })
      .finally(() => {
        country._loadingRegions = false;
      });
  } else {
    country._expanded = false;
  }
}

async function loadPHRegions() {
  const ph = countries.value.find(c => c.code === 'PH');
  if (!ph) return;

  try {
    const token = getToken();
    const res: any = await $fetch(`${getApiBaseUrl()}/api/admin/countries/${ph.id}/regions`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    ph._regions = res.regions;
    syncRegionsWithTree(res.regions);
  } catch {
    // silently fail
  }
}

function syncRegionsWithTree(regions: any[]) {
  regions.forEach((cr: any) => {
    const locRegion = locations.value.find((r: any) => r.name === cr.name);
    if (locRegion && !cr.enabled) {
      locRegion.checked = false;
      locRegion.indeterminate = false;
      locRegion.cities.forEach((city: any) => {
        city.checked = false;
        city.indeterminate = false;
        city.barangays.forEach((b: any) => (b.checked = false));
      });
    }
  });
}

async function toggleRegionChip(country: any, region: any) {
  try {
    const token = getToken();
    const res: any = await $fetch(
      `${getApiBaseUrl()}/api/admin/countries/${country.id}/regions/${region.id}/toggle`,
      {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    region.enabled = res.region.enabled;
    syncRegionsWithTree(country._regions);
    success.value = true;
    setTimeout(() => { success.value = false; }, 2500);
  } catch {
    // silently fail
  }
}

async function fetchCountries() {
  try {
    const token = getToken();
    const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/countries`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    countries.value = response.countries.map((c: any) => ({
      ...c,
      _expanded: false,
      _loadingRegions: false,
      _regions: [],
    }));
  } catch {
    countries.value = [];
  }
}

// ===== LIFECYCLE =====
onMounted(async () => {
  loadOpenState();
  await Promise.all([loadLocations(), fetchCountries()]);
  await loadPHRegions();
});

// ===== WATCH =====
watch(openIds, saveOpenState, { deep: true });
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
.settings-actions {
  display: flex;
  gap: 12px;
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
.loading-state,
.empty-state {
  padding: 40px;
  text-align: center;
  font-size: 14px;
  color: #6b7280;
}
.countries-wrap {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.country-section {
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
}
.country-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 16px;
  cursor: pointer;
  transition: background 0.1s;
  user-select: none;
}
.country-header:hover {
  background: #f9fafb;
}
.country-arrow {
  font-size: 11px;
  color: #9ca3af;
  width: 14px;
  text-align: center;
}
.country-name {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}
.country-code {
  font-size: 11px;
  color: #9ca3af;
  background: #f3f4f6;
  padding: 2px 7px;
  border-radius: 4px;
}
.country-body {
  border-top: 1px solid #f3f4f6;
  padding: 16px;
}
.region-status-bar {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}
.region-chip {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: opacity 0.15s;
}
.region-chip:hover {
  opacity: 0.7;
}
.region-chip.on {
  background: #f0fdf4;
  color: #059669;
}
.region-chip.off {
  background: #f3f4f6;
  color: #9ca3af;
}
.save-btn {
  padding: 12px 24px;
  background: #059669;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  transition: all 0.15s;
}
.save-btn:hover {
  background: #047857;
}
.save-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.reset-btn {
  padding: 12px 24px;
  background: #fff;
  color: #374151;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  transition: all 0.15s;
}
.reset-btn:hover {
  border-color: #9ca3af;
}
.reset-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
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
