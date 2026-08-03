<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>My Profile</ion-title>
        <ion-buttons slot="end">
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
          <h3 class="section-title">Last {{ totalRatings }} Rated Jobs</h3>
          <span class="updated-text">{{ updatedText }}</span>
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
          <div class="info-item">
            <div class="info-icon"><ion-icon :icon="personOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Full Name</p>
              <p class="info-value">{{ profileData.name }}</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon"><ion-icon :icon="callOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Phone Number</p>
              <p class="info-value">{{ profileData.phone }}</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon"><ion-icon :icon="mailOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Email Address</p>
              <p class="info-value">{{ profileData.email }}</p>
            </div>
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
              <p class="info-label">Vehicle Model</p>
              <p class="info-value">{{ profileData.vehicleModel }}</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon"><ion-icon :icon="colorPaletteOutline"></ion-icon></div>
            <div class="info-content">
              <p class="info-label">Vehicle Color</p>
              <p class="info-value">{{ profileData.vehicleColor }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Palengke Licence -->
      <div class="section" v-if="licenceStore.showLicenceFeature.value">
        <h3 class="section-title">Palengke Online Licence</h3>
        <div class="licence-card" :style="{ borderColor: licenceStatusColor }">
          <div class="licence-header">
            <ion-icon :icon="licenceStatusIcon" class="licence-icon" :style="{ color: licenceStatusColor }"></ion-icon>
            <div class="licence-info">
              <p class="licence-id">{{ licenceStore.getLicenceDisplay() }}</p>
              <p class="licence-status" :style="{ color: licenceStatusColor }">{{ licenceStore.getStatusText() }}</p>
            </div>
          </div>
          <div class="licence-details">
            <div class="licence-detail-item" v-if="!licenceStore.state.isFree">
              <span class="licence-detail-label">Days Active</span>
              <span class="licence-detail-value">{{ licenceStore.getDaysUsed() }} / 30</span>
            </div>
            <div class="licence-detail-item" v-if="!licenceStore.state.isFree">
              <span class="licence-detail-label">Amount</span>
              <span class="licence-detail-value">₱{{ licenceStore.state.amount.toLocaleString() }}</span>
            </div>
          </div>
          <div class="licence-bar-track" v-if="!licenceStore.state.isFree">
            <div class="licence-bar-fill" :style="{ width: licenceBarPercent + '%', background: licenceStatusColor }"></div>
          </div>
          <div class="licence-footer-text">
            <span v-if="licenceStore.state.isFree">Licence is free — no payment needed</span>
            <span v-else-if="licenceStore.state.isPaid">Licence is active until further notice</span>
            <span v-else-if="licenceStore.trialExpired.value">Trial ended — pay ₱{{ licenceStore.state.amount.toLocaleString() }} to continue delivering</span>
            <span v-else>{{ licenceStore.trialDaysRemaining.value }} days remaining in trial</span>
          </div>
          <ion-button
            v-if="!licenceStore.state.isFree && licenceStore.trialExpired.value && !licenceStore.state.isPaid"
            expand="block"
            color="success"
            class="licence-pay-btn"
            @click="goToLicencePayment"
          >
            Pay ₱{{ licenceStore.state.amount.toLocaleString() }} to Continue
          </ion-button>
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
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonFooter, IonButton, IonButtons, IonIcon,
  alertController
} from '@ionic/vue';
import {
  helpCircleOutline, personOutline,
  shieldCheckmarkOutline, chevronForwardOutline,
  happyOutline, callOutline, mailOutline, bicycleOutline,
  cardOutline, colorPaletteOutline, checkmarkCircleOutline,
  settingsOutline, logOutOutline,
  homeOutline, cubeOutline, cashOutline, trophyOutline,
  starOutline, flashOutline, thumbsUpOutline, heartOutline,
  timeOutline, closeCircleOutline, warningOutline
} from 'ionicons/icons';
import { useLicenceStore } from '@/composables/useLicenceStore';
import {
  checkmarkCircleOutline as checkmarkIcon,
  timeOutline as timeIcon,
  closeCircleOutline as closeIcon,
  cardOutline as cardIcon,
} from 'ionicons/icons';

const router = useRouter();
const activeTab = ref('achievements');
const cityAvgRating = ref(0);
const ratingUpdatedAt = ref(null);

const licenceStore = useLicenceStore();
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('token');
}

const profileData = ref({
  name: '',
  phone: '',
  email: '',
  vehicleType: '',
  plateNumber: '',
  vehicleModel: '',
  vehicleColor: '',
  avatar: '',
  totalTrips: 0,
  rating: 0,
  joinedDays: 0,
});

async function fetchProfile() {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/profile`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      profileData.value = { ...profileData.value, ...data };
      if (data.cityAvgRating !== undefined) cityAvgRating.value = data.cityAvgRating;
      if (data.ratingUpdatedAt) ratingUpdatedAt.value = data.ratingUpdatedAt;
      if (data.ratingCounts) {
        ratingCounts.value = data.ratingCounts;
      }
    }
  } catch {}
}

onMounted(async () => {
  await Promise.all([
    fetchProfile(),
    fetchBadges(),
    fetchPerformance(),
    licenceStore.checkLicenceEnabled(),
    licenceStore.fetchLicence(),
  ]);
});

const licenceStatusIcon = computed(() => {
  if (licenceStore.state.isPaid) return checkmarkIcon
  if (licenceStore.trialExpired.value) return closeIcon
  return timeIcon
})

const licenceStatusColor = computed(() => {
  return licenceStore.getStatusColor()
})

const licenceBarPercent = computed(() => {
  const used = licenceStore.getDaysUsed()
  return Math.min(100, (used / 30) * 100)
})

// Rating distribution
const ratingCounts = ref({ 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 });
const totalRatings = computed(() => {
  const counts = ratingCounts.value || {};
  return [5, 4, 3, 2, 1].reduce((sum, star) => sum + (counts[star] || 0), 0);
});
const getRatingCount = (star) => ratingCounts.value[star] || 0;
const getRatingWidth = (star) => {
  const total = totalRatings.value;
  return total > 0 ? Math.round((getRatingCount(star) / total) * 100) : 0;
};
const updatedText = computed(() => {
  if (!ratingUpdatedAt.value) return 'No ratings yet';
  const diff = Date.now() - new Date(ratingUpdatedAt.value).getTime();
  const minutes = Math.floor(diff / 60000);
  if (minutes < 1) return 'Updated just now';
  if (minutes < 60) return `Updated ${minutes} min ago`;
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `Updated ${hours} hour${hours > 1 ? 's' : ''} ago`;
  const days = Math.floor(hours / 24);
  if (days < 30) return `Updated ${days} day${days > 1 ? 's' : ''} ago`;
  const months = Math.floor(days / 30);
  return `Updated ${months} month${months > 1 ? 's' : ''} ago`;
});

const iconMap = {
  trophyOutline,
  flashOutline,
  starOutline,
  heartOutline,
  checkmarkCircleOutline,
  closeCircleOutline,
  cubeOutline,
  timeOutline,
  warningOutline,
  thumbsUpOutline,
};

const badges = ref([]);
const improvements = ref([]);
const documentsPreview = ref([]);

async function fetchBadges() {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/badges`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      badges.value = (data.badges || []).filter((b) => b.earned).map((b) => ({
        id: b.id,
        name: b.name,
        icon: iconMap[b.icon] || helpCircleOutline,
        color: b.color,
        isNew: b.isNew,
        count: b.isNew ? 1 : 0,
      }));
    }
  } catch {}
}

async function fetchPerformance() {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${API_BASE}/api/driver/performance`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      improvements.value = (data.improvements || []).map((item) => ({
        ...item,
        icon: iconMap[item.icon] || helpCircleOutline,
      }));
    }
  } catch {}
}

// Methods
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

const handleLogout = async () => {
  const alert = await alertController.create({
    header: 'Logout',
    message: 'Are you sure you want to logout?',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Logout', role: 'destructive', handler: () => { localStorage.removeItem('isAuthenticated'); localStorage.removeItem('token'); localStorage.removeItem('pendingAuth'); router.push('/login'); } }
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
const goToLicencePayment = () => router.push('/licence-payment');
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
/* Palengke Licence */
.licence-card {
  background: #f8f9fa; border-radius: 14px;
  padding: 16px; border: 2px solid #4CAF50;
  margin-top: 8px;
}

.licence-header {
  display: flex; align-items: center; gap: 14px;
  margin-bottom: 14px;
}

.licence-icon { font-size: 32px; }

.licence-info { flex: 1; }
.licence-id { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 2px 0; font-family: monospace; letter-spacing: 0.5px; }
.licence-status { font-size: 13px; font-weight: 600; margin: 0; }

.licence-details {
  display: flex; gap: 16px; margin-bottom: 12px;
}

.licence-detail-item {
  flex: 1; display: flex; flex-direction: column; gap: 2px;
}

.licence-detail-label { font-size: 11px; color: #999; text-transform: uppercase; font-weight: 600; }
.licence-detail-value { font-size: 14px; font-weight: 700; color: #1a1a1a; }

.licence-bar-track {
  width: 100%; height: 6px; background: #e0e0e0;
  border-radius: 3px; overflow: hidden; margin-bottom: 8px;
}

.licence-bar-fill {
  height: 100%; border-radius: 3px; transition: width 0.5s;
}

.licence-footer-text { font-size: 12px; color: #666; margin-bottom: 8px; }

.licence-pay-btn {
  --border-radius: 10px; height: 40px;
  font-size: 14px; font-weight: 700; text-transform: none;
  margin-top: 4px;
}

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