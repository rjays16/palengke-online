<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/login"></ion-back-button>
        </ion-buttons>
        <ion-title>Verify Code</ion-title>
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
          <h2 class="otp-title">Verify with Text Message</h2>
          <p class="otp-message">
            We sent a 6-digit authentication code to your registered mobile number
          </p>
          <p class="phone-number">+63 9**-***-**{{ phoneLastDigits }}</p>
          <p class="helper-text">
            Please enter the authentication code
          </p>
        </div>

        <!-- OTP Input Boxes -->
        <div class="otp-inputs">
          <input
            v-for="(digit, index) in otpDigits"
            :key="index"
            :ref="el => otpRefs[index] = el"
            v-model="otpDigits[index]"
            type="tel"
            maxlength="1"
            class="otp-box"
            :class="{ filled: otpDigits[index] }"
            @input="handleInput(index, $event)"
            @keydown="handleKeydown(index, $event)"
            @paste="handlePaste"
          />
        </div>

        <!-- Resend Code -->
        <div class="resend-section">
          <p v-if="countdown > 0" class="countdown-text">
            Resend code in {{ countdown }}s
          </p>
          <button v-else class="resend-button" @click="resendCode">
            Didn't get the code? <strong>Resend code</strong>
          </button>
        </div>

        <!-- Verify Button -->
        <ion-button
          expand="block"
          class="verify-button"
          :disabled="!isCodeComplete || isVerifying"
          @click="verifyCode"
        >
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
  toastController
} from '@ionic/vue';
import {
  mailOpenOutline,
  shieldCheckmarkOutline
} from 'ionicons/icons';

const router = useRouter();
const route = useRoute();

// Get phone from route params or use masked default
const phoneLastDigits = ref(route.params.phone || '890');

// OTP State
const otpDigits = ref(['', '', '', '', '', '']);
const otpRefs = ref([]);
const isVerifying = ref(false);
const countdown = ref(60);
let countdownInterval = null;

// Computed
const isCodeComplete = computed(() => {
  return otpDigits.value.every(digit => digit !== '');
});

const otpCode = computed(() => {
  return otpDigits.value.join('');
});

// Methods
const handleInput = (index, event) => {
  const value = event.target.value;
  
  // Only allow numbers
  if (!/^\d*$/.test(value)) {
    otpDigits.value[index] = '';
    return;
  }
  
  // Move to next input if digit entered
  if (value && index < 5) {
    otpRefs.value[index + 1]?.focus();
  }
};

const handleKeydown = (index, event) => {
  // Handle backspace
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    otpRefs.value[index - 1]?.focus();
  }
  
  // Handle arrow keys
  if (event.key === 'ArrowLeft' && index > 0) {
    otpRefs.value[index - 1]?.focus();
  }
  if (event.key === 'ArrowRight' && index < 5) {
    otpRefs.value[index + 1]?.focus();
  }
};

const handlePaste = (event) => {
  event.preventDefault();
  const pastedData = event.clipboardData.getData('text').slice(0, 6);
  
  if (!/^\d+$/.test(pastedData)) return;
  
  const digits = pastedData.split('');
  digits.forEach((digit, index) => {
    if (index < 6) {
      otpDigits.value[index] = digit;
    }
  });
  
  // Focus last filled or first empty box
  const nextEmpty = digits.length < 6 ? digits.length : 5;
  otpRefs.value[nextEmpty]?.focus();
};

const startCountdown = () => {
  countdown.value = 60;
  countdownInterval = setInterval(() => {
    if (countdown.value > 0) {
      countdown.value--;
    } else {
      clearInterval(countdownInterval);
    }
  }, 1000);
};

const resendCode = async () => {
  // TODO: Call API to resend OTP
  console.log('Resending OTP...');
  
  const toast = await toastController.create({
    message: '✅ Verification code sent!',
    duration: 2000,
    color: 'success',
    position: 'top'
  });
  await toast.present();
  
  // Clear current OTP
  otpDigits.value = ['', '', '', '', '', ''];
  otpRefs.value[0]?.focus();
  
  // Restart countdown
  startCountdown();
};

const verifyCode = async () => {
  if (!isCodeComplete.value) return;
  
  isVerifying.value = true;
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // TODO: Replace with actual OTP verification API
    console.log('Verifying OTP:', otpCode.value);
    
    // For demo: accept any 6-digit code
    // In production: verify with backend
    
    // Save authentication - User is now fully logged in!
    localStorage.setItem('isAuthenticated', 'true');
    localStorage.removeItem('pendingAuth'); // Clean up pending state
    
    // Show success modal
    const alert = await alertController.create({
      cssClass: 'success-modal',
      header: ' ',
      message: '✅ Verification successful!',
      buttons: ['OK']
    });
    
    await alert.present();
    
    // Wait for modal to close
    await alert.onDidDismiss();
    
    // Navigate to dashboard (not login!)
    router.push('/home');
    
  } catch (error) {
    const alert = await alertController.create({
      header: 'Verification Failed',
      message: 'Invalid code. Please try again.',
      buttons: ['OK']
    });
    await alert.present();
    
    // Clear OTP on error
    otpDigits.value = ['', '', '', '', '', ''];
    otpRefs.value[0]?.focus();
  } finally {
    isVerifying.value = false;
  }
};

// Lifecycle
onMounted(() => {
  // Focus first input
  setTimeout(() => {
    otpRefs.value[0]?.focus();
  }, 300);
  
  // Start countdown
  startCountdown();
});

onUnmounted(() => {
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
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
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

.phone-number {
  font-size: 18px;
  font-weight: 700;
  color: #4CAF50;
  margin: 0 0 16px 0;
}

.helper-text {
  font-size: 14px;
  color: #999;
  margin: 0;
}

/* OTP Input Boxes */
.otp-inputs {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-bottom: 32px;
}

.otp-box {
  width: 48px;
  height: 56px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 24px;
  font-weight: 700;
  text-align: center;
  color: #1a1a1a;
  background: white;
  transition: all 0.2s;
  font-family: inherit;
}

.otp-box:focus {
  outline: none;
  border-color: #4CAF50;
  box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
}

.otp-box.filled {
  border-color: #4CAF50;
  background: #E8F5E9;
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
  color: #4CAF50;
  font-weight: 600;
}

.resend-button:active {
  transform: scale(0.98);
}

/* Verify Button */
.verify-button {
  --background: #4CAF50;
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

/* Success Modal */
:deep(.success-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 320px;
  padding: 30px 20px;
}

:deep(.success-modal .alert-head) {
  padding: 0;
  min-height: 0;
}

:deep(.success-modal .alert-message) {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.success-modal .alert-button-group) {
  padding: 10px 0 0 0;
}

:deep(.success-modal button) {
  background: #4CAF50;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
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
  
  .otp-box {
    width: 42px;
    height: 50px;
    font-size: 20px;
  }
}

@media (max-width: 360px) {
  .otp-inputs {
    gap: 6px;
  }
  
  .otp-box {
    width: 38px;
    height: 46px;
    font-size: 18px;
  }
}
</style>