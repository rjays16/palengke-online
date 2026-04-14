<template>
  <ion-page>
    <ion-content :fullscreen="true" class="home-content">

      <!-- ─── Top Header ─────────────────────────── -->
      <div class="top-header">
        <div class="deliver-to" @click="openAddressPicker">
          <div class="deliver-label">DELIVER TO</div>
          <div class="deliver-address">
            <span>{{ currentAddress }}</span>
            <ion-icon :icon="chevronDownOutline"></ion-icon>
          </div>
        </div>
        <div class="header-actions">
          <div class="header-btn" @click="goToMessages">
            <ion-icon :icon="chatbubblesOutline"></ion-icon>
          </div>
          <div class="header-btn" @click="goToNotifications">
            <ion-icon :icon="notificationsOutline"></ion-icon>
            <span class="notif-dot" v-if="notifCount > 0">{{ notifCount }}</span>
          </div>
        </div>
      </div>

      <!-- ─── Search Bar ─────────────────────────── -->
      <div class="search-section" @click="goToBrowse">
        <div class="search-bar">
          <ion-icon :icon="searchOutline"></ion-icon>
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
            <div class="feature-icon">📍</div>
          </div>
          <div class="feature-card top-rated" @click="browseTopRated">
            <div class="feature-content">
              <h4>Top Rated</h4>
              <p>Best sellers</p>
            </div>
            <div class="feature-icon">⭐</div>
          </div>
          <div class="feature-card bundle-deals" @click="browseBundles">
            <div class="feature-content">
              <h4>Bundles</h4>
              <p>Best value</p>
            </div>
            <div class="feature-icon">📦</div>
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
                  <span class="seller-rating">⭐ {{ seller.rating }}</span>
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

    <!-- ─── Bottom Navigation ──────────────────── -->
    <ion-footer>
      <ion-toolbar class="bottom-nav-toolbar">
        <div class="bottom-nav">
          <div class="nav-item active" @click="goToHome">
            <ion-icon :icon="homeOutline"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item" @click="goToBrowse">
            <ion-icon :icon="searchOutline"></ion-icon>
            <span>Browse</span>
          </div>
          <div class="nav-item" @click="goToCart">
            <div class="cart-icon-wrap">
              <ion-icon :icon="cartOutline"></ion-icon>
              <span class="cart-badge" v-if="cartCount > 0">{{ cartCount }}</span>
            </div>
            <span>Cart</span>
          </div>
          <div class="nav-item" @click="goToActivity">
            <ion-icon :icon="receiptOutline"></ion-icon>
            <span>Activity</span>
          </div>
          <div class="nav-item" @click="goToProfile">
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
  IonPage, IonContent, IonFooter, IonToolbar, IonIcon,
  actionSheetController, toastController
} from '@ionic/vue';
import {
  chevronDownOutline, notificationsOutline,
  chatbubblesOutline,
  searchOutline, homeOutline, cartOutline, receiptOutline, personOutline
} from 'ionicons/icons';

const router = useRouter();

// State
const currentAddress = ref('Brgy. Carmen, CDO City');
const notifCount = ref(3);
const cartCount = ref(0);

// Banners
const banners = ref([
  {
    tag: 'LIMITED TIME',
    title: 'Fresh from Palengke\nUp to 20% OFF!',
    subtitle: 'Direct from local vendors',
    cta: 'Order Now',
    emoji: '',
    gradient: 'linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%)',
    image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=200&h=200&fit=crop'
  },
  {
    tag: 'FLASH SALE',
    title: 'Palengke Bundles\nSave More!',
    subtitle: 'Best deals from the market',
    cta: 'See Bundles',
    emoji: '',
    gradient: 'linear-gradient(135deg, #2196F3 0%, #1565C0 100%)',
    image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=200&h=200&fit=crop'
  }
]);

// Categories
const categories = ref([
  { id: 1, name: 'Vegetables', emoji: '', color: '#E8F5E9', route: 'vegetables', image: 'https://images.unsplash.com/photo-1518843875459-f738682238a6?w=120&h=120&fit=crop' },
  { id: 2, name: 'Fruits', emoji: '', color: '#FCE4EC', route: 'fruits', image: 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=120&h=120&fit=crop' },
  { id: 3, name: 'Seafoods', emoji: '', color: '#E3F2FD', route: 'seafoods', image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=120&h=120&fit=crop' },
  { id: 4, name: 'Pork', emoji: '', color: '#FFF3E0', route: 'pork', image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=120&h=120&fit=crop' },
  { id: 5, name: 'Chicken', emoji: '', color: '#FFF8E1', route: 'chicken', image: 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=120&h=120&fit=crop' },
  { id: 6, name: 'Bundles', emoji: '', color: '#F3E5F5', route: 'bundles', image: 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?w=120&h=120&fit=crop' },
  { id: 7, name: 'Rice', emoji: '', color: '#E0F2F1', route: 'rice', image: 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=120&h=120&fit=crop' },
  { id: 8, name: 'All Items', emoji: '', color: '#f5f5f5', route: 'all', image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=120&h=120&fit=crop' },
]);

// Order Again sellers
const orderAgain = ref([
  { id: 1, name: "Juan's Market", distance: '2.3 km', emoji: '', color: '#E8F5E9', isOpen: true, promo: '50 off', image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=200&h=150&fit=crop' },
  { id: 2, name: "Ana's Palengke", distance: '1.5 km', emoji: '', color: '#FCE4EC', isOpen: true, promo: null, image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=200&h=150&fit=crop' },
  { id: 3, name: "Rosa's Seafoods", distance: '3.1 km', emoji: '', color: '#E3F2FD', isOpen: false, promo: null, image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=200&h=150&fit=crop' },
]);

// Nearby Sellers
const nearbySellers = ref([
  {
    id: 1,
    name: "Juan's Fresh Palengke",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
    rating: '4.8',
    deliveryTime: '15-25 min',
    distance: '2.3 km',
    isOpen: true,
    discount: '50 OFF',
    tags: ['Vegetables', 'Fruits', 'Fresh']
  },
  {
    id: 2,
    name: "Ana's Palengke - Cogon",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #FF9800, #E65100)',
    rating: '4.6',
    deliveryTime: '10-20 min',
    distance: '1.5 km',
    isOpen: true,
    discount: null,
    tags: ['Fruits', 'Vegetables']
  },
  {
    id: 3,
    name: "Rosa's Seafoods & Meats",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #2196F3, #1565C0)',
    rating: '4.9',
    deliveryTime: '20-35 min',
    distance: '3.1 km',
    isOpen: false,
    discount: null,
    tags: ['Seafoods', 'Pork', 'Chicken']
  },
{
    id: 4,
    name: "Mario's Meat Shop",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #f44336, #b71c1c)',
    rating: '4.7',
    deliveryTime: '15-30 min',
    distance: '2.8 km',
    isOpen: true,
    discount: '30 OFF',
    tags: ['Pork', 'Beef', 'Chicken']
  },
  {
    id: 5,
    name: "Palengke Bundle Deals",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #9C27B0, #4A148C)',
    rating: '4.5',
    deliveryTime: '25-40 min',
    distance: '4.2 km',
    isOpen: true,
    discount: null,
    tags: ['Bundles', 'Tipid', 'Value']
  },
]);

// Methods
const openAddressPicker = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Delivery Address',
    buttons: [
      { text: '📍 Brgy. Carmen, CDO City', handler: () => { currentAddress.value = 'Brgy. Carmen, CDO City'; } },
      { text: '📍 Brgy. Lapasan, CDO City', handler: () => { currentAddress.value = 'Brgy. Lapasan, CDO City'; } },
      { text: '📍 Brgy. Macasandig, CDO City', handler: () => { currentAddress.value = 'Brgy. Macasandig, CDO City'; } },
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
const goToMessages = () => router.push('/messages');
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
  padding: 12px 20px 16px;
  display: flex; justify-content: space-between; align-items: flex-start;
}

.deliver-to { cursor: pointer; }
.deliver-label { font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
.deliver-address { display: flex; align-items: center; gap: 6px; color: white; font-size: 15px; font-weight: 700; }
.deliver-address ion-icon { font-size: 18px; }

.header-actions { display: flex; align-items: center; gap: 10px; margin-top: 4px; }
.header-btn {
  position: relative; width: 40px; height: 40px;
  background: rgba(255,255,255,0.2); border-radius: 50%;
  display: flex; align-items: center; justify-content: center; cursor: pointer;
}
.header-btn ion-icon { font-size: 22px; color: white; }
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
.feature-icon { font-size: 32px; }

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
  gap: 3px; cursor: pointer; color: #999;
  position: relative; transition: color 0.2s;
}
.nav-item ion-icon { font-size: 22px; }
.nav-item span { font-size: 10px; font-weight: 600; }
.nav-item.active { color: #4CAF50; }
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