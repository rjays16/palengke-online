<template>
  <ion-page>
    <ion-content :fullscreen="true" class="delivery-content">

      <!-- Map Container -->
      <div id="delivery-map" class="map-container"></div>

      <!-- Top Overlay - Back + Order Info -->
      <div class="top-overlay">
        <div class="back-btn" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>
        <div class="top-info-card">
          <div class="eta-info">
            <ion-icon :icon="timeOutline"></ion-icon>
            <span class="eta-time">{{ etaTime }}</span>
            <span class="eta-distance">• {{ etaDistance }}</span>
          </div>
        </div>
        <div class="more-btn" @click="showMoreActions">
          <ion-icon :icon="ellipsisVerticalOutline"></ion-icon>
        </div>
      </div>

      <!-- Bottom Sheet -->
      <div class="bottom-sheet" :class="{ expanded: sheetExpanded }">

        <!-- Handle -->
        <div class="sheet-handle" @click="sheetExpanded = !sheetExpanded">
          <div class="handle-bar"></div>
        </div>

        <!-- Delivery Steps Progress -->
        <div class="delivery-progress">
          <div class="progress-step" :class="{ active: currentStep >= 1, done: currentStep > 1 }">
            <div class="step-circle">
              <ion-icon :icon="currentStep > 1 ? checkmarkOutline : storefrontOutline"></ion-icon>
            </div>
            <span>Pickup</span>
          </div>
          <div class="progress-line" :class="{ done: currentStep > 1 }"></div>
          <div class="progress-step" :class="{ active: currentStep >= 2, done: currentStep > 2 }">
            <div class="step-circle">
              <ion-icon :icon="currentStep > 2 ? checkmarkOutline : bicycleOutline"></ion-icon>
            </div>
            <span>On the Way</span>
          </div>
          <div class="progress-line" :class="{ done: currentStep > 2 }"></div>
          <div class="progress-step" :class="{ active: currentStep >= 3 }">
            <div class="step-circle">
              <ion-icon :icon="homeOutline"></ion-icon>
            </div>
            <span>Delivered</span>
          </div>
        </div>

        <!-- Current Step Info -->
        <div class="step-info-card">

          <!-- Step 1: Pickup -->
          <div v-if="currentStep === 1" class="step-detail">
            <div class="step-badge pickup">
              <ion-icon :icon="storefrontOutline"></ion-icon>
              <span>Pickup Location</span>
            </div>
            <h3 class="location-name">{{ orderData.sellerName }}</h3>
            <p class="location-address">{{ orderData.pickupAddress }}</p>
            <div class="order-summary">
              <div class="summary-item">
                <ion-icon :icon="cubeOutline"></ion-icon>
                <span>{{ orderData.itemCount }} items to pickup</span>
              </div>
              <div class="summary-item">
                <ion-icon :icon="cashOutline"></ion-icon>
                <span>₱{{ orderData.totalAmount.toLocaleString() }} {{ orderData.paymentMethod === 'cash' ? '(Cash)' : '(GCash)' }}</span>
              </div>
            </div>
          </div>

          <!-- Step 2: Delivering -->
          <div v-if="currentStep === 2" class="step-detail">
            <div class="step-badge delivering">
              <ion-icon :icon="bicycleOutline"></ion-icon>
              <span>Delivering</span>
            </div>
            <h3 class="location-name">{{ orderData.customerName }}</h3>
            <p class="location-address">{{ orderData.deliveryAddress }}</p>
            <div class="order-summary">
              <div class="summary-item">
                <ion-icon :icon="timeOutline"></ion-icon>
                <span>ETA: {{ etaTime }}</span>
              </div>
              <div class="summary-item">
                <ion-icon :icon="cashOutline"></ion-icon>
                <span>Collect ₱{{ orderData.totalAmount.toLocaleString() }} on arrival</span>
              </div>
            </div>
          </div>

          <!-- Step 3: Arrived -->
          <div v-if="currentStep === 3" class="step-detail">
            <div class="step-badge arrived">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Arrived!</span>
            </div>
            <h3 class="location-name">You have arrived!</h3>
            <p class="location-address">{{ orderData.deliveryAddress }}</p>
            <div class="order-summary">
              <div class="summary-item highlight">
                <ion-icon :icon="cashOutline"></ion-icon>
                <span>Collect ₱{{ orderData.totalAmount.toLocaleString() }} from {{ orderData.customerName }}</span>
              </div>
            </div>
          </div>

        </div>

        <!-- Contact Buttons -->
        <div class="contact-buttons">
          <button class="contact-btn" @click="chatCustomer">
            <ion-icon :icon="chatbubbleOutline"></ion-icon>
            <span>{{ currentStep === 1 ? 'Chat Seller' : 'Chat Buyer' }}</span>
          </button>
          <button class="contact-btn" @click="callCustomer">
            <ion-icon :icon="callOutline"></ion-icon>
            <span>{{ currentStep === 1 ? 'Call Seller' : 'Call Buyer' }}</span>
          </button>
          <button class="contact-btn" @click="openHelpCenter">
            <ion-icon :icon="helpCircleOutline"></ion-icon>
            <span>Help</span>
          </button>
          <button class="contact-btn" @click="showMoreActions">
            <ion-icon :icon="ellipsisHorizontalOutline"></ion-icon>
            <span>More</span>
          </button>
        </div>

        <!-- Main Action Button -->
        <div class="main-action">
          <!-- Step 1: Picked Up -->
          <ion-button
            v-if="currentStep === 1"
            expand="block"
            class="action-btn pickup-btn"
            @click="confirmPickup"
          >
            <ion-icon slot="start" :icon="checkmarkCircleOutline"></ion-icon>
            I've Picked Up the Order
          </ion-button>

          <!-- Step 2: Arrived at Destination -->
          <ion-button
            v-if="currentStep === 2"
            expand="block"
            class="action-btn arrived-btn"
            @click="confirmArrived"
          >
            <ion-icon slot="start" :icon="locationOutline"></ion-icon>
            Arrived at Destination
          </ion-button>

          <!-- Step 3: Confirm Delivery -->
          <div v-if="currentStep === 3" class="confirm-delivery">
            <ion-button
              expand="block"
              class="action-btn complete-btn"
              @click="confirmDelivery"
            >
              <ion-icon slot="start" :icon="checkmarkCircleOutline"></ion-icon>
              Confirm Delivery Complete
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
import {
  IonPage, IonContent, IonButton, IonIcon,
  alertController, actionSheetController, toastController
} from '@ionic/vue';
import {
  arrowBackOutline, timeOutline, ellipsisVerticalOutline,
  storefrontOutline, bicycleOutline, homeOutline,
  checkmarkOutline, checkmarkCircleOutline, cubeOutline,
  cashOutline, chatbubbleOutline, callOutline,
  helpCircleOutline, ellipsisHorizontalOutline, locationOutline
} from 'ionicons/icons';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const router = useRouter();

let map = null;
let driverMarker = null;
let sellerMarker = null;
let customerMarker = null;
let routeLine = null;

// Delivery state
const currentStep = ref(1); // 1=pickup, 2=delivering, 3=arrived
const sheetExpanded = ref(false);
const etaTime = ref('12 min');
const etaDistance = ref('2.3 km');

// Order Data
const orderData = ref({
  id: '1234',
  sellerName: "Juan's Fresh Market",
  pickupAddress: 'Stall #15, CDO City Palengke, Cagayan de Oro',
  customerName: 'Maria Santos',
  deliveryAddress: 'Brgy. Carmen, Cagayan de Oro City',
  itemCount: 5,
  totalAmount: 850,
  paymentMethod: 'cash',
  driverEarnings: 85
});

// Locations
const sellerLocation = [8.4820, 124.6472];
const customerLocation = [8.4920, 124.6600];
const driverLocation = ref([8.4850, 124.6510]);

// Init Map
const initMap = () => {
  map = L.map('delivery-map', { zoomControl: false }).setView(driverLocation.value, 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors', maxZoom: 19
  }).addTo(map);

  // Seller marker (green)
  const sellerIcon = L.divIcon({
    className: 'custom-marker',
    html: `<div style="background:#4CAF50;color:white;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;border:3px solid white;box-shadow:0 3px 10px rgba(0,0,0,0.3);">🏪</div>`,
    iconSize: [44, 44], iconAnchor: [22, 22]
  });

  // Customer marker (red)
  const customerIcon = L.divIcon({
    className: 'custom-marker',
    html: `<div style="background:#f44336;color:white;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;border:3px solid white;box-shadow:0 3px 10px rgba(0,0,0,0.3);">🏠</div>`,
    iconSize: [44, 44], iconAnchor: [22, 22]
  });

  // Driver marker (blue)
  const driverIcon = L.divIcon({
    className: 'custom-marker',
    html: `<div style="background:#2196F3;color:white;width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;border:3px solid white;box-shadow:0 3px 10px rgba(0,0,0,0.3);">🏍️</div>`,
    iconSize: [48, 48], iconAnchor: [24, 24]
  });

  sellerMarker = L.marker(sellerLocation, { icon: sellerIcon })
    .addTo(map).bindPopup(`<b>${orderData.value.sellerName}</b><br>Pickup here`);

  customerMarker = L.marker(customerLocation, { icon: customerIcon })
    .addTo(map).bindPopup(`<b>${orderData.value.customerName}</b><br>Deliver here`);

  driverMarker = L.marker(driverLocation.value, { icon: driverIcon })
    .addTo(map).bindPopup('<b>You are here</b>');

  drawRoute();
  fitMap();
};

const drawRoute = () => {
  if (routeLine) map.removeLayer(routeLine);
  const dest = currentStep.value === 1 ? sellerLocation : customerLocation;
  routeLine = L.polyline([driverLocation.value, dest], {
    color: currentStep.value === 1 ? '#4CAF50' : '#2196F3',
    weight: 5, opacity: 0.8, dashArray: '10, 5'
  }).addTo(map);
};

const fitMap = () => {
  const dest = currentStep.value === 1 ? sellerLocation : customerLocation;
  const bounds = L.latLngBounds([driverLocation.value, dest]);
  map.fitBounds(bounds, { padding: [60, 60] });
};

// Actions
const confirmPickup = async () => {
  const alert = await alertController.create({
    header: 'Confirm Pickup',
    message: `Have you picked up all ${orderData.value.itemCount} items from ${orderData.value.sellerName}?`,
    buttons: [
      { text: 'Not Yet', role: 'cancel' },
      {
        text: 'Yes, Picked Up!',
        handler: async () => {
          currentStep.value = 2;
          drawRoute();
          fitMap();
          etaTime.value = '8 min';
          etaDistance.value = '1.5 km';
          const toast = await toastController.create({
            message: '🚀 Order picked up! Now deliver to customer.',
            duration: 2500, color: 'success', position: 'top'
          });
          await toast.present();
        }
      }
    ]
  });
  await alert.present();
};

const confirmArrived = async () => {
  currentStep.value = 3;
  etaTime.value = 'Arrived';
  etaDistance.value = '0 m';
  if (driverMarker) driverMarker.setLatLng(customerLocation);
  const toast = await toastController.create({
    message: '📍 You have arrived! Confirm delivery with customer.',
    duration: 2500, color: 'primary', position: 'top'
  });
  await toast.present();
};

const confirmDelivery = async () => {
  const alert = await alertController.create({
    header: 'Complete Delivery',
    message: `Confirm that you have delivered the order to ${orderData.value.customerName} and collected the payment?`,
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Complete!',
        handler: async () => {
          // Show success and navigate back
          const toast = await toastController.create({
            message: `✅ Delivery complete! You earned ₱${orderData.value.driverEarnings}`,
            duration: 3000, color: 'success', position: 'top'
          });
          await toast.present();
          setTimeout(() => router.push('/home'), 1000);
        }
      }
    ]
  });
  await alert.present();
};

const chatCustomer = () => router.push('/messages');

const callCustomer = () => {
  window.location.href = 'tel:+639123456789';
};

const openHelpCenter = () => router.push('/help');

const showMoreActions = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'More Actions',
    buttons: [
      {
        text: 'Cancel Order',
        role: 'destructive',
        icon: 'close-circle-outline',
        handler: () => showCancelReasons()
      },
      {
        text: 'Open in Google Maps',
        handler: () => {
          const dest = currentStep.value === 1 ? sellerLocation : customerLocation;
          window.open(`https://www.google.com/maps/dir/?api=1&destination=${dest[0]},${dest[1]}`, '_blank');
        }
      },
      { text: 'Close', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const showCancelReasons = async () => {
  const alert = await alertController.create({
    header: 'Why are you cancelling?',
    buttons: [
      {
        text: 'Accepted by mistake',
        handler: () => handleCancel('Accepted by mistake')
      },
      {
        text: "Can't reach pickup in time",
        handler: () => handleCancel("Can't reach pickup in time")
      },
      {
        text: 'Vehicle breakdown',
        handler: () => handleCancel('Vehicle breakdown')
      },
      { text: 'Close', role: 'cancel' }
    ]
  });
  await alert.present();
};

const handleCancel = async (reason) => {
  const toast = await toastController.create({
    message: `Order cancelled: ${reason}`,
    duration: 2500, color: 'danger', position: 'top'
  });
  await toast.present();
  router.push('/home');
};

const goBack = () => router.push('/home');

onMounted(() => {
  setTimeout(() => initMap(), 100);
});

onUnmounted(() => {
  if (map) { map.remove(); map = null; }
});
</script>

<style scoped>
.delivery-content { --background: #f0f0f0; }

/* Map */
.map-container {
  position: absolute; top: 0; left: 0;
  width: 100%; height: 100%; z-index: 0;
}

/* Top Overlay */
.top-overlay {
  position: absolute; top: 16px; left: 16px; right: 16px;
  display: flex; align-items: center; gap: 12px;
  z-index: 1000;
}

.back-btn, .more-btn {
  width: 44px; height: 44px;
  background: white; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 3px 12px rgba(0,0,0,0.15);
  cursor: pointer; flex-shrink: 0;
}
.back-btn ion-icon, .more-btn ion-icon { font-size: 22px; color: #1a1a1a; }

.top-info-card {
  flex: 1; background: white; border-radius: 20px;
  padding: 10px 16px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.15);
}

.eta-info { display: flex; align-items: center; gap: 6px; }
.eta-info ion-icon { font-size: 18px; color: #4CAF50; }
.eta-time { font-size: 16px; font-weight: 800; color: #1a1a1a; }
.eta-distance { font-size: 14px; color: #666; font-weight: 500; }

/* Bottom Sheet */
.bottom-sheet {
  position: absolute; left: 0; right: 0; bottom: 0;
  background: white; border-radius: 24px 24px 0 0;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
  z-index: 1000; padding: 0 0 20px 0;
  transition: transform 0.3s ease;
}

/* Handle */
.sheet-handle {
  padding: 12px 0 8px;
  display: flex; justify-content: center; cursor: pointer;
}
.handle-bar {
  width: 40px; height: 4px;
  background: #ddd; border-radius: 2px;
}

/* Delivery Progress */
.delivery-progress {
  display: flex; align-items: center;
  padding: 0 24px 16px;
  border-bottom: 1px solid #f0f0f0;
  margin-bottom: 16px;
}

.progress-step {
  display: flex; flex-direction: column;
  align-items: center; gap: 6px;
  opacity: 0.4; transition: opacity 0.3s;
}

.progress-step.active, .progress-step.done { opacity: 1; }

.step-circle {
  width: 40px; height: 40px;
  border-radius: 50%; background: #f0f0f0;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; transition: all 0.3s;
}

.progress-step.active .step-circle {
  background: #4CAF50; color: white;
  box-shadow: 0 0 0 4px rgba(76,175,80,0.2);
  animation: pulse-circle 2s infinite;
}

.progress-step.done .step-circle {
  background: #4CAF50; color: white;
}

@keyframes pulse-circle {
  0%, 100% { box-shadow: 0 0 0 4px rgba(76,175,80,0.2); }
  50% { box-shadow: 0 0 0 8px rgba(76,175,80,0.1); }
}

.progress-step span { font-size: 11px; font-weight: 700; color: #333; }

.progress-line {
  flex: 1; height: 3px; background: #e0e0e0;
  margin: 0 4px 16px; border-radius: 2px;
  transition: background 0.3s;
}
.progress-line.done { background: #4CAF50; }

/* Step Info Card */
.step-info-card { padding: 0 20px 16px; }

.step-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 20px;
  font-size: 13px; font-weight: 700;
  margin-bottom: 10px;
}

.step-badge.pickup { background: #E8F5E9; color: #4CAF50; }
.step-badge.delivering { background: #E3F2FD; color: #2196F3; }
.step-badge.arrived { background: #E8F5E9; color: #4CAF50; }

.location-name { font-size: 18px; font-weight: 800; color: #1a1a1a; margin: 0 0 4px 0; }
.location-address { font-size: 14px; color: #666; margin: 0 0 12px 0; line-height: 1.4; }

.order-summary { display: flex; flex-direction: column; gap: 8px; }

.summary-item {
  display: flex; align-items: center; gap: 8px;
  font-size: 14px; color: #555;
}
.summary-item ion-icon { font-size: 18px; color: #4CAF50; }

.summary-item.highlight {
  background: #FFF3E0; border-radius: 10px;
  padding: 10px 12px; font-weight: 600; color: #E65100;
}
.summary-item.highlight ion-icon { color: #FF9800; }

/* Contact Buttons */
.contact-buttons {
  display: flex; justify-content: space-around;
  padding: 0 20px 16px;
  border-bottom: 1px solid #f0f0f0;
  margin-bottom: 16px;
}

.contact-btn {
  display: flex; flex-direction: column;
  align-items: center; gap: 6px;
  padding: 10px 12px;
  background: #f8f9fa; border: none;
  border-radius: 14px; cursor: pointer;
  font-family: inherit;
  transition: all 0.2s; min-width: 64px;
}

.contact-btn:active { transform: scale(0.95); background: #e0e0e0; }
.contact-btn ion-icon { font-size: 24px; color: #4CAF50; }
.contact-btn span { font-size: 11px; font-weight: 600; color: #333; }

/* Main Action */
.main-action { padding: 0 20px; }

.action-btn {
  height: 56px; font-size: 16px;
  font-weight: 700; text-transform: none;
  --border-radius: 16px;
}

.pickup-btn { --background: #4CAF50; --box-shadow: 0 4px 16px rgba(76,175,80,0.3); }
.arrived-btn { --background: #2196F3; --box-shadow: 0 4px 16px rgba(33,150,243,0.3); }
.complete-btn { --background: #4CAF50; --box-shadow: 0 4px 16px rgba(76,175,80,0.3); }
</style>