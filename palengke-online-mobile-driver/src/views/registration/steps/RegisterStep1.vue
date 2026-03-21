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
            <div class="input-wrapper" :class="{ error: errors.email, success: isEmailValid }">
              <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
              <input 
                v-model="email" 
                type="email" 
                placeholder="driver@example.com"
                class="custom-input with-icon"
                @input="onEmailInput"
                @blur="validateField('email')"
              >
              <ion-icon 
                v-if="isEmailValid" 
                :icon="checkmarkCircleOutline" 
                class="success-icon"
              ></ion-icon>
            </div>
            <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
            <span v-else class="helper-text">We'll send verification to this email</span>
          </div>

          <!-- Phone Number -->
          <div class="input-group">
            <label class="input-label">Phone Number *</label>
            <div class="input-wrapper" :class="{ 
              error: errors.phone, 
              success: isPhoneValid && phone.length === 11
            }">
              <ion-icon :icon="callOutline" class="input-icon"></ion-icon>
              <input 
                v-model="phone" 
                type="tel" 
                placeholder="09171234567"
                class="custom-input with-icon"
                maxlength="11"
                @input="onPhoneInput"
                @blur="validateField('phone')"
              >
              <ion-icon 
                v-if="isPhoneValid && phone.length === 11" 
                :icon="checkmarkCircleOutline" 
                class="success-icon"
              ></ion-icon>
            </div>
            <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
            <span v-else class="helper-text">11 digits starting with 09</span>
          </div>

          <!-- Password -->
          <div class="input-group">
            <label class="input-label">Password *</label>
            <div class="input-wrapper" :class="{ error: errors.password, success: isPasswordValid }">
              <ion-icon :icon="lockClosedOutline" class="input-icon"></ion-icon>
              <input 
                v-model="password" 
                :type="showPassword ? 'text' : 'password'" 
                placeholder="Create a password"
                class="custom-input with-icon with-toggle"
                @input="onPasswordInput"
                @blur="validateField('password')"
              >
              <ion-icon 
                :icon="showPassword ? eyeOffOutline : eyeOutline" 
                class="password-toggle"
                @click="showPassword = !showPassword"
              ></ion-icon>
            </div>
            <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
            <span v-else class="helper-text">At least 8 characters</span>
          </div>

          <!-- Confirm Password -->
          <div class="input-group">
            <label class="input-label">Confirm Password *</label>
            <div class="input-wrapper" :class="{ error: errors.confirmPassword, success: isConfirmPasswordValid }">
              <ion-icon :icon="lockClosedOutline" class="input-icon"></ion-icon>
              <input 
                v-model="confirmPassword" 
                :type="showPassword ? 'text' : 'password'" 
                placeholder="Confirm your password"
                class="custom-input with-icon"
                @input="onConfirmPasswordInput"
                @blur="validateField('confirmPassword')"
              >
              <ion-icon 
                v-if="isConfirmPasswordValid" 
                :icon="checkmarkCircleOutline" 
                class="success-icon"
              ></ion-icon>
            </div>
            <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
            <span v-else-if="isConfirmPasswordValid" class="success-message">Passwords match</span>
            <span v-else class="helper-text">Re-enter your password</span>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>Why we need this:</strong><br>
              Your email address will be verified with a one-time code to ensure account security.
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
  IonIcon,
  toastController
} from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  mailOutline,
  callOutline,
  lockClosedOutline,
  eyeOutline,
  eyeOffOutline,
  checkmarkCircleOutline,
  informationCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const email = ref('');
const phone = ref('');
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);

const errors = ref({
  email: '',
  phone: '',
  password: '',
  confirmPassword: ''
});

const isEmailValid = computed(() => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return email.value && emailRegex.test(email.value);
});

const isPhoneValid = computed(() => {
  return phone.value.startsWith('09') && phone.value.length === 11;
});

const isPasswordValid = computed(() => {
  return password.value.length >= 8;
});

const isConfirmPasswordValid = computed(() => {
  return confirmPassword.value === password.value && password.value.length >= 8;
});

const isFormValid = computed(() => {
  return isEmailValid.value && isPhoneValid.value && isPasswordValid.value && isConfirmPasswordValid.value;
});

const onEmailInput = () => {
  errors.value.email = '';
};

const onPhoneInput = () => {
  phone.value = phone.value.replace(/\D/g, '');
  errors.value.phone = '';
};

const onPasswordInput = () => {
  errors.value.password = '';
};

const onConfirmPasswordInput = () => {
  errors.value.confirmPassword = '';
};

const validateField = (field) => {
  if (field === 'email') {
    if (!email.value) {
      errors.value.email = 'Email is required';
      return false;
    }
    if (!isEmailValid.value) {
      errors.value.email = 'Please enter a valid email';
      return false;
    }
    errors.value.email = '';
    return true;
  }
  
  if (field === 'phone') {
    if (!phone.value) {
      errors.value.phone = 'Phone number is required';
      return false;
    }
    if (!phone.value.startsWith('09')) {
      errors.value.phone = 'Phone must start with 09';
      return false;
    }
    if (phone.value.length !== 11) {
      errors.value.phone = 'Phone must be 11 digits';
      return false;
    }
    errors.value.phone = '';
    return true;
  }

  if (field === 'password') {
    if (!password.value) {
      errors.value.password = 'Password is required';
      return false;
    }
    if (password.value.length < 8) {
      errors.value.password = 'Password must be at least 8 characters';
      return false;
    }
    errors.value.password = '';
    return true;
  }

  if (field === 'confirmPassword') {
    if (!confirmPassword.value) {
      errors.value.confirmPassword = 'Please confirm your password';
      return false;
    }
    if (confirmPassword.value !== password.value) {
      errors.value.confirmPassword = 'Passwords do not match';
      return false;
    }
    errors.value.confirmPassword = '';
    return true;
  }
};

const goBack = () => {
  router.push('/signup');
};

const handleNext = () => {
  if (!isFormValid.value) return;

  const registrationData = {
    email: email.value,
    phone: phone.value,
    password: password.value
  };
  localStorage.setItem('driverRegistrationData', JSON.stringify(registrationData));

  router.push('/register/step2');
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

.custom-input {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
  background: white;
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input.with-icon {
  padding-left: 48px;
  padding-right: 48px;
}

.custom-input.with-toggle {
  padding-right: 48px;
}

.custom-input:focus {
  outline: none;
  border-color: #4CAF50;
}

.custom-input::placeholder {
  color: #999;
}

.success-icon,
.verified-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 24px;
  color: #4CAF50;
  z-index: 2;
}

.password-toggle {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #999;
  z-index: 2;
  cursor: pointer;
}

.input-wrapper.success .custom-input {
  border-color: #4CAF50;
}

.input-wrapper.error .custom-input {
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

.success-message {
  display: block;
  font-size: 12px;
  color: #4CAF50;
  margin-top: 6px;
  font-weight: 600;
}

.info-box {
  display: flex;
  gap: 12px;
  background: #E3F2FD;
  border-left: 4px solid #2196F3;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
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
}
</style>
