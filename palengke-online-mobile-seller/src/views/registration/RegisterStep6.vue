<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">
        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Progress Indicator -->
        <div v-if="!isLoggedIn" class="progress-section">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 85.68%"></div>
          </div>
          <p class="progress-text">Step 6 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">{{ isLoggedIn ? 'Take ID Photos' : 'Take ID Photos' }}</h1>
          <p class="subtitle">{{ idTypeName }}</p>
        </div>

        <!-- Current Step Indicator -->
        <div class="current-step-indicator">
          <div class="step-dot" :class="{ active: currentPhotoStep === 'front', done: frontPhotoTaken }">
            <ion-icon v-if="frontPhotoTaken" :icon="checkmarkOutline"></ion-icon>
            <span v-else>1</span>
          </div>
          <div class="step-line" :class="{ active: frontPhotoTaken }"></div>
          <div class="step-dot" :class="{ active: currentPhotoStep === 'back', done: backPhotoTaken }">
            <ion-icon v-if="backPhotoTaken" :icon="checkmarkOutline"></ion-icon>
            <span v-else>2</span>
          </div>
          <div class="step-labels">
            <span :class="{ active: currentPhotoStep === 'front' }">Front</span>
            <span :class="{ active: currentPhotoStep === 'back' }">Back</span>
          </div>
        </div>

        <!-- Instructions Card -->
        <div class="instructions-card">
          <div class="instruction-header">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <h3>{{ currentPhotoStep === 'front' ? 'Front Photo Instructions' : 'Back Photo Instructions' }}</h3>
          </div>
          <ul class="instruction-list">
            <li>Place your ID on a flat, contrasting surface</li>
            <li>Ensure good lighting with no shadows or glare</li>
            <li>Keep all four corners of the ID visible</li>
            <li>Make sure text is clear and readable</li>
            <li>Avoid using flash if possible</li>
          </ul>
        </div>

        <!-- Live Camera Preview -->
        <div v-if="showCamera" class="camera-preview-section">
          <div class="camera-preview-container">
            <video ref="videoRef" autoplay playsinline class="camera-video"></video>
            <canvas ref="canvasRef" style="display: none"></canvas>
            <div class="id-frame-overlay">
              <div class="corner top-left"></div>
              <div class="corner top-right"></div>
              <div class="corner bottom-left"></div>
              <div class="corner bottom-right"></div>
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

        <!-- Camera Preview / Photo Display (hidden when camera is live) -->
        <div v-if="!showCamera" class="camera-section">
          <div class="camera-frame">
            <!-- When no photo taken yet -->
            <div v-if="currentPhoto === null" class="camera-placeholder">
              <ion-icon :icon="cameraOutline"></ion-icon>
              <p>{{ currentPhotoStep === 'front' ? 'Front of ID' : 'Back of ID' }}</p>
              <span>Tap button below to capture</span>
            </div>

            <!-- When photo is taken -->
            <div v-else class="photo-preview">
              <img :src="currentPhoto" alt="ID Photo" />
              <div class="photo-overlay">
                <ion-button size="small" fill="clear" class="retake-btn" @click="retakePhoto">
                  <ion-icon slot="icon-only" :icon="refreshOutline"></ion-icon>
                </ion-button>
              </div>
            </div>

            <!-- ID Frame Overlay -->
            <div class="id-frame-overlay">
              <div class="corner top-left"></div>
              <div class="corner top-right"></div>
              <div class="corner bottom-left"></div>
              <div class="corner bottom-right"></div>
            </div>
          </div>

          <!-- Hidden canvas and file inputs -->
          <canvas ref="canvasRef" style="display: none"></canvas>
          <input
            ref="fileInput"
            type="file"
            accept="image/*"
            capture="environment"
            style="display: none"
            @change="handleFileInput"
          />
          <input ref="uploadInput" type="file" accept="image/*" style="display: none" @change="handleFileInput" />
        </div>

        <!-- Tips (hidden during camera preview) -->
        <div v-if="!showCamera" class="tips-box">
          <ion-icon :icon="bulbOutline"></ion-icon>
          <div class="tips-text">
            <strong>Tips for best results:</strong><br />
            Use natural lighting when possible. Hold your phone steady and ensure the entire ID fits within the frame.
          </div>
        </div>

        <!-- Action Buttons (hidden during camera preview) -->
        <div v-if="!showCamera" class="button-group">
          <!-- Show "Take Photo" and "Upload Photo" when no photo -->
          <template v-if="currentPhoto === null">
            <ion-button expand="block" class="capture-button" @click="openCamera">
              <ion-icon slot="start" :icon="cameraOutline"></ion-icon>
              Take Photo
            </ion-button>

            <ion-button expand="block" fill="outline" color="primary" class="upload-button" @click="openUpload">
              <ion-icon slot="start" :icon="cloudUploadOutline"></ion-icon>
              Upload from Gallery
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
              {{ currentPhotoStep === 'front' ? 'Confirm & Next' : 'Confirm Photo' }}
            </ion-button>
          </template>
        </div>

        <!-- Navigation Buttons (when both photos taken, hidden during camera preview) -->
        <div v-if="!showCamera && frontPhotoTaken && backPhotoTaken" class="nav-buttons">
          <ion-button expand="block" fill="outline" color="medium" class="back-btn" @click="goBack">
            <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
            Back
          </ion-button>

          <ion-button expand="block" class="next-button" @click="handleNext">
            Next: Selfie
            <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
          </ion-button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { Capacitor } from '@capacitor/core';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';
import { compressImage } from '@/utils/imageCompression';
import { IonPage, IonContent, IonButton, IonIcon, toastController, alertController } from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  cameraOutline,
  checkmarkOutline,
  refreshOutline,
  informationCircleOutline,
  bulbOutline,
  cloudUploadOutline,
} from 'ionicons/icons';
import { useRegistrationStore } from '@/stores/registration';

const router = useRouter();
const reg = useRegistrationStore();
const isLoggedIn = ref(!!localStorage.getItem('seller_token') && !!localStorage.getItem('userEmail'));
const fileInput = ref(null);
const uploadInput = ref(null);
const videoRef = ref(null);
const canvasRef = ref(null);

// Form data
const idTypeName = ref('');
const currentPhotoStep = ref('front'); // 'front' or 'back'
const frontPhoto = ref(null);
const backPhoto = ref(null);
const showCamera = ref(false);
const cameraStream = ref(null);

// Computed
const currentPhoto = computed(() => {
  return currentPhotoStep.value === 'front' ? frontPhoto.value : backPhoto.value;
});

const frontPhotoTaken = computed(() => {
  return frontPhoto.value !== null;
});

const backPhotoTaken = computed(() => {
  return backPhoto.value !== null;
});

// Load data
onMounted(() => {
  reg.load();
  idTypeName.value = reg.data.idTypeName || 'Government ID';

  // Load previously taken photos if going back
  if (reg.data.idFrontPhoto) frontPhoto.value = reg.data.idFrontPhoto;
  if (reg.data.idBackPhoto) backPhoto.value = reg.data.idBackPhoto;

  if (!frontPhoto.value) {
    currentPhotoStep.value = 'front';
  } else if (!backPhoto.value) {
    currentPhotoStep.value = 'back';
  }
});

onUnmounted(() => {
  stopCamera();
});

// Open camera
const openCamera = async () => {
  if (Capacitor.isNativePlatform()) {
    try {
      const photo = await Camera.getPhoto({
        resultType: CameraResultType.DataUrl,
        source: CameraSource.Camera,
        quality: 90,
      });
      await setPhoto(photo.dataUrl);
    } catch (error) {
      if (error.message !== 'User cancelled photos app') {
        showToast('Failed to capture photo.', 'warning');
      }
    }
  } else {
    startCamera();
  }
};

// Open upload/gallery
const openUpload = async () => {
  if (Capacitor.isNativePlatform()) {
    try {
      const photo = await Camera.getPhoto({
        resultType: CameraResultType.DataUrl,
        source: CameraSource.Photos,
        quality: 90,
      });
      await setPhoto(photo.dataUrl);
    } catch (error) {
      if (error.message !== 'User cancelled photos app') {
        showToast('Failed to load photo.', 'warning');
      }
    }
  } else {
    uploadInput.value?.click();
  }
};

// Start live camera preview
const startCamera = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
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

// Stop camera stream
const stopCamera = () => {
  if (cameraStream.value) {
    cameraStream.value.getTracks().forEach((track) => track.stop());
    cameraStream.value = null;
  }
  showCamera.value = false;
};

// Capture photo from video
const capturePhoto = async () => {
  const video = videoRef.value;
  const canvas = canvasRef.value;
  if (!video || !canvas) return;

  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(video, 0, 0);

  const raw = canvas.toDataURL('image/jpeg', 0.9);
  const approxBytes = raw.length * 0.75;
  if (approxBytes > 5 * 1024 * 1024) {
    showToast('Photo is too large. Please use a photo under 5MB.', 'warning');
    return;
  }
  let compressed;
  try {
    compressed = await compressImage(raw);
  } catch {
    compressed = raw;
  }
  if (currentPhotoStep.value === 'front') {
    frontPhoto.value = compressed;
  } else {
    backPhoto.value = compressed;
  }
  stopCamera();
};

// Cancel camera preview
const cancelCamera = () => {
  stopCamera();
};

// Fallback file handler for web browser
const handleFileInput = async (event) => {
  const file = event.target.files[0];
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) {
    showToast('Photo is too large. Please use a photo under 5MB.', 'warning');
    return;
  }
  const reader = new FileReader();
  reader.onload = async (e) => {
    const raw = e.target.result;
    let compressed;
    try {
      compressed = await compressImage(raw);
    } catch {
      compressed = raw;
    }
    if (currentPhotoStep.value === 'front') {
      frontPhoto.value = compressed;
    } else {
      backPhoto.value = compressed;
    }
  };
  reader.readAsDataURL(file);
  event.target.value = '';
};

// Set photo for current step
const setPhoto = async (dataUrl) => {
  const approxBytes = dataUrl.length * 0.75;
  if (approxBytes > 5 * 1024 * 1024) {
    showToast('Photo is too large. Please use a photo under 5MB.', 'warning');
    return;
  }
  let compressed;
  try {
    compressed = await compressImage(dataUrl);
  } catch {
    compressed = dataUrl;
  }
  if (currentPhotoStep.value === 'front') {
    frontPhoto.value = compressed;
  } else {
    backPhoto.value = compressed;
  }
};

// Retake photo
const retakePhoto = () => {
  if (currentPhotoStep.value === 'front') {
    frontPhoto.value = null;
  } else {
    backPhoto.value = null;
  }
};

// Confirm photo
const confirmPhoto = async () => {
  if (currentPhotoStep.value === 'front') {
    // Front photo confirmed, move to back
    const toast = await toastController.create({
      message: 'Front photo saved! Now capture the back.',
      duration: 2000,
      color: 'success',
      position: 'top',
    });
    await toast.present();

    currentPhotoStep.value = 'back';
  } else {
    // Back photo confirmed
    const toast = await toastController.create({
      message: 'Back photo saved! Both photos captured.',
      duration: 2000,
      color: 'success',
      position: 'top',
    });
    await toast.present();
  }
};

// Show toast
const showToast = async (message, color = 'primary') => {
  const toast = await toastController.create({
    message,
    duration: 2500,
    color,
    position: 'top',
  });
  await toast.present();
};

// Navigation
const goBack = async () => {
  const backRoute = isLoggedIn.value ? localStorage.getItem('verificationRedirect') || '/home' : '/register/step5';
  if (frontPhotoTaken.value || backPhotoTaken.value) {
    const alert = await alertController.create({
      header: 'Discard Photos?',
      message: 'Going back will discard your captured photos. Are you sure?',
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
        },
        {
          text: 'Discard',
          role: 'destructive',
          handler: () => {
            router.push(backRoute);
          },
        },
      ],
    });
    await alert.present();
  } else {
    router.push(backRoute);
  }
};

const handleNext = () => {
  if (!frontPhotoTaken.value || !backPhotoTaken.value) {
    showToast('Please capture both front and back photos.', 'warning');
    return;
  }

  reg.setFields({ idFrontPhoto: frontPhoto.value, idBackPhoto: backPhoto.value });

  // Navigate to Step 7 (Selfie)
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

/* Current Step Indicator */
.current-step-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
  position: relative;
}

.step-dot {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--color-border);
  color: var(--color-text-tertiary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  z-index: 2;
  transition: all 0.3s;
}

.step-dot.active {
  background: var(--color-primary);
  color: var(--color-text-inverse);
  transform: scale(1.1);
}

.step-dot.done {
  background: var(--color-primary);
  color: var(--color-text-inverse);
}

.step-dot ion-icon {
  font-size: 24px;
}

.step-line {
  width: 80px;
  height: 4px;
  background: var(--color-border);
  transition: background 0.3s;
}

.step-line.active {
  background: var(--color-primary);
}

.step-labels {
  position: absolute;
  bottom: -24px;
  width: 100%;
  display: flex;
  justify-content: space-between;
  padding: 0 40px;
}

.step-labels span {
  font-size: 13px;
  color: var(--color-text-tertiary);
  font-weight: 600;
  transition: color 0.3s;
}

.step-labels span.active {
  color: var(--color-primary);
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

/* Camera Section */
.camera-section {
  margin-bottom: 20px;
}

.camera-frame {
  position: relative;
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
  aspect-ratio: 1.6;
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

/* ID Frame Overlay */
.id-frame-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
}

.corner {
  position: absolute;
  width: 40px;
  height: 40px;
  border: 3px solid var(--color-primary);
}

.corner.top-left {
  top: 20px;
  left: 20px;
  border-right: none;
  border-bottom: none;
  border-radius: 12px 0 0 0;
}

.corner.top-right {
  top: 20px;
  right: 20px;
  border-left: none;
  border-bottom: none;
  border-radius: 0 12px 0 0;
}

.corner.bottom-left {
  bottom: 20px;
  left: 20px;
  border-right: none;
  border-top: none;
  border-radius: 0 0 0 12px;
}

.corner.bottom-right {
  bottom: 20px;
  right: 20px;
  border-left: none;
  border-top: none;
  border-radius: 0 0 12px 0;
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
  --border-radius: 12px;
  --border-width: 2px;
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
  aspect-ratio: 1.6;
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
    aspect-ratio: 1.4;
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
