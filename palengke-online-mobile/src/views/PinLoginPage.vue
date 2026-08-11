<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/login"></ion-back-button>
        </ion-buttons>
        <ion-title>Device PIN</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="pin-content">
      <div class="pin-container">
        <div class="icon-section">
          <div class="pin-icon">
            <ion-icon :icon="lockClosedOutline"></ion-icon>
          </div>
        </div>

        <div class="text-section">
          <h2 class="pin-title">Enter Your PIN</h2>
          <p class="pin-message">Enter your device PIN to sign in quickly</p>
          <p class="email-address">{{ maskedEmail }}</p>
        </div>

        <div class="pin-inputs">
          <input
            v-for="(digit, index) in pinDigits"
            :key="index"
            :ref="(el) => (pinRefs[index] = el)"
            v-model="pinDigits[index]"
            type="tel"
            maxlength="1"
            class="pin-box"
            :class="{ filled: pinDigits[index] }"
            @input="handleInput(index, $event)"
            @keydown="handleKeydown(index, $event)"
          />
        </div>

        <div class="options-section">
          <button class="switch-account-btn" @click="goToLogin">Sign in with different account</button>
          <button class="forgot-pin-btn" @click="handleForgotPin">Forgot PIN?</button>
        </div>

        <ion-button expand="block" class="verify-button" :disabled="!isCodeComplete || isVerifying" @click="verifyPin">
          <span v-if="!isVerifying">Sign In</span>
          <ion-spinner v-else name="crescent"></ion-spinner>
        </ion-button>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
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
} from '@ionic/vue';
import { lockClosedOutline } from 'ionicons/icons';
import { useModals } from '@/components/modals/useModals';
import { API_BASE } from '@/services/api';

const router = useRouter();
const { showLoadingModal } = useModals();

const userEmail = ref(localStorage.getItem('userEmail') || '');
const deviceId = ref(localStorage.getItem('deviceId') || '');

const maskedEmail = computed(() => {
  if (!userEmail.value) return '';
  const email = userEmail.value;
  const atIndex = email.indexOf('@');
  if (atIndex === -1) return email;
  const localPart = email.substring(0, atIndex);
  const domain = email.substring(atIndex);
  if (localPart.length > 3) {
    return localPart.substring(0, 2) + '***' + localPart.substring(localPart.length - 1) + domain;
  } else if (localPart.length > 1) {
    return localPart.substring(0, 1) + '***' + domain;
  }
  return '***' + domain;
});

const pinDigits = ref(['', '', '', '', '']);
const pinRefs = ref([]);
const isVerifying = ref(false);

const isCodeComplete = computed(() => {
  return pinDigits.value.every((digit) => digit !== '');
});

const pinCode = computed(() => {
  return pinDigits.value.join('');
});

const handleInput = (index, event) => {
  const value = event.target.value;
  if (!/^\d*$/.test(value)) {
    pinDigits.value[index] = '';
    return;
  }
  if (value && index < pinDigits.value.length - 1) {
    pinRefs.value[index + 1]?.focus();
  }
};

const handleKeydown = (index, event) => {
  if (event.key === 'Backspace' && !pinDigits.value[index] && index > 0) {
    pinRefs.value[index - 1]?.focus();
  }
  if (event.key === 'ArrowLeft' && index > 0) {
    pinRefs.value[index - 1]?.focus();
  }
  if (event.key === 'ArrowRight' && index < pinDigits.value.length - 1) {
    pinRefs.value[index + 1]?.focus();
  }
};

const verifyPin = async () => {
  if (!isCodeComplete.value) return;
  isVerifying.value = true;

  try {
    const response = await fetch(`${API_BASE}/api/customer/device/verify-pin`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: userEmail.value,
        device_id: deviceId.value,
        pin: pinCode.value,
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Invalid PIN.');
    }

    localStorage.setItem('customer_token', data.token);
    localStorage.setItem('isAuthenticated', 'true');

    router.push('/home');
  } catch (error) {
    if (error.message && error.message.includes('No PIN set')) {
      localStorage.removeItem('hasPin');
      router.push('/login');
      return;
    }
    const alert = await alertController.create({
      header: 'Sign In Failed',
      message: error.message || 'Invalid PIN. Please try again.',
      buttons: ['OK'],
    });
    await alert.present();
    pinDigits.value = ['', '', '', '', ''];
    pinRefs.value[0]?.focus();
  } finally {
    isVerifying.value = false;
  }
};

const goToLogin = () => {
  localStorage.removeItem('isAuthenticated');
  localStorage.removeItem('customer_token');
  localStorage.removeItem('hasPin');
  localStorage.removeItem('deviceId');
  localStorage.removeItem('userEmail');
  router.push('/login');
};

const handleForgotPin = async () => {
  const alert = await alertController.create({
    header: 'Forgot PIN',
    message: 'A verification code will be sent to your registered email to reset your PIN.',
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Send Code',
        handler: async () => {
          const loading = await showLoadingModal('Sending verification code...');

          try {
            const res = await fetch(`${API_BASE}/api/customer/forgot-pin`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email: userEmail.value }),
            });

            const data = await res.json();

            await loading.dismiss();

            if (!res.ok) {
              throw new Error(data.message || 'Failed to send code.');
            }

            router.push({
              name: 'OTPVerification',
              params: { email: userEmail.value },
              query: { mode: 'forgot-pin' },
            });
          } catch (e) {
            await loading.dismiss();
            const errAlert = await alertController.create({
              header: 'Error',
              message: e.message || 'Failed to send verification code.',
              buttons: ['OK'],
            });
            await errAlert.present();
          }
        },
      },
    ],
  });
  await alert.present();
};

onMounted(async () => {
  if (!localStorage.getItem('deviceId')) {
    const newId = 'device_' + Date.now() + '_' + Math.random().toString(36).substring(2, 10);
    localStorage.setItem('deviceId', newId);
    deviceId.value = newId;
  }
  if (!userEmail.value) {
    router.replace('/login');
    return;
  }
  try {
    const res = await fetch(`${API_BASE}/api/customer/device/status`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: userEmail.value, device_id: deviceId.value }),
    });
    const data = await res.json();
    if (!data.hasPin) {
      localStorage.removeItem('hasPin');
      router.replace('/login');
      return;
    }
  } catch {
    if (!localStorage.getItem('hasPin')) {
      router.replace('/login');
      return;
    }
  }
  setTimeout(() => {
    pinRefs.value[0]?.focus();
  }, 300);
});
</script>

<style scoped>
.pin-content {
  --background: #f8f9fa;
}

.pin-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  padding: 40px 24px;
}

.icon-section {
  text-align: center;
  margin-bottom: 32px;
}

.pin-icon {
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(76, 175, 80, 0.3);
}

.pin-icon ion-icon {
  font-size: 48px;
  color: white;
}

.text-section {
  text-align: center;
  margin-bottom: 40px;
}

.pin-title {
  font-size: 24px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.pin-message {
  font-size: 15px;
  color: #666;
  margin: 0 0 12px 0;
  line-height: 1.5;
}

.email-address {
  font-size: 18px;
  font-weight: 700;
  color: #4caf50;
  margin: 0;
}

.pin-inputs {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-bottom: 32px;
}

.pin-box {
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

.pin-box:focus {
  outline: none;
  border-color: #4caf50;
  box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
}

.pin-box.filled {
  border-color: #4caf50;
  background: #e8f5e9;
}

.options-section {
  text-align: center;
  margin-bottom: 32px;
  min-height: 40px;
}

.switch-account-btn {
  background: none;
  border: none;
  color: #666;
  font-size: 14px;
  cursor: pointer;
  font-family: inherit;
  padding: 0;
}

.switch-account-btn:active {
  transform: scale(0.98);
}

.forgot-pin-btn {
  background: none;
  border: none;
  color: #4caf50;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  padding: 0;
  margin-top: 12px;
  display: block;
  width: 100%;
}

.forgot-pin-btn:active {
  transform: scale(0.98);
}

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

@media (max-width: 480px) {
  .pin-container {
    padding: 32px 20px;
  }
  .pin-icon {
    width: 80px;
    height: 80px;
  }
  .pin-icon ion-icon {
    font-size: 40px;
  }
  .pin-title {
    font-size: 20px;
  }
  .pin-box {
    width: 42px;
    height: 50px;
    font-size: 20px;
  }
}

@media (max-width: 360px) {
  .pin-inputs {
    gap: 6px;
  }
  .pin-box {
    width: 38px;
    height: 46px;
    font-size: 18px;
  }
}

/* Loading Modal Styling */
:deep(.loading-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 280px;
  padding: 50px 30px;
  background: white;
}

:deep(.loading-modal .alert-message) {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}
</style>
