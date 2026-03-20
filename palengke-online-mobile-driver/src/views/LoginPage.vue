<template>
  <ion-page>
    <ion-content :fullscreen="true" class="login-content">
      <div class="login-container">
        
        <!-- Logo Section - Both logos, driver is bigger -->
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
import { ref } from 'vue';
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

// Login handler
const handleLogin = async () => {
  if (!email.value || !password.value) return;
  
  isLoading.value = true;
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // TODO: Replace with actual login logic
    console.log('Login:', { email: email.value, password: password.value });
    
    // Save auth state
    localStorage.setItem('isAuthenticated', 'true');
    
    // Navigate to home page
    router.push('/home');
  } catch (error) {
    const alert = await alertController.create({
      header: 'Login Failed',
      message: 'Invalid email or password. Please try again.',
      buttons: ['OK']
    });
    await alert.present();
  } finally {
    isLoading.value = false;
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
  router.push('/signup');
};
</script>

<style scoped>
/* Simple white background */
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

/* Logo Section - Driver is 40% bigger than Palengke logo */
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
  width: 154px;  /* 110px + 40% = 154px (BIGGER!) */
  height: 154px;
  object-fit: contain;
}

/* Title Section */
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
  
  .logo-driver {
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
  
  .logo-driver {
    width: 105px;  /* 75px + 40% */
    height: 105px;
  }
  
  .title {
    font-size: 24px;
  }
}
</style>