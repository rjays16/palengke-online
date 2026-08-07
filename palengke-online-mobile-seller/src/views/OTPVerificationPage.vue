<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/login"></ion-back-button>
        </ion-buttons>
        <ion-title>Email Verification</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="otp-content">
      <div class="otp-container">
        <!-- Icon/Illustration -->
        <div class="icon-section">
          <div class="otp-icon">
            <ion-icon :icon="mailOpenOutline"></ion-icon>
          </div>
        </div>

        <!-- Title & Message -->
        <div class="text-section">
          <h2 class="otp-title">Verify with Email</h2>
          <p class="otp-message">We sent a 6-digit authentication code to your registered email address</p>
          <p class="email-address">{{ maskedEmail }}</p>
          <p class="helper-text">Please enter the authentication code</p>
        </div>

        <!-- OTP Input Boxes -->
        <OtpInput ref="otpInputRef" v-model="otpCode" :length="6" type="otp" @complete="onComplete" />

        <!-- Resend Code -->
        <div class="resend-section">
          <p v-if="countdown > 0" class="countdown-text">Resend code in {{ countdown }}s</p>
          <button v-else class="resend-button" @click="resendCode" :disabled="isVerifying">
            <span v-if="!isVerifying">Didn't get the code? <strong>Resend code</strong></span>
            <span v-else><ion-spinner name="crescent" color="success"></ion-spinner> Sending...</span>
          </button>
        </div>

        <!-- Verify Button -->
        <ion-button expand="block" class="verify-button" :disabled="!isCodeComplete || isVerifying" @click="verifyCode">
          <span v-if="!isVerifying">Verify</span>
          <ion-spinner v-else name="crescent"></ion-spinner>
        </ion-button>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonButton,
  IonIcon,
  IonSpinner,
  alertController,
  toastController,
} from '@ionic/vue';
import { mailOpenOutline } from 'ionicons/icons';
import { api, ApiError } from '@/utils/apiClient';
import { useAuthStore } from '@/stores/auth';
import { useSuccessModal } from '@/composables/useSuccessModal';
import OtpInput from '@/components/OtpInput.vue';

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();
const { showSuccessModal } = useSuccessModal();

// Get email from route params or store
const userEmail = ref(route.params.email || auth.userEmail || '');

// Computed to mask email for display
const maskedEmail = computed(() => {
  if (!userEmail.value || userEmail.value === 'your email') return userEmail.value;

  const email = userEmail.value;
  const atIndex = email.indexOf('@');

  if (atIndex === -1) return email;

  const localPart = email.substring(0, atIndex);
  const domain = email.substring(atIndex);

  // Mask local part: show first 2 chars + *** + last char
  if (localPart.length > 3) {
    return localPart.substring(0, 2) + '***' + localPart.substring(localPart.length - 1) + domain;
  } else if (localPart.length > 1) {
    return localPart.substring(0, 1) + '***' + domain;
  }

  return '***' + domain;
});

// OTP State
const otpInputRef = ref();
const otpCode = ref('');
const isVerifying = ref(false);
const countdown = ref(60);
let countdownInterval = null;
let isMounted = true;

// Computed
const isCodeComplete = computed(() => otpInputRef.value?.isComplete ?? false);

// Methods
const onComplete = (value) => {
  otpCode.value = value;
};

const startCountdown = (autoResend = false) => {
  countdown.value = 60;
  countdownInterval = setInterval(() => {
    if (countdown.value > 0) {
      countdown.value--;
    } else {
      clearInterval(countdownInterval);
      if (autoResend) resendCode();
    }
  }, 1000);
};

const resendCode = async () => {
  if (!isMounted) return;
  isVerifying.value = true;
  try {
    await api.post('/send-otp', { email: userEmail.value }, { skipAuth: true });

    const toast = await toastController.create({
      message: 'Verification code sent!',
      duration: 2000,
      color: 'success',
      position: 'top',
    });
    await toast.present();

    otpInputRef.value?.reset();

    startCountdown(true);
  } catch (e) {
    const message = e instanceof ApiError ? (e.data?.message || e.message) : e.message;
    const toast = await toastController.create({
      message: message || 'Failed to resend code',
      duration: 2000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  } finally {
    isVerifying.value = false;
  }
};

const verifyCode = async () => {
  if (!isCodeComplete.value) return;

  isVerifying.value = true;

  try {
    await api.post('/verify-otp', { email: userEmail.value, otp: otpCode.value }, { skipAuth: true });

    if (countdownInterval) clearInterval(countdownInterval);

    localStorage.removeItem('pendingAuth');

    auth.initFromStorage();

    await showSuccessModal({ message: 'Verification successful!', awaitDismiss: true });

    // Check if this is a forgot-pin flow
    const mode = route.query.mode;
    if (mode === 'forgot-pin') {
      router.push('/create-pin');
      return;
    }

    const hasPin = auth.hasPin;
    if (hasPin) {
      router.push('/pin-login');
    } else {
      router.push('/create-pin');
    }
  } catch (error) {
    const message = error instanceof ApiError ? (error.data?.message || error.message) : error.message;
    const alert = await alertController.create({
      header: 'Verification Failed',
      message: message || 'Invalid code. Please try again.',
      buttons: ['OK'],
    });
    await alert.present();

    otpInputRef.value?.reset();
  } finally {
    isVerifying.value = false;
  }
};


// Lifecycle
onMounted(() => {
  // Focus first input
  setTimeout(() => {
    otpInputRef.value?.focus();
  }, 300);

  // Start countdown
  startCountdown(true);
});

onUnmounted(() => {
  isMounted = false;
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }
});
</script>

<style scoped>
.otp-content {
  --background: #f8f9fa;
}

.otp-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  padding: 40px 24px;
}

/* Icon Section */
.icon-section {
  text-align: center;
  margin-bottom: 32px;
}

.otp-icon {
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(76, 175, 80, 0.3);
}

.otp-icon ion-icon {
  font-size: 48px;
  color: white;
}

/* Text Section */
.text-section {
  text-align: center;
  margin-bottom: 40px;
}

.otp-title {
  font-size: 24px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.otp-message {
  font-size: 15px;
  color: #666;
  margin: 0 0 12px 0;
  line-height: 1.5;
}

.email-address {
  font-size: 18px;
  font-weight: 700;
  color: #4caf50;
  margin: 0 0 16px 0;
}

.helper-text {
  font-size: 14px;
  color: #999;
  margin: 0;
}

/* Resend Section */
.resend-section {
  text-align: center;
  margin-bottom: 32px;
  min-height: 40px;
}

.countdown-text {
  font-size: 14px;
  color: #999;
  margin: 0;
}

.resend-button {
  background: none;
  border: none;
  color: #666;
  font-size: 14px;
  cursor: pointer;
  font-family: inherit;
  padding: 0;
}

.resend-button strong {
  color: #4caf50;
  font-weight: 600;
}

.resend-button:active {
  transform: scale(0.98);
}

/* Verify Button */
.verify-button {
  --background: #4caf50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
  max-width: 400px;
  margin: 0 auto;
}

.verify-button[disabled] {
  --background: #e0e0e0;
  --color: #999;
}

/* Responsive */
@media (max-width: 480px) {
  .otp-container {
    padding: 32px 20px;
  }

  .otp-icon {
    width: 80px;
    height: 80px;
  }

  .otp-icon ion-icon {
    font-size: 40px;
  }

  .otp-title {
    font-size: 20px;
  }
}
</style>
