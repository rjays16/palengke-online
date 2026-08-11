<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/settings"></ion-back-button>
        </ion-buttons>
        <ion-title>My Destination</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="dest-content">
      <!-- Search -->
      <div class="search-section">
        <div class="search-bar">
          <ion-icon :icon="searchOutline" class="search-icon"></ion-icon>
          <input v-model="searchQuery" type="text" placeholder="Search cities or barangays..." class="search-input" />
          <ion-icon v-if="searchQuery" :icon="closeCircle" class="clear-icon" @click="searchQuery = ''"></ion-icon>
        </div>
      </div>

      <div class="dest-container">
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading locations...</p>
        </div>

        <template v-else>
          <!-- Info Banner -->
          <div class="info-banner">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <p>Select the areas you want to deliver to. Only enabled locations are shown.</p>
          </div>

          <!-- Selected count -->
          <div class="selection-bar">
            <span>{{ selectedCount }} location{{ selectedCount !== 1 ? 's' : '' }} selected</span>
            <button class="clear-btn" v-if="selectedCount > 0" @click="clearAll">Clear all</button>
          </div>

          <!-- Location Tree -->
          <div class="location-tree">
            <div v-for="region in filteredRegions" :key="region.id" class="region-section">
              <div class="region-header" @click="toggleRegion(region.id)">
                <ion-icon
                  :icon="regionOpen(region.id) ? chevronDownOutline : chevronForwardOutline"
                  class="region-arrow"
                ></ion-icon>
                <span class="region-name">{{ region.name }}</span>
                <span class="region-count">{{ countSelectedInRegion(region) }}/{{ countTotalInRegion(region) }}</span>
              </div>
              <div v-if="regionOpen(region.id)" class="region-body">
                <div
                  v-for="city in region.cities"
                  :key="city.id"
                  class="city-item"
                  :class="{ disabled: !city.checked && !region.checked && city.barangays.every((b) => !b.checked) }"
                >
                  <label class="check-label">
                    <input type="checkbox" :checked="isCityChecked(city)" @change="toggleCity(region, city)" />
                    <span class="checkmark"></span>
                    <span class="city-name">{{ city.name }}</span>
                  </label>
                  <div v-if="city.barangays.length > 0" class="barangay-list">
                    <label
                      v-for="barangay in city.barangays"
                      :key="barangay.id"
                      class="check-label barangay-check"
                      :class="{ disabled: !barangay.checked && !city.checked && !region.checked }"
                    >
                      <input
                        type="checkbox"
                        :checked="isBarangayChecked(barangay)"
                        @change="toggleBarangay(region, city, barangay)"
                      />
                      <span class="checkmark"></span>
                      <span class="barangay-name">{{ barangay.name }}</span>
                    </label>
                  </div>
                </div>
                <div v-if="region.cities.length === 0" class="empty-region">No cities available</div>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="save-section">
            <ion-button expand="block" color="success" class="save-btn" :disabled="saving" @click="saveLocations">
              <div v-if="saving" class="spinner-sm"></div>
              {{ saving ? 'Saving...' : 'Save Locations' }}
            </ion-button>
          </div>
        </template>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonButton,
  IonIcon,
  toastController,
} from '@ionic/vue';
import {
  searchOutline,
  closeCircle,
  informationCircleOutline,
  chevronForwardOutline,
  chevronDownOutline,
} from 'ionicons/icons';

import { API_BASE } from '@/services/api';

function getToken() {
  return localStorage.getItem('driver_token') || '';
}

const searchQuery = ref('');
const loading = ref(true);
const saving = ref(false);
const locations = ref([]);
const preferredIds = ref(new Set());
const openRegionIds = ref(new Set());

const filteredRegions = computed(() => {
  if (!searchQuery.value) return locations.value;
  const q = searchQuery.value.toLowerCase().trim();
  return locations.value
    .map((region) => {
      const filteredCities = region.cities.filter(
        (city) => city.name.toLowerCase().includes(q) || city.barangays.some((b) => b.name.toLowerCase().includes(q)),
      );
      return { ...region, cities: filteredCities };
    })
    .filter((r) => r.cities.length > 0);
});

const selectedCount = computed(() => preferredIds.value.size);

function regionOpen(id) {
  return openRegionIds.value.has(id);
}

function toggleRegion(id) {
  if (openRegionIds.value.has(id)) {
    openRegionIds.value.delete(id);
  } else {
    openRegionIds.value.add(id);
  }
}

function isCityChecked(city) {
  return preferredIds.value.has(city.id) || city.barangays.every((b) => preferredIds.value.has(b.id));
}

function isBarangayChecked(barangay) {
  return preferredIds.value.has(barangay.id);
}

function toggleCity(region, city) {
  const allChecked = city.barangays.every((b) => preferredIds.value.has(b.id));
  if (!city.barangays.length) {
    if (preferredIds.value.has(city.id)) {
      preferredIds.value.delete(city.id);
    } else {
      preferredIds.value.add(city.id);
    }
  } else if (allChecked) {
    city.barangays.forEach((b) => preferredIds.value.delete(b.id));
  } else {
    city.barangays.forEach((b) => preferredIds.value.add(b.id));
  }
}

function toggleBarangay(region, city, barangay) {
  if (preferredIds.value.has(barangay.id)) {
    preferredIds.value.delete(barangay.id);
  } else {
    preferredIds.value.add(barangay.id);
  }
}

function countSelectedInRegion(region) {
  let count = 0;
  region.cities.forEach((city) => {
    if (preferredIds.value.has(city.id)) count++;
    city.barangays.forEach((b) => {
      if (preferredIds.value.has(b.id)) count++;
    });
  });
  return count;
}

function countTotalInRegion(region) {
  let count = 0;
  region.cities.forEach((city) => {
    count++;
    count += city.barangays.length;
  });
  return count;
}

function clearAll() {
  preferredIds.value.clear();
}

onMounted(async () => {
  try {
    const [locRes, setRes] = await Promise.all([
      fetch(`${API_BASE}/api/locations`),
      fetch(`${API_BASE}/api/driver/settings`, {
        headers: { Authorization: `Bearer ${getToken()}` },
      }),
    ]);
    if (locRes.ok) {
      const data = await locRes.json();
      locations.value = data.locations || [];
    }
    if (setRes.ok) {
      const data = await setRes.json();
      if (data.preferred_locations) {
        preferredIds.value = new Set(data.preferred_locations);
      }
    }
  } catch {
    /* ignore */
  }
  loading.value = false;
});

const saveLocations = async () => {
  saving.value = true;
  try {
    await fetch(`${API_BASE}/api/driver/settings`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${getToken()}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        preferred_locations: [...preferredIds.value],
      }),
    });
    const toast = await toastController.create({
      message: 'Locations saved successfully',
      duration: 2000,
      color: 'success',
      position: 'top',
    });
    await toast.present();
  } catch {
    const toast = await toastController.create({
      message: 'Failed to save locations',
      duration: 2000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
.dest-content {
  --background: #f8f9fa;
}

.search-section {
  background: white;
  padding: 12px 16px;
  border-bottom: 1px solid #f0f0f0;
}
.search-bar {
  position: relative;
}
.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #999;
}
.search-input {
  width: 100%;
  padding: 14px 48px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
  font-family: inherit;
  transition: border-color 0.2s;
}
.search-input:focus {
  outline: none;
  border-color: #4caf50;
}
.clear-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #999;
  cursor: pointer;
}

.dest-container {
  padding: 16px;
}

.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
}
.loading-state p {
  margin: 12px 0 0;
  font-size: 14px;
}
.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid #e0e0e0;
  border-top-color: #4caf50;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  margin: 0 auto;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.info-banner {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  background: #e3f2fd;
  border-radius: 12px;
  padding: 12px 14px;
  margin-bottom: 12px;
  border-left: 4px solid #2196f3;
}
.info-banner ion-icon {
  font-size: 20px;
  color: #2196f3;
  flex-shrink: 0;
  margin-top: 1px;
}
.info-banner p {
  font-size: 13px;
  color: #1565c0;
  margin: 0;
  line-height: 1.5;
}

.selection-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  color: #666;
  margin-bottom: 12px;
  padding: 0 4px;
}
.clear-btn {
  background: none;
  border: none;
  color: #f44336;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
}

.location-tree {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  margin-bottom: 16px;
}

.region-section {
  border-bottom: 1px solid #f0f0f0;
}
.region-section:last-child {
  border-bottom: none;
}

.region-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px 20px;
  cursor: pointer;
  transition: background 0.2s;
  background: #fafafa;
}
.region-header:active {
  background: #f0f0f0;
}
.region-arrow {
  font-size: 16px;
  color: #999;
  flex-shrink: 0;
}
.region-name {
  font-size: 15px;
  font-weight: 700;
  color: #1a1a1a;
  flex: 1;
}
.region-count {
  font-size: 12px;
  color: #999;
  font-weight: 600;
}

.region-body {
  padding: 4px 20px 12px;
}

.city-item {
  padding: 8px 0;
  border-bottom: 1px solid #f5f5f5;
}
.city-item:last-child {
  border-bottom: none;
}
.city-item.disabled .city-name {
  opacity: 0.5;
}

.check-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-size: 14px;
  color: #1a1a1a;
  user-select: none;
}
.check-label input {
  display: none;
}
.checkmark {
  width: 20px;
  height: 20px;
  border-radius: 4px;
  border: 2px solid #ccc;
  flex-shrink: 0;
  position: relative;
  transition: all 0.15s;
}
.check-label input:checked + .checkmark {
  background: #4caf50;
  border-color: #4caf50;
}
.check-label input:checked + .checkmark::after {
  content: '';
  position: absolute;
  left: 5px;
  top: 1px;
  width: 6px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.city-name {
  font-weight: 600;
  font-size: 14px;
}

.barangay-list {
  padding: 6px 0 2px 28px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.barangay-check {
  font-size: 13px;
  color: #555;
}
.barangay-check .checkmark {
  width: 18px;
  height: 18px;
}
.barangay-check.disabled .barangay-name {
  opacity: 0.5;
}
.barangay-name {
  font-size: 13px;
}

.empty-region {
  padding: 16px;
  text-align: center;
  color: #999;
  font-size: 13px;
}

.save-section {
  padding: 0 0 24px;
}
.save-btn {
  --border-radius: 12px;
  height: 48px;
  font-weight: 700;
  text-transform: none;
  font-size: 15px;
}
.spinner-sm {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
</style>
