<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/profile"></ion-back-button>
        </ion-buttons>
        <ion-title>Devices</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div class="devices-container">
        <div class="header-section">
          <div class="header-icon">
            <ion-icon :icon="phonePortraitOutline"></ion-icon>
          </div>
          <h2>Logged-in Devices</h2>
          <p>Devices that have access to your account</p>
        </div>

        <div v-if="loading" class="loading-section">
          <ion-spinner name="crescent"></ion-spinner>
          <p>Loading devices...</p>
        </div>

        <div v-else-if="devices.length === 0" class="empty-section">
          <ion-icon :icon="phonePortraitOutline"></ion-icon>
          <p>No devices found</p>
        </div>

        <div v-else class="devices-list">
          <div
            v-for="device in devices"
            :key="device.id"
            class="device-card"
          >
            <div class="device-icon">
              <ion-icon :icon="device.is_current ? phonePortraitOutline : laptopOutline"></ion-icon>
            </div>
            <div class="device-info">
              <p class="device-name">
                {{ device.device_name || 'Unknown Device' }}
                <span v-if="device.is_current" class="current-badge">Current</span>
              </p>
              <p class="device-meta">
                <span>Last used {{ device.last_used_at || 'never' }}</span>
              </p>
              <p class="device-meta">Added {{ device.created_at }}</p>
            </div>
            <ion-button
              color="danger"
              fill="clear"
              class="logout-btn"
              @click="confirmLogout(device)"
            >
              <ion-icon :icon="logOutOutline"></ion-icon>
            </ion-button>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { API_BASE } from '@/config/api';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonButton,
  IonIcon,
  IonSpinner,
  alertController,
} from '@ionic/vue';
import {
  phonePortraitOutline,
  laptopOutline,
  logOutOutline,
} from 'ionicons/icons';

const router = useRouter();

const devices = ref([]);
const loading = ref(true);

const userEmail = ref(localStorage.getItem('userEmail') || '');
const currentDeviceId = ref(localStorage.getItem('deviceId') || '');

const fetchDevices = async () => {
  loading.value = true;
  try {
    const response = await fetch(`${API_BASE}/seller/devices`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: userEmail.value,
        current_device_id: currentDeviceId.value,
      }),
    });
    const data = await response.json();
    if (response.ok) {
      devices.value = data.devices || [];
    }
  } catch {
    // silent
  } finally {
    loading.value = false;
  }
};

const confirmLogout = async (device) => {
  const isCurrent = device.is_current;
  const msg = isCurrent
    ? 'Logout from this device? You will need to enter your email and password to sign in again.'
    : `Logout "${device.device_name || 'Unknown Device'}"? It will be signed out and removed from your account.`;

  const alert = await alertController.create({
    header: isCurrent ? 'Logout Device' : 'Remove Device',
    message: msg,
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Logout',
        role: 'destructive',
        handler: async () => {
          if (isCurrent) {
            await logoutCurrent();
          } else {
            await removeDevice(device);
          }
        },
      },
    ],
  });
  await alert.present();
};

const logoutCurrent = async () => {
  try {
    await fetch(`${API_BASE}/seller/devices/logout-current`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: userEmail.value }),
    });
  } catch {
    // silent
  }

  ['isAuthenticated', 'token', 'pendingAuth'].forEach(k => localStorage.removeItem(k));
  router.replace('/login');
};

const removeDevice = async (device) => {
  try {
    const response = await fetch(`${API_BASE}/seller/devices/remove`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: userEmail.value,
        device_id: device.device_id,
      }),
    });

    if (response.ok) {
      devices.value = devices.value.filter(d => d.id !== device.id);
      const toast = await alertController.create({
        header: 'Removed',
        message: 'Device logged out successfully.',
        buttons: ['OK'],
      });
      await toast.present();
    }
  } catch {
    // silent
  }
};

onMounted(() => {
  fetchDevices();
});
</script>

<style scoped>
.devices-container {
  padding: 24px 16px;
  min-height: 100%;
  background: #f8f9fa;
}

.header-section {
  text-align: center;
  margin-bottom: 32px;
}

.header-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 16px;
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(76, 175, 80, 0.3);
}

.header-icon ion-icon {
  font-size: 40px;
  color: white;
}

.header-section h2 {
  font-size: 22px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.header-section p {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.loading-section {
  text-align: center;
  padding: 60px 0;
  color: #999;
}

.loading-section p {
  margin-top: 12px;
  font-size: 14px;
}

.empty-section {
  text-align: center;
  padding: 60px 0;
  color: #ccc;
}

.empty-section ion-icon {
  font-size: 64px;
  margin-bottom: 16px;
}

.empty-section p {
  font-size: 16px;
  color: #999;
}

.devices-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.device-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: white;
  padding: 16px;
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.device-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #E8F5E9;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.device-icon ion-icon {
  font-size: 24px;
  color: #4CAF50;
}

.device-info {
  flex: 1;
  min-width: 0;
}

.device-name {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 4px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.current-badge {
  font-size: 11px;
  font-weight: 600;
  background: #E8F5E9;
  color: #4CAF50;
  padding: 2px 8px;
  border-radius: 10px;
}

.device-meta {
  font-size: 13px;
  color: #999;
  margin: 2px 0;
}

.logout-btn {
  --padding-start: 8px;
  --padding-end: 8px;
  height: 40px;
}

.logout-btn ion-icon {
  font-size: 20px;
}
</style>
