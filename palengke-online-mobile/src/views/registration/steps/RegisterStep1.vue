<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">

        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Progress -->
        <div class="progress-section">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 16.66%"></div>
          </div>
          <p class="progress-text">Step 1 of 6</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Get Started</h1>
          <p class="subtitle">Enter your mobile number to continue</p>
        </div>

        <!-- Form -->
        <div class="form-section">

          <!-- Phone Input -->
          <div class="input-group">
            <label class="input-label">Mobile Number *</label>
            <div class="phone-wrapper" :class="{ error: errors.phone, success: isPhoneValid }">
              <div class="country-code">
                <span class="flag">🇵🇭</span>
                <span class="code">+63</span>
              </div>
              <div class="divider-line"></div>
              <input
                v-model="phone"
                type="tel"
                placeholder="9XX XXX XXXX"
                class="phone-input"
                maxlength="10"
                @input="onPhoneInput"
                @blur="validatePhone"
                :disabled="showOtpSection && !otpError"
              >
              <ion-icon
                v-if="isPhoneValid && !showOtpSection"
                :icon="checkmarkCircleOutline"
                class="success-icon"
              ></ion-icon>
            </div>
            <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
            <span v-else class="helper-text">We'll send a verification code to this number</span>
          </div>

          <!-- Send OTP Button -->
          <ion-button
            v-if="isPhoneValid && !showOtpSection"
            expand="block"
            class="send-otp-button"
            :disabled="isSendingOtp"
            @click="sendOtp"
          >
            <ion-icon slot="start" :icon="paperPlaneOutline"></ion-icon>
            <span v-if="!isSendingOtp">Send Verification Code</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>

          <!-- OTP Section -->
          <div v-if="showOtpSection" class="otp-section">
            <div class="otp-header">
              <ion-icon :icon="phonePortraitOutline"></ion-icon>
              <div>
                <h3>Enter verification code</h3>
                <p>Sent to <strong>+63 {{ formattedPhone }}</strong></p>
              </div>
            </div>

            <div class="otp-inputs">
              <input
                v-for="(digit, index) in otpDigits"
                :key="index"
                :ref="el => otpRefs[index] = el"
                v-model="otpDigits[index]"
                type="tel"
                maxlength="1"
                class="otp-input"
                :class="{ error: otpError, filled: otpDigits[index] }"
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
                class="resend-btn"
                :disabled="resendTimer > 0"
                @click="resendOtp"
              >
                {{ resendTimer > 0 ? `Resend code in ${resendTimer}s` : 'Resend code' }}
              </button>
              <button class="change-btn" @click="changePhone">Change number</button>
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

          <!-- Info box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div>
              <strong>Why your mobile number?</strong><br>
              It helps us keep your account secure and lets you receive order updates via SMS.
            </div>
          </div>

          <!-- Next (only enabled after OTP verified) -->
          <ion-button
            expand="block"
            class="next-button"
            :disabled="!isPhoneVerified"
            @click="handleNext"
          >
            Continue
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
  IonPage, IonContent, IonButton, IonIcon, IonSpinner,
  toastController
} from '@ionic/vue';
import {
  arrowBackOutline, arrowForwardOutline, checkmarkCircleOutline,
  paperPlaneOutline, phonePortraitOutline, alertCircleOutline,
  informationCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const phone = ref('');
const isPhoneVerified = ref(false);
const showOtpSection = ref(false);
const otpDigits = ref(['', '', '', '', '', '']);
const otpRefs = ref([]);
const otpError = ref('');
const resendTimer = ref(0);
const isSendingOtp = ref(false);
const isVerifyingOtp = ref(false);
const errors = ref({ phone: '' });

const isPhoneValid = computed(() => /^9\d{9}$/.test(phone.value));
const otpValue = computed(() => otpDigits.value.join(''));
const formattedPhone = computed(() => {
  const p = phone.value;
  if (p.length < 10) return p;
  return `${p.slice(0,3)} ${p.slice(3,6)} ${p.slice(6)}`;
});

const onPhoneInput = () => {
  phone.value = phone.value.replace(/\D/g, '');
  errors.value.phone = '';
};

const validatePhone = () => {
  if (!phone.value) { errors.value.phone = 'Mobile number is required'; return false; }
  if (!isPhoneValid.value) { errors.value.phone = 'Enter a valid 10-digit number starting with 9'; return false; }
  errors.value.phone = '';
  return true;
};

const sendOtp = async () => {
  if (!validatePhone()) return;
  isSendingOtp.value = true;
  try {
    await new Promise(r => setTimeout(r, 1200));
    showOtpSection.value = true;
    startResendTimer();
    setTimeout(() => otpRefs.value[0]?.focus(), 300);
    const toast = await toastController.create({
      message: `Code sent to +63 ${formattedPhone.value}`,
      duration: 3000, color: 'success', position: 'top'
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
  const t = setInterval(() => {
    resendTimer.value--;
    if (resendTimer.value <= 0) clearInterval(t);
  }, 1000);
};

const changePhone = () => {
  showOtpSection.value = false;
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  isPhoneVerified.value = false;
  resendTimer.value = 0;
};

const onOtpInput = (index) => {
  otpDigits.value[index] = otpDigits.value[index].replace(/\D/g, '');
  otpError.value = '';
  if (otpDigits.value[index] && index < 5) otpRefs.value[index + 1]?.focus();
};

const onOtpKeydown = (e, index) => {
  if (e.key === 'Backspace' && !otpDigits.value[index] && index > 0)
    otpRefs.value[index - 1]?.focus();
};

const onOtpPaste = (e) => {
  e.preventDefault();
  const data = e.clipboardData.getData('text').replace(/\D/g, '');
  if (data.length === 6) {
    for (let i = 0; i < 6; i++) otpDigits.value[i] = data[i] || '';
    otpRefs.value[5]?.focus();
  }
};

const verifyOtp = async () => {
  if (otpValue.value.length !== 6) return;
  isVerifyingOtp.value = true;
  otpError.value = '';
  try {
    await new Promise(r => setTimeout(r, 1200));
    // Simulate: any 6-digit code passes
    isPhoneVerified.value = true;
    showOtpSection.value = false;
    const toast = await toastController.create({
      message: 'Mobile number verified!', duration: 2000, color: 'success', position: 'top'
    });
    await toast.present();
  } catch {
    otpError.value = 'Invalid code. Please try again.';
  } finally {
    isVerifyingOtp.value = false;
  }
};

const goBack = () => router.push('/signup-welcome');

const handleNext = () => {
  if (!isPhoneVerified.value) return;
  const data = JSON.parse(localStorage.getItem('buyerRegistrationData') || '{}');
  data.phone = `+63${phone.value}`;
  data.phoneVerified = true;
  localStorage.setItem('buyerRegistrationData', JSON.stringify(data));
  router.push('/register/step2');
};
</script>

<style scoped>
.step-content { --background: #ffffff; }
.step-container { min-height: 100vh; padding: 24px; padding-bottom: 100px; position: relative; }

.back-button {
  position: absolute; top: 24px; left: 24px;
  width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
  background: #f5f5f5; border-radius: 12px; cursor: pointer; z-index: 10;
}
.back-button:active { transform: scale(0.95); }
.back-button ion-icon { font-size: 24px; color: #1a1a1a; }

.progress-section { margin-top: 70px; margin-bottom: 32px; }
.progress-bar { width: 100%; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; margin-bottom: 8px; }
.progress-fill { height: 100%; background: #4CAF50; transition: width 0.3s ease; }
.progress-text { text-align: center; font-size: 13px; color: #666; margin: 0; font-weight: 600; }

.title-section { text-align: center; margin-bottom: 40px; }
.title { font-size: 28px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.subtitle { font-size: 15px; color: #666; margin: 0; }

.form-section { max-width: 480px; margin: 0 auto; width: 100%; }

.input-group { margin-bottom: 20px; }
.input-label { display: block; font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px; }

.phone-wrapper {
  display: flex; align-items: center;
  border: 2px solid #e0e0e0; border-radius: 12px; overflow: hidden;
  background: white; transition: border-color 0.2s; position: relative;
}
.phone-wrapper:focus-within { border-color: #4CAF50; }
.phone-wrapper.success { border-color: #4CAF50; }
.phone-wrapper.error { border-color: #f44336; }

.country-code {
  display: flex; align-items: center; gap: 6px;
  padding: 14px 12px; background: #f8f8f8; flex-shrink: 0;
}
.flag { font-size: 18px; }
.code { font-size: 15px; font-weight: 600; color: #1a1a1a; }
.divider-line { width: 1px; height: 28px; background: #e0e0e0; flex-shrink: 0; }

.phone-input {
  flex: 1; padding: 14px 12px; border: none; outline: none;
  font-size: 18px; font-weight: 600; background: transparent; letter-spacing: 2px;
  color: #1a1a1a; font-family: inherit;
}
.phone-input::placeholder { color: #bbb; font-weight: 400; letter-spacing: 0; font-size: 15px; }

.success-icon { font-size: 24px; color: #4CAF50; margin-right: 12px; flex-shrink: 0; }

.helper-text { display: block; font-size: 12px; color: #999; margin-top: 6px; }
.error-message { display: block; font-size: 12px; color: #f44336; margin-top: 6px; font-weight: 500; }

.send-otp-button {
  --background: #4CAF50; --background-hover: #45a049;
  --border-radius: 12px; height: 52px; font-size: 15px;
  font-weight: 600; text-transform: none; margin-bottom: 20px;
}

/* OTP Section */
.otp-section {
  background: #f0faf2; border-radius: 16px; padding: 24px;
  margin-bottom: 20px; border: 2px solid #4CAF50;
}
.otp-header { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 20px; }
.otp-header ion-icon { font-size: 28px; color: #4CAF50; flex-shrink: 0; margin-top: 2px; }
.otp-header h3 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.otp-header p { font-size: 13px; color: #555; margin: 0; }

.otp-inputs { display: flex; gap: 10px; justify-content: center; margin-bottom: 16px; }
.otp-input {
  width: 48px; height: 56px; border: 2px solid #e0e0e0; border-radius: 12px;
  text-align: center; font-size: 24px; font-weight: 700; color: #1a1a1a;
  background: white; transition: all 0.2s; font-family: inherit;
}
.otp-input:focus { outline: none; border-color: #4CAF50; box-shadow: 0 0 0 3px rgba(76,175,80,0.15); }
.otp-input.filled { border-color: #4CAF50; background: #f0faf2; }
.otp-input.error { border-color: #f44336; }

.otp-error {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 12px; background: #fff0f0; border-radius: 8px; margin-bottom: 12px;
}
.otp-error ion-icon { font-size: 18px; color: #f44336; flex-shrink: 0; }
.otp-error span { font-size: 13px; color: #c62828; font-weight: 500; }

.otp-actions { display: flex; justify-content: space-between; margin-bottom: 16px; }
.resend-btn, .change-btn {
  background: none; border: none; font-size: 13px; font-weight: 600;
  cursor: pointer; padding: 6px; font-family: inherit;
}
.resend-btn { color: #4CAF50; }
.resend-btn:disabled { color: #999; cursor: not-allowed; }
.change-btn { color: #888; }

.verify-button {
  --background: #4CAF50; --background-hover: #45a049;
  --border-radius: 12px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none;
}

.info-box {
  display: flex; gap: 12px; background: #E8F5E9; border-left: 4px solid #4CAF50;
  padding: 16px; border-radius: 8px; margin-bottom: 24px;
  font-size: 13px; color: #2E7D32; line-height: 1.5;
}
.info-box ion-icon { font-size: 22px; color: #4CAF50; flex-shrink: 0; margin-top: 2px; }
.info-box strong { font-weight: 700; }

.next-button {
  --background: #4CAF50; --background-hover: #45a049;
  --border-radius: 12px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none;
}
.next-button:disabled { --background: #e0e0e0; --color: #999; }

@media (max-width: 480px) {
  .title { font-size: 24px; }
  .step-container { padding: 20px 16px 100px 16px; }
  .otp-input { width: 44px; height: 52px; font-size: 20px; }
  .otp-inputs { gap: 8px; }
}
</style>