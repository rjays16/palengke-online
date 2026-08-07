<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container" :data-render="renderKey">
        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-section">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 57.12%"></div>
          </div>
          <p class="progress-text">Step 4 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Vehicle Information</h1>
          <p class="subtitle">Tell us about your vehicle</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          <!-- Section Header -->
          <div class="section-header">
            <ion-icon :icon="carOutline"></ion-icon>
            <h2>Vehicle Details</h2>
          </div>

          <!-- Restriction Info -->
          <div class="restriction-info" v-if="getRestrictionCode()">
            <ion-icon :icon="codeSlashOutline"></ion-icon>
            <span>Restriction Code <strong>{{ getRestrictionCode() }}</strong> — {{ restrictionCodes[getRestrictionCode()] }}</span>
          </div>
          <!-- Vehicle Type -->
          <div class="input-group">
            <label class="input-label">Vehicle Type *</label>
            <div class="input-wrapper" :class="{ error: errors.vehicleType }">
              <ion-icon :icon="bicycleOutline" class="input-icon"></ion-icon>
              <select
                v-model="formData.vehicleType"
                class="custom-input with-icon custom-select"
                @change="errors.vehicleType = ''"
                @blur="validateField('vehicleType')"
              >
                <option value="" disabled>Select vehicle type</option>
                <option v-for="v in vehicleOptions" :key="v" :value="v">
                  {{ v }}
                </option>
              </select>
            </div>
            <span v-if="errors.vehicleType" class="error-message">{{ errors.vehicleType }}</span>
            <span v-else-if="loadingCodes" class="helper-text">Loading available vehicle types...</span>
            <span v-else-if="!getRestrictionCode()" class="helper-text">Select your restriction code first</span>
            <span v-else-if="vehicleOptions.length === 0" class="warning-message">No vehicle types configured for restriction code {{ getRestrictionCode() }}. Please contact support.</span>
            <span v-else class="helper-text">{{ vehicleOptions.length }} vehicle type(s) available for restriction code {{ getRestrictionCode() }}</span>
          </div>

          <!-- Plate Number -->
          <div class="input-group">
            <label class="input-label">Plate Number *</label>
            <div class="input-wrapper" :class="{ error: errors.plateNumber }">
              <ion-icon :icon="barcodeOutline" class="input-icon"></ion-icon>
              <input
                v-model="formData.plateNumber"
                type="text"
                placeholder="ABC 1234"
                class="custom-input with-icon"
                @blur="validateField('plateNumber')"
                @input="errors.plateNumber = ''"
              />
            </div>
            <span v-if="errors.plateNumber" class="error-message">{{ errors.plateNumber }}</span>
            <span v-else class="helper-text">Enter your vehicle plate number</span>
          </div>

          <!-- Vehicle Model -->
          <div class="input-group">
            <label class="input-label">Vehicle Model & Year *</label>
            <div class="input-wrapper" :class="{ error: errors.vehicleModel }">
              <ion-icon :icon="constructOutline" class="input-icon"></ion-icon>
              <input
                v-model="formData.vehicleModel"
                type="text"
                placeholder="Honda Click 125i 2023"
                class="custom-input with-icon"
                @blur="validateField('vehicleModel')"
                @input="errors.vehicleModel = ''"
              />
            </div>
            <span v-if="errors.vehicleModel" class="error-message">{{ errors.vehicleModel }}</span>
          </div>

          <!-- Vehicle Color -->
          <div class="input-group">
            <label class="input-label">Vehicle Color *</label>
            <div class="input-wrapper">
              <ion-icon :icon="colorPaletteOutline" class="input-icon"></ion-icon>
              <input
                v-model="formData.vehicleColor"
                type="text"
                placeholder="Black"
                class="custom-input with-icon"
              />
            </div>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>Note:</strong><br />
              Your vehicle must be registered and have valid documents (OR/CR).
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

<script setup>
import { ref, computed, onMounted, onActivated } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import { useRegistrationStore } from '@/composables/useRegistrationStore';
import {
  arrowBackOutline,
  arrowForwardOutline,
  carOutline,
  bicycleOutline,
  barcodeOutline,
  constructOutline,
  colorPaletteOutline,
  informationCircleOutline,
  codeSlashOutline,
} from 'ionicons/icons';

const router = useRouter();
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

const restrictionCodes = ref({});
const vehiclesByCode = ref({});
const loadingCodes = ref(false);

async function fetchRestrictionCodes() {
  loadingCodes.value = true;
  try {
    const res = await fetch(`${API_BASE}/api/restriction-codes`);
    if (res.ok) {
      const data = await res.json();
      const map = {};
      const vehicles = {};
      for (const r of data.restriction_codes || []) {
        map[r.code] = r.label;
        vehicles[r.code] = r.vehicle_types || [];
      }
      restrictionCodes.value = map;
      vehiclesByCode.value = vehicles;
    }
  } catch {
    restrictionCodes.value = {};
    vehiclesByCode.value = {};
  } finally {
    loadingCodes.value = false;
  }
}

const renderKey = ref(0);
const formData = ref({
  vehicleType: '',
  plateNumber: '',
  vehicleModel: '',
  vehicleColor: '',
});

const errors = ref({
  vehicleType: '',
  plateNumber: '',
  vehicleModel: '',
});

function loadFromStorage() {
  const { state } = useRegistrationStore();
  resetVehicleTypeOnCodeMismatch(state.restrictionCode || '');
  if (state.vehicleType) formData.value.vehicleType = state.vehicleType;
  if (state.plateNumber) formData.value.plateNumber = state.plateNumber;
  if (state.vehicleModel) formData.value.vehicleModel = state.vehicleModel;
  if (state.vehicleColor) formData.value.vehicleColor = state.vehicleColor;
}

onMounted(() => {
  fetchRestrictionCodes();
  loadFromStorage();
});
onActivated(() => {
  renderKey.value++;
  loadFromStorage();
});

function getRestrictionCode() {
  const { getRestrictionCode: code } = useRegistrationStore();
  return code();
}

function resetVehicleTypeOnCodeMismatch(code) {
  const current = formData.value.vehicleType;
  if (!current) return;
  const allowed = vehiclesByCode.value[code] || [];
  if (!allowed.includes(current)) {
    formData.value.vehicleType = '';
  }
}

function getAvailableVehicles() {
  const code = getRestrictionCode();
  return vehiclesByCode.value[code] || [];
}

const vehicleOptions = computed(() => getAvailableVehicles());

const isFormValid = computed(() => {
  return (
    formData.value.vehicleType &&
    formData.value.plateNumber.trim() &&
    formData.value.vehicleModel.trim()
  );
});

const validateField = (fieldName) => {
  switch (fieldName) {
    case 'vehicleType':
      if (!formData.value.vehicleType) {
        errors.value.vehicleType = 'Please select a vehicle type';
        return false;
      }
      errors.value.vehicleType = '';
      return true;

    case 'plateNumber':
      if (!formData.value.plateNumber.trim()) {
        errors.value.plateNumber = 'Plate number is required';
        return false;
      }
      if (formData.value.plateNumber.trim().length < 3) {
        errors.value.plateNumber = 'Please enter a valid plate number';
        return false;
      }
      errors.value.plateNumber = '';
      return true;

    case 'vehicleModel':
      if (!formData.value.vehicleModel.trim()) {
        errors.value.vehicleModel = 'Vehicle model is required';
        return false;
      }
      if (formData.value.vehicleModel.trim().length < 3) {
        errors.value.vehicleModel = 'Please enter a valid model';
        return false;
      }
      errors.value.vehicleModel = '';
      return true;

    default:
      return true;
  }
};

const goBack = () => {
  saveData();
  router.push('/register/step3');
};

const saveData = () => {
  const { setVehicleInfo } = useRegistrationStore();
  setVehicleInfo({
    vehicleType: formData.value.vehicleType,
    plateNumber: formData.value.plateNumber,
    vehicleModel: formData.value.vehicleModel,
    vehicleColor: formData.value.vehicleColor,
  });
};

const handleNext = () => {
  const isVehicleTypeValid = validateField('vehicleType');
  const isPlateNumberValid = validateField('plateNumber');
  const isVehicleModelValid = validateField('vehicleModel');

  if (isVehicleTypeValid && isPlateNumberValid && isVehicleModelValid) {
    saveData();
    router.push('/register/step5');
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

/* Restriction Info Banner */
.restriction-info {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: var(--color-bg-secondary);
  border-radius: 12px;
  margin-bottom: 24px;
  font-size: 13px;
  color: var(--color-text-secondary);
}

.restriction-info ion-icon {
  font-size: 20px;
  color: var(--color-icon-primary);
  flex-shrink: 0;
}

.restriction-info strong {
  color: var(--color-text-primary);
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

.custom-select {
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  padding-right: 40px;
}

.custom-select:invalid {
  color: var(--color-input-placeholder);
}

/* Error State */
.input-wrapper.error .custom-input,
.input-wrapper.error .custom-select {
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

.warning-message {
  display: block;
  font-size: 12px;
  color: var(--color-warning);
  margin-top: 6px;
  font-weight: 500;
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
