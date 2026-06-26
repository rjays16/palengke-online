<template>
  <ion-page>
    <ion-content :fullscreen="true" class="success-content">
      <div class="success-container">

        <!-- Success Animation/Icon -->
        <div class="success-icon">
          <div class="icon-circle" :class="{ verified: isVerified, basic: !isVerified }">
            <ion-icon :icon="isVerified ? checkmarkCircleOutline : alertCircleOutline"></ion-icon>
          </div>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">{{ isVerified ? 'Driver Verified!' : 'Application Submitted!' }}</h1>
          <p class="subtitle">{{ isVerified ? 'Welcome to Palengke Online Driver' : 'Your application is being reviewed' }}</p>
        </div>

        <!-- Verification Status Badge -->
        <div class="status-badge" :class="{ verified: isVerified, basic: !isVerified }">
          <ion-icon :icon="isVerified ? shieldCheckmarkOutline : informationCircleOutline"></ion-icon>
          <span>{{ isVerified ? 'Fully Verified Driver' : 'Pending Review' }}</span>
        </div>

        <!-- Account Summary -->
        <div class="summary-card">
          <h3>Application Details</h3>
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
            <span class="label">Vehicle:</span>
            <span class="value">{{ accountData.vehicleType }}</span>
          </div>
          <div class="summary-item">
            <span class="label">Status:</span>
            <span class="value" :class="{ verified: isVerified, unverified: !isVerified }">
              {{ isVerified ? 'Verified' : 'Pending Review' }}
            </span>
          </div>
        </div>

        <!-- VERIFIED VERSION - What's Unlocked -->
        <div v-if="isVerified" class="unlocked-features">
          <h3><font-awesome-icon icon="glass-cheers" /> All Features Unlocked!</h3>
          <div class="features-grid">
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Verified Driver Badge</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Accept Delivery Requests</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>View Customer Info</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Priority Support</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Earnings Dashboard</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Customer Trust</span>
            </div>
          </div>
        </div>

        <!-- NOT VERIFIED VERSION - What's Missing -->
        <div v-else class="missing-features">
          <h3><font-awesome-icon icon="exclamation-triangle" /> Application Under Review</h3>
          <p class="info-text">Your application is being reviewed. You'll receive an email once approved.</p>
          <div class="features-list">
            <div class="feature-item limited">
              <ion-icon :icon="timeOutline"></ion-icon>
              <span>Review takes 24-48 hours</span>
            </div>
            <div class="feature-item limited">
              <ion-icon :icon="documentTextOutline"></ion-icon>
              <span>Documents being verified</span>
            </div>
            <div class="feature-item limited">
              <ion-icon :icon="mailOutline"></ion-icon>
              <span>Email notification on approval</span>
            </div>
          </div>

          <!-- Verify Later CTA -->
          <div class="verify-cta">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="cta-text">
              <strong>What happens next?</strong><br>
              Once your documents are verified, you'll receive an email with your login credentials.
            </div>
          </div>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
          <h3>What's Next?</h3>
          <div class="steps-list">
            <div class="step-item">
              <div class="step-number">1</div>
              <div class="step-content">
                <strong>Wait for approval</strong>
                <p>Our team is reviewing your application</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-number">2</div>
              <div class="step-content">
                <strong>Check your email</strong>
                <p>You'll receive login credentials once approved</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-number">3</div>
              <div class="step-content">
                <strong>Start accepting deliveries</strong>
                <p>Begin your delivery journey</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <ion-button 
            expand="block" 
            class="login-button"
            @click="goToLogin"
          >
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

<script setup>
import { ref, computed, onMounted, onActivated } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonContent,
  IonButton,
  IonIcon,
  IonSpinner,
  toastController
} from '@ionic/vue';
import { useRegistrationStore } from '@/composables/useRegistrationStore';
import {
  checkmarkCircleOutline,
  alertCircleOutline,
  shieldCheckmarkOutline,
  informationCircleOutline,
  arrowForwardOutline,
  timeOutline,
  documentTextOutline,
  mailOutline
} from 'ionicons/icons';

const router = useRouter();

// Account data
const accountData = ref({
  fullName: '',
  email: '',
  phone: '',
  vehicleType: '',
  verificationStatus: 'pending'
});

// Computed: Is account verified?
const isVerified = computed(() => {
  return accountData.value.verificationStatus === 'verified';
});

// Load account data
function loadAccountData() {
  const { getSubmittedData } = useRegistrationStore();
  const data = getSubmittedData();
  if (data) {
    const middle = data.middleName ? ` ${data.middleName} ` : ' ';
    accountData.value.fullName = `${data.firstName}${middle}${data.lastName}`;
    accountData.value.email = data.email || '';
    accountData.value.phone = data.fullPhone || data.phone || '';
    accountData.value.vehicleType = data.vehicleType || '';
    accountData.value.verificationStatus = data.verificationStatus || 'pending';
  }
}

onMounted(loadAccountData);
onActivated(loadAccountData);

// Navigation
const goToLogin = () => {
  const { clearSubmittedData } = useRegistrationStore();
  clearSubmittedData();
  
  // Show success toast
  showSuccessToast();
  
  // Navigate to login
  setTimeout(() => {
    router.push('/login');
  }, 1000);
};

const showSuccessToast = async () => {
  const toast = await toastController.create({
    message: 'Application submitted! Please check your email for updates.',
    duration: 3000,
    color: 'medium',
    position: 'top'
  });
  await toast.present();
};

const contactSupport = async () => {
  const toast = await toastController.create({
    message: 'Support: support@palengke-online.com',
    duration: 3000,
    color: 'medium',
    position: 'bottom'
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
  width: 120px;
  height: 120px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: scaleIn 0.5s ease-out;
}

.icon-circle.verified {
  background: linear-gradient(135deg, var(--color-verified-bg) 0%, var(--color-primary) 100%);
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.3);
}

.icon-circle.basic {
  background: linear-gradient(135deg, var(--color-warning) 0%, var(--color-warning-hover, #FF9800) 100%);
  box-shadow: 0 8px 24px rgba(255, 152, 0, 0.3);
}

.icon-circle ion-icon {
  font-size: 64px;
  color: var(--color-text-inverse);
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

/* Status Badge */
.status-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 24px;
  margin-bottom: 32px;
  font-weight: 600;
  font-size: 14px;
}

.status-badge.verified {
  background: var(--color-verified-bg);
  color: var(--color-text-inverse);
}

.status-badge.basic {
  background: var(--color-unverified-bg);
  color: var(--color-text-inverse);
}

.status-badge ion-icon {
  font-size: 20px;
}

/* Summary Card */
.summary-card {
  width: 100%;
  max-width: 500px;
  background: var(--color-card-bg);
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 16px var(--color-card-shadow);
}

.summary-card h3 {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 20px 0;
  padding-bottom: 12px;
  border-bottom: 2px solid var(--color-card-border);
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid var(--color-card-border);
}

.summary-item:last-child {
  border-bottom: none;
}

.summary-item .label {
  font-size: 14px;
  color: var(--color-text-secondary);
  font-weight: 500;
}

.summary-item .value {
  font-size: 14px;
  color: var(--color-text-primary);
  font-weight: 600;
  text-align: right;
}

.summary-item .value.verified {
  color: var(--color-verified-bg);
}

.summary-item .value.unverified {
  color: var(--color-unverified-bg);
}

/* Unlocked Features (Verified) */
.unlocked-features {
  width: 100%;
  max-width: 500px;
  background: var(--color-card-bg);
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 16px var(--color-card-shadow);
  border: 3px solid var(--color-verified-bg);
}

.unlocked-features h3 {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-success-text);
  margin: 0 0 20px 0;
}

.features-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.feature-item ion-icon {
  font-size: 24px;
  color: var(--color-verified-bg);
  flex-shrink: 0;
}

.feature-item span {
  font-size: 13px;
  color: var(--color-text-primary);
  font-weight: 500;
  line-height: 1.3;
}

/* Missing Features (Not Verified) */
.missing-features {
  width: 100%;
  max-width: 500px;
  background: var(--color-card-bg);
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 16px var(--color-card-shadow);
  border: 3px solid var(--color-unverified-bg);
}

.missing-features h3 {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-warning-text);
  margin: 0 0 12px 0;
}

.info-text {
  font-size: 14px;
  color: var(--color-text-secondary);
  margin: 0 0 16px 0;
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 20px;
}

.feature-item.limited {
  display: flex;
  align-items: center;
  gap: 10px;
}

.feature-item.limited ion-icon {
  font-size: 20px;
  color: var(--color-unverified-bg);
  flex-shrink: 0;
}

.feature-item.limited span {
  font-size: 14px;
  color: var(--color-text-secondary);
}

/* Verify CTA */
.verify-cta {
  display: flex;
  gap: 12px;
  background: var(--color-warning-bg);
  border-radius: 12px;
  padding: 16px;
  border-left: 4px solid var(--color-warning-border);
}

.verify-cta ion-icon {
  font-size: 24px;
  color: var(--color-warning-border);
  flex-shrink: 0;
  margin-top: 2px;
}

.cta-text {
  font-size: 13px;
  color: var(--color-warning-text);
  line-height: 1.5;
}

.cta-text strong {
  font-weight: 700;
  display: block;
  margin-bottom: 4px;
}

/* Next Steps */
.next-steps {
  width: 100%;
  max-width: 500px;
  background: var(--color-card-bg);
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 16px var(--color-card-shadow);
}

.next-steps h3 {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 20px 0;
}

.steps-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.step-item {
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.step-number {
  width: 32px;
  height: 32px;
  background: var(--color-primary);
  color: var(--color-text-inverse);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  flex-shrink: 0;
}

.step-content {
  flex: 1;
}

.step-content strong {
  display: block;
  font-size: 15px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin-bottom: 4px;
}

.step-content p {
  font-size: 13px;
  color: var(--color-text-secondary);
  margin: 0;
}

/* Action Buttons */
.action-buttons {
  width: 100%;
  max-width: 500px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.login-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
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
    font-size: 24px;
  }

  .icon-circle {
    width: 100px;
    height: 100px;
  }

  .icon-circle ion-icon {
    font-size: 56px;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }
}
</style>
