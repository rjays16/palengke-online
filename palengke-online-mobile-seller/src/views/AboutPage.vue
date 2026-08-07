<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>About</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div class="about-container">
        <!-- App Header -->
        <div class="app-header">
          <div class="app-logo">
            <img src="/assets/images/logo-without-circle.png" alt="Palengke Online" />
          </div>
          <h1 class="app-name">Palengke Online</h1>
          <p class="app-tagline">Empowering Filipino Sellers</p>
          <p class="app-version">Version 1.0.0 (Build 100)</p>
          <p class="company-badge">by Chui Tech Solution Inc.</p>
        </div>

        <!-- Mission Section -->
        <div class="section">
          <h2 class="section-title">Our Mission</h2>
          <div class="content-card">
            <p>
              Palengke Online is dedicated to empowering Filipino sellers by bringing the traditional palengke
              experience into the digital age. We connect local vendors with customers, making fresh produce and quality
              goods accessible to everyone.
            </p>
          </div>
        </div>

        <!-- Features Section -->
        <div class="section">
          <h2 class="section-title">What We Offer</h2>

          <div class="feature-list">
            <div class="feature-item">
              <div class="feature-icon">
                <ion-icon :icon="storefrontOutline"></ion-icon>
              </div>
              <div class="feature-content">
                <h3>Easy Store Management</h3>
                <p>Manage your products, orders, and inventory with ease</p>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon">
                <ion-icon :icon="phonePortraitOutline"></ion-icon>
              </div>
              <div class="feature-content">
                <h3>Mobile-First Platform</h3>
                <p>Run your business from anywhere using your smartphone</p>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon">
                <ion-icon :icon="cashOutline"></ion-icon>
              </div>
              <div class="feature-content">
                <h3>Flexible Payments</h3>
                <p>Accept payments via GCash, bank transfer, or cash on delivery</p>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon">
                <ion-icon :icon="chatbubblesOutline"></ion-icon>
              </div>
              <div class="feature-content">
                <h3>Direct Communication</h3>
                <p>Chat directly with your customers and delivery drivers</p>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon">
                <ion-icon :icon="statsChartOutline"></ion-icon>
              </div>
              <div class="feature-content">
                <h3>Sales Analytics</h3>
                <p>Track your performance with detailed sales reports</p>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon">
                <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              </div>
              <div class="feature-content">
                <h3>Secure & Reliable</h3>
                <p>Your data and transactions are protected and encrypted</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Section -->
        <div class="section">
          <h2 class="section-title">Our Impact</h2>

          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon"><font-awesome-icon icon="store" /></div>
              <div class="stat-value">{{ formatNumber(stats.activeSellers) }}</div>
              <div class="stat-label">Active Sellers</div>
            </div>

            <div class="stat-card">
              <div class="stat-icon"><font-awesome-icon icon="users" /></div>
              <div class="stat-value">{{ formatNumber(stats.happyCustomers) }}</div>
              <div class="stat-label">Happy Customers</div>
            </div>

            <div class="stat-card">
              <div class="stat-icon"><font-awesome-icon icon="box" /></div>
              <div class="stat-value">{{ formatNumber(stats.deliveriesDone) }}</div>
              <div class="stat-label">Orders Delivered</div>
            </div>

            <div class="stat-card">
              <div class="stat-icon"><font-awesome-icon icon="city" /></div>
              <div class="stat-value">{{ formatNumber(stats.citiesServed) }}</div>
              <div class="stat-label">Cities Served</div>
            </div>
          </div>
        </div>

        <!-- Company Info Section -->
        <div class="section">
          <h2 class="section-title">About Chui Tech Solution Inc.</h2>
          <div class="content-card">
            <p>
              <strong>Chui Tech Solution Inc.</strong> is a Filipino technology company dedicated to creating innovative
              digital solutions that empower local businesses and communities.
            </p>
            <p>
              We specialize in developing mobile and web applications that bridge the gap between traditional commerce
              and modern technology, helping Filipino entrepreneurs thrive in the digital economy.
            </p>
          </div>
        </div>

        <!-- Team Section -->
        <div class="section">
          <h2 class="section-title">Our Values</h2>

          <div class="values-list">
            <div class="value-item">
              <ion-icon :icon="heartOutline"></ion-icon>
              <div>
                <h3>Community First</h3>
                <p>Supporting local businesses and Filipino communities</p>
              </div>
            </div>

            <div class="value-item">
              <ion-icon :icon="rocketOutline"></ion-icon>
              <div>
                <h3>Innovation</h3>
                <p>Bringing modern technology to traditional markets</p>
              </div>
            </div>

            <div class="value-item">
              <ion-icon :icon="leafOutline"></ion-icon>
              <div>
                <h3>Sustainability</h3>
                <p>Promoting fresh, local, and sustainable produce</p>
              </div>
            </div>

            <div class="value-item">
              <ion-icon :icon="peopleOutline"></ion-icon>
              <div>
                <h3>Empowerment</h3>
                <p>Helping sellers grow their businesses digitally</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Section -->
        <div class="section">
          <h2 class="section-title">Get in Touch</h2>

          <div class="contact-list">
            <div class="contact-item" @click="openWebsite">
              <ion-icon :icon="globeOutline"></ion-icon>
              <div>
                <h3>Website</h3>
                <p>www.chuitech.com</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>

            <div class="contact-item" @click="sendEmail">
              <ion-icon :icon="mailOutline"></ion-icon>
              <div>
                <h3>Email</h3>
                <p>support@chuitech.com</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>

            <div class="contact-item" @click="callUs">
              <ion-icon :icon="callOutline"></ion-icon>
              <div>
                <h3>Phone</h3>
                <p>+63 912 345 6789</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>

            <div class="contact-item" @click="openAddress">
              <ion-icon :icon="locationOutline"></ion-icon>
              <div>
                <h3>Office</h3>
                <p>Cagayan de Oro City, Philippines</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Social Media Section -->
        <div class="section">
          <h2 class="section-title">Follow Us</h2>

          <div class="social-buttons">
            <div class="social-button facebook" @click="openFacebook">
              <ion-icon :icon="logoFacebook"></ion-icon>
              <span>Facebook</span>
            </div>

            <div class="social-button instagram" @click="openInstagram">
              <ion-icon :icon="logoInstagram"></ion-icon>
              <span>Instagram</span>
            </div>

            <div class="social-button twitter" @click="openTwitter">
              <ion-icon :icon="logoTwitter"></ion-icon>
              <span>Twitter</span>
            </div>

            <div class="social-button youtube" @click="openYoutube">
              <ion-icon :icon="logoYoutube"></ion-icon>
              <span>YouTube</span>
            </div>
          </div>
        </div>

        <!-- Legal Section -->
        <div class="section">
          <h2 class="section-title">Legal</h2>

          <div class="legal-list">
            <div class="legal-item" @click="viewTerms">
              <ion-icon :icon="documentTextOutline"></ion-icon>
              <span>Terms & Conditions</span>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>

            <div class="legal-item" @click="viewPrivacy">
              <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              <span>Privacy Policy</span>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>

            <div class="legal-item" @click="viewLicenses">
              <ion-icon :icon="documentOutline"></ion-icon>
              <span>Open Source Licenses</span>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Credits Section -->
        <div class="section">
          <h2 class="section-title">Credits</h2>

          <div class="content-card credits-card">
            <p><strong>Developed by:</strong><br />Chui Tech Solution Inc.</p>
            <p><strong>Powered by:</strong><br />Ionic Framework, Vue.js, Capacitor</p>
            <p><strong>Special Thanks:</strong><br />Our amazing community of sellers and customers</p>
          </div>
        </div>

        <!-- App Info -->
        <div class="app-info">
          <div class="info-row">
            <span>Company:</span>
            <span>Chui Tech Solution Inc.</span>
          </div>
          <div class="info-row">
            <span>Build Date:</span>
            <span>January 1, 2026</span>
          </div>
          <div class="info-row">
            <span>Bundle ID:</span>
            <span>com.chuitech.palengke</span>
          </div>
          <div class="info-row">
            <span>Last Updated:</span>
            <span>January 1, 2026</span>
          </div>
        </div>

        <!-- Copyright -->
        <div class="copyright">
          <p>© 2024-2026 Chui Tech Solution Inc.</p>
          <p>All rights reserved</p>
        </div>

        <!-- Rate App Button -->
        <div class="rate-section">
          <ion-button expand="block" color="success" @click="rateApp">
            <ion-icon :icon="starOutline" slot="start"></ion-icon>
            Rate This App
          </ion-button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonButton,
  IonIcon,
  alertController,
  toastController,
} from '@ionic/vue';
import {
  storefrontOutline,
  phonePortraitOutline,
  cashOutline,
  chatbubblesOutline,
  statsChartOutline,
  shieldCheckmarkOutline,
  heartOutline,
  rocketOutline,
  leafOutline,
  peopleOutline,
  globeOutline,
  mailOutline,
  callOutline,
  locationOutline,
  chevronForwardOutline,
  logoFacebook,
  logoInstagram,
  logoTwitter,
  logoYoutube,
  documentTextOutline,
  documentOutline,
  starOutline,
} from 'ionicons/icons';

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

const stats = ref({ activeSellers: 0, happyCustomers: 0, deliveriesDone: 0, citiesServed: 0 });
const statsLoaded = ref(false);

const formatNumber = (value) => {
  if (!statsLoaded.value) return '--';
  const num = Number(value) || 0;
  return num.toLocaleString() + '+';
};

onMounted(async () => {
  try {
    const res = await fetch(`${API_BASE}/api/app-stats`);
    if (res.ok) {
      const data = await res.json();
      stats.value = {
        activeSellers: data.active_sellers ?? 0,
        happyCustomers: data.happy_customers ?? 0,
        deliveriesDone: data.deliveries_done ?? 0,
        citiesServed: data.cities_served ?? 0,
      };
    }
  } catch (err) {
    console.error('Failed to load app stats:', err);
  } finally {
    statsLoaded.value = true;
  }
});

// Methods
const openWebsite = () => {
  window.open('https://chuitech.com', '_blank');
};

const sendEmail = () => {
  window.location.href = 'mailto:support@chuitech.com';
};

const callUs = () => {
  window.location.href = 'tel:+639123456789';
};

const openAddress = () => {
  const address = encodeURIComponent('Cagayan de Oro City, Philippines');
  window.open(`https://www.google.com/maps/search/?api=1&query=${address}`, '_blank');
};

const openFacebook = () => {
  window.open('https://facebook.com/ChuiTechSolution', '_blank');
};

const openInstagram = () => {
  window.open('https://instagram.com/chuitech', '_blank');
};

const openTwitter = () => {
  window.open('https://twitter.com/chuitech', '_blank');
};

const openYoutube = () => {
  window.open('https://youtube.com/@chuitech', '_blank');
};

const viewTerms = async () => {
  const alert = await alertController.create({
    header: 'Terms & Conditions',
    cssClass: 'terms-modal',
    message:
      'By using this app, you agree to provide accurate product information, deliver quality products to customers, maintain professional conduct, and follow local business regulations. © 2024-2026 Chui Tech Solution Inc. All rights reserved.',
    buttons: [
      {
        text: 'View Full Terms',
        handler: () => {
          window.open('https://chuitech.com/terms', '_blank');
        },
      },
      {
        text: 'Close',
        role: 'cancel',
      },
    ],
  });
  await alert.present();
};

const viewPrivacy = async () => {
  const alert = await alertController.create({
    header: 'Privacy Policy',
    cssClass: 'privacy-modal',
    message:
      'We protect your data by encrypting all transactions, never sharing personal information, securing payment details, and following data privacy laws. © 2024-2026 Chui Tech Solution Inc.',
    buttons: [
      {
        text: 'View Full Policy',
        handler: () => {
          window.open('https://chuitech.com/privacy', '_blank');
        },
      },
      {
        text: 'Close',
        role: 'cancel',
      },
    ],
  });
  await alert.present();
};

const viewLicenses = async () => {
  const alert = await alertController.create({
    header: 'Open Source Licenses',
    cssClass: 'licenses-modal',
    message:
      'This app is built with: Ionic Framework (MIT), Vue.js (MIT), Capacitor (MIT), and Ionicons (MIT). Thank you to the open source community!',
    buttons: [
      {
        text: 'View All Licenses',
        handler: () => {
          showToast('Full license list coming soon');
        },
      },
      {
        text: 'Close',
        role: 'cancel',
      },
    ],
  });
  await alert.present();
};

const rateApp = async () => {
  const alert = await alertController.create({
    header: 'Rate Palengke Online',
    message: 'Enjoying the app? Please rate us on the App Store or Google Play!',
    buttons: [
      {
        text: 'Later',
        role: 'cancel',
      },
      {
        text: 'Rate Now',
        handler: async () => {
          const toast = await toastController.create({
            message: '<font-awesome-icon icon="star" /> Opening app store...',
            duration: 2000,
            color: 'success',
            position: 'top',
          });
          await toast.present();
        },
      },
    ],
  });
  await alert.present();
};

const showToast = async (message) => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color: 'dark',
    position: 'top',
  });
  await toast.present();
};
</script>

<style scoped>
.about-container {
  background: #f8f9fa;
  min-height: 100%;
  padding-bottom: 40px;
}

/* App Header */
.app-header {
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  padding: 40px 20px;
  text-align: center;
  color: white;
}

.app-logo {
  width: 120px;
  height: 120px;
  margin: 0 auto 20px auto;
  background: white;
  border-radius: 30px;
  padding: 20px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.app-logo img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.app-name {
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 8px 0;
}

.app-tagline {
  font-size: 16px;
  margin: 0 0 8px 0;
  opacity: 0.95;
}

.app-version {
  font-size: 14px;
  margin: 0 0 8px 0;
  opacity: 0.85;
}

.company-badge {
  font-size: 13px;
  margin: 0;
  opacity: 0.9;
  font-style: italic;
}

/* Section */
.section {
  padding: 24px 20px 0 20px;
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.content-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.content-card p {
  font-size: 15px;
  line-height: 1.7;
  color: #666;
  margin: 0 0 12px 0;
}

.content-card p:last-child {
  margin-bottom: 0;
}

/* Features */
.feature-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.feature-item {
  background: white;
  border-radius: 16px;
  padding: 16px;
  display: flex;
  gap: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.feature-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: #e8f5e9;
  color: #4caf50;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  flex-shrink: 0;
}

.feature-content h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.feature-content p {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.5;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.stat-icon {
  font-size: 40px;
  margin-bottom: 12px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #4caf50;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 13px;
  color: #666;
}

/* Values */
.values-list {
  background: white;
  border-radius: 16px;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.value-item {
  display: flex;
  gap: 16px;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.value-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.value-item > ion-icon {
  font-size: 32px;
  color: #4caf50;
  flex-shrink: 0;
}

.value-item h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.value-item p {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.5;
}

/* Contact List */
.contact-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.contact-item {
  background: white;
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s;
}

.contact-item:active {
  transform: scale(0.98);
}

.contact-item > ion-icon:first-child {
  font-size: 28px;
  color: #4caf50;
}

.contact-item div {
  flex: 1;
}

.contact-item h3 {
  font-size: 15px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 2px 0;
}

.contact-item p {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.arrow {
  font-size: 20px;
  color: #ccc;
}

/* Social Buttons */
.social-buttons {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.social-button {
  background: white;
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s;
}

.social-button:active {
  transform: scale(0.98);
}

.social-button ion-icon {
  font-size: 36px;
}

.social-button span {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.social-button.facebook ion-icon {
  color: #1877f2;
}

.social-button.instagram ion-icon {
  color: #e4405f;
}

.social-button.twitter ion-icon {
  color: #1da1f2;
}

.social-button.youtube ion-icon {
  color: #ff0000;
}

/* Legal List */
.legal-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.legal-item {
  background: white;
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s;
}

.legal-item:active {
  transform: scale(0.98);
}

.legal-item > ion-icon:first-child {
  font-size: 24px;
  color: #4caf50;
}

.legal-item span {
  flex: 1;
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
}

/* Credits Card */
.credits-card p {
  margin-bottom: 16px;
}

.credits-card p:last-child {
  margin-bottom: 0;
}

.credits-card strong {
  color: #1a1a1a;
}

/* App Info */
.app-info {
  background: white;
  border-radius: 16px;
  padding: 16px;
  margin: 24px 20px 0 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
}

.info-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.info-row span:first-child {
  color: #666;
}

.info-row span:last-child {
  color: #1a1a1a;
  font-weight: 600;
}

/* Copyright */
.copyright {
  text-align: center;
  padding: 24px 20px;
}

.copyright p {
  font-size: 13px;
  color: #999;
  margin: 4px 0;
}

.made-in {
  font-size: 14px;
  font-weight: 600;
  color: #4caf50;
  margin-top: 12px;
}

/* Rate Section */
.rate-section {
  padding: 0 20px 24px 20px;
}

.rate-section ion-button {
  --border-radius: 12px;
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
}

/* Responsive */
@media (max-width: 360px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .social-buttons {
    grid-template-columns: 1fr;
  }
}
</style>
