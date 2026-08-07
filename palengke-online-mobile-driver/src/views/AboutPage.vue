<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/settings"></ion-back-button>
        </ion-buttons>
        <ion-title>About</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="about-content">
      <div class="about-container">

        <!-- App Hero -->
        <div class="app-hero">
          <div class="app-logo">
            <img src="/assets/images/logo.png" alt="Palengke Online">
          </div>
          <h1 class="app-name">Palengke Online</h1>
          <p class="app-tagline">Driver Partner App</p>
          <div class="version-badge">Version {{ appVersion }}</div>
        </div>

        <!-- App Description -->
        <div class="about-card">
          <h3 class="card-title">About the App</h3>
          <p class="about-text">Palengke Online Driver is your partner app for delivering fresh market goods from local sellers to customers. Earn daily, work flexible hours, and help bring the palengke experience to every doorstep.</p>
        </div>

        <!-- Stats -->
        <div class="stats-card">
          <div class="app-stat">
            <div class="app-stat-icon green"><ion-icon :icon="bicycleOutline"></ion-icon></div>
            <div>
              <h4>{{ formatNumber(stats.activeDrivers) }}</h4>
              <p>Active Drivers</p>
            </div>
          </div>
          <div class="stat-divider"></div>
          <div class="app-stat">
            <div class="app-stat-icon blue"><ion-icon :icon="cubeOutline"></ion-icon></div>
            <div>
              <h4>{{ formatNumber(stats.deliveriesDone) }}</h4>
              <p>Deliveries Done</p>
            </div>
          </div>
          <div class="stat-divider"></div>
          <div class="app-stat">
            <div class="app-stat-icon orange"><ion-icon :icon="storefrontOutline"></ion-icon></div>
            <div>
              <h4>{{ formatNumber(stats.activeSellers) }}</h4>
              <p>Sellers Served</p>
            </div>
          </div>
        </div>

        <!-- Company Info -->
        <div class="about-card">
          <h3 class="card-title">Company</h3>
          <div class="info-row">
            <ion-icon :icon="businessOutline"></ion-icon>
            <span>Palengke Online Inc.</span>
          </div>
          <div class="info-row">
            <ion-icon :icon="locationOutline"></ion-icon>
            <span>Cagayan de Oro City, Philippines</span>
          </div>
          <div class="info-row">
            <ion-icon :icon="mailOutline"></ion-icon>
            <span>support@palengkeonline.com</span>
          </div>
          <div class="info-row">
            <ion-icon :icon="globeOutline"></ion-icon>
            <span>www.palengkeonline.com</span>
          </div>
        </div>

        <!-- Follow Us -->
        <div class="about-card">
          <h3 class="card-title">Follow Us</h3>
          <div class="social-buttons">
            <ion-button expand="block" fill="outline" class="social-btn facebook" @click="openSocial('facebook')">
              <ion-icon :icon="logoFacebook" slot="start"></ion-icon>
              Facebook
            </ion-button>
            <ion-button expand="block" fill="outline" class="social-btn instagram" @click="openSocial('instagram')">
              <ion-icon :icon="logoInstagram" slot="start"></ion-icon>
              Instagram
            </ion-button>
          </div>
        </div>

        <!-- Legal -->
        <div class="legal-links">
          <span @click="openLegal('terms')">Terms & Conditions</span>
          <span class="dot">•</span>
          <span @click="openLegal('privacy')">Privacy Policy</span>
          <span class="dot">•</span>
          <span @click="openLegal('licenses')">Licenses</span>
        </div>

        <p class="copyright">© {{ copyrightYear }} Palengke Online Inc. All rights reserved.</p>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { App } from '@capacitor/app';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonButton, IonIcon,
  alertController
} from '@ionic/vue';
import {
  bicycleOutline, cubeOutline, storefrontOutline,
  businessOutline, locationOutline, mailOutline,
  globeOutline, logoFacebook, logoInstagram
} from 'ionicons/icons';

import { API_BASE } from '@/services/api';

const stats = ref({ activeDrivers: 0, deliveriesDone: 0, activeSellers: 0 });
const statsLoaded = ref(false);

const appVersion = ref('1.0.0 (Build 100)');
const copyrightYear = new Date().getFullYear();

const formatNumber = (value) => {
  if (!statsLoaded.value) return '--';
  const num = Number(value) || 0;
  return num.toLocaleString() + '+';
};

onMounted(async () => {
  try {
    const info = await App.getInfo();
    if (info.version) {
      appVersion.value = info.build ? `${info.version} (Build ${info.build})` : info.version;
    }
  } catch (err) {
    console.error('Failed to read app version:', err);
  }

  try {
    const res = await fetch(`${API_BASE}/api/app-stats`);
    if (res.ok) {
      const data = await res.json();
      stats.value = {
        activeDrivers: data.active_drivers ?? 0,
        deliveriesDone: data.deliveries_done ?? 0,
        activeSellers: data.active_sellers ?? 0,
      };
    }
  } catch (err) {
    console.error('Failed to load app stats:', err);
  } finally {
    statsLoaded.value = true;
  }
});

const openSocial = (platform) => {
  const urls = {
    facebook: 'https://www.facebook.com',
    instagram: 'https://www.instagram.com'
  };
  window.open(urls[platform], '_blank');
};

const openLegal = async (type) => {
  const titles = { terms: 'Terms & Conditions', privacy: 'Privacy Policy', licenses: 'Open Source Licenses' };
  const alert = await alertController.create({
    header: titles[type],
    message: `Opening ${titles[type]}...`,
    buttons: ['Close']
  });
  await alert.present();
};
</script>

<style scoped>
.about-content { --background: #f8f9fa; }
.about-container { padding: 16px; padding-bottom: 40px; }

/* App Hero */
.app-hero {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  border-radius: 20px; padding: 32px 20px;
  text-align: center; color: white; margin-bottom: 16px;
  box-shadow: 0 4px 20px rgba(76,175,80,0.3);
}

.app-logo { width: 80px; height: 80px; border-radius: 20px; overflow: hidden; margin: 0 auto 14px; background: white; padding: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.app-logo img { width: 100%; height: 100%; object-fit: contain; }

.app-name { font-size: 24px; font-weight: 800; margin: 0 0 4px 0; }
.app-tagline { font-size: 14px; margin: 0 0 14px 0; opacity: 0.9; }

.version-badge {
  display: inline-block; background: rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.4);
  padding: 6px 14px; border-radius: 20px;
  font-size: 12px; font-weight: 600;
}

/* About Card */
.about-card {
  background: white; border-radius: 16px;
  padding: 18px; margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.card-title { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 12px 0; }
.about-text { font-size: 13px; color: #555; line-height: 1.6; margin: 0; }

/* Stats Card */
.stats-card {
  background: white; border-radius: 16px;
  padding: 20px; margin-bottom: 16px;
  display: flex; justify-content: space-around; align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  gap: 12px;
}

.app-stat { display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; flex: 1; }
.app-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.app-stat-icon.green { background: #E8F5E9; color: #4CAF50; }
.app-stat-icon.blue { background: #E3F2FD; color: #2196F3; }
.app-stat-icon.orange { background: #FFF3E0; color: #FF9800; }
.app-stat h4 { font-size: 16px; font-weight: 800; color: #1a1a1a; margin: 0; }
.app-stat p { font-size: 10px; color: #666; margin: 0; }
.stat-divider { width: 1px; height: 40px; background: #f0f0f0; }

@media (max-width: 350px) {
  .stats-card { flex-direction: column; gap: 16px; }
  .stat-divider { width: 80%; height: 1px; }
  .app-stat { flex-direction: row; text-align: left; gap: 10px; }
}

/* Info Row */
.info-row { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
.info-row:last-child { border-bottom: none; }
.info-row ion-icon { font-size: 18px; color: #4CAF50; flex-shrink: 0; margin-top: 2px; }
.info-row span { font-size: 13px; color: #555; word-break: break-word; flex: 1; }

@media (max-width: 350px) {
  .info-row { flex-direction: column; gap: 8px; }
  .info-row ion-icon { margin-top: 0; }
}

/* Social */
.social-buttons { display: flex; flex-direction: column; gap: 10px; }
.social-btn { --border-radius: 12px; height: 42px; font-size: 13px; font-weight: 600; text-transform: none; }
.social-btn.facebook { --color: #1877F2; --border-color: #1877F2; }
.social-btn.instagram { --color: #E4405F; --border-color: #E4405F; }

@media (max-width: 350px) {
  .social-btn { height: 40px; font-size: 12px; }
}

/* Legal */
.legal-links { display: flex; justify-content: center; align-items: center; gap: 6px; flex-wrap: wrap; margin: 16px 0 8px; }
.legal-links span { font-size: 12px; color: #4CAF50; cursor: pointer; font-weight: 600; }
.legal-links .dot { color: #ccc; cursor: default; }

.copyright { text-align: center; font-size: 11px; color: #999; margin: 0; }
</style>