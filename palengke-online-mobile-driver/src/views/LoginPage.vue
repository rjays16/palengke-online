<template>
  <ion-page>
    <ion-content :fullscreen="true" class="login-content">
      <div class="login-container">
        
        <!-- Logo Section -->
        <div class="logo-section">
          <div class="logos-wrapper">
            <img src="/assets/images/logo.png" alt="Palengke Online" class="logo-main">
            <div class="plus-sign">+</div>
            <img src="/assets/images/palengke-driver.png" alt="Driver" class="logo-driver">
          </div>
        </div>

        <!-- Title -->
        <h1 class="title">Palengke Online</h1>
        <p class="subtitle">Fresh deals delivered to your door</p>

        <!-- Login Form -->
        <div class="form-section">
          
          <!-- Email Input -->
          <div class="input-group">
            <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
            <input 
              v-model="email" 
              type="email" 
              placeholder="Email address"
              class="custom-input"
              :disabled="showOtpSection"
              @keyup.enter="handleLogin"
            >
          </div>

          <!-- Password Input -->
          <div class="input-group" v-if="!showOtpSection">
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

          <!-- Forgot Password -->
          <div class="forgot-password" v-if="!showOtpSection">
            <a href="#" @click.prevent="handleForgotPassword">Forgot Password?</a>
          </div>

          <!-- Sign In Button -->
          <ion-button 
            v-if="!showOtpSection"
            expand="block" 
            class="login-button"
            @click="handleLogin"
            :disabled="!email || !password || isLoading"
          >
            <span v-if="!isLoading">Sign In</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>

          <!-- OTP Section -->
          <div v-if="showOtpSection" class="otp-section">
            <div class="otp-header">
              <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              <div class="otp-text">
                <h3>Verify Your Email</h3>
                <p>Enter the 6-digit code sent to <strong>{{ maskedEmail }}</strong></p>
              </div>
            </div>

            <div class="otp-inputs">
              <input 
                v-for="(digit, index) in otpDigits" 
                :key="index"
                :ref="el => otpInputs[index] = el"
                v-model="otpDigits[index]"
                type="tel"
                maxlength="1"
                class="otp-input"
                :class="{ error: otpError }"
                @input="onOtpInput(index)"
                @keydown="onOtpKeydown($event, index)"
                @paste="onOtpPaste"
              >
            </div>

            <div v-if="otpError" class="otp-error">
              <ion-icon :icon="alertCircleOutline"></ion-icon>
              <span>{{ otpError }}</span>
            </div>

            <div class="otp-actions">
              <button 
                class="resend-button" 
                :disabled="resendTimer > 0"
                @click="resendOtp"
              >
                {{ resendTimer > 0 ? `Resend in ${resendTimer}s` : 'Resend Code' }}
              </button>
              <button class="change-email-button" @click="changeEmail">
                Change Email
              </button>
            </div>

            <ion-button 
              expand="block" 
              class="verify-button"
              :disabled="otpValue.length !== 6 || isVerifying"
              @click="verifyOtp"
            >
              <span v-if="!isVerifying">Verify & Sign In</span>
              <ion-spinner v-else name="crescent"></ion-spinner>
            </ion-button>
          </div>

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
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { 
  IonPage, 
  IonContent, 
  IonButton, 
  IonIcon, 
  IonSpinner,
  alertController,
  toastController
} from '@ionic/vue';
import { 
  mailOutline, 
  lockClosedOutline, 
  eyeOutline, 
  eyeOffOutline,
  shieldCheckmarkOutline,
  alertCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const isLoading = ref(false);
const isVerifying = ref(false);

const showOtpSection = ref(false);
const otpDigits = ref(['', '', '', '', '', '']);
const otpInputs = ref([]);
const otpError = ref('');
const resendTimer = ref(0);

const maskedEmail = computed(() => {
  if (!email.value) return '';
  const atIndex = email.value.indexOf('@');
  if (atIndex === -1) return email.value;
  const localPart = email.value.substring(0, atIndex);
  const domain = email.value.substring(atIndex);
  if (localPart.length > 3) {
    return localPart.substring(0, 2) + '***' + localPart.substring(localPart.length - 1) + domain;
  }
  return '***' + domain;
});

const otpValue = computed(() => {
  return otpDigits.value.join('');
});

const handleLogin = async () => {
  if (!email.value || !password.value) return;
  
  isLoading.value = true;
  
  try {
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    const toast = await toastController.create({
      message: `Verification code sent to ${maskedEmail.value}`,
      duration: 3000,
      color: 'medium',
      position: 'top'
    });
    await toast.present();

    showOtpSection.value = true;
    startResendTimer();
    
    setTimeout(() => {
      if (otpInputs.value[0]) {
        otpInputs.value[0].focus();
      }
    }, 300);

  } catch (error) {
    const alert = await alertController.create({
      header: 'Login Failed',
      message: 'Something went wrong. Please try again.',
      buttons: ['OK']
    });
    await alert.present();
  } finally {
    isLoading.value = false;
  }
};

const onOtpInput = (index) => {
  otpDigits.value[index] = otpDigits.value[index].replace(/\D/g, '');
  otpError.value = '';
  
  if (otpDigits.value[index] && index < 5) {
    otpInputs.value[index + 1]?.focus();
  }
};

const onOtpKeydown = (event, index) => {
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    otpInputs.value[index - 1]?.focus();
  }
};

const onOtpPaste = (event) => {
  event.preventDefault();
  const pastedData = event.clipboardData.getData('text').replace(/\D/g, '');
  
  if (pastedData.length === 6) {
    for (let i = 0; i < 6; i++) {
      otpDigits.value[i] = pastedData[i] || '';
    }
    otpInputs.value[5]?.focus();
  }
};

const verifyOtp = async () => {
  if (otpValue.value.length !== 6) return;

  isVerifying.value = true;
  otpError.value = '';

  try {
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    localStorage.setItem('isAuthenticated', 'true');
    router.push('/home');
    
  } catch (error) {
    otpError.value = 'Verification failed. Please try again.';
  } finally {
    isVerifying.value = false;
  }
};

const resendOtp = async () => {
  if (resendTimer.value > 0) return;
  
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  
  await handleLogin();
};

const startResendTimer = () => {
  resendTimer.value = 60;
  const interval = setInterval(() => {
    resendTimer.value--;
    if (resendTimer.value <= 0) {
      clearInterval(interval);
    }
  }, 1000);
};

const changeEmail = () => {
  showOtpSection.value = false;
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  resendTimer.value = 0;
};

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
        }
      }
    ]
  });
  await alert.present();
};

const goToSignup = () => {
  router.push('/signup');
};
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
  padding: 40px 24px;
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
  color: #000000;
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

.custom-input:disabled {
  background: #f5f5f5;
  color: #999;
}

.toggle-password {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #999;
  cursor: pointer;
  z-index: 2;
}

.forgot-password {
  text-align: right;
  margin-bottom: 24px;
}

.forgot-password a {
  color: #4CAF50;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
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
  margin-bottom: 24px;
}

/* OTP Section */
.otp-section {
  background: #E3F2FD;
  border-radius: 16px;
  padding: 24px;
  margin-top: 20px;
  border: 2px solid #2196F3;
}

.otp-header {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.otp-header ion-icon {
  font-size: 32px;
  color: #2196F3;
  flex-shrink: 0;
}

.otp-text h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1565C0;
  margin: 0 0 6px 0;
}

.otp-text p {
  font-size: 13px;
  color: #1976D2;
  margin: 0;
  line-height: 1.4;
}

.otp-text strong {
  font-weight: 700;
}

.otp-inputs {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin-bottom: 16px;
}

.otp-input {
  width: 48px;
  height: 56px;
  border: 2px solid #2196F3;
  border-radius: 12px;
  text-align: center;
  font-size: 24px;
  font-weight: 700;
  color: #1a1a1a;
  background: white;
  transition: all 0.2s;
}

.otp-input:focus {
  outline: none;
  border-color: #1976D2;
  box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.otp-input.error {
  border-color: #f44336;
}

.otp-error {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  background: #FFEBEE;
  border-radius: 8px;
  margin-bottom: 16px;
}

.otp-error ion-icon {
  font-size: 20px;
  color: #f44336;
  flex-shrink: 0;
}

.otp-error span {
  font-size: 13px;
  color: #c62828;
  font-weight: 500;
}

.otp-actions {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.resend-button,
.change-email-button {
  background: none;
  border: none;
  color: #2196F3;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 8px;
  font-family: inherit;
}

.resend-button:disabled {
  color: #999;
  cursor: not-allowed;
}

.change-email-button {
  color: #666;
}

.verify-button {
  --background: #2196F3;
  --background-hover: #1976D2;
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
}

.verify-button:disabled {
  --background: #e0e0e0;
  --color: #999;
}

/* Sign Up Section */
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

  .otp-input {
    width: 44px;
    height: 52px;
    font-size: 22px;
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
</style>
