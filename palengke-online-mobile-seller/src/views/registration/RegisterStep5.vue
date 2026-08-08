<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">
        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Progress Indicator -->
        <div v-if="!isLoggedIn" class="progress-section">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 71.4%"></div>
          </div>
          <p class="progress-text">Step 5 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">{{ isLoggedIn ? 'Verify Your Account' : 'Select ID Type' }}</h1>
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
                  <span><font-awesome-icon icon="check" /> CORRECT</span>
                </div>
                <p>Clear, well-lit, all corners visible</p>
              </div>
              <div class="sample-item">
                <div class="sample-placeholder incorrect">
                  <ion-icon :icon="closeCircleOutline"></ion-icon>
                  <span><font-awesome-icon icon="times" /> INCORRECT</span>
                </div>
                <p>Blurry, cut off, or has glare</p>
              </div>
            </div>
          </div>

          <!-- Privacy Notice -->
          <div class="privacy-notice">
            <ion-icon :icon="lockClosedOutline"></ion-icon>
            <div class="privacy-text">
              <strong>Your Privacy is Protected</strong><br />
              All ID photos will be encrypted and used only for verification. We comply with the Data Privacy Act of
              2012 (RA 10173) and will never share your information without consent.
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="button-group">
            <ion-button expand="block" fill="outline" color="medium" class="back-btn" @click="goBack">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
              Back
            </ion-button>

            <ion-button expand="block" class="next-button" @click="handleNext" :disabled="!formData.idType">
              Start ID Capture
              <ion-icon slot="end" :icon="cameraOutline"></ion-icon>
            </ion-button>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, alertController } from '@ionic/vue';
import {
  arrowBackOutline,
  cardOutline,
  informationCircleOutline,
  checkmarkCircleOutline,
  closeCircleOutline,
  cameraOutline,
  personOutline,
  lockClosedOutline,
} from 'ionicons/icons';
import { useRegistrationStore } from '@/stores/registration';

const router = useRouter();
const reg = useRegistrationStore();

const isLoggedIn = ref(!!localStorage.getItem('seller_token') && !!localStorage.getItem('userEmail'));

// Form data
const formData = ref({
  idType: '',
} as { idType: string });

// Error states
const errors = ref({
  idType: '',
});

// ID Type names mapping
const idTypeNames = {
  passport: 'Philippine Passport',
  drivers_license: "Driver's License",
  umid: 'UMID (Unified Multi-Purpose ID)',
  sss: 'SSS ID',
  philhealth: 'PhilHealth ID',
  pagibig: 'Pag-IBIG ID',
  postal: 'Postal ID',
  voters: "Voter's ID",
  tin: 'TIN ID',
  national_id: 'National ID (PhilSys)',
  senior_citizen: 'Senior Citizen ID',
  pwd: 'PWD ID',
  owwa: 'OWWA ID',
  seaman: "Seaman's Book",
  prc: 'PRC ID',
  company: 'Company ID',
  student: 'School ID',
};

// Load data
onMounted(() => {
  reg.load();
  if (reg.data.idType) formData.value.idType = reg.data.idType;
});

// Get ID name
const getIdName = (idType: string) => {
  return idTypeNames[idType as keyof typeof idTypeNames] || idType;
};

// ID type change handler
const onIdTypeChange = () => {
  errors.value.idType = '';
};

// Field validation
const validateField = (fieldName: string) => {
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
  if (isLoggedIn.value) {
    const redirect = localStorage.getItem('verificationRedirect') || '/home';
    localStorage.removeItem('verificationRedirect');
    router.push(redirect);
  } else {
    router.push('/register/step4');
  }
};

const handleNext = async () => {
  if (!validateField('idType')) return;

  // Show final warning before camera
  const alert = await alertController.create({
    header: 'Ready to Capture ID',
    message:
      'Make sure you have good lighting and a clear background. You will need to take photos of the front and back of your ID, then a selfie holding your ID.',
    cssClass: 'camera-warning-modal',
    buttons: [
      {
        text: 'Not Ready',
        role: 'cancel',
        cssClass: 'alert-button-cancel',
      },
      {
        text: "I'm Ready",
        cssClass: 'alert-button-confirm',
        handler: () => {
          reg.setFields({ idType: formData.value.idType, idTypeName: getIdName(formData.value.idType) });

          // Navigate to Step 6 (ID Photo Capture)
          router.push('/register/step6');
        },
      },
    ],
  });
  await alert.present();
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

/* Error State */
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

/* Requirements Card */
.requirements-card {
  background: var(--color-success-bg);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 20px;
  border: 2px solid var(--color-success-border);
}

.requirements-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.requirements-header ion-icon {
  font-size: 24px;
  color: var(--color-success-border);
}

.requirements-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-success-text);
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
  color: var(--color-success-border);
  flex-shrink: 0;
}

.requirement-item span {
  font-size: 14px;
  color: var(--color-success-text);
  font-weight: 500;
}

/* What You'll Need */
.what-need {
  border-top: 2px solid var(--color-success-bg);
  padding-top: 16px;
}

.what-need h4 {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-success-text);
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
  color: var(--color-success-border);
}

.need-item span {
  font-size: 13px;
  color: var(--color-success-text);
}

/* Sample Card */
.sample-card {
  background: var(--color-card-bg);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px var(--color-card-shadow);
  border: 2px solid var(--color-border);
}

.sample-card h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-text-primary);
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
  background: var(--color-success-bg);
  border: 2px solid var(--color-success-border);
}

.sample-placeholder.incorrect {
  background: var(--color-error-bg);
  border: 2px solid var(--color-error-border);
}

.sample-placeholder ion-icon {
  font-size: 32px;
}

.sample-placeholder.correct ion-icon {
  color: var(--color-success-border);
}

.sample-placeholder.incorrect ion-icon {
  color: var(--color-error-border);
}

.sample-placeholder span {
  font-size: 12px;
  font-weight: 700;
}

.sample-placeholder.correct span {
  color: var(--color-success-text);
}

.sample-placeholder.incorrect span {
  color: var(--color-error-text);
}

.sample-item p {
  font-size: 12px;
  color: var(--color-text-secondary);
  margin: 0;
  line-height: 1.4;
}

/* Privacy Notice */
.privacy-notice {
  display: flex;
  gap: 12px;
  background: var(--color-info-bg);
  border-left: 4px solid var(--color-info-border);
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
}

.privacy-notice ion-icon {
  font-size: 24px;
  color: var(--color-info-border);
  flex-shrink: 0;
  margin-top: 2px;
}

.privacy-text {
  font-size: 13px;
  color: var(--color-info-text);
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

/* Alert Modals */
:deep(.camera-warning-modal .alert-wrapper) {
  border-radius: 20px;
  max-width: 380px;
  background: var(--color-modal-bg);
}

:deep(.alert-button-cancel) {
  color: var(--color-text-secondary) !important;
}

:deep(.alert-button-confirm) {
  color: var(--color-primary) !important;
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
