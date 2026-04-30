<!-- ============================================================ -->
<!-- FILE 1: DocumentsPage.vue -->
<!-- Save as: src/views/DocumentsPage.vue -->
<!-- ============================================================ -->
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

        <!-- Status Summary -->
        <div class="status-summary">
          <div class="status-icon">
            <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
          </div>
          <div>
            <h3>All Documents Verified</h3>
            <p>Your account is fully verified</p>
          </div>
        </div>

        <!-- Documents List -->
        <div class="docs-list">
          <div
            class="doc-card"
            v-for="doc in documents"
            :key="doc.id"
            @click="viewDocument(doc)"
          >
            <div class="doc-info">
              <div class="doc-icon" :class="doc.status">
                <ion-icon :icon="documentTextOutline"></ion-icon>
              </div>
              <div class="doc-details">
                <h4>{{ doc.name }}</h4>
                <p v-if="doc.expiry">Expires: {{ doc.expiry }}</p>
                <p v-else>No expiry</p>
              </div>
            </div>
            <ion-icon
              :icon="doc.status === 'verified' ? checkmarkCircleOutline : timeOutline"
              class="doc-status-icon"
              :class="doc.status"
            ></ion-icon>
          </div>
        </div>

        <!-- Update Notice -->
        <div class="update-notice">
          <ion-icon :icon="informationCircleOutline"></ion-icon>
          <p>Keep your documents updated to avoid account suspension. Upload new documents before they expire.</p>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonBackButton, IonIcon, alertController } from '@ionic/vue';
import { shieldCheckmarkOutline, documentTextOutline, checkmarkCircleOutline, timeOutline, informationCircleOutline } from 'ionicons/icons';

const router = useRouter();

const documents = ref([
  { id: 1, name: 'Identity Card', expiry: 'Jan 2027', status: 'verified' },
  { id: 2, name: 'NBI or Police Clearance', expiry: 'Jun 2026', status: 'verified' },
  { id: 3, name: 'Certificate of Registration (CR)', expiry: null, status: 'verified' },
  { id: 4, name: 'Official Receipt (OR)', expiry: 'Mar 2026', status: 'verified' },
  { id: 5, name: "Driver's License", expiry: 'Aug 2027', status: 'verified' },
  { id: 6, name: 'Business or Delivery Permit', expiry: 'Dec 2025', status: 'verified' },
]);

const viewDocument = async (doc) => {
  const alert = await alertController.create({
    header: doc.name,
    message: `Status: ${doc.status === 'verified' ? 'Verified' : 'Pending'}\n${doc.expiry ? `Expires: ${doc.expiry}` : 'No expiry date'}`,
    buttons: [
      { text: 'Close', role: 'cancel' },
      {
        text: 'Update Document',
        handler: async () => {
          await uploadDocument(doc);
        }
      }
    ]
  });
  await alert.present();
};

const uploadDocument = async (doc) => {
  const confirmAlert = await alertController.create({
    header: 'Update Document',
    message: `Are you sure you want to update your ${doc.name}? This will require review by our team.`,
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Upload',
        handler: async () => {
          doc.status = 'pending';
          await showSuccessModal(`${doc.name} uploaded!\nAwaiting review...`);
          doc.status = 'verified';
        }
      }
    ]
  });
  await confirmAlert.present();
};

const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      textDiv.style.whiteSpace = 'pre-line';
      
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  await alert.onDidDismiss();
};
</script>

<style scoped>
.docs-content { --background: #f8f9fa; }
.docs-container { padding: 16px; }

.status-summary {
  background: linear-gradient(135deg, #4CAF50, #45a049);
  border-radius: 16px; padding: 20px;
  display: flex; align-items: center; gap: 16px;
  color: white; margin-bottom: 20px;
  box-shadow: 0 4px 16px rgba(76,175,80,0.3);
}
.status-icon { width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.status-icon ion-icon { font-size: 32px; color: white; }
.status-summary h3 { font-size: 16px; font-weight: 700; margin: 0 0 4px 0; }
.status-summary p { font-size: 13px; margin: 0; opacity: 0.9; }

.docs-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px; }

.doc-card {
  background: white; border-radius: 14px;
  padding: 16px; display: flex;
  align-items: center; justify-content: space-between;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06); cursor: pointer;
}
.doc-card:active { transform: scale(0.98); }

.doc-info { display: flex; align-items: center; gap: 14px; }

.doc-icon {
  width: 48px; height: 48px; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; flex-shrink: 0;
}
.doc-icon.verified { background: #E8F5E9; color: #4CAF50; }
.doc-icon.pending { background: #FFF3E0; color: #FF9800; }

.doc-details h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.doc-details p { font-size: 12px; color: #999; margin: 0; }

.doc-status-icon { font-size: 24px; }
.doc-status-icon.verified { color: #4CAF50; }
.doc-status-icon.pending { color: #FF9800; }

.update-notice {
  display: flex; gap: 12px; background: #E3F2FD;
  border-radius: 14px; padding: 16px;
  border-left: 4px solid #2196F3;
}
.update-notice ion-icon { font-size: 22px; color: #2196F3; flex-shrink: 0; margin-top: 2px; }
.update-notice p { font-size: 13px; color: #1565C0; margin: 0; line-height: 1.5; }
</style>