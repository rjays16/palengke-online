<template>
  <ion-page>
    <ion-content :fullscreen="true" class="login-content">
      <div class="login-container">
        <!-- Logo Section -->
        <div class="logo-section">
          <div class="logos-wrapper">
            <img src="/assets/images/logo.png" alt="Palengke Online" class="logo-main" />
            <div class="plus-sign">+</div>
            <img src="/assets/images/palengke-driver.png" alt="Driver" class="logo-driver" />
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

const router = useRouter();
import { API_BASE } from '@/services/api';

const email = ref('');
const isLoading = ref(false);

const showLoadingModal = async (message = 'Loading...', autoDismiss = true) => {
  const alert = await alertController.create({
    cssClass: 'loading-modal-simple',
    message: message,
    buttons: [],
  });
  await alert.present();
  if (autoDismiss) {
    setTimeout(() => alert.dismiss(), 1500);
  }
  return alert;
};

const checkAvailability = async () => {
  const savedData = localStorage.getItem('registrationData');
  if (!savedData) return { available: true };

  const registration = JSON.parse(savedData);
  const userCountry = registration.country;
  const userCity = registration.birthplace || '';

  if (!userCountry) return { available: true };

  try {
    const response = await fetch(`${API_BASE}/api/countries`);
    const data = await response.json();

    if (!data.countries || data.countries.length === 0) {
      return { available: false, reason: 'country' };
    }

    const countryEnabled = data.countries.find((c) => c.code === userCountry && c.enabled);
    if (!countryEnabled) {
      return { available: false, reason: 'country' };
    }

    const locResponse = await fetch(`${API_BASE}/api/locations`);
    const locData = await locResponse.json();

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

const handleLogin = async () => {
  if (!email.value) return;

  isLoading.value = true;

  try {
    const response = await fetch(`${API_BASE}/api/driver/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value }),
    });

    const data = await response.json();

    if (!response.ok) {
      isLoading.value = false;

      if (response.status === 404) {
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
              handler: () => router.push('/signup'),
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

      if (response.status === 403) {
        if (data.status === 'rejected') {
          const alert = await alertController.create({
            cssClass: 'alert-login',
            header: ' ',
            message: '',
            buttons: [
              {
                text: 'Register Again',
                cssClass: 'alert-button-primary',
                handler: () => router.push('/signup'),
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

      throw new Error(data.message || 'Login failed.');
    }

    localStorage.setItem('hasPin', data.hasPin ? 'true' : 'false');
    localStorage.setItem('userEmail', email.value);

    const availability = await checkAvailability();

    if (!availability.available) {
      isLoading.value = false;
      router.push(`/area-not-available/${availability.reason}`);
      return;
    }

    isLoading.value = false;

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
            const loading = await showLoadingModal('Sending verification code...', false);

            try {
              await fetch(`${API_BASE}/api/send-otp`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email.value }),
              });
            } catch (e) {
              // Silent
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

const goToSignup = () => {
  router.push('/signup');
};

onMounted(async () => {
  if (!localStorage.getItem('deviceId')) {
    const deviceId = 'device_' + Date.now() + '_' + Math.random().toString(36).substring(2, 10);
    localStorage.setItem('deviceId', deviceId);
  }

  const email = localStorage.getItem('userEmail');
  const devId = localStorage.getItem('deviceId');
  if (email && devId) {
    try {
      const res = await fetch(`${API_BASE}/api/driver/device/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, device_id: devId }),
      });
      const data = await res.json();
      if (data.hasPin) {
        localStorage.setItem('hasPin', 'true');
        router.replace('/pin-login');
      } else {
        localStorage.removeItem('hasPin');
      }
    } catch {
      if (localStorage.getItem('hasPin') === 'true') {
        router.replace('/pin-login');
      }
    }
  }
});
</script>

<style scoped>
.login-content {
  --background: #ffffff;
}

.login-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: calc(40px + constant(safe-area-inset-top)) 24px calc(40px + constant(safe-area-inset-bottom));
  padding: calc(40px + env(safe-area-inset-top)) 24px calc(40px + env(safe-area-inset-bottom));
}

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
  color: #1a1a1a;
  line-height: 1;
  margin: 0 2px;
}

.logo-driver {
  width: 154px;
  height: 154px;
  object-fit: contain;
}

.title {
  font-size: 32px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
  text-align: center;
}

.subtitle {
  font-size: 16px;
  color: #666;
  margin: 0 0 40px 0;
  text-align: center;
}

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
  color: #4CAF50;
  z-index: 2;
}

.custom-input {
  width: 100%;
  padding: 16px 16px 16px 48px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 16px;
  background: white;
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input:focus {
  outline: none;
  border-color: #4CAF50;
}

.custom-input::placeholder {
  color: #999;
}

.login-button {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0.5px;
  margin-bottom: 0;
}

.signup-section {
  text-align: center;
  margin-top: 32px;
}

.signup-text {
  color: #666;
  font-size: 15px;
  margin: 0;
}

.signup-text a {
  color: #4CAF50;
  text-decoration: none;
  font-weight: 600;
  margin-left: 4px;
}

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
  .logo-driver {
    width: 126px;
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
  .logo-driver {
    width: 105px;
    height: 105px;
  }
  .title {
    font-size: 24px;
  }
}

/* Loading Modal */
:deep(.loading-modal-simple .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 280px;
  padding: 50px 30px;
  background: white;
}

:deep(.loading-modal-simple .alert-head) {
  display: none;
}

:deep(.loading-modal-simple .alert-message) {
  padding: 0;
  position: relative;
  padding-top: 70px;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

:deep(.loading-modal-simple .alert-message::before) {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  margin-left: -25px;
  width: 50px;
  height: 50px;
  border: 5px solid #f0f0f0;
  border-top: 5px solid #4CAF50;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

:deep(.loading-modal-simple .alert-button-group) {
  display: none;
}

/* OTP Confirm Alert */
:deep(.otp-confirm-alert .alert-wrapper) {
  border-radius: 20px;
  background: white;
}

:deep(.otp-confirm-alert .alert-head h2) {
  color: #1a1a1a;
}

:deep(.otp-confirm-alert .alert-message) {
  color: #666;
}

:deep(.otp-confirm-alert .alert-button-cancel) {
  color: #666;
}

:deep(.otp-confirm-alert .alert-button-confirm) {
  color: #4CAF50;
  font-weight: 600;
}

/* Alert Shared Styles */
:deep(.alert-login .alert-wrapper) {
  border-radius: 20px;
  max-width: 360px;
  background: white;
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
  color: #666 !important;
  background: #f5f5f5;
}

:deep(.alert-login .alert-button-primary) {
  color: #fff !important;
  background: #4CAF50;
}

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

.custom-alert-title {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
}

.custom-alert-text {
  font-size: 14px;
  color: #666;
  line-height: 1.5;
  margin-top: 4px;
}

.custom-alert-text strong {
  color: #1a1a1a;
  font-weight: 600;
}
</style>
