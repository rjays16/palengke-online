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

        <OtpInput ref="pinInputRef" v-model="pinCodeStr" :length="5" type="pin" @complete="onPinComplete" />

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
import { api, ApiError } from '@/utils/apiClient';
import { useAuthStore } from '@/stores/auth';
import { useRegistrationStore } from '@/stores/registration';
import OtpInput from '@/components/OtpInput.vue';
import { useModals } from '@/composables/useModals';

const router = useRouter();
const auth = useAuthStore();
const reg = useRegistrationStore();
const { showLoadingModal } = useModals();

const userEmail = ref(auth.userEmail || '');
const deviceId = ref(auth.deviceId || '');

const checkAvailability = async () => {
  reg.load();
  const userCountry = reg.data.country;
  const userCity = reg.data.birthplace || '';
  if (!userCountry) return { available: true };

  if (!userCountry) return { available: true };

  try {
    const data = await api.get('/countries');

    if (!data.countries || data.countries.length === 0) {
      return { available: false, reason: 'country' };
    }

    const countryEnabled = data.countries.find((c) => c.code === userCountry && c.enabled);
    if (!countryEnabled) {
      return { available: false, reason: 'country' };
    }

    const locData = await api.get('/locations');

    if (locData.locations && locData.locations.length > 0) {
      let foundRegion = null;
      let foundCity = null;

      for (const region of locData.locations) {
        const city = region.cities.find((c) =>
          userCity.toLowerCase().includes(c.name.toLowerCase())
        );
        if (city) {
          foundRegion = region;
          foundCity = city;
          break;
        }
      }

      if (!foundRegion) {
        return { available: false, reason: 'area' };
      }

      const regionHasEnabledCities = foundRegion.cities.some((c) => c.checked);
      if (!regionHasEnabledCities) {
        return { available: false, reason: 'region' };
      }

      if (!foundCity.checked) {
        return { available: false, reason: 'area' };
      }
    }

    return { available: true };
  } catch (err) {
    console.error('Availability check failed:', err);
    return { available: true };
  }
};

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

const pinInputRef = ref();
const pinCodeStr = ref('');
const isVerifying = ref(false);

const isCodeComplete = computed(() => pinInputRef.value?.isComplete ?? false);

const pinCode = computed(() => pinInputRef.value?.code ?? pinCodeStr.value);

const onPinComplete = () => {
  verifyPin();
};

const verifyPin = async () => {
  if (!isCodeComplete.value) return;
  isVerifying.value = true;

  try {
    const data = await api.post('/seller/device/verify-pin', {
      email: userEmail.value,
      device_id: deviceId.value,
      pin: pinCode.value,
    }, { skipAuth: true });

    auth.setSession(userEmail.value, data.token, true, deviceId.value);

    const availability = await checkAvailability();

    if (!availability.available) {
      router.push(`/area-not-available/${availability.reason}`);
      return;
    }

    router.push('/home');
  } catch (error) {
    const message = error instanceof ApiError ? (error.data?.message || error.message) : error.message;
    if (message && message.includes('No PIN set')) {
      auth.setHasPin(false);
      router.push('/login');
      return;
    }
    const alert = await alertController.create({
      header: 'Sign In Failed',
      message: message || 'Invalid PIN. Please try again.',
      buttons: ['OK'],
    });
    await alert.present();
    pinInputRef.value?.reset();
    pinInputRef.value?.focus();
  } finally {
    isVerifying.value = false;
  }
};

const goToLogin = () => {
  auth.clearAuth();
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
            let data;
            try {
              data = await api.post('/seller/forgot-pin', { email: userEmail.value }, { skipAuth: true });
            } catch (apiError) {
              await loading.dismiss();
              throw apiError;
            }

            await loading.dismiss();

            router.push({
              name: 'OTPVerification',
              params: { email: userEmail.value },
              query: { mode: 'forgot-pin' },
            });
          } catch (e) {
            await loading.dismiss();
            const message = e instanceof ApiError ? (e.data?.message || e.message) : e.message;
            const errAlert = await alertController.create({
              header: 'Error',
              message: message || 'Failed to send verification code.',
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
  auth.initFromStorage();
  userEmail.value = auth.userEmail || '';
  deviceId.value = auth.deviceId || '';
  if (!userEmail.value) {
    router.replace('/login');
    return;
  }
  // Verify PIN exists on backend
  try {
    const data = await api.post('/seller/device/status', { email: userEmail.value, device_id: deviceId.value }, { skipAuth: true });
    if (!data.hasPin) {
      auth.setHasPin(false);
      router.replace('/login');
      return;
    }
  } catch {
    // fallback — rely on store
    if (!auth.hasPin) {
      router.replace('/login');
      return;
    }
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
  color: var(--color-link, #4caf50);
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
}

@media (max-width: 360px) {
  .pin-inputs {
    gap: 6px;
  }
}


</style>
