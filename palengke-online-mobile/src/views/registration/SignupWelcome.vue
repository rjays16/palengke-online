<template>
  <ion-page>
    <ion-content :fullscreen="true" class="welcome-content">
      <div class="welcome-container">

        <!-- Background decoration -->
        <div class="bg-decoration">
          <div class="circle circle-1"></div>
          <div class="circle circle-2"></div>
          <div class="circle circle-3"></div>
        </div>

        <!-- Header -->
        <div class="header-section">
          <div class="logo-wrapper">
            <img src="/assets/images/logo.png" alt="Palengke Online" class="logo">
          </div>
          <h1 class="brand">Palengke Online</h1>
          <p class="tagline">Fresh produce, delivered fast 🥬</p>
        </div>

        <!-- Hero Section -->
        <div class="hero-section">
          <div class="hero-card">
            <div class="hero-icon">
              <ion-icon :icon="basketOutline"></ion-icon>
            </div>
            <div class="hero-text">
              <h2>Fresh Local Market</h2>
              <p>Shop directly from local sellers in your area</p>
            </div>
          </div>
        </div>

        <!-- Features -->
        <div class="features-section">
          <div class="feature-card">
            <div class="feature-icon green">
              <ion-icon :icon="storefrontOutline"></ion-icon>
            </div>
            <div class="feature-text">
              <h3>Local Sellers</h3>
              <p>Support your neighborhood vendors</p>
            </div>
          </div>

          <div class="feature-card">
            <div class="feature-icon blue">
              <ion-icon :icon="bicycleOutline"></ion-icon>
            </div>
            <div class="feature-text">
              <h3>Fast Delivery</h3>
              <p>Get your goods in 1-2 hours</p>
            </div>
          </div>

          <div class="feature-card">
            <div class="feature-icon orange">
              <ion-icon :icon="walletOutline"></ion-icon>
            </div>
            <div class="feature-text">
              <h3>Fair Prices</h3>
              <p>No middlemen, best deals</p>
            </div>
          </div>
        </div>

        <!-- CTA -->
        <div class="cta-section">
          <ion-button 
            expand="block" 
            color="success"
            class="create-btn" 
            @click="startRegistration"
          >
            Create Account
          </ion-button>

          <ion-button 
            expand="block" 
            fill="outline"
            color="success"
            class="signin-btn" 
            @click="goToLogin"
          >
            Sign In
          </ion-button>
        </div>

        <!-- Footer -->
        <div class="footer-section">
          <p>By continuing, you agree to our <a href="#">Terms</a> & <a href="#">Privacy Policy</a></p>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import {
  basketOutline,
  storefrontOutline,
  bicycleOutline,
  walletOutline
} from 'ionicons/icons';

const router = useRouter();

const startRegistration = () => {
  router.push('/register/step1');
};

const goToLogin = () => {
  router.push('/login');
};
</script>

<style scoped>
.welcome-content {
  --background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
}

.welcome-container {
  min-height: 100vh;
  padding: 48px 24px 32px;
  position: relative;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Background decoration */
.bg-decoration {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
  pointer-events: none;
}

.circle {
  position: absolute;
  border-radius: 50%;
  opacity: 0.15;
}

.circle-1 {
  width: 280px;
  height: 280px;
  background: white;
  top: -80px;
  right: -60px;
  animation: float 8s ease-in-out infinite;
}

.circle-2 {
  width: 180px;
  height: 180px;
  background: white;
  bottom: 20%;
  left: -40px;
  animation: float 6s ease-in-out infinite 1s;
}

.circle-3 {
  width: 120px;
  height: 120px;
  background: white;
  bottom: 5%;
  right: 20%;
  animation: float 7s ease-in-out infinite 2s;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}

/* Header */
.header-section {
  text-align: center;
  margin-bottom: 32px;
  position: relative;
  z-index: 1;
}

.logo-wrapper {
  width: 100px;
  height: 100px;
  background: white;
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.logo {
  width: 70px;
  height: 70px;
  object-fit: contain;
}

.brand {
  font-size: 32px;
  font-weight: 800;
  color: white;
  margin: 0 0 8px 0;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.tagline {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
}

/* Hero */
.hero-section {
  margin-bottom: 24px;
  position: relative;
  z-index: 1;
}

.hero-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.hero-icon {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #4CAF50, #2E7D32);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.hero-icon ion-icon {
  font-size: 32px;
  color: white;
}

.hero-text h2 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.hero-text p {
  font-size: 14px;
  color: #666;
  margin: 0;
}

/* Features */
.features-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 32px;
  position: relative;
  z-index: 1;
}

.feature-card {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 14px;
}

.feature-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.feature-icon.green {
  background: rgba(76, 175, 80, 0.2);
}

.feature-icon.green ion-icon {
  font-size: 24px;
  color: #4CAF50;
}

.feature-icon.blue {
  background: rgba(33, 150, 243, 0.2);
}

.feature-icon.blue ion-icon {
  font-size: 24px;
  color: #2196F3;
}

.feature-icon.orange {
  background: rgba(255, 152, 0, 0.2);
}

.feature-icon.orange ion-icon {
  font-size: 24px;
  color: #FF9800;
}

.feature-text h3 {
  font-size: 15px;
  font-weight: 700;
  color: white;
  margin: 0 0 2px 0;
}

.feature-text p {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.8);
  margin: 0;
}

/* CTA */
.cta-section {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.create-btn {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 700;
  text-transform: none;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.signin-btn {
  --border-radius: 12px;
  --border-width: 2px;
  --border-color: white;
  --color: white;
  height: 56px;
  font-size: 16px;
  font-weight: 700;
  text-transform: none;
}

/* Footer */
.footer-section {
  text-align: center;
  margin-top: 16px;
  position: relative;
  z-index: 1;
}

.footer-section p {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
}

.footer-section a {
  color: white;
  font-weight: 600;
  text-decoration: none;
}

@media (max-width: 480px) {
  .welcome-container {
    padding: 36px 16px 28px;
  }

  .brand {
    font-size: 26px;
  }

  .logo-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 20px;
  }

  .logo {
    width: 56px;
    height: 56px;
  }
}
</style>