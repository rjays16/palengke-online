<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/settings"></ion-back-button>
        </ion-buttons>
        <ion-title>Service Type</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="service-content">
      <div class="service-container">

        <!-- Info Banner -->
        <div class="info-banner">
          <ion-icon :icon="informationCircleOutline"></ion-icon>
          <p>Enable the delivery services you want to accept. You can toggle them anytime.</p>
        </div>

        <!-- Availability Section -->
        <div class="service-section">
          <h3 class="service-group-title">Availability</h3>

          <div class="service-item" v-for="service in services" :key="service.id">
            <div class="service-left">
              <div class="service-icon" :style="{ background: service.color + '20', color: service.color }">
                <ion-icon :icon="service.icon"></ion-icon>
              </div>
              <div class="service-info">
                <div class="service-name-row">
                  <h4>{{ service.name }}</h4>
                  <span class="service-tag" v-if="service.tag" :class="service.tag_type">{{ service.tag }}</span>
                </div>
                <p v-if="service.description">{{ service.description }}</p>
                <p v-if="service.always_on" class="always-on">Always on</p>
              </div>
            </div>
            <ion-toggle
              v-model="service.enabled"
              color="success"
              :disabled="service.always_on"
              @ionChange="onServiceToggle(service)"
            ></ion-toggle>
          </div>
        </div>

        <!-- Earnings Info -->
        <div v-if="expressGloballyEnabled" class="earnings-info-card">
          <ion-icon :icon="cashOutline"></ion-icon>
          <div>
            <h4>Earn More with Express!</h4>
            <p>Express delivery earns 15% more than standard delivery.</p>
          </div>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonIcon, IonToggle,
  toastController
} from '@ionic/vue';
import {
  informationCircleOutline, bicycleOutline, flashOutline,
  cubeOutline, cashOutline
} from 'ionicons/icons';

const router = useRouter();
import { API_BASE } from '@/services/api';

function getToken() {
  return localStorage.getItem('token') || '';
}

const iconMap = {
  bicycle: bicycleOutline,
  flash: flashOutline,
  cube: cubeOutline,
};

const expressGloballyEnabled = ref(false);
const services = ref([]);

onMounted(async () => {
  try {
    const [servicesRes, driverRes] = await Promise.all([
      fetch(`${API_BASE}/api/delivery-services`),
      fetch(`${API_BASE}/api/driver/settings`, {
        headers: { Authorization: `Bearer ${getToken()}` },
      }),
    ]);
    if (servicesRes.ok) {
      const list = await servicesRes.json();
      const driverSettings = driverRes.ok ? await driverRes.json() : {};
      services.value = list
        .filter((s) => s.global_enabled)
        .map((s) => ({
          ...s,
          icon: iconMap[s.icon] || bicycleOutline,
          enabled: s.always_on ? true : Boolean(driverSettings[s.driver_setting]),
        }));
      expressGloballyEnabled.value = services.value.some((s) => s.slug === 'express');
    }
  } catch {}
});

const onServiceToggle = async (service) => {
  try {
    if (service.driver_setting) {
      await fetch(`${API_BASE}/api/driver/settings`, {
        method: 'PUT',
        headers: {
          Authorization: `Bearer ${getToken()}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ [service.driver_setting]: service.enabled }),
      });
    }
  } catch {}
  const toast = await toastController.create({
    message: `${service.name} ${service.enabled ? 'enabled' : 'disabled'}`,
    duration: 2000, color: 'medium', position: 'top'
  });
  await toast.present();
};
</script>

<style scoped>
.service-content { --background: #f8f9fa; }
.service-container { padding: 16px; padding-bottom: 40px; }

.info-banner {
  display: flex; align-items: flex-start; gap: 12px;
  background: #E3F2FD; border-radius: 14px; padding: 14px 16px;
  margin-bottom: 16px; border-left: 4px solid #2196F3;
}
.info-banner ion-icon { font-size: 22px; color: #2196F3; flex-shrink: 0; margin-top: 2px; }
.info-banner p { font-size: 13px; color: #1565C0; margin: 0; line-height: 1.5; }

.service-section { background: white; border-radius: 16px; padding: 20px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

.service-group-title { font-size: 13px; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; }

.service-item {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 0; border-bottom: 1px solid #f0f0f0;
}
.service-item:last-child { border-bottom: none; padding-bottom: 0; }

.service-left { display: flex; align-items: center; gap: 14px; flex: 1; }

.service-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }

.service-name-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 3px; }
.service-info h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0; }
.service-info p { font-size: 12px; color: #666; margin: 0; }
.always-on { color: #4CAF50 !important; font-weight: 600 !important; }

.service-tag { padding: 2px 8px; border-radius: 8px; font-size: 10px; font-weight: 700; }
.service-tag.earnings { background: #FFF3E0; color: #FF9800; }

.earnings-info-card {
  display: flex; align-items: center; gap: 14px;
  background: #E8F5E9; border-radius: 16px; padding: 16px 20px;
  border: 2px solid #4CAF50;
}
.earnings-info-card ion-icon { font-size: 32px; color: #4CAF50; flex-shrink: 0; }
.earnings-info-card h4 { font-size: 15px; font-weight: 700; color: #2E7D32; margin: 0 0 4px 0; }
.earnings-info-card p { font-size: 13px; color: #388E3C; margin: 0; line-height: 1.4; }
</style>