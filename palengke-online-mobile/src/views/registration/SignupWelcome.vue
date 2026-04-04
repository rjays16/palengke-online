<template>
  <ion-page>
    <ion-content :fullscreen="true" class="welcome-content">
      <div class="welcome-container">

        <!-- Back -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Logo -->
        <div class="logo-section">
          <img src="/assets/images/logo.png" alt="Palengke Online" class="logo">
          <h1 class="brand">Palengke Online</h1>
          <p class="tagline">Fresh market deals delivered to your door</p>
        </div>

        <!-- Benefits -->
        <div class="benefits-section">
          <div class="benefit-row">
            <div class="benefit-icon green">
              <ion-icon :icon="cartOutline"></ion-icon>
            </div>
            <div>
              <h3>Order fresh produce</h3>
              <p>Directly from local market sellers</p>
            </div>
          </div>
          <div class="benefit-row">
            <div class="benefit-icon blue">
              <ion-icon :icon="bicycleOutline"></ion-icon>
            </div>
            <div>
              <h3>Fast delivery</h3>
              <p>Get your orders in as fast as 1-2 hours</p>
            </div>
          </div>
          <div class="benefit-row">
            <div class="benefit-icon orange">
              <ion-icon :icon="pricetagOutline"></ion-icon>
            </div>
            <div>
              <h3>Best market prices</h3>
              <p>No middlemen, straight from the palengke</p>
            </div>
          </div>
        </div>

        <!-- CTA -->
        <div class="cta-section">
          <ion-button expand="block" class="create-btn" @click="startRegistration">
            Create Free Account
          </ion-button>

          <div class="divider">
            <span>or sign up with</span>
          </div>

          <div class="social-row">
            <button class="social-btn google" @click="socialSignup('google')">
              <span class="g-logo">G</span>
              Google
            </button>
            <button class="social-btn facebook" @click="socialSignup('facebook')">
              <ion-icon :icon="logoFacebook"></ion-icon>
              Facebook
            </button>
          </div>

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
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import {
  arrowBackOutline, cartOutline, bicycleOutline,
  pricetagOutline, logoFacebook
} from 'ionicons/icons';

const router = useRouter();

const startRegistration = () => router.push('/register/step1');
const goToLogin = () => router.push('/login');
const goBack = () => router.back();
const socialSignup = (provider) => {
  console.log('Social signup with', provider);
  // TODO: Implement OAuth
};
</script>

<style scoped>
.welcome-content { --background: #ffffff; }
.welcome-container { min-height: 100vh; padding: 60px 24px 32px; position: relative; display: flex; flex-direction: column; }

.back-button {
  position: absolute; top: 24px; left: 24px; width: 40px; height: 40px;
  display: flex; align-items: center; justify-content: center;
  background: #f5f5f5; border-radius: 12px; cursor: pointer; z-index: 10;
}
.back-button:active { transform: scale(0.95); }
.back-button ion-icon { font-size: 24px; color: #1a1a1a; }

/* Logo */
.logo-section { text-align: center; margin-bottom: 40px; }
.logo { width: 80px; height: 80px; object-fit: contain; margin-bottom: 12px; }
.brand { font-size: 24px; font-weight: 700; color: #1a1a1a; margin: 0 0 6px 0; }
.tagline { font-size: 14px; color: #888; margin: 0; }

/* Benefits */
.benefits-section { flex: 1; margin-bottom: 32px; }
.benefit-row {
  display: flex; align-items: center; gap: 16px;
  padding: 16px; background: #f8f9fa; border-radius: 14px; margin-bottom: 12px;
}
.benefit-icon {
  width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.benefit-icon.green { background: #E8F5E9; }
.benefit-icon.green ion-icon { font-size: 24px; color: #4CAF50; }
.benefit-icon.blue { background: #E3F2FD; }
.benefit-icon.blue ion-icon { font-size: 24px; color: #2196F3; }
.benefit-icon.orange { background: #FFF3E0; }
.benefit-icon.orange ion-icon { font-size: 24px; color: #FF9800; }

.benefit-row h3 { font-size: 15px; font-weight: 600; color: #1a1a1a; margin: 0 0 3px 0; }
.benefit-row p { font-size: 13px; color: #888; margin: 0; }

/* CTA */
.cta-section { max-width: 480px; margin: 0 auto; width: 100%; }

.create-btn {
  --background: #4CAF50; --background-hover: #45a049;
  --border-radius: 14px; height: 56px; font-size: 16px;
  font-weight: 700; text-transform: none; margin-bottom: 24px;
}

.divider { display: flex; align-items: center; margin-bottom: 16px; font-size: 13px; color: #aaa; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e8e8e8; }
.divider span { padding: 0 14px; }

.social-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
.social-btn {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 13px; border: 2px solid #e0e0e0; border-radius: 12px;
  background: white; font-size: 14px; font-weight: 600; cursor: pointer;
  font-family: inherit; transition: all 0.2s;
}
.social-btn:active { transform: scale(0.97); }
.social-btn ion-icon { font-size: 18px; }
.g-logo { font-size: 16px; font-weight: 700; }
.social-btn.google { color: #DB4437; }
.social-btn.google:hover { border-color: #DB4437; background: #fff5f5; }
.social-btn.facebook { color: #1877F2; }
.social-btn.facebook:hover { border-color: #1877F2; background: #f0f5ff; }

.login-text { text-align: center; font-size: 14px; color: #666; margin: 0; }
.login-text a { color: #4CAF50; font-weight: 600; text-decoration: none; margin-left: 4px; }

@media (max-width: 480px) {
  .brand { font-size: 22px; }
  .welcome-container { padding: 52px 16px 28px; }
}
</style>