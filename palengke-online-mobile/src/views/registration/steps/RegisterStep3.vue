<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">

        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <div class="progress-section">
          <div class="progress-bar"><div class="progress-fill" style="width: 42.84%"></div></div>
          <p class="progress-text">Step 3 of 7</p>
        </div>

        <div class="title-section">
          <h1 class="title">Delivery Address</h1>
          <p class="subtitle">Where should we deliver your orders?</p>
        </div>

        <div class="form-section">

          <div class="section-header">
            <ion-icon :icon="locationOutline"></ion-icon>
            <h2>Your Address</h2>
          </div>

          <!-- Region -->
          <div class="input-group">
            <label class="input-label">Region *</label>
            <div class="input-wrapper" :class="{ error: errors.region }">
              <ion-icon :icon="mapOutline" class="input-icon"></ion-icon>
              <select v-model="formData.region" class="custom-select with-icon"
                @change="onRegionChange" @blur="validateField('region')">
                <option value="">Select region</option>
                <option value="luzon">Luzon</option>
                <option value="visayas">Visayas</option>
                <option value="mindanao">Mindanao</option>
              </select>
            </div>
            <span v-if="errors.region" class="error-message">{{ errors.region }}</span>
          </div>

          <!-- City -->
          <div class="input-group" v-if="formData.region">
            <label class="input-label">City / Municipality *</label>
            <div class="input-wrapper" :class="{ error: errors.city }">
              <ion-icon :icon="businessOutline" class="input-icon"></ion-icon>
              <select v-model="formData.city" class="custom-select with-icon"
                @change="onCityChange" @blur="validateField('city')">
                <option value="">Select city</option>
                <option v-for="city in availableCities" :key="city" :value="city">{{ city }}</option>
              </select>
            </div>
            <span v-if="errors.city" class="error-message">{{ errors.city }}</span>
          </div>

          <!-- Barangay -->
          <div class="input-group" v-if="formData.city">
            <label class="input-label">Barangay *</label>
            <div class="input-wrapper" :class="{ error: errors.barangay }">
              <ion-icon :icon="homeOutline" class="input-icon"></ion-icon>
              <select v-model="formData.barangay" class="custom-select with-icon"
                @blur="validateField('barangay')">
                <option value="">Select barangay</option>
                <option v-for="b in availableBarangays" :key="b" :value="b">{{ b }}</option>
              </select>
            </div>
            <span v-if="errors.barangay" class="error-message">{{ errors.barangay }}</span>
          </div>

          <!-- Street address -->
          <div class="input-group">
            <label class="input-label">Street Address / Unit / House No. *</label>
            <div class="input-wrapper" :class="{ error: errors.street }">
              <ion-icon :icon="homeOutline" class="input-icon-top"></ion-icon>
              <textarea v-model="formData.street" placeholder="123 Rizal St., Unit 4B"
                class="custom-textarea with-icon" rows="2"
                @blur="validateField('street')" @input="errors.street = ''">
              </textarea>
            </div>
            <span v-if="errors.street" class="error-message">{{ errors.street }}</span>
          </div>

          <!-- Landmark -->
          <div class="input-group">
            <label class="input-label">Landmark <span class="optional">(Optional)</span></label>
            <div class="input-wrapper">
              <ion-icon :icon="flagOutline" class="input-icon"></ion-icon>
              <input v-model="formData.landmark" type="text"
                placeholder="Near Jollibee, beside the church…"
                class="custom-input with-icon">
            </div>
            <span class="helper-text">Helps riders find your location faster</span>
          </div>

          <!-- Address label -->
          <div class="input-group">
            <label class="input-label">Address Label</label>
            <div class="label-options">
              <button
                v-for="lbl in ['Home', 'Work', 'Other']" :key="lbl"
                class="label-btn"
                :class="{ active: formData.addressLabel === lbl }"
                @click="formData.addressLabel = lbl"
              >
                <ion-icon :icon="lbl === 'Home' ? homeOutline : lbl === 'Work' ? briefcaseOutline : locationOutline"></ion-icon>
                {{ lbl }}
              </button>
            </div>
          </div>

          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div>You can add more delivery addresses later in your profile settings.</div>
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
  arrowBackOutline, arrowForwardOutline, locationOutline, mapOutline,
  businessOutline, homeOutline, flagOutline, briefcaseOutline, informationCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const formData = ref({
  region: '', city: '', barangay: '', street: '', landmark: '', addressLabel: 'Home'
});
const errors = ref({ region: '', city: '', barangay: '', street: '' });

const citiesByRegion = {
  luzon: ['Manila','Quezon City','Caloocan','Makati','Pasig','Taguig','Antipolo','Baguio','Cabanatuan','Lipa','Lucena','Olongapo','San Fernando (Pampanga)','Tarlac City'],
  visayas: ['Cebu City','Mandaue','Lapu-Lapu','Bacolod','Iloilo City','Tacloban','Dumaguete','Tagbilaran'],
  mindanao: ['Davao City','Cagayan de Oro','General Santos','Iligan','Butuan','Zamboanga City','Cotabato City','Koronadal','Kidapawan','Surigao City']
};

const barangaysByCity = {
  'Cagayan de Oro': ['Agusan','Balulang','Bulua','Camaman-an','Carmen','Consolacion','Cugman','Gusa','Lapasan','Macasandig','Nazareth','Puerto','Puntod','Tablon'],
  'Davao City': ['Agdao','Bago Gallera','Bunawan','Buhangin','Catalunan Grande','Panacan','Sasa','Talomo','Toril','Tugbok'],
  'Manila': ['Binondo','Ermita','Intramuros','Malate','Paco','Pandacan','Port Area','Sampaloc','Santa Ana','Santa Cruz','Tondo'],
  'Quezon City': ['Batasan Hills','Commonwealth','Cubao','Diliman','Fairview','Holy Spirit','Payatas','Tandang Sora','UP Campus'],
  'Cebu City': ['Apas','Banilad','Capitol Site','Guadalupe','Lahug','Mabolo','Pardo','Talamban','Tisa'],
  'default': ['Barangay 1 (Poblacion)','Barangay 2 (Poblacion)','Barangay 3 (Poblacion)','Barangay 4 (Poblacion)','Barangay 5 (Poblacion)','Others']
};

const availableCities = computed(() => citiesByRegion[formData.value.region] || []);
const availableBarangays = computed(() => barangaysByCity[formData.value.city] || barangaysByCity['default']);

const isFormValid = computed(() =>
  formData.value.region && formData.value.city && formData.value.barangay && formData.value.street.trim()
);

const onRegionChange = () => { formData.value.city = ''; formData.value.barangay = ''; errors.value.region = ''; };
const onCityChange = () => { formData.value.barangay = ''; errors.value.city = ''; };

const validateField = (field) => {
  if (!formData.value[field]) {
    errors.value[field] = `Please select a ${field}`;
    if (field === 'street') errors.value.street = 'Street address is required';
    return false;
  }
  errors.value[field] = '';
  return true;
};

const goBack = () => router.push('/register/step2');

const handleNext = () => {
  if (!isFormValid.value) return;
  const existing = JSON.parse(localStorage.getItem('buyerRegistrationData') || '{}');
  Object.assign(existing, { ...formData.value });
  localStorage.setItem('buyerRegistrationData', JSON.stringify(existing));
  router.push('/register/step4');
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

.section-header { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0; }
.section-header ion-icon { font-size: 22px; color: #4CAF50; }
.section-header h2 { font-size: 17px; font-weight: 700; color: #1a1a1a; margin: 0; }

.input-group { margin-bottom: 20px; }
.input-label { display: block; font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px; }
.optional { font-weight: 400; color: #999; font-size: 13px; }

.input-wrapper { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #4CAF50; z-index: 2; }
.input-icon-top { position: absolute; left: 14px; top: 16px; font-size: 20px; color: #4CAF50; z-index: 2; }

.custom-input, .custom-select {
  width: 100%; padding: 14px 16px; border: 2px solid #e0e0e0;
  border-radius: 12px; font-size: 15px; background: white;
  transition: border-color 0.2s; font-family: inherit;
}
.custom-input.with-icon, .custom-select.with-icon { padding-left: 46px; }
.custom-input:focus, .custom-select:focus { outline: none; border-color: #4CAF50; }
.custom-input::placeholder { color: #999; }

.custom-select {
  cursor: pointer; appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 16px center; padding-right: 40px;
}

.custom-textarea {
  width: 100%; padding: 14px 16px; border: 2px solid #e0e0e0;
  border-radius: 12px; font-size: 15px; background: white;
  font-family: inherit; resize: none; min-height: 72px;
}
.custom-textarea.with-icon { padding-left: 46px; }
.custom-textarea:focus { outline: none; border-color: #4CAF50; }
.custom-textarea::placeholder { color: #999; }

.input-wrapper.error .custom-input,
.input-wrapper.error .custom-select,
.input-wrapper.error .custom-textarea { border-color: #f44336; }

.helper-text { display: block; font-size: 12px; color: #999; margin-top: 6px; }
.error-message { display: block; font-size: 12px; color: #f44336; margin-top: 6px; font-weight: 500; }

/* Address label buttons */
.label-options { display: flex; gap: 10px; flex-wrap: wrap; }
.label-btn {
  display: flex; align-items: center; gap: 6px; padding: 10px 16px;
  border: 2px solid #e0e0e0; border-radius: 10px; background: white;
  font-size: 14px; font-weight: 600; color: #555; cursor: pointer;
  font-family: inherit; transition: all 0.2s;
}
.label-btn ion-icon { font-size: 16px; }
.label-btn.active { border-color: #4CAF50; background: #f0faf2; color: #2E7D32; }
.label-btn:hover { border-color: #4CAF50; }

.info-box {
  display: flex; gap: 12px; background: #E3F2FD; border-left: 4px solid #2196F3;
  padding: 16px; border-radius: 8px; margin: 24px 0;
  font-size: 13px; color: #1565C0; line-height: 1.5;
}
.info-box ion-icon { font-size: 22px; color: #2196F3; flex-shrink: 0; margin-top: 2px; }

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