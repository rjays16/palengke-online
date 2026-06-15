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
          <p class="subtitle">Take a photo with your valid ID</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          <!-- Section Header -->
          <div class="section-header">
            <ion-icon :icon="cameraOutline"></ion-icon>
            <h2>Photo Verification</h2>
          </div>

          <!-- Instructions Card -->
          <div class="instructions-card">
            <div class="instruction-header">
              <ion-icon :icon="informationCircleOutline"></ion-icon>
              <h3>Selfie with ID Instructions</h3>
            </div>
            <ul class="instruction-list">
              <li>Hold your valid ID beside your face</li>
              <li>Make sure your face and ID are clearly visible</li>
              <li>Ensure good lighting with no shadows or glare</li>
              <li>Do not wear sunglasses or hats</li>
              <li>Keep a neutral expression</li>
            </ul>
          </div>

          <!-- Live Camera Preview -->
          <div v-if="showCamera" class="camera-preview-section">
            <div class="camera-preview-container">
              <video ref="videoRef" autoplay playsinline class="camera-video"></video>
              <canvas ref="canvasRef" style="display: none"></canvas>
              <div class="selfie-frame-overlay">
                <div class="oval-frame"></div>
              </div>
            </div>
            <div class="camera-controls">
              <ion-button fill="clear" class="cancel-camera-btn" @click="cancelCamera"> Cancel </ion-button>
              <div class="capture-circle" @click="capturePhoto">
                <div class="capture-inner"></div>
              </div>
              <div style="width: 70px"></div>
            </div>
          </div>

          <!-- Camera Section (hidden during live preview) -->
          <div v-if="!showCamera" class="camera-section">
            <div class="camera-frame">
              <!-- When no photo taken yet -->
              <div v-if="selfiePhoto === null" class="camera-placeholder">
                <ion-icon :icon="cameraOutline"></ion-icon>
                <p>Take a Selfie with Your ID</p>
                <span>Tap button below to capture</span>
              </div>

              <!-- When photo is taken -->
              <div v-else class="photo-preview">
                <img :src="selfiePhoto" alt="Selfie preview" />
                <div class="photo-overlay">
                  <ion-button size="small" fill="clear" class="retake-btn" @click="retakePhoto">
                    <ion-icon slot="icon-only" :icon="refreshOutline"></ion-icon>
                  </ion-button>
                </div>
              </div>

              <!-- Selfie Frame Overlay -->
              <div class="selfie-frame-overlay">
                <div class="oval-frame"></div>
              </div>
            </div>

            <!-- Hidden canvas and file inputs -->
            <canvas ref="canvasRef" style="display: none"></canvas>
            <input
              ref="fileInput"
              type="file"
              accept="image/*"
              capture="user"
              style="display: none"
              @change="handleFileInput"
            />
          </div>

          <!-- Tips (hidden during camera preview) -->
          <div v-if="!showCamera" class="tips-box">
            <ion-icon :icon="bulbOutline"></ion-icon>
            <div class="tips-text">
              <strong>Tips for best results:</strong><br />
              Use natural lighting when possible. Hold your phone steady and ensure both your face and ID are clearly in frame.
            </div>
          </div>

          <!-- Action Buttons (hidden during camera preview) -->
          <div v-if="!showCamera" class="button-group">
            <!-- Show "Take Photo" when no photo -->
            <template v-if="selfiePhoto === null">
              <ion-button expand="block" class="capture-button" @click="openCamera">
                <ion-icon slot="start" :icon="cameraOutline"></ion-icon>
                Take Selfie
              </ion-button>
            </template>

            <!-- Show "Retake" and "Confirm" when photo taken -->
            <template v-else>
              <ion-button expand="block" fill="outline" color="medium" class="retake-button" @click="retakePhoto">
                <ion-icon slot="start" :icon="refreshOutline"></ion-icon>
                Retake Photo
              </ion-button>

              <ion-button expand="block" class="confirm-button" @click="confirmPhoto">
                <ion-icon slot="start" :icon="checkmarkOutline"></ion-icon>
                Confirm Photo
              </ion-button>
            </template>
          </div>

          <!-- Navigation Buttons (when photo confirmed) -->
          <div v-if="!showCamera && selfieConfirmed" class="nav-buttons">
            <ion-button expand="block" fill="outline" color="medium" class="back-btn" @click="goBack">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
              Back
            </ion-button>

            <ion-button expand="block" class="next-button" @click="handleNext">
              Next: Review
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { Capacitor } from '@capacitor/core';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';
import { IonPage, IonContent, IonButton, IonIcon, toastController } from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  cameraOutline,
  checkmarkOutline,
  refreshOutline,
  informationCircleOutline,
  bulbOutline,
} from 'ionicons/icons';

const router = useRouter();
const fileInput = ref(null);
const videoRef = ref(null);
const canvasRef = ref(null);

const selfiePhoto = ref(null);
const selfieConfirmed = ref(false);
const showCamera = ref(false);
const cameraStream = ref(null);

onMounted(() => {
  const savedData = localStorage.getItem('driverRegistrationData');
  if (savedData) {
    const data = JSON.parse(savedData);
    if (data.selfiePhoto) {
      selfiePhoto.value = data.selfiePhoto;
      selfieConfirmed.value = true;
    }
  }
});

onUnmounted(() => {
  stopCamera();
});

const openCamera = async () => {
  if (Capacitor.isNativePlatform()) {
    try {
      const photo = await Camera.getPhoto({
        resultType: CameraResultType.DataUrl,
        source: CameraSource.Camera,
        quality: 90,
      });
      setPhoto(photo.dataUrl);
    } catch (error) {
      if (error.message !== 'User cancelled photos app') {
        showToast('Failed to capture photo.', 'warning');
      }
    }
  } else {
    startCamera();
  }
};

const startCamera = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
      audio: false,
    });
    cameraStream.value = stream;
    showCamera.value = true;
    await new Promise((resolve) => {
      setTimeout(resolve, 100);
    });
    if (videoRef.value) {
      videoRef.value.srcObject = stream;
    }
  } catch {
    fileInput.value?.click();
  }
};

const stopCamera = () => {
  if (cameraStream.value) {
    cameraStream.value.getTracks().forEach((track) => track.stop());
    cameraStream.value = null;
  }
  showCamera.value = false;
};

const capturePhoto = () => {
  const video = videoRef.value;
  const canvas = canvasRef.value;
  if (!video || !canvas) return;

  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(video, 0, 0);

  const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
  const approxBytes = dataUrl.length * 0.75;
  if (approxBytes > 5 * 1024 * 1024) {
    showToast('Photo is too large. Please use a photo under 5MB.', 'warning');
    return;
  }
  selfiePhoto.value = dataUrl;
  stopCamera();
};

const cancelCamera = () => {
  stopCamera();
};

const handleFileInput = (event) => {
  const file = event.target.files[0];
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) {
    showToast('Photo is too large. Please use a photo under 5MB.', 'warning');
    return;
  }
  const reader = new FileReader();
  reader.onload = (e) => {
    selfiePhoto.value = e.target.result;
  };
  reader.readAsDataURL(file);
  event.target.value = '';
};

const setPhoto = (dataUrl) => {
  const approxBytes = dataUrl.length * 0.75;
  if (approxBytes > 5 * 1024 * 1024) {
    showToast('Photo is too large. Please use a photo under 5MB.', 'warning');
    return;
  }
  selfiePhoto.value = dataUrl;
};

const retakePhoto = () => {
  selfiePhoto.value = null;
  selfieConfirmed.value = false;
};

const confirmPhoto = async () => {
  if (!selfiePhoto.value) return;
  selfieConfirmed.value = true;
  const toast = await toastController.create({
    message: 'Selfie photo saved!',
    duration: 2000,
    color: 'success',
    position: 'top',
  });
  await toast.present();
};

const showToast = async (message, color = 'primary') => {
  const toast = await toastController.create({
    message,
    duration: 2500,
    color,
    position: 'top',
  });
  await toast.present();
};

const goBack = () => {
  if (selfiePhoto.value) {
    saveData();
  }
  router.push('/register/step5');
};

const saveData = () => {
  const existingData = JSON.parse(localStorage.getItem('driverRegistrationData') || '{}');
  existingData.selfiePhoto = selfiePhoto.value;
  localStorage.setItem('driverRegistrationData', JSON.stringify(existingData));
};

const handleNext = () => {
  if (!selfiePhoto.value || !selfieConfirmed.value) {
    showToast('Please take and confirm your selfie photo.', 'warning');
    return;
  }
  saveData();
  router.push('/register/step7');
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

/* Form Section */
.form-section {
  max-width: 500px;
  margin: 0 auto;
  width: 100%;
}

/* Section Header */
.section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  padding-bottom: 12px;
  border-bottom: 2px solid var(--color-card-border);
}

.section-header ion-icon {
  font-size: 24px;
  color: var(--color-icon-primary);
}

.section-header h2 {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-primary);
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
  content: '\2713';
  position: absolute;
  left: 0;
  color: var(--color-success-border);
  font-weight: 700;
}

/* Camera Section */
.camera-section {
  margin-bottom: 20px;
}

.camera-frame {
  position: relative;
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
  aspect-ratio: 1;
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
  font-size: 64px;
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

/* Selfie Frame Overlay */
.selfie-frame-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
}

.oval-frame {
  position: absolute;
  top: 10%;
  left: 10%;
  right: 10%;
  bottom: 10%;
  border: 3px dashed var(--color-primary);
  border-radius: 50%;
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

.retake-button {
  --border-radius: 12px;
  --border-width: 2px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.confirm-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

/* Navigation Buttons */
.nav-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-top: 24px;
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
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

/* Live Camera Preview */
.camera-preview-section {
  margin-bottom: 20px;
}

.camera-preview-container {
  position: relative;
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
  aspect-ratio: 1;
  border-radius: 20px;
  overflow: hidden;
  background: #000;
  box-shadow: 0 4px 16px var(--color-card-shadow);
}

.camera-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.camera-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 40px;
  padding: 20px 0;
}

.cancel-camera-btn {
  --color: var(--color-text-secondary);
  font-size: 15px;
  font-weight: 600;
}

.capture-circle {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  border: 4px solid var(--color-text-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.15s;
}

.capture-circle:active {
  transform: scale(0.9);
}

.capture-inner {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: var(--color-text-primary);
}

/* Responsive */
@media (max-width: 480px) {
  .title {
    font-size: 24px;
  }

  .step-container {
    padding: 20px 16px 100px 16px;
  }

  .camera-frame {
    aspect-ratio: 1;
  }

  .nav-buttons {
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
