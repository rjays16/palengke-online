<template>
  <ion-page>
    <ion-content :fullscreen="true" class="success-content">
      <div class="success-container">

        <!-- Animated checkmark -->
        <div class="success-animation">
          <div class="ring-outer"></div>
          <div class="checkmark-circle">
            <ion-icon :icon="checkmarkOutline"></ion-icon>
          </div>
        </div>

        <!-- Welcome message -->
        <div class="welcome-section">
          <h1 class="welcome-title">Welcome to<br>Palengke Online!</h1>
          <p class="welcome-sub">Your account has been created successfully.</p>
        </div>

        <!-- Account summary -->
        <div class="summary-card">
          <div class="summary-row">
            <span class="summary-label">Name</span>
            <span class="summary-value">{{ accountData.fullName }}</span>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row">
            <span class="summary-label">Mobile</span>
            <span class="summary-value">{{ accountData.phone }}</span>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row">
            <span class="summary-label">Email</span>
            <span class="summary-value">{{ accountData.email }}</span>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row">
            <span class="summary-label">Status</span>
            <span class="badge unverified">Unverified</span>
          </div>
        </div>

        <!-- Verify CTA -->
        <div class="verify-card">
          <div class="verify-header">
            <ion-icon :icon="shieldHalfOutline"></ion-icon>
            <div>
              <h3>Verify now to unlock all features</h3>
              <p>Submit a valid ID and get your verified badge</p>
            </div>
          </div>
          <div class="features-grid">
            <div class="feature-chip">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>Higher purchase limits
            </div>
            <div class="feature-chip">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>Verified buyer badge
            </div>
            <div class="feature-chip">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>Priority support
            </div>
            <div class="feature-chip">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>Full checkout access
            </div>
          </div>
          <ion-button expand="block" class="verify-btn" @click="goToVerify">
            <ion-icon slot="start" :icon="shieldCheckmarkOutline"></ion-icon>
            Verify My Account
          </ion-button>
        </div>

        <!-- Skip / browse -->
        <div class="skip-section">
          <button class="skip-btn" @click="goToHome">
            Skip for now — Browse the market
            <ion-icon :icon="arrowForwardOutline"></ion-icon>
          </button>
          <p class="skip-note">You can verify anytime in Profile → Account Verification</p>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import {
  checkmarkOutline, checkmarkCircleOutline, shieldHalfOutline,
  shieldCheckmarkOutline, arrowForwardOutline
} from 'ionicons/icons';

const router = useRouter();

const accountData = ref({ fullName: '', phone: '', email: '' });

onMounted(() => {
  const data = JSON.parse(localStorage.getItem('buyerRegistrationData') || '{}');
  const mid = data.middleName ? ` ${data.middleName} ` : ' ';
  accountData.value.fullName = `${data.firstName || ''}${mid}${data.lastName || ''}`.trim();
  accountData.value.phone = data.phone || '';
  accountData.value.email = data.email || '';
});

const goToVerify = () => {
  router.push('/verify-account');
};

const goToHome = () => {
  localStorage.removeItem('buyerRegistrationData');
  localStorage.setItem('isAuthenticated', 'true');
  router.push('/home');
};
</script>

<style scoped>
.success-content { --background: #ffffff; }
.success-container {
  min-height: 100vh; padding: 48px 24px 40px;
  display: flex; flex-direction: column; align-items: center;
}

/* Animation */
.success-animation {
  position: relative; width: 120px; height: 120px; margin-bottom: 32px;
}
.checkmark-circle {
  width: 120px; height: 120px; border-radius: 50%;
  background: #4CAF50; display: flex; align-items: center; justify-content: center;
  animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 8px 24px rgba(76,175,80,0.3);
}
.checkmark-circle ion-icon { font-size: 64px; color: white; }

.ring-outer {
  position: absolute; top: 0; left: 0; width: 100%; height: 100%;
  border-radius: 50%; border: 3px solid #4CAF50;
  animation: expandRing 1.5s ease-out infinite; opacity: 0;
}

@keyframes popIn {
  0% { transform: scale(0); opacity: 0; }
  60% { transform: scale(1.1); }
  100% { transform: scale(1); opacity: 1; }
}
@keyframes expandRing {
  0% { transform: scale(1); opacity: 0.6; }
  100% { transform: scale(1.6); opacity: 0; }
}

/* Welcome */
.welcome-section { text-align: center; margin-bottom: 28px; }
.welcome-title { font-size: 30px; font-weight: 700; color: #1a1a1a; margin: 0 0 10px 0; line-height: 1.3; }
.welcome-sub { font-size: 15px; color: #666; margin: 0; }

/* Summary card */
.summary-card {
  width: 100%; max-width: 420px; background: #f8f9fa;
  border-radius: 16px; padding: 20px; margin-bottom: 20px;
}
.summary-row { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; }
.summary-label { font-size: 13px; color: #888; }
.summary-value { font-size: 14px; font-weight: 600; color: #1a1a1a; text-align: right; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.summary-divider { height: 1px; background: #e8e8e8; margin: 10px 0; }

.badge {
  display: inline-flex; align-items: center; padding: 4px 12px;
  border-radius: 20px; font-size: 12px; font-weight: 700;
}
.badge.unverified { background: #FFF3E0; color: #E65100; }
.badge.verified { background: #E8F5E9; color: #2E7D32; }

/* Verify card */
.verify-card {
  width: 100%; max-width: 420px; background: #E8F5E9;
  border: 2px solid #4CAF50; border-radius: 20px; padding: 24px; margin-bottom: 20px;
}
.verify-header { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 16px; }
.verify-header ion-icon { font-size: 32px; color: #4CAF50; flex-shrink: 0; margin-top: 2px; }
.verify-header h3 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.verify-header p { font-size: 13px; color: #555; margin: 0; }

.features-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 20px; }
.feature-chip {
  display: flex; align-items: center; gap: 6px;
  font-size: 12px; color: #2E7D32; font-weight: 500;
}
.feature-chip ion-icon { font-size: 16px; color: #4CAF50; flex-shrink: 0; }

.verify-btn {
  --background: #4CAF50; --background-hover: #45a049;
  --border-radius: 12px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none;
}

/* Skip */
.skip-section { text-align: center; }
.skip-btn {
  display: inline-flex; align-items: center; gap: 6px;
  background: none; border: none; font-size: 14px; font-weight: 600;
  color: #4CAF50; cursor: pointer; font-family: inherit; padding: 10px;
}
.skip-btn ion-icon { font-size: 16px; }
.skip-note { font-size: 12px; color: #aaa; margin: 8px 0 0 0; }

@media (max-width: 480px) {
  .success-container { padding: 40px 16px 32px; }
  .welcome-title { font-size: 26px; }
  .checkmark-circle { width: 100px; height: 100px; }
  .checkmark-circle ion-icon { font-size: 52px; }
  .success-animation { width: 100px; height: 100px; }
  .features-grid { grid-template-columns: 1fr; }
}
</style>