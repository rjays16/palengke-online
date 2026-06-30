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
            <div
              class="input-wrapper"
              :class="{ error: errors.email, success: isEmailValid && !isEmailVerified, verified: isEmailVerified }"
            >
              <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
              <input
                v-model="email"
                type="email"
                placeholder="you@example.com"
                class="custom-input with-icon"
                :disabled="isEmailVerified"
                autocomplete="off"
                @input="onEmailInput"
                @blur="onEmailBlur"
              />
              <ion-icon v-if="isEmailVerified" :icon="checkmarkCircleOutline" class="verified-icon"></ion-icon>
            </div>
            <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
            <span v-else-if="emailExistsError" class="error-message">{{ emailExistsError }}</span>
            <span v-else-if="isCheckingEmail" class="helper-text">Checking email...</span>
            <span v-else-if="isEmailVerified" class="verified-text">Email verified</span>
            <span v-else class="helper-text">We'll send verification to this email</span>
          </div>

          <!-- Phone Number with Country Code -->
          <div class="input-group">
            <label class="input-label">Phone Number *</label>
            <div class="phone-input-row">
              <!-- Country Code Dropdown -->
              <div class="country-code-wrapper">
                <template v-if="isLoadingCountries">
                  <div class="country-code-select loading-placeholder">
                    <ion-spinner name="crescent" class="loading-spinner"></ion-spinner>
                  </div>
                </template>
                <template v-else>
                  <select v-model="countryCode" class="country-code-select">
                    <option v-for="c in countryCodes" :key="c.code" :value="c.code">
                      {{ c.code }} {{ c.abbr || c.country.substring(0, 2).toUpperCase() }}
                    </option>
                  </select>
                </template>
              </div>
              <!-- Phone Number Input -->
              <div
                class="phone-number-wrapper"
                :class="{
                  error: errors.phone,
                  success: isPhoneValid,
                }"
              >
                <input
                  v-model="phone"
                  type="tel"
                  :placeholder="phonePlaceholder"
                  class="phone-input"
                  :maxlength="maxPhoneLength"
                  @input="onPhoneInput"
                  @blur="validateField('phone')"
                />
                <ion-icon v-if="isPhoneValid" :icon="checkmarkCircleOutline" class="success-icon"></ion-icon>
              </div>
            </div>
            <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
            <span v-else class="helper-text">{{ phoneHelperText }}</span>
          </div>



          <!-- Send OTP Button (shows when email is valid but OTP not sent) -->
          <ion-button
            v-if="isEmailValid && !showOtpSection && !isEmailVerified && !emailExistsError"
            expand="block"
            class="send-otp-button"
            :disabled="isSendingOtp || isCheckingEmail"
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
                <p>
                  Enter the 6-digit code sent to <strong>{{ maskedEmail }}</strong>
                </p>
              </div>
            </div>

            <div class="otp-inputs">
              <input
                v-for="(digit, index) in otpDigits"
                :key="index"
                :ref="(el) => (otpInputs[index] = el)"
                v-model="otpDigits[index]"
                type="tel"
                maxlength="1"
                class="otp-input"
                :class="{ error: otpError }"
                @input="onOtpInput(index)"
                @keydown="onOtpKeydown($event, index)"
                @paste="onOtpPaste"
              />
            </div>

            <div v-if="otpError" class="otp-error">
              <ion-icon :icon="alertCircleOutline"></ion-icon>
              <span>{{ otpError }}</span>
            </div>

            <div class="otp-actions">
              <button class="resend-button" :disabled="resendTimer > 0" @click="resendOtp">
                {{ resendTimer > 0 ? `Resend in ${resendTimer}s` : 'Resend Code' }}
              </button>
              <button class="change-number-button" @click="changeEmail">Change Email</button>
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
              <strong>Why we need this:</strong><br />
              Your email address will be verified with a one-time code to ensure account security.
            </div>
          </div>

          <!-- Next Button (only enabled when email is verified) -->
          <ion-button expand="block" class="next-button" @click="handleNext" :disabled="!isFormValid">
            Next
            <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
          </ion-button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { API_BASE } from '@/config/api';
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, IonSpinner, toastController } from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  mailOutline,
  callOutline,
  checkmarkCircleOutline,
  shieldCheckmarkOutline,
  paperPlaneOutline,
  informationCircleOutline,
  alertCircleOutline,
} from 'ionicons/icons';

const router = useRouter();

const countryCodes = ref([]);
const isLoadingCountries = ref(true);

const countryCode = ref('+63');

onMounted(async () => {
  try {
    const res = await fetch(`${API_BASE}/countries`);
    const data = await res.json();
    countryCodes.value = data.countries.map(c => ({
      code: c.dial_code,
      country: c.name,
      abbr: c.code,
      digits: c.digit_count,
    }));
    if (countryCodes.value.length > 0) {
      const ph = countryCodes.value.find(c => c.code === '+63');
      if (ph) countryCode.value = '+63';
    }
  } catch {
    countryCodes.value = [];
  } finally {
    isLoadingCountries.value = false;
  }
});

// Form data
const email = ref('');
const phone = ref('');
const isEmailVerified = ref(false);
const isCheckingEmail = ref(false);
const emailExistsError = ref('');

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
  phone: '',
});

// Validation
const isEmailValid = computed(() => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return email.value && emailRegex.test(email.value);
});

const currentCountry = computed(() => {
  return countryCodes.value.find((c) => c.code === countryCode.value) || countryCodes.value[0];
});

const maxPhoneLength = computed(() => {
  return currentCountry.value.digits;
});

const phonePlaceholder = computed(() => {
  const digits = currentCountry.value.digits;
  if (countryCode.value === '+63') return '9171234567';
  return '0'.repeat(digits);
});

const phoneHelperText = computed(() => {
  return `${currentCountry.value.digits} digits for ${currentCountry.value.country}`;
});

const isPhoneValid = computed(() => {
  return phone.value.length === currentCountry.value.digits && /^\d+$/.test(phone.value);
});

const otpValue = computed(() => {
  return otpDigits.value.join('');
});

const isFormValid = computed(() => {
  return (
    isEmailValid.value &&
    isEmailVerified.value &&
    !emailExistsError.value &&
    isPhoneValid.value &&
    phone.value.length === currentCountry.value.digits
  );
});

// Check if email already exists in the system
const checkEmailExists = async () => {
  if (!isEmailValid.value || isEmailVerified.value) {
    emailExistsError.value = '';
    return;
  }

  isCheckingEmail.value = true;
  emailExistsError.value = '';

  try {
    const response = await fetch(`${API_BASE}/seller/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value }),
    });

    if (response.status === 404) {
      emailExistsError.value = '';
    } else if (response.status === 200) {
      emailExistsError.value = 'This email is already registered.';
    } else {
      const data = await response.json();
      if (data.status === 'rejected') {
        emailExistsError.value = '';
      } else {
        emailExistsError.value = 'This email is already registered.';
      }
    }
  } catch {
    emailExistsError.value = '';
  } finally {
    isCheckingEmail.value = false;
  }
};

// Email blur handler
const onEmailBlur = () => {
  validateField('email');
  if (isEmailValid.value && !isEmailVerified.value) {
    checkEmailExists();
  }
};

// Email input handler
const onEmailInput = () => {
  errors.value.email = '';
  emailExistsError.value = '';
};

// Phone input handler
const onPhoneInput = () => {
  // Only allow numbers
  phone.value = phone.value.replace(/\D/g, '');
  // Limit to max digits
  if (phone.value.length > currentCountry.value.digits) {
    phone.value = phone.value.slice(0, currentCountry.value.digits);
  }
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
    if (phone.value.length !== currentCountry.value.digits) {
      errors.value.phone = `Phone must be ${currentCountry.value.digits} digits`;
      return false;
    }
    if (!/^\d+$/.test(phone.value)) {
      errors.value.phone = 'Phone must contain only numbers';
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

// Mask email for display
const maskedEmail = computed(() => {
  if (!email.value) return '';

  const atIndex = email.value.indexOf('@');
  if (atIndex === -1) return email.value;

  const localPart = email.value.substring(0, atIndex);
  const domain = email.value.substring(atIndex);

  if (localPart.length > 3) {
    return localPart.substring(0, 2) + '***' + localPart.substring(localPart.length - 1) + domain;
  } else if (localPart.length > 1) {
    return localPart.substring(0, 1) + '***' + domain;
  }

  return '***' + domain;
});

// Send OTP to email
const sendOtp = async () => {
  if (!validateField('email')) return;

  isSendingOtp.value = true;

  try {
    const response = await fetch(`${API_BASE}/send-otp`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Failed to send code.');
    }

    const toast = await toastController.create({
      message: `Verification code sent to ${maskedEmail.value}`,
      duration: 3000,
      color: 'success',
      position: 'top',
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
      message: error.message || 'Failed to send code. Please try again.',
      duration: 3000,
      color: 'danger',
      position: 'top',
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

// Change email
const changeEmail = () => {
  showOtpSection.value = false;
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  isEmailVerified.value = false;
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
    const response = await fetch(`${API_BASE}/verify-otp`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value, otp: otpValue.value }),
    });

    const data = await response.json();

    if (!response.ok) {
      otpError.value = data.message || 'Invalid code. Please try again.';
      return;
    }

    isEmailVerified.value = true;
    showOtpSection.value = false;

    const toast = await toastController.create({
      message: 'Email verified successfully!',
      duration: 2500,
      color: 'success',
      position: 'top',
    });
    await toast.present();
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
    countryCode: countryCode.value,
    phone: phone.value,
    fullPhone: `${countryCode.value}${phone.value}`,
    emailVerified: isEmailVerified.value,
  };
  localStorage.setItem('registrationData', JSON.stringify(registrationData));

  // Navigate to Step 2
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
  top: 50%;
  transform: translateY(-50%);
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
  padding-right: 48px;
}

.custom-input.with-toggle {
  padding-right: 48px;
}

.custom-input:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.custom-input:disabled {
  background: var(--color-input-disabled-bg);
  color: var(--color-text-tertiary);
  cursor: not-allowed;
}

.custom-input::placeholder {
  color: var(--color-input-placeholder);
}

/* Phone Input Row with Country Code */
.phone-input-row {
  display: flex;
  gap: 8px;
}

.country-code-wrapper {
  flex-shrink: 0;
}

.country-code-select {
  padding: 14px 36px 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  font-family: inherit;
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  cursor: pointer;
  min-width: 110px;
  font-weight: 500;
}

.country-code-select:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

/* On mobile, the native select picker will be scrollable automatically */
.country-code-select option {
  font-size: 16px;
  padding: 10px;
}

.loading-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text-tertiary);
  font-size: 14px;
}

.loading-spinner {
  width: 20px;
  height: 20px;
}

.phone-number-wrapper {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.phone-number-wrapper.error .phone-input {
  border-color: var(--color-error);
}

.phone-number-wrapper.success .phone-input {
  border-color: var(--color-success);
}

.phone-input {
  width: 100%;
  padding: 14px 48px 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
}

.phone-input:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.phone-input::placeholder {
  color: var(--color-input-placeholder);
}

/* Success/Error Icons */
.success-icon,
.verified-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 24px;
  color: var(--color-success);
  z-index: 2;
}

.verified-icon {
  animation: scaleIn 0.3s ease-out;
}

@keyframes scaleIn {
  0% {
    transform: translateY(-50%) scale(0);
  }
  50% {
    transform: translateY(-50%) scale(1.2);
  }
  100% {
    transform: translateY(-50%) scale(1);
  }
}

/* Input States */
.input-wrapper.success .custom-input {
  border-color: var(--color-success);
}

.input-wrapper.verified .custom-input {
  border-color: var(--color-success);
  background: var(--color-success-bg);
}

.input-wrapper.error .custom-input {
  border-color: var(--color-error);
}

.input-wrapper.error .input-icon {
  color: var(--color-error);
}

/* Helper/Error/Success Text */
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

.success-message {
  display: block;
  font-size: 12px;
  color: var(--color-success);
  margin-top: 6px;
  font-weight: 600;
}

/* Send OTP Button */
.send-otp-button {
  --background: var(--color-info);
  --background-hover: var(--color-info);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
  margin-bottom: 20px;
}

/* OTP Section */
.otp-section {
  background: var(--color-otp-bg);
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 20px;
  border: 2px solid var(--color-otp-border);
}

.otp-header {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.otp-header ion-icon {
  font-size: 32px;
  color: var(--color-otp-border);
  flex-shrink: 0;
}

.otp-text h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-otp-text);
  margin: 0 0 6px 0;
}

.otp-text p {
  font-size: 13px;
  color: var(--color-otp-text);
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
  border: 2px solid var(--color-info);
  border-radius: 12px;
  text-align: center;
  font-size: 24px;
  font-weight: 700;
  color: var(--color-text-primary);
  background: var(--color-input-bg);
  transition: all 0.2s;
}

.otp-input:focus {
  outline: none;
  border-color: var(--color-info);
  box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.2);
}

.otp-input.error {
  border-color: var(--color-error);
}

/* OTP Error */
.otp-error {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  background: var(--color-error-bg);
  border-radius: 8px;
  margin-bottom: 16px;
}

.otp-error ion-icon {
  font-size: 20px;
  color: var(--color-error);
  flex-shrink: 0;
}

.otp-error span {
  font-size: 13px;
  color: var(--color-error-text);
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
  color: var(--color-info);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 8px;
  font-family: inherit;
}

.resend-button:disabled {
  color: var(--color-text-tertiary);
  cursor: not-allowed;
}

.change-number-button {
  color: var(--color-text-secondary);
}

/* Verify Button */
.verify-button {
  --background: var(--color-info);
  --background-hover: var(--color-info);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.verify-button:disabled {
  --background: var(--color-border);
  --color: var(--color-text-tertiary);
}

/* Info Box */
.info-box {
  display: flex;
  gap: 12px;
  background: var(--color-info-bg);
  border-left: 4px solid var(--color-info-border);
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
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

/* Next Button */
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

  .otp-input {
    width: 44px;
    height: 52px;
    font-size: 22px;
  }
}
</style>
