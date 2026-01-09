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
            <div class="progress-fill" style="width: 71.4%"></div>
          </div>
          <p class="progress-text">Step 5 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Select ID Type</h1>
          <p class="subtitle">Choose your government-issued ID</p>
        </div>

        <!-- Form Section -->
        <div class="form-section">

          <!-- ID Type Selection -->
          <div class="input-group">
            <label class="input-label">Valid Government-Issued ID *</label>
            <div class="input-wrapper" :class="{ error: errors.idType }">
              <ion-icon :icon="cardOutline" class="input-icon"></ion-icon>
              <select 
                v-model="formData.idType" 
                class="custom-select with-icon"
                @change="onIdTypeChange"
                @blur="validateField('idType')"
              >
                <option value="">Select ID Type</option>
                <optgroup label="Primary IDs">
                  <option value="passport">Philippine Passport</option>
                  <option value="drivers_license">Driver's License</option>
                  <option value="umid">UMID (Unified Multi-Purpose ID)</option>
                  <option value="sss">SSS ID</option>
                  <option value="philhealth">PhilHealth ID</option>
                  <option value="pagibig">Pag-IBIG ID</option>
                  <option value="postal">Postal ID</option>
                </optgroup>
                <optgroup label="Government IDs">
                  <option value="voters">Voter's ID</option>
                  <option value="tin">TIN ID</option>
                  <option value="national_id">National ID (PhilSys)</option>
                  <option value="senior_citizen">Senior Citizen ID</option>
                  <option value="pwd">PWD ID</option>
                  <option value="owwa">OWWA ID</option>
                  <option value="seaman">Seaman's Book</option>
                </optgroup>
                <optgroup label="Professional IDs">
                  <option value="prc">PRC ID (Professional Regulation Commission)</option>
                  <option value="company">Company ID (with DTI/SEC registration)</option>
                  <option value="student">School ID (with NSO Birth Certificate)</option>
                </optgroup>
              </select>
            </div>
            <span v-if="errors.idType" class="error-message">{{ errors.idType }}</span>
            <span v-else class="helper-text">Choose one valid ID from the list</span>
          </div>

          <!-- ID Requirements Card (shows after selection) -->
          <div v-if="formData.idType" class="requirements-card">
            <div class="requirements-header">
              <ion-icon :icon="informationCircleOutline"></ion-icon>
              <h3>Requirements for {{ getIdName(formData.idType) }}</h3>
            </div>
            
            <div class="requirements-list">
              <div class="requirement-item">
                <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
                <span>Must be clear and readable</span>
              </div>
              <div class="requirement-item">
                <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
                <span>No glare or shadows</span>
              </div>
              <div class="requirement-item">
                <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
                <span>All corners visible</span>
              </div>
              <div class="requirement-item">
                <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
                <span>Not expired (if applicable)</span>
              </div>
              <div class="requirement-item">
                <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
                <span>Photo and signature visible</span>
              </div>
            </div>

            <!-- What you'll need -->
            <div class="what-need">
              <h4>What you'll need:</h4>
              <div class="need-items">
                <div class="need-item">
                  <ion-icon :icon="cameraOutline"></ion-icon>
                  <span>Front photo of your ID</span>
                </div>
                <div class="need-item">
                  <ion-icon :icon="cameraOutline"></ion-icon>
                  <span>Back photo of your ID</span>
                </div>
                <div class="need-item">
                  <ion-icon :icon="personOutline"></ion-icon>
                  <span>Selfie with your ID</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Sample Photos (shows after selection) -->
          <div v-if="formData.idType" class="sample-card">
            <h3>Sample Photos</h3>
            <div class="sample-images">
              <div class="sample-item">
                <div class="sample-placeholder correct">
                  <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
                  <span>✓ CORRECT</span>
                </div>
                <p>Clear, well-lit, all corners visible</p>
              </div>
              <div class="sample-item">
                <div class="sample-placeholder incorrect">
                  <ion-icon :icon="closeCircleOutline"></ion-icon>
                  <span>✗ INCORRECT</span>
                </div>
                <p>Blurry, cut off, or has glare</p>
              </div>
            </div>
          </div>

          <!-- Privacy Notice -->
          <div class="privacy-notice">
            <ion-icon :icon="lockClosedOutline"></ion-icon>
            <div class="privacy-text">
              <strong>Your Privacy is Protected</strong><br>
              All ID photos will be encrypted and used only for verification. We comply with the Data Privacy Act of 2012 (RA 10173) and will never share your information without consent.
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
              :disabled="!formData.idType"
            >
              Start ID Capture
              <ion-icon slot="end" :icon="cameraOutline"></ion-icon>
            </ion-button>
          </div>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonContent,
  IonButton,
  IonIcon,
  alertController
} from '@ionic/vue';
import {
  arrowBackOutline,
  cardOutline,
  informationCircleOutline,
  checkmarkCircleOutline,
  closeCircleOutline,
  cameraOutline,
  personOutline,
  lockClosedOutline
} from 'ionicons/icons';

const router = useRouter();

// Form data
const formData = ref({
  idType: ''
});

// Error states
const errors = ref({
  idType: ''
});

// ID Type names mapping
const idTypeNames = {
  'passport': 'Philippine Passport',
  'drivers_license': "Driver's License",
  'umid': 'UMID (Unified Multi-Purpose ID)',
  'sss': 'SSS ID',
  'philhealth': 'PhilHealth ID',
  'pagibig': 'Pag-IBIG ID',
  'postal': 'Postal ID',
  'voters': "Voter's ID",
  'tin': 'TIN ID',
  'national_id': 'National ID (PhilSys)',
  'senior_citizen': 'Senior Citizen ID',
  'pwd': 'PWD ID',
  'owwa': 'OWWA ID',
  'seaman': "Seaman's Book",
  'prc': 'PRC ID',
  'company': 'Company ID',
  'student': 'School ID'
};

// Load data
onMounted(() => {
  const savedData = localStorage.getItem('registrationData');
  if (savedData) {
    const data = JSON.parse(savedData);
    if (data.idType) formData.value.idType = data.idType;
  }
});

// Get ID name
const getIdName = (idType) => {
  return idTypeNames[idType] || idType;
};

// ID type change handler
const onIdTypeChange = () => {
  errors.value.idType = '';
};

// Field validation
const validateField = (fieldName) => {
  if (fieldName === 'idType') {
    if (!formData.value.idType) {
      errors.value.idType = 'Please select an ID type';
      return false;
    }
    errors.value.idType = '';
    return true;
  }
  return true;
};

// Navigation
const goBack = () => {
  router.push('/register/step4');
};

const handleNext = async () => {
  if (!validateField('idType')) return;

  // Show final warning before camera
  const alert = await alertController.create({
    header: 'Ready to Capture ID',
    message: 'Make sure you have good lighting and a clear background. You will need to take photos of the front and back of your ID, then a selfie holding your ID.',
    cssClass: 'camera-warning-modal',
    buttons: [
      {
        text: 'Not Ready',
        role: 'cancel',
        cssClass: 'alert-button-cancel'
      },
      {
        text: 'I\'m Ready',
        cssClass: 'alert-button-confirm',
        handler: () => {
          // Save ID type
          const existingData = JSON.parse(localStorage.getItem('registrationData') || '{}');
          existingData.idType = formData.value.idType;
          existingData.idTypeName = getIdName(formData.value.idType);
          localStorage.setItem('registrationData', JSON.stringify(existingData));
          
          // Navigate to Step 6 (ID Photo Capture)
          router.push('/register/step6');
        }
      }
    ]
  });
  await alert.present();
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

/* Progress Section */
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

/* Title Section */
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

/* Form Section */
.form-section {
  max-width: 600px;
  margin: 0 auto;
  width: 100%;
}

/* Input Group */
.input-group {
  margin-bottom: 24px;
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
  top: 16px;
  font-size: 20px;
  color: #4CAF50;
  z-index: 2;
}

/* Select Dropdown */
.custom-select {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
  background: white;
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
  border-color: #4CAF50;
}

/* Error State */
.input-wrapper.error .custom-select {
  border-color: #f44336;
}

.input-wrapper.error .input-icon {
  color: #f44336;
}

/* Helper/Error Text */
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

/* Requirements Card */
.requirements-card {
  background: #E8F5E9;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 20px;
  border: 2px solid #4CAF50;
}

.requirements-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.requirements-header ion-icon {
  font-size: 24px;
  color: #4CAF50;
}

.requirements-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: #2E7D32;
  margin: 0;
}

.requirements-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 20px;
}

.requirement-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.requirement-item ion-icon {
  font-size: 20px;
  color: #4CAF50;
  flex-shrink: 0;
}

.requirement-item span {
  font-size: 14px;
  color: #2E7D32;
  font-weight: 500;
}

/* What You'll Need */
.what-need {
  border-top: 2px solid #C8E6C9;
  padding-top: 16px;
}

.what-need h4 {
  font-size: 14px;
  font-weight: 700;
  color: #2E7D32;
  margin: 0 0 12px 0;
}

.need-items {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.need-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.need-item ion-icon {
  font-size: 18px;
  color: #4CAF50;
}

.need-item span {
  font-size: 13px;
  color: #2E7D32;
}

/* Sample Card */
.sample-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 2px solid #e0e0e0;
}

.sample-card h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.sample-images {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.sample-item {
  text-align: center;
}

.sample-placeholder {
  height: 120px;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 8px;
}

.sample-placeholder.correct {
  background: #E8F5E9;
  border: 2px solid #4CAF50;
}

.sample-placeholder.incorrect {
  background: #FFEBEE;
  border: 2px solid #f44336;
}

.sample-placeholder ion-icon {
  font-size: 32px;
}

.sample-placeholder.correct ion-icon {
  color: #4CAF50;
}

.sample-placeholder.incorrect ion-icon {
  color: #f44336;
}

.sample-placeholder span {
  font-size: 12px;
  font-weight: 700;
}

.sample-placeholder.correct span {
  color: #2E7D32;
}

.sample-placeholder.incorrect span {
  color: #c62828;
}

.sample-item p {
  font-size: 12px;
  color: #666;
  margin: 0;
  line-height: 1.4;
}

/* Privacy Notice */
.privacy-notice {
  display: flex;
  gap: 12px;
  background: #E3F2FD;
  border-left: 4px solid #2196F3;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
}

.privacy-notice ion-icon {
  font-size: 24px;
  color: #2196F3;
  flex-shrink: 0;
  margin-top: 2px;
}

.privacy-text {
  font-size: 13px;
  color: #1565C0;
  line-height: 1.5;
}

.privacy-text strong {
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

/* Alert Modals */
:deep(.camera-warning-modal .alert-wrapper) {
  border-radius: 20px;
  max-width: 380px;
}

:deep(.alert-button-cancel) {
  color: #666 !important;
}

:deep(.alert-button-confirm) {
  color: #4CAF50 !important;
  font-weight: 700 !important;
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

  .sample-images {
    grid-template-columns: 1fr;
  }
}
</style>