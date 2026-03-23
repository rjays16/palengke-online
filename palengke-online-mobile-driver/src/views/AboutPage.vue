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
          <div class="version-badge">Version 1.0.0 (Build 100)</div>
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
              <h4>1,000+</h4>
              <p>Active Drivers</p>
            </div>
          </div>
          <div class="stat-divider"></div>
          <div class="app-stat">
            <div class="app-stat-icon blue"><ion-icon :icon="cubeOutline"></ion-icon></div>
            <div>
              <h4>50,000+</h4>
              <p>Deliveries Done</p>
            </div>
          </div>
          <div class="stat-divider"></div>
          <div class="app-stat">
            <div class="app-stat-icon orange"><ion-icon :icon="storefrontOutline"></ion-icon></div>
            <div>
              <h4>500+</h4>
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

        <p class="copyright">© 2024 Palengke Online Inc. All rights reserved.</p>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { useRouter } from 'vue-router';
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

const router = useRouter();

const openSocial = (platform) => {
  const urls = {
    facebook: 'https://facebook.com/palengkeonline',
    instagram: 'https://instagram.com/palengkeonline'
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
.about-container { padding: 20px 16px 40px; }

/* App Hero */
.app-hero {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  border-radius: 20px; padding: 36px 20px;
  text-align: center; color: white; margin-bottom: 16px;
  box-shadow: 0 4px 20px rgba(76,175,80,0.3);
}

.app-logo { width: 90px; height: 90px; border-radius: 22px; overflow: hidden; margin: 0 auto 16px; background: white; padding: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.app-logo img { width: 100%; height: 100%; object-fit: contain; }

.app-name { font-size: 26px; font-weight: 800; margin: 0 0 4px 0; }
.app-tagline { font-size: 15px; margin: 0 0 16px 0; opacity: 0.9; }

.version-badge {
  display: inline-block; background: rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.4);
  padding: 6px 16px; border-radius: 20px;
  font-size: 13px; font-weight: 600;
}

/* About Card */
.about-card {
  background: white; border-radius: 16px;
  padding: 20px; margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.card-title { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 14px 0; }
.about-text { font-size: 14px; color: #555; line-height: 1.7; margin: 0; }

/* Stats Card */
.stats-card {
  background: white; border-radius: 16px;
  padding: 20px; margin-bottom: 16px;
  display: flex; justify-content: space-around; align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.app-stat { display: flex; align-items: center; gap: 12px; }
.app-stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
.app-stat-icon.green { background: #E8F5E9; color: #4CAF50; }
.app-stat-icon.blue { background: #E3F2FD; color: #2196F3; }
.app-stat-icon.orange { background: #FFF3E0; color: #FF9800; }
.app-stat h4 { font-size: 18px; font-weight: 800; color: #1a1a1a; margin: 0 0 2px 0; }
.app-stat p { font-size: 11px; color: #666; margin: 0; }
.stat-divider { width: 1px; height: 48px; background: #f0f0f0; }

/* Info Row */
.info-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
.info-row:last-child { border-bottom: none; }
.info-row ion-icon { font-size: 20px; color: #4CAF50; flex-shrink: 0; }
.info-row span { font-size: 14px; color: #555; }

/* Social */
.social-buttons { display: flex; flex-direction: column; gap: 10px; }
.social-btn { --border-radius: 12px; height: 46px; font-size: 14px; font-weight: 600; text-transform: none; }
.social-btn.facebook { --color: #1877F2; --border-color: #1877F2; }
.social-btn.instagram { --color: #E4405F; --border-color: #E4405F; }

/* Legal */
.legal-links { display: flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: wrap; margin: 16px 0 8px; }
.legal-links span { font-size: 13px; color: #4CAF50; cursor: pointer; font-weight: 600; }
.legal-links .dot { color: #ccc; cursor: default; }

.copyright { text-align: center; font-size: 12px; color: #999; margin: 0; }
</style>