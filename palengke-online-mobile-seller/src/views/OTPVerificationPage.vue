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
          <p class="otp-message">
            We sent a 6-digit authentication code to your registered email address
          </p>
          <p class="email-address">{{ maskedEmail }}</p>
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
          <button v-else class="resend-button" @click="resendCode" :disabled="isVerifying">
            <span v-if="!isVerifying">Didn't get the code? <strong>Resend code</strong></span>
            <span v-else><ion-spinner name="crescent" color="success"></ion-spinner> Sending...</span>
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

// Get email from route params or localStorage
const userEmail = ref(route.params.email || localStorage.getItem('userEmail') || '');

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
  isVerifying.value = true;
  try {
    const res = await fetch('http://localhost:8000/api/send-otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: userEmail.value }),
    });

    if (!res.ok) {
      const data = await res.json();
      throw new Error(data.message || 'Failed to resend code');
    }

    const toast = await toastController.create({
      message: 'Verification code sent!',
      duration: 2000,
      color: 'success',
      position: 'top'
    });
    await toast.present();

    otpDigits.value = ['', '', '', '', '', ''];
    otpRefs.value[0]?.focus();

    startCountdown();
  } catch (e) {
    const toast = await toastController.create({
      message: e.message || 'Failed to resend code',
      duration: 2000,
      color: 'danger',
      position: 'top'
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
    const response = await fetch('http://localhost:8000/api/verify-otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: userEmail.value, otp: otpCode.value }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Invalid code.');
    }

    localStorage.removeItem('pendingAuth');

    // Generate device ID if not exists
    if (!localStorage.getItem('deviceId')) {
      const deviceId = 'device_' + Date.now() + '_' + Math.random().toString(36).substring(2, 10);
      localStorage.setItem('deviceId', deviceId);
    }

    await showSuccessModal('Verification successful!');

    // Check if this is a forgot-pin flow
    const mode = route.query.mode;
    if (mode === 'forgot-pin') {
      router.push('/create-pin');
      return;
    }

    // Check if seller already has a PIN
    const hasPin = localStorage.getItem('hasPin') === 'true';
    if (hasPin) {
      router.push('/pin-login');
    } else {
      router.push('/create-pin');
    }

  } catch (error) {
    const alert = await alertController.create({
      header: 'Verification Failed',
      message: error.message || 'Invalid code. Please try again.',
      buttons: ['OK']
    });
    await alert.present();

    otpDigits.value = ['', '', '', '', ''];
    otpRefs.value[0]?.focus();
  } finally {
    isVerifying.value = false;
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
  
  // Add the image after modal is presented
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      // Clear the text content
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      // Create and add the image
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      // Create and add the text
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      
      // Append both
      alertElement.appendChild(img);
      alertElement.appendChild(textDiv);
    }
  }, 50);
  
  // Wait for modal to close
  await alert.onDidDismiss();
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

.email-address {
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