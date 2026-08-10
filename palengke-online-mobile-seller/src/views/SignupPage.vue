<template>
  <ion-page>
    <ion-content :fullscreen="true" class="signup-content">
      <div class="signup-container">
        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Logo Section - Both logos, seller is bigger -->
        <div class="logo-section">
          <div class="logos-wrapper">
            <img src="/assets/images/logo.png" alt="Palengke Online" class="logo-main" />
            <div class="plus-sign">+</div>
            <img src="/assets/images/palengke-seller-boy.png" alt="Seller" class="logo-seller" />
          </div>
        </div>

        <!-- Title -->
        <h1 class="title">Create Seller Account</h1>
        <p class="subtitle">Start selling fresh produce</p>

        <!-- Signup Form -->
        <div class="form-section">
          <div class="input-group">
            <ion-icon :icon="personOutline" class="input-icon"></ion-icon>
            <input v-model="fullName" type="text" placeholder="Full name" class="custom-input" />
          </div>

          <div class="input-group">
            <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
            <input v-model="email" type="email" placeholder="Email address" class="custom-input" />
          </div>

          <div class="input-group">
            <ion-icon :icon="callOutline" class="input-icon"></ion-icon>
            <input v-model="phone" type="tel" placeholder="Phone number" class="custom-input" />
          </div>

          <div class="input-group">
            <ion-icon :icon="storefrontOutline" class="input-icon"></ion-icon>
            <input v-model="storeName" type="text" placeholder="Store name" class="custom-input" />
          </div>

          <!-- Terms & Conditions -->
          <div class="terms-checkbox">
            <input type="checkbox" id="terms" v-model="acceptedTerms" class="checkbox-input" />
            <label for="terms">
              I agree to the
              <a href="#" @click.prevent="showTerms">Terms & Conditions</a>
            </label>
          </div>

          <ion-button expand="block" class="signup-button" @click="handleSignup" :disabled="!isFormValid">
            <span v-if="!isLoading">Create Account</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>

          <!-- Divider -->
          <div class="divider">
            <span>or sign up with</span>
          </div>

          <!-- Social Signup -->
          <div class="social-buttons">
            <button class="social-btn google" @click="handleGoogleSignup">
              <ion-icon :icon="logoGoogle"></ion-icon>
              Google
            </button>
            <button class="social-btn facebook" @click="handleFacebookSignup">
              <ion-icon :icon="logoFacebook"></ion-icon>
              Facebook
            </button>
          </div>
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
import { IonPage, IonContent, IonButton, IonIcon, IonSpinner, alertController } from '@ionic/vue';
import {
  arrowBackOutline,
  personOutline,
  mailOutline,
  callOutline,
  storefrontOutline,
  logoGoogle,
  logoFacebook,
} from 'ionicons/icons';

const router = useRouter();

// Form data
const fullName = ref('');
const email = ref('');
const phone = ref('');
const storeName = ref('');
const acceptedTerms = ref(false);
const isLoading = ref(false);

// Form validation
const isFormValid = computed(() => {
  return fullName.value && email.value && phone.value && storeName.value && acceptedTerms.value;
});

// Signup handler
const handleSignup = async () => {
  if (!isFormValid.value) return;

  isLoading.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // TODO: Replace with actual signup logic
    console.log('Signup:', {
      fullName: fullName.value,
      email: email.value,
      phone: phone.value,
      storeName: storeName.value,
    });

    // Show success message
    const alert = await alertController.create({
      header: 'Success!',
      message: 'Your seller account has been created successfully.',
      buttons: ['OK'],
    });
    await alert.present();

    // Navigate to login page
    router.push('/login');
  } catch (error) {
    const alert = await alertController.create({
      header: 'Signup Failed',
      message: 'Something went wrong. Please try again.',
      buttons: ['OK'],
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
    message: 'By using Palengke Online Seller, you agree to our terms of service and privacy policy.',
    buttons: ['Close'],
  });
  await alert.present();
};

// Social signup handlers
const handleGoogleSignup = () => {
  console.log('Google signup');
  // TODO: Implement Google signup
};

const handleFacebookSignup = () => {
  console.log('Facebook signup');
  // TODO: Implement Facebook signup
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
/* Simple white background */
.signup-content {
  --background: #ffffff;
}

.signup-container {
  min-height: 100vh;
  padding: calc(24px + constant(safe-area-inset-top)) 24px calc(24px + constant(safe-area-inset-bottom));
  padding: calc(24px + env(safe-area-inset-top)) 24px calc(24px + env(safe-area-inset-bottom));
  position: relative;
}

/* Back Button */
.back-button {
  position: absolute;
  top: calc(24px + constant(safe-area-inset-top));
  top: calc(24px + env(safe-area-inset-top));
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

/* Logo Section - Seller is 40% bigger */
.logo-section {
  text-align: center;
  margin: 60px 0 24px 0;
}

.logos-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.logo-main {
  width: 100px;
  height: 100px;
  object-fit: contain;
}

.plus-sign {
  font-size: 32px;
  font-weight: 700;
  color: #000000;
  line-height: 1;
  margin: 0 2px;
}

.logo-seller {
  width: 140px; /* 100px + 40% = 140px (BIGGER!) */
  height: 140px;
  object-fit: contain;
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
  color: #4caf50;
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
  border-color: #4caf50;
}

.custom-input::placeholder {
  color: #999;
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
  accent-color: #4caf50;
}

.terms-checkbox label {
  flex: 1;
  line-height: 1.5;
}

.terms-checkbox a {
  color: #4caf50;
  text-decoration: none;
  font-weight: 600;
}

.signup-button {
  --background: #4caf50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0.5px;
  margin-bottom: 24px;
}

/* Divider */
.divider {
  display: flex;
  align-items: center;
  margin: 24px 0;
  color: #999;
  font-size: 13px;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #e0e0e0;
}

.divider span {
  padding: 0 16px;
}

/* Social Buttons */
.social-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  background: white;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
}

.social-btn:active {
  transform: scale(0.98);
}

.social-btn ion-icon {
  font-size: 18px;
}

.social-btn.google {
  color: #db4437;
}

.social-btn.google:hover {
  border-color: #db4437;
  background: #fff5f5;
}

.social-btn.facebook {
  color: #4267b2;
}

.social-btn.facebook:hover {
  border-color: #4267b2;
  background: #f0f5ff;
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
  color: #4caf50;
  text-decoration: none;
  font-weight: 600;
  margin-left: 4px;
}

/* Responsive */
@media (max-width: 480px) {
  .logos-wrapper {
    gap: 5px;
  }

  .logo-main {
    width: 80px;
    height: 80px;
  }

  .plus-sign {
    font-size: 24px;
    margin: 0 1px;
  }

  .logo-seller {
    width: 112px; /* 80px + 40% */
    height: 112px;
  }

  .title {
    font-size: 24px;
  }
}

@media (max-width: 360px) {
  .signup-container {
    padding: 20px 16px;
  }

  .logos-wrapper {
    gap: 4px;
  }

  .logo-main {
    width: 65px;
    height: 65px;
  }

  .plus-sign {
    font-size: 20px;
    margin: 0 1px;
  }

  .logo-seller {
    width: 91px; /* 65px + 40% */
    height: 91px;
  }

  .title {
    font-size: 22px;
  }
}
</style>
