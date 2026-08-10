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
          <OtpInput
            ref="pinInputRef"
            v-model="pinCodeStr"
            :length="5"
            type="pin"
            @complete="onPinComplete"
            :error="!!errorMessage"
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
import { api, ApiError } from '@/utils/apiClient';
import { useAuthStore } from '@/stores/auth';
import OtpInput from '@/components/OtpInput.vue';

const router = useRouter();
const auth = useAuthStore();

const step = ref('create');
const savedPin = ref('');
const isSaving = ref(false);
const errorMessage = ref('');
const pinInputRef = ref();
const pinCodeStr = ref('');

const userEmail = ref(auth.userEmail || '');
const deviceId = ref(auth.deviceId || '');

const isCodeComplete = computed(() => pinInputRef.value?.isComplete ?? false);

const pinCode = computed(() => pinInputRef.value?.code ?? pinCodeStr.value);

const onPinComplete = () => {
  handleNext();
};

const handleNext = async () => {
  if (!isCodeComplete.value) return;

  if (step.value === 'create') {
    savedPin.value = pinCode.value;
    step.value = 'confirm';
    errorMessage.value = '';
    pinCodeStr.value = '';
    pinInputRef.value?.reset();
    return;
  }

  if (pinCode.value !== savedPin.value) {
    errorMessage.value = 'PIN does not match. Please try again.';
    pinCodeStr.value = '';
    pinInputRef.value?.reset();
    return;
  }

  isSaving.value = true;

  try {
    const data = await api.post(
      '/seller/device/register',
      {
        email: userEmail.value,
        device_id: deviceId.value,
        pin: savedPin.value,
        device_name: navigator.userAgent,
      },
      { skipAuth: true },
    );

    auth.setSession(userEmail.value, data.token || '', true, deviceId.value);

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
    const message = error instanceof ApiError ? error.data?.message || error.message : error.message;
    const alert = await alertController.create({
      header: 'Error',
      message: message || 'Failed to save PIN. Please try again.',
      buttons: ['OK'],
    });
    await alert.present();
    step.value = 'create';
    savedPin.value = '';
    pinCodeStr.value = '';
    pinInputRef.value?.reset();
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
    pinInputRef.value?.focus();
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
}

@media (max-width: 360px) {
  .pin-inputs {
    gap: 6px;
  }
}
</style>
