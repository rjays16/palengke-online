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
            <div class="progress-fill" style="width: 71.4%"></div>
          </div>
          <p class="progress-text">Step 5 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Required Documents</h1>
          <p class="subtitle">Upload the following documents</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          
          <!-- Section Header -->
          <div class="section-header">
            <ion-icon :icon="documentTextOutline"></ion-icon>
            <h2>Document Upload</h2>
          </div>

          <!-- Driver's License -->
          <div class="upload-item">
            <div class="upload-label">
              <ion-icon :icon="cardOutline"></ion-icon>
              <span>Driver's License (Front & Back) *</span>
            </div>
            <div class="upload-box" :class="{ uploaded: uploadedDocs.license }">
              <input 
                type="file" 
                accept="image/*" 
                class="upload-input"
                @change="onFileUpload('license', $event)"
              >
              <div class="upload-content">
                <ion-icon :icon="cloudUploadOutline"></ion-icon>
                <p v-if="!uploadedDocs.license">Tap to upload</p>
                <p v-else class="uploaded-text">Uploaded</p>
              </div>
            </div>
          </div>

          <!-- Vehicle Registration -->
          <div class="upload-item">
            <div class="upload-label">
              <ion-icon :icon="carOutline"></ion-icon>
              <span>Vehicle Registration (OR/CR) *</span>
            </div>
            <div class="upload-box" :class="{ uploaded: uploadedDocs.vehicleReg }">
              <input 
                type="file" 
                accept="image/*" 
                class="upload-input"
                @change="onFileUpload('vehicleReg', $event)"
              >
              <div class="upload-content">
                <ion-icon :icon="cloudUploadOutline"></ion-icon>
                <p v-if="!uploadedDocs.vehicleReg">Tap to upload</p>
                <p v-else class="uploaded-text">Uploaded</p>
              </div>
            </div>
          </div>

          <!-- NBI Clearance -->
          <div class="upload-item">
            <div class="upload-label">
              <ion-icon :icon="shieldOutline"></ion-icon>
              <span>NBI or Police Clearance *</span>
            </div>
            <div class="upload-box" :class="{ uploaded: uploadedDocs.nbi }">
              <input 
                type="file" 
                accept="image/*" 
                class="upload-input"
                @change="onFileUpload('nbi', $event)"
              >
              <div class="upload-content">
                <ion-icon :icon="cloudUploadOutline"></ion-icon>
                <p v-if="!uploadedDocs.nbi">Tap to upload</p>
                <p v-else class="uploaded-text">Uploaded</p>
              </div>
            </div>
          </div>

          <!-- Birth Certificate -->
          <div class="upload-item">
            <div class="upload-label">
              <ion-icon :icon="documentOutline"></ion-icon>
              <span>Birth Certificate *</span>
            </div>
            <div class="upload-box" :class="{ uploaded: uploadedDocs.birthCert }">
              <input 
                type="file" 
                accept="image/*" 
                class="upload-input"
                @change="onFileUpload('birthCert', $event)"
              >
              <div class="upload-content">
                <ion-icon :icon="cloudUploadOutline"></ion-icon>
                <p v-if="!uploadedDocs.birthCert">Tap to upload</p>
                <p v-else class="uploaded-text">Uploaded</p>
              </div>
            </div>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>File Requirements:</strong><br>
              Images must be clear and readable. Accepted formats: JPG, PNG (max 5MB each).
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="button-group">
            <ion-button 
              expand="block" 
              fill="outline"
              color="medium"
              class="back-btn"
              @click="goBack"
            >
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
              Back
            </ion-button>

            <ion-button 
              expand="block" 
              class="next-button"
              @click="handleNext"
              :disabled="!isFormValid"
            >
              Next
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
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
import {
  IonPage,
  IonContent,
  IonButton,
  IonIcon
} from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  documentTextOutline,
  documentOutline,
  cardOutline,
  carOutline,
  shieldOutline,
  cloudUploadOutline,
  informationCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const uploadedDocs = ref({
  license: false,
  vehicleReg: false,
  nbi: false,
  birthCert: false
});

const isFormValid = computed(() => {
  return uploadedDocs.value.license && 
         uploadedDocs.value.vehicleReg && 
         uploadedDocs.value.nbi &&
         uploadedDocs.value.birthCert;
});

const onFileUpload = (docType, event) => {
  const file = event.target.files[0];
  if (file) {
    uploadedDocs.value[docType] = true;
  }
};

const goBack = () => {
  router.push('/register/step4');
};

const handleNext = () => {
  router.push('/register/step6');
};
</script>

<style scoped>
.step-content {
  --background: #ffffff;
}

.step-container {
  min-height: 100vh;
  padding: 24px;
  padding-bottom: 100px;
  position: relative;
}

.back-button {
  position: absolute;
  top: 24px;
  left: 24px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border-radius: 12px;
  cursor: pointer;
  z-index: 10;
  transition: background 0.2s;
}

.back-button:active {
  transform: scale(0.95);
  background: #e0e0e0;
}

.back-button ion-icon {
  font-size: 24px;
  color: #1a1a1a;
}

.progress-section {
  margin-top: 70px;
  margin-bottom: 32px;
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: #e0e0e0;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: #4CAF50;
  transition: width 0.3s ease;
}

.progress-text {
  text-align: center;
  font-size: 13px;
  color: #666;
  margin: 0;
  font-weight: 600;
}

.title-section {
  text-align: center;
  margin-bottom: 40px;
}

.title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 15px;
  color: #666;
  margin: 0;
}

.form-section {
  max-width: 500px;
  margin: 0 auto;
  width: 100%;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f0f0f0;
}

.section-header ion-icon {
  font-size: 24px;
  color: #4CAF50;
}

.section-header h2 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.upload-item {
  margin-bottom: 24px;
}

.upload-label {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.upload-label ion-icon {
  font-size: 20px;
  color: #4CAF50;
}

.upload-box {
  position: relative;
  border: 2px dashed #e0e0e0;
  border-radius: 12px;
  padding: 24px;
  text-align: center;
  transition: all 0.2s;
  cursor: pointer;
}

.upload-box:hover {
  border-color: #4CAF50;
  background: #f9f9f9;
}

.upload-box.uploaded {
  border-color: #4CAF50;
  background: #e8f5e9;
}

.upload-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.upload-content {
  pointer-events: none;
}

.upload-content ion-icon {
  font-size: 36px;
  color: #999;
  margin-bottom: 8px;
}

.upload-box.uploaded .upload-content ion-icon {
  color: #4CAF50;
}

.upload-content p {
  font-size: 13px;
  color: #666;
  margin: 0;
}

.uploaded-text {
  color: #4CAF50 !important;
  font-weight: 600;
}

.info-box {
  display: flex;
  gap: 12px;
  background: #E3F2FD;
  border-left: 4px solid #2196F3;
  padding: 16px;
  border-radius: 8px;
  margin: 24px 0;
}

.info-box ion-icon {
  font-size: 24px;
  color: #2196F3;
  flex-shrink: 0;
  margin-top: 2px;
}

.info-text {
  font-size: 13px;
  color: #1565C0;
  line-height: 1.5;
}

.info-text strong {
  font-weight: 700;
}

.button-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-top: 32px;
}

.back-btn {
  --border-radius: 12px;
  --border-width: 2px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.next-button {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.next-button:disabled {
  --background: #e0e0e0;
  --color: #999;
}

@media (max-width: 480px) {
  .title {
    font-size: 24px;
  }

  .step-container {
    padding: 20px 16px 100px 16px;
  }

  .button-group {
    grid-template-columns: 1fr;
  }

  .back-btn {
    order: 2;
  }

  .next-button {
    order: 1;
  }
}
</style>
