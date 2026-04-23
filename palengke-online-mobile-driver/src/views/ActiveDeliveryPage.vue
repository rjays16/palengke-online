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
            <span class="eta-label">{{ etaTime }}</span>
            <span class="eta-dist">• {{ etaDistance }}</span>
          </div>
        </div>
        <div class="center-btn" @click="centerOnDriver">
          <ion-icon :icon="locateOutline"></ion-icon>
        </div>
      </div>

<!-- Bottom Sheet - scrollable -->
      <div class="bottom-sheet" :class="{ expanded: sheetExpanded }">
        <div class="sheet-handle" @click="sheetExpanded = !sheetExpanded">
          <div class="handle-bar"></div>
        </div>
        
        <!-- Delivery Progress -->
        <div class="delivery-progress">
          <div class="progress-step" :class="{ active: currentStep >= 1, done: currentStep > 1 }">
            <div class="step-icon">
              <ion-icon :icon="currentStep > 1 ? checkmarkCircleOutline : storefrontOutline"></ion-icon>
            </div>
            <span>Pickup</span>
          </div>
          <div class="progress-line" :class="{ done: currentStep > 1 }"></div>
          <div class="progress-step" :class="{ active: currentStep >= 2, done: currentStep > 2 }">
            <div class="step-icon">
              <ion-icon :icon="currentStep > 2 ? checkmarkCircleOutline : bicycleOutline"></ion-icon>
            </div>
            <span>On the Way</span>
          </div>
          <div class="progress-line" :class="{ done: currentStep > 2 }"></div>
          <div class="progress-step" :class="{ active: currentStep >= 3 }">
            <div class="step-icon">
              <ion-icon :icon="homeOutline"></ion-icon>
            </div>
            <span>Delivered</span>
          </div>
        </div>
        
        <!-- Scrollable Content -->
        <div class="sheet-content">
          <!-- Step 1: Pickup -->
          <div v-if="currentStep === 1" class="step-detail">
            <div class="step-badge pickup">
              <ion-icon :icon="storefrontOutline"></ion-icon>
              Pickup from Vendor
            </div>
            <h3>{{ orderData.sellerName }}</h3>
            <p>{{ orderData.pickupAddress }}</p>
            <p class="phone">
              <ion-icon :icon="callOutline"></ion-icon>
              {{ orderData.pickupPhone }}
            </p>
            <p class="instructions">
              <ion-icon :icon="documentTextOutline"></ion-icon>
              {{ orderData.pickupInstructions }}
            </p>
            <div class="items-row">
              <span>
                <ion-icon :icon="cubeOutline"></ion-icon>
                Items:
              </span>
              <div class="item-tags">
                <span v-for="item in orderData.items" :key="item" class="item-tag">{{ item }}</span>
              </div>
            </div>
            <p class="collect">
              <ion-icon :icon="cashOutline"></ion-icon>
              Collect: ₱{{ orderData.totalAmount }} ({{ orderData.paymentMethod }})
            </p>
          </div>
          
          <!-- Step 2: Delivering -->
          <div v-if="currentStep === 2" class="step-detail">
            <div class="step-badge delivering">
              <ion-icon :icon="bicycleOutline"></ion-icon>
              Delivering to Customer
            </div>
            <h3>{{ orderData.customerName }}</h3>
            <p>{{ orderData.deliveryAddress }}</p>
            <p class="phone">
              <ion-icon :icon="callOutline"></ion-icon>
              {{ orderData.customerPhone }}
            </p>
            <p class="eta">
              <ion-icon :icon="timeOutline"></ion-icon>
              ETA: {{ etaTime }} ({{ etaDistance }})
            </p>
            <p class="collect">
              <ion-icon :icon="cashOutline"></ion-icon>
              Collect: ₱{{ orderData.totalAmount }} on arrival
            </p>
          </div>
          
          <!-- Step 3: Arrived -->
          <div v-if="currentStep === 3" class="step-detail">
            <div class="step-badge arrived">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              You have arrived!
            </div>
            <p>{{ orderData.deliveryAddress }}</p>
            <p class="collect">
              <ion-icon :icon="cashOutline"></ion-icon>
              Collect ₱{{ orderData.totalAmount }} from {{ orderData.customerName }}
            </p>
          </div>
        </div>
        
        <!-- Quick Action Button -->
        <div class="action-row">
          <button v-if="currentStep === 1" class="action-btn pickup-btn" @click="confirmPickup">
            <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
            I've Picked Up
          </button>
          <button v-if="currentStep === 2" class="action-btn arrived-btn" @click="confirmArrived">
            <ion-icon :icon="locationOutline"></ion-icon>
            Arrived at Destination
          </button>
          <button v-if="currentStep === 3" class="action-btn complete-btn" @click="confirmDelivery">
            <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
            Confirm Complete
          </button>
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
  arrowBackOutline, timeOutline,
  basketOutline, bicycleOutline, homeOutline,
  checkmarkOutline, checkmarkCircleOutline, cubeOutline,
  cashOutline, chatbubbleOutline, callOutline,
  locationOutline, locateOutline, storefrontOutline,
  documentTextOutline
} from 'ionicons/icons';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const router = useRouter();

let map = null;
let driverMarker = null;
let customerMarker = null;
let vendorMarker = null;
let pickupRouteLine = null;
let deliveryRouteLine = null;

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
  pickupPhone: '0912 345 6789',
  customerName: 'Maria Santos',
  deliveryAddress: 'Brgy. Carmen, Cagayan de Oro City',
  customerPhone: '0918 765 4321',
  itemCount: 5,
  totalAmount: 850,
  paymentMethod: 'cash',
  driverEarnings: 85,
  pickupInstructions: 'Ask for Juan at Stall #15',
  items: ['2kg Pork', '1kg Tomatoes', '3pcs Cabbage']
});

// Vendor/Driver/Customer Locations (mock coordinates - CDO area)
const driverStartLocation = [8.4850, 124.6510];
const vendorLocation = [8.4820, 124.6472];
const customerLocation = [8.4920, 124.6600];
const driverLocation = ref([8.4850, 124.6510]);

// Init Map with both pickup and delivery locations
const initMap = () => {
  map = L.map('delivery-map', { 
    zoomControl: false, 
    dragging: true, 
    tap: true,
    bounceZoom: false
  }).setView(driverLocation.value, 15);

  // Track drag state to prevent auto-pan while user is dragging
  map.on('movestart', onMapDragStart);
  map.on('moveend', onMapDragEnd);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap', maxZoom: 19
  }).addTo(map);

  // Custom vendor icon (green store)
  const vendorIconHtml = `
    <div style="background:#4CAF50;color:white;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;border:3px solid white;box-shadow:0 3px 12px rgba(0,0,0,0.4)">🏪</div>`;
  const vendorIcon = L.divIcon({
    className: 'vendor-marker',
    html: vendorIconHtml,
    iconSize: [44, 44], iconAnchor: [22, 22]
  });

  // Custom customer icon (red home)
  const customerIconHtml = `
    <div style="background:#f44336;color:white;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;border:3px solid white;box-shadow:0 3px 12px rgba(0,0,0,0.4)">🏠</div>`;
  const customerIcon = L.divIcon({
    className: 'customer-marker',
    html: customerIconHtml,
    iconSize: [44, 44], iconAnchor: [22, 22]
  });

  // Driver icon (blue motorcycle)
  const driverIconHtml = `
    <div style="background:#2196F3;color:white;width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;border:4px solid white;box-shadow:0 4px 16px rgba(33,150,243,0.5)">🏍️</div>`;
  const driverIcon = L.divIcon({
    className: 'driver-marker',
    html: driverIconHtml,
    iconSize: [48, 48], iconAnchor: [24, 24]
  });

  // Add all markers
  vendorMarker = L.marker(vendorLocation, { icon: vendorIcon })
    .addTo(map).bindPopup(`
      <div style="text-align:center;min-width:160px;">
        <strong style="color:#4CAF50;font-size:14px;">🏪 PICKUP HERE</strong><br>
        <span style="color:#666;font-size:12px;">${orderData.value.sellerName}</span><br>
        <span style="color:#888;font-size:11px;">${orderData.value.pickupAddress}</span><br>
        <hr style="margin:6px 0;border:none;border-top:1px solid #eee;">
        <span style="color:#4CAF50;font-size:11px;">📞 ${orderData.value.pickupPhone}</span>
      </div>
    `);

  customerMarker = L.marker(customerLocation, { icon: customerIcon })
    .addTo(map).bindPopup(`
      <div style="text-align:center;min-width:160px;">
        <strong style="color:#f44336;font-size:14px;">🏠 DELIVER HERE</strong><br>
        <span style="color:#666;font-size:12px;">${orderData.value.customerName}</span><br>
        <span style="color:#888;font-size:11px;">${orderData.value.deliveryAddress}</span><br>
        <hr style="margin:6px 0;border:none;border-top:1px solid #eee;">
        <span style="color:#f44336;font-size:11px;">📞 ${orderData.value.customerPhone}</span>
      </div>
    `);

  driverMarker = L.marker(driverStartLocation, { icon: driverIcon })
    .addTo(map).bindPopup('<b>🏍️ Driver</b>');

  // Draw route from driver to vendor
  drawPickupRoute();
  
  // Fit map to show all locations
  fitMapToShowAll();
  
  // Start driver moving to vendor for pickup
  simulateDriverMovement(driverStartLocation, vendorLocation);
};

// Draw route from driver to vendor (pickup phase)
const drawPickupRoute = () => {
  // Remove existing route
  if (pickupRouteLine) map.removeLayer(pickupRouteLine);
  if (deliveryRouteLine) map.removeLayer(deliveryRouteLine);
  
  if (currentStep.value === 1) {
    // Pickup phase: show driver → vendor route (green dashed)
    pickupRouteLine = L.polyline([driverLocation.value, vendorLocation], {
      color: '#4CAF50',
      weight: 5,
      opacity: 0.9,
      dashArray: '12, 8',
      lineCap: 'round'
    }).addTo(map);
    
    // Also show dotted line from vendor to customer (as reference)
    deliveryRouteLine = L.polyline([vendorLocation, customerLocation], {
      color: '#ccc',
      weight: 3,
      opacity: 0.6,
      dashArray: '8, 8',
      lineCap: 'round'
    }).addTo(map);
    
    // Start driver moving to vendor
    simulateDriverMovement(driverLocation.value, vendorLocation);
  }
};

// Simulate driver movement to destination
let movementInterval = null;
let currentDriverPos = null;
let userIsDragging = false;

const simulateDriverMovement = (from, to) => {
  // Clear any existing movement
  if (movementInterval) clearInterval(movementInterval);
  
  currentDriverPos = [...from];
  const endLat = to[0];
  const endLng = to[1];
  
  let progress = 0;
  const steps = 20;
  const stepLat = (endLat - from[0]) / steps;
  const stepLng = (endLng - from[1]) / steps;
  
  movementInterval = setInterval(() => {
    progress++;
    currentDriverPos[0] = from[0] + (stepLat * progress);
    currentDriverPos[1] = from[1] + (stepLng * progress);
    
// Update driver marker position
    if (driverMarker) {
      driverMarker.setLatLng(currentDriverPos);
      driverMarker.setPopupContent('<b>🏍️ On the way</b>');
    }
    
    // Stop when reached
    if (progress >= steps) {
      clearInterval(movementInterval);
      movementInterval = null;
      etaTime.value = 'Arrived';
      etaDistance.value = '0 km';
      if (driverMarker) {
        driverMarker.setPopupContent('<b>🏍️ Arrived!</b>');
      }
    }
    
    // Only pan if user is NOT manually dragging
    if (map && currentDriverPos && !userIsDragging) {
      map.panTo(currentDriverPos);
    }
    
    // Update ETA based on progress
    const remainingSteps = steps - progress;
    if (remainingSteps > 0) {
      etaTime.value = Math.ceil(remainingSteps * 0.6) + ' min';
      const distanceRemaining = (remainingSteps / steps * 2.3).toFixed(1);
      etaDistance.value = distanceRemaining + ' km';
    }
    
    // Stop when reached
    if (progress >= steps) {
      clearInterval(movementInterval);
      movementInterval = null;
      etaTime.value = 'Arrived';
      etaDistance.value = '0 km';
      if (driverMarker) {
        driverMarker.setPopupContent('<b>Arrived!</b>');
      }
    }
  }, 800);
};

// Track when user is dragging the map
const onMapDragStart = () => { userIsDragging = true; };
const onMapDragEnd = () => { 
  // Don't auto-pan after user manually drags - let them explore
  userIsDragging = true;
};

// Stop driver movement simulation
const stopDriverMovement = () => {
  if (movementInterval) {
    clearInterval(movementInterval);
    movementInterval = null;
  }
};

// Draw route from driver to customer (delivery phase)  
const drawDeliveryRoute = () => {
  if (pickupRouteLine) map.removeLayer(pickupRouteLine);
  if (deliveryRouteLine) map.removeLayer(deliveryRouteLine);
  
  // Delivery phase: show driver → customer route (blue)
  pickupRouteLine = L.polyline([driverLocation.value, customerLocation], {
    color: '#2196F3',
    weight: 5,
    opacity: 0.9,
    dashArray: '12, 8',
    lineCap: 'round'
  }).addTo(map);
};

// Fit map to show all locations
const fitMapToShowAll = () => {
  const allPoints = [driverLocation.value, vendorLocation, customerLocation];
  const bounds = L.latLngBounds(allPoints);
  map.fitBounds(bounds, { padding: [80, 80] });
};

// Update map when step changes
const updateMapForStep = () => {
  if (currentStep.value === 1) {
    // Focus on vendor for pickup
    const bounds = L.latLngBounds([driverLocation.value, vendorLocation]);
    map.fitBounds(bounds, { padding: [80, 80] });
    drawPickupRoute();
  } else if (currentStep.value >= 2) {
    // Focus on customer for delivery
    const bounds = L.latLngBounds([vendorLocation, customerLocation]);
    map.fitBounds(bounds, { padding: [80, 80] });
    drawDeliveryRoute();
    
    // Start moving from vendor to customer
    simulateDriverMovement(vendorLocation, customerLocation);
  }
};

// Actions
const confirmPickup = async () => {
  const itemsList = orderData.value.items.join(', ');
  const message = `Pickup from: ${orderData.value.sellerName}\n${orderData.value.pickupAddress}\n\nItems: ${itemsList}\n\nCollect: ₱${orderData.value.totalAmount.toLocaleString()} (${orderData.value.paymentMethod})`;
  
  const alert = await alertController.create({
    header: 'Confirm Pickup',
    message: message,
    buttons: [
      { text: 'Not Yet', role: 'cancel' },
      {
        text: 'Yes, Picked Up!',
        handler: async () => {
          currentStep.value = 2;
          updateMapForStep();
          etaTime.value = '8 min';
          etaDistance.value = '1.5 km';
          const toast = await toastController.create({
            message: 'Order picked up! Now deliver to customer.',
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
    message: 'You have arrived! Confirm delivery with customer.',
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
          // Stop driver movement
          stopDriverMovement();
          
          // Show success and navigate back
          const toast = await toastController.create({
            message: `Delivery complete! You earned ₱${orderData.value.driverEarnings}`,
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
  const phone = currentStep.value === 1 ? orderData.value.pickupPhone : orderData.value.customerPhone;
  window.location.href = `tel:${phone.replace(/\s/g, '')}`;
};

const openNavigation = () => {
  // Open Google Maps navigation to current destination
  const dest = currentStep.value === 1 ? vendorLocation : customerLocation;
  const name = currentStep.value === 1 ? orderData.value.sellerName : orderData.value.customerName;
  const address = currentStep.value === 1 ? orderData.value.pickupAddress : orderData.value.deliveryAddress;
  const url = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(address)}`;
  window.open(url, '_blank');
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

const goBack = async () => {
  const alert = await alertController.create({
    header: 'Go Back?',
    message: 'You can come back to track this delivery anytime from your orders.',
    buttons: [
      { text: 'Stay', role: 'cancel' },
      { 
        text: 'Go Back', 
        handler: () => router.push('/home')
      }
    ]
  });
  await alert.present();
};

const centerOnDriver = () => {
  if (map) {
    // Stop following temporarily when user taps center button
    userIsDragging = true;
    setTimeout(() => { userIsDragging = false; }, 3000);
    
    if (currentDriverPos) {
      map.panTo(currentDriverPos);
      map.setZoom(16);
    } else if (driverMarker) {
      const pos = driverMarker.getLatLng();
      map.panTo(pos);
      map.setZoom(16);
    }
  }
};

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

.back-btn, .center-btn {
  width: 44px; height: 44px;
  background: white; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 3px 12px rgba(0,0,0,0.15);
  cursor: pointer; flex-shrink: 0;
}
.back-btn ion-icon, .center-btn ion-icon { font-size: 22px; color: #1a1a1a; }

.top-info-card {
  flex: 1; background: white; border-radius: 20px;
  padding: 10px 16px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.15);
}

.eta-info { display: flex; align-items: center; gap: 6px; }
.eta-label { font-size: 16px; font-weight: 800; color: #1a1a1a; }
.eta-dist { font-size: 14px; color: #666; font-weight: 500; }

/* Bottom Sheet - scrollable */
.bottom-sheet {
  position: absolute; left: 0; right: 0; bottom: 0;
  background: white; border-radius: 24px 24px 0 0;
  box-shadow: 0 -4px 24px rgba(0,0,0,0.2);
  z-index: 1000;
  display: flex; flex-direction: column;
  max-height: 55%;
}

.sheet-handle {
  padding: 12px 0 10px;
  display: flex; justify-content: center;
  cursor: pointer;
  flex-shrink: 0;
}

.handle-bar {
  width: 40px; height: 4px;
  background: #ccc; border-radius: 2px;
}

/* Delivery Progress */
.delivery-progress {
  display: flex; align-items: center;
  padding: 0 20px 12px;
  flex-shrink: 0;
}

.progress-step {
  display: flex; flex-direction: column;
  align-items: center; gap: 4px;
}

.step-icon {
  width: 36px; height: 36px;
  border-radius: 50%; background: #f0f0f0;
  display: flex; align-items: center; justify-content: center;
}

.step-icon ion-icon {
  font-size: 18px;
  color: #999;
}

.progress-step.active .step-icon {
  background: #4CAF50;
  box-shadow: 0 0 0 3px rgba(76,175,80,0.2);
}

.progress-step.active .step-icon ion-icon {
  color: white;
}

.progress-step.done .step-icon {
  background: #4CAF50;
}

.progress-step.done .step-icon ion-icon {
  color: white;
}

.progress-step span {
  font-size: 10px; font-weight: 600; color: #666;
}

.progress-line {
  flex: 1; height: 3px; background: #e0e0e0;
  margin: 0 8px;
}
.progress-line.done { background: #4CAF50; }

/* Scrollable Content */
.sheet-content {
  flex: 1; overflow-y: auto;
  padding: 0 20px 12px;
}

.step-detail { }

.step-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 12px; border-radius: 16px;
  font-size: 12px; font-weight: 700;
  margin-bottom: 10px;
}
.step-badge.pickup { background: #E8F5E9; color: #2E7D32; }
.step-badge.delivering { background: #E3F2FD; color: #1565C0; }
.step-badge.arrived { background: #E8F5E9; color: #2E7D32; }

.step-detail h3 {
  font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0;
}
.step-detail p {
  font-size: 13px; color: #666; margin: 0 0 8px 0;
}
.step-detail .phone { color: #333; font-weight: 600; }
.step-detail .instructions {
  background: #FFF3E0; padding: 10px 12px; border-radius: 10px;
  color: #E65100;
}
.step-detail .eta {
  background: #E3F2FD; padding: 10px 12px; border-radius: 10px;
  color: #1565C0;
}
.step-detail .collect {
  font-size: 14px; font-weight: 700; color: #1a1a1a;
  padding: 10px 12px; background: #f5f5f5; border-radius: 10px;
}

.items-row {
  display: flex; flex-direction: column; gap: 6px;
  margin-bottom: 10px;
}
.items-row > span { font-size: 12px; color: #666; font-weight: 600; }

.item-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.item-tag {
  background: #E8F5E9; color: #2E7D32;
  padding: 4px 10px; border-radius: 12px;
  font-size: 11px; font-weight: 600;
}

/* Action Row */
.action-row {
  padding: 12px 20px 20px;
  flex-shrink: 0;
}

.action-btn {
  width: 100%; padding: 14px; border-radius: 14px;
  font-size: 14px; font-weight: 700; border: none;
  cursor: pointer; font-family: inherit;
}
.pickup-btn { background: #4CAF50; color: white; }
.arrived-btn { background: #2196F3; color: white; }
.complete-btn { background: #4CAF50; color: white; }
</style>