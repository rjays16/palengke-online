<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">

        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <div class="progress-section">
          <div class="progress-bar"><div class="progress-fill" style="width: 71.4%"></div></div>
          <p class="progress-text">Step 5 of 7</p>
        </div>

        <div class="title-section">
          <div class="security-icon">
            <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
          </div>
          <h1 class="title">Secure Your Account</h1>
          <p class="subtitle">Your account is almost ready</p>
        </div>

        <div class="form-section">

          <!-- Progress checklist -->
          <div class="security-checklist">
            <div class="check-item done">
              <div class="check-dot done">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
              </div>
              <div class="check-text">
                <span class="check-title">Account created</span>
                <span class="check-sub">Email and phone verified, details saved</span>
              </div>
            </div>
            <div class="check-connector"></div>
            <div class="check-item done">
              <div class="check-dot done">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
              </div>
              <div class="check-text">
                <span class="check-title">Email verified</span>
                <span class="check-sub">Email confirmed and account secured</span>
              </div>
            </div>
          </div>

          <!-- Info card -->
          <div class="info-card">
            <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
            <div class="info-content">
              <h3>Account Ready!</h3>
              <p>Your email has been verified. You can now continue to complete your registration.</p>
            </div>
          </div>

          <ion-button expand="block" class="next-button" :disabled="isSubmitting" @click="handleNext">
            <span v-if="!isSubmitting">Continue<ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon></span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>

        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, IonSpinner, toastController } from '@ionic/vue';
import {
  arrowBackOutline, arrowForwardOutline, shieldCheckmarkOutline,
  checkmarkOutline, checkmarkCircleOutline
} from 'ionicons/icons';
import { API_BASE } from '@/services/api';
import { useRegistrationStore } from '@/composables/useRegistrationStore';

const router = useRouter();
const isSubmitting = ref(false);

const handleNext = async () => {
  if (isSubmitting.value) return;
  isSubmitting.value = true;

  try {
    const { getApiPayload } = useRegistrationStore();
    const res = await fetch(`${API_BASE}/api/customer/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(getApiPayload())
    });

    const result = await res.json().catch(() => ({}));

    if (!res.ok) {
      throw new Error(result.message || 'Registration failed. Please try again.');
    }

    localStorage.setItem('customer_token', result.token);
    localStorage.setItem('isAuthenticated', 'true');
    router.push('/register/step6');
  } catch (error) {
    const toast = await toastController.create({
      message: error.message || 'Registration failed. Please try again.',
      duration: 3000,
      color: 'danger',
      position: 'top'
    });
    await toast.present();
  } finally {
    isSubmitting.value = false;
  }
};

const goBack = () => router.push('/register/step4');
</script>

<style scoped>
.step-content { --background: #ffffff; }
.step-container { min-height: 100vh; padding: 24px; padding-bottom: 100px; position: relative; }

.back-button {
  position: absolute; top: 24px; left: 24px; width: 40px; height: 40px;
  display: flex; align-items: center; justify-content: center;
  background: #f5f5f5; border-radius: 12px; cursor: pointer; z-index: 10;
}
.back-button:active { transform: scale(0.95); }
.back-button ion-icon { font-size: 24px; color: #1a1a1a; }

.progress-section { margin-top: 70px; margin-bottom: 24px; }
.progress-bar { width: 100%; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; margin-bottom: 8px; }
.progress-fill { height: 100%; background: #4CAF50; }
.progress-text { text-align: center; font-size: 13px; color: #666; margin: 0; font-weight: 600; }

.title-section { text-align: center; margin-bottom: 32px; }
.security-icon {
  width: 72px; height: 72px; background: #E8F5E9; border-radius: 50%;
  display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
}
.security-icon ion-icon { font-size: 36px; color: #4CAF50; }
.title { font-size: 26px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.subtitle { font-size: 14px; color: #666; margin: 0; line-height: 1.5; }

.form-section { max-width: 480px; margin: 0 auto; width: 100%; }

/* Checklist */
.security-checklist { margin-bottom: 28px; }
.check-item { display: flex; align-items: flex-start; gap: 14px; }
.check-connector { width: 2px; height: 20px; background: #e0e0e0; margin: 4px 0 4px 17px; }
.check-dot {
  width: 36px; height: 36px; border-radius: 50%; display: flex;
  align-items: center; justify-content: center; flex-shrink: 0;
  background: #e0e0e0; font-size: 14px; font-weight: 700; color: #999;
}
.check-dot.done { background: #4CAF50; color: white; }
.check-dot.active { background: #4CAF50; color: white; }
.check-dot ion-icon { font-size: 20px; }
.check-text { display: flex; flex-direction: column; gap: 2px; padding-top: 6px; }
.check-title { font-size: 14px; font-weight: 600; color: #1a1a1a; }
.check-sub { font-size: 12px; color: #888; }

/* Info card */
.info-card {
  display: flex; align-items: flex-start; gap: 16px;
  padding: 20px; background: #E8F5E9; border-radius: 16px;
  border-left: 4px solid #4CAF50;
}
.info-card ion-icon {
  font-size: 32px; color: #4CAF50; flex-shrink: 0;
}
.info-content h3 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.info-content p { font-size: 13px; color: #555; margin: 0; line-height: 1.5; }

.next-button {
  --background: #4CAF50; --background-hover: #45a049;
  --border-radius: 12px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none;
  margin-top: 24px;
}

@media (max-width: 480px) {
  .title { font-size: 22px; }
  .step-container { padding: 20px 16px 100px 16px; }
}
</style>