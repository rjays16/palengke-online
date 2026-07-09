<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/profile"></ion-back-button>
        </ion-buttons>
        <ion-title>All Settings</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="settings-content">
      <div class="settings-container">

        <!-- Account Section -->
        <div class="settings-section">
          <h3 class="settings-group-title">Account</h3>

          <div class="settings-item" @click="editProfile">
            <div class="item-left">
              <div class="item-icon green"><ion-icon :icon="personOutline"></ion-icon></div>
              <div class="item-info">
                <h4>{{ driverName }}</h4>
                <p>{{ driverId }}</p>
              </div>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="item-arrow"></ion-icon>
          </div>

          <div class="settings-item" @click="goToDocuments">
            <div class="item-left">
              <div class="item-icon blue"><ion-icon :icon="documentTextOutline"></ion-icon></div>
              <div class="item-info">
                <h4>Documents</h4>
                <p>View & update your documents</p>
              </div>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="item-arrow"></ion-icon>
          </div>

          <div class="settings-item" @click="linkedAccounts">
            <div class="item-left">
              <div class="item-icon purple"><ion-icon :icon="linkOutline"></ion-icon></div>
              <div class="item-info">
                <h4>Linked Accounts</h4>
                <p>Use third-party accounts to sign in</p>
              </div>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="item-arrow"></ion-icon>
          </div>

          <div class="settings-item" @click="communications">
            <div class="item-left">
              <div class="item-icon orange"><ion-icon :icon="notificationsOutline"></ion-icon></div>
              <div class="item-info">
                <h4>Communications</h4>
                <p>Notification preferences</p>
              </div>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="item-arrow"></ion-icon>
          </div>
        </div>

        <!-- Job Settings Section -->
        <div class="settings-section">
          <h3 class="settings-group-title">Job Settings</h3>

          <!-- Auto Accept Toggle -->
          <div class="settings-item toggle-item">
            <div class="item-left">
              <div class="item-icon green"><ion-icon :icon="flashOutline"></ion-icon></div>
              <div class="item-info">
                <h4>Auto Accept</h4>
                <p>{{ autoAccept ? 'Incoming jobs will be accepted automatically.' : 'Manually accept incoming jobs.' }}</p>
              </div>
            </div>
            <ion-toggle
              v-model="autoAccept"
              color="success"
              @ionChange="onAutoAcceptChange"
            ></ion-toggle>
          </div>

          <!-- Service Type -->
          <div class="settings-item" @click="goToServiceType">
            <div class="item-left">
              <div class="item-icon blue"><ion-icon :icon="bicycleOutline"></ion-icon></div>
              <div class="item-info">
                <h4>Service Type</h4>
                <p>Manage your delivery services</p>
              </div>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="item-arrow"></ion-icon>
          </div>

          <!-- Bulky Item Delivery Toggle -->
          <div class="settings-item toggle-item">
            <div class="item-left">
              <div class="item-icon orange"><ion-icon :icon="cubeOutline"></ion-icon></div>
              <div class="item-info">
                <h4>Bulky Item Delivery</h4>
                <p>{{ bulkyEnabled ? 'You can accept large or heavy orders' : 'Large or heavy orders will be filtered out' }}</p>
              </div>
            </div>
            <ion-toggle
              v-model="bulkyEnabled"
              color="success"
              @ionChange="onBulkyToggle"
            ></ion-toggle>
          </div>

          <!-- My Destination -->
          <div class="settings-item" @click="goToMyDestination">
            <div class="item-left">
              <div class="item-icon orange"><ion-icon :icon="locationOutline"></ion-icon></div>
              <div class="item-info">
                <h4>My Destination</h4>
                <p>Set your preferred destination</p>
              </div>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="item-arrow"></ion-icon>
          </div>

        </div>

        <!-- Support Section -->
        <div class="settings-section">
          <h3 class="settings-group-title">Support & Legal</h3>

          <div class="settings-item" @click="showTerms">
            <div class="item-left">
              <div class="item-icon blue"><ion-icon :icon="documentTextOutline"></ion-icon></div>
              <div class="item-info"><h4>Terms & Conditions</h4></div>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="item-arrow"></ion-icon>
          </div>

          <div class="settings-item" @click="showPrivacy">
            <div class="item-left">
              <div class="item-icon orange"><ion-icon :icon="shieldCheckmarkOutline"></ion-icon></div>
              <div class="item-info"><h4>Privacy Policy</h4></div>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="item-arrow"></ion-icon>
          </div>

          <div class="settings-item" @click="goToAbout">
            <div class="item-left">
              <div class="item-icon purple"><ion-icon :icon="informationCircleOutline"></ion-icon></div>
              <div class="item-info">
                <h4>About</h4>
                <p>Version 1.0.0 (Build 100)</p>
              </div>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="item-arrow"></ion-icon>
          </div>
        </div>

        <!-- Logout -->
        <div class="logout-section">
          <ion-button expand="block" color="danger" @click="handleLogout" class="logout-btn">
            <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
            Logout
          </ion-button>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonButton, IonIcon, IonToggle,
  alertController, actionSheetController, toastController
} from '@ionic/vue';
import {
  personOutline, documentTextOutline, linkOutline,
  notificationsOutline, flashOutline, bicycleOutline, cubeOutline,
  locationOutline,
  shieldCheckmarkOutline, informationCircleOutline,
  logOutOutline, chevronForwardOutline
} from 'ionicons/icons';

const router = useRouter();
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

const driverName = ref('Juan Dela Cruz');
const driverId = ref('DRV-2024-001234');
const autoAccept = ref(false);
const bulkyEnabled = ref(false);

function getToken() {
  return localStorage.getItem('token') || '';
}

onMounted(async () => {
  try {
    const res = await fetch(`${API_BASE}/api/driver/settings`, {
      headers: { Authorization: `Bearer ${getToken()}` },
    });
    if (res.ok) {
      const data = await res.json();
      autoAccept.value = data.auto_accept;
      bulkyEnabled.value = data.bulky_enabled;
    }
  } catch {}
});

async function saveSettings() {
  try {
    await fetch(`${API_BASE}/api/driver/settings`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${getToken()}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        auto_accept: autoAccept.value,
        bulky_enabled: bulkyEnabled.value,
      }),
    });
  } catch {}
}

const onAutoAcceptChange = async () => {
  await saveSettings();
  const toast = await toastController.create({
    message: autoAccept.value ? 'Auto Accept is ON' : 'Auto Accept is OFF',
    duration: 2000, color: 'medium', position: 'top'
  });
  await toast.present();
};

const onBulkyToggle = async () => {
  await saveSettings();
  const toast = await toastController.create({
    message: bulkyEnabled.value ? 'Bulky orders enabled' : 'Bulky orders disabled',
    duration: 2000, color: 'medium', position: 'top'
  });
  await toast.present();
};

const linkedAccounts = async () => {
  const alert = await alertController.create({
    header: 'Linked Accounts',
    message: 'Connect your Google or Facebook account for easier sign in.',
    buttons: ['Close']
  });
  await alert.present();
};

const communications = async () => {
  const alert = await alertController.create({
    header: 'Communications',
    message: 'Manage your notification and communication preferences.',
    buttons: ['Close']
  });
  await alert.present();
};

const showTerms = async () => {
  const alert = await alertController.create({
    header: 'Terms & Conditions',
    message: 'By using Palengke Online Driver, you agree to our terms of service.',
    buttons: ['Close']
  });
  await alert.present();
};

const showPrivacy = async () => {
  const alert = await alertController.create({
    header: 'Privacy Policy',
    message: 'We protect your personal data and will never share it without your consent.',
    buttons: ['Close']
  });
  await alert.present();
};

const handleLogout = async () => {
  const alert = await alertController.create({
    header: 'Logout',
    message: 'Are you sure you want to logout?',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Logout', role: 'destructive', handler: () => { localStorage.removeItem('isAuthenticated'); router.push('/login'); } }
    ]
  });
  await alert.present();
};

const editProfile = () => router.push('/profile');
const goToDocuments = () => router.push('/documents');
const goToServiceType = () => router.push('/service-type');
const goToMyDestination = () => router.push('/my-destination');
const goToAbout = () => router.push('/about');
</script>

<style scoped>
.settings-content { --background: #f8f9fa; }
.settings-container { padding: 16px; padding-bottom: 40px; }

.settings-section { background: white; border-radius: 16px; padding: 16px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

.settings-group-title { font-size: 13px; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; }

.settings-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0; cursor: pointer; }
.settings-item:last-child { border-bottom: none; padding-bottom: 0; }
.settings-item:first-of-type { padding-top: 0; }
.settings-item:active { opacity: 0.7; }

.toggle-item { cursor: default; }

.item-left { display: flex; align-items: center; gap: 14px; flex: 1; }

.item-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.item-icon.green { background: #E8F5E9; color: #4CAF50; }
.item-icon.blue { background: #E3F2FD; color: #2196F3; }
.item-icon.orange { background: #FFF3E0; color: #FF9800; }
.item-icon.purple { background: #F3E5F5; color: #9C27B0; }

.item-info h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.item-info p { font-size: 12px; color: #666; margin: 0; line-height: 1.4; }

.item-arrow { font-size: 20px; color: #ccc; flex-shrink: 0; }

.logout-section { padding-top: 8px; }
.logout-btn { --border-radius: 12px; height: 48px; font-weight: 600; text-transform: none; }
</style>