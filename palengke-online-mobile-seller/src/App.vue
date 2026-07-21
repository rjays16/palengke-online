<template>
  <ion-app>
    <SplashScreen />
    <ion-router-outlet />

    <!-- Inactivity Modal -->
    <div class="inactivity-overlay" v-if="showInactivityModal" @click.prevent>
      <div class="inactivity-modal">
        <div class="inactivity-icon">⏰</div>
        <h3 class="inactivity-title">Session Expired</h3>
        <p class="inactivity-desc">
          You have been inactive for 5 minutes. You will be logged out for security.
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
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();

const INACTIVITY_TIMEOUT = 5 * 60 * 1000;
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
  if (auth.isAuthenticated && !showInactivityModal.value) {
    inactivityTimer = setTimeout(() => {
      if (auth.isAuthenticated) {
        showInactivityModal.value = true;
      }
    }, INACTIVITY_TIMEOUT);
  }
}

function onUserActivity() {
  if (showInactivityModal.value) return;
  resetInactivityTimer();
}

function handleInactivityOk() {
  showInactivityModal.value = false;
  auth.isAuthenticated = false;
  auth.token = null;
  localStorage.removeItem('isAuthenticated');
  localStorage.removeItem('token');
  router.push('/pin-login');
}

watch(() => auth.isAuthenticated, (loggedIn) => {
  if (loggedIn) {
    showInactivityModal.value = false;
    resetInactivityTimer();
  } else {
    clearInactivityTimer();
    showInactivityModal.value = false;
  }
});

onMounted(() => {
  const events = ['mousedown', 'touchstart', 'keydown', 'scroll', 'click'];
  events.forEach((e) => document.addEventListener(e, onUserActivity, { passive: true }));
  if (auth.isAuthenticated) resetInactivityTimer();
});

onUnmounted(() => {
  clearInactivityTimer();
  const events = ['mousedown', 'touchstart', 'keydown', 'scroll', 'click'];
  events.forEach((e) => document.removeEventListener(e, onUserActivity));
});
</script>

<style>
.inactivity-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999;
  backdrop-filter: blur(4px);
}

.inactivity-modal {
  background: white;
  border-radius: 20px;
  padding: 36px 28px 28px;
  text-align: center;
  width: 300px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  animation: inactivity-pop 0.3s ease;
}

@keyframes inactivity-pop {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.inactivity-icon {
  font-size: 48px;
  margin-bottom: 16px;
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
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.inactivity-ok-btn:active {
  background: #43a047;
}
</style>
