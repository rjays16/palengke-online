<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Step 1 of 2</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="skipToEnd" fill="clear" color="light">Skip</ion-button>
        </ion-buttons>
      </ion-toolbar>
      <ion-toolbar color="light">
        <ion-progress-bar :value="0.5"></ion-progress-bar>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="step-container">
        <h1 class="step-title">Personal Information</h1>
        <p class="step-subtitle">Tell us about yourself</p>

        <div class="form-section">
          <div class="input-group">
            <ion-icon :icon="personOutline" class="input-icon"></ion-icon>
            <input 
              v-model="formData.fullName" 
              type="text" 
              placeholder="Full name"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
            <input 
              v-model="formData.email" 
              type="email" 
              placeholder="Email address"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <ion-icon :icon="callOutline" class="input-icon"></ion-icon>
            <input 
              v-model="formData.phone" 
              type="tel" 
              placeholder="Phone number"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <ion-icon :icon="locationOutline" class="input-icon"></ion-icon>
            <input 
              v-model="formData.address" 
              type="text" 
              placeholder="Home address"
              class="custom-input"
            >
          </div>
        </div>

        <div class="action-section">
          <ion-button 
            expand="block" 
            class="continue-button"
            @click="proceedToStep2"
            :disabled="!isFormValid"
          >
            Continue
          </ion-button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { 
  IonPage, 
  IonHeader, 
  IonToolbar, 
  IonTitle, 
  IonContent, 
  IonButton,
  IonButtons,
  IonIcon,
  IonProgressBar
} from '@ionic/vue';
import { 
  arrowBackOutline,
  personOutline,
  mailOutline,
  callOutline,
  locationOutline
} from 'ionicons/icons';

const router = useRouter();

const formData = ref({
  fullName: '',
  email: '',
  phone: '',
  address: ''
});

const isFormValid = computed(() => {
  return formData.value.fullName && 
         formData.value.email && 
         formData.value.phone;
});

const goBack = () => {
  router.push('/signup');
};

const skipToEnd = () => {
  router.push('/register/success');
};

const proceedToStep2 = () => {
  router.push('/register/step2');
};
</script>

<style scoped>
.step-container {
  max-width: 500px;
  margin: 0 auto;
  padding-top: 20px;
}

.step-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.step-subtitle {
  font-size: 15px;
  color: #666;
  margin: 0 0 32px 0;
}

.form-section {
  margin-bottom: 32px;
}

.input-group {
  position: relative;
  margin-bottom: 16px;
}

.input-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #4CAF50;
  z-index: 2;
}

.custom-input {
  width: 100%;
  padding: 16px 16px 16px 48px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 16px;
  background: white;
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input:focus {
  outline: none;
  border-color: #4CAF50;
}

.custom-input::placeholder {
  color: #999;
}

.continue-button {
  --background: #4CAF50;
  --background-hover: #45a049;
  --border-radius: 12px;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
}
</style>
