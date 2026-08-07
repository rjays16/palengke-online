<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/orders"></ion-back-button>
        </ion-buttons>
        <ion-title>Track Driver</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="refreshLocation">
            <ion-icon :icon="refreshOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div class="tracking-container">
        <!-- Map Container - FULL SCREEN -->
        <div id="map" class="map-container"></div>

        <!-- Floating Toggle Button -->
        <div class="toggle-button" @click="toggleCards">
          <ion-icon :icon="cardsExpanded ? chevronDownOutline : chevronUpOutline"></ion-icon>
          <span v-if="!cardsExpanded">Show Info</span>
        </div>

        <!-- Collapsible Bottom Sheet -->
        <div class="bottom-sheet" :class="{ expanded: cardsExpanded, collapsed: !cardsExpanded }">
          <!-- Handle Bar -->
          <div class="handle-bar" @click="toggleCards">
            <div class="handle"></div>
          </div>

          <!-- Compact Header (When Collapsed) -->
          <div v-if="!cardsExpanded" class="compact-header" @click="toggleCards">
            <div class="compact-avatar">
              <ion-icon :icon="personOutline"></ion-icon>
            </div>
            <div class="compact-info">
              <h4>{{ driverData.name }}</h4>
              <p>{{ trackingData.distance }} • {{ trackingData.duration }}</p>
            </div>
            <div class="compact-status">
              <span class="status-dot"></span>
              En Route
            </div>
          </div>

          <!-- Full Content (When Expanded) -->
          <div v-if="cardsExpanded" class="expanded-content">
            <!-- Driver Info -->
            <div class="driver-section">
              <div class="driver-header">
                <div class="driver-avatar">
                  <img v-if="driverData.avatar" :src="driverData.avatar" alt="Driver" />
                  <ion-icon v-else :icon="personOutline"></ion-icon>
                </div>
                <div class="driver-info">
                  <h3>{{ driverData.name }}</h3>
                  <p class="driver-vehicle">{{ driverData.vehicle }} • {{ driverData.plateNumber }}</p>
                  <div class="driver-status" :class="driverData.status">
                    <span class="status-dot"></span>
                    {{ driverData.statusText }}
                  </div>
                </div>
                <ion-button fill="clear" @click="callDriver" class="call-button">
                  <ion-icon :icon="callOutline" color="success"></ion-icon>
                </ion-button>
              </div>

              <!-- Stats -->
              <div class="tracking-stats">
                <div class="stat">
                  <ion-icon :icon="navigateOutline"></ion-icon>
                  <div class="stat-info">
                    <span class="stat-value">{{ trackingData.distance }}</span>
                    <span class="stat-label">Distance</span>
                  </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                  <ion-icon :icon="timeOutline"></ion-icon>
                  <div class="stat-info">
                    <span class="stat-value">{{ trackingData.duration }}</span>
                    <span class="stat-label">Time Left</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Details -->
            <div class="order-section">
              <h4 class="section-title">Order Details</h4>
              <div class="detail-item">
                <ion-icon :icon="receiptOutline"></ion-icon>
                <div class="detail-content">
                  <span class="detail-label">Order ID</span>
                  <span class="detail-value">#{{ orderData.id }}</span>
                </div>
              </div>
              <div class="detail-item">
                <ion-icon :icon="locationOutline"></ion-icon>
                <div class="detail-content">
                  <span class="detail-label">Delivery Address</span>
                  <span class="detail-value">{{ orderData.deliveryAddress }}</span>
                </div>
              </div>
            </div>

            <!-- Timeline -->
            <div class="timeline-section">
              <h4 class="section-title">Delivery Progress</h4>
              <div class="timeline">
                <div
                  class="timeline-item"
                  :class="{ completed: step.completed, active: step.active }"
                  v-for="step in deliverySteps"
                  :key="step.id"
                >
                  <div class="timeline-icon">
                    <ion-icon :icon="step.completed ? checkmarkCircleOutline : step.icon"></ion-icon>
                  </div>
                  <div class="timeline-content">
                    <p class="timeline-label">{{ step.label }}</p>
                    <p class="timeline-time" v-if="step.time">{{ step.time }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Button -->
            <div class="action-section">
              <ion-button expand="block" @click="chatWithDriver" fill="outline" color="success">
                <ion-icon :icon="chatbubbleEllipsesOutline" slot="start"></ion-icon>
                Message Driver
              </ion-button>
            </div>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonButton,
  IonBackButton,
  IonIcon,
} from '@ionic/vue';
import {
  personOutline,
  callOutline,
  receiptOutline,
  locationOutline,
  timeOutline,
  navigateOutline,
  chatbubbleEllipsesOutline,
  checkmarkCircleOutline,
  refreshOutline,
  basketOutline,
  bicycleOutline,
  homeOutline,
  chevronUpOutline,
  chevronDownOutline,
} from 'ionicons/icons';
import { type DriverInfo, type OrderTrackingInfo, type TrackingMetrics, type DeliveryStep } from '@/types';
import { useSuccessModal } from '@/composables/useSuccessModal';
import { useModals } from '@/composables/useModals';

// Import Leaflet
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const router = useRouter();
const { showSuccessModal } = useSuccessModal();
const { showLoadingModal } = useModals();

let map: any = null;
let driverMarker: any = null;
let routeLine: any = null;
let locationUpdateInterval: any = null;

// UI State
const cardsExpanded = ref(false);

// Driver Data
const driverData = ref({
  name: 'Miguel Santos',
  vehicle: 'Motorcycle',
  plateNumber: 'ABC 1234',
  phone: '+63 912 345 6789',
  avatar: null,
  status: 'on-route',
  statusText: 'On the way',
  currentLocation: [8.485, 124.6517],
} as DriverInfo);

// Order Data
const orderData = ref({
  id: '12345',
  deliveryAddress: 'Brgy. Carmen, Cagayan de Oro City',
  eta: '15 minutes',
  customerName: 'Maria Santos',
} as OrderTrackingInfo);

// Tracking Data
const trackingData = ref({
  distance: '3.2 km',
  duration: '15 min',
} as TrackingMetrics);

// Delivery Steps
const deliverySteps = ref([
  {
    id: 1,
    icon: basketOutline,
    label: 'Order Picked Up',
    time: '2:30 PM',
    completed: true,
    active: false,
  },
  {
    id: 2,
    icon: bicycleOutline,
    label: 'Driver En Route',
    time: null,
    completed: false,
    active: true,
  },
  {
    id: 3,
    icon: homeOutline,
    label: 'Delivered',
    time: null,
    completed: false,
    active: false,
  },
] as DeliveryStep[]);

// Locations
const sellerLocation: [number, number] = [8.485, 124.6517];
const customerLocation: [number, number] = [8.492, 124.66];

// Toggle cards
const toggleCards = () => {
  cardsExpanded.value = !cardsExpanded.value;

  // Resize map after animation
  setTimeout(() => {
    if (map) {
      map.invalidateSize();
    }
  }, 300);
};

// Initialize Map
const initMap = () => {
  map = L.map('map').setView(driverData.value.currentLocation, 14);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(map);

  // Custom icons
  const sellerIcon = L.divIcon({
    className: 'custom-marker',
    html: '<div style="background: #4CAF50; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">S</div>',
    iconSize: [40, 40],
  });

  const customerIcon = L.divIcon({
    className: 'custom-marker',
    html: '<div style="background: #f44336; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">C</div>',
    iconSize: [40, 40],
  });

  const driverIcon = L.divIcon({
    className: 'custom-marker',
    html: '<div style="background: #2196F3; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-truck"></i></div>',
    iconSize: [40, 40],
  });

  // Add markers
  L.marker(sellerLocation, { icon: sellerIcon })
    .addTo(map)
    .bindPopup('<b><i class="fas fa-store"></i> Your Shop</b><br>Pickup Location');

  L.marker(customerLocation, { icon: customerIcon })
    .addTo(map)
    .bindPopup('<b><i class="fas fa-home"></i> Customer</b><br>' + orderData.value.customerName);

  driverMarker = L.marker(driverData.value.currentLocation, { icon: driverIcon })
    .addTo(map)
    .bindPopup('<b><i class="fas fa-truck"></i> ' + driverData.value.name + '</b><br>' + driverData.value.vehicle);

  drawRoute();
  fitMapToMarkers();
};

// Draw route
const drawRoute = () => {
  if (routeLine) {
    map.removeLayer(routeLine);
  }

  routeLine = L.polyline([driverData.value.currentLocation, customerLocation], {
    color: '#2196F3',
    weight: 4,
    opacity: 0.8,
  }).addTo(map);

  const distance = map.distance(driverData.value.currentLocation, customerLocation);
  const distanceKm = (distance / 1000).toFixed(1);
  const durationMin = Math.ceil(distance / 400);

  trackingData.value.distance = distanceKm + ' km';
  trackingData.value.duration = durationMin + ' min';
  orderData.value.eta = durationMin + ' minutes';
};

// Fit map to markers
const fitMapToMarkers = () => {
  const bounds = L.latLngBounds([sellerLocation, customerLocation, driverData.value.currentLocation]);
  map.fitBounds(bounds, { padding: [50, 50] });
};

// Simulate driver movement
const simulateDriverMovement = () => {
  locationUpdateInterval = setInterval(() => {
    const currentLat = driverData.value.currentLocation[0];
    const currentLng = driverData.value.currentLocation[1];

    const newLat = currentLat + (customerLocation[0] - currentLat) * 0.02;
    const newLng = currentLng + (customerLocation[1] - currentLng) * 0.02;

    driverData.value.currentLocation = [newLat, newLng];

    if (driverMarker) {
      driverMarker.setLatLng(driverData.value.currentLocation);
    }

    drawRoute();

    const distance = map.distance(driverData.value.currentLocation, customerLocation);
    if (distance < 100) {
      clearInterval(locationUpdateInterval);
      showSuccessModal('Driver has arrived at delivery location!');

      deliverySteps.value[1].completed = true;
      deliverySteps.value[1].active = false;
      deliverySteps.value[2].completed = true;
      deliverySteps.value[2].time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }
  }, 5000);
};

// Refresh location
const refreshLocation = async () => {
  await showLoadingModal('Updating location...');
  drawRoute();
  fitMapToMarkers();
};

// Actions
const callDriver = () => {
  window.location.href = `tel:${driverData.value.phone}`;
};

const chatWithDriver = () => {
  router.push('/messages');
};

// Lifecycle
onMounted(() => {
  setTimeout(() => {
    initMap();
    simulateDriverMovement();
  }, 100);
});

onUnmounted(() => {
  if (locationUpdateInterval) {
    clearInterval(locationUpdateInterval);
  }

  if (map) {
    map.remove();
    map = null;
  }
});
</script>

<style scoped>
.tracking-container {
  position: relative;
  height: 100%;
}

/* Map Container - FULL SCREEN */
.map-container {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 0;
}

/* Floating Toggle Button (When Collapsed) */
.toggle-button {
  position: absolute;
  top: 20px;
  right: 20px;
  background: white;
  border-radius: 30px;
  padding: 12px 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 900;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.3s;
}

.toggle-button ion-icon {
  font-size: 20px;
  color: #4caf50;
}

.toggle-button span {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.toggle-button:active {
  transform: scale(0.95);
}

/* Bottom Sheet */
.bottom-sheet {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  background: white;
  border-radius: 20px 20px 0 0;
  box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  transition:
    transform 0.3s ease,
    height 0.3s ease;
}

.bottom-sheet.collapsed {
  height: 100px;
  transform: translateY(0);
}

.bottom-sheet.expanded {
  height: 70vh;
  max-height: 600px;
  transform: translateY(0);
  overflow-y: auto;
}

/* Handle Bar */
.handle-bar {
  padding: 12px 0;
  display: flex;
  justify-content: center;
  cursor: pointer;
}

.handle {
  width: 40px;
  height: 4px;
  background: #ddd;
  border-radius: 2px;
}

/* Compact Header (Collapsed State) */
.compact-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 20px 16px 20px;
  cursor: pointer;
}

.compact-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #e8f5e9;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.compact-avatar ion-icon {
  font-size: 24px;
  color: #4caf50;
}

.compact-info {
  flex: 1;
}

.compact-info h4 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.compact-info p {
  font-size: 13px;
  color: #666;
  margin: 0;
}

.compact-status {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #2196f3;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #2196f3;
  animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Expanded Content */
.expanded-content {
  padding: 0 20px 20px 20px;
}

.section-title {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 12px 0;
}

/* Driver Section */
.driver-section {
  margin-bottom: 24px;
}

.driver-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.driver-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #e8f5e9;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex-shrink: 0;
}

.driver-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.driver-avatar ion-icon {
  font-size: 28px;
  color: #4caf50;
}

.driver-info {
  flex: 1;
}

.driver-info h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.driver-vehicle {
  font-size: 13px;
  color: #666;
  margin: 0 0 6px 0;
}

.driver-status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 12px;
}

.driver-status.on-route {
  background: #e3f2fd;
  color: #2196f3;
}

.call-button {
  --padding-start: 8px;
  --padding-end: 8px;
}

/* Tracking Stats */
.tracking-stats {
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 12px;
}

.stat {
  display: flex;
  align-items: center;
  gap: 12px;
}

.stat ion-icon {
  font-size: 28px;
  color: #4caf50;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
}

.stat-label {
  font-size: 12px;
  color: #999;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: #e0e0e0;
}

/* Order Section */
.order-section {
  margin-bottom: 24px;
}

.detail-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 12px;
}

.detail-item ion-icon {
  font-size: 20px;
  color: #4caf50;
  margin-top: 2px;
  flex-shrink: 0;
}

.detail-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.detail-label {
  font-size: 12px;
  color: #999;
}

.detail-value {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

/* Timeline Section */
.timeline-section {
  margin-bottom: 24px;
}

.timeline {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.timeline-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  position: relative;
}

.timeline-item:not(:last-child)::after {
  content: '';
  position: absolute;
  left: 19px;
  top: 40px;
  width: 2px;
  height: calc(100% + 0px);
  background: #e0e0e0;
}

.timeline-item.completed::after {
  background: #4caf50;
}

.timeline-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  z-index: 2;
}

.timeline-icon ion-icon {
  font-size: 20px;
  color: #999;
}

.timeline-item.completed .timeline-icon {
  background: #e8f5e9;
}

.timeline-item.completed .timeline-icon ion-icon {
  color: #4caf50;
}

.timeline-item.active .timeline-icon {
  background: #e3f2fd;
  animation: pulse 2s infinite;
}

.timeline-item.active .timeline-icon ion-icon {
  color: #2196f3;
}

@keyframes pulse {
  0%,
  100% {
    box-shadow: 0 0 0 0 rgba(33, 150, 243, 0.4);
  }
  50% {
    box-shadow: 0 0 0 10px rgba(33, 150, 243, 0);
  }
}

.timeline-content {
  flex: 1;
  padding-top: 8px;
}

.timeline-label {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 2px 0;
}

.timeline-time {
  font-size: 12px;
  color: #999;
  margin: 0;
}

/* Action Section */
.action-section {
  margin-top: 16px;
}

/* Responsive */
@media (max-width: 480px) {
  .bottom-sheet.expanded {
    height: 80vh;
  }
}
</style>
