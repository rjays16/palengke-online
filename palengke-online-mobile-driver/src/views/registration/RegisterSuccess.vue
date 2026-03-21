<template>
  <ion-page>
    <ion-content :fullscreen="true" class="success-content">
      <div class="success-container">
        
        <!-- Success Animation -->
        <div class="success-animation">
          <div class="checkmark-circle">
            <ion-icon :icon="checkmarkOutline"></ion-icon>
          </div>
          <div class="pulse-ring"></div>
        </div>

        <!-- Success Message -->
        <div class="success-message">
          <h1>Registration Complete!</h1>
          <p>Your driver application has been submitted successfully. Our team will review your documents and get back to you within 24-48 hours.</p>
        </div>

        <!-- Info Cards -->
        <div class="info-cards">
          <div class="info-card">
            <ion-icon :icon="timeOutline"></ion-icon>
            <div class="info-text">
              <h3>Review Process</h3>
              <p>1-2 business days</p>
            </div>
          </div>

          <div class="info-card">
            <ion-icon :icon="mailOutline"></ion-icon>
            <div class="info-text">
              <h3>Check Your Email</h3>
              <p>Updates will be sent to your email</p>
            </div>
          </div>

          <div class="info-card">
            <ion-icon :icon="chatbubblesOutline"></ion-icon>
            <div class="info-text">
              <h3>Need Help?</h3>
              <p>Contact support anytime</p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-section">
          <ion-button 
            expand="block" 
            class="home-button"
            @click="goToHome"
          >
            Go to Home
          </ion-button>

          <ion-button 
            expand="block" 
            fill="outline"
            class="login-button"
            @click="goToLogin"
          >
            Back to Login
          </ion-button>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { 
  IonPage, 
  IonContent, 
  IonButton, 
  IonIcon
} from '@ionic/vue';
import { 
  checkmarkOutline,
  timeOutline,
  mailOutline,
  chatbubblesOutline
} from 'ionicons/icons';

const router = useRouter();

const goToHome = () => {
  router.push('/home');
};

const goToLogin = () => {
  router.push('/login');
};
</script>

<style scoped>
.success-content {
  --background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.success-container {
  min-height: 100vh;
  padding: 40px 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

/* Success Animation */
.success-animation {
  position: relative;
  width: 140px;
  height: 140px;
  margin-bottom: 40px;
}

.checkmark-circle {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  animation: scaleIn 0.5s ease-out;
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.3);
}

.checkmark-circle ion-icon {
  font-size: 72px;
  color: white;
  animation: checkmarkDraw 0.4s ease-out 0.3s both;
}

.pulse-ring {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 3px solid #4CAF50;
  animation: pulseRing 1.5s ease-out infinite;
}

/* Success Message */
.success-message {
  margin-bottom: 40px;
}

.success-message h1 {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.success-message p {
  font-size: 15px;
  color: #666;
  margin: 0;
  line-height: 1.6;
  max-width: 320px;
}

/* Info Cards */
.info-cards {
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: 100%;
  max-width: 320px;
  margin-bottom: 40px;
}

.info-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  text-align: left;
}

.info-card ion-icon {
  font-size: 28px;
  color: #4CAF50;
  flex-shrink: 0;
}

.info-text h3 {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 2px 0;
}

.info-text p {
  font-size: 13px;
  color: #666;
  margin: 0;
}

/* Action Buttons */
.action-section {
  width: 100%;
  max-width: 320px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.home-button {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
}

.login-button {
  --border-radius: 12px;
  --border-color: #4CAF50;
  --color: #4CAF50;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
}

/* Animations */
@keyframes scaleIn {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes checkmarkDraw {
  0% {
    opacity: 0;
    transform: scale(0.5);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes pulseRing {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(1.5);
    opacity: 0;
  }
}

/* Responsive */
@media (max-width: 480px) {
  .success-container {
    padding: 32px 20px;
  }

  .success-message h1 {
    font-size: 24px;
  }

  .checkmark-circle {
    width: 120px;
    height: 120px;
  }

  .checkmark-circle ion-icon {
    font-size: 60px;
  }
}
</style>
