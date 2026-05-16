<template>
  <ion-page>
    <ion-content :fullscreen="true" class="home-content">

      <!-- ─── Top Header ─────────────────────────── -->
      <div class="top-header">
        <div class="deliver-to" @click="openAddressPicker">
          <div class="deliver-label">DELIVER TO</div>
          <div class="deliver-address">
            <span>{{ currentAddress }}</span>
            <ion-icon :icon="chevronDown"></ion-icon>
          </div>
        </div>
        <div class="header-actions">
          <div class="header-btn wallet-btn" @click="goToWallet">
            <ion-icon :icon="wallet" class="wallet-icon"></ion-icon>
            <span class="wallet-balance">₱{{ walletBalance }}</span>
          </div>
          <div class="header-btn" @click="goToNotifications">
            <ion-icon :icon="notifications"></ion-icon>
            <span class="notif-dot" v-if="notifCount > 0">{{ notifCount }}</span>
          </div>
        </div>
      </div>

      <!-- ─── Search Bar ─────────────────────────── -->
      <div class="search-section" @click="goToBrowse">
        <div class="search-bar">
          <ion-icon :icon="search"></ion-icon>
          <span class="search-placeholder">What shall we deliver?</span>
        </div>
      </div>

      <!-- ─── Promo Banner ──────────────────────── -->
      <div class="banner-section">
        <div class="banner-scroll">
          <div
            v-for="(banner, index) in banners"
            :key="index"
            class="banner-card"
            :style="{ background: banner.gradient }"
            @click="openBanner(banner)"
          >
            <div class="banner-text">
              <p class="banner-tag">{{ banner.tag }}</p>
              <h3 class="banner-title">{{ banner.title }}</h3>
              <p class="banner-subtitle">{{ banner.subtitle }}</p>
              <div class="banner-btn">{{ banner.cta }}</div>
            </div>
            <div class="banner-img">
              <img :src="banner.image" :alt="banner.title">
            </div>
          </div>
        </div>
        <!-- Dots -->
        <div class="banner-dots">
          <div v-for="(b, i) in banners" :key="i"
            class="dot" :class="{ active: i === 0 }"></div>
        </div>
      </div>

      <!-- ─── Category Circles ───────────────────── -->
      <div class="section">
        <div class="categories-scroll">
          <div
            v-for="cat in categories"
            :key="cat.id"
            class="category-item"
            @click="browseCategory(cat)"
          >
            <div class="category-circle" :style="{ background: cat.color }">
              <img :src="cat.image" :alt="cat.name" class="category-img">
            </div>
            <span class="category-name">{{ cat.name }}</span>
          </div>
        </div>
      </div>

      <!-- ─── Feature Cards ─────────────────────── -->
      <div class="section px">
        <div class="feature-grid">
          <div class="feature-card near-me" @click="browseNearMe">
            <div class="feature-content">
              <h4>Near Me</h4>
              <p>Get it quick</p>
            </div>
            <ion-icon :icon="locationOutline" class="feature-icon"></ion-icon>
          </div>
          <div class="feature-card top-rated" @click="browseTopRated">
            <div class="feature-content">
              <h4>Top Rated</h4>
              <p>Best sellers</p>
            </div>
            <ion-icon :icon="starOutline" class="feature-icon"></ion-icon>
          </div>
          <div class="feature-card bundle-deals" @click="browseBundles">
            <div class="feature-content">
              <h4>Bundles</h4>
              <p>Best value</p>
            </div>
            <ion-icon :icon="cubeOutline" class="feature-icon"></ion-icon>
          </div>
        </div>
      </div>

      <!-- ─── Order Again ────────────────────────── -->
      <div class="section" v-if="orderAgain.length > 0">
        <div class="section-header px">
          <h2 class="section-title">Order Again</h2>
          <span class="see-all" @click="goToActivity">See all →</span>
        </div>
        <div class="sellers-scroll">
          <div
            v-for="seller in orderAgain"
            :key="seller.id"
            class="seller-card-small"
            @click="openSeller(seller)"
          >
            <div class="seller-img-wrap">
              <img :src="seller.image" :alt="seller.name" class="seller-img">
              <div class="seller-status" :class="seller.isOpen ? 'open' : 'closed'">
                {{ seller.isOpen ? 'Open' : 'Closed' }}
              </div>
            </div>
            <p class="seller-name-small">{{ seller.name }}</p>
            <p class="seller-dist-small">{{ seller.distance }}</p>
            <p class="seller-promo" v-if="seller.promo">{{ seller.promo }}</p>
          </div>
        </div>
      </div>

      <!-- ─── Sellers You May Like ───────────────── -->
      <div class="section px">
        <div class="section-header">
          <h2 class="section-title">Sellers Near You</h2>
          <span class="see-all" @click="goToBrowse">See all →</span>
        </div>

        <div class="seller-list">
          <div
            v-for="seller in nearbySellers"
            :key="seller.id"
            class="seller-card-big"
            @click="openSeller(seller)"
          >
            <!-- Seller Image -->
            <div class="seller-banner">
              <img :src="seller.image" :alt="seller.name" class="seller-banner-img">
              <div class="seller-badge" v-if="!seller.isOpen">Closed</div>
              <div class="seller-discount" v-if="seller.discount">{{ seller.discount }}</div>
            </div>

            <!-- Seller Info -->
            <div class="seller-info">
              <div class="seller-info-left">
                <h3 class="seller-name">{{ seller.name }}</h3>
                <div class="seller-meta">
                  <span class="seller-rating"><ion-icon :icon="starOutline"></ion-icon> {{ seller.rating }}</span>
                  <span class="meta-dot">•</span>
                  <span class="seller-time">{{ seller.deliveryTime }}</span>
                  <span class="meta-dot">•</span>
                  <span class="seller-dist">{{ seller.distance }}</span>
                </div>
                <div class="seller-tags">
                  <span class="tag" v-for="tag in seller.tags" :key="tag">{{ tag }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── Bottom Spacer ─────────────────────── -->
      <div style="height: 80px;"></div>

    </ion-content>

    <!-- ─── Notice Modal ───────────────────────── -->
    <ion-modal :is-open="showNoticeModal" @did-dismiss="closeNoticeModal">
      <ion-header>
        <ion-toolbar color="warning">
          <ion-title>Notice</ion-title>
          <ion-buttons slot="end">
            <ion-button @click="closeNoticeModal">
              <ion-icon :icon="close"></ion-icon>
            </ion-button>
          </ion-buttons>
        </ion-toolbar>
      </ion-header>
      <ion-content class="ion-padding">
        <div class="notice-modal-body" v-if="activeNotice">
          <div class="notice-modal-icon">
            <ion-icon :icon="megaphone"></ion-icon>
          </div>
          <h2 class="notice-modal-title">{{ activeNotice.title }}</h2>
          <p class="notice-modal-msg">{{ activeNotice.message }}</p>
          <ion-button expand="block" class="notice-modal-btn" @click="closeNoticeModal">Got it</ion-button>
        </div>
      </ion-content>
    </ion-modal>

    <!-- ─── Bottom Navigation ──────────────────── -->
    <ion-footer>
      <ion-toolbar class="bottom-nav-toolbar">
        <div class="bottom-nav">
          <div class="nav-item active" @click="goToHome">
            <ion-icon :icon="home"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item" @click="goToBrowse">
            <ion-icon :icon="search"></ion-icon>
            <span>Browse</span>
          </div>
          <div class="nav-item" @click="goToCart">
            <div class="cart-icon-wrap">
              <ion-icon :icon="cart"></ion-icon>
              <span class="cart-badge" v-if="cartCount > 0">{{ cartCount }}</span>
            </div>
            <span>Cart</span>
          </div>
          <div class="nav-item" @click="goToActivity">
            <ion-icon :icon="receipt"></ion-icon>
            <span>Activity</span>
          </div>
          <div class="nav-item" @click="goToProfile">
            <ion-icon :icon="person"></ion-icon>
            <span>Profile</span>
          </div>
        </div>
      </ion-toolbar>
    </ion-footer>

  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonContent, IonFooter, IonToolbar, IonIcon,
  IonModal, IonHeader, IonButtons, IonTitle,
  onIonViewWillEnter,
  actionSheetController, toastController
} from '@ionic/vue';
import {
  chevronDown, notifications, wallet,
  home, cart, receipt, person, search,
  location, star, cube,
  starOutline, megaphone, close
} from 'ionicons/icons';

import { banners as mockBanners, homeCategories as mockCategories, orderAgain as mockOrderAgain, nearbySellers as mockNearbySellers } from '@/mock/home';

const router = useRouter();

// State
const currentAddress = ref('Brgy. Carmen, CDO City');
const walletBalance = ref('1,250.00');
const notifCount = ref(3);
const cartCount = ref(0);

const banners = ref(mockBanners);
const categories = ref(mockCategories);
const orderAgain = ref(mockOrderAgain);
const nearbySellers = ref(mockNearbySellers);

// Notice banner
const NOTICE_KEY = 'palengke_notices'
const DISMISS_KEY = 'palengke_dismissed_notices'
const dismissedNotices = ref(JSON.parse(localStorage.getItem(DISMISS_KEY) || '[]'))

function seedNotices() {
  const existing = JSON.parse(localStorage.getItem(NOTICE_KEY) || '[]')
  if (existing.length) return
  const mock = [
    { id: 1, title: 'System Maintenance Tonight', message: 'The app will be down from 2AM to 4AM for scheduled maintenance. Please plan your orders accordingly.', target: 'all', active: true, createdAt: '2026-05-17' },
    { id: 2, title: 'Seller Portal Updates', message: 'New product analytics and bulk edit features are now available on your seller dashboard.', target: 'seller', active: true, createdAt: '2026-05-16' },
    { id: 3, title: 'Delivery Schedule Change', message: 'Due to the holiday, deliveries will run until 5PM only on May 20.', target: 'customer', active: true, createdAt: '2026-05-15' },
  ]
  localStorage.setItem(NOTICE_KEY, JSON.stringify(mock))
}

seedNotices()

const activeNotice = computed(() => {
  const all = JSON.parse(localStorage.getItem(NOTICE_KEY) || '[]')
  return all.find(n => n.active && (n.target === 'all' || n.target === 'customer')) || null
})

function dismissNotice(id) {
  if (!dismissedNotices.value.includes(id)) {
    dismissedNotices.value.push(id)
    localStorage.setItem(DISMISS_KEY, JSON.stringify(dismissedNotices.value))
  }
}

const showNoticeModal = ref(false)

function checkNotice() {
  const all = JSON.parse(localStorage.getItem(NOTICE_KEY) || '[]')
  const val = all.find(n => n.active && (n.target === 'all' || n.target === 'customer')) || null
  if (val && !dismissedNotices.value.includes(val.id)) {
    showNoticeModal.value = true
  }
}

onIonViewWillEnter(checkNotice)

function closeNoticeModal() {
  if (activeNotice.value) dismissNotice(activeNotice.value.id)
  showNoticeModal.value = false
}

// Methods
const openAddressPicker = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Delivery Address',
    buttons: [
      { text: 'Brgy. Carmen, CDO City', handler: () => { currentAddress.value = 'Brgy. Carmen, CDO City'; } },
      { text: 'Brgy. Lapasan, CDO City', handler: () => { currentAddress.value = 'Brgy. Lapasan, CDO City'; } },
      { text: 'Brgy. Macasandig, CDO City', handler: () => { currentAddress.value = 'Brgy. Macasandig, CDO City'; } },
      { text: '+ Add New Address', handler: () => router.push('/addresses') },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const openBanner = (banner) => { router.push('/browse'); };
const browseCategory = (cat) => { router.push(`/browse?category=${cat.route}`); };
const browseNearMe = () => { router.push('/browse?filter=nearby'); };
const browseTopRated = () => { router.push('/browse?filter=top-rated'); };
const browseBundles = () => { router.push('/browse?category=bundles'); };
const openSeller = (seller) => {
  if (!seller.isOpen) {
    showClosedMessage(seller.name);
    return;
  }
  router.push(`/seller/${seller.id}`);
};

const showClosedMessage = async (name) => {
  const toast = await toastController.create({
    message: `${name} is currently closed`,
    duration: 2000, color: 'dark', position: 'top'
  });
  await toast.present();
};

// Navigation
const goToHome = () => {};
const goToBrowse = () => router.push('/browse');
const goToCart = () => router.push('/cart');
const goToActivity = () => router.push('/activity');
const goToProfile = () => router.push('/profile');
const goToWallet = () => router.push('/payment-methods');
const goToNotifications = () => router.push('/notifications');
</script>

<style scoped>
.home-content { 
  --background: #f8f9fa;
  --padding-top: 0px;
  --padding-start: 0px;
  --padding-end: 0px;
}

/* ─── Top Header ────────────────────────────── */
.top-header {
  background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
  padding: 16px 20px 20px;
  display: flex; justify-content: space-between; align-items: flex-start;
}

.deliver-to { cursor: pointer; }
.deliver-label { font-size: 10px; color: rgba(255,255,255,0.7); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.deliver-address { display: flex; align-items: center; gap: 8px; color: white; font-size: 14px; font-weight: 600; }
.deliver-address span { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.deliver-address ion-icon { font-size: 16px; color: rgba(255,255,255,0.8); }

.header-actions { display: flex; align-items: center; gap: 8px; }
.header-btn {
  position: relative; width: 36px; height: 36px;
  background: rgba(255,255,255,0.2); border-radius: 50%;
  display: flex; align-items: center; justify-content: center; cursor: pointer;
}
.header-btn ion-icon { font-size: 20px; color: white; }
.wallet-btn { width: auto; padding: 6px 12px; border-radius: 20px; gap: 4px; background: rgba(255,255,255,0.25); display: flex; align-items: center; }
.wallet-icon { font-size: 14px; color: white; }
.wallet-balance { font-size: 13px; font-weight: 600; color: white; }
.notif-dot {
  position: absolute; top: -2px; right: -2px;
  background: #f44336; color: white; border-radius: 10px;
  padding: 1px 5px; font-size: 9px; font-weight: 700;
  border: 2px solid #4CAF50;
}
.avatar-btn {
  width: 40px; height: 40px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
}
.avatar-btn ion-icon { font-size: 40px; color: white; }

/* ─── Search Bar ─────────────────────────────── */
.search-section {
  background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
  padding: 0 16px 20px;
}
.search-bar {
  background: white; border-radius: 14px;
  padding: 14px 18px; display: flex; align-items: center; gap: 12px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1); cursor: pointer;
}
.search-bar ion-icon { font-size: 20px; color: #999; flex-shrink: 0; }
.search-placeholder { font-size: 15px; color: #999; }

/* ─── Notice Modal ──────────────────────────── */
.notice-modal-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 32px 16px 16px;
  gap: 12px;
}
.notice-modal-icon {
  font-size: 48px;
  color: #F57F17;
  margin-bottom: 8px;
}
.notice-modal-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}
.notice-modal-msg {
  font-size: 14px;
  color: #666;
  line-height: 1.6;
  margin: 0 0 8px;
}
.notice-modal-btn {
  --border-radius: 12px;
  height: 48px;
  font-weight: 600;
  --background: #F57F17;
}

/* ─── Banner ─────────────────────────────────── */
.banner-section { padding: 16px 0 8px; }
.banner-scroll {
  display: flex; gap: 12px;
  padding: 0 16px;
  overflow-x: auto; scroll-snap-type: x mandatory;
}
.banner-scroll::-webkit-scrollbar { display: none; }

.banner-card {
  min-width: calc(100% - 32px); scroll-snap-align: start;
  border-radius: 18px; padding: 20px 24px;
  display: flex; justify-content: space-between; align-items: center;
  cursor: pointer; position: relative; overflow: hidden;
}

.banner-text { color: white; z-index: 1; flex: 1; }
.banner-tag { font-size: 11px; font-weight: 700; opacity: 0.9; margin: 0 0 6px 0; letter-spacing: 0.5px; }
.banner-title { font-size: 20px; font-weight: 800; margin: 0 0 6px 0; white-space: pre-line; line-height: 1.2; }
.banner-subtitle { font-size: 12px; opacity: 0.85; margin: 0 0 12px 0; }
.banner-btn {
  display: inline-block; background: rgba(255,255,255,0.25);
  padding: 8px 16px; border-radius: 20px;
  font-size: 13px; font-weight: 700; color: white;
}
.banner-img { z-index: 1; }
.banner-img img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 12px;
}

.banner-dots { display: flex; justify-content: center; gap: 6px; margin-top: 12px; }
.dot { width: 6px; height: 6px; border-radius: 50%; background: #ddd; transition: all 0.2s; }
.dot.active { width: 20px; background: #4CAF50; border-radius: 3px; }

/* ─── Section ────────────────────────────────── */
.section { margin-bottom: 8px; }
.px { padding: 0 16px; }

.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.section-title { font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0; }
.see-all { font-size: 13px; color: #4CAF50; font-weight: 600; cursor: pointer; }

/* ─── Categories ──────────────────────────────── */
.categories-scroll {
  display: flex; gap: 12px;
  padding: 16px 16px;
  overflow-x: auto;
}
.categories-scroll::-webkit-scrollbar { display: none; }

.category-item { display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; flex-shrink: 0; }
.category-item:active { transform: scale(0.95); }

.category-circle {
  width: 60px; height: 60px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  overflow: hidden;
  border: 2px solid white;
}
.category-img { width: 100%; height: 100%; object-fit: cover; }
.category-emoji { font-size: 28px; }
.category-name { font-size: 11px; font-weight: 600; color: #333; white-space: nowrap; }

/* ─── Feature Cards ──────────────────────────── */
.feature-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 8px; }

.feature-card {
  border-radius: 16px; padding: 16px;
  display: flex; justify-content: space-between; align-items: center;
  cursor: pointer; transition: transform 0.2s;
}
.feature-card:active { transform: scale(0.97); }
.feature-content h4 { font-size: 15px; font-weight: 700; color: white; margin: 0 0 4px 0; }
.feature-content p { font-size: 12px; color: rgba(255,255,255,0.85); margin: 0; }
.feature-icon { font-size: 32px; color: white; }

.near-me { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
.top-rated { background: linear-gradient(135deg, #FF9800, #E65100); }
.bundle-deals { background: linear-gradient(135deg, #9C27B0, #4A148C); }

/* ─── Sellers Scroll (Order Again) ───────────── */
.sellers-scroll {
  display: flex; gap: 12px;
  padding: 0 16px 16px;
  overflow-x: auto;
}
.sellers-scroll::-webkit-scrollbar { display: none; }

.seller-card-small {
  flex-shrink: 0; width: 120px; cursor: pointer;
}
.seller-card-small:active { transform: scale(0.97); }

.seller-img-wrap { position: relative; margin-bottom: 8px; }
.seller-img {
  width: 120px; height: 90px; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 36px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  object-fit: cover;
}
.seller-status {
  position: absolute; top: 6px; left: 6px;
  padding: 2px 8px; border-radius: 8px;
  font-size: 10px; font-weight: 700;
}
.seller-status.open { background: #E8F5E9; color: #4CAF50; }
.seller-status.closed { background: #FFEBEE; color: #f44336; }

.seller-name-small { font-size: 13px; font-weight: 700; color: #1a1a1a; margin: 0 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.seller-dist-small { font-size: 11px; color: #999; margin: 0 0 2px 0; }
.seller-promo { font-size: 11px; color: #4CAF50; font-weight: 600; margin: 0; }

/* ─── Seller List (Nearby) ───────────────────── */
.seller-list { display: flex; flex-direction: column; gap: 16px; }

.seller-card-big {
  background: white; border-radius: 18px;
  overflow: hidden; cursor: pointer;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  transition: transform 0.2s;
}
.seller-card-big:active { transform: scale(0.98); }

.seller-banner {
  height: 140px; display: flex; align-items: center;
  justify-content: center; position: relative; overflow: hidden;
  border-radius: 18px 18px 0 0;
}
.seller-banner-img {
  position: absolute; top: 0; left: 0; width: 100%; height: 100%;
  object-fit: cover;
}
.seller-badge {
  position: absolute; top: 12px; left: 12px;
  background: rgba(0,0,0,0.6); color: white;
  padding: 4px 10px; border-radius: 8px;
  font-size: 12px; font-weight: 700;
}
.seller-discount {
  position: absolute; top: 12px; right: 12px;
  background: #4CAF50; color: white;
  padding: 4px 10px; border-radius: 8px;
  font-size: 12px; font-weight: 700;
}

.seller-info { padding: 14px 16px; }
.seller-name { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 6px 0; }
.seller-meta { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; flex-wrap: wrap; }
.seller-rating { font-size: 13px; font-weight: 600; color: #1a1a1a; }
.seller-time, .seller-dist { font-size: 13px; color: #666; }
.meta-dot { color: #ccc; font-size: 12px; }

.seller-tags { display: flex; gap: 6px; flex-wrap: wrap; }
.tag {
  background: #f0f0f0; color: #555;
  padding: 3px 10px; border-radius: 10px;
  font-size: 11px; font-weight: 600;
}

/* ─── Bottom Nav ─────────────────────────────── */
.bottom-nav-toolbar {
  --background: white;
  --border-color: #f0f0f0;
  box-shadow: 0 -2px 12px rgba(0,0,0,0.08);
}
.bottom-nav {
  display: grid; grid-template-columns: repeat(5, 1fr); height: 60px;
}
.nav-item {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 3px; cursor: pointer; color: #888;
  position: relative; transition: all 0.2s;
  background: transparent !important;
  border-radius: 12px;
  padding: 4px 8px;
}
.nav-item ion-icon { font-size: 22px; }
.nav-item span { font-size: 10px; font-weight: 600; }
.nav-item.active { 
  color: white !important; 
  background: #4CAF50 !important; 
}
:deep(.nav-item.active ion-icon) { color: white !important; }
:deep(.nav-item.active span) { color: white !important; }
.nav-item:active { transform: scale(0.95); }

.cart-icon-wrap { position: relative; }
.cart-badge {
  position: absolute; top: -6px; right: -8px;
  background: #f44336; color: white;
  border-radius: 10px; padding: 1px 5px;
  font-size: 9px; font-weight: 700;
}

@media (max-width: 360px) {
  .banner-title { font-size: 17px; }
  .category-circle { width: 52px; height: 52px; }
  .category-emoji { font-size: 24px; }
}
</style>