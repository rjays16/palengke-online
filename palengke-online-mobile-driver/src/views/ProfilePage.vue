<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>My Profile</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="editProfile">
            <ion-icon :icon="createOutline"></ion-icon>
          </ion-button>
          <ion-button @click="showHelp">
            <ion-icon :icon="helpCircleOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="profile-content">

      <!-- Profile Header -->
      <div class="profile-header">
        <div class="avatar-container">
          <div class="avatar">
            <img v-if="profileData.avatar" :src="profileData.avatar" alt="Profile">
            <ion-icon v-else :icon="personOutline"></ion-icon>
          </div>
          <ion-button class="change-photo-btn" fill="clear" size="small" @click="changePhoto">
            <ion-icon :icon="cameraOutline"></ion-icon>
          </ion-button>
        </div>
        <h2 class="profile-name">{{ profileData.name }}</h2>
        <p class="profile-type">{{ profileData.vehicleType }} Driver</p>
        <div class="profile-stats">
          <div class="stat-item">
            <div class="stat-value">{{ profileData.totalTrips }}</div>
            <div class="stat-label">Trips</div>
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

      <!-- Driving Safety Report -->
      <div class="safety-card" @click="showSafetyReport">
        <div class="safety-left">
          <ion-icon :icon="shieldCheckmarkOutline" class="safety-icon"></ion-icon>
          <div>
            <h3>Driving Safety Report</h3>
            <p>Take a look at your overview</p>
          </div>
        </div>
        <ion-icon :icon="chevronForwardOutline"></ion-icon>
      </div>

      <!-- Rating Breakdown -->
      <div class="section">
        <div class="section-header-row">
          <h3 class="section-title">Last 100 Rated Jobs</h3>
          <span class="updated-text">Updated 2 Hours ago</span>
        </div>

        <div class="rating-breakdown">
          <div class="rating-big">
            <span class="rating-number">{{ profileData.rating }}</span>
            <span class="rating-label">Rating</span>
          </div>
          <div class="rating-bars">
            <div class="rating-bar-row" v-for="star in [5,4,3,2,1]" :key="star">
              <span class="star-num">{{ star }}</span>
              <div class="bar-track">
                <div
                  class="bar-fill"
                  :style="{ width: getRatingWidth(star) + '%' }"
                  :class="{ highlight: star === 5 }"
                ></div>
              </div>
              <span class="bar-count">{{ getRatingCount(star) }}</span>
            </div>
          </div>
        </div>

        <div class="avg-notice">
          <ion-icon :icon="happyOutline"></ion-icon>
          <span>The average rating is {{ cityAvgRating }} for drivers in your city.</span>
        </div>
      </div>

      <!-- Achievements / Improvements Tabs -->
      <div class="section">
        <div class="tab-row">
          <div
            class="tab-btn"
            :class="{ active: activeTab === 'achievements' }"
            @click="activeTab = 'achievements'"
          >
            Achievements
          </div>
          <div
            class="tab-btn"
            :class="{ active: activeTab === 'improvements' }"
            @click="activeTab = 'improvements'"
          >
            Improvements
          </div>
        </div>

        <!-- Achievements -->
        <div v-if="activeTab === 'achievements'">
          <p class="badges-label">Compliment Badges</p>
          <div class="badges-grid">
            <div class="badge-item" v-for="badge in badges" :key="badge.id" @click="showBadgeInfo(badge)">
              <div class="badge-icon-wrap" :style="{ background: badge.color }">
                <ion-icon :icon="badge.icon"></ion-icon>
              </div>
              <div class="badge-new" v-if="badge.isNew">{{ badge.count }} New</div>
              <span class="badge-name">{{ badge.name }}</span>
            </div>
          </div>
        </div>

        <!-- Improvements -->
        <div v-if="activeTab === 'improvements'">
          <div class="improvement-item" v-for="item in improvements" :key="item.id">
            <div class="improvement-icon" :class="item.type">
              <ion-icon :icon="item.icon"></ion-icon>
            </div>
            <div class="improvement-info">
              <h4>{{ item.title }}</h4>
              <p>{{ item.description }}</p>
            </div>
            <div class="improvement-score" :class="item.type">{{ item.score }}</div>
          </div>
        </div>
      </div>

      <!-- Personal Information -->
      <div class="section">
        <h3 class="section-title">Personal Information</h3>
        <div class="info-list">
          <div class="info-item" @click="editField('name')">
            <div class="info-icon"><ion-icon :icon="personOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Full Name</p>
              <p class="info-value">{{ profileData.name }}</p>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
          </div>
          <div class="info-item" @click="editField('phone')">
            <div class="info-icon"><ion-icon :icon="callOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Phone Number</p>
              <p class="info-value">{{ profileData.phone }}</p>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
          </div>
          <div class="info-item" @click="editField('email')">
            <div class="info-icon"><ion-icon :icon="mailOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Email Address</p>
              <p class="info-value">{{ profileData.email }}</p>
            </div>
            <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
          </div>
        </div>
      </div>

      <!-- Vehicle Information -->
      <div class="section">
        <h3 class="section-title">Vehicle Information</h3>
        <div class="info-list">
          <div class="info-item">
            <div class="info-icon"><ion-icon :icon="bicycleOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Vehicle Type</p>
              <p class="info-value">{{ profileData.vehicleType }}</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon"><ion-icon :icon="cardOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Plate Number</p>
              <p class="info-value">{{ profileData.plateNumber }}</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon"><ion-icon :icon="colorPaletteOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Vehicle Model & Color</p>
              <p class="info-value">{{ profileData.vehicleModel }} · {{ profileData.vehicleColor }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Documents Status -->
      <div class="section" @click="goToDocuments">
        <div class="section-header-row">
          <h3 class="section-title">Documents</h3>
          <span class="view-all-link">View All</span>
        </div>
        <div class="docs-preview">
          <div class="doc-preview-item" v-for="doc in documentsPreview" :key="doc.id">
            <ion-icon :icon="checkmarkCircleOutline" class="doc-check" :class="doc.status"></ion-icon>
            <span>{{ doc.name }}</span>
          </div>
        </div>
      </div>

      <!-- Account Settings -->
      <div class="section">
        <h3 class="section-title">Account Settings</h3>
        <div class="settings-list">
          <div class="settings-item" @click="goToSettings">
            <div class="settings-icon"><ion-icon :icon="settingsOutline"></ion-icon></div>
            <div class="settings-content"><p>All Settings</p></div>
            <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
          </div>
          <div class="settings-item" @click="goToHelp">
            <div class="settings-icon"><ion-icon :icon="helpCircleOutline"></ion-icon></div>
            <div class="settings-content"><p>Help & Support</p></div>
            <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
          </div>
        </div>
      </div>

      <!-- Logout -->
      <div class="logout-section">
        <ion-button expand="block" color="danger" @click="handleLogout" class="logout-btn">
          <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
          Logout
        </ion-button>
      </div>

      <div class="app-version">
        <p>Palengke Online Driver</p>
        <p>Version 1.0.0 (Build 100)</p>
      </div>

    </ion-content>

    <!-- Bottom Navigation -->
    <ion-footer>
      <ion-toolbar>
        <div class="bottom-nav">
          <div class="nav-item" @click="goToHome">
            <ion-icon :icon="homeOutline"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item" @click="goToOrders">
            <ion-icon :icon="cubeOutline"></ion-icon>
            <span>Orders</span>
          </div>
          <div class="nav-item" @click="goToEarnings">
            <ion-icon :icon="cashOutline"></ion-icon>
            <span>Earnings</span>
          </div>
          <div class="nav-item active">
            <ion-icon :icon="personOutline"></ion-icon>
            <span>Profile</span>
          </div>
        </div>
      </ion-toolbar>
    </ion-footer>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonFooter, IonButton, IonButtons, IonIcon,
  alertController, actionSheetController, toastController
} from '@ionic/vue';
import {
  createOutline, helpCircleOutline, personOutline,
  cameraOutline, shieldCheckmarkOutline, chevronForwardOutline,
  happyOutline, callOutline, mailOutline, bicycleOutline,
  cardOutline, colorPaletteOutline, checkmarkCircleOutline,
  settingsOutline, logOutOutline,
  homeOutline, cubeOutline, cashOutline, trophyOutline,
  starOutline, flashOutline, thumbsUpOutline, heartOutline,
  timeOutline, closeCircleOutline, warningOutline
} from 'ionicons/icons';
import {
  mockProfileData, mockRatingCounts, mockBadges,
  mockImprovements, mockDocumentsPreview
} from '@/mock/profile';

const router = useRouter();
const activeTab = ref('achievements');
const cityAvgRating = ref(4.70);

const profileData = ref(mockProfileData);

// Rating distribution
const ratingCounts = mockRatingCounts;
const getRatingCount = (star) => ratingCounts[star] || 0;
const getRatingWidth = (star) => {
  const total = Object.values(ratingCounts).reduce((a, b) => a + b, 0);
  return total > 0 ? (ratingCounts[star] / total) * 100 : 0;
};

const badges = ref(mockBadges);
const improvements = ref(mockImprovements);
const documentsPreview = ref(mockDocumentsPreview);

// Methods
const editProfile = async () => {
  const alert = await alertController.create({
    header: 'Edit Profile',
    buttons: [
      {
        text: 'Personal Info',
        handler: () => editField('name')
      },
      {
        text: 'Vehicle Info',
        handler: () => editVehicleInfo()
      },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await alert.present();
};

const editVehicleInfo = async () => {
  const alert = await alertController.create({
    header: 'Edit Vehicle Info',
    inputs: [
      { name: 'vehicleType', type: 'text', value: profileData.value.vehicleType, placeholder: 'Vehicle Type' },
      { name: 'vehicleModel', type: 'text', value: profileData.value.vehicleModel, placeholder: 'Vehicle Model' },
      { name: 'plateNumber', type: 'text', value: profileData.value.plateNumber, placeholder: 'Plate Number' },
      { name: 'vehicleColor', type: 'text', value: profileData.value.vehicleColor, placeholder: 'Vehicle Color' }
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Save',
        handler: async (data) => {
          if (data.vehicleType) profileData.value.vehicleType = data.vehicleType;
          if (data.vehicleModel) profileData.value.vehicleModel = data.vehicleModel;
          if (data.plateNumber) profileData.value.plateNumber = data.plateNumber;
          if (data.vehicleColor) profileData.value.vehicleColor = data.vehicleColor;
          await showSuccessModal('Vehicle info updated!');
        }
      }
    ]
  });
  await alert.present();
};

const changePhoto = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Change Profile Photo',
    buttons: [
      {
        text: 'Use Default Photo',
        icon: personOutline,
        handler: () => {
          profileData.value.avatar = 'https://randomuser.me/api/portraits/men/32.jpg';
        }
      },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const showSafetyReport = async () => {
  const alert = await alertController.create({
    header: 'Driving Safety Report',
    message: 'Your driving safety score is excellent! Keep up the good work.',
    buttons: ['Close']
  });
  await alert.present();
};

const showBadgeInfo = async (badge) => {
  const alert = await alertController.create({
    header: badge.name,
    message: `You have earned the ${badge.name} badge! Keep up the great work.`,
    buttons: ['Close']
  });
  await alert.present();
};

const editField = async (field) => {
  const fieldNames = { name: 'Full Name', phone: 'Phone Number', email: 'Email Address' };
  const alert = await alertController.create({
    header: `Edit ${fieldNames[field]}`,
    inputs: [{ name: 'value', type: field === 'email' ? 'email' : field === 'phone' ? 'tel' : 'text', value: profileData.value[field] }],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Save',
        handler: async (data) => {
          if (data.value) {
            profileData.value[field] = data.value;
            await showSuccessModal('Updated successfully!');
          }
        }
      }
    ]
  });
  await alert.present();
};

const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  await alert.onDidDismiss();
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

const showHelp = () => router.push('/help');

// Navigation
const goToHome = () => router.push('/home');
const goToOrders = () => router.push('/orders');
const goToEarnings = () => router.push('/earnings');
const goToDocuments = () => router.push('/documents');
const goToSettings = () => router.push('/settings');
const goToHelp = () => router.push('/help');
</script>

<style scoped>
.profile-content { --background: #f8f9fa; }

/* Profile Header */
.profile-header {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  padding: 32px 20px 28px; text-align: center; color: white;
}

.avatar-container { position: relative; display: inline-block; margin-bottom: 14px; }

.avatar {
  width: 90px; height: 90px; border-radius: 50%;
  background: rgba(255,255,255,0.2); border: 3px solid white;
  display: flex; align-items: center; justify-content: center;
  font-size: 44px; overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.avatar img { width: 100%; height: 100%; object-fit: cover; }

.change-photo-btn {
  position: absolute; bottom: 0; right: 0;
  --background: white; --color: #4CAF50;
  --border-radius: 50%; width: 32px; height: 32px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.profile-name { font-size: 22px; font-weight: 700; margin: 0 0 4px 0; }
.profile-type { font-size: 14px; margin: 0 0 20px 0; opacity: 0.9; }

.profile-stats { display: flex; justify-content: center; align-items: center; gap: 20px; }
.stat-item { text-align: center; }
.stat-value { font-size: 22px; font-weight: 800; }
.stat-label { font-size: 12px; opacity: 0.85; }
.stat-divider { width: 1px; height: 36px; background: rgba(255,255,255,0.3); }

/* Safety Card */
.safety-card {
  background: white; margin: 16px 16px 0;
  border-radius: 16px; padding: 16px 20px;
  display: flex; align-items: center; justify-content: space-between;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06); cursor: pointer;
}
.safety-card:active { transform: scale(0.98); }
.safety-left { display: flex; align-items: center; gap: 14px; }
.safety-icon { font-size: 28px; color: #4CAF50; }
.safety-left h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.safety-left p { font-size: 13px; color: #666; margin: 0; }
.safety-card > ion-icon { font-size: 20px; color: #ccc; }

/* Section */
.section {
  background: white; margin: 12px 16px 0;
  border-radius: 16px; padding: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.section-header-row {
  display: flex; justify-content: space-between;
  align-items: center; margin-bottom: 16px;
}

.section-title { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }
.updated-text { font-size: 12px; color: #999; }
.view-all-link { font-size: 13px; color: #4CAF50; font-weight: 600; cursor: pointer; }

/* Rating Breakdown */
.rating-breakdown { display: flex; gap: 20px; align-items: center; margin-bottom: 14px; }

.rating-big { display: flex; flex-direction: column; align-items: center; gap: 4px; flex-shrink: 0; }
.rating-number { font-size: 40px; font-weight: 900; color: #1a1a1a; line-height: 1; }
.rating-label { font-size: 12px; color: #666; font-weight: 600; }

.rating-bars { flex: 1; display: flex; flex-direction: column; gap: 6px; }

.rating-bar-row { display: flex; align-items: center; gap: 8px; }
.star-num { font-size: 13px; color: #666; width: 10px; text-align: right; flex-shrink: 0; }
.bar-track { flex: 1; height: 8px; background: #f0f0f0; border-radius: 4px; overflow: hidden; }
.bar-fill { height: 100%; background: #e0e0e0; border-radius: 4px; transition: width 0.5s; }
.bar-fill.highlight { background: #FFD700; }
.bar-count { font-size: 12px; color: #666; width: 28px; text-align: right; flex-shrink: 0; }

.avg-notice { display: flex; align-items: center; gap: 8px; background: #f8f9fa; border-radius: 10px; padding: 10px 12px; }
.avg-notice ion-icon { font-size: 20px; color: #4CAF50; flex-shrink: 0; }
.avg-notice span { font-size: 13px; color: #555; line-height: 1.4; }

/* Tabs */
.tab-row { display: flex; border-bottom: 2px solid #f0f0f0; margin-bottom: 16px; }
.tab-btn {
  flex: 1; text-align: center; padding: 12px;
  font-size: 14px; font-weight: 700; color: #999;
  cursor: pointer; border-bottom: 3px solid transparent;
  margin-bottom: -2px; transition: all 0.2s;
}
.tab-btn.active { color: #4CAF50; border-bottom-color: #4CAF50; }

/* Badges */
.badges-label { font-size: 13px; color: #999; font-weight: 600; margin: 0 0 12px 0; text-transform: uppercase; }
.badges-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }

.badge-item {
  display: flex; flex-direction: column;
  align-items: center; gap: 6px;
  position: relative; cursor: pointer;
}

.badge-icon-wrap {
  width: 56px; height: 56px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 26px; color: white;
}

.badge-new {
  position: absolute; top: -4px; right: -4px;
  background: #f44336; color: white;
  border-radius: 10px; padding: 2px 6px;
  font-size: 9px; font-weight: 700;
}

.badge-name { font-size: 10px; color: #333; font-weight: 600; text-align: center; line-height: 1.2; }

/* Improvements */
.improvement-item {
  display: flex; align-items: center; gap: 14px;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}
.improvement-item:last-child { border-bottom: none; }

.improvement-icon {
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; flex-shrink: 0;
}
.improvement-icon.good { background: #E8F5E9; color: #4CAF50; }
.improvement-icon.great { background: #E3F2FD; color: #2196F3; }
.improvement-icon.warning { background: #FFF3E0; color: #FF9800; }

.improvement-info { flex: 1; }
.improvement-info h4 { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0 0 2px 0; }
.improvement-info p { font-size: 12px; color: #666; margin: 0; }

.improvement-score { font-size: 16px; font-weight: 800; }
.improvement-score.good { color: #4CAF50; }
.improvement-score.great { color: #2196F3; }
.improvement-score.warning { color: #FF9800; }

/* Info List */
.info-list { display: flex; flex-direction: column; gap: 12px; }

.info-item {
  display: flex; align-items: center; gap: 12px;
  cursor: pointer; padding: 10px;
  margin: -10px; border-radius: 12px; transition: background 0.2s;
}
.info-item:active { background: #f8f9fa; }

.info-icon {
  width: 40px; height: 40px; border-radius: 12px;
  background: #E8F5E9; color: #4CAF50;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; flex-shrink: 0;
}

.info-content { flex: 1; }
.info-label { font-size: 12px; color: #999; margin: 0 0 2px 0; }
.info-value { font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 0; }
.info-arrow { font-size: 18px; color: #ccc; }

/* Documents Preview */
.docs-preview { display: flex; flex-direction: column; gap: 8px; }
.doc-preview-item { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #555; }
.doc-check { font-size: 20px; }
.doc-check.verified { color: #4CAF50; }
.doc-check.pending { color: #FF9800; }

/* Settings List */
.settings-list { display: flex; flex-direction: column; gap: 4px; }
.settings-item {
  display: flex; align-items: center; gap: 12px;
  cursor: pointer; padding: 12px;
  margin: 0 -12px; border-radius: 12px; transition: background 0.2s;
}
.settings-item:active { background: #f8f9fa; }
.settings-icon {
  width: 40px; height: 40px; border-radius: 12px;
  background: #f0f0f0; color: #666;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; flex-shrink: 0;
}
.settings-content { flex: 1; }
.settings-content p { font-size: 15px; font-weight: 600; color: #1a1a1a; margin: 0; }
.settings-arrow { font-size: 18px; color: #ccc; }

/* Logout */
.logout-section { padding: 16px; }
.logout-btn { --border-radius: 12px; height: 48px; font-weight: 600; text-transform: none; }

.app-version { text-align: center; padding: 0 0 24px; }
.app-version p { font-size: 12px; color: #999; margin: 4px 0; }

/* Bottom Nav */
.bottom-nav { display: grid; grid-template-columns: repeat(4, 1fr); height: 60px; }
.nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; cursor: pointer; color: #999; }
.nav-item ion-icon { font-size: 24px; }
.nav-item span { font-size: 11px; font-weight: 500; }
.nav-item.active { color: #4CAF50; }
.nav-item:active { transform: scale(0.95); }
</style>