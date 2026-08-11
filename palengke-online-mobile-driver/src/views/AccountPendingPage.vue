<template>
  <ion-page>
    <ion-content :fullscreen="true" class="pending-content">
      <div class="pending-container">
        <div class="card">
          <div class="icon-wrap icon-pending">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32">
              <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>

          <h1 class="title">Account Pending Approval</h1>

          <p class="desc">
            Your account is not yet approved. Please wait — we'll notify you via email once it's ready.
          </p>

          <div class="info-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
              <circle cx="12" cy="12" r="10" />
              <path d="M12 16v-4m0-4h.01" />
            </svg>
            <span
              >You'll receive an email at <strong>{{ email }}</strong> once approved.</span
            >
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import { logInOutline } from 'ionicons/icons';

const router = useRouter();
const email = ref('');

onMounted(() => {
  email.value = localStorage.getItem('userEmail') || '';
});

const goToLogin = () => {
  localStorage.removeItem('userEmail');
  router.push('/login');
};
</script>

<style scoped>
.pending-content {
  --background: #f8fafc;
}

.pending-container {
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
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
}

.icon-pending {
  background: #fef3c7;
  color: #d97706;
}

.title {
  font-size: 22px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 12px 0;
}

.desc {
  font-size: 15px;
  color: #64748b;
  margin: 0 0 24px 0;
  line-height: 1.6;
}

.info-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px 16px;
  margin-bottom: 28px;
  text-align: left;
  font-size: 13px;
  color: #64748b;
  line-height: 1.5;
}

.info-box svg {
  flex-shrink: 0;
  color: #94a3b8;
}

.info-box strong {
  color: #1a1a1a;
  font-weight: 600;
}

.login-btn {
  --background: #4caf50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
}

.login-btn::part(native) {
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 480px) {
  .card {
    padding: 32px 24px;
  }
  .title {
    font-size: 20px;
  }
}
</style>
