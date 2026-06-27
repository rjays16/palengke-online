<template>
  <ion-page>
    <ion-content :fullscreen="true" class="na-content">
      <div class="na-container">
        <div class="card">
          <div class="icon-wrap" :class="iconClass">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32">
              <circle cx="12" cy="12" r="10"/>
              <path d="M12 8v4m0 4h.01"/>
            </svg>
          </div>

          <h1 class="title">{{ title }}</h1>
          <p class="desc">{{ description }}</p>

          <div class="info-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span>Your account is registered but <strong>not yet available</strong> in your selected {{ reasonLabel }}.</span>
          </div>

          <ion-button expand="block" class="login-btn" @click="goToLogin">
            <ion-icon slot="start" :icon="logInOutline"></ion-icon>
            Back to Login
          </ion-button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import { logInOutline } from 'ionicons/icons';

const router = useRouter();
const route = useRoute();
const reason = route.params.reason || 'country';

const messages = {
  country: {
    title: 'Country Not Available',
    description: 'This app is not available in your country yet. We are working to expand to more regions soon.',
    iconClass: 'icon-country',
    label: 'country',
  },
  region: {
    title: 'Region Not Available',
    description: 'This app is not yet available in your region. We are gradually rolling out to more areas.',
    iconClass: 'icon-region',
    label: 'region',
  },
  area: {
    title: 'Area Not Available',
    description: 'This app is not yet available in your city or barangay. We are expanding to more locations soon.',
    iconClass: 'icon-area',
    label: 'area',
  },
};

const config = computed(() => messages[reason] || messages.country);
const title = computed(() => config.value.title);
const description = computed(() => config.value.description);
const iconClass = computed(() => config.value.iconClass);
const reasonLabel = computed(() => config.value.label);

const goToLogin = () => {
  localStorage.removeItem('userEmail');
  router.push('/login');
};
</script>

<style scoped>
.na-content {
  --background: #f8fafc;
}
.na-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 24px;
}
.card {
  width: 100%;
  max-width: 400px;
  background: #fff;
  border-radius: 24px;
  padding: 40px 32px;
  text-align: center;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
}
.icon-wrap {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}
.icon-country {
  background: #fee2e2;
  color: #dc2626;
}
.icon-region {
  background: #fef3c7;
  color: #d97706;
}
.icon-area {
  background: #e0e7ff;
  color: #4338ca;
}
.title {
  font-size: 22px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 12px;
}
.desc {
  font-size: 15px;
  color: #6b7280;
  line-height: 1.6;
  margin: 0 0 24px;
}
.info-box {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 12px;
  padding: 14px 16px;
  text-align: left;
  margin-bottom: 28px;
  color: #166534;
  font-size: 13px;
  line-height: 1.5;
}
.info-box svg {
  flex-shrink: 0;
  margin-top: 2px;
  color: #16a34a;
}
.info-box strong {
  color: #14532d;
}
.login-btn {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 50px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
  margin: 0;
}
</style>
