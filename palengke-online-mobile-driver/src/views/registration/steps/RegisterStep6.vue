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
            <div class="progress-fill" style="width: 85.68%"></div>
          </div>
          <p class="progress-text">Step 6 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Selfie Verification</h1>
          <p class="subtitle">Take a photo with your ID</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          
          <!-- Section Header -->
          <div class="section-header">
            <ion-icon :icon="cameraOutline"></ion-icon>
            <h2>Photo Verification</h2>
          </div>

          <!-- Instructions -->
          <div class="instructions-box">
            <h3>Selfie with ID Instructions:</h3>
            <ul>
              <li>Hold your valid ID beside your face</li>
              <li>Make sure your face and ID are clearly visible</li>
              <li>Ensure good lighting (no shadows)</li>
              <li>Keep a neutral expression</li>
              <li>Do not wear sunglasses or hats</li>
            </ul>
          </div>

          <!-- Camera/Selfie Upload -->
          <div class="selfie-upload-box" :class="{ uploaded: selfieUploaded }">
            <input 
              type="file" 
              accept="image/*" 
              capture="user"
              class="selfie-input"
              @change="onSelfieUpload"
            >
            <div class="selfie-content" v-if="!selfieUploaded">
              <div class="camera-icon">
                <ion-icon :icon="cameraOutline"></ion-icon>
              </div>
              <p>Tap to take selfie</p>
              <span>or upload from gallery</span>
            </div>
            <div class="selfie-preview" v-else>
              <img src="" alt="Selfie preview" id="selfiePreview">
              <div class="overlay">
                <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
                <p>Photo uploaded</p>
              </div>
            </div>
          </div>

          <!-- ID Preview -->
          <div class="id-preview-section" v-if="selfieUploaded">
            <p class="id-label">Selfie with ID uploaded successfully!</p>
          </div>

          <!-- Info Box -->
          <div class="info-box warning">
            <ion-icon :icon="warningOutline"></ion-icon>
            <div class="info-text">
              <strong>Important:</strong><br>
              The photo must clearly show your face and the ID details. Blurry or unreadable photos will be rejected.
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
              :disabled="!selfieUploaded"
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
import { ref } from 'vue';
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
  cameraOutline,
  checkmarkCircleOutline,
  warningOutline
} from 'ionicons/icons';

const router = useRouter();

const selfieUploaded = ref(false);

const onSelfieUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    selfieUploaded.value = true;
    const reader = new FileReader();
    reader.onload = (e) => {
      const preview = document.getElementById('selfiePreview');
      if (preview && e.target) {
        preview.src = e.target.result;
      }
    };
    reader.readAsDataURL(file);
  }
};

const goBack = () => {
  router.push('/register/step5');
};

const handleNext = () => {
  router.push('/register/step7');
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

.instructions-box {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 24px;
}

.instructions-box h3 {
  font-size: 15px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 12px 0;
}

.instructions-box ul {
  margin: 0;
  padding-left: 20px;
}

.instructions-box li {
  font-size: 13px;
  color: #666;
  margin-bottom: 8px;
  line-height: 1.4;
}

.selfie-upload-box {
  position: relative;
  width: 200px;
  height: 200px;
  margin: 0 auto 24px;
  border: 3px dashed #e0e0e0;
  border-radius: 50%;
  overflow: hidden;
  transition: all 0.2s;
  cursor: pointer;
}

.selfie-upload-box:hover {
  border-color: #4CAF50;
}

.selfie-upload-box.uploaded {
  border-color: #4CAF50;
  border-style: solid;
}

.selfie-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  z-index: 10;
}

.selfie-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  text-align: center;
}

.camera-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
}

.camera-icon ion-icon {
  font-size: 32px;
  color: #999;
}

.selfie-content p {
  font-size: 14px;
  color: #666;
  margin: 0 0 4px 0;
  font-weight: 600;
}

.selfie-content span {
  font-size: 12px;
  color: #999;
}

.selfie-preview {
  position: relative;
  width: 100%;
  height: 100%;
}

.selfie-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.selfie-preview .overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(76, 175, 80, 0.9);
  padding: 8px;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.selfie-preview .overlay ion-icon {
  font-size: 20px;
  color: white;
}

.selfie-preview .overlay p {
  font-size: 12px;
  color: white;
  margin: 0;
  font-weight: 600;
}

.id-preview-section {
  text-align: center;
  margin-bottom: 16px;
}

.id-label {
  font-size: 14px;
  color: #4CAF50;
  font-weight: 600;
  margin: 0;
}

.info-box.warning {
  background: #FFF3E0;
  border-left-color: #ff9800;
}

.info-box.warning ion-icon {
  color: #ff9800;
}

.info-box.warning .info-text {
  color: #e65100;
}

.info-box {
  display: flex;
  gap: 12px;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
}

.info-box ion-icon {
  font-size: 24px;
  flex-shrink: 0;
  margin-top: 2px;
}

.info-text {
  font-size: 13px;
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

  .selfie-upload-box {
    width: 180px;
    height: 180px;
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
