<template>
  <ion-page>
    <ion-content :fullscreen="true" class="tracking-content">

      <!-- Map -->
      <div id="tracking-map" class="map-container"></div>

      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-btn" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>
        <div class="top-right">
          <div class="top-btn" @click="getHelp">
            <span class="help-text">Get help</span>
          </div>
          <div class="top-btn" @click="shareOrder">
            <ion-icon :icon="shareOutline"></ion-icon>
          </div>
        </div>
      </div>

      <!-- Bottom Sheet -->
      <div class="bottom-sheet">
        <div class="sheet-handle" @click="toggleSheet">
          <div class="handle-bar"></div>
        </div>

        <!-- Delivery Type Badge -->
        <div class="delivery-badge-row">
          <div class="delivery-type-badge">{{ orderStatus.deliveryType }}</div>
        </div>

        <!-- ETA -->
        <div class="eta-section">
          <div class="eta-left">
            <h2 class="eta-time">{{ orderStatus.eta }}</h2>
            <p class="eta-status">
              <span class="status-dot" :class="orderStatus.statusColor"></span>
              {{ orderStatus.statusText }}
            </p>
          </div>
          <div class="driver-avatar-wrap" @click="showDriverInfo">
            <div class="driver-avatar">🏍️</div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar-section">
          <div class="progress-track">
            <!-- Store Step -->
            <div class="progress-step done">
              <div class="step-icon store">
                <span>🏪</span>
              </div>
            </div>
            <!-- Line 1 -->
            <div class="progress-line" :class="{ done: currentStep >= 2 }"></div>
            <!-- Prep Step -->
            <div class="progress-step" :class="{ done: currentStep >= 2, active: currentStep === 2 }">
              <div class="step-icon prep">
                <span>🍳</span>
              </div>
            </div>
            <!-- Line 2 -->
            <div class="progress-line" :class="{ done: currentStep >= 3 }"></div>
            <!-- Rider Step -->
            <div class="progress-step" :class="{ done: currentStep >= 3, active: currentStep === 3 }">
              <div class="step-icon rider">
                <span>🏍️</span>
              </div>
            </div>
            <!-- Line 3 -->
            <div class="progress-line" :class="{ done: currentStep >= 4 }"></div>
            <!-- Home Step -->
            <div class="progress-step" :class="{ active: currentStep === 4 }">
              <div class="step-icon home">
                <span>🏠</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Promo Banner -->
        <div class="promo-banner" v-if="showPromo">
          <div class="promo-icon">🎁</div>
          <div class="promo-text">
            <p>{{ promoBanner.title }}</p>
            <span>{{ promoBanner.subtitle }}</span>
          </div>
          <ion-icon :icon="chevronForwardOutline" @click="openPromo"></ion-icon>
          <div class="promo-close" @click="showPromo = false">✕</div>
        </div>

        <!-- Driver Info -->
        <div class="driver-card">
          <div class="driver-photo">🧑</div>
          <div class="driver-info">
            <p class="driver-name">{{ driverInfo.name }}</p>
            <p class="driver-meta">
              ⭐ {{ driverInfo.rating }}
              <span class="driver-id">{{ driverInfo.vehicleId }}</span>
            </p>
          </div>
          <div class="driver-actions">
            <div class="driver-action-btn" @click="chatDriver">
              <ion-icon :icon="chatbubbleOutline"></ion-icon>
            </div>
            <div class="driver-action-btn" @click="callDriver">
              <ion-icon :icon="callOutline"></ion-icon>
            </div>
          </div>
        </div>

      </div>

    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage, IonContent, IonIcon,
  alertController, toastController
} from '@ionic/vue';
import {
  arrowBackOutline, shareOutline, chevronForwardOutline,
  chatbubbleOutline, callOutline
} from 'ionicons/icons';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const router = useRouter();
const route = useRoute();

let map = null;
let driverMarker = null;
let simulationInterval = null;

const currentStep = ref(3);
const showPromo = ref(true);

const orderStatus = ref({
  eta: 'Arriving by 1:05 PM',
  statusText: 'On time · Out for delivery!',
  statusColor: 'green',
  deliveryType: 'Standard Delivery'
});

const driverInfo = ref({
  name: 'Juan Dela Cruz',
  rating: '5.0',
  vehicleId: 'ABC 1234 · Honda Click'
});

const promoBanner = ref({
  title: 'Save up to ₱50 on your next order',
  subtitle: 'Use code NEXT50 on your next delivery!'
});

const storeLocation = [8.4820, 124.6472];
const customerLocation = [8.4920, 124.6600];
const driverPos = ref([8.4860, 124.6530]);

const initMap = () => {
  map = L.map('tracking-map', { zoomControl: false, dragging: true })
    .setView(driverPos.value, 14);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap', maxZoom: 19
  }).addTo(map);

  // Store marker
  const storeIcon = L.divIcon({
    className: '',
    html: `<div style="background:#4CAF50;color:white;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;border:3px solid white;box-shadow:0 3px 10px rgba(0,0,0,0.3)">🏪</div>`,
    iconSize: [40, 40], iconAnchor: [20, 20]
  });

  // Customer marker
  const customerIcon = L.divIcon({
    className: '',
    html: `<div style="background:#f44336;color:white;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;border:3px solid white;box-shadow:0 3px 10px rgba(0,0,0,0.3)">🏠</div>`,
    iconSize: [40, 40], iconAnchor: [20, 20]
  });

  // Driver marker
  const driverIcon = L.divIcon({
    className: '',
    html: `<div style="background:#2196F3;color:white;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;border:3px solid white;box-shadow:0 3px 12px rgba(0,0,0,0.3)">🏍️</div>`,
    iconSize: [44, 44], iconAnchor: [22, 22]
  });

  L.marker(storeLocation, { icon: storeIcon }).addTo(map);
  L.marker(customerLocation, { icon: customerIcon }).addTo(map);
  driverMarker = L.marker(driverPos.value, { icon: driverIcon }).addTo(map);

  // Route line
  L.polyline([storeLocation, driverPos.value, customerLocation], {
    color: '#4CAF50', weight: 5, opacity: 0.8, dashArray: '10, 5'
  }).addTo(map);

  // Fit bounds
  map.fitBounds(L.latLngBounds([storeLocation, customerLocation, driverPos.value]), { padding: [60, 60] });

  // Simulate driver movement
  simulateDriverMovement();
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
const chatDriver = () => {
  router.push('/messages');
};
const callDriver = () => { window.location.href = 'tel:+639123456789'; };
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

const goBack = async () => {
  const alert = await alertController.create({
    header: 'Leave tracking?',
    message: 'Your order is still being delivered. You can come back anytime.',
    buttons: [
      { text: 'Stay', role: 'cancel' },
      { text: 'Leave', handler: () => router.push('/home') }
    ]
  });
  await alert.present();
};

onMounted(() => { setTimeout(() => initMap(), 100); });
onUnmounted(() => {
  if (simulationInterval) clearInterval(simulationInterval);
  if (map) { map.remove(); map = null; }
});
</script>

<style scoped>
.tracking-content { --background: #f0f0f0; }

.map-container {
  position: absolute; top: 0; left: 0;
  width: 100%; height: 100%; z-index: 0;
}

/* Top Bar */
.top-bar {
  position: absolute; top: 0; left: 0; right: 0;
  display: flex; justify-content: space-between; align-items: center;
  padding: 52px 16px 12px; z-index: 1000;
}

.top-btn {
  background: white; border-radius: 24px;
  padding: 8px 14px; display: flex; align-items: center;
  gap: 6px; box-shadow: 0 3px 12px rgba(0,0,0,0.15);
  cursor: pointer;
}
.top-btn ion-icon { font-size: 20px; color: #1a1a1a; }
.help-text { font-size: 14px; font-weight: 600; color: #4CAF50; }
.top-right { display: flex; gap: 10px; }

/* Bottom Sheet */
.bottom-sheet {
  position: absolute; left: 0; right: 0; bottom: 0;
  background: white; border-radius: 24px 24px 0 0;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
  z-index: 1000; padding-bottom: 20px;
}

.sheet-handle { padding: 12px 0 8px; display: flex; justify-content: center; cursor: pointer; }
.handle-bar { width: 40px; height: 4px; background: #ddd; border-radius: 2px; }

/* Delivery Badge */
.delivery-badge-row { padding: 0 20px 12px; }
.delivery-type-badge {
  display: inline-block; background: #1a1a1a; color: white;
  padding: 6px 16px; border-radius: 20px;
  font-size: 13px; font-weight: 700;
}

/* ETA */
.eta-section {
  display: flex; justify-content: space-between; align-items: flex-start;
  padding: 0 20px 16px;
}
.eta-time { font-size: 24px; font-weight: 800; color: #1a1a1a; margin: 0 0 6px 0; }
.eta-status { display: flex; align-items: center; gap: 6px; font-size: 14px; color: #333; font-weight: 500; margin: 0; }
.status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.status-dot.green { background: #4CAF50; }
.status-dot.orange { background: #FF9800; }

.driver-avatar-wrap { cursor: pointer; }
.driver-avatar {
  width: 52px; height: 52px; background: #E3F2FD;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; font-size: 26px;
  border: 3px solid #2196F3;
}

/* Progress Bar */
.progress-bar-section { padding: 0 20px 16px; }
.progress-track {
  display: flex; align-items: center;
  background: #f8f9fa; border-radius: 16px; padding: 14px 16px;
}

.progress-step { display: flex; flex-direction: column; align-items: center; }
.step-icon {
  width: 36px; height: 36px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; background: #e0e0e0;
  transition: all 0.3s;
}
.progress-step.done .step-icon { background: #4CAF50; }
.progress-step.active .step-icon {
  background: #4CAF50;
  box-shadow: 0 0 0 6px rgba(76,175,80,0.2);
  animation: pulse-progress 2s infinite;
}

@keyframes pulse-progress {
  0%, 100% { box-shadow: 0 0 0 4px rgba(76,175,80,0.2); }
  50% { box-shadow: 0 0 0 8px rgba(76,175,80,0.1); }
}

.progress-line {
  flex: 1; height: 3px; background: #e0e0e0;
  margin: 0 4px; border-radius: 2px; transition: background 0.3s;
}
.progress-line.done { background: #4CAF50; }

/* Promo Banner */
.promo-banner {
  margin: 0 20px 16px; background: #FFF3E0;
  border-radius: 14px; padding: 14px 16px;
  display: flex; align-items: center; gap: 12px; position: relative;
}
.promo-icon { font-size: 28px; flex-shrink: 0; }
.promo-text { flex: 1; }
.promo-text p { font-size: 14px; font-weight: 600; color: #E65100; margin: 0 0 3px 0; }
.promo-text span { font-size: 12px; color: #FF9800; }
.promo-banner ion-icon { font-size: 20px; color: #FF9800; cursor: pointer; }
.promo-close {
  position: absolute; top: 8px; right: 8px;
  font-size: 14px; color: #FF9800; cursor: pointer;
  width: 20px; height: 20px; display: flex;
  align-items: center; justify-content: center;
}

/* Driver Card */
.driver-card {
  margin: 0 20px; background: #f8f9fa; border-radius: 16px;
  padding: 14px 16px; display: flex; align-items: center; gap: 14px;
}
.driver-photo {
  width: 56px; height: 56px; background: #e0e0e0;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; font-size: 28px; flex-shrink: 0;
  border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.driver-info { flex: 1; }
.driver-name { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.driver-meta { font-size: 13px; color: #666; margin: 0; }
.driver-id { margin-left: 8px; color: #999; }
.driver-actions { display: flex; gap: 10px; }
.driver-action-btn {
  width: 44px; height: 44px; background: white;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.driver-action-btn ion-icon { font-size: 22px; color: #1a1a1a; }
</style>