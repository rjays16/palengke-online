<template>
  <ion-page>
    <ion-content :fullscreen="true" class="tracking-content">

      <div id="tracking-map" class="map-container"></div>

      <div class="top-bar">
        <div class="top-btn glass" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>
        <div class="top-right">
          <div class="top-btn glass" @click="getHelp">
            <span class="help-text">Get help</span>
          </div>
          <div class="top-btn glass" @click="shareOrder">
            <ion-icon :icon="shareOutline"></ion-icon>
          </div>
        </div>
      </div>

      <div class="bottom-sheet">
        <div class="sheet-handle" @click="toggleSheet">
          <div class="handle-bar"></div>
        </div>

        <div class="delivery-badge-row">
          <div class="delivery-type-badge">{{ orderStatus.deliveryType }}</div>
          <div class="payment-badge" :class="paymentMethod">
            <span v-if="paymentMethod === 'gcash'">GCash</span>
            <span v-else>💵 Cash On Delivery</span>
          </div>
        </div>

        <div class="eta-section">
          <div class="eta-left">
            <h2 class="eta-time">{{ orderStatus.eta }}</h2>
            <p class="eta-status">
              <span class="status-dot" :class="orderStatus.statusColor"></span>
              {{ orderStatus.statusText }}
            </p>
          </div>
          <div class="driver-avatar-wrap" @click="showDriverInfo">
            <div class="driver-avatar-ring">
              <img :src="driverInfo.image" class="driver-avatar-img">
            </div>
          </div>
        </div>

        <div class="progress-bar-section">
          <div class="progress-track">
            <div class="progress-step done">
              <div class="step-icon store"><span>🏪</span></div>
              <span class="step-label">Store</span>
            </div>
            <div class="progress-line" :class="{ done: currentStep >= 2 }"></div>
            <div class="progress-step" :class="{ done: currentStep >= 2, active: currentStep === 2 }">
              <div class="step-icon prep"><span>🧺</span></div>
              <span class="step-label">Prep</span>
            </div>
            <div class="progress-line" :class="{ done: currentStep >= 3 }"></div>
            <div class="progress-step" :class="{ done: currentStep >= 3, active: currentStep === 3 }">
              <div class="step-icon rider"><span>🏍️</span></div>
              <span class="step-label">Rider</span>
            </div>
            <div class="progress-line" :class="{ done: currentStep >= 4 }"></div>
            <div class="progress-step" :class="{ active: currentStep === 4 }">
              <div class="step-icon home"><span>🏠</span></div>
              <span class="step-label">Arrived</span>
            </div>
          </div>
        </div>

        <div class="promo-banner" v-if="showPromo">
          <div class="promo-icon-wrap">🎁</div>
          <div class="promo-text">
            <p>{{ promoBanner.title }}</p>
            <span>{{ promoBanner.subtitle }}</span>
          </div>
          <div class="promo-close" @click="showPromo = false">✕</div>
        </div>

        <div class="driver-card">
          <img :src="driverInfo.image" class="driver-photo-img">
          <div class="driver-info">
            <p class="driver-name">{{ driverInfo.name }}</p>
            <p class="driver-meta">
              <span class="driver-rating">⭐ {{ driverInfo.rating }}</span>
              <span class="driver-vehicle">{{ driverInfo.vehicleId }}</span>
            </p>
          </div>
          <div class="driver-actions">
            <div class="driver-action-btn" @click="chatDriver">
              <ion-icon :icon="chatbubbleOutline"></ion-icon>
            </div>
            <div class="driver-action-btn" @click="callDriver">
              <ion-icon :icon="callOutline"></ion-icon>
            </div>
            <div class="driver-action-btn report-btn" @click="reportDriver">
              <ion-icon :icon="flagOutline"></ion-icon>
            </div>
          </div>
        </div>

        <div class="cancel-order-section" v-if="canCancelOrder">
          <ion-button expand="block" fill="outline" color="danger" class="cancel-order-btn" @click="cancelOrder">
            <ion-icon slot="start" :icon="closeOutline"></ion-icon>
            Cancel Order
          </ion-button>
        </div>

      </div>

    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage, IonContent, IonIcon,
  alertController, toastController
} from '@ionic/vue';
import {
  arrowBackOutline, shareOutline, chevronForwardOutline,
  chatbubbleOutline, callOutline, closeOutline, flagOutline
} from 'ionicons/icons';
import { submitReport } from '../services/reportService';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const router = useRouter();
const route = useRoute();

let map = null;
let driverMarker = null;
let simulationInterval = null;

const currentStep = ref(3);
const showPromo = ref(true);
const orderState = ref('delivery');
const paymentMethod = ref('gcash');

const canCancelOrder = computed(() => orderState.value === 'pending');

const cancelOrder = async () => {
  const isCOD = paymentMethod.value === 'cod' || paymentMethod.value === 'cash';
  const alert = await alertController.create({
    header: 'Cancel Order?',
    message: isCOD
      ? 'Are you sure you want to cancel your order? You will not be charged since this is Cash On Delivery.'
      : 'Are you sure you want to cancel this order? Your refund will be processed within 1-2 business days.',
    buttons: [
      { text: 'No, Keep Order', role: 'cancel' },
      {
        text: 'Yes, Cancel',
        cssClass: 'cancel-confirm',
        handler: async () => {
          const loadingToast = await toastController.create({
            message: 'Cancelling order...', duration: 1500, color: 'warning', position: 'top'
          });
          await loadingToast.present();
          setTimeout(async () => {
            const toast = await toastController.create({
              message: isCOD ? 'Order cancelled successfully!' : 'Order cancelled. Refund will be processed in 1-2 days.',
              duration: 3000, color: 'success', position: 'top'
            });
            await toast.present();
            router.push('/activity');
          }, 1500);
        }
      }
    ]
  });
  await alert.present();
};

const orderStatus = ref({
  eta: 'Arriving by 1:05 PM',
  statusText: 'On time · Out for delivery!',
  statusColor: 'green',
  deliveryType: 'Standard Delivery'
});

const driverInfo = ref({
  name: 'Juan Dela Cruz',
  rating: '5.0',
  vehicleId: 'ABC 1234 · Honda Click',
  image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d'
});

const promoBanner = ref({
  title: 'Save up to ₱50 on your next order',
  subtitle: 'Use code NEXT50 on your next delivery!'
});

const storeLocation = [8.4820, 124.6472];
const customerLocation = [8.4920, 124.6600];
const driverPos = ref([8.4860, 124.6530]);

const initMap = () => {
  map = L.map('tracking-map', { zoomControl: false, dragging: true }).setView(driverPos.value, 14);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap', maxZoom: 19
  }).addTo(map);

  const storeIcon = L.divIcon({
    className: '',
    html: `<div style="background:#059669;color:white;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;border:3px solid white;box-shadow:0 4px 14px rgba(5,150,105,0.4)">🏪</div>`,
    iconSize: [44, 44], iconAnchor: [22, 22]
  });
  const customerIcon = L.divIcon({
    className: '',
    html: `<div style="background:#dc2626;color:white;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;border:3px solid white;box-shadow:0 4px 14px rgba(220,38,38,0.4)">🏠</div>`,
    iconSize: [44, 44], iconAnchor: [22, 22]
  });
  const driverIcon = L.divIcon({
    className: '',
    html: `<div style="background:#3b82f6;color:white;width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;border:3px solid white;box-shadow:0 4px 14px rgba(59,130,246,0.5)">🏍️</div>`,
    iconSize: [48, 48], iconAnchor: [24, 24]
  });

  L.marker(storeLocation, { icon: storeIcon }).addTo(map);
  L.marker(customerLocation, { icon: customerIcon }).addTo(map);
  driverMarker = L.marker(driverPos.value, { icon: driverIcon }).addTo(map);

  L.polyline([storeLocation, driverPos.value, customerLocation], {
    color: '#059669', weight: 4, opacity: 0.7, dashArray: '8, 6'
  }).addTo(map);

  map.fitBounds(L.latLngBounds([storeLocation, customerLocation, driverPos.value]), { padding: [60, 60] });

  if (orderState.value === 'delivery') simulateDriverMovement();
};

const simulateDriverMovement = () => {
  const steps = 20;
  let step = 0;
  const latDiff = (customerLocation[0] - driverPos.value[0]) / steps;
  const lngDiff = (customerLocation[1] - driverPos.value[1]) / steps;

  simulationInterval = setInterval(() => {
    if (step >= steps) {
      clearInterval(simulationInterval);
      orderStatus.value.statusText = '📍 Driver has arrived!';
      currentStep.value = 4;
      return;
    }
    const newLat = driverPos.value[0] + latDiff;
    const newLng = driverPos.value[1] + lngDiff;
    driverPos.value = [newLat, newLng];
    if (driverMarker) driverMarker.setLatLng(driverPos.value);
    step++;
  }, 3000);
};

const toggleSheet = () => {};
const openPromo = () => {};
const chatDriver = () => router.push('/messages');
const callDriver = () => { window.location.href = 'tel:+639123456789'; };
const reportDriver = async () => {
  const alert = await alertController.create({
    header: 'Report this Driver',
    subHeader: driverInfo.value.name,
    inputs: [
      { name: 'subject', type: 'text', placeholder: 'Short summary' },
      { name: 'description', type: 'textarea', placeholder: 'Describe the issue...' }
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Submit Report',
        handler: async (data) => {
          if (!data.description) {
            const toast = await toastController.create({
              message: 'Please describe the issue', duration: 2000, color: 'warning', position: 'top'
            });
            await toast.present();
            return false
          }
          submitReport({
            type: 'driver',
            submittedBy: driverInfo.value.name,
            subject: data.subject || 'Report: ' + driverInfo.value.name,
            description: data.description,
            priority: 'medium'
          })
          const toast = await toastController.create({
            message: 'Report submitted! We will review it shortly.', duration: 2500, color: 'success', position: 'top'
          });
          await toast.present();
        }
      }
    ]
  });
  await alert.present();
};
const getHelp = () => router.push('/help');
const shareOrder = () => {};
const showDriverInfo = async () => {
  const alert = await alertController.create({
    header: driverInfo.value.name,
    message: `⭐ Rating: ${driverInfo.value.rating}\n🏍️ ${driverInfo.value.vehicleId}`,
    buttons: ['Close']
  });
  await alert.present();
};

const goBack = () => router.push('/activity');

onMounted(() => { setTimeout(() => initMap(), 100); });
onUnmounted(() => {
  if (simulationInterval) clearInterval(simulationInterval);
  if (map) { map.remove(); map = null; }
});
</script>

<style scoped>
.tracking-content { --background: #f5f5f7; }

.map-container {
  position: absolute; top: 0; left: 0;
  width: 100%; height: 100%; z-index: 0;
}

/* ── Top Bar ─────────────────────────── */
.top-bar {
  position: absolute; top: 0; left: 0; right: 0;
  display: flex; justify-content: space-between; align-items: center;
  padding: 52px 16px 12px; z-index: 1000;
}
.top-btn.glass {
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-radius: 24px; padding: 10px 16px;
  display: flex; align-items: center; gap: 6px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;
}
.top-btn.glass:active { transform: scale(0.95); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.top-btn.glass ion-icon { font-size: 20px; color: #1a1a1a; }
.help-text { font-size: 14px; font-weight: 600; color: #059669; }
.top-right { display: flex; gap: 10px; }

/* ── Bottom Sheet ─────────────────────── */
.bottom-sheet {
  position: absolute; left: 0; right: 0; bottom: 0;
  background: #fff; border-radius: 28px 28px 0 0;
  box-shadow: 0 -6px 30px rgba(0,0,0,0.12);
  z-index: 1000; padding-bottom: 24px;
  max-height: 70vh; overflow-y: auto;
}
.bottom-sheet::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0;
  height: 1px; background: linear-gradient(90deg, transparent, rgba(5,150,105,0.15), transparent);
}

.sheet-handle { padding: 14px 0 10px; display: flex; justify-content: center; cursor: pointer; }
.handle-bar { width: 44px; height: 5px; background: #e0e0e0; border-radius: 3px; transition: background 0.2s; }
.sheet-handle:hover .handle-bar { background: #ccc; }

/* ── Delivery Badge ───────────────────── */
.delivery-badge-row { padding: 0 20px 14px; display: flex; gap: 8px; }
.delivery-type-badge {
  display: inline-block; background: #059669; color: white;
  padding: 6px 16px; border-radius: 20px;
  font-size: 12px; font-weight: 700; letter-spacing: 0.3px;
}
.payment-badge {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 6px 14px; border-radius: 20px;
  font-size: 12px; font-weight: 700;
}
.payment-badge.gcash { background: #eff6ff; color: #2563eb; }
.payment-badge.cod { background: #f0fdf4; color: #16a34a; }

/* ── ETA ─────────────────────────────── */
.eta-section {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0 20px 18px;
}
.eta-time {
  font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
  font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;
  letter-spacing: -0.5px;
}
.eta-status {
  display: flex; align-items: center; gap: 6px;
  font-size: 14px; color: #475569; font-weight: 500; margin: 0;
}
.status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.status-dot.green { background: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,0.15); }
.status-dot.orange { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.15); }

.driver-avatar-wrap { cursor: pointer; }
.driver-avatar-ring {
  width: 56px; height: 56px; border-radius: 50%;
  padding: 3px; background: linear-gradient(135deg, #059669, #3b82f6);
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 14px rgba(5,150,105,0.25);
}
.driver-avatar-img {
  width: 100%; height: 100%; border-radius: 50%; object-fit: cover;
  border: 2px solid white;
}

/* ── Progress Bar ────────────────────── */
.progress-bar-section { padding: 0 20px 16px; }
.progress-track {
  display: flex; align-items: center;
  background: #f8fafc; border-radius: 16px; padding: 16px 16px 12px;
  border: 1px solid #f1f5f9;
}
.progress-step { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.step-icon {
  width: 38px; height: 38px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; background: #e2e8f0;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.step-label { font-size: 10px; font-weight: 600; color: #94a3b8; letter-spacing: 0.2px; transition: color 0.3s; }
.progress-step.done .step-icon { background: #059669; transform: scale(1.05); }
.progress-step.done .step-label { color: #059669; }
.progress-step.active .step-icon {
  background: #059669;
  box-shadow: 0 0 0 6px rgba(5,150,105,0.15);
  animation: pulse-progress 2s infinite;
}
.progress-step.active .step-label { color: #059669; font-weight: 700; }

@keyframes pulse-progress {
  0%, 100% { box-shadow: 0 0 0 4px rgba(5,150,105,0.15); }
  50% { box-shadow: 0 0 0 8px rgba(5,150,105,0.08); }
}

.progress-line {
  flex: 1; height: 3px; background: #e2e8f0;
  margin: 0 4px; border-radius: 2px; transition: background 0.4s;
}
.progress-line.done { background: #059669; }

/* ── Promo Banner ────────────────────── */
.promo-banner {
  margin: 0 20px 16px; background: linear-gradient(135deg, #fef3c7, #fde68a);
  border-radius: 14px; padding: 14px 16px;
  display: flex; align-items: center; gap: 12px;
  position: relative; overflow: hidden;
}
.promo-banner::after {
  content: '🎉'; position: absolute; right: -10px; bottom: -10px;
  font-size: 60px; opacity: 0.12;
}
.promo-icon-wrap { font-size: 28px; flex-shrink: 0; }
.promo-text { flex: 1; z-index: 1; }
.promo-text p { font-size: 14px; font-weight: 700; color: #92400e; margin: 0 0 2px 0; }
.promo-text span { font-size: 12px; color: #b45309; font-weight: 500; }
.promo-close {
  position: absolute; top: 8px; right: 8px;
  width: 22px; height: 22px; border-radius: 50%;
  background: rgba(0,0,0,0.06);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; color: #92400e; cursor: pointer; z-index: 1;
}

/* ── Driver Card ─────────────────────── */
.driver-card {
  margin: 0 20px; background: #f8fafc;
  border-radius: 16px; padding: 16px;
  display: flex; align-items: center; gap: 14px;
  border: 1px solid #f1f5f9;
  transition: box-shadow 0.2s;
}
.driver-card:active { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
.driver-photo-img {
  width: 56px; height: 56px; border-radius: 50%; object-fit: cover;
  border: 2px solid #e2e8f0; flex-shrink: 0;
}
.driver-info { flex: 1; min-width: 0; }
.driver-name { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.driver-meta { font-size: 12px; color: #64748b; margin: 0; display: flex; flex-wrap: wrap; gap: 6px; }
.driver-vehicle { color: #94a3b8; }
.driver-actions { display: flex; gap: 8px; flex-shrink: 0; }
.driver-action-btn {
  width: 42px; height: 42px; background: white;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: transform 0.15s, box-shadow 0.15s;
}
.driver-action-btn:active { transform: scale(0.9); box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.driver-action-btn ion-icon { font-size: 20px; color: #475569; }
.driver-action-btn.report-btn ion-icon { color: #94a3b8; }
.driver-action-btn.report-btn:active ion-icon { color: #dc2626; }

/* ── Cancel Order ────────────────────── */
.cancel-order-section { margin: 16px 20px 0; padding-top: 16px; border-top: 1px solid #f1f5f9; }
.cancel-order-btn {
  --border-radius: 12px; --border-color: #ef4444; --color: #ef4444;
  height: 48px; font-weight: 600; text-transform: none;
  font-family: -apple-system, sans-serif;
}
</style>
