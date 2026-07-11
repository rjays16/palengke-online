<template>
  <ion-page>
    <ion-content :fullscreen="true" class="success-content">
      <!-- Loading overlay for skip submission -->
      <div v-if="isSubmitting" class="loading-overlay">
        <div class="loading-card">
          <div class="loading-spinner"></div>
          <h3>Creating your account...</h3>
          <p>Please wait while we set up your seller account.</p>
        </div>
      </div>

      <!-- Error overlay -->
      <div v-if="submitError" class="loading-overlay">
        <div class="loading-card">
          <div class="error-icon">!</div>
          <h3>Submission Failed</h3>
          <p>{{ submitError }}</p>
          <ion-button expand="block" class="retry-btn" @click="submitRegistration">
            <ion-icon slot="start" :icon="refreshOutline"></ion-icon>
            Try Again
          </ion-button>
          <ion-button expand="block" fill="clear" class="back-btn" @click="goToLogin">
            Back to Login
          </ion-button>
        </div>
      </div>

      <div v-if="!isSubmitting && !submitError" class="success-container">
        <!-- Success Animation/Icon -->
        <div class="success-icon">
          <div class="icon-circle icon-submitted">
            <ion-icon :icon="timeOutline"></ion-icon>
          </div>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Registration Submitted!</h1>
          <p class="subtitle">Your account is pending admin approval</p>
        </div>

        <!-- Status Badge -->
        <div class="status-badge status-pending">
          <ion-icon :icon="timeOutline"></ion-icon>
          <span>Pending Approval</span>
        </div>

        <!-- Account Summary -->
        <div class="summary-card">
          <h3>Account Details</h3>
          <div class="summary-item">
            <span class="label">Name:</span>
            <span class="value">{{ accountData.fullName }}</span>
          </div>
          <div class="summary-item">
            <span class="label">Email:</span>
            <span class="value">{{ accountData.email }}</span>
          </div>
          <div class="summary-item">
            <span class="label">Phone:</span>
            <span class="value">{{ accountData.phone }}</span>
          </div>
          <div class="summary-item">
            <span class="label">Store:</span>
            <span class="value">{{ accountData.storeName }}</span>
          </div>
          <div class="summary-item">
            <span class="label">Verification:</span>
            <span class="value" :class="{ verified: isVerified, unverified: !isVerified }">
              {{ isVerified ? 'Submitted with ID' : 'Skipped — verify later' }}
            </span>
          </div>
        </div>

        <!-- Verified Note -->
        <div v-if="isVerified" class="info-card info-success">
          <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
          <span>ID documents submitted successfully. You can verify anytime in Settings later.</span>
        </div>

        <!-- Not Verified Note -->
        <div v-else class="info-card info-warning">
          <ion-icon :icon="informationCircleOutline"></ion-icon>
          <span>You skipped ID verification. You can verify later in Settings to unlock all features.</span>
        </div>

        <!-- What Happens Next -->
        <div class="next-steps">
          <h3>What Happens Next?</h3>
          <div class="steps-list">
            <div class="step-item">
              <div class="step-number">1</div>
              <div class="step-content">
                <strong>Admin Reviews Your Account</strong>
                <p>Our team will check your registration details</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-number">2</div>
              <div class="step-content">
                <strong>Receive Email Notification</strong>
                <p>We'll email you once your account is approved</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-number">3</div>
              <div class="step-content">
                <strong>Log In & Start Selling</strong>
                <p>Set up your store and start receiving orders</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <ion-button expand="block" class="login-button" @click="goToLogin">
            Go to Login
            <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
          </ion-button>
        </div>

        <!-- Support Link -->
        <div class="support-link">
          <p>Need help? <a href="#" @click.prevent="contactSupport">Contact Support</a></p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, toastController } from '@ionic/vue';
import {
  checkmarkCircleOutline,
  informationCircleOutline,
  arrowForwardOutline,
  refreshOutline,
  timeOutline,
} from 'ionicons/icons';
import { api } from '@/utils/apiClient';
import { useRegistrationStore } from '@/stores/registration';
import { type AccountSummary } from '@/types';

const router = useRouter();
const reg = useRegistrationStore();

// Account data
const accountData = ref({
  fullName: '',
  email: '',
  phone: '',
  storeName: '',
  verificationStatus: 'not_verified',
} as AccountSummary);

// Submission state
const isSubmitting = ref(false);
const submitError = ref('');

// Computed: Is account verified?
const isVerified = computed(() => {
  return accountData.value.verificationStatus === 'verified';
});

// Submit registration to backend
const submitRegistration = async () => {
  reg.load();
  const d = reg.getAll();
  if (!d.email) return;

  isSubmitting.value = true;
  submitError.value = '';

  try {
    await api.post('/seller/register', {
        email: d.email,
        country_code: (d as any).countryCode,
        phone: d.phone,
        full_phone: (d as any).fullPhone,
        email_verified: (d as any).emailVerified,
        first_name: d.firstName,
        middle_name: d.middleName || '',
        last_name: d.lastName,
        country: d.country,
        birthdate: d.birthdate,
        birthplace: d.birthplace,
        current_address: d.currentAddress,
        store_name: d.storeName,
        region: d.region,
        city: d.city,
        barangay: d.barangay,
        store_place: d.storePlace,
        complete_address: d.completeAddress,
        verification_choice: d.verificationChoice,
        verification_status: (d as any).verificationStatus,
        id_type: d.idType || null,
        id_type_name: (d as any).idTypeName || null,
        id_front_photo: (d as any).idFrontPhoto || null,
        id_back_photo: (d as any).idBackPhoto || null,
        selfie_photo: (d as any).selfiePhoto || null,
      }, { skipAuth: true });

    (reg as any).setFields({ verificationSubmittedAt: new Date().toISOString() });

    isSubmitting.value = false;

    showSuccessToast();
  } catch (error: any) {
    isSubmitting.value = false;
    submitError.value = error.message || 'Something went wrong. Please try again.';
  }
};

// Load account data
onMounted(() => {
  reg.load();

  // Build full name
  const middle = reg.data.middleName ? ` ${reg.data.middleName} ` : ' ';
  accountData.value.fullName = `${reg.data.firstName}${middle}${reg.data.lastName}`;

  accountData.value.email = reg.data.email || '';
  accountData.value.phone = reg.data.phone || '';
  accountData.value.storeName = reg.data.storeName || '';
  accountData.value.verificationStatus = (reg.data as any).verificationStatus || 'not_verified';

  // If skipped verification, submit registration now
  if (reg.data.verificationChoice === 'skip') {
    submitRegistration();
  }
});

const goToLogin = () => {
  reg.clearAll();
  showSuccessToast();
  setTimeout(() => {
    router.push('/login');
  }, 1000);
};

const showSuccessToast = async () => {
  const toast = await toastController.create({
    message: 'Registration submitted! Please login to check your approval status.',
    duration: 3000,
    color: 'success',
    position: 'top',
  });
  await toast.present();
};

const contactSupport = async () => {
  const toast = await toastController.create({
    message: 'Support: support@palengke-online.com',
    duration: 3000,
    color: 'primary',
    position: 'bottom',
  });
  await toast.present();
};
</script>

<style scoped>
.success-content {
  --background: var(--color-bg-primary);
}

.success-container {
  min-height: 100vh;
  padding: 40px 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Success Icon */
.success-icon {
  margin-bottom: 24px;
}

.icon-circle {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: scaleIn 0.5s ease-out;
}

.icon-circle.icon-submitted {
  background: linear-gradient(135deg, #e0f2fe 0%, #7dd3fc 100%);
  box-shadow: 0 8px 24px rgba(14, 165, 233, 0.3);
}

.icon-circle.icon-submitted ion-icon {
  font-size: 52px;
  color: #0369a1;
}

@keyframes scaleIn {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Title Section */
.title-section {
  text-align: center;
  margin-bottom: 24px;
  max-width: 500px;
}

.title {
  font-size: 32px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 16px;
  color: var(--color-text-secondary);
  margin: 0;
  line-height: 1.5;
}

/* Status Badge */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 20px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 28px;
}

.status-badge.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-badge ion-icon {
  font-size: 18px;
}

/* Summary Card */
.summary-card {
  width: 100%;
  background: var(--color-card-bg);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: var(--card-shadow);
}

.summary-card h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--color-border);
}

.summary-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
}

.summary-item .label {
  font-size: 14px;
  color: var(--color-text-secondary);
}

.summary-item .value {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-primary);
  text-align: right;
}

.summary-item .value.verified {
  color: var(--color-primary);
}

.summary-item .value.unverified {
  color: #d97706;
}

/* Info Cards */
.info-card {
  width: 100%;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 14px 16px;
  border-radius: 12px;
  font-size: 13px;
  line-height: 1.5;
  margin-bottom: 24px;
}

.info-card ion-icon {
  font-size: 18px;
  flex-shrink: 0;
  margin-top: 1px;
}

.info-card.info-success {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #166534;
}

.info-card.info-success ion-icon {
  color: #16a34a;
}

.info-card.info-warning {
  background: #fffbeb;
  border: 1px solid #fde68a;
  color: #92400e;
}

.info-card.info-warning ion-icon {
  color: #d97706;
}

/* Next Steps */
.next-steps {
  width: 100%;
  margin-bottom: 32px;
}

.next-steps h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 16px;
}

.steps-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.step-item {
  display: flex;
  gap: 14px;
  align-items: flex-start;
}

.step-number {
  width: 30px;
  height: 30px;
  background: #e0f2fe;
  color: #0369a1;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
  flex-shrink: 0;
}

.step-content {
  flex: 1;
}

.step-content strong {
  font-size: 14px;
  color: var(--color-text-primary);
  display: block;
  margin-bottom: 2px;
}

.step-content p {
  font-size: 13px;
  color: var(--color-text-secondary);
  margin: 0;
}

/* Action Buttons */
.action-buttons {
  width: 100%;
  margin-bottom: 24px;
}

.login-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 14px;
  height: 50px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
  margin: 0;
}

/* Support Link */
.support-link {
  text-align: center;
  margin-top: 16px;
}

.support-link p {
  font-size: 14px;
  color: var(--color-text-secondary);
  margin: 0;
}

.support-link a {
  color: var(--color-link);
  text-decoration: none;
  font-weight: 600;
}

/* Responsive */
@media (max-width: 480px) {
  .success-container {
    padding: 32px 16px;
  }

  .title {
    font-size: 28px;
  }

  .icon-circle {
    width: 100px;
    height: 100px;
  }

  .icon-circle ion-icon {
    font-size: 56px;
  }

}

/* Loading / Error Overlay */
.loading-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  padding: 24px;
}

.loading-card {
  background: #fff;
  border-radius: 24px;
  padding: 40px 32px;
  text-align: center;
  max-width: 340px;
  width: 100%;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
}

.loading-card h3 {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin: 16px 0 4px;
}

.loading-card p {
  font-size: 14px;
  color: #6b7280;
  line-height: 1.5;
  margin: 0;
}

.loading-spinner {
  width: 44px;
  height: 44px;
  border: 4px solid #e5e7eb;
  border-top-color: #059669;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.error-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #fef2f2;
  color: #dc2626;
  font-size: 28px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.retry-btn {
  --background: #059669;
  --border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  text-transform: none;
  margin: 20px 0 8px;
  height: 46px;
}

.back-btn {
  --color: #6b7280;
  font-size: 13px;
  text-transform: none;
  font-weight: 500;
  margin: 0;
}
</style>
