<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">
        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-section">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 42.84%"></div>
          </div>
          <p class="progress-text">Step 3 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Store Information</h1>
          <p class="subtitle">Tell us about your store location</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          <!-- Store Information Section -->
          <div class="section-header">
            <ion-icon :icon="storefrontOutline"></ion-icon>
            <h2>Your Store</h2>
          </div>

          <!-- Store Name -->
          <div class="input-group">
            <label class="input-label">Store Name *</label>
            <div class="input-wrapper" :class="{ error: errors.storeName }">
              <input
                v-model="formData.storeName"
                type="text"
                placeholder="Juan's Fresh Produce"
                class="custom-input"
                @blur="validateField('storeName')"
                @input="errors.storeName = ''"
              />
            </div>
            <span v-if="errors.storeName" class="error-message">{{ errors.storeName }}</span>
            <span v-else class="helper-text">This will be visible to customers</span>
          </div>

          <!-- Region Dropdown -->
          <div class="input-group">
            <label class="input-label">Region *</label>
            <div class="input-wrapper" :class="{ error: errors.region }">
              <ion-icon :icon="locationOutline" class="input-icon"></ion-icon>
              <select
                v-model="formData.region"
                class="custom-select with-icon"
                @change="onRegionChange"
                @blur="validateField('region')"
                :disabled="isLoadingLocations"
              >
                <option value="">Select Region</option>
                <option v-if="isLoadingLocations" value="">Loading regions...</option>
                <option v-for="region in locations" :key="region.id" :value="region.name">
                  {{ region.name }}
                </option>
              </select>
            </div>
            <span v-if="errors.region" class="error-message">{{ errors.region }}</span>
          </div>

          <!-- City Dropdown (shows after region selected) -->
          <div class="input-group" v-if="formData.region">
            <label class="input-label">City *</label>
            <div class="input-wrapper" :class="{ error: errors.city }">
              <ion-icon :icon="businessOutline" class="input-icon"></ion-icon>
              <select
                v-model="formData.city"
                class="custom-select with-icon"
                @change="onCityChange"
                @blur="validateField('city')"
              >
                <option value="">Select City</option>
                <option v-for="city in availableCities" :key="city.id" :value="city.name">
                  {{ city.name }}
                </option>
              </select>
            </div>
            <span v-if="errors.city" class="error-message">{{ errors.city }}</span>
          </div>

          <!-- Barangay Dropdown (shows after city selected) -->
          <div class="input-group" v-if="formData.city">
            <label class="input-label">Barangay *</label>
            <div class="input-wrapper" :class="{ error: errors.barangay }">
              <ion-icon :icon="mapOutline" class="input-icon"></ion-icon>
              <select v-model="formData.barangay" class="custom-select with-icon" @blur="validateField('barangay')">
                <option value="">Select Barangay</option>
                <option v-for="barangay in availableBarangays" :key="barangay.id" :value="barangay.name">
                  {{ barangay.name }}
                </option>
              </select>
            </div>
            <span v-if="errors.barangay" class="error-message">{{ errors.barangay }}</span>
            <span v-else class="helper-text">{{ barangayCount }} barangays available</span>
          </div>

          <!-- Store Place/Location -->
          <div class="input-group">
            <label class="input-label">Store Location/Address *</label>
            <div class="input-wrapper" :class="{ error: errors.storePlace }">
              <ion-icon :icon="pinOutline" class="input-icon"></ion-icon>
              <input
                v-model="formData.storePlace"
                type="text"
                placeholder="Carmen Public Market, Stall 12"
                class="custom-input with-icon"
                @blur="validateField('storePlace')"
                @input="errors.storePlace = ''"
              />
            </div>
            <span v-if="errors.storePlace" class="error-message">{{ errors.storePlace }}</span>
            <span v-else class="helper-text">Market name, stall number, or street address</span>
          </div>

          <!-- Complete Address -->
          <div class="input-group">
            <label class="input-label">Complete Address *</label>
            <div class="input-wrapper" :class="{ error: errors.completeAddress }">
              <ion-icon :icon="homeOutline" class="input-icon"></ion-icon>
              <textarea
                v-model="formData.completeAddress"
                placeholder="123 Main Street, Barangay Carmen, Cagayan de Oro City, Misamis Oriental"
                class="custom-textarea with-icon"
                rows="3"
                @blur="validateField('completeAddress')"
                @input="errors.completeAddress = ''"
              ></textarea>
            </div>
            <span v-if="errors.completeAddress" class="error-message">{{ errors.completeAddress }}</span>
            <span v-else class="helper-text">Include barangay, city, and province</span>
          </div>

          <!-- Store Coordinates -->
          <div class="input-group">
            <label class="input-label">Store Coordinates</label>
            <div class="gps-box" :class="{ captured: !!formData.latitude }">
              <div class="gps-box-main">
                <ion-icon :icon="locateOutline"></ion-icon>
                <div>
                  <p v-if="formData.latitude" class="gps-coords">{{ formData.latitude }}, {{ formData.longitude }}</p>
                  <p v-else class="gps-hint">Pin the exact store location so drivers can find you.</p>
                </div>
              </div>
              <ion-button size="small" class="gps-button" @click="captureLocation" :disabled="isLocating">
                <ion-spinner v-if="isLocating" name="crescent"></ion-spinner>
                <span v-else>{{ formData.latitude ? 'Update Location' : 'Use Current Location' }}</span>
              </ion-button>
            </div>
            <span v-if="locationError" class="error-message">{{ locationError }}</span>
            <span v-else-if="formData.latitude" class="helper-text">Location captured via GPS</span>
            <span v-else class="helper-text">Optional — allows the driver app to show your store on the map</span>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>Why we need this:</strong><br />
              Accurate location helps customers find your store and enables efficient delivery services.
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="button-group">
            <ion-button expand="block" fill="outline" color="medium" class="back-btn" @click="goBack">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
              Back
            </ion-button>

            <ion-button expand="block" class="next-button" @click="handleNext" :disabled="!isFormValid">
              Next
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { api } from '@/utils/apiClient';
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, IonSpinner } from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  storefrontOutline,
  locationOutline,
  businessOutline,
  mapOutline,
  pinOutline,
  homeOutline,
  locateOutline,
  informationCircleOutline,
} from 'ionicons/icons';
import { useRegistrationStore } from '@/stores/registration';
import { type StoreInfoForm } from '@/types';

const router = useRouter();
const reg = useRegistrationStore();

interface LocationRegion {
  id: string;
  name: string;
  cities: {
    id: string;
    name: string;
    checked: boolean;
    barangays: { id: string; name: string; checked: boolean }[];
  }[];
}

// Locations loaded from the backend
const locations = ref<LocationRegion[]>([]);
const isLoadingLocations = ref(true);

// Form data
const formData = ref({
  storeName: '',
  region: '',
  city: '',
  barangay: '',
  storePlace: '',
  completeAddress: '',
  latitude: '',
  longitude: '',
} as StoreInfoForm);

// Error states
const errors = ref({
  storeName: '',
  region: '',
  city: '',
  barangay: '',
  storePlace: '',
  completeAddress: '',
});

// GPS capture
const isLocating = ref(false);
const locationError = ref('');

const captureLocation = () => {
  if (!navigator.geolocation) {
    locationError.value = 'Geolocation is not supported on this device.';
    return;
  }
  isLocating.value = true;
  locationError.value = '';
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      formData.value.latitude = pos.coords.latitude.toFixed(7);
      formData.value.longitude = pos.coords.longitude.toFixed(7);
      isLocating.value = false;
    },
    () => {
      isLocating.value = false;
      locationError.value = 'Could not get your location. Please enable location access.';
    },
    { enableHighAccuracy: true, timeout: 10000 },
  );
};

// Available cities based on selected region
const availableCities = computed(() => {
  const region = locations.value.find((r) => r.name === formData.value.region);
  return region ? region.cities : [];
});

// Available barangays based on selected city
const availableBarangays = computed(() => {
  const region = locations.value.find((r) => r.name === formData.value.region);
  const city = region ? region.cities.find((c) => c.name === formData.value.city) : null;
  return city ? city.barangays : [];
});

// Barangay count for helper text
const barangayCount = computed(() => {
  return availableBarangays.value.length;
});

// Load data
onMounted(async () => {
  try {
    const data = await api.get<{ locations: any[] }>('/locations');
    locations.value = data.locations || [];
  } catch {
    locations.value = [];
  } finally {
    isLoadingLocations.value = false;
  }

  reg.load();
  if (reg.data.storeName) formData.value.storeName = reg.data.storeName;
  if (reg.data.region) formData.value.region = reg.data.region;
  if (reg.data.city) formData.value.city = reg.data.city;
  if (reg.data.barangay) formData.value.barangay = reg.data.barangay;
  if (reg.data.storePlace) formData.value.storePlace = reg.data.storePlace;
  if (reg.data.completeAddress) formData.value.completeAddress = reg.data.completeAddress;
  if (reg.data.latitude) formData.value.latitude = reg.data.latitude;
  if (reg.data.longitude) formData.value.longitude = reg.data.longitude;
});

// Region change handler
const onRegionChange = () => {
  // Reset city and barangay when region changes
  formData.value.city = '';
  formData.value.barangay = '';
  errors.value.region = '';
  errors.value.city = '';
  errors.value.barangay = '';
};

// City change handler
const onCityChange = () => {
  // Reset barangay when city changes
  formData.value.barangay = '';
  errors.value.city = '';
  errors.value.barangay = '';
};

// Field validation
const validateField = (fieldName: string) => {
  switch (fieldName) {
    case 'storeName':
      if (!formData.value.storeName.trim()) {
        errors.value.storeName = 'Store name is required';
        return false;
      }
      if (formData.value.storeName.trim().length < 3) {
        errors.value.storeName = 'Store name must be at least 3 characters';
        return false;
      }
      errors.value.storeName = '';
      return true;

    case 'region':
      if (!formData.value.region) {
        errors.value.region = 'Please select a region';
        return false;
      }
      errors.value.region = '';
      return true;

    case 'city':
      if (!formData.value.city) {
        errors.value.city = 'Please select a city';
        return false;
      }
      errors.value.city = '';
      return true;

    case 'barangay':
      if (!formData.value.barangay) {
        errors.value.barangay = 'Please select a barangay';
        return false;
      }
      errors.value.barangay = '';
      return true;

    case 'storePlace':
      if (!formData.value.storePlace.trim()) {
        errors.value.storePlace = 'Store location is required';
        return false;
      }
      if (formData.value.storePlace.trim().length < 5) {
        errors.value.storePlace = 'Please provide more details';
        return false;
      }
      errors.value.storePlace = '';
      return true;

    case 'completeAddress':
      if (!formData.value.completeAddress.trim()) {
        errors.value.completeAddress = 'Complete address is required';
        return false;
      }
      if (formData.value.completeAddress.trim().length < 15) {
        errors.value.completeAddress = 'Please provide a complete address';
        return false;
      }
      errors.value.completeAddress = '';
      return true;

    default:
      return true;
  }
};

// Form validation
const isFormValid = computed(() => {
  return (
    formData.value.storeName.trim() &&
    formData.value.region &&
    formData.value.city &&
    formData.value.barangay &&
    formData.value.storePlace.trim() &&
    formData.value.completeAddress.trim()
  );
});

// Navigation
const goBack = () => {
  saveData();
  router.push('/register/step2');
};

const saveData = () => {
  reg.setFields({
    storeName: formData.value.storeName,
    region: formData.value.region,
    city: formData.value.city,
    barangay: formData.value.barangay,
    storePlace: formData.value.storePlace,
    completeAddress: formData.value.completeAddress,
    latitude: formData.value.latitude,
    longitude: formData.value.longitude,
  });
};

const handleNext = () => {
  const isStoreNameValid = validateField('storeName');
  const isRegionValid = validateField('region');
  const isCityValid = validateField('city');
  const isBarangayValid = validateField('barangay');
  const isStorePlaceValid = validateField('storePlace');
  const isAddressValid = validateField('completeAddress');

  if (isStoreNameValid && isRegionValid && isCityValid && isBarangayValid && isStorePlaceValid && isAddressValid) {
    saveData();
    router.push('/register/step4');
  }
};
</script>

<style scoped>
.step-content {
  --background: var(--color-bg-primary);
}

.step-container {
  min-height: 100vh;
  padding: 24px;
  padding-bottom: 100px;
  position: relative;
}

/* Back Button */
.back-button {
  position: absolute;
  top: 24px;
  left: 24px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-bg-tertiary);
  border-radius: 12px;
  cursor: pointer;
  z-index: 10;
  transition: background 0.2s;
}

.back-button:active {
  transform: scale(0.95);
  background: var(--color-border);
}

.back-button ion-icon {
  font-size: 24px;
  color: var(--color-text-primary);
}

/* Progress Section */
.progress-section {
  margin-top: 70px;
  margin-bottom: 32px;
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: var(--color-border);
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: var(--color-primary);
  transition: width 0.3s ease;
}

.progress-text {
  text-align: center;
  font-size: 13px;
  color: var(--color-text-secondary);
  margin: 0;
  font-weight: 600;
}

/* Title Section */
.title-section {
  text-align: center;
  margin-bottom: 40px;
}

.title {
  font-size: 28px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 15px;
  color: var(--color-text-secondary);
  margin: 0;
}

/* Form Section */
.form-section {
  max-width: 500px;
  margin: 0 auto;
  width: 100%;
}

/* Section Header */
.section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  padding-bottom: 12px;
  border-bottom: 2px solid var(--color-card-border);
}

.section-header ion-icon {
  font-size: 24px;
  color: var(--color-icon-primary);
}

.section-header h2 {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0;
}

/* Input Group */
.input-group {
  margin-bottom: 20px;
}

.input-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-primary);
  margin-bottom: 8px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 16px;
  top: 16px;
  font-size: 20px;
  color: var(--color-icon-primary);
  z-index: 2;
}

.custom-input {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input.with-icon {
  padding-left: 48px;
}

.custom-input:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.custom-input::placeholder {
  color: var(--color-input-placeholder);
}

/* Select Dropdown */
.custom-select {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  padding-right: 40px;
}

.custom-select.with-icon {
  padding-left: 48px;
}

.custom-select:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

/* Textarea */
.custom-textarea {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
}

.custom-textarea.with-icon {
  padding-left: 48px;
}

.custom-textarea:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.custom-textarea::placeholder {
  color: var(--color-input-placeholder);
}

/* Error State */
.input-wrapper.error .custom-input,
.input-wrapper.error .custom-select,
.input-wrapper.error .custom-textarea {
  border-color: var(--color-error);
}

.input-wrapper.error .input-icon {
  color: var(--color-error);
}

/* Helper/Error Text */
.helper-text {
  display: block;
  font-size: 12px;
  color: var(--color-text-tertiary);
  margin-top: 6px;
}

.error-message {
  display: block;
  font-size: 12px;
  color: var(--color-error);
  margin-top: 6px;
  font-weight: 500;
}

/* GPS Box */
.gps-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  background: var(--color-input-bg);
}

.gps-box.captured {
  border-color: var(--color-success-border);
  background: var(--color-success-bg);
}

.gps-box-main {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.gps-box-main ion-icon {
  font-size: 24px;
  color: var(--color-icon-primary);
  flex-shrink: 0;
}

.gps-box.captured .gps-box-main ion-icon {
  color: var(--color-success-border);
}

.gps-coords {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-primary);
  margin: 0;
  word-break: break-all;
}

.gps-hint {
  font-size: 13px;
  color: var(--color-text-secondary);
  margin: 0;
}

.gps-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 10px;
  flex-shrink: 0;
  font-size: 13px;
  font-weight: 600;
  text-transform: none;
}

/* Info Box */
.info-box {
  display: flex;
  gap: 12px;
  background: var(--color-info-bg);
  border-left: 4px solid var(--color-info-border);
  padding: 16px;
  border-radius: 8px;
  margin: 32px 0 24px 0;
}

.info-box ion-icon {
  font-size: 24px;
  color: var(--color-info-border);
  flex-shrink: 0;
  margin-top: 2px;
}

.info-text {
  font-size: 13px;
  color: var(--color-info-text);
  line-height: 1.5;
}

.info-text strong {
  font-weight: 700;
}

/* Button Group */
.button-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-top: 32px;
}

.back-btn {
  --border-radius: 12px;
  --border-width: 2px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.next-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.next-button:disabled {
  --background: var(--color-border);
  --color: var(--color-text-tertiary);
}

/* Responsive */
@media (max-width: 480px) {
  .title {
    font-size: 24px;
  }

  .step-container {
    padding: 20px 16px 100px 16px;
  }

  .button-group {
    grid-template-columns: 1fr;
  }

  .back-btn {
    order: 2;
  }

  .next-button {
    order: 1;
  }
}
</style>
