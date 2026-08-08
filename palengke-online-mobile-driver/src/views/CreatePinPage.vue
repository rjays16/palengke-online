<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/login"></ion-back-button>
        </ion-buttons>
        <ion-title>Create Device PIN</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="pin-content">
      <div class="pin-container">
        <div class="icon-section">
          <div class="pin-icon">
            <ion-icon :icon="step === 'create' ? keyOutline : shieldCheckmarkOutline"></ion-icon>
          </div>
        </div>

        <div class="text-section">
          <h2 class="pin-title" v-if="step === 'create'">Create Your PIN</h2>
          <h2 class="pin-title" v-else>Confirm Your PIN</h2>
          <p class="pin-message" v-if="step === 'create'">Set a 5-digit PIN for quick access on this device</p>
          <p class="pin-message" v-else>Re-enter your PIN to confirm</p>
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

        <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>

        <ion-button expand="block" class="verify-button" :disabled="!isCodeComplete || isSaving" @click="handleNext">
          <span v-if="!isSaving">{{ step === 'create' ? 'Next' : 'Confirm' }}</span>
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
import { keyOutline, shieldCheckmarkOutline } from 'ionicons/icons';

const router = useRouter();
import { API_BASE } from '@/services/api';

const step = ref('create');
const pinDigits = ref(['', '', '', '', '']);
const savedPin = ref('');
const pinRefs = ref([]);
const isSaving = ref(false);
const errorMessage = ref('');

const userEmail = ref(localStorage.getItem('userEmail') || '');
const deviceId = ref(localStorage.getItem('deviceId') || '');

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
  errorMessage.value = '';
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

const handleNext = async () => {
  if (!isCodeComplete.value) return;

  if (step.value === 'create') {
    savedPin.value = pinCode.value;
    pinDigits.value = ['', '', '', '', ''];
    step.value = 'confirm';
    errorMessage.value = '';
    setTimeout(() => pinRefs.value[0]?.focus(), 300);
    return;
  }

  if (pinCode.value !== savedPin.value) {
    errorMessage.value = 'PIN does not match. Please try again.';
    pinDigits.value = ['', '', '', '', ''];
    setTimeout(() => pinRefs.value[0]?.focus(), 300);
    return;
  }

  isSaving.value = true;

  try {
    const response = await fetch(`${API_BASE}/api/driver/device/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: userEmail.value,
        device_id: deviceId.value,
        pin: savedPin.value,
        device_name: navigator.userAgent,
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Failed to save PIN.');
    }

    localStorage.setItem('hasPin', 'true');
    localStorage.setItem('isAuthenticated', 'true');
    if (data.token) {
      localStorage.setItem('driver_token', data.token);
    }

    const alert = await alertController.create({
      cssClass: 'success-modal',
      header: ' ',
      message: 'Device PIN set successfully!',
      buttons: ['OK'],
    });
    await alert.present();
    await alert.onDidDismiss();

    router.push('/home');
  } catch (error) {
    const alert = await alertController.create({
      header: 'Error',
      message: error.message || 'Failed to save PIN. Please try again.',
      buttons: ['OK'],
    });
    await alert.present();
    step.value = 'create';
    savedPin.value = '';
    pinDigits.value = ['', '', '', '', ''];
  } finally {
    isSaving.value = false;
  }
};

onMounted(() => {
  if (!localStorage.getItem('deviceId')) {
    const newId = 'device_' + Date.now() + '_' + Math.random().toString(36).substring(2, 10);
    localStorage.setItem('deviceId', newId);
    deviceId.value = newId;
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
  margin: 0;
  line-height: 1.5;
}

.pin-inputs {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-bottom: 16px;
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

.error-text {
  text-align: center;
  color: #e74c3c;
  font-size: 14px;
  margin: 0 0 16px 0;
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
  background: #4caf50;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
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
</style>
