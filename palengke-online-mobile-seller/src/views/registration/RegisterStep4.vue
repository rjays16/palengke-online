<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">
        
        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-section">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 57.12%"></div>
          </div>
          <p class="progress-text">Step 4 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Account Verification</h1>
          <p class="subtitle">Choose how you want to proceed</p>
        </div>

        <!-- Verification Section -->
        <div class="verification-section">

          <!-- Info Message -->
          <div class="info-message">
            <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
            <div class="info-content">
              <h3>Why Verify Your Account?</h3>
              <p>Verification helps build trust with customers and unlocks all features of Palengke Online.</p>
            </div>
          </div>

          <!-- Option 1: Verify Now -->
          <div class="option-card verify" @click="selectOption('verify')">
            <div class="option-header">
              <div class="option-icon verified">
                <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              </div>
              <div class="option-title">
                <h2>Verify Now</h2>
                <span class="badge recommended">Recommended</span>
              </div>
            </div>
            
            <p class="option-description">
              Get your account fully verified by providing a valid government-issued ID.
            </p>

            <div class="benefits-list">
              <div class="benefit-item">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
                <span>Full access to all features</span>
              </div>
              <div class="benefit-item">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
                <span>Verified seller badge</span>
              </div>
              <div class="benefit-item">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
                <span>Higher transaction limits</span>
              </div>
              <div class="benefit-item">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
                <span>Build customer trust faster</span>
              </div>
              <div class="benefit-item">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
                <span>Priority support access</span>
              </div>
              <div class="benefit-item">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
                <span>Faster approval for listings</span>
              </div>
            </div>

            <div class="time-estimate">
              <ion-icon :icon="timeOutline"></ion-icon>
              <span>Takes about 3-5 minutes</span>
            </div>
          </div>

          <!-- Option 2: Skip for Now -->
          <div class="option-card skip" @click="selectOption('skip')">
            <div class="option-header">
              <div class="option-icon unverified">
                <ion-icon :icon="alertCircleOutline"></ion-icon>
              </div>
              <div class="option-title">
                <h2>Skip for Now</h2>
              </div>
            </div>
            
            <p class="option-description">
              Create your account without verification. You can verify anytime later in Settings.
            </p>

            <div class="limitations-list">
              <div class="limitation-item">
                <ion-icon :icon="removeCircleOutline"></ion-icon>
                <span>Basic account features only</span>
              </div>
              <div class="limitation-item">
                <ion-icon :icon="removeCircleOutline"></ion-icon>
                <span>Limited transaction amounts</span>
              </div>
              <div class="limitation-item">
                <ion-icon :icon="removeCircleOutline"></ion-icon>
                <span>No verified badge</span>
              </div>
              <div class="limitation-item">
                <ion-icon :icon="informationCircleOutline"></ion-icon>
                <span>Can verify later in Settings</span>
              </div>
            </div>
          </div>

          <!-- Security Note -->
          <div class="security-note">
            <ion-icon :icon="lockClosedOutline"></ion-icon>
            <div class="security-text">
              <strong>Your Privacy Matters</strong><br>
              All verification documents are encrypted and securely stored. Your information will only be used for identity verification and will never be shared with third parties.
            </div>
          </div>

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
  IonIcon,
  alertController
} from '@ionic/vue';
import {
  arrowBackOutline,
  shieldCheckmarkOutline,
  checkmarkCircleOutline,
  checkmarkOutline,
  alertCircleOutline,
  removeCircleOutline,
  informationCircleOutline,
  timeOutline,
  lockClosedOutline
} from 'ionicons/icons';

const router = useRouter();

// Navigation
const goBack = () => {
  router.push('/register/step3');
};

const selectOption = async (option) => {
  if (option === 'verify') {
    // Show warning before proceeding to ID verification
    const alert = await alertController.create({
      header: 'Important Information',
      message: 'Before proceeding, please prepare: Valid Government-Issued ID, Good Lighting, and Clear Background. Your information will be encrypted and secured, used only for verification, not shared with third parties, and handled by designated recipients only. This is for your account security and to comply with Know Your Customer (KYC) regulations.',
      cssClass: 'verification-warning-modal',
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'alert-button-cancel',
          handler: () => {
            // User cancelled, stay on this page
          }
        },
        {
          text: 'I Understand, Continue',
          cssClass: 'alert-button-confirm',
          handler: () => {
            // Save verification choice
            const existingData = JSON.parse(localStorage.getItem('registrationData') || '{}');
            existingData.verificationChoice = 'verify';
            localStorage.setItem('registrationData', JSON.stringify(existingData));
            
            // Navigate to Step 5 (Select ID Type)
            router.push('/register/step5');
          }
        }
      ]
    });
    await alert.present();
    
  } else if (option === 'skip') {
    // Show confirmation for skipping
    const alert = await alertController.create({
      header: 'Skip Verification?',
      message: 'You can verify your account anytime later in Settings to unlock all features and build customer trust.',
      cssClass: 'skip-confirmation-modal',
      buttons: [
        {
          text: 'Go Back',
          role: 'cancel',
          cssClass: 'alert-button-cancel'
        },
        {
          text: 'Skip & Continue',
          cssClass: 'alert-button-confirm',
          handler: () => {
            // Save verification choice
            const existingData = JSON.parse(localStorage.getItem('registrationData') || '{}');
            existingData.verificationChoice = 'skip';
            existingData.verificationStatus = 'not_verified';
            localStorage.setItem('registrationData', JSON.stringify(existingData));
            
            // Navigate to Success page (not verified)
            router.push('/register/success');
          }
        }
      ]
    });
    await alert.present();
  }
};
</script>

<style scoped>
.step-content {
  --background: var(--color-bg-primary);
}

.step-container {
  min-height: 100vh;
  padding: 24px;
  padding-bottom: 100px;
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
  background: var(--color-card-bg);
  border-radius: 12px;
  cursor: pointer;
  z-index: 10;
  transition: background 0.2s;
  box-shadow: 0 2px 8px var(--color-card-shadow);
}

.back-button:active {
  transform: scale(0.95);
  background: var(--color-bg-tertiary);
}

.back-button ion-icon {
  font-size: 24px;
  color: var(--color-text-primary);
}

/* Progress Section */
.progress-section {
  margin-top: 70px;
  margin-bottom: 32px;
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: var(--color-border);
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: var(--color-primary);
  transition: width 0.3s ease;
}

.progress-text {
  text-align: center;
  font-size: 13px;
  color: var(--color-text-secondary);
  margin: 0;
  font-weight: 600;
}

/* Title Section */
.title-section {
  text-align: center;
  margin-bottom: 32px;
}

.title {
  font-size: 28px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 15px;
  color: var(--color-text-secondary);
  margin: 0;
}

/* Verification Section */
.verification-section {
  max-width: 600px;
  margin: 0 auto;
  width: 100%;
}

/* Info Message */
.info-message {
  display: flex;
  gap: 16px;
  background: var(--color-success-bg);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 24px;
  border: 2px solid var(--color-success-border);
}

.info-message > ion-icon {
  font-size: 32px;
  color: var(--color-success-border);
  flex-shrink: 0;
}

.info-content h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-success-text);
  margin: 0 0 8px 0;
}

.info-content p {
  font-size: 14px;
  color: var(--color-success-text);
  margin: 0;
  line-height: 1.5;
}

/* Option Cards */
.option-card {
  background: var(--color-card-bg);
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 20px;
  cursor: pointer;
  transition: all 0.3s;
  border: 3px solid transparent;
  box-shadow: 0 4px 12px var(--color-card-shadow);
}

.option-card:active {
  transform: scale(0.98);
}

.option-card.verify {
  border-color: var(--color-primary);
  background: var(--color-success-bg);
}

.option-card.skip {
  border-color: var(--color-border);
}

.option-card.verify:hover {
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.2);
  transform: translateY(-2px);
}

.option-card.skip:hover {
  box-shadow: 0 8px 24px var(--color-card-shadow);
  transform: translateY(-2px);
}

/* Option Header */
.option-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}

.option-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.option-icon.verified {
  background: var(--color-primary);
}

.option-icon.unverified {
  background: var(--color-warning);
}

.option-icon ion-icon {
  font-size: 32px;
  color: var(--color-text-inverse);
}

.option-title {
  flex: 1;
}

.option-title h2 {
  font-size: 20px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 4px 0;
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.badge.recommended {
  background: var(--color-primary);
  color: var(--color-text-inverse);
}

/* Option Description */
.option-description {
  font-size: 14px;
  color: var(--color-text-secondary);
  margin: 0 0 20px 0;
  line-height: 1.6;
}

/* Benefits List */
.benefits-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.benefit-item ion-icon {
  font-size: 20px;
  color: var(--color-primary);
  flex-shrink: 0;
}

.benefit-item span {
  font-size: 14px;
  color: var(--color-text-primary);
  font-weight: 500;
}

/* Limitations List */
.limitations-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.limitation-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.limitation-item ion-icon {
  font-size: 20px;
  flex-shrink: 0;
}

.limitation-item:not(:last-child) ion-icon {
  color: var(--color-error);
}

.limitation-item:last-child ion-icon {
  color: var(--color-info);
}

.limitation-item span {
  font-size: 14px;
  color: var(--color-text-secondary);
}

/* Time Estimate */
.time-estimate {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: var(--color-success-bg);
  border-radius: 12px;
  margin-top: 16px;
}

.time-estimate ion-icon {
  font-size: 20px;
  color: var(--color-success-border);
}

.time-estimate span {
  font-size: 13px;
  color: var(--color-success-text);
  font-weight: 600;
}

/* Security Note */
.security-note {
  display: flex;
  gap: 16px;
  background: var(--color-info-bg);
  border-left: 4px solid var(--color-info-border);
  border-radius: 12px;
  padding: 20px;
  margin-top: 24px;
}

.security-note ion-icon {
  font-size: 28px;
  color: var(--color-info-border);
  flex-shrink: 0;
  margin-top: 2px;
}

.security-text {
  font-size: 13px;
  color: var(--color-info-text);
  line-height: 1.6;
}

.security-text strong {
  font-weight: 700;
  display: block;
  margin-bottom: 4px;
}

/* Alert Modals */
:deep(.verification-warning-modal .alert-wrapper) {
  border-radius: 20px;
  max-width: 400px;
  background: var(--color-modal-bg);
}

:deep(.verification-warning-modal .alert-message) {
  font-size: 14px;
  line-height: 1.6;
  text-align: left;
  color: var(--color-modal-text);
}

:deep(.skip-confirmation-modal .alert-wrapper) {
  border-radius: 20px;
  max-width: 360px;
  background: var(--color-modal-bg);
}

:deep(.alert-button-cancel) {
  color: var(--color-text-secondary) !important;
}

:deep(.alert-button-confirm) {
  color: var(--color-primary) !important;
  font-weight: 700 !important;
}

/* Responsive */
@media (max-width: 480px) {
  .title {
    font-size: 24px;
  }

  .step-container {
    padding: 20px 16px 100px 16px;
  }

  .option-card {
    padding: 20px;
  }
}
</style>