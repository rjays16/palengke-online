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
          <h1 class="title">{{ isVerified ? 'Account Verified!' : 'Account Created!' }}</h1>
          <p class="subtitle">{{ isVerified ? 'Welcome to Palengke Online Seller' : 'You\'re almost ready to start selling' }}</p>
        </div>

        <!-- Verification Status Badge -->
        <div class="status-badge" :class="{ verified: isVerified, basic: !isVerified }">
          <ion-icon :icon="isVerified ? shieldCheckmarkOutline : informationCircleOutline"></ion-icon>
          <span>{{ isVerified ? 'Fully Verified Account' : 'Basic Account - Not Verified' }}</span>
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
            <span class="label">Status:</span>
            <span class="value" :class="{ verified: isVerified, unverified: !isVerified }">
              {{ isVerified ? 'Verified ✓' : 'Not Verified' }}
            </span>
          </div>
        </div>

        <!-- VERIFIED VERSION - What's Unlocked -->
        <div v-if="isVerified" class="unlocked-features">
          <h3>🎉 All Features Unlocked!</h3>
          <div class="features-grid">
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Verified Seller Badge</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Unlimited Listings</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Higher Transaction Limits</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Priority Support</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Featured in Search</span>
            </div>
            <div class="feature-item">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Customer Trust Badge</span>
            </div>
          </div>
        </div>

        <!-- NOT VERIFIED VERSION - What's Missing -->
        <div v-else class="missing-features">
          <h3>⚠️ Limited Account Features</h3>
          <p class="info-text">Verify your account to unlock all features:</p>
          <div class="features-list">
            <div class="feature-item limited">
              <ion-icon :icon="lockClosedOutline"></ion-icon>
              <span>Limited to 10 product listings</span>
            </div>
            <div class="feature-item limited">
              <ion-icon :icon="lockClosedOutline"></ion-icon>
              <span>Lower transaction limits</span>
            </div>
            <div class="feature-item limited">
              <ion-icon :icon="lockClosedOutline"></ion-icon>
              <span>No verified seller badge</span>
            </div>
            <div class="feature-item limited">
              <ion-icon :icon="lockClosedOutline"></ion-icon>
              <span>Standard support only</span>
            </div>
          </div>

          <!-- Verify Later CTA -->
          <div class="verify-cta">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="cta-text">
              <strong>You can verify anytime!</strong><br>
              Go to Settings → Account Verification to complete your verification and unlock all features.
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
                <strong>Complete your profile</strong>
                <p>Add more details about your store</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-number">2</div>
              <div class="step-content">
                <strong>Add your first products</strong>
                <p>Start listing items to sell</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-number">3</div>
              <div class="step-content">
                <strong>Start receiving orders</strong>
                <p>Begin your selling journey</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <!-- NOT VERIFIED: Show both buttons -->
          <template v-if="!isVerified">
            <ion-button 
              expand="block" 
              fill="outline"
              color="primary"
              class="verify-button"
              @click="goToVerification"
            >
              <ion-icon slot="start" :icon="shieldCheckmarkOutline"></ion-icon>
              Verify Now
            </ion-button>

            <ion-button 
              expand="block" 
              class="login-button"
              @click="goToLogin"
            >
              Continue to Login
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </template>

          <!-- VERIFIED: Show single button -->
          <template v-else>
            <ion-button 
              expand="block" 
              class="dashboard-button"
              @click="goToLogin"
            >
              Go to Dashboard
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </template>
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
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonContent,
  IonButton,
  IonIcon,
  toastController
} from '@ionic/vue';
import {
  checkmarkCircleOutline,
  alertCircleOutline,
  shieldCheckmarkOutline,
  informationCircleOutline,
  lockClosedOutline,
  arrowForwardOutline
} from 'ionicons/icons';

const router = useRouter();

// Account data
const accountData = ref({
  fullName: '',
  email: '',
  phone: '',
  storeName: '',
  verificationStatus: 'not_verified'
});

// Computed: Is account verified?
const isVerified = computed(() => {
  return accountData.value.verificationStatus === 'verified';
});

// Load account data
onMounted(() => {
  const savedData = localStorage.getItem('registrationData');
  if (savedData) {
    const data = JSON.parse(savedData);
    
    // Build full name
    const middle = data.middleName ? ` ${data.middleName} ` : ' ';
    accountData.value.fullName = `${data.firstName}${middle}${data.lastName}`;
    
    accountData.value.email = data.email || '';
    accountData.value.phone = data.phone || '';
    accountData.value.storeName = data.storeName || '';
    accountData.value.verificationStatus = data.verificationStatus || 'not_verified';
  }
});

// Navigation
const goToVerification = () => {
  // Go back to Step 5 to start verification
  router.push('/register/step5');
};

const goToLogin = () => {
  // Clear registration data
  localStorage.removeItem('registrationData');
  
  // Show success toast
  showSuccessToast();
  
  // Navigate to login
  setTimeout(() => {
    router.push('/login');
  }, 1000);
};

const showSuccessToast = async () => {
  const toast = await toastController.create({
    message: isVerified.value 
      ? 'Account verified successfully! Please login to continue.' 
      : 'Account created! Please login to continue.',
    duration: 3000,
    color: 'success',
    position: 'top'
  });
  await toast.present();
};

const contactSupport = async () => {
  const toast = await toastController.create({
    message: 'Support: support@palengke-online.com',
    duration: 3000,
    color: 'primary',
    position: 'bottom'
  });
  await toast.present();
};
</script>

<style scoped>
.success-content {
  --background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%);
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
  background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.3);
}

.icon-circle.basic {
  background: linear-gradient(135deg, #FF9800 0%, #FFA726 100%);
  box-shadow: 0 8px 24px rgba(255, 152, 0, 0.3);
}

.icon-circle ion-icon {
  font-size: 64px;
  color: white;
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
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 16px;
  color: #666;
  margin: 0;
  line-height: 1.5;
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
  background: #4CAF50;
  color: white;
}

.status-badge.basic {
  background: #FF9800;
  color: white;
}

.status-badge ion-icon {
  font-size: 20px;
}

/* Summary Card */
.summary-card {
  width: 100%;
  max-width: 500px;
  background: white;
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.summary-card h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 20px 0;
  padding-bottom: 12px;
  border-bottom: 2px solid #f0f0f0;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
}

.summary-item:last-child {
  border-bottom: none;
}

.summary-item .label {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

.summary-item .value {
  font-size: 14px;
  color: #1a1a1a;
  font-weight: 600;
  text-align: right;
}

.summary-item .value.verified {
  color: #4CAF50;
}

.summary-item .value.unverified {
  color: #FF9800;
}

/* Unlocked Features (Verified) */
.unlocked-features {
  width: 100%;
  max-width: 500px;
  background: white;
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  border: 3px solid #4CAF50;
}

.unlocked-features h3 {
  font-size: 18px;
  font-weight: 700;
  color: #2E7D32;
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
  color: #4CAF50;
  flex-shrink: 0;
}

.feature-item span {
  font-size: 13px;
  color: #1a1a1a;
  font-weight: 500;
  line-height: 1.3;
}

/* Missing Features (Not Verified) */
.missing-features {
  width: 100%;
  max-width: 500px;
  background: white;
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  border: 3px solid #FF9800;
}

.missing-features h3 {
  font-size: 18px;
  font-weight: 700;
  color: #E65100;
  margin: 0 0 12px 0;
}

.info-text {
  font-size: 14px;
  color: #666;
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
  color: #FF9800;
  flex-shrink: 0;
}

.feature-item.limited span {
  font-size: 14px;
  color: #666;
}

/* Verify CTA */
.verify-cta {
  display: flex;
  gap: 12px;
  background: #FFF3E0;
  border-radius: 12px;
  padding: 16px;
  border-left: 4px solid #FF9800;
}

.verify-cta ion-icon {
  font-size: 24px;
  color: #FF9800;
  flex-shrink: 0;
  margin-top: 2px;
}

.cta-text {
  font-size: 13px;
  color: #E65100;
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
  background: white;
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.next-steps h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
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
  background: #4CAF50;
  color: white;
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
  color: #1a1a1a;
  margin-bottom: 4px;
}

.step-content p {
  font-size: 13px;
  color: #666;
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

.verify-button {
  --border-radius: 12px;
  --border-width: 2px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.login-button,
.dashboard-button {
  --background: #4CAF50;
  --background-hover: #45a049;
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
  color: #666;
  margin: 0;
}

.support-link a {
  color: #4CAF50;
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

  .features-grid {
    grid-template-columns: 1fr;
  }
}
</style>