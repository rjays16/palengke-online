<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/profile"></ion-back-button>
        </ion-buttons>
        <ion-title>Documents</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="docs-content">
      <div class="docs-container">

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <ion-spinner name="crescent"></ion-spinner>
          <p>Loading documents...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-state">
          <ion-icon :icon="alertCircleOutline"></ion-icon>
          <p>{{ error }}</p>
          <ion-button fill="outline" color="medium" @click="fetchDocuments">Retry</ion-button>
        </div>

        <!-- Documents Content -->
        <template v-else>

          <!-- Empty State -->
          <div v-if="documents.length === 0" class="empty-state">
            <ion-icon :icon="documentTextOutline"></ion-icon>
            <h3>No Documents Found</h3>
            <p>Could not load your documents. Please try again.</p>
            <ion-button fill="outline" color="medium" @click="fetchDocuments">Retry</ion-button>
          </div>

          <!-- Status Summary -->
          <div v-else class="status-summary" :class="summaryClass">
            <div class="status-icon">
              <ion-icon :icon="summaryIcon"></ion-icon>
            </div>
            <div>
              <h3>{{ summaryTitle }}</h3>
              <p>{{ summarySubtitle }}</p>
            </div>
          </div>

          <!-- Documents List -->
          <div class="docs-list">
            <div
              class="doc-card"
              v-for="doc in documents"
              :key="doc.id"
              @click="handleDocumentClick(doc)"
            >
              <div class="doc-preview">
                <template v-if="doc.photo_url">
                  <img
                    :src="doc.photo_url"
                    :alt="doc.name"
                    class="doc-thumbnail"
                    :class="{ 'img-hidden': !imageLoaded[doc.id] }"
                    @load="imageLoaded[doc.id] = true"
                    @error="imageLoaded[doc.id] = false"
                  />
                  <div v-if="imageLoaded[doc.id] === false" class="doc-placeholder">
                    <ion-icon :icon="documentTextOutline"></ion-icon>
                    <span>No file</span>
                  </div>
                </template>
                <div v-else class="doc-placeholder">
                  <ion-icon :icon="documentTextOutline"></ion-icon>
                  <span>No file</span>
                </div>
              </div>
              <div class="doc-info">
                <div class="doc-details">
                  <h4>{{ doc.name }}</h4>
                  <p v-if="doc.expiry">Expires: {{ formatDate(doc.expiry) }}</p>
                  <p v-else>No expiry</p>
                </div>
                <div class="doc-meta">
                  <span class="doc-status" :class="doc.status">
                    {{ statusLabel(doc.status) }}
                  </span>
                  <ion-icon
                    :icon="doc.status === 'missing' ? cloudUploadOutline : refreshOutline"
                    class="doc-action-icon"
                  ></ion-icon>
                </div>
              </div>
            </div>
          </div>

          <!-- Uploading Overlay -->
          <div v-if="uploading" class="uploading-overlay">
            <div class="uploading-modal">
              <ion-spinner name="crescent"></ion-spinner>
              <p>Uploading document...</p>
            </div>
          </div>

          <!-- Update Notice -->
          <div class="update-notice">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <p>Keep your documents updated to avoid account suspension. Tap any document to upload or re-upload.</p>
          </div>

        </template>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonButton, IonIcon, IonSpinner,
  alertController, toastController
} from '@ionic/vue';
import {
  shieldCheckmarkOutline, documentTextOutline, cloudUploadOutline,
  refreshOutline, informationCircleOutline, alertCircleOutline,
  timeOutline, warningOutline
} from 'ionicons/icons';
import { compressImage, readFileAsDataURL } from '@/utils/imageCompression';

import { API_BASE } from '@/services/api';

const documents = ref([]);
const loading = ref(true);
const error = ref('');
const uploading = ref(false);
const imageLoaded = ref({});

function getEmail() {
  const fromStore = localStorage.getItem('driverRegistrationData');
  if (fromStore) {
    try { const parsed = JSON.parse(fromStore); if (parsed.email) return parsed.email; } catch { /* ignore */ }
  }
  const submitted = localStorage.getItem('submittedRegistrationData');
  if (submitted) {
    try { const parsed = JSON.parse(submitted); if (parsed.email) return parsed.email; } catch { /* ignore */ }
  }
  return localStorage.getItem('userEmail') || '';
}

const pendingCount = computed(() => documents.value.filter(d => d.status === 'pending').length);
const missingCount = computed(() => documents.value.filter(d => d.status === 'missing').length);

const summaryClass = computed(() => {
  if (missingCount.value > 0) return 'warning';
  if (pendingCount.value > 0) return 'pending';
  return 'verified';
});

const summaryIcon = computed(() => {
  if (missingCount.value > 0) return warningOutline;
  if (pendingCount.value > 0) return timeOutline;
  return shieldCheckmarkOutline;
});

const summaryTitle = computed(() => {
  if (missingCount.value > 0) return 'Missing Documents';
  if (pendingCount.value > 0) return 'Documents Pending Review';
  return 'All Documents Verified';
});

const summarySubtitle = computed(() => {
  if (missingCount.value > 0) return `${missingCount.value} document(s) need to be uploaded`;
  if (pendingCount.value > 0) return `${pendingCount.value} document(s) awaiting review`;
  return 'Your account is fully verified';
});

function statusLabel(status) {
  const labels = {
    verified: 'Verified',
    pending: 'Pending',
    missing: 'Missing',
  };
  return labels[status] || status;
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
}

async function fetchDocuments() {
  const email = getEmail();
  if (!email) {
    error.value = 'No user email found. Please log in again.';
    loading.value = false;
    return;
  }

  loading.value = true;
  error.value = '';
  imageLoaded.value = {};

  try {
    const res = await fetch(`${API_BASE}/api/driver/documents?email=${encodeURIComponent(email)}`);
    const data = await res.json();

    if (!res.ok) throw new Error(data.message || 'Failed to load documents');

    if (data.documents.length === 0) {
      await seedDocuments();
    } else {
      documents.value = data.documents;
    }
  } catch (e) {
    error.value = e.message || 'Failed to load documents. Please try again.';
  } finally {
    loading.value = false;
  }
}

async function seedDocuments() {
  const email = getEmail();
  try {
    const res = await fetch(`${API_BASE}/api/driver/documents/seed`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email }),
    });
    if (res.ok) {
      const data = await res.json();
      documents.value = data.documents;
      imageLoaded.value = {};
    }
  } catch (e) {
    error.value = 'Failed to load documents. Check your connection and try again.';
    console.error('Seed documents error:', e);
  }
}

async function handleDocumentClick(doc) {
  const alert = await alertController.create({
    header: doc.name,
    message: `Status: ${statusLabel(doc.status)}${doc.expiry ? `\nExpires: ${formatDate(doc.expiry)}` : '\nNo expiry date'}`,
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: doc.photo_url ? 'Re-upload' : 'Upload',
        handler: () => uploadDocument(doc),
      },
    ],
  });
  await alert.present();
}

async function uploadDocument(doc) {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/*';
  input.onchange = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
      showToast('File too large (max 5MB)', 'warning');
      return;
    }

    if (!file.type.startsWith('image/')) {
      showToast('Please select an image file', 'warning');
      return;
    }

    uploading.value = true;

    try {
      const raw = await readFileAsDataURL(file);
      const compressed = await compressImage(raw);

      const res = await fetch(`${API_BASE}/api/driver/documents/upload`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: getEmail(),
          type: doc.type,
          photo: compressed,
        }),
      });

      if (!res.ok) throw new Error('Upload failed');

      const data = await res.json();
      const idx = documents.value.findIndex(d => d.id === doc.id);
      if (idx !== -1) {
        documents.value[idx] = data.document;
      }

      showToast(`${doc.name} uploaded successfully!`, 'success');
    } catch {
      showToast('Upload failed. Please try again.', 'danger');
    } finally {
      uploading.value = false;
    }
  };
  input.click();
}

async function showToast(message, color = 'primary') {
  const toast = await toastController.create({
    message, duration: 2500, color, position: 'top',
  });
  await toast.present();
}

onMounted(fetchDocuments);
</script>

<style scoped>
.docs-content { --background: #f8f9fa; }
.docs-container { padding: 16px; }

.loading-state {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; padding: 60px 20px; gap: 16px;
  color: #999; font-size: 14px;
}

.error-state {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; padding: 60px 20px; gap: 12px;
  color: #ef4444; font-size: 14px; text-align: center;
}
.error-state ion-icon { font-size: 48px; color: #ef4444; }

.empty-state {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; padding: 40px 20px; gap: 12px;
  text-align: center;
}
.empty-state ion-icon { font-size: 64px; color: #ccc; }
.empty-state h3 { font-size: 18px; color: #1a1a1a; margin: 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0; }

.status-summary {
  border-radius: 16px; padding: 20px;
  display: flex; align-items: center; gap: 16px;
  color: white; margin-bottom: 20px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
  transition: all 0.3s;
}
.status-summary.verified { background: linear-gradient(135deg, #4CAF50, #45a049); }
.status-summary.pending { background: linear-gradient(135deg, #FF9800, #F57C00); }
.status-summary.warning { background: linear-gradient(135deg, #ef4444, #dc2626); }

.status-icon {
  width: 56px; height: 56px; background: rgba(255,255,255,0.2);
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; flex-shrink: 0;
}
.status-icon ion-icon { font-size: 32px; color: white; }
.status-summary h3 { font-size: 16px; font-weight: 700; margin: 0 0 4px 0; }
.status-summary p { font-size: 13px; margin: 0; opacity: 0.9; }

.docs-list {
  display: flex; flex-direction: column; gap: 12px;
  margin-bottom: 20px;
}

.doc-card {
  background: white; border-radius: 14px; padding: 12px;
  display: flex; align-items: center; gap: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  cursor: pointer; transition: transform 0.2s;
}
.doc-card:active { transform: scale(0.98); }

.doc-preview {
  width: 64px; height: 64px; border-radius: 12px;
  overflow: hidden; flex-shrink: 0;
  background: #f0f0f0;
}

.doc-thumbnail {
  width: 100%; height: 100%; object-fit: cover;
}
.doc-thumbnail.img-hidden { display: none; }

.doc-placeholder {
  width: 100%; height: 100%;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 2px; color: #bbb;
}
.doc-placeholder ion-icon { font-size: 24px; }
.doc-placeholder span { font-size: 9px; font-weight: 600; }

.doc-info {
  flex: 1; display: flex;
  align-items: center; justify-content: space-between;
  gap: 12px; min-width: 0;
}

.doc-details { flex: 1; min-width: 0; }
.doc-details h4 {
  font-size: 14px; font-weight: 700; color: #1a1a1a;
  margin: 0 0 3px 0; white-space: nowrap;
  overflow: hidden; text-overflow: ellipsis;
}
.doc-details p { font-size: 12px; color: #999; margin: 0; }

.doc-meta {
  display: flex; flex-direction: column;
  align-items: flex-end; gap: 4px; flex-shrink: 0;
}

.doc-status {
  font-size: 11px; font-weight: 700; padding: 2px 8px;
  border-radius: 6px; text-transform: uppercase;
  letter-spacing: 0.3px;
}
.doc-status.verified { background: #E8F5E9; color: #4CAF50; }
.doc-status.pending { background: #FFF3E0; color: #FF9800; }
.doc-status.missing { background: #FEE2E2; color: #ef4444; }

.doc-action-icon {
  font-size: 18px; color: #4CAF50;
}

.uploading-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.4); z-index: 9999;
  display: flex; align-items: center; justify-content: center;
}
.uploading-modal {
  background: white; border-radius: 16px; padding: 32px;
  text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}
.uploading-modal ion-spinner { font-size: 36px; color: #4CAF50; margin-bottom: 12px; }
.uploading-modal p { font-size: 15px; color: #1a1a1a; margin: 0; font-weight: 600; }

.update-notice {
  display: flex; gap: 12px; background: #E3F2FD;
  border-radius: 14px; padding: 16px;
  border-left: 4px solid #2196F3;
}
.update-notice ion-icon { font-size: 22px; color: #2196F3; flex-shrink: 0; margin-top: 2px; }
.update-notice p { font-size: 13px; color: #1565C0; margin: 0; line-height: 1.5; }
</style>
