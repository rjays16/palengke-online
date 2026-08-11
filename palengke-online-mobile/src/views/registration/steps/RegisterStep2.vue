<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">

        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <div class="progress-section">
          <div class="progress-bar"><div class="progress-fill" style="width: 28.56%"></div></div>
          <p class="progress-text">Step 2 of 7</p>
        </div>

        <div class="title-section">
          <h1 class="title">Tell us about yourself</h1>
          <p class="subtitle">Make sure all details match your valid ID</p>
        </div>

        <div class="form-section">

          <div class="section-header">
            <ion-icon :icon="personOutline"></ion-icon>
            <h2>Personal Information</h2>
          </div>

          <!-- First Name -->
          <div class="input-group">
            <label class="input-label">First Name *</label>
            <div class="input-wrapper" :class="{ error: errors.firstName }">
              <input v-model="formData.firstName" type="text" placeholder="Juan"
                class="custom-input" @blur="validateField('firstName')" @input="errors.firstName = ''">
            </div>
            <span v-if="errors.firstName" class="error-message">{{ errors.firstName }}</span>
          </div>

          <!-- Middle Name -->
          <div class="input-group">
            <label class="input-label">
              Middle Name <span class="optional">(Optional)</span>
            </label>
            <div class="no-middle-row">
              <div class="input-wrapper" :class="{ disabled: noMiddleName }" style="flex:1">
                <input v-model="formData.middleName" type="text" placeholder="Santos"
                  class="custom-input" :disabled="noMiddleName">
              </div>
            </div>
            <div class="checkbox-inline">
              <input type="checkbox" id="noMiddle" v-model="noMiddleName" class="checkbox-input"
                @change="onNoMiddleNameChange">
              <label for="noMiddle">I do not have a middle name</label>
            </div>
          </div>

          <!-- Last Name -->
          <div class="input-group">
            <label class="input-label">Last Name *</label>
            <div class="input-wrapper" :class="{ error: errors.lastName }">
              <input v-model="formData.lastName" type="text" placeholder="Dela Cruz"
                class="custom-input" @blur="validateField('lastName')" @input="errors.lastName = ''">
            </div>
            <span v-if="errors.lastName" class="error-message">{{ errors.lastName }}</span>
          </div>

          <!-- Birthdate -->
          <div class="input-group">
            <label class="input-label">Date of Birth *</label>
            <div class="input-wrapper" :class="{ error: errors.birthdate }">
              <ion-icon :icon="calendarOutline" class="input-icon"></ion-icon>
              <input v-model="formData.birthdate" type="date" class="custom-input with-icon"
                :max="maxDate" @blur="validateField('birthdate')" @input="errors.birthdate = ''">
            </div>
            <span v-if="errors.birthdate" class="error-message">{{ errors.birthdate }}</span>
            <span v-else class="helper-text">You must be at least 18 years old</span>
          </div>

          <div class="info-box">
            <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
            <div>
              <strong>Your data is safe</strong><br>
              All personal information is encrypted and used only for account verification.
            </div>
          </div>

          <div class="button-group">
            <ion-button expand="block" fill="outline" color="medium" class="back-btn" @click="goBack">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>Back
            </ion-button>
            <ion-button expand="block" class="next-button" :disabled="!isFormValid" @click="handleNext">
              Next<ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </div>

        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import {
  arrowBackOutline, arrowForwardOutline, personOutline,
  calendarOutline, shieldCheckmarkOutline
} from 'ionicons/icons';
import { useRegistrationStore } from '@/composables/useRegistrationStore';

const router = useRouter();

const formData = ref({ firstName: '', middleName: '', lastName: '', birthdate: '' });
const noMiddleName = ref(false);
const errors = ref({ firstName: '', lastName: '', birthdate: '' });

const maxDate = computed(() => {
  const d = new Date();
  d.setFullYear(d.getFullYear() - 18);
  return d.toISOString().split('T')[0];
});

const isFormValid = computed(() =>
  formData.value.firstName.trim() &&
  formData.value.lastName.trim() &&
  formData.value.birthdate
);

const onNoMiddleNameChange = () => {
  if (noMiddleName.value) formData.value.middleName = '';
};

const validateField = (field) => {
  if (field === 'firstName') {
    if (!formData.value.firstName.trim()) { errors.value.firstName = 'First name is required'; return false; }
    if (formData.value.firstName.trim().length < 2) { errors.value.firstName = 'Too short'; return false; }
    errors.value.firstName = ''; return true;
  }
  if (field === 'lastName') {
    if (!formData.value.lastName.trim()) { errors.value.lastName = 'Last name is required'; return false; }
    errors.value.lastName = ''; return true;
  }
  if (field === 'birthdate') {
    if (!formData.value.birthdate) { errors.value.birthdate = 'Date of birth is required'; return false; }
    const birth = new Date(formData.value.birthdate);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    if (age < 18) { errors.value.birthdate = 'You must be at least 18 years old'; return false; }
    errors.value.birthdate = ''; return true;
  }
  return true;
};

const goBack = () => router.push('/register/step1');

const handleNext = () => {
  const valid = ['firstName', 'lastName', 'birthdate'].every(f => validateField(f));
  if (!valid) return;
  const { setPersonalInfo } = useRegistrationStore();
  setPersonalInfo({
    firstName: formData.value.firstName,
    middleName: noMiddleName.value ? '' : formData.value.middleName,
    lastName: formData.value.lastName,
    noMiddleName: noMiddleName.value,
    birthdate: formData.value.birthdate
  });
  router.push('/register/step3');
};
</script>

<style scoped>
.step-content { --background: #ffffff; }
.step-container { min-height: 100vh; padding: 24px; padding-bottom: 100px; position: relative; }

.back-button {
  position: absolute; top: 24px; left: 24px; width: 40px; height: 40px;
  display: flex; align-items: center; justify-content: center;
  background: #f5f5f5; border-radius: 12px; cursor: pointer; z-index: 10;
}
.back-button:active { transform: scale(0.95); }
.back-button ion-icon { font-size: 24px; color: #1a1a1a; }

.progress-section { margin-top: 70px; margin-bottom: 32px; }
.progress-bar { width: 100%; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; margin-bottom: 8px; }
.progress-fill { height: 100%; background: #4CAF50; }
.progress-text { text-align: center; font-size: 13px; color: #666; margin: 0; font-weight: 600; }

.title-section { text-align: center; margin-bottom: 32px; }
.title { font-size: 28px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.subtitle { font-size: 15px; color: #666; margin: 0; }

.form-section { max-width: 480px; margin: 0 auto; width: 100%; }

.section-header {
  display: flex; align-items: center; gap: 10px;
  margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0;
}
.section-header ion-icon { font-size: 22px; color: #4CAF50; }
.section-header h2 { font-size: 17px; font-weight: 700; color: #1a1a1a; margin: 0; }

.input-group { margin-bottom: 20px; }
.input-label { display: block; font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px; }
.optional { font-weight: 400; color: #999; font-size: 13px; }

.input-wrapper { position: relative; display: flex; align-items: center; }
.input-wrapper.disabled { opacity: 0.5; }

.input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #4CAF50; z-index: 2; }
.success-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 22px; color: #4CAF50; z-index: 2; }

.custom-input {
  width: 100%; padding: 14px 16px; border: 2px solid #e0e0e0;
  border-radius: 12px; font-size: 15px; background: white;
  transition: border-color 0.2s; font-family: inherit;
}
.custom-input.with-icon { padding-left: 46px; }
.custom-input:focus { outline: none; border-color: #4CAF50; }
.custom-input::placeholder { color: #999; }
.custom-input:disabled { background: #f5f5f5; color: #999; cursor: not-allowed; }

.input-wrapper.error .custom-input { border-color: #f44336; }
.input-wrapper.success .custom-input { border-color: #4CAF50; }

.no-middle-row { display: flex; gap: 12px; }

.checkbox-inline {
  display: flex; align-items: center; gap: 8px;
  margin-top: 10px; font-size: 13px; color: #555;
}
.checkbox-input { width: 18px; height: 18px; accent-color: #4CAF50; cursor: pointer; flex-shrink: 0; }
.checkbox-inline label { cursor: pointer; }

.helper-text { display: block; font-size: 12px; color: #999; margin-top: 6px; }
.error-message { display: block; font-size: 12px; color: #f44336; margin-top: 6px; font-weight: 500; }

.info-box {
  display: flex; gap: 12px; background: #E8F5E9; border-left: 4px solid #4CAF50;
  padding: 16px; border-radius: 8px; margin: 28px 0 24px 0;
  font-size: 13px; color: #2E7D32; line-height: 1.5;
}
.info-box ion-icon { font-size: 22px; color: #4CAF50; flex-shrink: 0; margin-top: 2px; }
.info-box strong { font-weight: 700; }

.button-group { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px; }
.back-btn { --border-radius: 12px; --border-width: 2px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none; }
.next-button { --background: #4CAF50; --background-hover: #45a049; --border-radius: 12px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none; }
.next-button:disabled { --background: #e0e0e0; --color: #999; }

@media (max-width: 480px) {
  .title { font-size: 24px; }
  .step-container { padding: 20px 16px 100px 16px; }
  .button-group { grid-template-columns: 1fr; }
  .back-btn { order: 2; }
  .next-button { order: 1; }
}
</style>