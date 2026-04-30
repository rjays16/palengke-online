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
            <div class="input-wrapper" :class="{ error: errors.email, success: isEmailValid && !isEmailVerified, verified: isEmailVerified }">
              <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
              <input 
                v-model="email" 
                type="email" 
                placeholder="driver@example.com"
                class="custom-input with-icon"
                :disabled="isEmailVerified"
                @input="onEmailInput"
                @blur="validateField('email')"
              >
              <ion-icon 
                v-if="isEmailVerified" 
                :icon="checkmarkCircleOutline" 
                class="verified-icon"
              ></ion-icon>
            </div>
            <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
            <span v-else-if="isEmailVerified" class="verified-text">Email verified</span>
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
                :type="showConfirmPassword ? 'text' : 'password'" 
                placeholder="Confirm your password"
                class="custom-input with-icon with-toggle"
                @input="onConfirmPasswordInput"
                @blur="validateField('confirmPassword')"
              >
              <ion-icon 
                :icon="showConfirmPassword ? eyeOffOutline : eyeOutline" 
                class="password-toggle"
                @click="showConfirmPassword = !showConfirmPassword"
              ></ion-icon>
            </div>
            <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
            <span v-else-if="isConfirmPasswordValid" class="success-message">Passwords match</span>
            <span v-else class="helper-text">Re-enter your password</span>
          </div>

          <!-- Send OTP Button (shows when email is valid but OTP not sent) -->
          <ion-button 
            v-if="isEmailValid && !showOtpSection && !isEmailVerified && isPasswordValid && isConfirmPasswordValid"
            expand="block" 
            class="send-otp-button"
            :disabled="isSendingOtp"
            @click="sendOtp"
          >
            <ion-icon slot="start" :icon="paperPlaneOutline"></ion-icon>
            <span v-if="!isSendingOtp">Send Verification Code</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>

          <!-- OTP Section (shows after sending OTP) -->
          <div v-if="showOtpSection && !isEmailVerified" class="otp-section">
            <div class="otp-header">
              <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              <div class="otp-text">
                <h3>Verify Email Address</h3>
                <p>Enter the 6-digit code sent to <strong>{{ maskedEmail }}</strong></p>
              </div>
            </div>

            <div class="otp-inputs">
              <input 
                v-for="(digit, index) in otpDigits" 
                :key="index"
                :ref="el => otpInputs[index] = el"
                v-model="otpDigits[index]"
                type="tel"
                maxlength="1"
                class="otp-input"
                :class="{ error: otpError }"
                @input="onOtpInput(index)"
                @keydown="onOtpKeydown($event, index)"
                @paste="onOtpPaste"
              >
            </div>

            <div v-if="otpError" class="otp-error">
              <ion-icon :icon="alertCircleOutline"></ion-icon>
              <span>{{ otpError }}</span>
            </div>

            <div class="otp-actions">
              <button 
                class="resend-button" 
                :disabled="resendTimer > 0"
                @click="resendOtp"
              >
                {{ resendTimer > 0 ? `Resend in ${resendTimer}s` : 'Resend Code' }}
              </button>
              <button class="change-number-button" @click="changeEmail">
                Change Email
              </button>
            </div>

            <ion-button 
              expand="block" 
              class="verify-button"
              :disabled="otpValue.length !== 6 || isVerifyingOtp"
              @click="verifyOtp"
            >
              <span v-if="!isVerifyingOtp">Verify Code</span>
              <ion-spinner v-else name="crescent"></ion-spinner>
            </ion-button>
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
  IonSpinner,
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
  paperPlaneOutline,
  shieldCheckmarkOutline,
  informationCircleOutline,
  alertCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const email = ref('');
const phone = ref('');
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const isEmailVerified = ref(false);

const showOtpSection = ref(false);
const otpDigits = ref(['', '', '', '', '', '']);
const otpInputs = ref([]);
const otpError = ref('');
const resendTimer = ref(0);
const isSendingOtp = ref(false);
const isVerifyingOtp = ref(false);

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

const otpValue = computed(() => {
  return otpDigits.value.join('');
});

const maskedEmail = computed(() => {
  if (!email.value) return '';
  const atIndex = email.value.indexOf('@');
  if (atIndex === -1) return email.value;
  const localPart = email.value.substring(0, atIndex);
  const domain = email.value.substring(atIndex);
  if (localPart.length > 3) {
    return localPart.substring(0, 2) + '***' + localPart.substring(localPart.length - 1) + domain;
  }
  return '***' + domain;
});

const isFormValid = computed(() => {
  return isEmailValid.value && isEmailVerified.value && isPhoneValid.value && phone.value.length === 11 && isPasswordValid.value && isConfirmPasswordValid.value;
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

const sendOtp = async () => {
  if (!validateField('email')) return;

  isSendingOtp.value = true;

  try {
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    const toast = await toastController.create({
      message: `Verification code sent to ${maskedEmail.value}`,
      duration: 3000,
      color: 'medium',
      position: 'top'
    });
    await toast.present();

    showOtpSection.value = true;
    startResendTimer();
    
    setTimeout(() => {
      if (otpInputs.value[0]) {
        otpInputs.value[0].focus();
      }
    }, 300);

  } catch (error) {
    const toast = await toastController.create({
      message: 'Failed to send code. Please try again.',
      duration: 3000,
      color: 'danger',
      position: 'top'
    });
    await toast.present();
  } finally {
    isSendingOtp.value = false;
  }
};

const resendOtp = async () => {
  if (resendTimer.value > 0) return;
  
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  
  await sendOtp();
};

const startResendTimer = () => {
  resendTimer.value = 60;
  const interval = setInterval(() => {
    resendTimer.value--;
    if (resendTimer.value <= 0) {
      clearInterval(interval);
    }
  }, 1000);
};

const changeEmail = () => {
  showOtpSection.value = false;
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  isEmailVerified.value = false;
  resendTimer.value = 0;
};

const onOtpInput = (index) => {
  otpDigits.value[index] = otpDigits.value[index].replace(/\D/g, '');
  otpError.value = '';
  
  if (otpDigits.value[index] && index < 5) {
    otpInputs.value[index + 1]?.focus();
  }
};

const onOtpKeydown = (event, index) => {
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    otpInputs.value[index - 1]?.focus();
  }
};

const onOtpPaste = (event) => {
  event.preventDefault();
  const pastedData = event.clipboardData.getData('text').replace(/\D/g, '');
  
  if (pastedData.length === 6) {
    for (let i = 0; i < 6; i++) {
      otpDigits.value[i] = pastedData[i] || '';
    }
    otpInputs.value[5]?.focus();
  }
};

const verifyOtp = async () => {
  if (otpValue.value.length !== 6) return;

  isVerifyingOtp.value = true;
  otpError.value = '';

  try {
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    const isValid = true;
    
    if (isValid) {
      isEmailVerified.value = true;
      showOtpSection.value = false;
      
      const toast = await toastController.create({
        message: 'Email verified successfully!',
        duration: 2500,
        color: 'medium',
        position: 'top'
      });
      await toast.present();
    } else {
      otpError.value = 'Invalid code. Please try again.';
    }

  } catch (error) {
    otpError.value = 'Verification failed. Please try again.';
  } finally {
    isVerifyingOtp.value = false;
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

.custom-input:disabled {
  background: #f5f5f5;
  color: #999;
  cursor: not-allowed;
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

.verified-icon {
  animation: scaleIn 0.3s ease-out;
}

@keyframes scaleIn {
  0% { transform: translateY(-50%) scale(0); }
  50% { transform: translateY(-50%) scale(1.2); }
  100% { transform: translateY(-50%) scale(1); }
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

.input-wrapper.verified .custom-input {
  border-color: #4CAF50;
  background: #f1f8f4;
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

.verified-text {
  display: block;
  font-size: 12px;
  color: #4CAF50;
  margin-top: 6px;
  font-weight: 600;
}

/* Send OTP Button */
.send-otp-button {
  --background: #2196F3;
  --background-hover: #1976D2;
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
  margin-bottom: 20px;
}

/* OTP Section */
.otp-section {
  background: #E3F2FD;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 20px;
  border: 2px solid #2196F3;
}

.otp-header {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.otp-header ion-icon {
  font-size: 32px;
  color: #2196F3;
  flex-shrink: 0;
}

.otp-text h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1565C0;
  margin: 0 0 6px 0;
}

.otp-text p {
  font-size: 13px;
  color: #1976D2;
  margin: 0;
  line-height: 1.4;
}

.otp-text strong {
  font-weight: 700;
}

/* OTP Inputs */
.otp-inputs {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin-bottom: 16px;
}

.otp-input {
  width: 48px;
  height: 56px;
  border: 2px solid #2196F3;
  border-radius: 12px;
  text-align: center;
  font-size: 24px;
  font-weight: 700;
  color: #1a1a1a;
  background: white;
  transition: all 0.2s;
}

.otp-input:focus {
  outline: none;
  border-color: #1976D2;
  box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.otp-input.error {
  border-color: #f44336;
}

/* OTP Error */
.otp-error {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  background: #FFEBEE;
  border-radius: 8px;
  margin-bottom: 16px;
}

.otp-error ion-icon {
  font-size: 20px;
  color: #f44336;
  flex-shrink: 0;
}

.otp-error span {
  font-size: 13px;
  color: #c62828;
  font-weight: 500;
}

/* OTP Actions */
.otp-actions {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.resend-button,
.change-number-button {
  background: none;
  border: none;
  color: #2196F3;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 8px;
  font-family: inherit;
}

.resend-button:disabled {
  color: #999;
  cursor: not-allowed;
}

.change-number-button {
  color: #666;
}

/* Verify Button */
.verify-button {
  --background: #2196F3;
  --background-hover: #1976D2;
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.verify-button:disabled {
  --background: #e0e0e0;
  --color: #999;
}

/* Info Box */
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

/* Next Button */
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

  .otp-input {
    width: 44px;
    height: 52px;
    font-size: 22px;
  }
}
</style>
