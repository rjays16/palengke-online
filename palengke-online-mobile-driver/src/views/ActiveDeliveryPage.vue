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

<!-- No Active Order -->
      <div v-if="!orderData" class="no-order-overlay">
        <div class="no-order-card">
          <ion-icon :icon="bicycleOutline"></ion-icon>
          <h3>No active delivery</h3>
          <p>There is no active order assigned to you right now.</p>
          <ion-button expand="block" class="no-order-btn" @click="router.replace('/home')">
            Back to Home
          </ion-button>
        </div>
      </div>

<!-- Bottom Sheet - scrollable -->
      <div v-if="orderData" class="bottom-sheet" :class="{ expanded: sheetExpanded }">
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
            <p v-if="orderData.pickupInstructions" class="instructions">
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
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonContent, IonButton, IonIcon,
  alertController, actionSheetController, toastController
} from '@ionic/vue';
import {
  arrowBackOutline, timeOutline,
  bicycleOutline, homeOutline,
  checkmarkCircleOutline, cubeOutline,
  cashOutline, callOutline,
  locationOutline, locateOutline, storefrontOutline,
  documentTextOutline
} from 'ionicons/icons';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const router = useRouter();
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

function getToken() {
  return localStorage.getItem('token') || '';
}

let map = null;
let driverMarker = null;
let customerMarker = null;
let vendorMarker = null;
let pickupRouteLine = null;
let deliveryRouteLine = null;
let watchId = null;

// Delivery state
const currentStep = ref(1); // 1=pickup, 2=delivering, 3=arrived
const sheetExpanded = ref(false);
const etaTime = ref('--');
const etaDistance = ref('');
const orderData = ref(null);
const gpsActive = ref(false);

// Real coordinates (populated from the API / device GPS; null when unavailable)
const driverPos = ref(null);
const vendorPos = ref(null);
const customerPos = ref(null);

const paymentLabel = (method) => {
  if (method === 'cod') return 'Cash on Delivery';
  if (method === 'gcash') return 'GCash';
  if (method === 'cash') return 'Cash';
  return method || 'Cash';
};

// Current destination marker based on delivery step
const destination = computed(() => {
  if (currentStep.value === 1 && vendorPos.value) return vendorPos.value;
  if (currentStep.value >= 2 && customerPos.value) return customerPos.value;
  return null;
});

// Fetch the driver's active order and populate the page with real data
const fetchActiveOrder = async () => {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/orders`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (!res.ok) return;
    const data = await res.json();
    const active = (data.active || [])[0];
    if (!active) return;

    orderData.value = {
      id: active.id,
      sellerName: active.sellerName,
      pickupAddress: active.pickupAddress,
      pickupPhone: active.pickupPhone || '',
      customerName: active.customerName,
      deliveryAddress: active.deliveryAddress,
      customerPhone: active.customerPhone || '',
      itemCount: active.itemCount || 0,
      totalAmount: active.totalAmount || 0,
      paymentMethod: paymentLabel(active.paymentMethod),
      driverEarnings: active.driverEarnings || 0,
      pickupInstructions: active.pickupInstructions || '',
      items: Array.isArray(active.items) && active.items.length
        ? active.items.map((i) => `${i.qty ?? ''}${i.unit ?? ''} ${i.name ?? ''}`.trim())
        : [],
    };

    if (active.dbStatus === 'processing') {
      currentStep.value = 2;
    }

    if (active.sellerLatitude && active.sellerLongitude) {
      vendorPos.value = [Number(active.sellerLatitude), Number(active.sellerLongitude)];
    }
    if (active.latitude && active.longitude) {
      customerPos.value = [Number(active.latitude), Number(active.longitude)];
    }
  } catch {
    // Leave the page in a "no active order" state
  }
};

// Haversine distance (km)
const haversine = (a, b) => {
  const R = 6371;
  const toRad = (v) => (v * Math.PI) / 180;
  const dLat = toRad(b[0] - a[0]);
  const dLng = toRad(b[1] - a[1]);
  const s = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(a[0])) * Math.cos(toRad(b[0])) * Math.sin(dLng / 2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(s), Math.sqrt(1 - s));
};

// Real ETA based on current GPS position
const updateEta = () => {
  if (!driverPos.value || !destination.value) {
    etaTime.value = '--';
    etaDistance.value = '';
    return;
  }
  const km = haversine(driverPos.value, destination.value);
  etaDistance.value = km < 1 ? `${Math.round(km * 1000)} m` : `${km.toFixed(1)} km`;
  const mins = Math.round((km / 30) * 60);
  etaTime.value = mins < 1 ? '< 1 min' : `${mins} min`;
};

// Watch the driver's real device position
const startWatchingPosition = () => {
  if (!navigator.geolocation) return;
  watchId = navigator.geolocation.watchPosition(
    (pos) => {
      driverPos.value = [pos.coords.latitude, pos.coords.longitude];
      gpsActive.value = true;
      if (driverMarker) driverMarker.setLatLng(driverPos.value);
      updateEta();
      if (map && !userIsDragging) map.panTo(driverPos.value);
    },
    () => {
      gpsActive.value = false;
    },
    { enableHighAccuracy: true, maximumAge: 2000, timeout: 15000 },
  );
};

const stopWatchingPosition = () => {
  if (watchId !== null) {
    navigator.geolocation.clearWatch(watchId);
    watchId = null;
  }
};

// Init Map with the coordinates we actually have
const initMap = () => {
  const firstPoint = driverPos.value || vendorPos.value || customerPos.value;
  if (!firstPoint) return;

  map = L.map('delivery-map', {
    zoomControl: false,
    dragging: true,
    tap: true,
    bounceZoom: false,
  }).setView(firstPoint, 15);

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

  if (vendorPos.value) {
    vendorMarker = L.marker(vendorPos.value, { icon: vendorIcon })
      .addTo(map).bindPopup(`
        <div style="text-align:center;min-width:160px;">
          <strong style="color:#4CAF50;font-size:14px;">🏪 PICKUP HERE</strong><br>
          <span style="color:#666;font-size:12px;">${orderData.value?.sellerName || ''}</span><br>
          <span style="color:#888;font-size:11px;">${orderData.value?.pickupAddress || ''}</span><br>
          <hr style="margin:6px 0;border:none;border-top:1px solid #eee;">
          <span style="color:#4CAF50;font-size:11px;">📞 ${orderData.value?.pickupPhone || ''}</span>
        </div>
      `);
  }

  if (customerPos.value) {
    customerMarker = L.marker(customerPos.value, { icon: customerIcon })
      .addTo(map).bindPopup(`
        <div style="text-align:center;min-width:160px;">
          <strong style="color:#f44336;font-size:14px;">🏠 DELIVER HERE</strong><br>
          <span style="color:#666;font-size:12px;">${orderData.value?.customerName || ''}</span><br>
          <span style="color:#888;font-size:11px;">${orderData.value?.deliveryAddress || ''}</span><br>
          <hr style="margin:6px 0;border:none;border-top:1px solid #eee;">
          <span style="color:#f44336;font-size:11px;">📞 ${orderData.value?.customerPhone || ''}</span>
        </div>
      `);
  }

  if (driverPos.value) {
    driverMarker = L.marker(driverPos.value, { icon: driverIcon })
      .addTo(map).bindPopup('<b>🏍️ Driver</b>');
  }

  drawPickupRoute();
  fitMapToShowAll();
};

// Draw route from driver to vendor (pickup phase)
const drawPickupRoute = () => {
  if (!map) return;
  if (pickupRouteLine) map.removeLayer(pickupRouteLine);
  if (deliveryRouteLine) map.removeLayer(deliveryRouteLine);

  if (currentStep.value === 1) {
    if (driverPos.value && vendorPos.value) {
      pickupRouteLine = L.polyline([driverPos.value, vendorPos.value], {
        color: '#4CAF50',
        weight: 5,
        opacity: 0.9,
        dashArray: '12, 8',
        lineCap: 'round'
      }).addTo(map);
    }

    if (vendorPos.value && customerPos.value) {
      deliveryRouteLine = L.polyline([vendorPos.value, customerPos.value], {
        color: '#ccc',
        weight: 3,
        opacity: 0.6,
        dashArray: '8, 8',
        lineCap: 'round'
      }).addTo(map);
    }
  }
};

// Track when user is dragging the map
let userIsDragging = false;
const onMapDragStart = () => { userIsDragging = true; };
const onMapDragEnd = () => {
  // Resume following the driver once the user lets go
  userIsDragging = false;
};

// Draw route from driver to customer (delivery phase)
const drawDeliveryRoute = () => {
  if (!map) return;
  if (pickupRouteLine) map.removeLayer(pickupRouteLine);
  if (deliveryRouteLine) map.removeLayer(deliveryRouteLine);

  if (driverPos.value && customerPos.value) {
    pickupRouteLine = L.polyline([driverPos.value, customerPos.value], {
      color: '#2196F3',
      weight: 5,
      opacity: 0.9,
      dashArray: '12, 8',
      lineCap: 'round'
    }).addTo(map);
  }
};

// Fit map to show all available locations
const fitMapToShowAll = () => {
  if (!map) return;
  const allPoints = [driverPos.value, vendorPos.value, customerPos.value].filter(Boolean);
  if (!allPoints.length) return;
  const bounds = L.latLngBounds(allPoints);
  map.fitBounds(bounds, { padding: [80, 80] });
};

// Update map when step changes
const updateMapForStep = () => {
  if (!map) return;
  if (currentStep.value === 1) {
    const points = [driverPos.value, vendorPos.value].filter(Boolean);
    if (points.length) map.fitBounds(L.latLngBounds(points), { padding: [80, 80] });
    drawPickupRoute();
  } else if (currentStep.value >= 2) {
    const points = [vendorPos.value, customerPos.value].filter(Boolean);
    if (points.length) map.fitBounds(L.latLngBounds(points), { padding: [80, 80] });
    drawDeliveryRoute();
  }
  updateEta();
};

// Send a real status update to the backend
const updateOrderStatus = async (status) => {
  const token = getToken();
  if (!token || !orderData.value) return false;
  try {
    const res = await fetch(`${API_BASE}/api/driver/orders/${orderData.value.id}/status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ status }),
    });
    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      throw new Error(err.message || 'Could not update order.');
    }
    return true;
  } catch (e) {
    const toast = await toastController.create({
      message: e.message || 'Could not update the order. Please try again.',
      duration: 3000, color: 'danger', position: 'top'
    });
    await toast.present();
    return false;
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
          const ok = await updateOrderStatus('picked_up');
          if (!ok) return;
          currentStep.value = 2;
          updateMapForStep();
          const toast = await toastController.create({
            message: 'Order picked up! Now deliver to customer.',
            duration: 2500, color: 'medium', position: 'top'
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
  const toast = await toastController.create({
    message: 'You have arrived! Confirm delivery with customer.',
    duration: 2500, color: 'medium', position: 'top'
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
          const ok = await updateOrderStatus('delivered');
          if (!ok) return;
          stopWatchingPosition();
          await showSuccessModal(`Delivery complete!\nYou earned ₱${orderData.value.driverEarnings}`);
          router.replace('/home');
        }
      }
    ]
  });
  await alert.present();
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

const openNavigation = () => {
  const name = currentStep.value === 1 ? orderData.value.sellerName : orderData.value.customerName;
  const address = currentStep.value === 1 ? orderData.value.pickupAddress : orderData.value.deliveryAddress;
  const url = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(address)}`;
  window.open(url, '_blank');
};

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
  const ok = await updateOrderStatus('cancelled');
  if (!ok) return;
  stopWatchingPosition();
  const toast = await toastController.create({
    message: `Order cancelled: ${reason}`,
    duration: 2500, color: 'danger', position: 'top'
  });
  await toast.present();
  router.replace('/home');
};

const goBack = async () => {
  const alert = await alertController.create({
    header: 'Go Back?',
    message: 'You can come back to track this delivery anytime from your orders.',
    buttons: [
      { text: 'Stay', role: 'cancel' },
      {
        text: 'Go Back',
        handler: () => router.replace('/home')
      }
    ]
  });
  await alert.present();
};

const centerOnDriver = () => {
  if (map && driverPos.value) {
    map.panTo(driverPos.value);
    map.setZoom(16);
  }
};

onMounted(async () => {
  await fetchActiveOrder();
  startWatchingPosition();
  setTimeout(() => initMap(), 100);

  if (orderData.value && (!vendorPos.value || !customerPos.value)) {
    const toast = await toastController.create({
      message: 'Some location data is missing for this order.',
      duration: 3000, color: 'warning', position: 'top'
    });
    await toast.present();
  }
});

onUnmounted(() => {
  stopWatchingPosition();
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

/* No Active Order */
.no-order-overlay {
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  z-index: 1500;
  display: flex; align-items: center; justify-content: center;
  padding: 24px;
}

.no-order-card {
  background: white; border-radius: 20px;
  padding: 32px 24px; text-align: center;
  box-shadow: 0 4px 24px rgba(0,0,0,0.15);
  width: 100%; max-width: 320px;
}

.no-order-card ion-icon {
  font-size: 56px; color: #999; margin-bottom: 12px;
}

.no-order-card h3 {
  font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0;
}

.no-order-card p {
  font-size: 14px; color: #666; margin: 0 0 20px 0; line-height: 1.5;
}

.no-order-btn {
  --background: #4CAF50; --border-radius: 12px;
  font-size: 15px; font-weight: 600; text-transform: none;
}

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