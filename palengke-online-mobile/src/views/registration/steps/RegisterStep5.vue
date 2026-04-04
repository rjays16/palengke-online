<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">

        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <div class="progress-section">
          <div class="progress-bar"><div class="progress-fill" style="width: 83.3%"></div></div>
          <p class="progress-text">Step 5 of 6</p>
        </div>

        <div class="title-section">
          <div class="security-icon">
            <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
          </div>
          <h1 class="title">Secure Your Account</h1>
          <p class="subtitle">Link a social account for faster sign-in and account recovery</p>
        </div>

        <div class="form-section">

          <!-- Progress checklist -->
          <div class="security-checklist">
            <div class="check-item done">
              <div class="check-dot done">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
              </div>
              <div class="check-text">
                <span class="check-title">Basic account created</span>
                <span class="check-sub">Phone verified, details saved</span>
              </div>
            </div>
            <div class="check-connector"></div>
            <div class="check-item done">
              <div class="check-dot done">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
              </div>
              <div class="check-text">
                <span class="check-title">Password set</span>
                <span class="check-sub">Account login secured</span>
              </div>
            </div>
            <div class="check-connector"></div>
            <div class="check-item" :class="{ done: linkedAccount }">
              <div class="check-dot" :class="{ done: linkedAccount, active: !linkedAccount }">
                <ion-icon v-if="linkedAccount" :icon="checkmarkOutline"></ion-icon>
                <span v-else>3</span>
              </div>
              <div class="check-text">
                <span class="check-title">Link social account</span>
                <span class="check-sub">For easy login &amp; recovery</span>
              </div>
            </div>
          </div>

          <!-- Social options -->
          <div v-if="!linkedAccount" class="social-section">
            <p class="social-intro">Choose a social account to link (optional but recommended):</p>

            <!-- Google -->
            <button class="social-card" @click="linkSocial('google')" :disabled="isLinking">
              <div class="social-logo google-logo">G</div>
              <div class="social-info">
                <span class="social-name">Continue with Google</span>
                <span class="social-desc">Link your Google account</span>
              </div>
              <ion-icon :icon="chevronForwardOutline"></ion-icon>
            </button>

            <!-- Facebook -->
            <button class="social-card" @click="linkSocial('facebook')" :disabled="isLinking">
              <div class="social-logo facebook-logo">f</div>
              <div class="social-info">
                <span class="social-name">Continue with Facebook</span>
                <span class="social-desc">Link your Facebook account</span>
              </div>
              <ion-icon :icon="chevronForwardOutline"></ion-icon>
            </button>

            <!-- Skip -->
            <button class="skip-link" @click="handleSkip">Skip for now</button>
          </div>

          <!-- Linked success -->
          <div v-else class="linked-success">
            <div class="linked-icon">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
            </div>
            <h3>{{ linkedAccount === 'google' ? 'Google' : 'Facebook' }} linked successfully!</h3>
            <p>You can now sign in faster using your {{ linkedAccount === 'google' ? 'Google' : 'Facebook' }} account.</p>
            <ion-button expand="block" class="next-button" @click="handleNext">
              Continue
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </div>

          <!-- Benefits list -->
          <div v-if="!linkedAccount" class="benefits-card">
            <h4>Why link a social account?</h4>
            <div class="benefit-item">
              <ion-icon :icon="flashOutline"></ion-icon>
              <span>Sign in faster — no need to type your password</span>
            </div>
            <div class="benefit-item">
              <ion-icon :icon="keyOutline"></ion-icon>
              <span>Recover your account if you forget your password</span>
            </div>
            <div class="benefit-item">
              <ion-icon :icon="shieldOutline"></ion-icon>
              <span>Extra layer of security for your account</span>
            </div>
          </div>

        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, toastController } from '@ionic/vue';
import {
  arrowBackOutline, arrowForwardOutline, shieldCheckmarkOutline,
  checkmarkOutline, checkmarkCircleOutline, chevronForwardOutline,
  flashOutline, keyOutline, shieldOutline
} from 'ionicons/icons';

const router = useRouter();

const linkedAccount = ref('');
const isLinking = ref(false);

const linkSocial = async (provider) => {
  isLinking.value = true;
  try {
    await new Promise(r => setTimeout(r, 1500));
    linkedAccount.value = provider;
    const toast = await toastController.create({
      message: `${provider === 'google' ? 'Google' : 'Facebook'} account linked successfully!`,
      duration: 2500, color: 'success', position: 'top'
    });
    await toast.present();
  } finally {
    isLinking.value = false;
  }
};

const handleSkip = () => {
  const existing = JSON.parse(localStorage.getItem('buyerRegistrationData') || '{}');
  existing.linkedSocial = null;
  localStorage.setItem('buyerRegistrationData', JSON.stringify(existing));
  router.push('/register/step6');
};

const handleNext = () => {
  const existing = JSON.parse(localStorage.getItem('buyerRegistrationData') || '{}');
  existing.linkedSocial = linkedAccount.value;
  localStorage.setItem('buyerRegistrationData', JSON.stringify(existing));
  router.push('/register/step6');
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

/* Social section */
.social-section { margin-bottom: 24px; }
.social-intro { font-size: 14px; color: #555; margin: 0 0 16px 0; font-weight: 500; }

.social-card {
  display: flex; align-items: center; gap: 16px; width: 100%;
  padding: 16px; border: 2px solid #e0e0e0; border-radius: 14px;
  background: white; cursor: pointer; font-family: inherit;
  transition: all 0.2s; margin-bottom: 12px; text-align: left;
}
.social-card:hover { border-color: #4CAF50; box-shadow: 0 2px 12px rgba(76,175,80,0.12); }
.social-card:active { transform: scale(0.98); }
.social-card:disabled { opacity: 0.6; cursor: not-allowed; }
.social-card ion-icon { font-size: 20px; color: #ccc; flex-shrink: 0; }

.social-logo {
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; font-weight: 700; flex-shrink: 0;
}
.google-logo { background: #fff; border: 2px solid #e0e0e0; color: #DB4437; }
.facebook-logo { background: #1877F2; color: white; }

.social-info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.social-name { font-size: 15px; font-weight: 600; color: #1a1a1a; }
.social-desc { font-size: 12px; color: #888; }

.skip-link {
  display: block; width: 100%; padding: 14px; text-align: center;
  background: none; border: 2px dashed #e0e0e0; border-radius: 14px;
  font-size: 14px; font-weight: 600; color: #888; cursor: pointer;
  font-family: inherit; transition: all 0.2s; margin-top: 4px;
}
.skip-link:hover { border-color: #999; color: #555; }

/* Linked success */
.linked-success { text-align: center; padding: 24px 0; }
.linked-icon {
  width: 80px; height: 80px; background: #E8F5E9; border-radius: 50%;
  display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
}
.linked-icon ion-icon { font-size: 44px; color: #4CAF50; }
.linked-success h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.linked-success p { font-size: 14px; color: #666; margin: 0 0 24px 0; line-height: 1.5; }

.next-button {
  --background: #4CAF50; --background-hover: #45a049;
  --border-radius: 12px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none;
}

/* Benefits */
.benefits-card {
  background: #f8f9fa; border-radius: 16px; padding: 20px; margin-top: 8px;
}
.benefits-card h4 { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }
.benefit-item { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; font-size: 13px; color: #555; }
.benefit-item:last-child { margin-bottom: 0; }
.benefit-item ion-icon { font-size: 20px; color: #4CAF50; flex-shrink: 0; }

@media (max-width: 480px) {
  .title { font-size: 22px; }
  .step-container { padding: 20px 16px 100px 16px; }
}
</style>