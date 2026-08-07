<template>
  <ion-app>
    <SplashScreen />
    <ion-router-outlet />

    <!-- Inactivity Modal -->
    <div class="inactivity-overlay" v-if="showInactivityModal" @click.prevent>
      <div class="inactivity-modal">
        <h3 class="inactivity-title">Session Expired</h3>
        <p class="inactivity-desc">
          You have been inactive for {{ inactivityMinutes }} minutes. You will be logged out for security.
        </p>
        <button class="inactivity-ok-btn" @click="handleInactivityOk">OK</button>
      </div>
    </div>
  </ion-app>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { useRouter } from 'vue-router';
import SplashScreen from './components/SplashScreen.vue';

const router = useRouter();

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

const DEFAULT_INACTIVITY_TIMEOUT = 10 * 60 * 1000;
const inactivityEnabled = ref(true);
const inactivityTimeoutMs = ref(DEFAULT_INACTIVITY_TIMEOUT);
const inactivityMinutes = ref(10);
const showInactivityModal = ref(false);
let inactivityTimer = null;

function clearInactivityTimer() {
  if (inactivityTimer) {
    clearTimeout(inactivityTimer);
    inactivityTimer = null;
  }
}

function resetInactivityTimer() {
  clearInactivityTimer();
  if (!inactivityEnabled.value) return;
  if (!showInactivityModal.value) {
    inactivityTimer = setTimeout(() => {
      if (!inactivityEnabled.value) return;
      showInactivityModal.value = true;
    }, inactivityTimeoutMs.value);
  }
}

function onUserActivity() {
  if (showInactivityModal.value) return;
  resetInactivityTimer();
}

function handleInactivityOk() {
  showInactivityModal.value = false;
  localStorage.removeItem('isAuthenticated');
  localStorage.removeItem('token');
  localStorage.removeItem('pendingAuth');
  router.push('/pin-login');
}

watch(() => localStorage.getItem('isAuthenticated') === 'true', (loggedIn) => {
  if (loggedIn) {
    showInactivityModal.value = false;
    resetInactivityTimer();
  } else {
    clearInactivityTimer();
    showInactivityModal.value = false;
  }
});

onMounted(async () => {
  document.addEventListener('mousedown', onUserActivity);
  document.addEventListener('touchstart', onUserActivity);
  document.addEventListener('keydown', onUserActivity);
  document.addEventListener('scroll', onUserActivity);
  document.addEventListener('click', onUserActivity);

  try {
    const res = await fetch(`${API_BASE}/api/settings`, { headers: { Accept: 'application/json' } });
    const data = await res.json();
    inactivityEnabled.value = data.inactivity_enabled !== 'false';
    const minutes = parseInt(data.inactivity_timeout_minutes, 10);
    if (!Number.isNaN(minutes) && minutes > 0) {
      inactivityMinutes.value = minutes;
      inactivityTimeoutMs.value = minutes * 60 * 1000;
    }
  } catch {
    inactivityEnabled.value = true;
  }

  if (localStorage.getItem('isAuthenticated') === 'true') {
    resetInactivityTimer();
  }
});

onUnmounted(() => {
  clearInactivityTimer();
  document.removeEventListener('mousedown', onUserActivity);
  document.removeEventListener('touchstart', onUserActivity);
  document.removeEventListener('keydown', onUserActivity);
  document.removeEventListener('scroll', onUserActivity);
  document.removeEventListener('click', onUserActivity);
});
</script>

<style>
/* Global Alert/Modal Styles - Same as Customer & Seller apps */
:root {
  --color-primary: #4CAF50;
  --color-bg: #f8f9fa;
  --color-text: #1a1a1a;
}

/* Base Alert Wrapper */
:deep(.alert-wrapper) {
  border-radius: 20px;
  max-width: 320px;
  background: white;
  box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

:deep(.alert-head) {
  padding: 20px 20px 0;
  text-align: center;
}

:deep(.alert-title) {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text);
}

:deep(.alert-subtitle) {
  display: none;
}

:deep(.alert-message) {
  padding: 12px 20px 20px;
  text-align: center;
  color: #666;
  font-size: 14px;
  line-height: 1.5;
}

/* Button Styles */
:deep(.alert-button-group) {
  padding: 0 16px 16px;
  display: flex;
  gap: 8px;
}

:deep(.alert-button) {
  flex: 1;
  border-radius: 12px !important;
  font-weight: 600;
  text-transform: none;
  height: 44px;
  font-size: 15px;
  margin: 0;
}

:deep(.alert-button.button-destructive) {
  background: #f44336;
  color: white;
}

/* Default button - green background */
:deep(.alert-button) {
  background: var(--color-primary);
  color: white;
}

/* Cancel button - gray */
:deep(.alert-button[role="cancel"]) {
  background: #f5f5f5;
  color: #666;
}

/* Loading Modal */
:deep(.loading-modal .alert-wrapper) {
  text-align: center;
  padding: 50px 30px 30px;
  background: white;
}

:deep(.loading-modal .alert-head) {
  display: none;
}

:deep(.loading-modal .alert-message) {
  padding-top: 60px;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  position: relative;
}

:deep(.loading-modal .alert-message::before) {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-25px);
  width: 50px;
  height: 50px;
  border: 5px solid #e0e0e0;
  border-top: 5px solid var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

:deep(.loading-modal .alert-button-group) {
  display: none;
}

/* Success Modal */
:deep(.success-modal .alert-wrapper) {
  text-align: center;
  padding: 40px 20px 20px;
  background: white;
}

:deep(.success-modal .alert-head) {
  display: none;
}

:deep(.success-modal .alert-message) {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.success-modal .alert-button-group) {
  padding: 16px 20px 20px;
}

:deep(.success-modal .alert-button) {
  background: var(--color-primary);
  color: white;
}

/* Dark Mode */
@media (prefers-color-scheme: dark) {
  :deep(.alert-wrapper) {
    background: #1a1a1a;
  }
  
  :deep(.alert-title),
  :deep(.success-modal .alert-message) {
    color: white;
  }
  
  :deep(.alert-message) {
    color: #aaa;
  }
  
  :deep(.alert-button[role="cancel"]) {
    background: #333;
    color: #aaa;
  }
  
  :deep(.loading-modal .alert-wrapper),
  :deep(.success-modal .alert-wrapper) {
    background: #1a1a1a;
  }

  .inactivity-modal {
    background: #1a1a1a;
  }

  .inactivity-title {
    color: white;
  }
}

/* Inactivity Modal */
.inactivity-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.inactivity-modal {
  background: white;
  border-radius: 20px;
  padding: 32px 24px;
  width: 85%;
  max-width: 320px;
  text-align: center;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  animation: popIn 0.3s ease-out;
}

@keyframes popIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.inactivity-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px;
}

.inactivity-desc {
  font-size: 14px;
  color: #6b7280;
  line-height: 1.5;
  margin: 0 0 24px;
}

.inactivity-ok-btn {
  width: 100%;
  padding: 14px;
  background: #4caf50;
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
}

.inactivity-ok-btn:active {
  background: #43a047;
}
</style>