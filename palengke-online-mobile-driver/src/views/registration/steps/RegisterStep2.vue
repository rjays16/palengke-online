<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Step 2 of 2</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="finishRegistration" fill="clear" color="light">Skip</ion-button>
        </ion-buttons>
      </ion-toolbar>
      <ion-toolbar color="light">
        <ion-progress-bar :value="1"></ion-progress-bar>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="step-container">
        <h1 class="step-title">Vehicle Information</h1>
        <p class="step-subtitle">Tell us about your vehicle</p>

        <div class="form-section">
          <div class="input-group">
            <ion-icon :icon="carOutline" class="input-icon"></ion-icon>
            <input 
              v-model="formData.vehicleType" 
              type="text" 
              placeholder="Vehicle type (e.g., Motorcycle, Tricycle, Van)"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <ion-icon :icon="barcodeOutline" class="input-icon"></ion-icon>
            <input 
              v-model="formData.plateNumber" 
              type="text" 
              placeholder="Plate number"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <ion-icon :icon="constructOutline" class="input-icon"></ion-icon>
            <input 
              v-model="formData.vehicleModel" 
              type="text" 
              placeholder="Vehicle model & year"
              class="custom-input"
            >
          </div>
        </div>

        <!-- Document Upload Placeholder -->
        <div class="upload-section">
          <h3>Upload Documents</h3>
          <p>Please prepare the following documents for upload:</p>
          <div class="document-list">
            <div class="document-item">
              <ion-icon :icon="documentTextOutline"></ion-icon>
              <span>Driver's License (front & back)</span>
            </div>
            <div class="document-item">
              <ion-icon :icon="documentTextOutline"></ion-icon>
              <span>Vehicle Registration (OR/CR)</span>
            </div>
            <div class="document-item">
              <ion-icon :icon="documentTextOutline"></ion-icon>
              <span>NBI or Police Clearance</span>
            </div>
            <div class="document-item">
              <ion-icon :icon="documentTextOutline"></ion-icon>
              <span>Birth Certificate</span>
            </div>
          </div>
        </div>

        <div class="action-section">
          <ion-button 
            expand="block" 
            class="finish-button"
            @click="finishRegistration"
          >
            <span v-if="!isLoading">Complete Registration</span>
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
import { 
  IonPage, 
  IonHeader, 
  IonToolbar, 
  IonTitle, 
  IonContent, 
  IonButton,
  IonButtons,
  IonIcon,
  IonProgressBar,
  IonSpinner
} from '@ionic/vue';
import { 
  arrowBackOutline,
  carOutline,
  barcodeOutline,
  constructOutline,
  documentTextOutline
} from 'ionicons/icons';

const router = useRouter();

const isLoading = ref(false);

const formData = ref({
  vehicleType: '',
  plateNumber: '',
  vehicleModel: ''
});

const goBack = () => {
  router.push('/register/step1');
};

const finishRegistration = async () => {
  isLoading.value = true;
  
  try {
    await new Promise(resolve => setTimeout(resolve, 1500));
    router.push('/register/success');
  } catch (error) {
    console.error('Registration failed:', error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.step-container {
  max-width: 500px;
  margin: 0 auto;
  padding-top: 20px;
}

.step-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.step-subtitle {
  font-size: 15px;
  color: #666;
  margin: 0 0 32px 0;
}

.form-section {
  margin-bottom: 24px;
}

.input-group {
  position: relative;
  margin-bottom: 16px;
}

.input-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #4CAF50;
  z-index: 2;
}

.custom-input {
  width: 100%;
  padding: 16px 16px 16px 48px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 16px;
  background: white;
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input:focus {
  outline: none;
  border-color: #4CAF50;
}

.custom-input::placeholder {
  color: #999;
}

.upload-section {
  background: #f8f9fa;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 32px;
}

.upload-section h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.upload-section > p {
  font-size: 14px;
  color: #666;
  margin: 0 0 16px 0;
}

.document-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.document-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: white;
  border-radius: 10px;
  border: 1px solid #e0e0e0;
}

.document-item ion-icon {
  font-size: 20px;
  color: #4CAF50;
}

.document-item span {
  font-size: 14px;
  color: #333;
}

.finish-button {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
}
</style>
