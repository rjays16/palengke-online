<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Report an Issue</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="dispute-content">
      <div v-if="existing" class="existing-dispute-card">
        <div class="existing-icon">📋</div>
        <h3>Dispute Already Filed</h3>
        <p>You have already reported an issue for this order.</p>
        <div class="existing-details">
          <div class="ed-row"><span>Reason:</span><span>{{ existing.reasonLabel }}</span></div>
          <div class="ed-row"><span>Status:</span><span class="status-badge" :class="existing.status">{{ existing.status }}</span></div>
          <div class="ed-row"><span>Filed on:</span><span>{{ existing.date }}</span></div>
        </div>
        <ion-button expand="block" color="medium" @click="goBack">Back to Orders</ion-button>
      </div>

      <div v-else class="form-wrapper">
        <div class="order-summary-card">
          <h3>Order #{{ orderId }}</h3>
          <p class="os-store">{{ order?.storeName || 'Unknown Store' }}</p>
          <p class="os-total">₱{{ order?.total?.toFixed(2) || '0.00' }}</p>
        </div>

        <div class="form-section">
          <label class="form-label">What went wrong? <span class="req">*</span></label>
          <div class="reason-list">
            <div
              v-for="r in reasons"
              :key="r.value"
              class="reason-option"
              :class="{ selected: form.reason === r.value }"
              @click="form.reason = r.value; form.reasonLabel = r.label"
            >
              <div class="reason-radio" :class="{ checked: form.reason === r.value }"></div>
              <span>{{ r.label }}</span>
            </div>
          </div>
        </div>

        <div class="form-section">
          <label class="form-label">Describe the issue <span class="req">*</span></label>
          <ion-textarea
            v-model="form.description"
            placeholder="Tell us what happened..."
            rows="4"
            class="custom-textarea"
          ></ion-textarea>
        </div>

        <div class="form-section">
          <label class="form-label">Photo (optional)</label>
          <div class="photo-upload-area" @click="triggerFileInput">
            <input type="file" ref="fileInput" accept="image/*" @change="onFileSelected" hidden />
            <div v-if="!form.image" class="upload-placeholder">
              <ion-icon :icon="cameraOutline"></ion-icon>
              <span>Tap to add a photo</span>
            </div>
            <div v-else class="upload-preview">
              <img :src="form.image" class="preview-img" />
              <div class="remove-photo" @click.stop="form.image = ''">✕</div>
            </div>
          </div>
        </div>

        <div class="submit-section">
          <ion-button expand="block" color="danger" :disabled="!canSubmit" @click="submitDispute">
            <ion-icon :icon="warningOutline" slot="start"></ion-icon>
            Submit Report
          </ion-button>
          <p class="submit-hint">Your report will be reviewed by our team within 24 hours.</p>
        </div>
      </div>

      <div class="custom-modal-overlay" v-if="showSuccess" @click="showSuccess = false">
        <div class="custom-modal" @click.stop>
          <img src="/assets/images/checkmark_success.png" alt="Success" class="success-img">
          <h3>Report Submitted!</h3>
          <p>We will review your report and get back to you within 24 hours.</p>
          <ion-button expand="block" class="modal-btn" @click="goBack">OK</ion-button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonButton, IonIcon, IonTextarea,
} from '@ionic/vue'
import { arrowBackOutline, cameraOutline, warningOutline } from 'ionicons/icons'
import { DISPUTE_REASONS, submitDispute as saveDispute, getDisputeByOrderId, readFileAsDataURL } from '../services/disputeService'

const router = useRouter()
const route = useRoute()
const orderId = route.params.orderId

const reasons = DISPUTE_REASONS
const fileInput = ref(null)
const showSuccess = ref(false)

const form = ref({
  reason: '',
  reasonLabel: '',
  description: '',
  image: '',
})

const existing = computed(() => getDisputeByOrderId(orderId))

const canSubmit = computed(() => form.value.reason && form.value.description.trim())

const order = ref(null)

onMounted(() => {
  const stored = localStorage.getItem('lastOrder_' + orderId)
  if (stored) {
    try { order.value = JSON.parse(stored) } catch {}
  }
})

function triggerFileInput() {
  fileInput.value?.click()
}

async function onFileSelected(e) {
  const file = e.target.files?.[0]
  if (!file) return
  try {
    form.value.image = await readFileAsDataURL(file)
  } catch {}
}

function submitDispute() {
  if (!canSubmit.value) return

  saveDispute({
    orderId,
    customerName: 'You',
    sellerName: order.value?.storeName || 'Unknown',
    storeName: order.value?.storeName || 'Unknown',
    reason: form.value.reason,
    reasonLabel: form.value.reasonLabel,
    description: form.value.description,
    image: form.value.image || undefined,
    priority: reasons.find(r => r.value === form.value.reason)?.priority || 'medium',
  })

  showSuccess.value = true
}

function goBack() {
  router.back()
}
</script>

<style scoped>
.dispute-content { --background: #f8f9fa; }
.form-wrapper { padding: 16px; display: flex; flex-direction: column; gap: 16px; }

.order-summary-card {
  background: white; border-radius: 16px; padding: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-align: center;
}
.order-summary-card h3 {
  font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px;
}
.os-store { font-size: 14px; color: #666; margin: 0 0 4px; }
.os-total { font-size: 22px; font-weight: 800; color: #4CAF50; margin: 0; }

.form-section { background: white; border-radius: 16px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.form-label { display: block; font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 12px; }
.req { color: #f44336; }

.reason-list { display: flex; flex-direction: column; gap: 2px; }
.reason-option {
  display: flex; align-items: center; gap: 12px;
  padding: 12px; border-radius: 10px; cursor: pointer;
  transition: background 0.15s;
}
.reason-option:hover { background: #f5f5f5; }
.reason-option.selected { background: #FFF3E0; }
.reason-radio {
  width: 20px; height: 20px; border-radius: 50%;
  border: 2px solid #ccc; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.15s;
}
.reason-radio.checked { border-color: #f44336; }
.reason-radio.checked::after {
  content: ''; width: 10px; height: 10px;
  background: #f44336; border-radius: 50%;
}
.custom-textarea { --background: #f9f9f9; --border-radius: 10px; --padding-start: 12px; --padding-end: 12px; font-size: 14px; }

.photo-upload-area { cursor: pointer; }
.upload-placeholder {
  border: 2px dashed #ddd; border-radius: 12px;
  padding: 32px; display: flex; flex-direction: column;
  align-items: center; gap: 8px; color: #999;
  transition: border-color 0.15s;
}
.upload-placeholder:hover { border-color: #4CAF50; }
.upload-placeholder ion-icon { font-size: 40px; }
.upload-placeholder span { font-size: 13px; }
.upload-preview { position: relative; display: inline-block; }
.preview-img { max-height: 180px; border-radius: 12px; width: 100%; object-fit: cover; }
.remove-photo {
  position: absolute; top: 8px; right: 8px;
  width: 28px; height: 28px; border-radius: 50%;
  background: rgba(0,0,0,0.6); color: white;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; cursor: pointer;
}

.submit-section { text-align: center; }
.submit-section ion-button { --border-radius: 12px; height: 50px; font-weight: 600; text-transform: none; font-family: inherit; }
.submit-hint { font-size: 12px; color: #999; margin: 10px 0 0; }

/* Existing dispute */
.existing-dispute-card {
  margin: 32px 16px; background: white; border-radius: 16px;
  padding: 32px 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.existing-icon { font-size: 48px; margin-bottom: 12px; }
.existing-dispute-card h3 { font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px; }
.existing-dispute-card p { font-size: 14px; color: #666; margin: 0 0 16px; }
.existing-details { text-align: left; background: #f9f9f9; border-radius: 10px; padding: 14px; margin-bottom: 16px; }
.ed-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
.ed-row span:first-child { color: #999; }
.ed-row span:last-child { font-weight: 600; color: #1a1a1a; }
.status-badge { padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.open { background: #FFEBEE; color: #f44336; }
.status-badge.investigating { background: #FFF3E0; color: #FF9800; }
.status-badge.resolved { background: #E8F5E9; color: #4CAF50; }
.status-badge.dismissed { background: #f5f5f5; color: #999; }

.custom-modal-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5); display: flex;
  align-items: center; justify-content: center;
  z-index: 99999; padding: 32px;
}
.custom-modal {
  background: white; border-radius: 24px;
  padding: 40px 32px 24px; text-align: center;
  width: 100%; max-width: 320px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.success-img { width: 80px; height: 80px; margin: 0 auto 16px; display: block; }
.custom-modal h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px; }
.custom-modal p { font-size: 14px; color: #666; margin: 0 0 24px; }
.modal-btn { --background: #4CAF50; --border-radius: 12px; height: 48px; font-weight: 600; text-transform: none; font-family: inherit; }
</style>
