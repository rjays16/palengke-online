<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">
        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-section">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 42.84%"></div>
          </div>
          <p class="progress-text">Step 3 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Store Information</h1>
          <p class="subtitle">Tell us about your store location</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          <!-- Store Information Section -->
          <div class="section-header">
            <ion-icon :icon="storefrontOutline"></ion-icon>
            <h2>Your Store</h2>
          </div>

          <!-- Store Name -->
          <div class="input-group">
            <label class="input-label">Store Name *</label>
            <div class="input-wrapper" :class="{ error: errors.storeName }">
              <input
                v-model="formData.storeName"
                type="text"
                placeholder="Juan's Fresh Produce"
                class="custom-input"
                @blur="validateField('storeName')"
                @input="errors.storeName = ''"
              />
            </div>
            <span v-if="errors.storeName" class="error-message">{{ errors.storeName }}</span>
            <span v-else class="helper-text">This will be visible to customers</span>
          </div>

          <!-- Region Dropdown -->
          <div class="input-group">
            <label class="input-label">Region *</label>
            <div class="input-wrapper" :class="{ error: errors.region }">
              <ion-icon :icon="locationOutline" class="input-icon"></ion-icon>
              <select
                v-model="formData.region"
                class="custom-select with-icon"
                @change="onRegionChange"
                @blur="validateField('region')"
              >
                <option value="">Select Region</option>
                <option value="luzon">Luzon</option>
                <option value="visayas">Visayas</option>
                <option value="mindanao">Mindanao</option>
              </select>
            </div>
            <span v-if="errors.region" class="error-message">{{ errors.region }}</span>
          </div>

          <!-- City Dropdown (shows after region selected) -->
          <div class="input-group" v-if="formData.region">
            <label class="input-label">City *</label>
            <div class="input-wrapper" :class="{ error: errors.city }">
              <ion-icon :icon="businessOutline" class="input-icon"></ion-icon>
              <select
                v-model="formData.city"
                class="custom-select with-icon"
                @change="onCityChange"
                @blur="validateField('city')"
              >
                <option value="">Select City</option>
                <option v-for="city in availableCities" :key="city" :value="city">
                  {{ city }}
                </option>
              </select>
            </div>
            <span v-if="errors.city" class="error-message">{{ errors.city }}</span>
          </div>

          <!-- Barangay Dropdown (shows after city selected) -->
          <div class="input-group" v-if="formData.city">
            <label class="input-label">Barangay *</label>
            <div class="input-wrapper" :class="{ error: errors.barangay }">
              <ion-icon :icon="mapOutline" class="input-icon"></ion-icon>
              <select v-model="formData.barangay" class="custom-select with-icon" @blur="validateField('barangay')">
                <option value="">Select Barangay</option>
                <option v-for="barangay in availableBarangays" :key="barangay" :value="barangay">
                  {{ barangay }}
                </option>
              </select>
            </div>
            <span v-if="errors.barangay" class="error-message">{{ errors.barangay }}</span>
            <span v-else class="helper-text">{{ barangayCount }} barangays available</span>
          </div>

          <!-- Store Place/Location -->
          <div class="input-group">
            <label class="input-label">Store Location/Address *</label>
            <div class="input-wrapper" :class="{ error: errors.storePlace }">
              <ion-icon :icon="pinOutline" class="input-icon"></ion-icon>
              <input
                v-model="formData.storePlace"
                type="text"
                placeholder="Carmen Public Market, Stall 12"
                class="custom-input with-icon"
                @blur="validateField('storePlace')"
                @input="errors.storePlace = ''"
              />
            </div>
            <span v-if="errors.storePlace" class="error-message">{{ errors.storePlace }}</span>
            <span v-else class="helper-text">Market name, stall number, or street address</span>
          </div>

          <!-- Complete Address -->
          <div class="input-group">
            <label class="input-label">Complete Address *</label>
            <div class="input-wrapper" :class="{ error: errors.completeAddress }">
              <ion-icon :icon="homeOutline" class="input-icon"></ion-icon>
              <textarea
                v-model="formData.completeAddress"
                placeholder="123 Main Street, Barangay Carmen, Cagayan de Oro City, Misamis Oriental"
                class="custom-textarea with-icon"
                rows="3"
                @blur="validateField('completeAddress')"
                @input="errors.completeAddress = ''"
              ></textarea>
            </div>
            <span v-if="errors.completeAddress" class="error-message">{{ errors.completeAddress }}</span>
            <span v-else class="helper-text">Include barangay, city, and province</span>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>Why we need this:</strong><br />
              Accurate location helps customers find your store and enables efficient delivery services.
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="button-group">
            <ion-button expand="block" fill="outline" color="medium" class="back-btn" @click="goBack">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
              Back
            </ion-button>

            <ion-button expand="block" class="next-button" @click="handleNext" :disabled="!isFormValid">
              Next
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  storefrontOutline,
  locationOutline,
  businessOutline,
  mapOutline,
  pinOutline,
  homeOutline,
  informationCircleOutline,
} from 'ionicons/icons';

const router = useRouter();

// Cities by region
const citiesByRegion = {
  luzon: [
    'Manila',
    'Quezon City',
    'Caloocan',
    'Las Piñas',
    'Makati',
    'Malabon',
    'Mandaluyong',
    'Marikina',
    'Muntinlupa',
    'Navotas',
    'Parañaque',
    'Pasay',
    'Pasig',
    'Pateros',
    'San Juan',
    'Taguig',
    'Valenzuela',
    'Antipolo',
    'Cavite City',
    'Bacoor',
    'Dasmariñas',
    'Imus',
    'Tagaytay',
    'Baguio',
    'Cabanatuan',
    'Lipa',
    'Lucena',
    'Olongapo',
    'San Fernando (Pampanga)',
    'San Fernando (La Union)',
    'Tarlac City',
  ],
  visayas: [
    'Cebu City',
    'Mandaue',
    'Lapu-Lapu',
    'Talisay (Cebu)',
    'Toledo',
    'Bacolod',
    'Iloilo City',
    'Roxas City',
    'Tacloban',
    'Ormoc',
    'Calbayog',
    'Catbalogan',
    'Borongan',
    'Dumaguete',
    'Bais',
    'Tagbilaran',
    'Cadiz',
    'Silay',
    'Victorias',
    'Kabankalan',
    'Bago City',
    'Escalante',
  ],
  mindanao: [
    'Davao City',
    'Digos',
    'Mati',
    'Tagum',
    'Panabo',
    'Samal',
    'Cagayan de Oro',
    'Gingoog',
    'Malaybalay',
    'Valencia',
    'Oroquieta',
    'Ozamiz',
    'Tangub',
    'Iligan',
    'Marawi',
    'Cotabato City',
    'General Santos',
    'Koronadal',
    'Tacurong',
    'Kidapawan',
    'Butuan',
    'Surigao City',
    'Bislig',
    'Tandag',
    'Pagadian',
    'Zamboanga City',
    'Dipolog',
    'Dapitan',
  ],
};

// Barangays by city (major cities)
const barangaysByCity = {
  'Cagayan de Oro': [
    'Agusan',
    'Baikingon',
    'Balubal',
    'Balulang',
    'Barangay 1 (Poblacion)',
    'Barangay 2 (Poblacion)',
    'Barangay 3 (Poblacion)',
    'Barangay 4 (Poblacion)',
    'Barangay 5 (Poblacion)',
    'Barangay 6 (Poblacion)',
    'Barangay 7 (Poblacion)',
    'Barangay 8 (Poblacion)',
    'Barangay 9 (Poblacion)',
    'Barangay 10 (Poblacion)',
    'Besigan',
    'Bonbon',
    'Bugo',
    'Bulua',
    'Camaman-an',
    'Canito-an',
    'Carmen',
    'Consolacion',
    'Cugman',
    'Dansolihon',
    'F.S. Catanico',
    'Gusa',
    'Indahag',
    'Iponan',
    'Kauswagan',
    'Lapasan',
    'Lumbia',
    'Macabalan',
    'Macasandig',
    'Mambuaya',
    'Nazareth',
    'Pagalungan',
    'Pagatpat',
    'Patag',
    'Pigsag-an',
    'Puerto',
    'Puntod',
    'San Simon',
    'Tablon',
    'Taglimao',
    'Tagpangi',
    'Tignapoloan',
    'Tuburan',
    'Tumpagon',
  ],
  'Davao City': [
    'Acacia',
    'Agdao',
    'Alambre',
    'Alejandra Navarro (Lasang)',
    'Alfonso Angliongto Sr.',
    'Angalan',
    'Atan-awe',
    'Baganihan',
    'Bago Aplaya',
    'Bago Gallera',
    'Bago Oshiro',
    'Baguio',
    'Balengaeng',
    'Baliok',
    'Bangkas Heights',
    'Bantol',
    'Barcelona',
    'Baril',
    'Bayabas',
    'Biao Escuela',
    'Biao Guianga',
    'Biao Joaquin',
    'Binugao',
    'Bucana',
    'Buda',
    'Buhangin',
    'Bunawan',
    'Cabantian',
    'Cadalian',
    'Calinan',
    'Callawa',
    'Camansi',
    'Carmen',
    'Catalunan Grande',
    'Catalunan Pequeño',
    'Catigan',
    'Cawayan',
    'Centro (Pob.)',
    'Colosas',
    'Communal',
    'Crossing Bayabas',
    'Dacudao',
    'Dalag',
    'Dalagdag',
    'Daliao',
    'Daliaon Plantation',
    'Datu Salumay',
    'Dominga',
    'Dumoy',
    'Eden',
    'Fatima',
    'Gatungan',
    'Gov. Paciano Bangoy',
    'Gov. Vicente Duterte',
    'Gumalang',
    'Gumitan',
    'Ilang',
    'Inayangan',
    'Indangan',
    'Kap. Tomas Monteverde, Sr.',
    'Kilate',
    'Lacson',
    'Lamanan',
    'Lampianao',
    'Langub',
    'Lapu-lapu',
    'Leon Garcia, Sr.',
    'Lizada',
    'Los Amigos',
    'Lubogan',
    'Lumiad',
    'Ma-a',
    'Mabuhay',
    'Magsaysay',
    'Magtuod',
    'Mahayag',
    'Malabog',
    'Malagos',
    'Malamba',
    'Manambulan',
    'Mandug',
    'Manuel Guianga',
    'Mapula',
    'Marapangi',
    'Marilog',
    'Matina Aplaya',
    'Matina Biao',
    'Matina Crossing',
    'Matina Pangi',
    'Megkawayan',
    'Mintal',
    'Mudiang',
    'Mulig',
    'New Carmen',
    'New Valencia',
    'Pampanga',
    'Panacan',
    'Panalum',
    'Pandaitan',
    'Pangyan',
    'Paquibato',
    'Paradise Embak',
    'Rafael Castillo',
    'Riverside',
    'Salapawan',
    'Salaysay',
    'Saloy',
    'San Antonio',
    'San Isidro (Licanan)',
    'Santo Niño',
    'Sasa',
    'Sibulan',
    'Sirawan',
    'Sirib',
    'Suawan (Tuli)',
    'Subasta',
    'Sumimao',
    'Tacunan',
    'Tagakpan',
    'Tagluno',
    'Tagurano',
    'Talandang',
    'Talomo',
    'Talomo River',
    'Tamayong',
    'Tambobong',
    'Tamugan',
    'Tapak',
    'Tawan-tawan',
    'Tibuloy',
    'Tibungco',
    'Tigatto',
    'Toril',
    'Tugbok',
    'Tungkalan',
    'Ubalde',
    'Ula',
    'Vicente Hizon Sr.',
    'Waan',
    'Wangan',
    'Wilfredo Aquino',
    'Wines',
  ],
  Manila: [
    'Barangay 1 - Intramuros',
    'Barangay 2 - Intramuros',
    'Barangay 3 - Intramuros',
    'Barangay 4 - Intramuros',
    'Barangay 5 - Intramuros',
    'Barangay 6 - Intramuros',
    'Barangay 7 - Intramuros',
    'Barangay 8 - Intramuros',
    'Barangay 9 - Intramuros',
    'Barangay 10 - Intramuros',
    'Ermita',
    'Malate',
    'Pandacan',
    'Paco',
    'Port Area',
    'San Andres',
    'San Miguel',
    'San Nicolas',
    'Sampaloc',
    'Santa Ana',
    'Santa Cruz',
    'Santa Mesa',
    'Tondo',
  ],
  'Quezon City': [
    'Alicia',
    'Amihan',
    'Apolonio Samson',
    'Aurora',
    'Baesa',
    'Bagbag',
    'Bagong Lipunan ng Crame',
    'Bagong Pag-asa',
    'Bagong Silangan',
    'Bagumbayan',
    'Bagumbuhay',
    'Bahay Toro',
    'Balingasa',
    'Balintawak',
    'Batasan Hills',
    'Bayanihan',
    'Blue Ridge A',
    'Blue Ridge B',
    'Botocan',
    'Bungad',
    'Camp Aguinaldo',
    'Capri',
    'Central',
    'Claro',
    'Commonwealth',
    'Culiat',
    'Damar',
    'Damayan',
    'Damayang Lagi',
    'Del Monte',
    'Dioquino Zobel',
    'Don Manuel',
    'Dona Imelda',
    'Dona Josefa',
    'Duyan-Duyan',
    'E. Rodriguez',
    'East Kamias',
    'Escopa I',
    'Escopa II',
    'Escopa III',
    'Escopa IV',
    'Fairview',
    'Greater Lagro',
    'Gulod',
    'Holy Spirit',
    'Horseshoe',
    'Immaculate Conception',
    'Kaligayahan',
    'Kalusugan',
    'Kamias',
    'Kamuning',
    'Katipunan',
    'Kaunlaran',
    'Kristong Hari',
    'Krus na Ligas',
    'Laging Handa',
    'Libis',
    'Lourdes',
    'Loyola Heights',
    'Maharlika',
    'Malaya',
    'Mangga',
    'Manresa',
    'Mariana',
    'Mariblo',
    'Marilag',
    'Masagana',
    'Masambong',
    'Matandang Balara',
    'Milagrosa',
    'N.S. Amoranto',
    'Nagkaisang Nayon',
    'Naliyahan',
    'New Era',
    'North Fairview',
    'Novaliches Proper',
    'Obrero',
    'Old Capitol Site',
    'Paang Bundok',
    'Pag-ibig sa Nayon',
    'Pagkakaisa',
    'Palayan',
    'Paligsahan',
    'Paltok',
    'Pansol',
    'Paraiso',
    'Pasong Putik Proper',
    'Pasong Tamo',
    'Payatas',
    'Phil-Am',
    'Pinagkaisahan',
    'Pinyahan',
    'Project 6',
    'Quirino 2-A',
    'Quirino 2-B',
    'Quirino 2-C',
    'Quirino 3-A',
    'Ramon Magsaysay',
    'Roxas',
    'Sacred Heart',
    'Saint Ignatius',
    'Saint Peter',
    'Salvacion',
    'San Agustin',
    'San Antonio',
    'San Bartolome',
    'San Isidro',
    'San Isidro Labrador',
    'San Jose',
    'San Martin de Porres',
    'San Roque',
    'San Vicente',
    'Sangandaan',
    'Santa Cruz',
    'Santa Lucia',
    'Santa Monica',
    'Santa Teresita',
    'Santo Cristo',
    'Santo Niño',
    'Santol',
    'Sauyo',
    'Sienna',
    'Sikatuna Village',
    'Silangan',
    'Socorro',
    'South Triangle',
    'Tagumpay',
    'Talampas',
    'Talayan',
    'Talipapa',
    'Tandang Sora',
    'Tatalon',
    'Teachers Village East',
    'Teachers Village West',
    'Ugong Norte',
    'Unang Sigaw',
    'UP Campus',
    'UP Village',
    'Valencia',
    'Vasra',
    'Veterans Village',
    'West Kamias',
    'West Triangle',
    'White Plains',
  ],
  'Cebu City': [
    'Adlaon',
    'Agsungot',
    'Apas',
    'Babag',
    'Bacayan',
    'Banilad',
    'Basak Pardo',
    'Basak San Nicolas',
    'Binaliw',
    'Bonbon',
    'Budla-an',
    'Buhisan',
    'Bulacao',
    'Buot-Taup Pardo',
    'Busay',
    'Calamba',
    'Cambinocot',
    'Capitol Site',
    'Carreta',
    'Central',
    'Cogon Pardo',
    'Cogon Ramos',
    'Day-as',
    'Duljo Fatima',
    'Ermita',
    'Guba',
    'Guadalupe',
    'Hipodromo',
    'Inayawan',
    'Kalubihan',
    'Kalunasan',
    'Kamagayan',
    'Kamputhaw',
    'Kasambagan',
    'Kinasang-an Pardo',
    'Labangon',
    'Lahug',
    'Lorega San Miguel',
    'Lusaran',
    'Luz',
    'Mabini',
    'Mabolo',
    'Malubog',
    'Mambaling',
    'Pahina Central',
    'Pahina San Nicolas',
    'Pamutan',
    'Pardo',
    'Parian',
    'Paril',
    'Pasil',
    'Pit-os',
    'Poblacion Pardo',
    'Pulangbato',
    'Pung-ol Sibugay',
    'Punta Princesa',
    'Quiot Pardo',
    'Sambag I',
    'Sambag II',
    'San Antonio',
    'San Jose',
    'San Nicolas Central',
    'San Roque',
    'Santa Cruz',
    'Santo Niño',
    'Sapangdaku',
    'Sawang Calero',
    'Sinsin',
    'Sirao',
    'Suba Pasil',
    'Sudlon I',
    'Sudlon II',
    'T. Padilla',
    'Tabunan',
    'Tagbao',
    'Talamban',
    'Taptap',
    'Tejero',
    'Tinago',
    'Tisa',
    'To-ong Pardo',
    'Zapatera',
  ],
  // Default barangays for cities without specific data
  default: [
    'Barangay 1 (Poblacion)',
    'Barangay 2 (Poblacion)',
    'Barangay 3 (Poblacion)',
    'Barangay 4 (Poblacion)',
    'Barangay 5 (Poblacion)',
    'Barangay 6 (Poblacion)',
    'Barangay 7 (Poblacion)',
    'Barangay 8 (Poblacion)',
    'Barangay 9 (Poblacion)',
    'Barangay 10 (Poblacion)',
    'Please select from list or type manually',
  ],
};

// Form data
const formData = ref({
  storeName: '',
  region: '',
  city: '',
  barangay: '',
  storePlace: '',
  completeAddress: '',
});

// Error states
const errors = ref({
  storeName: '',
  region: '',
  city: '',
  barangay: '',
  storePlace: '',
  completeAddress: '',
});

// Available cities based on selected region
const availableCities = computed(() => {
  if (!formData.value.region) return [];
  return citiesByRegion[formData.value.region] || [];
});

// Available barangays based on selected city
const availableBarangays = computed(() => {
  if (!formData.value.city) return [];
  // Check if city has specific barangays, otherwise use default
  return barangaysByCity[formData.value.city] || barangaysByCity['default'];
});

// Barangay count for helper text
const barangayCount = computed(() => {
  return availableBarangays.value.length;
});

// Load data
onMounted(() => {
  const savedData = localStorage.getItem('registrationData');
  if (savedData) {
    const data = JSON.parse(savedData);
    if (data.storeName) formData.value.storeName = data.storeName;
    if (data.region) formData.value.region = data.region;
    if (data.city) formData.value.city = data.city;
    if (data.barangay) formData.value.barangay = data.barangay;
    if (data.storePlace) formData.value.storePlace = data.storePlace;
    if (data.completeAddress) formData.value.completeAddress = data.completeAddress;
  }
});

// Region change handler
const onRegionChange = () => {
  // Reset city and barangay when region changes
  formData.value.city = '';
  formData.value.barangay = '';
  errors.value.region = '';
  errors.value.city = '';
  errors.value.barangay = '';
};

// City change handler
const onCityChange = () => {
  // Reset barangay when city changes
  formData.value.barangay = '';
  errors.value.city = '';
  errors.value.barangay = '';
};

// Field validation
const validateField = (fieldName) => {
  switch (fieldName) {
    case 'storeName':
      if (!formData.value.storeName.trim()) {
        errors.value.storeName = 'Store name is required';
        return false;
      }
      if (formData.value.storeName.trim().length < 3) {
        errors.value.storeName = 'Store name must be at least 3 characters';
        return false;
      }
      errors.value.storeName = '';
      return true;

    case 'region':
      if (!formData.value.region) {
        errors.value.region = 'Please select a region';
        return false;
      }
      errors.value.region = '';
      return true;

    case 'city':
      if (!formData.value.city) {
        errors.value.city = 'Please select a city';
        return false;
      }
      errors.value.city = '';
      return true;

    case 'barangay':
      if (!formData.value.barangay) {
        errors.value.barangay = 'Please select a barangay';
        return false;
      }
      errors.value.barangay = '';
      return true;

    case 'storePlace':
      if (!formData.value.storePlace.trim()) {
        errors.value.storePlace = 'Store location is required';
        return false;
      }
      if (formData.value.storePlace.trim().length < 5) {
        errors.value.storePlace = 'Please provide more details';
        return false;
      }
      errors.value.storePlace = '';
      return true;

    case 'completeAddress':
      if (!formData.value.completeAddress.trim()) {
        errors.value.completeAddress = 'Complete address is required';
        return false;
      }
      if (formData.value.completeAddress.trim().length < 15) {
        errors.value.completeAddress = 'Please provide a complete address';
        return false;
      }
      errors.value.completeAddress = '';
      return true;

    default:
      return true;
  }
};

// Form validation
const isFormValid = computed(() => {
  return (
    formData.value.storeName.trim() &&
    formData.value.region &&
    formData.value.city &&
    formData.value.barangay &&
    formData.value.storePlace.trim() &&
    formData.value.completeAddress.trim()
  );
});

// Navigation
const goBack = () => {
  saveData();
  router.push('/register/step2');
};

const saveData = () => {
  const existingData = JSON.parse(localStorage.getItem('registrationData') || '{}');
  const updatedData = {
    ...existingData,
    storeName: formData.value.storeName,
    region: formData.value.region,
    city: formData.value.city,
    barangay: formData.value.barangay,
    storePlace: formData.value.storePlace,
    completeAddress: formData.value.completeAddress,
  };
  localStorage.setItem('registrationData', JSON.stringify(updatedData));
};

const handleNext = () => {
  const isStoreNameValid = validateField('storeName');
  const isRegionValid = validateField('region');
  const isCityValid = validateField('city');
  const isBarangayValid = validateField('barangay');
  const isStorePlaceValid = validateField('storePlace');
  const isAddressValid = validateField('completeAddress');

  if (isStoreNameValid && isRegionValid && isCityValid && isBarangayValid && isStorePlaceValid && isAddressValid) {
    saveData();
    router.push('/register/step4');
  }
};
</script>

<style scoped>
.step-content {
  --background: var(--color-bg-primary);
}

.step-container {
  min-height: 100vh;
  padding: 24px;
  padding-bottom: 100px;
  position: relative;
}

/* Back Button */
.back-button {
  position: absolute;
  top: 24px;
  left: 24px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-bg-tertiary);
  border-radius: 12px;
  cursor: pointer;
  z-index: 10;
  transition: background 0.2s;
}

.back-button:active {
  transform: scale(0.95);
  background: var(--color-border);
}

.back-button ion-icon {
  font-size: 24px;
  color: var(--color-text-primary);
}

/* Progress Section */
.progress-section {
  margin-top: 70px;
  margin-bottom: 32px;
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: var(--color-border);
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: var(--color-primary);
  transition: width 0.3s ease;
}

.progress-text {
  text-align: center;
  font-size: 13px;
  color: var(--color-text-secondary);
  margin: 0;
  font-weight: 600;
}

/* Title Section */
.title-section {
  text-align: center;
  margin-bottom: 40px;
}

.title {
  font-size: 28px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 15px;
  color: var(--color-text-secondary);
  margin: 0;
}

/* Form Section */
.form-section {
  max-width: 500px;
  margin: 0 auto;
  width: 100%;
}

/* Section Header */
.section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  padding-bottom: 12px;
  border-bottom: 2px solid var(--color-card-border);
}

.section-header ion-icon {
  font-size: 24px;
  color: var(--color-icon-primary);
}

.section-header h2 {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0;
}

/* Input Group */
.input-group {
  margin-bottom: 20px;
}

.input-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-primary);
  margin-bottom: 8px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 16px;
  top: 16px;
  font-size: 20px;
  color: var(--color-icon-primary);
  z-index: 2;
}

.custom-input {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input.with-icon {
  padding-left: 48px;
}

.custom-input:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.custom-input::placeholder {
  color: var(--color-input-placeholder);
}

/* Select Dropdown */
.custom-select {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  padding-right: 40px;
}

.custom-select.with-icon {
  padding-left: 48px;
}

.custom-select:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

/* Textarea */
.custom-textarea {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
}

.custom-textarea.with-icon {
  padding-left: 48px;
}

.custom-textarea:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.custom-textarea::placeholder {
  color: var(--color-input-placeholder);
}

/* Error State */
.input-wrapper.error .custom-input,
.input-wrapper.error .custom-select,
.input-wrapper.error .custom-textarea {
  border-color: var(--color-error);
}

.input-wrapper.error .input-icon {
  color: var(--color-error);
}

/* Helper/Error Text */
.helper-text {
  display: block;
  font-size: 12px;
  color: var(--color-text-tertiary);
  margin-top: 6px;
}

.error-message {
  display: block;
  font-size: 12px;
  color: var(--color-error);
  margin-top: 6px;
  font-weight: 500;
}

/* Info Box */
.info-box {
  display: flex;
  gap: 12px;
  background: var(--color-info-bg);
  border-left: 4px solid var(--color-info-border);
  padding: 16px;
  border-radius: 8px;
  margin: 32px 0 24px 0;
}

.info-box ion-icon {
  font-size: 24px;
  color: var(--color-info-border);
  flex-shrink: 0;
  margin-top: 2px;
}

.info-text {
  font-size: 13px;
  color: var(--color-info-text);
  line-height: 1.5;
}

.info-text strong {
  font-weight: 700;
}

/* Button Group */
.button-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-top: 32px;
}

.back-btn {
  --border-radius: 12px;
  --border-width: 2px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.next-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.next-button:disabled {
  --background: var(--color-border);
  --color: var(--color-text-tertiary);
}

/* Responsive */
@media (max-width: 480px) {
  .title {
    font-size: 24px;
  }

  .step-container {
    padding: 20px 16px 100px 16px;
  }

  .button-group {
    grid-template-columns: 1fr;
  }

  .back-btn {
    order: 2;
  }

  .next-button {
    order: 1;
  }
}
</style>
