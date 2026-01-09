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
            <div class="progress-fill" style="width: 14.28%"></div>
          </div>
          <p class="progress-text">Step 1 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Contact Information</h1>
          <p class="subtitle">We'll use this to verify your account</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          <!-- Email Input -->
          <div class="input-group">
            <label class="input-label">Email Address *</label>
            <div class="input-wrapper" :class="{ error: emailError, success: emailValid }">
              <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
              <input 
                v-model="email" 
                type="email" 
                placeholder="juan@example.com"
                class="custom-input"
                @blur="validateEmail"
                @input="emailError = ''"
              >
              <ion-icon 
                v-if="emailValid" 
                :icon="checkmarkCircleOutline" 
                class="success-icon"
              ></ion-icon>
            </div>
            <span v-if="emailError" class="error-message">{{ emailError }}</span>
            <span v-else class="helper-text">We'll send verification to this email</span>
          </div>

          <!-- Phone Input -->
          <div class="input-group">
            <label class="input-label">Phone Number *</label>
            <div class="input-wrapper" :class="{ error: phoneError, success: phoneValid }">
              <ion-icon :icon="callOutline" class="input-icon"></ion-icon>
              <input 
                v-model="phone" 
                type="tel" 
                placeholder="09171234567"
                class="custom-input"
                @blur="validatePhone"
                @input="phoneError = ''"
                maxlength="11"
              >
              <ion-icon 
                v-if="phoneValid" 
                :icon="checkmarkCircleOutline" 
                class="success-icon"
              ></ion-icon>
            </div>
            <span v-if="phoneError" class="error-message">{{ phoneError }}</span>
            <span v-else class="helper-text">Format: 09171234567 (11 digits)</span>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>Why we need this:</strong><br>
              To verify your identity and send important updates about your orders and account.
            </div>
          </div>

          <!-- Next Button -->
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
  mailOutline,
  callOutline,
  checkmarkCircleOutline,
  informationCircleOutline
} from 'ionicons/icons';

const router = useRouter();

// Form data
const email = ref('');
const phone = ref('');

// Validation states
const emailError = ref('');
const phoneError = ref('');
const emailValid = ref(false);
const phoneValid = ref(false);

// Email validation
const validateEmail = () => {
  if (!email.value) {
    emailError.value = 'Email is required';
    emailValid.value = false;
    return false;
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    emailError.value = 'Please enter a valid email address';
    emailValid.value = false;
    return false;
  }
  
  emailError.value = '';
  emailValid.value = true;
  return true;
};

// Phone validation
const validatePhone = () => {
  if (!phone.value) {
    phoneError.value = 'Phone number is required';
    phoneValid.value = false;
    return false;
  }
  
  // Remove any non-digit characters
  const cleanPhone = phone.value.replace(/\D/g, '');
  
  // Check if it starts with 09 and has 11 digits
  if (!cleanPhone.startsWith('09') || cleanPhone.length !== 11) {
    phoneError.value = 'Please enter valid PH mobile (09XXXXXXXXX)';
    phoneValid.value = false;
    return false;
  }
  
  phoneError.value = '';
  phoneValid.value = true;
  return true;
};

// Form validation
const isFormValid = computed(() => {
  return email.value && 
         phone.value && 
         emailValid.value && 
         phoneValid.value;
});

// Navigation
const goBack = () => {
  router.push('/welcome');
};

const handleNext = () => {
  // Validate both fields
  const emailOk = validateEmail();
  const phoneOk = validatePhone();
  
  if (emailOk && phoneOk) {
    // Store data in localStorage for now (will use Vuex/Pinia later)
    const registrationData = {
      email: email.value,
      phone: phone.value
    };
    localStorage.setItem('registrationData', JSON.stringify(registrationData));
    
    // Navigate to Step 2
    router.push('/register/step2');
  }
};
</script>

<style scoped>
.step-content {
  --background: #ffffff;
}

.step-container {
  min-height: 100vh;
  padding: 24px;
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
  max-width: 400px;
  margin: 0 auto;
  width: 100%;
}

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
  font-size: 20px;
  color: #4CAF50;
  z-index: 2;
}

.custom-input {
  width: 100%;
  padding: 14px 16px 14px 48px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
  background: white;
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input:focus {
  outline: none;
  border-color: #4CAF50;
}

.custom-input::placeholder {
  color: #999;
}

/* Success Icon */
.success-icon {
  position: absolute;
  right: 16px;
  font-size: 24px;
  color: #4CAF50;
  z-index: 2;
}

/* Error State */
.input-wrapper.error .custom-input {
  border-color: #f44336;
  padding-right: 16px;
}

.input-wrapper.error .input-icon {
  color: #f44336;
}

/* Success State */
.input-wrapper.success .custom-input {
  border-color: #4CAF50;
  padding-right: 48px;
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

/* Info Box */
.info-box {
  display: flex;
  gap: 12px;
  background: #E3F2FD;
  border-left: 4px solid #2196F3;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 32px;
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

/* Next Button */
.next-button {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0.5px;
  margin-top: 24px;
}

.next-button:disabled {
  --background: #e0e0e0;
  --color: #999;
}

/* Responsive */
@media (max-width: 480px) {
  .title {
    font-size: 24px;
  }

  .step-container {
    padding: 20px 16px;
  }
}
</style>