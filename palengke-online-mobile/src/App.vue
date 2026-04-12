<template>
  <ion-app>
    <SplashScreen />
    <ion-router-outlet />
  </ion-app>
</template>

<script>
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import SplashScreen from './components/SplashScreen.vue';

export default {
  name: 'App',
  components: {
    IonApp,
    IonRouterOutlet,
    SplashScreen
  }
};
</script>

<style>
/* Global Alert/Modal Styles - Same as Seller & Driver apps */
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
}
</style>