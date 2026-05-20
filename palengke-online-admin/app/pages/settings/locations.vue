<template>
  <div class="page-shell">
    <div class="settings-subnav">
      <NuxtLink to="/settings" class="subnav-item" active-class="active"
        >General</NuxtLink
      >
      <NuxtLink to="/settings/fees" class="subnav-item" active-class="active"
        >Delivery Fees</NuxtLink
      >
      <NuxtLink
        to="/settings/commissions"
        class="subnav-item"
        active-class="active"
        >Commissions</NuxtLink
      >
      <NuxtLink
        to="/settings/payments"
        class="subnav-item"
        active-class="active"
        >Payments</NuxtLink
      >
      <NuxtLink
        to="/settings/locations"
        class="subnav-item"
        active-class="active"
        exact-active-class="active"
        >Locations</NuxtLink
      >
    </div>

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

        <div class="location-tree">
          <div
            v-for="region in locations"
            :key="region.id"
            class="tree-node region-node"
          >
            <div class="node-row" @click="toggleRegion(region.id)">
              <button class="chevron" :class="{ open: isOpen(region.id) }">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="9 18 15 12 9 6" />
                </svg>
              </button>
              <label class="checkbox-label" @click.stop>
                <input
                  type="checkbox"
                  :checked="region.checked"
                  :indeterminate.prop="region.indeterminate"
                  @change="toggleRegionCheck(region)"
                />
                <span class="checkmark"></span>
                <span class="node-name">{{ region.name }}</span>
                <span class="node-badge region-badge">Region</span>
              </label>
              <span class="node-meta">{{ enabledCount(region) }} / {{ totalCount(region) }} enabled</span>
            </div>

            <div v-show="isOpen(region.id)" class="children cities">
              <div
                v-for="city in region.cities"
                :key="city.id"
                class="tree-node city-node"
              >
                <div class="node-row" @click="toggleCity(city.id)">
                  <button class="chevron" :class="{ open: isOpen(city.id) }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="9 18 15 12 9 6" />
                    </svg>
                  </button>
                  <label class="checkbox-label" @click.stop>
                    <input
                      type="checkbox"
                      :checked="city.checked"
                      :indeterminate.prop="city.indeterminate"
                      @change="toggleCityCheck(region, city)"
                    />
                    <span class="checkmark"></span>
                    <span class="node-name">{{ city.name }}</span>
                    <span class="node-badge city-badge">City</span>
                  </label>
                  <span class="node-meta">{{ enabledCount(city) }} / {{ totalCount(city) }} enabled</span>
                </div>

                <div v-show="isOpen(city.id)" class="children barangays">
                  <div
                    v-for="barangay in city.barangays"
                    :key="barangay.id"
                    class="tree-node barangay-node"
                  >
                    <div class="node-row barangay-row">
                      <label class="checkbox-label" @click.stop>
                        <input
                          type="checkbox"
                          v-model="barangay.checked"
                          @change="onBarangayChange(region, city)"
                        />
                        <span class="checkmark"></span>
                        <span class="node-name">{{ barangay.name }}</span>
                        <span class="node-badge barangay-badge">Barangay</span>
                      </label>
                    </div>
                  </div>
                </div>
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
            <span class="summary-value">{{ enabledRegions }} / {{ locations.length }}</span>
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

<script setup>
import { locationDefaults } from "./locations-data";

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

const defaults = locationDefaults;

const openIds = ref(new Set());
const locations = ref(structuredClone(defaults));
const success = ref(false);

function loadOpenState() {
  try {
    const raw = localStorage.getItem(OPEN_KEY);
    if (raw) openIds.value = new Set(JSON.parse(raw));
  } catch {}
}

function saveOpenState() {
  localStorage.setItem(OPEN_KEY, JSON.stringify([...openIds.value]));
}

function loadLocations() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
      locations.value = JSON.parse(saved);
      // Recompute indeterminate states after loading
      locations.value.forEach((region) => {
        region.cities.forEach((city) => {
          onBarangayChange(region, city, false);
        });
      });
      return;
    }
  } catch {}
  locations.value = structuredClone(defaults);
}

function isOpen(id) {
  return openIds.value.has(id);
}

function toggleRegion(id) {
  if (openIds.value.has(id)) openIds.value.delete(id);
  else openIds.value.add(id);
  saveOpenState();
}

function toggleCity(id) {
  if (openIds.value.has(id)) openIds.value.delete(id);
  else openIds.value.add(id);
  saveOpenState();
}

function expandAll() {
  locations.value.forEach((r) => {
    openIds.value.add(r.id);
    r.cities.forEach((c) => openIds.value.add(c.id));
  });
  saveOpenState();
}

function collapseAll() {
  openIds.value.clear();
  saveOpenState();
}

function totalCount(node) {
  if (node.cities) return node.cities.length;
  if (node.barangays) return node.barangays.length;
  return 0;
}

function enabledCount(node) {
  if (node.cities) return node.cities.filter((c) => c.checked).length;
  if (node.barangays) return node.barangays.filter((b) => b.checked).length;
  return 0;
}

const enabledRegions = computed(() => locations.value.filter((r) => r.checked).length);
const enabledCities = computed(() =>
  locations.value.reduce(
    (sum, r) => sum + r.cities.filter((c) => c.checked).length,
    0
  )
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

function updateCityState(city) {
  const checked = city.barangays.filter((b) => b.checked).length;
  const total = city.barangays.length;
  if (checked === 0) {
    city.checked = false;
    city.indeterminate = false;
  } else if (checked === total) {
    city.checked = true;
    city.indeterminate = false;
  } else {
    city.checked = false;
    city.indeterminate = true;
  }
}

function updateRegionState(region) {
  const checked = region.cities.filter((c) => c.checked).length;
  const indet = region.cities.filter((c) => c.indeterminate).length;
  const total = region.cities.length;
  if (checked === 0 && indet === 0) {
    region.checked = false;
    region.indeterminate = false;
  } else if (checked === total && indet === 0) {
    region.checked = true;
    region.indeterminate = false;
  } else {
    region.checked = false;
    region.indeterminate = true;
  }
}

function onBarangayChange(region, city, bubble = true) {
  updateCityState(city);
  if (bubble) updateRegionState(region);
}

function toggleCityCheck(region, city) {
  const val = !city.checked;
  city.checked = val;
  city.indeterminate = false;
  city.barangays.forEach((b) => (b.checked = val));
  updateRegionState(region);
}

function toggleRegionCheck(region) {
  const val = !region.checked;
  region.checked = val;
  region.indeterminate = false;
  region.cities.forEach((city) => {
    city.checked = val;
    city.indeterminate = false;
    city.barangays.forEach((b) => (b.checked = val));
  });
}

function saveLocations() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(locations.value));
  success.value = true;
  setTimeout(() => {
    success.value = false;
  }, 2500);
}

function resetLocations() {
  locations.value = structuredClone(defaults);
  saveLocations();
}

onMounted(() => {
  loadOpenState();
  loadLocations();
});
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 900px;
}
.settings-subnav {
  display: flex;
  gap: 4px;
  background: #fff;
  border-radius: 12px;
  padding: 6px;
  border: 1px solid #e5e7eb;
  overflow-x: auto;
}
.subnav-item {
  padding: 9px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.15s;
  white-space: nowrap;
}
.subnav-item:hover {
  color: #374151;
  background: #f9fafb;
}
.subnav-item.active {
  background: #f0fdf4;
  color: #059669;
  font-weight: 600;
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

/* Tree */
.location-tree {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.tree-node {
  border: 1px solid #f3f4f6;
  border-radius: 10px;
  overflow: hidden;
}
.region-node {
  border-color: #e5e7eb;
}
.city-node {
  margin-left: 28px;
  margin-top: 4px;
}
.barangay-node {
  margin-left: 28px;
  border: none;
}
.node-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 14px;
  cursor: pointer;
  transition: background 0.1s;
  user-select: none;
}
.node-row:hover {
  background: #f9fafb;
}
.barangay-row {
  padding: 8px 14px;
  cursor: default;
}
.barangay-row:hover {
  background: transparent;
}
.chevron {
  width: 22px;
  height: 22px;
  border: none;
  background: none;
  color: #9ca3af;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  flex-shrink: 0;
  transition: transform 0.2s;
}
.chevron svg {
  width: 14px;
  height: 14px;
}
.chevron.open {
  transform: rotate(90deg);
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  flex: 1;
  min-width: 0;
}
.checkbox-label input {
  display: none;
}
.checkmark {
  width: 18px;
  height: 18px;
  border: 1.5px solid #d1d5db;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.15s;
  position: relative;
}
.checkbox-label input:checked + .checkmark {
  background: #059669;
  border-color: #059669;
}
.checkbox-label input:checked + .checkmark::after {
  content: "";
  width: 5px;
  height: 9px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
  margin-bottom: 1px;
}
/* indeterminate state via inline prop handled by browser; we add a visual indicator */
.checkbox-label input:indeterminate + .checkmark {
  background: #059669;
  border-color: #059669;
}
.checkbox-label input:indeterminate + .checkmark::after {
  content: "";
  width: 8px;
  height: 2px;
  background: white;
  border: none;
  transform: none;
  margin: 0;
}
.node-name {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.node-badge {
  font-size: 10px;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 10px;
  line-height: 1.3;
  flex-shrink: 0;
}
.region-badge {
  background: #fef3c7;
  color: #b45309;
}
.city-badge {
  background: #e0e7ff;
  color: #4338ca;
}
.barangay-badge {
  background: #f3f4f6;
  color: #6b7280;
}
.node-meta {
  font-size: 11px;
  color: #9ca3af;
  flex-shrink: 0;
  margin-left: auto;
  padding-left: 8px;
}
.children {
  display: flex;
  flex-direction: column;
}
.children.cities {
  padding: 0 0 8px;
}
.children.barangays {
  padding: 0 0 4px;
}

/* Summary */
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

/* Actions */
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
  .node-meta {
    display: none;
  }
}
</style>
