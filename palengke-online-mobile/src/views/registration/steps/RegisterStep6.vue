<template>
  <ion-page>
    <ion-content :fullscreen="true" class="success-content">
      <div class="success-container">

        <!-- Success Icon -->
        <div class="success-icon">
          <div class="icon-circle">
            <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
          </div>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Account Created!</h1>
          <p class="subtitle">Welcome to Palengke Online</p>
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
            <span class="label">Address:</span>
            <span class="value">{{ accountData.address }}</span>
          </div>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
          <h3>What's Next?</h3>
          <div class="steps-list">
            <div class="step-item">
              <div class="step-number">1</div>
              <div class="step-content">
                <strong>Browse sellers</strong>
                <p>Explore fresh produce in your area</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-number">2</div>
              <div class="step-content">
                <strong>Add items to cart</strong>
                <p>Select products you want to buy</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-number">3</div>
              <div class="step-content">
                <strong>Place your order</strong>
                <p>Choose delivery and payment method</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <ion-button 
            expand="block" 
            class="home-button"
            @click="goToHome"
          >
            Continue to Browse
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
import { ref, onMounted } from 'vue';
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
  arrowForwardOutline
} from 'ionicons/icons';
import { useRegistrationStore } from '@/composables/useRegistrationStore';

const router = useRouter();

const accountData = ref({
  fullName: '',
  email: '',
  phone: '',
  address: ''
});

onMounted(() => {
  const data = JSON.parse(localStorage.getItem('buyerRegistrationData') || '{}');
  const mid = data.middleName ? ` ${data.middleName} ` : ' ';
  accountData.value.fullName = `${data.firstName || ''}${mid}${data.lastName || ''}`.trim();
  accountData.value.email = data.email || '';
  accountData.value.phone = data.phone || '';
  
  // Build address from delivery info
  const addressParts = [];
  if (data.barangay) addressParts.push(data.barangay);
  if (data.city) addressParts.push(data.city);
  if (data.region) addressParts.push(data.region);
  accountData.value.address = addressParts.length > 0 
    ? addressParts.join(', ') 
    : 'Not provided';
});

const goToHome = () => {
  localStorage.setItem('userEmail', accountData.value.email);
  useRegistrationStore().onSubmitted();
  localStorage.setItem('isAuthenticated', 'true');
  
  showSuccessToast();
  
  setTimeout(() => {
    router.push('/create-pin');
  }, 1000);
};

const showSuccessToast = async () => {
  const toast = await toastController.create({
    message: 'Account created! Welcome to Palengke Online.',
    duration: 3000,
    color: 'success',
    position: 'top'
  });
  await toast.present();
};

const contactSupport = async () => {
  const toast = await toastController.create({
    message: 'Support: support@palengkeonline.com',
    duration: 3000,
    color: 'primary',
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
  background: linear-gradient(135deg, var(--color-unverified-bg) 0%, var(--color-warning) 100%);
  box-shadow: 0 8px 24px rgba(255, 152, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  animation: scaleIn 0.5s ease-out;
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
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
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

.home-button {
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
</style>