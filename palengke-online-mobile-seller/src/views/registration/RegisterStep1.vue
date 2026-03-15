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
                placeholder="juan@example.com"
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
              success: isPhoneValid && phone.length === 11,
              verified: isPhoneVerified 
            }">
              <ion-icon :icon="callOutline" class="input-icon"></ion-icon>
              <input 
                v-model="phone" 
                type="tel" 
                placeholder="09171234567"
                class="custom-input with-icon"
                maxlength="11"
                :disabled="isPhoneVerified"
                @input="onPhoneInput"
                @blur="validateField('phone')"
              >
              <ion-icon 
                v-if="isPhoneValid && phone.length === 11 && !isPhoneVerified" 
                :icon="checkmarkCircleOutline" 
                class="success-icon"
              ></ion-icon>
              <ion-icon 
                v-if="isPhoneVerified" 
                :icon="shieldCheckmarkOutline" 
                class="verified-icon"
              ></ion-icon>
            </div>
            <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
            <span v-else-if="isPhoneVerified" class="success-message"><font-awesome-icon icon="check" /> Phone number verified</span>
            <span v-else class="helper-text">11 digits starting with 09</span>
          </div>

          <!-- Send OTP Button (shows when phone is valid but OTP not sent) -->
          <ion-button 
            v-if="isPhoneValid && phone.length === 11 && !showOtpSection && !isPhoneVerified"
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
          <div v-if="showOtpSection && !isPhoneVerified" class="otp-section">
            <div class="otp-header">
              <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              <div class="otp-text">
                <h3>Verify Phone Number</h3>
                <p>Enter the 6-digit code sent to <strong>{{ formatPhone(phone) }}</strong></p>
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
              <button class="change-number-button" @click="changePhoneNumber">
                Change Number
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
              Your phone number will be verified with a one-time code to ensure account security.
            </div>
          </div>

          <!-- Next Button (only enabled when phone is verified) -->
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
  checkmarkCircleOutline,
  shieldCheckmarkOutline,
  paperPlaneOutline,
  informationCircleOutline,
  alertCircleOutline
} from 'ionicons/icons';

const router = useRouter();

// Form data
const email = ref('');
const phone = ref('');
const isPhoneVerified = ref(false);

// OTP data
const showOtpSection = ref(false);
const otpDigits = ref(['', '', '', '', '', '']);
const otpInputs = ref([]);
const otpError = ref('');
const resendTimer = ref(0);
const isSendingOtp = ref(false);
const isVerifyingOtp = ref(false);

// Errors
const errors = ref({
  email: '',
  phone: ''
});

// Validation
const isEmailValid = computed(() => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return email.value && emailRegex.test(email.value);
});

const isPhoneValid = computed(() => {
  return phone.value.startsWith('09') && phone.value.length === 11;
});

const otpValue = computed(() => {
  return otpDigits.value.join('');
});

const isFormValid = computed(() => {
  return isEmailValid.value && isPhoneVerified.value;
});

// Email input handler
const onEmailInput = () => {
  errors.value.email = '';
};

// Phone input handler
const onPhoneInput = () => {
  // Only allow numbers
  phone.value = phone.value.replace(/\D/g, '');
  errors.value.phone = '';
};

// Validate field
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
};

// Format phone for display
const formatPhone = (phoneNum) => {
  if (!phoneNum || phoneNum.length !== 11) return phoneNum;
  return `${phoneNum.slice(0, 4)} ${phoneNum.slice(4, 7)} ${phoneNum.slice(7)}`;
};

// Send OTP
const sendOtp = async () => {
  if (!validateField('phone')) return;

  isSendingOtp.value = true;

  try {
    // TODO: Replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Simulate sending OTP
    console.log('Sending OTP to:', phone.value);
    
    // Show success toast
    const toast = await toastController.create({
      message: `Verification code sent to ${formatPhone(phone.value)}`,
      duration: 3000,
      color: 'success',
      position: 'top'
    });
    await toast.present();

    // Show OTP section
    showOtpSection.value = true;
    
    // Start resend timer
    startResendTimer();
    
    // Focus first OTP input
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

// Resend OTP
const resendOtp = async () => {
  if (resendTimer.value > 0) return;
  
  // Clear OTP inputs
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  
  // Resend
  await sendOtp();
};

// Start resend timer
const startResendTimer = () => {
  resendTimer.value = 60;
  const interval = setInterval(() => {
    resendTimer.value--;
    if (resendTimer.value <= 0) {
      clearInterval(interval);
    }
  }, 1000);
};

// Change phone number
const changePhoneNumber = () => {
  showOtpSection.value = false;
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  isPhoneVerified.value = false;
  resendTimer.value = 0;
};

// OTP input handler
const onOtpInput = (index) => {
  // Only allow numbers
  otpDigits.value[index] = otpDigits.value[index].replace(/\D/g, '');
  
  // Clear error
  otpError.value = '';
  
  // Auto-focus next input
  if (otpDigits.value[index] && index < 5) {
    otpInputs.value[index + 1]?.focus();
  }
};

// OTP keydown handler
const onOtpKeydown = (event, index) => {
  // Backspace handling
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    otpInputs.value[index - 1]?.focus();
  }
};

// OTP paste handler
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

// Verify OTP
const verifyOtp = async () => {
  if (otpValue.value.length !== 6) return;

  isVerifyingOtp.value = true;
  otpError.value = '';

  try {
    // TODO: Replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Simulate OTP verification
    const isValid = true; // Replace with actual validation
    
    if (isValid) {
      isPhoneVerified.value = true;
      showOtpSection.value = false;
      
      const toast = await toastController.create({
        message: '<font-awesome-icon icon="check" /> Phone number verified successfully!',
        duration: 2500,
        color: 'success',
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

// Navigation
const goBack = () => {
  router.push('/welcome');
};

const handleNext = () => {
  if (!isFormValid.value) return;

  // Save data
  const registrationData = {
    email: email.value,
    phone: phone.value,
    phoneVerified: isPhoneVerified.value
  };
  localStorage.setItem('registrationData', JSON.stringify(registrationData));

  // Navigate to Step 2
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
  max-width: 500px;
  margin: 0 auto;
  width: 100%;
}

/* Input Group */
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

/* Success/Error Icons */
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

/* Input States */
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

/* Helper/Error/Success Text */
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

/* Responsive */
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