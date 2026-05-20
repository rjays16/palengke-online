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

        <LocationTree
          v-model="locations"
          v-model:open-ids="openIds"
        />
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

    <div class="settings-actions">
      <button class="save-btn" @click="saveLocations">Save Changes</button>
      <button class="reset-btn" @click="resetLocations">Reset to Default</button>
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

const STORAGE_KEY = "palengke_settings_locations";
const OPEN_KEY = "palengke_settings_locations_open";

const openIds = ref<Set<string>>(new Set());
const locations = ref<Region[]>(structuredClone(locationDefaults));
const success = ref(false);

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

// ===== LOCAL STORAGE =====
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

function safeRemoveItem(key: string): void {
  try {
    localStorage.removeItem(key);
  } catch (err) {
    console.warn(`Failed to remove from localStorage (${key}):`, err);
  }
}

// ===== STATE MANAGEMENT =====
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

function loadLocations(): void {
  const saved = safeGetItem(STORAGE_KEY);
  if (saved) {
    try {
      locations.value = JSON.parse(saved);
      return;
    } catch (err) {
      console.warn("Failed to parse saved locations:", err);
      safeRemoveItem(STORAGE_KEY);
    }
  }
  locations.value = structuredClone(locationDefaults);
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

// ===== SAVE / RESET =====
function saveLocations(): void {
  safeSetItem(STORAGE_KEY, JSON.stringify(locations.value));
  success.value = true;
  setTimeout(() => {
    success.value = false;
  }, 2500);
}

function resetLocations(): void {
  locations.value = structuredClone(locationDefaults);
  saveLocations();
}

// ===== LIFECYCLE =====
onMounted(() => {
  loadOpenState();
  loadLocations();
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
@media (max-width: 600px) {
  .summary-grid {
    grid-template-columns: 1fr;
  }
  .section-header {
    flex-direction: column;
  }
}
</style>
