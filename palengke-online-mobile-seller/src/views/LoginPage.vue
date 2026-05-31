<template>
  <ion-page>
    <ion-content :fullscreen="true" class="login-content">
      <div class="login-container">
        
        <!-- Logo Section - Both logos, seller is bigger -->
        <div class="logo-section">
          <div class="logos-wrapper">
            <img src="/assets/images/logo.png" alt="Palengke Online" class="logo-main">
            <div class="plus-sign">+</div>
            <img src="/assets/images/palengke-seller-boy.png" alt="Seller" class="logo-seller">
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
            >
          </div>

          <div class="input-group">
            <ion-icon :icon="lockClosedOutline" class="input-icon"></ion-icon>
            <input 
              v-model="password" 
              :type="showPassword ? 'text' : 'password'" 
              placeholder="Password"
              class="custom-input"
              @keyup.enter="handleLogin"
            >
            <ion-icon 
              :icon="showPassword ? eyeOffOutline : eyeOutline" 
              class="toggle-password"
              @click="showPassword = !showPassword"
            ></ion-icon>
          </div>

          <div class="forgot-password">
            <a href="#" @click.prevent="handleForgotPassword">Forgot Password?</a>
          </div>

          <ion-button 
            expand="block" 
            class="login-button"
            @click="handleLogin"
            :disabled="!email || !password"
          >
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
import { 
  IonPage, 
  IonContent, 
  IonButton, 
  IonIcon, 
  IonSpinner,
  alertController
} from '@ionic/vue';
import { 
  mailOutline, 
  lockClosedOutline, 
  eyeOutline, 
  eyeOffOutline
} from 'ionicons/icons';

const router = useRouter();

// Form data
const email = ref('');
const password = ref('');
const showPassword = ref(false);
const isLoading = ref(false);

// ✨ NEW: Loading Modal Function
const showLoadingModal = async (message = 'Loading...') => {
  const alert = await alertController.create({
    cssClass: 'loading-modal-simple',
    message: message,
    buttons: []
  });
  
  await alert.present();
  
  // Auto dismiss after 1.5 seconds
  setTimeout(() => {
    alert.dismiss();
  }, 1500);
};

// Check if user's country is available
const checkAvailability = async () => {
  // Get user's registration data
  const savedData = localStorage.getItem('registrationData');
  if (!savedData) return { available: true }; // No registration data, allow login

  const registration = JSON.parse(savedData);
  const userCountry = registration.country;
  const userCity = registration.birthplace || '';

  if (!userCountry) return { available: true }; // No country set, allow login

  try {
    // Fetch enabled countries from backend
    const response = await fetch('http://localhost:8000/api/countries');
    const data = await response.json();

    if (!data.countries || data.countries.length === 0) {
      return { available: false, reason: 'country', message: 'No countries are currently enabled.' };
    }

    // Check if user's country is enabled
    const countryEnabled = data.countries.find((c) => c.code === userCountry && c.enabled);

    if (!countryEnabled) {
      return {
        available: false,
        reason: 'country',
        message: 'This app is not available in your country.',
      };
    }

    // If Philippines, check if city is enabled
    if (userCountry === 'PH') {
      const locResponse = await fetch('http://localhost:8000/api/locations');
      const locData = await locResponse.json();

      if (locData.locations) {
        // Search through all cities in all regions
        let cityFound = false;
        for (const region of locData.locations) {
          for (const city of region.cities) {
            if (city.checked && userCity.toLowerCase().includes(city.name.toLowerCase())) {
              cityFound = true;
              break;
            }
          }
          if (cityFound) break;
        }

        if (!cityFound) {
          return {
            available: false,
            reason: 'area',
            message: 'This app is not available yet in your area.',
          };
        }
      }
    }

    return { available: true };
  } catch (err) {
    console.error('Availability check failed:', err);
    return { available: true }; // Allow login on API failure
  }
};

// Login handler
const handleLogin = async () => {
  if (!email.value || !password.value) return;

  isLoading.value = true;

  try {
    const response = await fetch('http://localhost:8000/api/seller/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value, password: password.value }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Login failed.');
    }

    isLoading.value = false;

    localStorage.setItem('token', data.token);

    const availability = await checkAvailability();

    if (!availability.available) {
      const alert = await alertController.create({
        header: availability.reason === 'country' ? 'Country Not Available' : 'Area Not Available',
        message: availability.message,
        cssClass: 'availability-alert',
        buttons: [
          {
            text: 'OK',
            role: 'cancel',
            cssClass: 'alert-button-cancel',
          },
        ],
      });
      await alert.present();
      return;
    }

    // Show OTP confirmation dialog (like GCash)
    const alert = await alertController.create({
      header: 'Verify Account',
      message: 'Do you want to link this device to your account?',
      subHeader: 'We need to do a quick authentication before linking your device. A 6-digit verification code will be sent to your registered email address.',
      cssClass: 'otp-confirm-alert',
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'alert-button-cancel'
        },
        {
          text: 'Send Code',
          cssClass: 'alert-button-confirm',
          handler: async () => {
            await showLoadingModal('Sending verification code...');

            try {
              await fetch('http://localhost:8000/api/send-otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email.value }),
              });
            } catch (e) {
              // Silent — OTP may already have been sent
            }

            localStorage.setItem('pendingAuth', 'true');
            localStorage.setItem('userEmail', email.value);

            setTimeout(() => {
              router.push({
                name: 'OTPVerification',
                params: { email: email.value }
              });
            }, 1500);
          }
        }
      ]
    });
    
    await alert.present();
    
  } catch (error) {
    isLoading.value = false;
    localStorage.removeItem('token');
    const alert = await alertController.create({
      header: 'Login Failed',
      message: error.message || 'Invalid email or password. Please try again.',
      buttons: ['OK']
    });
    await alert.present();
  }
};

// Forgot password handler
const handleForgotPassword = async () => {
  const alert = await alertController.create({
    header: 'Reset Password',
    message: 'Enter your email address to receive a password reset link.',
    inputs: [
      {
        name: 'email',
        type: 'email',
        placeholder: 'Email address'
      }
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Send',
        handler: (data) => {
          console.log('Reset password for:', data.email);
          // TODO: Implement password reset logic
        }
      }
    ]
  });
  await alert.present();
};

// Navigate to signup
const goToSignup = () => {
  router.push('/welcome');
};

// Ensure device ID exists and check backend for PIN
onMounted(async () => {
  if (!localStorage.getItem('deviceId')) {
    const deviceId = 'device_' + Date.now() + '_' + Math.random().toString(36).substring(2, 10);
    localStorage.setItem('deviceId', deviceId);
  }

  const email = localStorage.getItem('userEmail');
  const devId = localStorage.getItem('deviceId');
  if (email && devId) {
    try {
      const res = await fetch('http://localhost:8000/api/seller/device/status', {
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
      // fallback to localStorage check
      if (localStorage.getItem('hasPin') === 'true') {
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
  padding: 40px 24px;
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
  width: 154px;  /* 110px + 40% = 154px (BIGGER!) */
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

.toggle-password {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: var(--color-icon-tertiary);
  cursor: pointer;
  z-index: 2;
}

.forgot-password {
  text-align: right;
  margin-bottom: 24px;
}

.forgot-password a {
  color: var(--color-link);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
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
    width: 126px;  /* 90px + 40% */
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
    width: 105px;  /* 75px + 40% */
    height: 105px;
  }
  
  .title {
    font-size: 24px;
  }
}

/* ✨ NEW: Loading Modal Styling */
:deep(.loading-modal-simple .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 280px;
  padding: 50px 30px;
  background: var(--color-modal-bg);
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
  color: var(--color-modal-text);
}

:deep(.loading-modal-simple .alert-message::before) {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  margin-left: -25px;
  width: 50px;
  height: 50px;
  border: 5px solid var(--color-bg-tertiary);
  border-top: 5px solid var(--color-primary);
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
