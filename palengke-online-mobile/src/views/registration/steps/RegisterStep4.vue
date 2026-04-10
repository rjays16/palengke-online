<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">

        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <div class="progress-section">
          <div class="progress-bar"><div class="progress-fill" style="width: 57.12%"></div></div>
          <p class="progress-text">Step 4 of 7</p>
        </div>

        <div class="title-section">
          <h1 class="title">Terms & Conditions</h1>
          <p class="subtitle">Please review and accept to continue</p>
        </div>

        <div class="form-section">

          <!-- Terms & Conditions -->
          <div class="terms-section">
            <div class="checkbox-row">
              <input type="checkbox" id="terms" v-model="acceptedTerms" class="checkbox-input">
              <label for="terms">
                I agree to the
                <a href="#" @click.prevent="openTerms">Terms & Conditions</a>
                and
                <a href="#" @click.prevent="openPrivacy">Privacy Policy</a>
              </label>
            </div>
            <div class="checkbox-row" style="margin-top: 12px;">
              <input type="checkbox" id="marketing" v-model="acceptMarketing" class="checkbox-input">
              <label for="marketing">
                Receive updates, promotions and market deals via SMS/email
                <span class="optional">(Optional)</span>
              </label>
            </div>
          </div>

          <div class="security-note">
            <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
            <div>
              <strong>Your data is secure</strong><br>
              Your personal information is encrypted and never shared with third parties.
            </div>
          </div>

          <div class="button-group">
            <ion-button expand="block" fill="outline" color="medium" class="back-btn" @click="goBack">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>Back
            </ion-button>
            <ion-button expand="block" class="next-button" :disabled="!isFormValid" @click="handleNext">
              Next<ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </div>

        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, alertController } from '@ionic/vue';
import {
  arrowBackOutline, arrowForwardOutline,
  shieldCheckmarkOutline
} from 'ionicons/icons';

const router = useRouter();

const acceptedTerms = ref(false);
const acceptMarketing = ref(false);

const isFormValid = computed(() => acceptedTerms.value);

const openTerms = async () => {
  const alert = await alertController.create({
    header: 'Terms & Conditions',
    message: 'By creating a Palengke Online account, you agree to our terms of service. You must be at least 18 years old. We may update these terms from time to time.',
    buttons: ['Close']
  });
  await alert.present();
};

const openPrivacy = async () => {
  const alert = await alertController.create({
    header: 'Privacy Policy',
    message: 'We collect personal information to provide our services. Your data is encrypted and never sold to third parties. We comply with the Data Privacy Act of 2012 (RA 10173).',
    buttons: ['Close']
  });
  await alert.present();
};

const goBack = () => router.push('/register/step3');

const handleNext = () => {
  if (!isFormValid.value) return;
  const existing = JSON.parse(localStorage.getItem('buyerRegistrationData') || '{}');
  existing.acceptMarketing = acceptMarketing.value;
  existing.termsAccepted = true;
  localStorage.setItem('buyerRegistrationData', JSON.stringify(existing));
  router.push('/register/step5');
};
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

.progress-section { margin-top: 70px; margin-bottom: 32px; }
.progress-bar { width: 100%; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; margin-bottom: 8px; }
.progress-fill { height: 100%; background: #4CAF50; }
.progress-text { text-align: center; font-size: 13px; color: #666; margin: 0; font-weight: 600; }

.title-section { text-align: center; margin-bottom: 40px; }
.title { font-size: 28px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.subtitle { font-size: 15px; color: #666; margin: 0; }

.form-section { max-width: 480px; margin: 0 auto; width: 100%; }

/* Terms */
.terms-section { margin: 28px 0; }
.checkbox-row { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #555; }
.checkbox-input { width: 18px; height: 18px; accent-color: #4CAF50; cursor: pointer; flex-shrink: 0; margin-top: 2px; }
.checkbox-row label { flex: 1; line-height: 1.5; cursor: pointer; }
.checkbox-row a { color: #4CAF50; font-weight: 600; text-decoration: none; }
.optional { color: #999; font-weight: 400; }

/* Security note */
.security-note {
  display: flex; gap: 12px; background: #E8F5E9; border-left: 4px solid #4CAF50;
  padding: 16px; border-radius: 8px; margin-bottom: 24px;
  font-size: 13px; color: #2E7D32; line-height: 1.5;
}
.security-note ion-icon { font-size: 22px; color: #4CAF50; flex-shrink: 0; margin-top: 2px; }
.security-note strong { font-weight: 700; }

.button-group { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.back-btn { --border-radius: 12px; --border-width: 2px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none; }
.next-button { --background: #4CAF50; --background-hover: #45a049; --border-radius: 12px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none; }
.next-button:disabled { --background: #e0e0e0; --color: #999; }

@media (max-width: 480px) {
  .title { font-size: 24px; }
  .step-container { padding: 20px 16px 100px 16px; }
  .button-group { grid-template-columns: 1fr; }
  .back-btn { order: 2; }
  .next-button { order: 1; }
}
</style>