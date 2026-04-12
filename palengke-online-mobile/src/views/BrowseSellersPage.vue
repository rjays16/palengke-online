<template>
  <ion-page>
    <ion-content :fullscreen="true" class="browse-content">

      <!-- ─── Search Header ──────────────────── -->
      <div class="search-header">
        <div class="search-row">
          <div class="back-btn" @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </div>
          <div class="search-bar">
            <ion-icon :icon="searchOutline" class="search-icon"></ion-icon>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="What shall we deliver?"
              class="search-input"
              @input="onSearch"
            >
            <ion-icon
              v-if="searchQuery"
              :icon="closeCircleOutline"
              class="clear-icon"
              @click="clearSearch"
            ></ion-icon>
          </div>
        </div>

        <!-- ─── Filter Chips ─────────────────── -->
        <div class="filter-chips">
          <div class="filter-chip" @click="showSortSheet">
            <ion-icon :icon="swapVerticalOutline"></ion-icon>
            <span>Sort By</span>
            <ion-icon :icon="chevronDownOutline"></ion-icon>
          </div>
          <div class="filter-chip" @click="showCategorySheet">
            <ion-icon :icon="gridOutline"></ion-icon>
            <span>Category</span>
            <ion-icon :icon="chevronDownOutline"></ion-icon>
          </div>
          <div
            class="filter-chip"
            :class="{ active: activeFilter === 'nearby' }"
            @click="toggleFilter('nearby')"
          >
            <ion-icon :icon="locationOutline"></ion-icon>
            <span>Nearby</span>
          </div>
          <div
            class="filter-chip"
            :class="{ active: activeFilter === 'free-delivery' }"
            @click="toggleFilter('free-delivery')"
          >
            <ion-icon :icon="bicycleOutline"></ion-icon>
            <span>Free Delivery</span>
          </div>
          <div
            class="filter-chip"
            :class="{ active: activeFilter === 'open' }"
            @click="toggleFilter('open')"
          >
            <span>Open Now</span>
          </div>
        </div>
      </div>

      <!-- ─── Results ────────────────────────── -->
      <div class="results-container">

        <!-- Results count -->
        <div class="results-count" v-if="!isSearching">
          <span>{{ filteredSellers.length }} sellers found</span>
        </div>

        <!-- Empty state -->
        <div v-if="filteredSellers.length === 0" class="empty-state">
          <div class="empty-icon">🔍</div>
          <h3>No sellers found</h3>
          <p>Try a different search or filter</p>
          <ion-button fill="outline" color="success" @click="clearAll">Clear Filters</ion-button>
        </div>

        <!-- Sellers List -->
        <div class="sellers-list" v-else>
          <div
            v-for="seller in filteredSellers"
            :key="seller.id"
            class="seller-card"
            @click="openSeller(seller)"
          >
            <!-- Banner -->
            <div class="seller-banner" :style="{ background: seller.color }">
              <span class="banner-emoji" v-if="false">{{ seller.emoji }}</span>
              <div class="closed-badge" v-if="!seller.isOpen">
                <span>Closed</span>
              </div>
              <div class="discount-badge" v-if="seller.discount">{{ seller.discount }}</div>
              <div class="free-delivery-badge" v-if="seller.freeDelivery"><ion-icon :icon="bicycleOutline"></ion-icon> Free Delivery</div>
            </div>

            <!-- Info -->
            <div class="seller-info">
              <div class="seller-top">
                <h3 class="seller-name">{{ seller.name }}</h3>
                <div class="seller-options" @click.stop="showSellerOptions(seller)">
                  <ion-icon :icon="ellipsisVerticalOutline"></ion-icon>
                </div>
              </div>

              <div class="seller-meta">
                <span class="rating">⭐ {{ seller.rating }}</span>
                <span class="rating-count">({{ seller.reviewCount }})</span>
                <span class="dot">•</span>
                <span class="category">{{ seller.category }}</span>
              </div>

              <div class="seller-details">
                <span class="detail-item">
                  <ion-icon :icon="timeOutline"></ion-icon>
                  {{ seller.deliveryTime }}
                </span>
                <span class="detail-item">
                  <ion-icon :icon="locationOutline"></ion-icon>
                  {{ seller.distance }}
                </span>
                <span class="detail-item delivery-fee">
                  <ion-icon :icon="bicycleOutline"></ion-icon>
                  {{ seller.freeDelivery ? 'Free' : '₱' + seller.deliveryFee }}
                </span>
              </div>

              <!-- Promo Tag -->
              <div class="promo-tag" v-if="seller.promo">
                <ion-icon :icon="pricetagOutline"></ion-icon>
                <span>{{ seller.promo }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>

    </ion-content>

    <!-- ─── Bottom Nav ─────────────────────── -->
    <ion-footer>
      <ion-toolbar class="bottom-nav-toolbar">
        <div class="bottom-nav">
          <div class="nav-item" @click="goToHome">
            <ion-icon :icon="homeOutline"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item active">
            <ion-icon :icon="searchOutline"></ion-icon>
            <span>Browse</span>
          </div>
          <div class="nav-item" @click="goToCart">
            <ion-icon :icon="cartOutline"></ion-icon>
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
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonContent, IonFooter, IonToolbar, IonIcon, IonButton,
  actionSheetController, toastController
} from '@ionic/vue';
import {
  arrowBackOutline, searchOutline, closeCircleOutline,
  swapVerticalOutline, chevronDownOutline, gridOutline,
  locationOutline, timeOutline, bicycleOutline, pricetagOutline,
  ellipsisVerticalOutline, homeOutline, cartOutline,
  receiptOutline, personOutline
} from 'ionicons/icons';

const router = useRouter();

const searchQuery = ref('');
const activeFilter = ref('');
const isSearching = ref(false);
const selectedSort = ref('recommended');
const selectedCategory = ref('all');

// All sellers data
const allSellers = ref([
  {
    id: 1, name: "Juan's Fresh Market", emoji: '',
    color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
    rating: '4.8', reviewCount: 124, category: 'Vegetables & Fruits',
    deliveryTime: '15-25 min', distance: '2.3 km',
    deliveryFee: 45, freeDelivery: false,
    isOpen: true, discount: 'OFF', promo: 'off min. 300 order',
    tags: ['vegetables', 'fruits']
  },
  {
    id: 2, name: "Ana's Palengke - Cogon", emoji: '',
    color: 'linear-gradient(135deg, #FF9800, #E65100)',
    rating: '4.6', reviewCount: 89, category: 'Fruits & Vegetables',
    deliveryTime: '10-20 min', distance: '1.5 km',
    deliveryFee: 0, freeDelivery: true,
    isOpen: true, discount: null, promo: null,
    tags: ['fruits', 'vegetables']
  },
  {
    id: 3, name: "Rosa's Seafoods & Meats", emoji: '',
    color: 'linear-gradient(135deg, #2196F3, #1565C0)',
    rating: '4.9', reviewCount: 203, category: 'Seafoods & Meats',
    deliveryTime: '20-35 min', distance: '3.1 km',
    deliveryFee: 60, freeDelivery: false,
    isOpen: false, discount: null, promo: null,
    tags: ['seafoods', 'pork', 'chicken']
  },
  {
    id: 4, name: "Mario's Meat Shop", emoji: '',
    color: 'linear-gradient(135deg, #f44336, #b71c1c)',
    rating: '4.7', reviewCount: 156, category: 'Pork & Beef',
    deliveryTime: '15-30 min', distance: '2.8 km',
    deliveryFee: 0, freeDelivery: true,
    isOpen: true, discount: 'OFF', promo: 'off your first order',
    tags: ['pork', 'beef', 'chicken']
  },
  {
    id: 5, name: "Palengke Bundle Deals", emoji: '',
    color: 'linear-gradient(135deg, #9C27B0, #4A148C)',
    rating: '4.5', reviewCount: 67, category: 'Bundle Deals',
    deliveryTime: '25-40 min', distance: '4.2 km',
    deliveryFee: 55, freeDelivery: false,
    isOpen: true, discount: null, promo: null,
    tags: ['bundles']
  },
  {
    id: 6, name: "CDO Fresh Chicken Hub", emoji: '',
    color: 'linear-gradient(135deg, #FF9800, #F57F17)',
    rating: '4.4', reviewCount: 91, category: 'Chicken & Poultry',
    deliveryTime: '15-25 min', distance: '1.8 km',
    deliveryFee: 0, freeDelivery: true,
    isOpen: true, discount: null, promo: 'Buy 2 get 1 free!',
    tags: ['chicken']
  },
]);

const filteredSellers = computed(() => {
  let list = [...allSellers.value];

  // Search filter
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(s =>
      s.name.toLowerCase().includes(q) ||
      s.category.toLowerCase().includes(q) ||
      s.tags.some(t => t.includes(q))
    );
  }

  // Active filter
  if (activeFilter.value === 'nearby') {
    list = list.sort((a, b) => parseFloat(a.distance) - parseFloat(b.distance));
  }
  if (activeFilter.value === 'free-delivery') {
    list = list.filter(s => s.freeDelivery);
  }
  if (activeFilter.value === 'open') {
    list = list.filter(s => s.isOpen);
  }

  // Category filter
  if (selectedCategory.value !== 'all') {
    list = list.filter(s => s.tags.includes(selectedCategory.value));
  }

  // Sort
  if (selectedSort.value === 'rating') {
    list = list.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
  } else if (selectedSort.value === 'delivery-fee') {
    list = list.sort((a, b) => a.deliveryFee - b.deliveryFee);
  } else if (selectedSort.value === 'delivery-time') {
    list = list.sort((a, b) => parseInt(a.deliveryTime) - parseInt(b.deliveryTime));
  }

  return list;
});

const onSearch = () => { isSearching.value = searchQuery.value.length > 0; };
const clearSearch = () => { searchQuery.value = ''; isSearching.value = false; };
const clearAll = () => { searchQuery.value = ''; activeFilter.value = ''; selectedCategory.value = 'all'; };
const toggleFilter = (filter) => { activeFilter.value = activeFilter.value === filter ? '' : filter; };

const showSortSheet = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Sort By',
    buttons: [
      { text: selectedSort.value === 'recommended' ? 'Recommended' : 'Recommended', handler: () => { selectedSort.value = 'recommended'; } },
      { text: selectedSort.value === 'rating' ? 'Rating' : 'Rating', handler: () => { selectedSort.value = 'rating'; } },
      { text: selectedSort.value === 'delivery-fee' ? 'Delivery Fee' : 'Delivery Fee', handler: () => { selectedSort.value = 'delivery-fee'; } },
      { text: selectedSort.value === 'delivery-time' ? 'Delivery Time' : 'Delivery Time', handler: () => { selectedSort.value = 'delivery-time'; } },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const showCategorySheet = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Category',
    buttons: [
      { text: 'All', handler: () => { selectedCategory.value = 'all'; } },
      { text: 'Vegetables', handler: () => { selectedCategory.value = 'vegetables'; } },
      { text: 'Fruits', handler: () => { selectedCategory.value = 'fruits'; } },
      { text: 'Seafoods', handler: () => { selectedCategory.value = 'seafoods'; } },
      { text: 'Pork & Beef', handler: () => { selectedCategory.value = 'pork'; } },
      { text: 'Chicken', handler: () => { selectedCategory.value = 'chicken'; } },
      { text: 'Bundles', handler: () => { selectedCategory.value = 'bundles'; } },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const showSellerOptions = async (seller) => {
  const actionSheet = await actionSheetController.create({
    header: seller.name,
    buttons: [
      { text: 'Add to Favorites', handler: () => {} },
      { text: '🔗 Share', handler: () => {} },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const openSeller = async (seller) => {
  if (!seller.isOpen) {
    const toast = await toastController.create({
      message: `${seller.name} is currently closed`,
      duration: 2000, color: 'dark', position: 'top'
    });
    await toast.present();
    return;
  }
  router.push(`/seller/${seller.id}`);
};

const goBack = () => router.push('/home');
const goToHome = () => router.push('/home');
const goToCart = () => router.push('/cart');
const goToActivity = () => router.push('/activity');
const goToProfile = () => router.push('/profile');
</script>

<style scoped>
.browse-content { 
  --background: #f8f9fa;
  --padding-top: 0px;
  --padding-start: 0px;
  --padding-end: 0px;
}

.browse-content .scroll-content {
  padding-top: 0px !important;
}

/* ─── Search Header ──────────────────────────── */
.search-header {
  background: white;
  padding: 4px 16px 12px;
  border-bottom: 1px solid #f0f0f0;
  position: sticky; top: 0; z-index: 100;
}

.search-row { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }

.back-btn {
  width: 40px; height: 40px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  background: #f5f5f5; border-radius: 12px; cursor: pointer;
}
.back-btn ion-icon { font-size: 22px; color: #1a1a1a; }

.search-bar {
  flex: 1; position: relative;
  background: #f5f5f5; border-radius: 14px;
  display: flex; align-items: center;
}
.search-icon { position: absolute; left: 14px; font-size: 18px; color: #999; z-index: 1; }
.search-input {
  width: 100%; padding: 13px 40px 13px 42px;
  background: transparent; border: none;
  font-size: 15px; font-family: inherit;
  color: #1a1a1a !important;
  -webkit-text-fill-color: #1a1a1a !important;
}
.search-input:focus { outline: none; }
.search-input::placeholder { color: #999; }
.clear-icon { position: absolute; right: 14px; font-size: 18px; color: #999; cursor: pointer; }

/* ─── Filter Chips ───────────────────────────── */
.filter-chips {
  display: flex; gap: 8px;
  overflow-x: auto; padding-bottom: 4px;
}
.filter-chips::-webkit-scrollbar { display: none; }

.filter-chip {
  display: flex; align-items: center; gap: 4px;
  padding: 7px 12px; border-radius: 20px;
  background: #f5f5f5; border: 1px solid #e0e0e0;
  font-size: 13px; font-weight: 600; color: #555;
  cursor: pointer; white-space: nowrap; transition: all 0.2s; flex-shrink: 0;
}
.filter-chip ion-icon { font-size: 14px; }
.filter-chip.active { background: #E8F5E9; border-color: #4CAF50; color: #4CAF50; }

/* ─── Results ────────────────────────────────── */
.results-container { padding: 12px 16px 16px; }
.results-count { font-size: 13px; color: #999; font-weight: 600; margin-bottom: 16px; }

/* ─── Seller Card ────────────────────────────── */
.sellers-list { display: flex; flex-direction: column; gap: 16px; }

.seller-card {
  background: white; border-radius: 18px;
  overflow: hidden; cursor: pointer;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  transition: transform 0.2s;
}
.seller-card:active { transform: scale(0.98); }

.seller-banner {
  height: 130px; display: flex; align-items: center;
  justify-content: center; position: relative;
}
.banner-emoji { font-size: 58px; }

.closed-badge {
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5); display: flex;
  align-items: center; justify-content: center;
}
.closed-badge span {
  background: rgba(0,0,0,0.7); color: white;
  padding: 6px 16px; border-radius: 20px;
  font-size: 14px; font-weight: 700;
}

.discount-badge {
  position: absolute; top: 10px; right: 10px;
  background: #4CAF50; color: white;
  padding: 4px 10px; border-radius: 10px;
  font-size: 12px; font-weight: 700;
}

.free-delivery-badge {
  position: absolute; bottom: 10px; left: 10px;
  background: rgba(255,255,255,0.9); color: #4CAF50;
  padding: 4px 10px; border-radius: 10px;
  font-size: 11px; font-weight: 700;
}

/* Seller Info */
.seller-info { padding: 14px 16px; }
.seller-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; }
.seller-name { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0; }
.seller-options { padding: 4px; cursor: pointer; color: #999; }
.seller-options ion-icon { font-size: 20px; }

.seller-meta { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
.rating { font-size: 13px; font-weight: 700; color: #1a1a1a; }
.rating-count { font-size: 12px; color: #999; }
.dot { color: #ccc; }
.category { font-size: 12px; color: #666; }

.seller-details { display: flex; gap: 14px; margin-bottom: 8px; flex-wrap: wrap; }
.detail-item { display: flex; align-items: center; gap: 4px; font-size: 13px; color: #555; font-weight: 500; }
.detail-item ion-icon { font-size: 14px; color: #4CAF50; }
.delivery-fee { color: #4CAF50; font-weight: 600; }

.promo-tag {
  display: inline-flex; align-items: center; gap: 6px;
  background: #FFF3E0; border-radius: 8px; padding: 5px 10px;
}
.promo-tag ion-icon { font-size: 14px; color: #FF9800; }
.promo-tag span { font-size: 12px; color: #E65100; font-weight: 600; }

/* Empty State */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 64px; margin-bottom: 16px; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0 0 24px 0; }

/* Bottom Nav */
.bottom-nav-toolbar { --background: white; box-shadow: 0 -2px 12px rgba(0,0,0,0.08); }
.bottom-nav { display: grid; grid-template-columns: repeat(5, 1fr); height: 60px; }
.nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; cursor: pointer; color: #999; }
.nav-item ion-icon { font-size: 22px; }
.nav-item span { font-size: 10px; font-weight: 600; }
.nav-item.active { color: #4CAF50; }
.nav-item:active { transform: scale(0.95); }
</style>