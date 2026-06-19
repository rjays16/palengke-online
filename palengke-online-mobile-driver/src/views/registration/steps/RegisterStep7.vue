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
            <div class="progress-fill" style="width: 100%"></div>
          </div>
          <p class="progress-text">Step 7 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Review & Submit</h1>
          <p class="subtitle">Please review your information</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          
          <!-- Contact Information -->
          <div class="review-card">
            <div class="review-header">
              <ion-icon :icon="mailOutline"></ion-icon>
              <h2>Contact Information</h2>
            </div>
            <div class="review-content">
              <div class="review-item">
                <span class="label">Email:</span>
                <span class="value">{{ registrationData.email || 'N/A' }}</span>
              </div>
              <div class="review-item">
                <span class="label">Phone:</span>
                <span class="value">{{ registrationData.phone || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Personal Information -->
          <div class="review-card">
            <div class="review-header">
              <ion-icon :icon="personOutline"></ion-icon>
              <h2>Personal Information</h2>
            </div>
            <div class="review-content">
              <div class="review-item">
                <span class="label">Name:</span>
                <span class="value">{{ fullName || 'N/A' }}</span>
              </div>
              <div class="review-item">
                <span class="label">Birthdate:</span>
                <span class="value">{{ registrationData.birthdate || 'N/A' }}</span>
              </div>
              <div class="review-item">
                <span class="label">Birthplace:</span>
                <span class="value">{{ registrationData.birthplace || 'N/A' }}</span>
              </div>
              <div class="review-item">
                <span class="label">Address:</span>
                <span class="value">{{ registrationData.currentAddress || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Driver's License -->
          <div class="review-card">
            <div class="review-header">
              <ion-icon :icon="cardOutline"></ion-icon>
              <h2>Driver's License</h2>
            </div>
            <div class="review-content">
              <div class="review-item">
                <span class="label">License #:</span>
                <span class="value">{{ registrationData.licenseNumber || 'N/A' }}</span>
              </div>
              <div class="review-item">
                <span class="label">Type:</span>
                <span class="value">{{ registrationData.licenseType || 'N/A' }}</span>
              </div>
              <div class="review-item">
                <span class="label">Expiry:</span>
                <span class="value">{{ registrationData.expiryDate || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Vehicle Information -->
          <div class="review-card">
            <div class="review-header">
              <ion-icon :icon="carOutline"></ion-icon>
              <h2>Vehicle Information</h2>
            </div>
            <div class="review-content">
              <div class="review-item">
                <span class="label">Type:</span>
                <span class="value">{{ registrationData.vehicleType || 'N/A' }}</span>
              </div>
              <div class="review-item">
                <span class="label">Plate #:</span>
                <span class="value">{{ registrationData.plateNumber || 'N/A' }}</span>
              </div>
              <div class="review-item">
                <span class="label">Model:</span>
                <span class="value">{{ registrationData.vehicleModel || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Terms Checkbox -->
          <div class="terms-checkbox">
            <input 
              type="checkbox" 
              id="terms" 
              v-model="acceptedTerms"
              class="checkbox-input"
            >
            <label for="terms">
              I agree to the <a href="#" @click.prevent="showTerms">Terms & Conditions</a> and confirm that all information provided is accurate.
            </label>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>What happens next?</strong><br>
              Our team will review your application within 24-48 hours. You will receive an email notification once approved.
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
              class="submit-button"
              @click="handleSubmit"
              :disabled="!acceptedTerms || isSubmitting"
            >
              <ion-spinner v-if="isSubmitting" name="crescent"></ion-spinner>
              <span v-else>Submit Application</span>
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
  IonIcon,
  IonSpinner,
  alertController
} from '@ionic/vue';
import {
  arrowBackOutline,
  mailOutline,
  personOutline,
  cardOutline,
  carOutline,
  informationCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const acceptedTerms = ref(false);
const isSubmitting = ref(false);

const registrationData = JSON.parse(localStorage.getItem('driverRegistrationData') || '{}');

const fullName = computed(() => {
  const { firstName, middleName, lastName } = registrationData;
  const parts = [firstName, middleName, lastName].filter(Boolean);
  return parts.join(' ');
});

const showTerms = async () => {
  const alert = await alertController.create({
    header: 'Terms & Conditions',
    message: 'By submitting this application, you agree to our terms of service, privacy policy, and driver partnership agreement.',
    buttons: ['Close']
  });
  await alert.present();
};

const goBack = () => {
  router.push('/register/step6');
};

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

const handleSubmit = async () => {
  if (!acceptedTerms.value || isSubmitting.value) return;

  isSubmitting.value = true;

  try {
    const data = JSON.parse(localStorage.getItem('driverRegistrationData') || '{}');

    const payload = {
      email: data.email,
      country_code: data.countryCode,
      phone: data.phone,
      full_phone: data.fullPhone,
      email_verified: data.emailVerified || false,
      first_name: data.firstName,
      middle_name: data.middleName || '',
      last_name: data.lastName,
      country: data.country,
      birthdate: data.birthdate,
      birthplace: data.birthplace,
      current_address: data.currentAddress,
      license_number: data.licenseNumber,
      license_type: data.licenseType,
      restriction_code: data.restrictionCode,
      expiry_date: data.expiryDate,
      vehicle_type: data.vehicleType,
      plate_number: data.plateNumber,
      vehicle_model: data.vehicleModel,
      vehicle_color: data.vehicleColor || '',
      license_front_photo: data.license_front_photo || '',
      vehicle_reg_photo: data.vehicle_reg_photo || '',
      nbi_clearance_photo: data.nbi_clearance_photo || '',
      birth_cert_photo: data.birth_cert_photo || '',
      selfie_photo: data.selfiePhoto || '',
    };

    const res = await fetch(`${API_BASE}/api/driver/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    const result = await res.json();

    if (!res.ok) {
      throw new Error(result.message || 'Submission failed');
    }

    const submittedData = localStorage.getItem('driverRegistrationData');
    if (submittedData) {
      localStorage.setItem('submittedRegistrationData', submittedData);
    }
    localStorage.removeItem('driverRegistrationData');

    await showSuccessModal('Application submitted successfully!\nWe\'ll review it within 24-48 hours.');

    router.push('/register/success');
  } catch (error) {
    const alert = await alertController.create({
      header: 'Error',
      message: error.message || 'Something went wrong. Please try again.',
      buttons: ['OK']
    });
    await alert.present();
  } finally {
    isSubmitting.value = false;
  }
};

const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      textDiv.style.whiteSpace = 'pre-line';
      
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  await alert.onDidDismiss();
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

.review-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  padding-bottom: 10px;
  border-bottom: 1px solid #e0e0e0;
}

.review-header ion-icon {
  font-size: 20px;
  color: #4CAF50;
}

.review-header h2 {
  font-size: 15px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.review-content {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.review-item {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
}

.review-item .label {
  font-size: 13px;
  color: #666;
  flex-shrink: 0;
}

.review-item .value {
  font-size: 13px;
  color: #1a1a1a;
  font-weight: 600;
  text-align: right;
}

.terms-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 12px;
  margin: 24px 0;
}

.checkbox-input {
  width: 22px;
  height: 22px;
  margin-top: 2px;
  cursor: pointer;
  accent-color: #4CAF50;
  flex-shrink: 0;
}

.terms-checkbox label {
  font-size: 13px;
  color: #666;
  line-height: 1.5;
  cursor: pointer;
}

.terms-checkbox a {
  color: #4CAF50;
  text-decoration: none;
  font-weight: 600;
}

.info-box {
  display: flex;
  gap: 12px;
  background: #E8F5E9;
  border-left: 4px solid #4CAF50;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
}

.info-box ion-icon {
  font-size: 24px;
  color: #4CAF50;
  flex-shrink: 0;
  margin-top: 2px;
}

.info-text {
  font-size: 13px;
  color: #2E7D32;
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

.submit-button {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.submit-button:disabled {
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

  .submit-button {
    order: 1;
  }
}
</style>
