<template>
  <ion-page>
    <ion-content :fullscreen="true" class="signup-content">
      <div class="signup-container">
        
        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Logo -->
        <div class="logo-section">
          <img src="/assets/images/logo.png" alt="Palengke Online" class="logo">
        </div>

        <!-- Title -->
        <h1 class="title">Create Account</h1>
        <p class="subtitle">Join us for fresh market deals</p>

        <!-- Signup Form -->
        <div class="form-section">
          <div class="input-group">
            <ion-icon :icon="personOutline" class="input-icon"></ion-icon>
            <input 
              v-model="fullName" 
              type="text" 
              placeholder="Full name"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
            <input 
              v-model="email" 
              type="email" 
              placeholder="Email address"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <ion-icon :icon="callOutline" class="input-icon"></ion-icon>
            <input 
              v-model="phone" 
              type="tel" 
              placeholder="Phone number"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <ion-icon :icon="lockClosedOutline" class="input-icon"></ion-icon>
            <input 
              v-model="password" 
              :type="showPassword ? 'text' : 'password'" 
              placeholder="Password"
              class="custom-input"
            >
            <ion-icon 
              :icon="showPassword ? eyeOffOutline : eyeOutline" 
              class="toggle-password"
              @click="showPassword = !showPassword"
            ></ion-icon>
          </div>

          <div class="input-group">
            <ion-icon :icon="lockClosedOutline" class="input-icon"></ion-icon>
            <input 
              v-model="confirmPassword" 
              :type="showConfirmPassword ? 'text' : 'password'" 
              placeholder="Confirm password"
              class="custom-input"
            >
            <ion-icon 
              :icon="showConfirmPassword ? eyeOffOutline : eyeOutline" 
              class="toggle-password"
              @click="showConfirmPassword = !showConfirmPassword"
            ></ion-icon>
          </div>

          <!-- Terms & Conditions -->
          <div class="terms-checkbox">
            <input 
              type="checkbox" 
              id="terms" 
              v-model="acceptedTerms"
              class="checkbox-input"
            >
            <label for="terms">
              I agree to the 
              <a href="#" @click.prevent="showTerms">Terms & Conditions</a>
            </label>
          </div>

          <ion-button 
            expand="block" 
            class="signup-button"
            @click="handleSignup"
            :disabled="!isFormValid || isLoading"
          >
            <span v-if="!isLoading">Create Account</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>
        </div>

        <!-- Login Link -->
        <div class="login-section">
          <p class="login-text">
            Already have an account? 
            <a href="#" @click.prevent="goToLogin">Sign In</a>
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
  alertController 
} from '@ionic/vue';
import { 
  arrowBackOutline,
  personOutline,
  mailOutline,
  callOutline,
  lockClosedOutline, 
  eyeOutline, 
  eyeOffOutline
} from 'ionicons/icons';

const router = useRouter();

// Form data
const fullName = ref('');
const email = ref('');
const phone = ref('');
const password = ref('');
const confirmPassword = ref('');
const acceptedTerms = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const isLoading = ref(false);

// Form validation
const isFormValid = computed(() => {
  return fullName.value && 
         email.value && 
         phone.value && 
         password.value && 
         confirmPassword.value &&
         password.value === confirmPassword.value &&
         acceptedTerms.value;
});

// Signup handler
const handleSignup = async () => {
  if (!isFormValid.value) return;
  
  isLoading.value = true;
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // TODO: Replace with actual signup logic
    console.log('Signup:', { 
      fullName: fullName.value, 
      email: email.value,
      phone: phone.value,
      password: password.value 
    });
    
    // Show success message
    const alert = await alertController.create({
      header: 'Success!',
      message: 'Your account has been created successfully.',
      buttons: ['OK']
    });
    await alert.present();
    
    // Navigate to login page
    router.push('/login');
  } catch (error) {
    const alert = await alertController.create({
      header: 'Signup Failed',
      message: 'Something went wrong. Please try again.',
      buttons: ['OK']
    });
    await alert.present();
  } finally {
    isLoading.value = false;
  }
};

// Show terms
const showTerms = async () => {
  const alert = await alertController.create({
    header: 'Terms & Conditions',
    message: 'By using Palengke Online, you agree to our terms of service and privacy policy.',
    buttons: ['Close']
  });
  await alert.present();
};

// Navigation
const goBack = () => {
  router.back();
};

const goToLogin = () => {
  router.push('/login');
};
</script>

<style scoped>
/* Simple white background, no animations */
.signup-content {
  --background: #ffffff;
}

.signup-container {
  min-height: 100vh;
  padding: 24px;
  position: relative;
}

/* Back Button */
.back-button {
  position: absolute;
  top: 24px;
  left: 24px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border-radius: 12px;
  cursor: pointer;
  z-index: 10;
  transition: background 0.2s;
}

.back-button:hover {
  background: #e0e0e0;
}

.back-button:active {
  transform: scale(0.95);
}

.back-button ion-icon {
  font-size: 24px;
  color: #1a1a1a;
}

/* Logo Section - Simple, no animation */
.logo-section {
  text-align: center;
  margin: 60px 0 30px 0;
}

.logo {
  width: 160px;
  height: auto;
}

/* Title Section */
.title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
  text-align: center;
}

.subtitle {
  font-size: 15px;
  color: #666;
  margin: 0 0 32px 0;
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
  margin-bottom: 16px;
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
  padding: 14px 16px 14px 48px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
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

/* Terms Checkbox */
.terms-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin: 20px 0 24px 0;
  font-size: 13px;
  color: #666;
}

.checkbox-input {
  width: 18px;
  height: 18px;
  margin-top: 2px;
  cursor: pointer;
  accent-color: #4CAF50;
}

.terms-checkbox label {
  flex: 1;
  line-height: 1.5;
}

.terms-checkbox a {
  color: #4CAF50;
  text-decoration: none;
  font-weight: 600;
}

.signup-button {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0.5px;
  margin-bottom: 24px;
}

/* Login Section */
.login-section {
  text-align: center;
  margin-top: 24px;
  padding-bottom: 20px;
}

.login-text {
  color: #666;
  font-size: 14px;
  margin: 0;
}

.login-text a {
  color: #4CAF50;
  text-decoration: none;
  font-weight: 600;
  margin-left: 4px;
}

/* Responsive */
@media (max-width: 360px) {
  .signup-container {
    padding: 20px 16px;
  }
  
  .title {
    font-size: 24px;
  }
  
  .logo {
    width: 130px;
  }
}
</style>