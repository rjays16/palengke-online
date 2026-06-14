<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Profile</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
          <div class="profile-avatar-container">
            <div class="profile-avatar">
              <img v-if="profileData.avatar" :src="profileData.avatar" alt="Profile" />
              <ion-icon v-else :icon="personOutline"></ion-icon>
            </div>
            <ion-button class="change-photo-btn" fill="clear" size="small" @click="changePhoto">
              <ion-icon :icon="cameraOutline"></ion-icon>
            </ion-button>
          </div>

          <h2 class="profile-name">{{ profileData.name }}</h2>
          <p class="profile-type">{{ profileData.businessType }}</p>

          <div class="profile-stats">
            <div class="stat-item">
              <div class="stat-value">{{ profileData.totalOrders }}</div>
              <div class="stat-label">Orders</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-value">{{ profileData.rating }}</div>
              <div class="stat-label">Rating</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-value">{{ profileData.joinedDays }}</div>
              <div class="stat-label">Days Active</div>
            </div>
          </div>
        </div>

        <!-- Personal Information -->
        <div class="section">
          <h3 class="section-title">Personal Information</h3>

          <div class="info-list">
            <div class="info-item disabled">
              <div class="info-icon">
                <ion-icon :icon="personOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Full Name</p>
                <p class="info-value">{{ profileData.name }}</p>
              </div>
            </div>

            <div class="info-item" @click="editField('phone')">
              <div class="info-icon">
                <ion-icon :icon="callOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Phone Number</p>
                <p class="info-value">{{ profileData.phone }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('email')">
              <div class="info-icon">
                <ion-icon :icon="mailOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Email Address</p>
                <p class="info-value">{{ profileData.email }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('address')">
              <div class="info-icon">
                <ion-icon :icon="locationOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Address</p>
                <p class="info-value">{{ profileData.address }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Shop Information -->
        <div class="section">
          <h3 class="section-title">Shop Information</h3>

          <div class="info-list">
            <div class="info-item" @click="editField('shopName')">
              <div class="info-icon">
                <ion-icon :icon="storefrontOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Shop Name</p>
                <p class="info-value">{{ profileData.shopName }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('shopAddress')">
              <div class="info-icon">
                <ion-icon :icon="mapOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Shop Location</p>
                <p class="info-value">{{ profileData.shopAddress }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editOperatingHours">
              <div class="info-icon">
                <ion-icon :icon="timeOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Operating Hours</p>
                <p class="info-value">{{ hoursDisplayText }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('description')">
              <div class="info-icon">
                <ion-icon :icon="documentTextOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Shop Description</p>
                <p class="info-value">{{ profileData.description }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Payment Information -->
        <div class="section" v-if="paymentMethodsEnabled">
          <h3 class="section-title">Payment Information</h3>

          <div class="info-list" v-if="paymentMethods.length > 0">
            <div class="info-item" v-for="method in paymentMethods" :key="method.id">
              <div class="info-icon" :class="method.type">
                <ion-icon :icon="paymentIcon(method.type)"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">{{ displayType(method.type) }}</p>
                <p class="info-value">{{ method.account_name }} - {{ maskAccount(method.account_number) }}</p>
              </div>
              <ion-button fill="clear" color="danger" size="small" @click="deletePaymentMethod(method.id)">
                <ion-icon :icon="trashOutline" slot="icon-only"></ion-icon>
              </ion-button>
            </div>
          </div>

          <div v-else class="no-payments">
            <ion-icon :icon="cardOutline"></ion-icon>
            <p>No payment methods added yet</p>
          </div>

          <ion-button expand="block" class="add-payment-btn" @click="showPaymentAgreement">
            <ion-icon :icon="addOutline" slot="start"></ion-icon>
            Add Payment Method
          </ion-button>
        </div>

        <!-- Account Settings -->
        <div class="section">
          <h3 class="section-title">Account Settings</h3>

          <div class="settings-list">
            <div class="settings-item" @click="changePassword">
              <div class="settings-icon">
                <ion-icon :icon="lockClosedOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Change Password</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="changePin">
              <div class="settings-icon">
                <ion-icon :icon="keypadOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Change PIN</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="goToDevices">
              <div class="settings-icon">
                <ion-icon :icon="phonePortraitOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Devices</p>
                <p class="settings-value">Manage logged-in devices</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="notificationSettings">
              <div class="settings-icon">
                <ion-icon :icon="notificationsOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Notification Settings</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item">
              <div class="settings-icon">
                <ion-icon :icon="moonOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Dark Mode</p>
              </div>
              <ion-toggle v-model="darkMode" @ionChange="toggleDarkMode"></ion-toggle>
            </div>

            <div class="settings-item">
              <div class="settings-icon">
                <ion-icon :icon="languageOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Language</p>
                <p class="settings-value">English</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Support & Legal -->
        <div class="section">
          <h3 class="section-title">Support & Legal</h3>

          <div class="settings-list">
            <div class="settings-item" @click="helpCenter">
              <div class="settings-icon">
                <ion-icon :icon="helpCircleOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Help Center</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="contactSupport">
              <div class="settings-icon">
                <ion-icon :icon="chatbubbleEllipsesOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Contact Support</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="showTerms">
              <div class="settings-icon">
                <ion-icon :icon="documentTextOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Terms & Conditions</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="showPrivacy">
              <div class="settings-icon">
                <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Privacy Policy</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="aboutApp">
              <div class="settings-icon">
                <ion-icon :icon="informationCircleOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">About</p>
                <p class="settings-value">Version 1.0.0</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Logout Button -->
        <div class="logout-section">
          <ion-button expand="block" color="danger" @click="handleLogout" class="logout-button">
            <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
            Logout
          </ion-button>
        </div>

        <!-- App Version -->
        <div class="app-version">
          <p>Palengke Online Seller</p>
          <p>Version 1.0.0 (Build 100)</p>
        </div>
      </div>
    </ion-content>

    <!-- Hidden file input for web fallback -->
    <input type="file" accept="image/*" ref="fileInputRef" @change="handleFileInput" style="display: none" />

    <!-- Operating Hours Modal -->
    <div class="hours-overlay" v-if="showHoursModal" @click.self="showHoursModal = false">
      <div class="hours-modal">
        <div class="hours-header">
          <h3>Operating Hours</h3>
          <button class="hours-close" @click="showHoursModal = false">&times;</button>
        </div>
        <div class="hours-body">
          <div class="hours-day" v-for="day in dayKeys" :key="day">
            <div class="hours-day-name">{{ dayLabels[day] }}</div>
            <label class="hours-closed-toggle">
              <input type="checkbox" v-model="hoursData[day].closed" />
              Closed
            </label>
            <div class="hours-time-group" v-if="!hoursData[day].closed">
              <select v-model="hoursData[day].openHour">
                <option v-for="h in 12" :key="'oh' + h" :value="h">{{ h }}</option>
              </select>
              <span class="hours-sep">:</span>
              <select v-model="hoursData[day].openMin">
                <option v-for="m in ['00', '15', '30', '45']" :key="'om' + m" :value="m">{{ m }}</option>
              </select>
              <select v-model="hoursData[day].openAmPm">
                <option value="AM">AM</option>
                <option value="PM">PM</option>
              </select>
              <span class="hours-to">to</span>
              <select v-model="hoursData[day].closeHour">
                <option v-for="h in 12" :key="'ch' + h" :value="h">{{ h }}</option>
              </select>
              <span class="hours-sep">:</span>
              <select v-model="hoursData[day].closeMin">
                <option v-for="m in ['00', '15', '30', '45']" :key="'cm' + m" :value="m">{{ m }}</option>
              </select>
              <select v-model="hoursData[day].closeAmPm">
                <option value="AM">AM</option>
                <option value="PM">PM</option>
              </select>
            </div>
            <div class="hours-closed-text" v-else>Closed</div>
          </div>
        </div>
        <div class="hours-footer">
          <button class="hours-cancel" @click="showHoursModal = false">Cancel</button>
          <button class="hours-save" @click="saveOperatingHours">Save</button>
        </div>
      </div>
    </div>

    <!-- Bottom Navigation -->
    <ion-footer>
      <ion-toolbar>
        <div class="bottom-nav">
          <div class="nav-item" @click="goToHome">
            <ion-icon :icon="homeOutline"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item" @click="goToProducts">
            <ion-icon :icon="cubeOutline"></ion-icon>
            <span>Products</span>
          </div>
          <div class="nav-item" @click="goToOrders">
            <ion-icon :icon="receiptOutline"></ion-icon>
            <span>Orders</span>
          </div>
          <div class="nav-item active" @click="goToProfile">
            <ion-icon :icon="personOutline"></ion-icon>
            <span>Profile</span>
          </div>
        </div>
      </ion-toolbar>
    </ion-footer>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Capacitor } from '@capacitor/core';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonFooter,
  IonButtons,
  IonButton,
  IonBackButton,
  IonIcon,
  IonToggle,
  alertController,
  actionSheetController,
  toastController,
} from '@ionic/vue';
import {
  personOutline,
  cameraOutline,
  callOutline,
  mailOutline,
  locationOutline,
  storefrontOutline,
  mapOutline,
  timeOutline,
  documentTextOutline,
  addOutline,
  walletOutline,
  cardOutline,
  lockClosedOutline,
  keypadOutline,
  notificationsOutline,
  moonOutline,
  languageOutline,
  helpCircleOutline,
  chatbubbleEllipsesOutline,
  shieldCheckmarkOutline,
  informationCircleOutline,
  logOutOutline,
  chevronForwardOutline,
  imagesOutline,
  trashOutline,
  closeOutline,
  homeOutline,
  cubeOutline,
  receiptOutline,
  phonePortraitOutline,
  cashOutline,
  businessOutline,
  checkmarkCircleOutline,
} from 'ionicons/icons';
import { API_BASE } from '@/config/api';

const router = useRouter();

// Profile Data
const profileData = ref({
  name: 'Loading...',
  phone: '',
  email: '',
  address: '',
  businessType: 'Seller',
  shopName: 'Loading...',
  shopAddress: '',
  shopHours: '',
  description: '',
  categories: [],
  avatar: null,
  totalOrders: 0,
  rating: 0,
  joinedDays: 0,
});

const loadProfile = async () => {
  const email = localStorage.getItem('userEmail');
  if (!email) return;

  try {
    const response = await fetch(`${API_BASE}/seller/profile?email=${encodeURIComponent(email)}`);
    const data = await response.json();
    if (response.ok && data.user) {
      const u = data.user;
      profileData.value = {
        ...profileData.value,
        name: u.firstName + ' ' + u.lastName,
        phone: u.phone || '',
        email: u.email || '',
        address: u.address || '',
        businessType: 'Seller',
        shopName: u.storeName || '',
        shopAddress: u.storeAddress || '',
        shopHours: u.operatingHours || '',
        description: u.description || '',
        categories: u.categories || [],
        avatar: u.avatar || null,
      };
    }
  } catch {
    // keep defaults
  }
};

const darkMode = ref(false);
const paymentMethodsEnabled = ref(true);
const showHoursModal = ref(false);

const fileInputRef = ref(null);
const cameraCaptureMode = ref('camera');

const dayKeys = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
const dayLabels = {
  monday: 'Monday',
  tuesday: 'Tuesday',
  wednesday: 'Wednesday',
  thursday: 'Thursday',
  friday: 'Friday',
  saturday: 'Saturday',
  sunday: 'Sunday',
};

const defaultDayHours = {
  openHour: 7,
  openMin: '00',
  openAmPm: 'AM',
  closeHour: 5,
  closeMin: '00',
  closeAmPm: 'PM',
  closed: false,
};
const hoursData = ref({});

function initDefaultHours() {
  const h = {};
  dayKeys.forEach((day) => {
    h[day] = { ...defaultDayHours };
  });
  h.saturday = { ...defaultDayHours, openHour: 8, closeHour: 12 };
  h.sunday = { ...defaultDayHours, closed: true };
  return h;
}

function parseHoursFromString(str) {
  if (!str) return initDefaultHours();
  try {
    const parsed = JSON.parse(str);
    const h = initDefaultHours();
    dayKeys.forEach((day) => {
      if (parsed[day]) {
        h[day] = { ...h[day], ...parsed[day] };
      }
    });
    return h;
  } catch {
    return initDefaultHours();
  }
}

function formatOpenClose(day) {
  const d = hoursData.value[day];
  if (!d || d.closed) return 'Closed';
  return `${d.openHour}:${d.openMin} ${d.openAmPm} - ${d.closeHour}:${d.closeMin} ${d.closeAmPm}`;
}

function formatOperatingHours(hoursObj) {
  const lines = dayKeys.map((day) => {
    const d = hoursObj[day];
    if (!d || d.closed) return `${dayLabels[day]}: Closed`;
    return `${dayLabels[day]}: ${d.openHour}:${d.openMin} ${d.openAmPm} - ${d.closeHour}:${d.closeMin} ${d.closeAmPm}`;
  });
  return lines.join('\n');
}

function getHoursSummary(str) {
  if (!str) return 'Set operating hours';
  try {
    const parsed = JSON.parse(str);
    const openDays = dayKeys.filter((d) => parsed[d] && !parsed[d].closed);
    if (openDays.length === 0) return 'Closed all week';
    if (openDays.length === 7) return 'Open daily';
    const first = parsed[openDays[0]];
    const allSame = openDays.every(
      (d) =>
        parsed[d].openHour === first.openHour &&
        parsed[d].openMin === first.openMin &&
        parsed[d].openAmPm === first.openAmPm &&
        parsed[d].closeHour === first.closeHour &&
        parsed[d].closeMin === first.closeMin &&
        parsed[d].closeAmPm === first.closeAmPm,
    );
    if (allSame) {
      if (openDays.length === 5 && openDays[0] === 'monday' && openDays[4] === 'friday')
        return `Mon-Fri: ${first.openHour}:${first.openMin} ${first.openAmPm} - ${first.closeHour}:${first.closeMin} ${first.closeAmPm}`;
      return `${openDays.length} days, ${first.openHour}:${first.openMin} ${first.openAmPm} - ${first.closeHour}:${first.closeMin} ${first.closeAmPm}`;
    }
    return `${openDays.length} days open`;
  } catch {
    return str;
  }
}

const hoursDisplayText = computed(() => getHoursSummary(profileData.value.shopHours));

function editOperatingHours() {
  hoursData.value = parseHoursFromString(profileData.value.shopHours);
  showHoursModal.value = true;
}

const saveOperatingHours = async () => {
  const hoursString = JSON.stringify(hoursData.value);
  const email = localStorage.getItem('userEmail');
  if (!email) return;

  showHoursModal.value = false;
  await showLoadingModal('Saving operating hours...', false);

  try {
    const response = await fetch(`${API_BASE}/seller/profile`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, operating_hours: hoursString }),
    });

    await dismissLoadingModal();

    if (!response.ok) {
      const err = await response.json();
      throw new Error(err.message || 'Failed to save');
    }

    profileData.value.shopHours = hoursString;
    await showSuccessModal('Operating hours updated!');
  } catch (error) {
    await dismissLoadingModal();
    const toast = await toastController.create({
      message: error.message || 'Failed to save operating hours',
      duration: 2000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  }
};

let loadingModalRef = null;

// Loading Modal Function
const showLoadingModal = async (message = 'Loading...', autoDismiss = true) => {
  const alert = await alertController.create({
    cssClass: 'loading-modal-simple',
    message: message,
    buttons: [],
  });

  await alert.present();
  loadingModalRef = alert;

  if (autoDismiss) {
    setTimeout(() => {
      alert.dismiss();
      loadingModalRef = null;
    }, 1500);
  }
};

const dismissLoadingModal = async () => {
  if (loadingModalRef) {
    await loadingModalRef.dismiss();
    loadingModalRef = null;
  }
};

// Payment Methods
const paymentMethods = ref([]);

const loadSettings = async () => {
  try {
    const response = await fetch(`${API_BASE}/settings`);
    const data = await response.json();
    if (response.ok) {
      const val = data.enable_seller_payment_methods;
      if (val !== undefined) {
        paymentMethodsEnabled.value = val === 'true' || val === true;
      }
    }
  } catch {
    // default to enabled on error
  }
};

const loadPaymentMethods = async () => {
  const email = localStorage.getItem('userEmail');
  if (!email) return;

  try {
    const response = await fetch(`${API_BASE}/seller/payment-methods?email=${encodeURIComponent(email)}`);
    const data = await response.json();
    if (response.ok) {
      paymentMethods.value = data.payment_methods || [];
    }
  } catch {
    // silent
  }
};

const paymentIcon = (type) => {
  const icons = {
    gcash: walletOutline,
    bdo: businessOutline,
    bpi: businessOutline,
    metrobank: businessOutline,
    landbank: businessOutline,
    unionbank: businessOutline,
    pnb: businessOutline,
  };
  return icons[type] || cardOutline;
};

const displayType = (type) => {
  const names = {
    gcash: 'GCash',
    bdo: 'BDO',
    bpi: 'BPI',
    metrobank: 'Metrobank',
    landbank: 'Landbank',
    unionbank: 'Unionbank',
    pnb: 'PNB',
  };
  return names[type] || type.charAt(0).toUpperCase() + type.slice(1);
};

const maskAccount = (number) => {
  if (!number) return '';
  if (number.length <= 4) return number;
  return '****' + number.slice(-4);
};

const showPaymentAgreement = async () => {
  const alert = await alertController.create({
    cssClass: 'agreement-modal',
    header: ' ',
    message: 'loading',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'I Agree',
        handler: () => {
          showAddPaymentForm();
        },
      },
    ],
  });
  await alert.present();

  setTimeout(() => {
    const alertElement = document.querySelector('.agreement-modal .alert-message');
    if (alertElement) {
      alertElement.innerHTML = `
        <div class="agreement-content">
          <div class="agreement-icon">
            <img src="/assets/images/checkmark_success.png" alt="shield" style="width:36px;height:36px;">
          </div>
          <h3>Payment Information Agreement</h3>
          <p>By adding your payment details, you agree that:</p>
          <ul>
            <li>Your payment information will be used for order payouts and refunds</li>
            <li>You are the owner of the provided payment account</li>
            <li>The information provided is accurate and up-to-date</li>
            <li>Palengke Online may verify your payment details</li>
          </ul>
        </div>
      `;
    }
  }, 50);
};

const showAddPaymentForm = async () => {
  // Step 1: Select payment type
  const typeAlert = await alertController.create({
    header: 'Select Payment Type',
    inputs: [
      { type: 'radio', label: 'GCash', value: 'gcash', checked: true },
      { type: 'radio', label: 'BDO', value: 'bdo' },
      { type: 'radio', label: 'BPI', value: 'bpi' },
      { type: 'radio', label: 'Metrobank', value: 'metrobank' },
      { type: 'radio', label: 'Landbank', value: 'landbank' },
      { type: 'radio', label: 'Unionbank', value: 'unionbank' },
      { type: 'radio', label: 'PNB', value: 'pnb' },
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Next',
        handler: (selectedType) => {
          if (!selectedType) {
            return false;
          }
          showPaymentDetailsForm(selectedType);
        },
      },
    ],
  });
  await typeAlert.present();
};

const showPaymentDetailsForm = async (selectedType) => {
  const alert = await alertController.create({
    header: `${displayType(selectedType)} Details`,
    inputs: [
      {
        name: 'account_name',
        type: 'text',
        placeholder: 'Account Name',
      },
      {
        name: 'account_number',
        type: 'text',
        placeholder: 'Account Number',
      },
    ],
    buttons: [
      { text: 'Back', role: 'cancel' },
      {
        text: 'Add',
        handler: async (data) => {
          if (!data.account_name || !data.account_number) {
            const toast = await toastController.create({
              message: 'All fields are required',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            return false;
          }

          alert.dismiss();
          await showLoadingModal('Adding payment method...', false);

          try {
            const response = await fetch(`${API_BASE}/seller/payment-methods`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                email: localStorage.getItem('userEmail') || '',
                type: selectedType,
                account_name: data.account_name,
                account_number: data.account_number,
                agreed: true,
              }),
            });

            const result = await response.json();

            if (!response.ok) {
              throw new Error(result.message || 'Failed to add payment method');
            }

            await dismissLoadingModal();
            await showSuccessModal('Payment method added successfully!');
            await loadPaymentMethods();
          } catch (error) {
            await dismissLoadingModal();
            const toast = await toastController.create({
              message: error.message || 'Failed to add payment method',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
          }
        },
      },
    ],
  });
  await alert.present();
};

const deletePaymentMethod = async (id) => {
  const alert = await alertController.create({
    header: 'Remove Payment Method',
    message: 'Are you sure you want to remove this payment method?',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Remove',
        role: 'destructive',
        handler: async () => {
          await showLoadingModal('Removing...', false);

          try {
            const response = await fetch(`${API_BASE}/seller/payment-methods/${id}`, {
              method: 'DELETE',
            });

            if (!response.ok) {
              throw new Error('Failed to remove payment method');
            }

            await dismissLoadingModal();
            await showSuccessModal('Payment method removed successfully!');
            await loadPaymentMethods();
          } catch (error) {
            await dismissLoadingModal();
            const toast = await toastController.create({
              message: error.message || 'Failed to remove payment method',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
          }
        },
      },
    ],
  });
  await alert.present();
};

// Success Modal Function
const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK'],
  });

  await alert.present();

  // Add the image after modal is presented
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      // Clear the text content
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';

      // Create wrapper for centering
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';

      // Create and add the image
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';

      // Create and add the text
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';

      // Append to wrapper
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);

      // Append wrapper to alert
      alertElement.appendChild(wrapper);
    }
  }, 50);

  // Auto dismiss after 2 seconds
  setTimeout(() => {
    alert.dismiss();
  }, 2000);
};

// Methods

const changePhoto = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Change Profile Photo',
    buttons: [
      {
        text: 'Take Photo',
        icon: cameraOutline,
        handler: async () => {
          if (Capacitor.isNativePlatform()) {
            try {
              const photo = await Camera.getPhoto({
                resultType: CameraResultType.DataUrl,
                source: CameraSource.Camera,
                quality: 80,
                width: 500,
                height: 500,
              });
              if (photo?.dataUrl) {
                await uploadAvatar(photo.dataUrl);
              }
            } catch (e) {
              if (e?.message !== 'User cancelled photos app') {
                const toast = await toastController.create({
                  message: 'Failed to take photo',
                  duration: 2000,
                  color: 'danger',
                  position: 'top',
                });
                await toast.present();
              }
            }
          } else {
            const input = fileInputRef.value;
            if (input) {
              input.setAttribute('capture', 'user');
              input.value = '';
              cameraCaptureMode.value = 'camera';
              input.click();
            }
          }
        },
      },
      {
        text: 'Choose from Gallery',
        icon: imagesOutline,
        handler: async () => {
          if (Capacitor.isNativePlatform()) {
            try {
              const photo = await Camera.getPhoto({
                resultType: CameraResultType.DataUrl,
                source: CameraSource.Photos,
                quality: 80,
                width: 500,
                height: 500,
              });
              if (photo?.dataUrl) {
                await uploadAvatar(photo.dataUrl);
              }
            } catch (e) {
              if (e?.message !== 'User cancelled photos app') {
                const toast = await toastController.create({
                  message: 'Failed to pick photo',
                  duration: 2000,
                  color: 'danger',
                  position: 'top',
                });
                await toast.present();
              }
            }
          } else {
            const input = fileInputRef.value;
            if (input) {
              input.removeAttribute('capture');
              input.value = '';
              cameraCaptureMode.value = 'gallery';
              input.click();
            }
          }
        },
      },
      {
        text: 'Remove Photo',
        icon: trashOutline,
        role: 'destructive',
        handler: async () => {
          const email = localStorage.getItem('userEmail');
          if (!email) {
            profileData.value.avatar = null;
            return;
          }

          await showLoadingModal('Removing photo...', false);

          try {
            const response = await fetch(`${API_BASE}/seller/profile`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
              body: JSON.stringify({ email, remove_avatar: true }),
            });

            await dismissLoadingModal();

            if (!response.ok) {
              const err = await response.json();
              throw new Error(err.message || 'Failed to remove photo');
            }

            profileData.value.avatar = null;
            await showSuccessModal('Photo removed!');
          } catch (error) {
            await dismissLoadingModal();
            const toast = await toastController.create({
              message: error.message || 'Failed to remove photo',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
          }
        },
      },
      {
        text: 'Cancel',
        icon: closeOutline,
        role: 'cancel',
      },
    ],
  });
  await actionSheet.present();
};

const handleFileInput = async (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = async (e) => {
    const dataUrl = e.target?.result;
    if (dataUrl) {
      await uploadAvatar(dataUrl);
    }
  };
  reader.readAsDataURL(file);
  event.target.value = '';
};

const uploadAvatar = async (dataUrl) => {
  const email = localStorage.getItem('userEmail');
  if (!email) return;

  await showLoadingModal('Uploading photo...', false);

  try {
    const response = await fetch(`${API_BASE}/seller/profile`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ email, avatar: dataUrl }),
    });

    await dismissLoadingModal();

    if (!response.ok) {
      const err = await response.json();
      throw new Error(err.message || 'Failed to upload photo');
    }

    const result = await response.json();
    profileData.value.avatar = result.user?.avatar || null;
    await showSuccessModal('Photo updated!');
  } catch (error) {
    await dismissLoadingModal();
    const toast = await toastController.create({
      message: error.message || 'Failed to upload photo',
      duration: 2000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  }
};

const editField = async (field) => {
  const fieldNames = {
    name: 'Full Name',
    phone: 'Phone Number',
    email: 'Email Address',
    address: 'Address',
    shopName: 'Shop Name',
    shopAddress: 'Shop Location',
    shopHours: 'Operating Hours',
    description: 'Shop Description',
    gcash: 'GCash Number',
    bank: 'Bank Account',
  };

  const alert = await alertController.create({
    header: `Edit ${fieldNames[field]}`,
    inputs: [
      {
        name: 'value',
        type: field === 'email' ? 'email' : field === 'phone' ? 'tel' : 'text',
        placeholder: `Enter ${fieldNames[field]}`,
        value: profileData.value[field],
      },
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Save',
        handler: async (data) => {
          if (!data.value) return;

          profileData.value[field] = data.value;

          const shopFields = ['shopName', 'shopAddress', 'description', 'address', 'phone', 'email'];
          if (shopFields.includes(field)) {
            const email = localStorage.getItem('userEmail');
            if (!email) {
              await showSuccessModal('Updated successfully!');
              return;
            }

            alert.dismiss();
            await showLoadingModal('Saving...', false);

            try {
              const body = { email };
              if (field === 'shopName') body.store_name = data.value;
              if (field === 'shopAddress') body.store_place = data.value;
              if (field === 'shopHours') body.operating_hours = data.value;
              if (field === 'description') body.description = data.value;
              if (field === 'address') body.current_address = data.value;
              if (field === 'phone') body.phone = data.value;
              if (field === 'email') body.new_email = data.value;

              const response = await fetch(`${API_BASE}/seller/profile`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify(body),
              });

              await dismissLoadingModal();

              if (!response.ok) {
                const err = await response.json();
                throw new Error(err.message || 'Failed to save');
              }

              if (field === 'email') {
                localStorage.setItem('userEmail', data.value);
              }
            } catch (error) {
              await dismissLoadingModal();
              const toast = await toastController.create({
                message: error.message || 'Failed to save',
                duration: 2000,
                color: 'danger',
                position: 'top',
              });
              await toast.present();
              return;
            }
          }

          await showSuccessModal('Updated successfully!');
        },
      },
    ],
  });
  await alert.present();
};

const changePassword = async () => {
  const alert = await alertController.create({
    header: 'Change Password',
    inputs: [
      {
        name: 'currentPassword',
        type: 'password',
        placeholder: 'Current Password',
      },
      {
        name: 'newPassword',
        type: 'password',
        placeholder: 'New Password',
      },
      {
        name: 'confirmPassword',
        type: 'password',
        placeholder: 'Confirm New Password',
      },
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Update',
        handler: async (data) => {
          if (!data.currentPassword || !data.newPassword || !data.confirmPassword) {
            const toast = await toastController.create({
              message: 'All fields are required',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            return false;
          }

          if (data.newPassword !== data.confirmPassword) {
            const toast = await toastController.create({
              message: 'Passwords do not match',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            return false;
          }

          if (data.newPassword.length < 6) {
            const toast = await toastController.create({
              message: 'Password must be at least 6 characters',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            return false;
          }

          alert.dismiss();
          await showLoadingModal('Updating password...', false);

          try {
            const response = await fetch(`${API_BASE}/seller/change-password`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                email: localStorage.getItem('userEmail') || '',
                current_password: data.currentPassword,
                new_password: data.newPassword,
              }),
            });

            const result = await response.json();

            if (!response.ok) {
              throw new Error(result.message || 'Failed to change password');
            }

            await dismissLoadingModal();
            await showSuccessModal('Password changed successfully!');
          } catch (error) {
            await dismissLoadingModal();
            const toast = await toastController.create({
              message: error.message || 'Failed to change password',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            changePassword();
          }
        },
      },
    ],
  });
  await alert.present();
};

const changePin = async () => {
  const alert = await alertController.create({
    header: 'Change PIN',
    inputs: [
      {
        name: 'currentPin',
        type: 'password',
        placeholder: 'Current PIN',
        attributes: {
          inputmode: 'numeric',
          maxlength: 5,
          pattern: '[0-9]*',
        },
      },
      {
        name: 'newPin',
        type: 'password',
        placeholder: 'New PIN (5 digits)',
        attributes: {
          inputmode: 'numeric',
          maxlength: 5,
          pattern: '[0-9]*',
        },
      },
      {
        name: 'confirmPin',
        type: 'password',
        placeholder: 'Confirm New PIN',
        attributes: {
          inputmode: 'numeric',
          maxlength: 5,
          pattern: '[0-9]*',
        },
      },
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Update',
        handler: async (data) => {
          if (!data.currentPin || !data.newPin || !data.confirmPin) {
            const toast = await toastController.create({
              message: 'All fields are required',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            return false;
          }

          if (data.newPin !== data.confirmPin) {
            const toast = await toastController.create({
              message: 'New PINs do not match',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            return false;
          }

          if (data.newPin.length !== 5 || !/^\d{5}$/.test(data.newPin)) {
            const toast = await toastController.create({
              message: 'PIN must be exactly 5 digits',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            return false;
          }

          alert.dismiss();
          await showLoadingModal('Updating PIN...', false);

          try {
            const response = await fetch(`${API_BASE}/seller/device/change-pin`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                email: localStorage.getItem('userEmail') || '',
                device_id: localStorage.getItem('deviceId') || '',
                current_pin: data.currentPin,
                new_pin: data.newPin,
              }),
            });

            const result = await response.json();

            if (!response.ok) {
              throw new Error(result.message || 'Failed to change PIN');
            }

            // Dismiss loading before showing success
            await dismissLoadingModal();
            await showSuccessModal('PIN changed successfully!');
          } catch (error) {
            // Dismiss loading before showing error
            await dismissLoadingModal();
            const toast = await toastController.create({
              message: error.message || 'Failed to change PIN',
              duration: 2000,
              color: 'danger',
              position: 'top',
            });
            await toast.present();
            // Re-open the PIN alert for retry
            changePin();
          }
        },
      },
    ],
  });
  await alert.present();
};

const goToDevices = () => {
  router.push('/devices');
};

const notificationSettings = () => {
  router.push('/notifications');
};

const toggleDarkMode = () => {
  console.log('Dark mode:', darkMode.value);
  // TODO: Implement dark mode
};

const helpCenter = () => {
  router.push('/help');
};

const contactSupport = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Contact Support',
    buttons: [
      {
        text: 'Chat Support',
        handler: async () => {
          await showLoadingModal('Opening chat...');
        },
      },
      {
        text: 'Email Support',
        handler: async () => {
          await showLoadingModal('Opening email...');
        },
      },
      {
        text: 'Call Support',
        handler: async () => {
          await showLoadingModal('Connecting call...');
        },
      },
      {
        text: 'Cancel',
        role: 'cancel',
      },
    ],
  });
  await actionSheet.present();
};

const showTerms = async () => {
  const alert = await alertController.create({
    header: 'Terms & Conditions',
    message: 'Terms and conditions content will be displayed here.',
    buttons: ['Close'],
  });
  await alert.present();
};

const showPrivacy = async () => {
  const alert = await alertController.create({
    header: 'Privacy Policy',
    message: 'Privacy policy content will be displayed here.',
    buttons: ['Close'],
  });
  await alert.present();
};

const aboutApp = async () => {
  const alert = await alertController.create({
    header: 'About Palengke Online',
    message:
      'Palengke Online Seller\nVersion 1.0.0 (Build 100)\n\nEmpowering Filipino sellers with digital solutions.\n\n© 2024 Palengke Online\nAll rights reserved.',
    buttons: ['Close'],
  });
  await alert.present();
};

const handleLogout = async () => {
  const alert = await alertController.create({
    header: 'Logout',
    message: 'Are you sure you want to logout?',
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Logout',
        role: 'destructive',
        handler: async () => {
          const email = localStorage.getItem('userEmail');
          const deviceId = localStorage.getItem('deviceId');
          const hasPin = localStorage.getItem('hasPin');

          const loading = await alertController.create({
            cssClass: 'loading-modal-simple',
            message: 'Logging out...',
            buttons: [],
          });
          await loading.present();

          try {
            await fetch(`${API_BASE}/seller/devices/logout-current`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email }),
            });
          } catch {
            // silent
          }

          ['isAuthenticated', 'token', 'pendingAuth'].forEach((k) => localStorage.removeItem(k));
          if (email) localStorage.setItem('userEmail', email);
          if (deviceId) localStorage.setItem('deviceId', deviceId);
          if (hasPin) localStorage.setItem('hasPin', hasPin);

          await loading.dismiss();
          showSuccessLogout();
          setTimeout(() => {
            router.push('/login');
          }, 1500);
        },
      },
    ],
  });
  await alert.present();
};

const showSuccessLogout = async () => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: 'Logged out successfully',
    buttons: ['OK'],
  });

  await alert.present();

  // Add the image after modal is presented
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      // Clear the text content
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';

      // Create wrapper for centering
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';

      // Create and add the image
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';

      // Create and add the text
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';

      // Append to wrapper
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);

      // Append wrapper to alert
      alertElement.appendChild(wrapper);
    }
  }, 50);

  setTimeout(() => {
    alert.dismiss();
  }, 1500);
};

// Bottom Navigation
const goToHome = () => {
  router.push('/home');
};

const goToProducts = () => {
  router.push('/products');
};

const goToOrders = () => {
  router.push('/orders');
};

const goToProfile = () => {
  // Already on profile page
};

onMounted(() => {
  loadProfile();
  loadSettings();
  loadPaymentMethods();
});
</script>

<style scoped>
.profile-container {
  background: #f8f9fa;
  min-height: 100%;
  padding-bottom: 40px;
}

/* Profile Header */
.profile-header {
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  padding: 40px 20px 32px 20px;
  text-align: center;
  color: white;
}

.profile-avatar-container {
  position: relative;
  display: inline-block;
  margin-bottom: 16px;
}

.profile-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  border: 4px solid white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.profile-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.change-photo-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  --background: white;
  --color: #4caf50;
  --border-radius: 50%;
  width: 36px;
  height: 36px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.profile-name {
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 4px 0;
}

.profile-type {
  font-size: 14px;
  margin: 0 0 24px 0;
  opacity: 0.9;
}

.profile-stats {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 24px;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 12px;
  opacity: 0.9;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.3);
}

/* Sections */
.section {
  background: white;
  margin: 16px 16px 0 16px;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.section-title {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

/* Info List */
.info-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  padding: 12px;
  margin: -12px;
  border-radius: 12px;
  transition: background 0.2s;
}

.info-item:active {
  background: #f8f9fa;
}

.info-item.disabled {
  cursor: default;
  opacity: 0.8;
}

.info-item.disabled:active {
  background: transparent;
}

.info-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #e8f5e9;
  color: #4caf50;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}

.info-icon.payment {
  background: #fff3e0;
  color: #ff9800;
}

.info-content {
  flex: 1;
  min-width: 0;
}

.info-label {
  font-size: 12px;
  color: #999;
  margin: 0 0 4px 0;
}

.info-value {
  font-size: 15px;
  color: #1a1a1a;
  font-weight: 500;
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.info-arrow {
  font-size: 20px;
  color: #ccc;
}

/* Settings List */
.settings-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.settings-item {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  padding: 12px;
  margin: -12px;
  border-radius: 12px;
  transition: background 0.2s;
}

.settings-item:active {
  background: #f8f9fa;
}

.settings-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #f0f0f0;
  color: #666;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}

.settings-content {
  flex: 1;
  min-width: 0;
}

.settings-label {
  font-size: 15px;
  color: #1a1a1a;
  font-weight: 500;
  margin: 0;
}

.settings-value {
  font-size: 13px;
  color: #999;
  margin: 4px 0 0 0;
}

.settings-arrow {
  font-size: 20px;
  color: #ccc;
}

/* Logout Section */
.logout-section {
  padding: 0 16px;
  margin-top: 24px;
}

.logout-button {
  --border-radius: 12px;
  height: 48px;
  font-weight: 600;
  text-transform: none;
  font-size: 15px;
}

/* App Version */
.app-version {
  text-align: center;
  padding: 24px 16px;
}

.app-version p {
  font-size: 12px;
  color: #999;
  margin: 4px 0;
}

/* Responsive */
@media (max-width: 360px) {
  .profile-avatar {
    width: 80px;
    height: 80px;
  }

  .profile-name {
    font-size: 20px;
  }

  .stat-value {
    font-size: 20px;
  }
}

/* Bottom Navigation */
.bottom-nav {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  height: 60px;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  cursor: pointer;
  color: #999;
  transition: color 0.2s;
}

.nav-item ion-icon {
  font-size: 24px;
}

.nav-item span {
  font-size: 11px;
  font-weight: 500;
}

.nav-item.active {
  color: #4caf50;
}

.nav-item:active {
  transform: scale(0.95);
}

/* Success Modal Styling */
:deep(.success-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 320px;
  padding: 30px 20px;
}

:deep(.success-modal .alert-head) {
  padding: 0;
  min-height: 0;
}

:deep(.success-modal .alert-message) {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.success-modal .alert-button-group) {
  padding: 10px 0 0 0;
}

:deep(.success-modal button) {
  background: #4caf50;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
}

/* Loading Modal Styling */
:deep(.loading-modal-simple .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 280px;
  padding: 50px 30px;
  background: white;
}

:deep(.loading-modal-simple .alert-head) {
  display: none;
}

:deep(.loading-modal-simple .alert-message) {
  padding: 0;
  position: relative;
  padding-top: 70px;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

:deep(.loading-modal-simple .alert-message::before) {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  margin-left: -25px;
  width: 50px;
  height: 50px;
  border: 5px solid #f3f3f3;
  border-top: 5px solid #1877f2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

:deep(.loading-modal-simple .alert-button-group) {
  display: none;
}

/* Payment Methods */
.no-payments {
  text-align: center;
  padding: 24px 0;
  color: #999;
}

.no-payments ion-icon {
  font-size: 48px;
  margin-bottom: 12px;
  color: #ccc;
}

.no-payments p {
  font-size: 14px;
  margin: 0;
}

.add-payment-btn {
  --background: #e8f5e9;
  --color: #4caf50;
  --border-radius: 12px;
  --border-color: #4caf50;
  --border-style: dashed;
  --border-width: 2px;
  height: 48px;
  font-weight: 600;
  text-transform: none;
  font-size: 14px;
  margin-top: 16px;
}

/* Payment Type Icon Colors */
.info-icon.gcash {
  background: #e8f5e9;
  color: #4caf50;
}

.info-icon.bdo {
  background: #e3f2fd;
  color: #1565c0;
}

.info-icon.bpi {
  background: #fff3e0;
  color: #e65100;
}

.info-icon.metrobank {
  background: #f3e5f5;
  color: #7b1fa2;
}

.info-icon.landbank {
  background: #e8f5e9;
  color: #2e7d32;
}

.info-icon.unionbank {
  background: #fff8e1;
  color: #f9a825;
}

.info-icon.pnb {
  background: #fce4ec;
  color: #c62828;
}

/* Agreement Modal */
:deep(.agreement-modal .alert-wrapper) {
  border-radius: 20px;
  max-width: 340px;
  padding: 24px 20px;
}

:deep(.agreement-modal .alert-head) {
  display: none;
}

:deep(.agreement-modal .alert-message) {
  padding: 0;
  color: #1a1a1a;
}

.agreement-content {
  text-align: center;
}

.agreement-icon {
  width: 72px;
  height: 72px;
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px auto;
}

.agreement-icon ion-icon {
  font-size: 36px;
  color: white;
}

.agreement-content h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.agreement-content p {
  font-size: 14px;
  color: #666;
  margin: 0 0 12px 0;
  text-align: left;
}

.agreement-content ul {
  text-align: left;
  padding-left: 20px;
  margin: 0;
}

.agreement-content ul li {
  font-size: 13px;
  color: #666;
  margin-bottom: 8px;
  line-height: 1.4;
}

:deep(.agreement-modal button) {
  margin-top: 8px;
  border-radius: 12px;
  height: 44px;
  font-weight: 600;
}

:deep(.agreement-modal .alert-button-group) {
  padding: 12px 0 0 0;
}

/* Operating Hours Modal */
.hours-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 16px;
}

.hours-modal {
  background: white;
  border-radius: 20px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.hours-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px 0;
}

.hours-header h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.hours-close {
  background: none;
  border: none;
  font-size: 28px;
  color: #999;
  cursor: pointer;
  padding: 4px 8px;
  line-height: 1;
}

.hours-body {
  padding: 16px 24px;
  overflow-y: auto;
  flex: 1;
}

.hours-day {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
  flex-wrap: wrap;
}

.hours-day:last-child {
  border-bottom: none;
}

.hours-day-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  min-width: 90px;
  flex-shrink: 0;
}

.hours-closed-toggle {
  font-size: 13px;
  color: #666;
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  flex-shrink: 0;
}

.hours-closed-toggle input[type='checkbox'] {
  width: 16px;
  height: 16px;
  accent-color: #4caf50;
}

.hours-time-group {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-wrap: wrap;
}

.hours-time-group select {
  padding: 4px 8px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 13px;
  background: white;
  color: #333;
  outline: none;
  min-width: 48px;
}

.hours-sep {
  font-size: 14px;
  color: #999;
}

.hours-to {
  font-size: 12px;
  color: #999;
  margin: 0 2px;
}

.hours-closed-text {
  font-size: 13px;
  color: #999;
  font-style: italic;
}

.hours-footer {
  display: flex;
  gap: 12px;
  padding: 16px 24px 20px;
  border-top: 1px solid #f0f0f0;
}

.hours-cancel {
  flex: 1;
  padding: 12px;
  border: 2px solid #ddd;
  border-radius: 12px;
  background: white;
  color: #666;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  text-align: center;
}

.hours-save {
  flex: 1;
  padding: 12px;
  border: none;
  border-radius: 12px;
  background: #4caf50;
  color: white;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  text-align: center;
}

.hours-cancel:active {
  background: #f5f5f5;
}

.hours-save:active {
  background: #45a049;
}
</style>
