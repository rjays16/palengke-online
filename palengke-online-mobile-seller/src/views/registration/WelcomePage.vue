<template>
  <ion-page>
    <ion-content :fullscreen="true" class="welcome-content">
      <div class="welcome-container">
        <!-- Logo Section -->
        <div class="logo-section">
          <div class="logos-wrapper">
            <img src="/assets/images/logo.png" alt="Palengke Online" class="logo-main" />
            <div class="plus-sign">+</div>
            <img src="/assets/images/palengke-seller-boy.png" alt="Seller" class="logo-seller" />
          </div>
          <h1 class="portal-title">Seller Portal</h1>
          <p class="portal-subtitle">Empowering Filipino Sellers</p>
        </div>

        <!-- Welcome Message -->
        <div class="welcome-message">
          <h2>Welcome to Palengke Online!</h2>
          <p>Start selling fresh produce and reach more customers in your community.</p>
        </div>

        <!-- Features -->
        <div class="features-section">
          <div class="feature-item">
            <div class="feature-icon">
              <ion-icon :icon="storefrontOutline"></ion-icon>
            </div>
            <div class="feature-text">
              <h3>Easy Store Management</h3>
              <p>Manage products & orders</p>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon">
              <ion-icon :icon="cashOutline"></ion-icon>
            </div>
            <div class="feature-text">
              <h3>Flexible Payments</h3>
              <p>GCash, Bank, Paypal, Stripe, or COD</p>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon">
              <ion-icon :icon="peopleOutline"></ion-icon>
            </div>
            <div class="feature-text">
              <h3>Reach More Customers</h3>
              <p>Grow your business online</p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <ion-button expand="block" color="success" class="primary-button" @click="goToRegister">
            Create Account
          </ion-button>

          <ion-button expand="block" fill="outline" color="success" class="secondary-button" @click="goToLogin">
            Sign In
          </ion-button>
        </div>

        <!-- Help Link -->
        <div class="help-section">
          <p>Need help? <a href="#" @click.prevent="contactSupport">Contact Support</a></p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, toastController } from '@ionic/vue';
import { storefrontOutline, cashOutline, peopleOutline } from 'ionicons/icons';

const router = useRouter();

const goToRegister = () => {
  router.push('/register/step1');
};

const goToLogin = () => {
  router.push('/login');
};

const contactSupport = async () => {
  const toast = await toastController.create({
    message: '<font-awesome-icon icon="phone" /> Contact: support@palengke-online.com',
    duration: 3000,
    color: 'dark',
    position: 'top',
  });
  await toast.present();
};
</script>

<style scoped>
.welcome-content {
  --background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
}

.welcome-container {
  min-height: 100vh;
  padding: 40px 24px 24px 24px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* Logo Section */
.logo-section {
  text-align: center;
  margin-bottom: 40px;
}

.logos-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 20px;
}

.logo-main {
  width: 130px; /* Increased from 100px */
  height: 130px;
  object-fit: contain;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.plus-sign {
  font-size: 40px; /* Increased from 32px */
  font-weight: 700;
  color: white;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.logo-seller {
  width: 182px; /* Increased from 140px (130px + 40%) */
  height: 182px;
  object-fit: contain;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.portal-title {
  font-size: 32px;
  font-weight: 700;
  color: white;
  margin: 0 0 8px 0;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.portal-subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.95);
  margin: 0;
}

/* Welcome Message */
.welcome-message {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 24px;
  text-align: center;
  margin-bottom: 32px;
}

.welcome-message h2 {
  font-size: 22px;
  font-weight: 700;
  color: white;
  margin: 0 0 12px 0;
}

.welcome-message p {
  font-size: 15px;
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
  line-height: 1.6;
}

/* Features */
.features-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 40px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 16px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 16px;
}

.feature-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.feature-icon ion-icon {
  font-size: 28px;
  color: #4caf50;
}

.feature-text {
  flex: 1;
}

.feature-text h3 {
  font-size: 16px;
  font-weight: 700;
  color: white;
  margin: 0 0 4px 0;
}

.feature-text p {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.85);
  margin: 0;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 20px;
}

.primary-button {
  --border-radius: 12px;
  --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  height: 56px;
  font-size: 16px;
  font-weight: 700;
  text-transform: none;
}

.secondary-button {
  --border-radius: 12px;
  --border-width: 2px;
  --border-color: white;
  --color: white;
  height: 56px;
  font-size: 16px;
  font-weight: 700;
  text-transform: none;
}

/* Help Section */
.help-section {
  text-align: center;
  padding-bottom: 20px;
}

.help-section p {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
}

.help-section a {
  color: white;
  text-decoration: underline;
  font-weight: 600;
}

/* Responsive */
@media (max-width: 480px) {
  .logo-main {
    width: 110px; /* Increased from 80px */
    height: 110px;
  }

  .logo-seller {
    width: 154px; /* Increased from 112px (110px + 40%) */
    height: 154px;
  }

  .portal-title {
    font-size: 28px;
  }

  .plus-sign {
    font-size: 34px; /* Increased from 24px */
  }
}
</style>
