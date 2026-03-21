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
          <h1 class="title">Driver's License</h1>
          <p class="subtitle">Upload your professional driver's license</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          
          <!-- Section Header -->
          <div class="section-header">
            <ion-icon :icon="cardOutline"></ion-icon>
            <h2>License Information</h2>
          </div>

          <!-- License Number -->
          <div class="input-group">
            <label class="input-label">License Number *</label>
            <div class="input-wrapper" :class="{ error: errors.licenseNumber }">
              <ion-icon :icon="barcodeOutline" class="input-icon"></ion-icon>
              <input 
                v-model="formData.licenseNumber" 
                type="text" 
                placeholder="N01-12-345678"
                class="custom-input with-icon"
                @blur="validateField('licenseNumber')"
                @input="errors.licenseNumber = ''"
              >
            </div>
            <span v-if="errors.licenseNumber" class="error-message">{{ errors.licenseNumber }}</span>
            <span v-else class="helper-text">Enter your 13-character license number</span>
          </div>

          <!-- License Type -->
          <div class="input-group">
            <label class="input-label">License Type *</label>
            <div class="input-wrapper" :class="{ error: errors.licenseType }">
              <ion-icon :icon="documentTextOutline" class="input-icon"></ion-icon>
              <select v-model="formData.licenseType" class="custom-select with-icon" @blur="validateField('licenseType')" @change="errors.licenseType = ''">
                <option value="">Select license type</option>
                <option value="Professional 1">Professional 1</option>
                <option value="Professional 2">Professional 2</option>
                <option value="Professional 3">Professional 3</option>
              </select>
            </div>
            <span v-if="errors.licenseType" class="error-message">{{ errors.licenseType }}</span>
          </div>

          <!-- Expiry Date -->
          <div class="input-group">
            <label class="input-label">Expiry Date *</label>
            <div class="input-wrapper" :class="{ error: errors.expiryDate }">
              <ion-icon :icon="calendarOutline" class="input-icon"></ion-icon>
              <input 
                v-model="formData.expiryDate" 
                type="date" 
                class="custom-input with-icon"
                :min="minDate"
                @blur="validateField('expiryDate')"
                @input="errors.expiryDate = ''"
              >
            </div>
            <span v-if="errors.expiryDate" class="error-message">{{ errors.expiryDate }}</span>
            <span v-else class="helper-text">License must not be expired</span>
          </div>

          <!-- Restriction Code -->
          <div class="input-group">
            <label class="input-label">Restriction Code *</label>
            <div class="input-wrapper">
              <ion-icon :icon="keyOutline" class="input-icon"></ion-icon>
              <input 
                v-model="formData.restrictionCode" 
                type="text" 
                placeholder="e.g., B1, B2"
                class="custom-input with-icon"
              >
            </div>
            <span class="helper-text">Enter restriction codes (e.g., B1, B2)</span>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>Important:</strong><br>
              Your driver's license must be valid and not expired. Expired licenses will not be accepted.
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
  cardOutline,
  barcodeOutline,
  documentTextOutline,
  calendarOutline,
  keyOutline,
  informationCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const formData = ref({
  licenseNumber: '',
  licenseType: '',
  expiryDate: '',
  restrictionCode: ''
});

const errors = ref({
  licenseNumber: '',
  licenseType: '',
  expiryDate: ''
});

const minDate = computed(() => {
  const today = new Date();
  return today.toISOString().split('T')[0];
});

const isFormValid = computed(() => {
  return formData.value.licenseNumber && 
         formData.value.licenseType && 
         formData.value.expiryDate;
});

const validateField = (fieldName) => {
  switch (fieldName) {
    case 'licenseNumber':
      if (!formData.value.licenseNumber.trim()) {
        errors.value.licenseNumber = 'License number is required';
        return false;
      }
      errors.value.licenseNumber = '';
      return true;

    case 'licenseType':
      if (!formData.value.licenseType) {
        errors.value.licenseType = 'Please select a license type';
        return false;
      }
      errors.value.licenseType = '';
      return true;

    case 'expiryDate':
      if (!formData.value.expiryDate) {
        errors.value.expiryDate = 'Expiry date is required';
        return false;
      }
      errors.value.expiryDate = '';
      return true;

    default:
      return true;
  }
};

const goBack = () => {
  saveData();
  router.push('/register/step2');
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
  router.push('/register/step4');
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
