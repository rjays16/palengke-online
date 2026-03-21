<template>
  <ion-page>
    <ion-content :fullscreen="true" class="requirements-content">
      <div class="requirements-container">
        
        <!-- Header with Back Button -->
        <div class="header-section">
          <div class="back-button" @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </div>
          <h1 class="page-title">Driver Requirements</h1>
          <p class="page-subtitle">Please confirm the following requirements to proceed with registration</p>
        </div>

        <!-- Requirements Checklist -->
        <div class="requirements-list">
          
          <!-- Age Requirement -->
          <div class="requirement-card" :class="{ completed: requirements.ageConfirmed }">
            <div class="requirement-icon">
              <ion-icon :icon="personOutline"></ion-icon>
            </div>
            <div class="requirement-info">
              <h3>Age Requirement</h3>
              <p>Are you at least 18 years old and do you have an updated NBI or Police Clearance?</p>
            </div>
            <div class="requirement-toggle">
              <ion-checkbox 
                v-model="requirements.ageConfirmed"
                color="success"
              ></ion-checkbox>
            </div>
          </div>

          <!-- Driver's License -->
          <div class="requirement-card" :class="{ completed: requirements.hasLicense }">
            <div class="requirement-icon">
              <ion-icon :icon="cardOutline"></ion-icon>
            </div>
            <div class="requirement-info">
              <h3>Professional Driver's License</h3>
              <p>Do you have a Professional Driver's License?</p>
            </div>
            <div class="requirement-toggle">
              <ion-checkbox 
                v-model="requirements.hasLicense"
                color="success"
              ></ion-checkbox>
            </div>
          </div>

          <!-- Vehicle Requirements -->
          <div class="requirement-card" :class="{ completed: requirements.hasVehicle }">
            <div class="requirement-icon">
              <ion-icon :icon="carOutline"></ion-icon>
            </div>
            <div class="requirement-info">
              <h3>Vehicle Requirements</h3>
              <p>Do you have the following Vehicle Requirements?</p>
            </div>
            <div class="requirement-toggle">
              <ion-checkbox 
                v-model="requirements.hasVehicle"
                color="success"
              ></ion-checkbox>
            </div>
          </div>

          <!-- Birth Certificate -->
          <div class="requirement-card" :class="{ completed: requirements.hasBirthCertificate }">
            <div class="requirement-icon">
              <ion-icon :icon="documentTextOutline"></ion-icon>
            </div>
            <div class="requirement-info">
              <h3>Birth Certificate</h3>
              <p>Do you have a copy of your Birth Certificate?</p>
            </div>
            <div class="requirement-toggle">
              <ion-checkbox 
                v-model="requirements.hasBirthCertificate"
                color="success"
              ></ion-checkbox>
            </div>
          </div>

        </div>

        <!-- Qualification Message -->
        <div v-if="allRequirementsMet" class="qualification-message success">
          <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
          <h2>Great! You qualify!</h2>
          <p>You meet all the requirements to be a Palengke Online driver partner. Let's get you registered!</p>
        </div>

        <div v-else class="qualification-message pending">
          <ion-icon :icon="alertCircleOutline"></ion-icon>
          <h2>Almost there!</h2>
          <p>Please confirm all requirements to proceed with registration.</p>
        </div>

        <!-- Action Button -->
        <div class="action-section">
          <ion-button 
            expand="block" 
            class="proceed-button"
            @click="proceedToRegistration"
            :disabled="!allRequirementsMet"
          >
            <span v-if="!isLoading">Proceed to Registration</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { 
  IonPage, 
  IonContent, 
  IonButton, 
  IonIcon,
  IonCheckbox,
  IonSpinner,
  alertController 
} from '@ionic/vue';
import { 
  arrowBackOutline,
  personOutline,
  cardOutline,
  carOutline,
  documentTextOutline,
  checkmarkCircleOutline,
  alertCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const isLoading = ref(false);

const requirements = ref({
  ageConfirmed: false,
  hasLicense: false,
  hasVehicle: false,
  hasBirthCertificate: false
});

const allRequirementsMet = computed(() => {
  return Object.values(requirements.value).every(v => v === true);
});

const goBack = () => {
  router.push('/login');
};

const proceedToRegistration = async () => {
  if (!allRequirementsMet.value) {
    const alert = await alertController.create({
      header: 'Requirements Not Met',
      message: 'Please confirm all requirements to proceed with registration.',
      buttons: ['OK']
    });
    await alert.present();
    return;
  }

  isLoading.value = true;
  
  try {
    await new Promise(resolve => setTimeout(resolve, 1000));
    router.push('/register/step1');
  } catch (error) {
    const alert = await alertController.create({
      header: 'Error',
      message: 'Something went wrong. Please try again.',
      buttons: ['OK']
    });
    await alert.present();
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.requirements-content {
  --background: #ffffff;
}

.requirements-container {
  min-height: 100vh;
  padding: 24px;
}

/* Header Section */
.header-section {
  margin-bottom: 32px;
}

.back-button {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border-radius: 12px;
  cursor: pointer;
  margin-bottom: 20px;
  transition: background 0.2s;
}

.back-button:active {
  transform: scale(0.95);
}

.back-button:hover {
  background: #e0e0e0;
}

.back-button ion-icon {
  font-size: 24px;
  color: #1a1a1a;
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.page-subtitle {
  font-size: 15px;
  color: #666;
  margin: 0;
  line-height: 1.5;
}

/* Requirements List */
.requirements-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 32px;
}

.requirement-card {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 20px;
  background: #f8f9fa;
  border: 2px solid #e0e0e0;
  border-radius: 16px;
  transition: all 0.3s ease;
}

.requirement-card.completed {
  background: #e8f5e9;
  border-color: #4CAF50;
}

.requirement-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: background 0.3s ease;
}

.requirement-card.completed .requirement-icon {
  background: #4CAF50;
}

.requirement-icon ion-icon {
  font-size: 24px;
  color: #666;
  transition: color 0.3s ease;
}

.requirement-card.completed .requirement-icon ion-icon {
  color: white;
}

.requirement-info {
  flex: 1;
  min-width: 0;
}

.requirement-info h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 6px 0;
}

.requirement-info p {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.5;
}

.requirement-toggle {
  flex-shrink: 0;
  padding-top: 4px;
}

.requirement-toggle ion-checkbox {
  --size: 28px;
  --border-radius: 8px;
}

/* Qualification Message */
.qualification-message {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 24px;
  border-radius: 16px;
  margin-bottom: 24px;
}

.qualification-message.success {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
}

.qualification-message.pending {
  background: #fff3e0;
}

.qualification-message ion-icon {
  font-size: 48px;
  margin-bottom: 12px;
}

.qualification-message.success ion-icon {
  color: white;
}

.qualification-message.pending ion-icon {
  color: #ff9800;
}

.qualification-message h2 {
  font-size: 20px;
  font-weight: 700;
  margin: 0 0 8px 0;
}

.qualification-message.success h2 {
  color: white;
}

.qualification-message.pending h2 {
  color: #e65100;
}

.qualification-message p {
  font-size: 14px;
  margin: 0;
  line-height: 1.5;
}

.qualification-message.success p {
  color: rgba(255, 255, 255, 0.95);
}

.qualification-message.pending p {
  color: #bf360c;
}

/* Action Section */
.action-section {
  margin-top: 8px;
}

.proceed-button {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0.5px;
}

.proceed-button:disabled {
  --background: #e0e0e0;
  --background-hover: #d0d0d0;
  opacity: 1;
}

/* Responsive */
@media (max-width: 480px) {
  .requirements-container {
    padding: 20px 16px;
  }

  .page-title {
    font-size: 24px;
  }

  .requirement-card {
    padding: 16px;
  }

  .requirement-icon {
    width: 44px;
    height: 44px;
  }

  .requirement-icon ion-icon {
    font-size: 22px;
  }

  .requirement-info h3 {
    font-size: 15px;
  }

  .requirement-info p {
    font-size: 13px;
  }
}
</style>
