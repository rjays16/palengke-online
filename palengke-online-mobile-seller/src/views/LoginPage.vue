<template>
  <ion-page>
    <ion-content :fullscreen="true" class="login-content">
      <div class="login-container">
        <!-- Logo Section - Both logos, seller is bigger -->
        <div class="logo-section">
          <div class="logos-wrapper">
            <img src="/assets/images/logo.png" alt="Palengke Online" class="logo-main" />
            <div class="plus-sign">+</div>
            <img src="/assets/images/palengke-seller-boy.png" alt="Seller" class="logo-seller" />
          </div>
        </div>

        <!-- Title -->
        <h1 class="title">Palengke Online</h1>
        <p class="subtitle">Fresh deals delivered to your door</p>

        <!-- Login Form -->
        <div class="form-section">
          <div class="input-group">
            <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
            <input
              v-model="email"
              type="email"
              placeholder="Email address"
              class="custom-input"
              @keyup.enter="handleLogin"
            />
          </div>

          <ion-button expand="block" class="login-button" @click="handleLogin" :disabled="!email">
            <span v-if="!isLoading">Sign In</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>
        </div>

        <!-- Sign Up Link -->
        <div class="signup-section">
          <p class="signup-text">
            Don't have an account?
            <a href="#" @click.prevent="goToSignup">Sign Up</a>
          </p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, IonSpinner, alertController } from '@ionic/vue';
import { mailOutline } from 'ionicons/icons';
import { api, ApiError } from '@/utils/apiClient';
import { useAuthStore } from '@/stores/auth';
import { useRegistrationStore } from '@/stores/registration';
import { useModals } from '@/composables/useModals';

const router = useRouter();
const auth = useAuthStore();
const reg = useRegistrationStore();
const { showLoadingModal } = useModals();

// Form data
const email = ref('');
const isLoading = ref(false);

// Check if user's country / region / area is available
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

// Login handler
const handleLogin = async () => {
  if (!email.value) return;

  isLoading.value = true;

  try {
    let data;
    try {
      data = await api.post('/seller/login', { email: email.value }, { skipAuth: true });
    } catch (apiError) {
      if (apiError instanceof ApiError) {
        isLoading.value = false;

        if (apiError.status === 404) {
          const alert = await alertController.create({
            cssClass: 'alert-login',
            header: ' ',
            message: '',
            buttons: [
              {
                text: 'Cancel',
                role: 'cancel',
                cssClass: 'alert-button-cancel',
              },
              {
                text: 'Sign Up',
                cssClass: 'alert-button-primary',
                handler: () => {
                  router.push('/welcome');
                },
              },
            ],
          });
          await alert.present();
          const alertEl = document.querySelector('.alert-login');
          if (alertEl) {
            const msgEl = alertEl.querySelector('.alert-message');
            if (msgEl) {
              msgEl.innerHTML = `
                <div class="custom-alert-body">
                  <div class="custom-alert-icon icon-warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </div>
                  <div class="custom-alert-title">Account Not Found</div>
                  <div class="custom-alert-text">No account found with <strong>${email.value}</strong>. Would you like to sign up instead?</div>
                </div>`;
            }
          }
          return;
        }

        if (apiError.status === 403) {
          if (apiError.data?.status === 'rejected') {
            const alert = await alertController.create({
              cssClass: 'alert-login',
              header: ' ',
              message: '',
              buttons: [
                {
                  text: 'Register Again',
                  cssClass: 'alert-button-primary',
                  handler: () => router.push('/welcome'),
                },
              ],
            });
            await alert.present();
            const alertEl = document.querySelector('.alert-login');
            if (alertEl) {
              const msgEl = alertEl.querySelector('.alert-message');
              if (msgEl) {
                msgEl.innerHTML = `
                  <div class="custom-alert-body">
                    <div class="custom-alert-icon icon-error">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    </div>
                    <div class="custom-alert-title">Registration Not Approved</div>
                    <div class="custom-alert-text">Your previous registration was not approved. Please register again with updated information.</div>
                  </div>`;
              }
            }
          } else {
            localStorage.setItem('userEmail', email.value);
            router.push('/account-pending');
          }
          return;
        }

        throw new Error(apiError.data?.message || 'Login failed.');
      }
      throw apiError;
    }

    auth.setHasPin(data.hasPin);
    auth.userEmail = email.value;
    localStorage.setItem('userEmail', email.value);

    const availability = await checkAvailability();

    if (!availability.available) {
      isLoading.value = false;
      router.push(`/area-not-available/${availability.reason}`);
      return;
    }

    isLoading.value = false;

    // Show OTP confirmation dialog
    const alert = await alertController.create({
      header: 'Verify Account',
      message: 'Do you want to link this device to your account?',
      subHeader:
        'We need to do a quick authentication before linking your device. A 6-digit verification code will be sent to your registered email address.',
      cssClass: 'otp-confirm-alert',
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'alert-button-cancel',
        },
        {
          text: 'Send Code',
          cssClass: 'alert-button-confirm',
          handler: async () => {
            const loading = await showLoadingModal('Sending verification code...');

            try {
              await api.post('/send-otp', { email: email.value }, { skipAuth: true });
            } catch (e) {
              // Silent — OTP may already have been sent
            }

            localStorage.setItem('pendingAuth', 'true');

            await loading.dismiss();
            router.push({
              name: 'OTPVerification',
              params: { email: email.value },
            });
          },
        },
      ],
    });

    await alert.present();
  } catch (error) {
    isLoading.value = false;
    const alert = await alertController.create({
      cssClass: 'alert-login',
      header: ' ',
      message: '',
      buttons: [{ text: 'OK', cssClass: 'alert-button-cancel' }],
    });
    await alert.present();
    const alertEl = document.querySelector('.alert-login');
    if (alertEl) {
      const msgEl = alertEl.querySelector('.alert-message');
      if (msgEl) {
        msgEl.innerHTML = `
          <div class="custom-alert-body">
            <div class="custom-alert-icon icon-error">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
            </div>
            <div class="custom-alert-title">Login Failed</div>
            <div class="custom-alert-text">${error.message || 'Invalid email. Please try again.'}</div>
          </div>`;
      }
    }
  }
};

// Navigate to signup
const goToSignup = () => {
  router.push('/welcome');
};

// Ensure device ID exists and check backend for PIN
onMounted(async () => {
  auth.initFromStorage();

  const email = auth.userEmail;
  const devId = auth.deviceId;
  if (email && devId) {
    try {
      const data = await api.post('/seller/device/status', { email, device_id: devId }, { skipAuth: true });
      if (data.hasPin) {
        auth.setHasPin(true);
        router.replace('/pin-login');
      } else {
        auth.setHasPin(false);
      }
    } catch {
      // fallback to store check
      if (auth.hasPin) {
        router.replace('/pin-login');
      }
    }
  }
});
</script>

<style scoped>
/* Simple white background */
.login-content {
  --background: var(--color-bg-primary);
}

.login-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: calc(40px + constant(safe-area-inset-top)) 24px calc(40px + constant(safe-area-inset-bottom));
  padding: calc(40px + env(safe-area-inset-top)) 24px calc(40px + env(safe-area-inset-bottom));
}

/* Logo Section - Seller is 40% bigger than Palengke logo */
.logo-section {
  text-align: center;
  margin-bottom: 30px;
}

.logos-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.logo-main {
  width: 110px;
  height: 110px;
  object-fit: contain;
}

.plus-sign {
  font-size: 36px;
  font-weight: 700;
  color: var(--color-text-primary);
  line-height: 1;
  margin: 0 2px;
}

.logo-seller {
  width: 154px; /* 110px + 40% = 154px (BIGGER!) */
  height: 154px;
  object-fit: contain;
}

/* Title Section */
.title {
  font-size: 32px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 8px 0;
  text-align: center;
}

.subtitle {
  font-size: 16px;
  color: var(--color-text-secondary);
  margin: 0 0 40px 0;
  text-align: center;
}

/* Form Section */
.form-section {
  max-width: 400px;
  margin: 0 auto;
  width: 100%;
}

.input-group {
  position: relative;
  margin-bottom: 20px;
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
  padding: 16px 16px 16px 48px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 16px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.custom-input::placeholder {
  color: var(--color-input-placeholder);
}

.login-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0.5px;
  margin-bottom: 0;
}

/* Sign Up Section */
.signup-section {
  text-align: center;
  margin-top: 32px;
}

.signup-text {
  color: var(--color-text-secondary);
  font-size: 15px;
  margin: 0;
}

.signup-text a {
  color: var(--color-link);
  text-decoration: none;
  font-weight: 600;
  margin-left: 4px;
}

/* Responsive */
@media (max-width: 480px) {
  .logos-wrapper {
    gap: 6px;
  }

  .logo-main {
    width: 90px;
    height: 90px;
  }

  .plus-sign {
    font-size: 28px;
    margin: 0 1px;
  }

  .logo-seller {
    width: 126px; /* 90px + 40% */
    height: 126px;
  }

  .title {
    font-size: 28px;
  }
}

@media (max-width: 360px) {
  .login-container {
    padding: 32px 20px;
  }

  .logos-wrapper {
    gap: 5px;
  }

  .logo-main {
    width: 75px;
    height: 75px;
  }

  .plus-sign {
    font-size: 24px;
    margin: 0 1px;
  }

  .logo-seller {
    width: 105px; /* 75px + 40% */
    height: 105px;
  }

  .title {
    font-size: 24px;
  }
}

/* Dark Mode: OTP Confirm Alert */
:deep(.otp-confirm-alert .alert-wrapper) {
  background: var(--color-modal-bg);
}

:deep(.otp-confirm-alert .alert-head) {
  background: var(--color-modal-bg);
}

:deep(.otp-confirm-alert .alert-head h2) {
  color: var(--color-modal-text);
}

:deep(.otp-confirm-alert .alert-message) {
  background: var(--color-modal-bg);
  color: var(--color-text-secondary);
}

:deep(.otp-confirm-alert .alert-button-group) {
  background: var(--color-modal-bg);
}

:deep(.otp-confirm-alert .alert-button) {
  color: var(--color-primary);
}

:deep(.otp-confirm-alert .alert-button-cancel) {
  color: var(--color-text-secondary);
}

/* Not Found Alert */
:deep(.alert-login .alert-wrapper) {
  border-radius: 20px;
  max-width: 360px;
  background: var(--color-modal-bg);
  padding: 0;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
}

:deep(.alert-login .alert-head) {
  display: none;
  padding: 0;
  min-height: 0;
}

:deep(.alert-login .alert-message) {
  padding: 24px 24px 8px;
  text-align: center;
}

:deep(.alert-login .alert-button-group) {
  padding: 8px 24px 24px;
  justify-content: center;
  gap: 10px;
}

:deep(.alert-login .alert-button) {
  border-radius: 12px;
  height: 46px;
  font-size: 14px;
  font-weight: 600;
  text-transform: none;
  flex: 1;
  margin: 0;
}

:deep(.alert-login .alert-button-cancel) {
  color: var(--color-text-secondary) !important;
  background: var(--color-bg-tertiary);
}

:deep(.alert-login .alert-button-primary) {
  color: #fff !important;
  background: var(--color-primary);
}

/* Custom Alert Body */
.custom-alert-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.custom-alert-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 4px;
}

.custom-alert-icon.icon-warning {
  background: #fef3c7;
  color: #d97706;
}

.custom-alert-icon.icon-error {
  background: #fef2f2;
  color: #dc2626;
}

.custom-alert-icon.icon-pending {
  background: #e0f2fe;
  color: #0284c7;
}

.custom-alert-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-primary);
}

.custom-alert-text {
  font-size: 14px;
  color: var(--color-text-secondary);
  line-height: 1.5;
  margin-top: 4px;
}

.custom-alert-text strong {
  color: var(--color-text-primary);
  font-weight: 600;
}

:deep(.not-found-alert .alert-head) {
  display: none;
}

:deep(.not-found-alert .alert-message) {
  position: relative;
  padding-top: 70px;
  font-size: 16px;
  font-weight: 600;
  color: var(--color-modal-text);
  text-align: center;
  padding-bottom: 8px;
  line-height: 1.5;
}

:deep(.not-found-alert .alert-message::before) {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  margin-left: -30px;
  width: 60px;
  height: 60px;
  background: url('/assets/images/checkmark_success.png') no-repeat center center;
  background-size: contain;
}

:deep(.not-found-alert .alert-button-group) {
  justify-content: center;
  gap: 12px;
  padding: 16px 0 0;
}

:deep(.not-found-alert .alert-button) {
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
  min-width: 100px;
}

:deep(.not-found-alert .alert-button-cancel) {
  color: var(--color-text-secondary);
  background: var(--color-bg-tertiary);
}

:deep(.not-found-alert .alert-button-confirm) {
  color: white;
  background: var(--color-primary);
}

/* Availability Alert */
:deep(.availability-alert .alert-wrapper) {
  border-radius: 20px;
  background: var(--color-modal-bg);
  max-width: 320px;
}

:deep(.availability-alert .alert-head) {
  background: var(--color-modal-bg);
  padding-top: 24px;
}

:deep(.availability-alert .alert-head h2) {
  color: var(--color-modal-text);
  font-size: 18px;
  font-weight: 700;
}

:deep(.availability-alert .alert-message) {
  background: var(--color-modal-bg);
  color: var(--color-text-secondary);
  font-size: 14px;
  line-height: 1.5;
  padding-bottom: 8px;
}

:deep(.availability-alert .alert-button-group) {
  background: var(--color-modal-bg);
  padding: 0 16px 16px;
}

:deep(.availability-alert .alert-button) {
  color: var(--color-primary);
  font-weight: 600;
}
</style>
