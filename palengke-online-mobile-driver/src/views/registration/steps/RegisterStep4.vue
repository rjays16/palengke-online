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

          <!-- Vehicle Type -->
          <div class="input-group">
            <label class="input-label">Vehicle Type *</label>
            <div class="input-wrapper" :class="{ error: errors.vehicleType }">
              <ion-icon :icon="bicycleOutline" class="input-icon"></ion-icon>
              <select v-model="formData.vehicleType" class="custom-select with-icon" @blur="validateField('vehicleType')" @change="errors.vehicleType = ''">
                <option value="">Select vehicle type</option>
                <option value="Motorcycle">Motorcycle</option>
                <option value="Tricycle">Tricycle</option>
                <option value="Van">Van</option>
                <option value="Truck">Truck</option>
              </select>
            </div>
            <span v-if="errors.vehicleType" class="error-message">{{ errors.vehicleType }}</span>
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
              >
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
              >
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
              >
            </div>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>Note:</strong><br>
              Your vehicle must be registered and have valid documents (OR/CR).
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="button-group">
            <ion-button 
              expand="block" 
              fill="outline"
              color="medium"
              class="back-btn"
              @click="goBack"
            >
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
              Back
            </ion-button>

            <ion-button 
              expand="block" 
              class="next-button"
              @click="handleNext"
              :disabled="!isFormValid"
            >
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
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonContent,
  IonButton,
  IonIcon
} from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  carOutline,
  bicycleOutline,
  barcodeOutline,
  constructOutline,
  colorPaletteOutline,
  informationCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const formData = ref({
  vehicleType: '',
  plateNumber: '',
  vehicleModel: '',
  vehicleColor: ''
});

const errors = ref({
  vehicleType: '',
  plateNumber: '',
  vehicleModel: ''
});

const isFormValid = computed(() => {
  return formData.value.vehicleType && 
         formData.value.plateNumber && 
         formData.value.vehicleModel;
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
      errors.value.plateNumber = '';
      return true;

    case 'vehicleModel':
      if (!formData.value.vehicleModel.trim()) {
        errors.value.vehicleModel = 'Vehicle model is required';
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
  const existingData = JSON.parse(localStorage.getItem('driverRegistrationData') || '{}');
  const updatedData = {
    ...existingData,
    ...formData.value
  };
  localStorage.setItem('driverRegistrationData', JSON.stringify(updatedData));
};

const handleNext = () => {
  saveData();
  router.push('/register/step5');
};
</script>

<style scoped>
.step-content {
  --background: #ffffff;
}

.step-container {
  min-height: 100vh;
  padding: 24px;
  padding-bottom: 100px;
  position: relative;
}

.back-button {
  position: absolute;
  top: 24px;
  left: 24px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border-radius: 12px;
  cursor: pointer;
  z-index: 10;
  transition: background 0.2s;
}

.back-button:active {
  transform: scale(0.95);
  background: #e0e0e0;
}

.back-button ion-icon {
  font-size: 24px;
  color: #1a1a1a;
}

.progress-section {
  margin-top: 70px;
  margin-bottom: 32px;
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: #e0e0e0;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: #4CAF50;
  transition: width 0.3s ease;
}

.progress-text {
  text-align: center;
  font-size: 13px;
  color: #666;
  margin: 0;
  font-weight: 600;
}

.title-section {
  text-align: center;
  margin-bottom: 40px;
}

.title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 15px;
  color: #666;
  margin: 0;
}

.form-section {
  max-width: 500px;
  margin: 0 auto;
  width: 100%;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f0f0f0;
}

.section-header ion-icon {
  font-size: 24px;
  color: #4CAF50;
}

.section-header h2 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.input-group {
  margin-bottom: 20px;
}

.input-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
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
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #4CAF50;
  z-index: 2;
}

.custom-input,
.custom-select {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
  background: white;
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input.with-icon,
.custom-select.with-icon {
  padding-left: 48px;
}

.custom-input:focus,
.custom-select:focus {
  outline: none;
  border-color: #4CAF50;
}

.custom-input::placeholder {
  color: #999;
}

.custom-select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  background-size: 20px;
}

.input-wrapper.error .custom-input,
.input-wrapper.error .custom-select {
  border-color: #f44336;
}

.input-wrapper.error .input-icon {
  color: #f44336;
}

.helper-text {
  display: block;
  font-size: 12px;
  color: #999;
  margin-top: 6px;
}

.error-message {
  display: block;
  font-size: 12px;
  color: #f44336;
  margin-top: 6px;
  font-weight: 500;
}

.info-box {
  display: flex;
  gap: 12px;
  background: #E3F2FD;
  border-left: 4px solid #2196F3;
  padding: 16px;
  border-radius: 8px;
  margin: 24px 0;
}

.info-box ion-icon {
  font-size: 24px;
  color: #2196F3;
  flex-shrink: 0;
  margin-top: 2px;
}

.info-text {
  font-size: 13px;
  color: #1565C0;
  line-height: 1.5;
}

.info-text strong {
  font-weight: 700;
}

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
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.next-button:disabled {
  --background: #e0e0e0;
  --color: #999;
}

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
