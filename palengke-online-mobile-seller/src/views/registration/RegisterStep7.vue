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
            <div class="progress-fill" style="width: 100%"></div>
          </div>
          <p class="progress-text">Step 7 of 7 - Final Step!</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Take Selfie with ID</h1>
          <p class="subtitle">Hold your ID next to your face</p>
        </div>

        <!-- Instructions Card -->
        <div class="instructions-card">
          <div class="instruction-header">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <h3>Selfie Instructions</h3>
          </div>
          <ul class="instruction-list">
            <li>Hold your ID next to your face (near cheek level)</li>
            <li>Make sure your face is clearly visible</li>
            <li>Ensure good lighting with no shadows</li>
            <li>ID photo and text should be readable</li>
            <li>Look directly at the camera</li>
            <li>Remove glasses or hat if wearing</li>
          </ul>
        </div>

        <!-- Example Image -->
        <div class="example-section">
          <h3>Example:</h3>
          <div class="example-grid">
            <div class="example-item correct">
              <div class="example-placeholder">
                <ion-icon :icon="personOutline"></ion-icon>
                <div class="id-badge">ID</div>
              </div>
              <div class="example-label correct">
                <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
                <span>CORRECT</span>
              </div>
              <p>ID next to face, both clearly visible</p>
            </div>
            <div class="example-item incorrect">
              <div class="example-placeholder">
                <ion-icon :icon="personOutline"></ion-icon>
                <ion-icon :icon="closeOutline" class="cross"></ion-icon>
              </div>
              <div class="example-label incorrect">
                <ion-icon :icon="closeCircleOutline"></ion-icon>
                <span>INCORRECT</span>
              </div>
              <p>Face or ID not visible, poor lighting</p>
            </div>
          </div>
        </div>

        <!-- Camera Preview / Photo Display -->
        <div class="camera-section">
          <div class="selfie-frame">
            <!-- When no photo taken yet -->
            <div v-if="selfiePhoto === null" class="camera-placeholder">
              <ion-icon :icon="personOutline"></ion-icon>
              <p>Your Selfie with ID</p>
              <span>Use camera or upload photo</span>
            </div>

            <!-- When photo is taken -->
            <div v-else class="photo-preview">
              <img :src="selfiePhoto" alt="Selfie with ID">
              <div class="photo-overlay">
                <ion-button 
                  size="small" 
                  fill="clear" 
                  class="retake-btn"
                  @click="retakePhoto"
                >
                  <ion-icon slot="icon-only" :icon="refreshOutline"></ion-icon>
                </ion-button>
              </div>
            </div>

            <!-- Face Frame Overlay -->
            <div class="face-frame-overlay">
              <div class="face-guide"></div>
            </div>
          </div>

          <!-- Hidden file inputs -->
          <!-- Camera capture -->
          <input 
            ref="cameraInput" 
            type="file" 
            accept="image/*" 
            capture="user"
            style="display: none"
            @change="handlePhotoCapture"
          >
          
          <!-- Gallery upload -->
          <input 
            ref="uploadInput" 
            type="file" 
            accept="image/*"
            style="display: none"
            @change="handlePhotoCapture"
          >
        </div>

        <!-- Tips -->
        <div class="tips-box">
          <ion-icon :icon="bulbOutline"></ion-icon>
          <div class="tips-text">
            <strong>Important:</strong><br>
            This is the final step! Make sure both your face and your ID are clearly visible in the same photo.
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="button-group">
          <!-- Show "Take Selfie" and "Upload Photo" when no photo -->
          <template v-if="selfiePhoto === null">
            <ion-button 
              expand="block" 
              class="capture-button"
              @click="openCamera"
            >
              <ion-icon slot="start" :icon="cameraOutline"></ion-icon>
              Take Selfie
            </ion-button>

            <ion-button 
              expand="block" 
              class="upload-button"
              @click="openUpload"
            >
              <ion-icon slot="start" :icon="cloudUploadOutline"></ion-icon>
              Upload Photo
            </ion-button>
          </template>

          <!-- Show "Retake" and "Submit" when photo taken -->
          <template v-else>
            <ion-button 
              expand="block" 
              fill="outline"
              color="medium"
              class="retake-button"
              @click="retakePhoto"
            >
              <ion-icon slot="start" :icon="refreshOutline"></ion-icon>
              Retake Photo
            </ion-button>

            <ion-button 
              expand="block" 
              class="submit-button"
              @click="handleSubmit"
            >
              <ion-icon slot="start" :icon="checkmarkCircleOutline"></ion-icon>
              Submit Verification
            </ion-button>
          </template>
        </div>

        <!-- Back Button (bottom) -->
        <div v-if="selfiePhoto === null" class="back-nav">
          <ion-button 
            expand="block" 
            fill="outline"
            color="medium"
            class="back-btn"
            @click="goBack"
          >
            <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
            Back to ID Photos
          </ion-button>
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
  toastController,
  alertController,
  loadingController
} from '@ionic/vue';
import {
  arrowBackOutline,
  cameraOutline,
  personOutline,
  checkmarkCircleOutline,
  closeCircleOutline,
  closeOutline,
  refreshOutline,
  informationCircleOutline,
  bulbOutline,
  cloudUploadOutline
} from 'ionicons/icons';

const router = useRouter();
const cameraInput = ref(null);
const uploadInput = ref(null);

// Form data
const selfiePhoto = ref(null);

// Load data
onMounted(() => {
  const savedData = localStorage.getItem('registrationData');
  if (savedData) {
    const data = JSON.parse(savedData);
    
    // Load previously taken selfie if going back
    if (data.selfiePhoto) {
      selfiePhoto.value = data.selfiePhoto;
    }
  }
});

// Open camera (front camera for selfie)
const openCamera = () => {
  cameraInput.value.click();
};

// Open upload/gallery
const openUpload = () => {
  uploadInput.value.click();
};

// Handle photo capture
const handlePhotoCapture = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  // Check file size (max 5MB)
  if (file.size > 5 * 1024 * 1024) {
    showToast('Photo is too large. Please use a photo under 5MB.', 'warning');
    return;
  }

  // Read file as base64
  const reader = new FileReader();
  reader.onload = (e) => {
    selfiePhoto.value = e.target.result;
  };
  reader.readAsDataURL(file);

  // Reset input
  event.target.value = '';
};

// Retake photo
const retakePhoto = () => {
  selfiePhoto.value = null;
};

// Show toast
const showToast = async (message, color = 'primary') => {
  const toast = await toastController.create({
    message,
    duration: 2500,
    color,
    position: 'top'
  });
  await toast.present();
};

// Navigation
const goBack = async () => {
  // Show confirmation if photo is taken
  if (selfiePhoto.value !== null) {
    const alert = await alertController.create({
      header: 'Discard Photo?',
      message: 'Going back will discard your selfie. Are you sure?',
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel'
        },
        {
          text: 'Discard',
          role: 'destructive',
          handler: () => {
            router.push('/register/step6');
          }
        }
      ]
    });
    await alert.present();
  } else {
    router.push('/register/step6');
  }
};

// Submit verification
const handleSubmit = async () => {
  if (!selfiePhoto.value) {
    showToast('Please take a selfie with your ID.', 'warning');
    return;
  }

  // Show confirmation
  const alert = await alertController.create({
    header: 'Submit Verification?',
    message: 'Please review all your photos before submitting. Once submitted, your verification will be processed.',
    buttons: [
      {
        text: 'Review Photos',
        role: 'cancel'
      },
      {
        text: 'Submit',
        handler: async () => {
          await submitVerification();
        }
      }
    ]
  });
  await alert.present();
};

// Submit verification process
const submitVerification = async () => {
  // Show loading
  const loading = await loadingController.create({
    message: 'Submitting verification...',
    spinner: 'crescent'
  });
  await loading.present();

  try {
    // Save selfie photo
    const existingData = JSON.parse(localStorage.getItem('registrationData') || '{}');
    existingData.selfiePhoto = selfiePhoto.value;
    existingData.verificationStatus = 'verified';
    existingData.verificationSubmittedAt = new Date().toISOString();
    localStorage.setItem('registrationData', JSON.stringify(existingData));

    // Simulate API call (replace with actual API)
    await new Promise(resolve => setTimeout(resolve, 2000));

    // TODO: Send verification data to backend
    // const response = await fetch('/api/verification/submit', {
    //   method: 'POST',
    //   body: JSON.stringify({
    //     email: existingData.email,
    //     idType: existingData.idType,
    //     idFrontPhoto: existingData.idFrontPhoto,
    //     idBackPhoto: existingData.idBackPhoto,
    //     selfiePhoto: existingData.selfiePhoto
    //   })
    // });

    await loading.dismiss();

    // Show success message
    const successAlert = await alertController.create({
      header: 'Verification Submitted!',
      message: 'Your verification has been submitted successfully. You will be notified once it\'s reviewed.',
      cssClass: 'success-alert',
      buttons: [
        {
          text: 'Continue',
          handler: () => {
            // Navigate to Success page (verified)
            router.push('/register/success');
          }
        }
      ]
    });
    await successAlert.present();

  } catch (error) {
    await loading.dismiss();
    
    const errorAlert = await alertController.create({
      header: 'Submission Failed',
      message: 'Something went wrong. Please try again.',
      buttons: ['OK']
    });
    await errorAlert.present();
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
  background: var(--color-bg-tertiary);
  border-radius: 12px;
  cursor: pointer;
  z-index: 10;
  transition: background 0.2s;
}

.back-button:active {
  transform: scale(0.95);
  background: var(--color-border);
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
  animation: fillComplete 0.5s ease-out;
}

@keyframes fillComplete {
  0% { width: 85.68%; }
  100% { width: 100%; }
}

.progress-text {
  text-align: center;
  font-size: 13px;
  color: var(--color-primary);
  margin: 0;
  font-weight: 700;
}

/* Title Section */
.title-section {
  text-align: center;
  margin-bottom: 24px;
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

/* Instructions Card */
.instructions-card {
  background: var(--color-success-bg);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 20px;
  border: 2px solid var(--color-success-border);
}

.instruction-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.instruction-header ion-icon {
  font-size: 24px;
  color: var(--color-success-border);
}

.instruction-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-success-text);
  margin: 0;
}

.instruction-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.instruction-list li {
  font-size: 14px;
  color: var(--color-success-text);
  padding-left: 24px;
  margin-bottom: 8px;
  position: relative;
}

.instruction-list li:last-child {
  margin-bottom: 0;
}

.instruction-list li::before {
  content: '\f00c';
  position: absolute;
  left: 0;
  color: var(--color-success-border);
  font-weight: 700;
  font-family: 'Font Awesome 6 Free';
}

/* Example Section */
.example-section {
  margin-bottom: 20px;
}

.example-section h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 12px 0;
}

.example-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.example-item {
  text-align: center;
}

.example-placeholder {
  height: 140px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
  position: relative;
}

.example-item.correct .example-placeholder {
  background: var(--color-success-bg);
  border: 2px solid var(--color-success-border);
}

.example-item.incorrect .example-placeholder {
  background: var(--color-error-bg);
  border: 2px solid var(--color-error-border);
}

.example-placeholder ion-icon {
  font-size: 48px;
}

.example-item.correct .example-placeholder ion-icon {
  color: var(--color-success-border);
}

.example-item.incorrect .example-placeholder ion-icon {
  color: var(--color-text-tertiary);
}

.id-badge {
  position: absolute;
  bottom: 12px;
  right: 12px;
  background: var(--color-primary);
  color: var(--color-text-inverse);
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
}

.cross {
  position: absolute;
  font-size: 64px !important;
  color: var(--color-error) !important;
}

.example-label {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin-bottom: 6px;
}

.example-label ion-icon {
  font-size: 18px;
}

.example-label.correct {
  color: var(--color-success-border);
}

.example-label.incorrect {
  color: var(--color-error-border);
}

.example-label span {
  font-size: 12px;
  font-weight: 700;
}

.example-item p {
  font-size: 11px;
  color: var(--color-text-secondary);
  margin: 0;
  line-height: 1.3;
}

/* Camera Section */
.camera-section {
  margin-bottom: 20px;
}

.selfie-frame {
  position: relative;
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
  aspect-ratio: 0.75;
  border-radius: 20px;
  overflow: hidden;
  background: var(--color-bg-tertiary);
  box-shadow: 0 4px 16px var(--color-card-shadow);
}

.camera-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  background: var(--color-bg-tertiary);
}

.camera-placeholder ion-icon {
  font-size: 80px;
  color: var(--color-text-tertiary);
}

.camera-placeholder p {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-secondary);
  margin: 0;
}

.camera-placeholder span {
  font-size: 14px;
  color: var(--color-text-tertiary);
  margin: 0;
}

.photo-preview {
  width: 100%;
  height: 100%;
  position: relative;
}

.photo-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.photo-overlay {
  position: absolute;
  top: 12px;
  right: 12px;
}

.retake-btn {
  --background: rgba(255, 255, 255, 0.9);
  --color: var(--color-text-primary);
  --border-radius: 50%;
  width: 40px;
  height: 40px;
}

/* Face Frame Overlay */
.face-frame-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.face-guide {
  width: 200px;
  height: 280px;
  border: 3px dashed var(--color-primary);
  border-radius: 50% / 60%;
  opacity: 0.6;
}

/* Tips Box */
.tips-box {
  display: flex;
  gap: 12px;
  background: var(--color-warning-bg);
  border-left: 4px solid var(--color-warning-border);
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
}

.tips-box ion-icon {
  font-size: 24px;
  color: var(--color-warning-border);
  flex-shrink: 0;
  margin-top: 2px;
}

.tips-text {
  font-size: 13px;
  color: var(--color-warning-text);
  line-height: 1.5;
}

.tips-text strong {
  font-weight: 700;
}

/* Button Group */
.button-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.capture-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
}

.upload-button {
  --background: var(--color-info);
  --background-hover: var(--color-info);
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
}

.retake-button {
  --border-radius: 12px;
  --border-width: 2px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.submit-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 700;
  text-transform: none;
}

/* Back Nav */
.back-nav {
  margin-top: 16px;
}

.back-btn {
  --border-radius: 12px;
  --border-width: 2px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

/* Success Alert */
:deep(.success-alert .alert-wrapper) {
  border-radius: 20px;
  background: var(--color-modal-bg);
}

/* Responsive */
@media (max-width: 480px) {
  .title {
    font-size: 24px;
  }

  .step-container {
    padding: 20px 16px 100px 16px;
  }

  .selfie-frame {
    aspect-ratio: 0.7;
  }

  .example-grid {
    grid-template-columns: 1fr;
  }
}
</style>