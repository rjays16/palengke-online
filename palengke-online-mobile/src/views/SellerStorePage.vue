<template>
  <ion-page>
    <ion-content :fullscreen="true" class="store-content" @scroll="onScroll">

      <!-- ─── Hero Banner ────────────────────── -->
      <div class="hero-section">
        <div class="hero-banner" :style="{ background: storeData.color }">
          <img src="https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=800&h=400&fit=crop" alt="Store Banner" class="hero-image">
          <span class="hero-emoji" v-if="false">{{ storeData.emoji }}</span>
          <!-- Closed Badge -->
          <div class="closed-overlay" v-if="!storeData.isOpen">
            <span class="closed-badge">Closed</span>
          </div>
        </div>

        <!-- Back + Search + Fav -->
        <div class="hero-actions">
          <div class="hero-btn" @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </div>
          <div class="hero-search" @click="focusSearch">
            <ion-icon :icon="searchOutline"></ion-icon>
            <span>Search this store</span>
          </div>
          <div class="hero-btn" @click="toggleFavorite">
            <ion-icon :icon="isFavorite ? heart : heartOutline"
              :style="{ color: isFavorite ? '#f44336' : 'white' }"></ion-icon>
          </div>
        </div>
      </div>

      <!-- ─── Store Info Sheet ───────────────── -->
      <div class="store-sheet">

        <!-- Store Name & Info -->
        <div class="store-header" @click="showStoreInfo">
          <div class="store-title-row">
            <h1 class="store-name">{{ storeData.name }}</h1>
            <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
          </div>

          <div class="store-rating-row" @click.stop="showReviews">
            <span class="store-rating"><ion-icon :icon="starOutline"></ion-icon> {{ storeData.rating }}</span>
            <span class="store-reviews">({{ storeData.reviewCount }})</span>
            <span class="dot">•</span>
            <span class="ratings-link">Ratings and reviews</span>
            <ion-icon :icon="chevronForwardOutline" class="link-arrow"></ion-icon>
          </div>

          <div class="store-delivery-row">
            <div class="delivery-icon"><ion-icon :icon="bicycleOutline"></ion-icon></div>
            <div>
              <div class="delivery-dist">{{ storeData.distance }}</div>
              <div class="delivery-label">Delivery</div>
            </div>
          </div>
          <div class="report-link" @click.stop="reportVendor">
            <ion-icon :icon="flagOutline"></ion-icon>
            <span>Report this store</span>
          </div>

          <!-- Closed Notice -->
          <div class="closed-notice" v-if="!storeData.isOpen">
            <ion-icon :icon="timeOutline"></ion-icon>
            <span>This store will open on {{ storeData.openTime }}</span>
          </div>
        </div>

        <!-- Promo Banner -->
        <div class="promo-banner" v-if="storeData.promo" @click="showPromoDetails">
          <div class="promo-icon"><ion-icon :icon="pricetagOutline"></ion-icon></div>
          <div class="promo-text">
            <p>{{ storeData.promo }}</p>
            <span>Min. spend {{ storeData.minSpend }}</span>
          </div>
          <ion-icon :icon="chevronForwardOutline"></ion-icon>
        </div>

        <!-- Category Chips -->
        <div class="category-tabs-wrap">
          <div class="category-tabs">
            <div
              v-for="cat in categories"
              :key="cat.id"
              class="cat-tab"
              :class="{ active: selectedCategory === cat.id }"
              @click="selectCategory(cat.id)"
            >
              {{ cat.name }}
            </div>
          </div>
        </div>

        <!-- Search Bar (expandable) -->
        <div class="store-search" v-if="showSearch">
          <ion-icon :icon="searchOutline" class="search-icon"></ion-icon>
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="text"
            placeholder="Search this store"
            class="search-input"
            @input="onSearchInput"
          >
          <ion-icon
            v-if="searchQuery"
            :icon="closeCircleOutline"
            class="clear-icon"
            @click="clearSearch"
          ></ion-icon>
        </div>

        <!-- ─── Product Sections ────────────── -->
        <div class="products-container">

          <div
            v-for="category in filteredCategories"
            :key="category.id"
            class="product-section"
            :id="`section-${category.id}`"
          >
            <!-- Section Header -->
            <div class="section-header">
              <h2 class="section-title">{{ category.name }}</h2>
              <span class="see-all-btn" @click="viewAllCategory(category)">
                View All
                <ion-icon :icon="chevronForwardOutline" class="see-all-arrow"></ion-icon>
              </span>
            </div>

            <!-- Products Grid -->
            <div class="products-scroll">
              <div
                v-for="product in category.products"
                :key="product.id"
                class="product-card"
                :class="{ unavailable: !storeData.isOpen || !product.available }"
                @click="openProduct(product)"
              >
                <!-- Product Image -->
                <div class="product-img-wrap">
                  <div class="product-img">
                    <img :src="product.image" :alt="product.name" class="product-img-src">
                  </div>
                  <!-- Add button -->
                  <div
                    class="add-btn"
                    v-if="storeData.isOpen && product.available"
                    @click.stop="addToCart(product)"
                  >
                    <ion-icon :icon="addOutline"></ion-icon>
                  </div>
                  <!-- Cart count -->
                  <div class="cart-count" v-if="getCartCount(product.id) > 0">
                    {{ getCartCount(product.id) }}
                  </div>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                  <p class="product-price">₱{{ product.price.toFixed(2) }}</p>
                  <p class="product-name">{{ product.name }}</p>
                  <p class="product-unit">{{ product.unit }}</p>
                  <p class="product-stock" v-if="product.stock !== undefined && product.stock > 0">
                    {{ product.stock }} left
                  </p>
                  <p class="product-unavailable" v-if="!storeData.isOpen || !product.available">
                    Unavailable
                  </p>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Bottom spacer -->
        <div style="height: 120px;"></div>

      </div>

    </ion-content>

    <!-- ─── Floating Basket Button ─────────── -->
    <div class="basket-float" v-if="cartItems.length > 0 && storeData.isOpen" @click="goToCart">
      <div class="basket-left">
        <span class="basket-count">{{ totalCartItems }}</span>
        <span class="basket-text">Basket</span>
      </div>
      <span class="basket-amount">₱{{ totalCartAmount.toFixed(2) }}</span>
    </div>

    <input type="file" ref="vendorPhotoInput" accept="image/*" @change="onVendorPhotoSelected" hidden>

    <div class="custom-modal-overlay" v-if="showReportSuccess" @click="showReportSuccess = false">
      <div class="custom-modal" @click.stop>
        <img src="/assets/images/checkmark_success.png" alt="Success" class="success-img">
        <h3>Report submitted!</h3>
        <p>We will review it shortly.</p>
        <ion-button expand="block" class="modal-btn" @click="showReportSuccess = false">OK</ion-button>
      </div>
    </div>

  </ion-page>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage, IonContent, IonIcon,
  alertController, toastController, actionSheetController
} from '@ionic/vue';
import {
  arrowBackOutline, searchOutline, heartOutline, heart,
  chevronForwardOutline, timeOutline, addOutline,
  closeCircleOutline, arrowForwardOutline, flagOutline,
  starOutline
} from 'ionicons/icons';
import { submitReport, readFileAsDataURL } from '../services/reportService';

const router = useRouter();
const route = useRoute();

const isFavorite = ref(false);
const showSearch = ref(true);
const searchQuery = ref('');
const selectedCategory = ref('all');
const searchInput = ref(null);
const cartItems = ref([]);

import { storeData as mockStoreData, categories as mockCategories, productCategories as mockProductCategories } from '@/mock/sellerStore';

// Store Data
const storeData = ref({ ...mockStoreData, id: route.params.id || 1 });

const categories = ref(mockCategories);
const productCategories = ref(mockProductCategories);

// Computed
const filteredCategories = computed(() => {
  let cats = productCategories.value;

  // Category filter
  if (selectedCategory.value !== 'all') {
    cats = cats.filter(c => c.id === selectedCategory.value);
  }

  // Search filter
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    cats = cats.map(cat => ({
      ...cat,
      products: cat.products.filter(p =>
        p.name.toLowerCase().includes(q)
      )
    })).filter(cat => cat.products.length > 0);
  }

  return cats;
});

const totalCartItems = computed(() =>
  cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
);

const totalCartAmount = computed(() =>
  cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
);

// Methods
const selectCategory = (catId) => {
  selectedCategory.value = catId;
  if (catId !== 'all') {
    const el = document.getElementById(`section-${catId}`);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
  }
};

const focusSearch = () => {
  showSearch.value = true;
  nextTick(() => searchInput.value?.focus());
};

const clearSearch = () => { searchQuery.value = ''; };
const onSearchInput = () => { selectedCategory.value = 'all'; };

const getCartCount = (productId) => {
  const item = cartItems.value.find(i => i.id === productId);
  return item ? item.quantity : 0;
};

const addToCart = async (product) => {
  if (!storeData.value.isOpen) return;
  const existing = cartItems.value.find(i => i.id === product.id);
  if (existing) {
    existing.quantity++;
  } else {
    cartItems.value.push({ ...product, quantity: 1 });
  }
  const toast = await toastController.create({
    message: `${product.name} added to basket`,
    duration: 1500, color: 'success', position: 'top'
  });
  await toast.present();
};

const openProduct = (product) => {
  if (!storeData.value.isOpen || !product.available) return;
  router.push(`/product/${product.id}`);
};

const viewAllCategory = (cat) => {
  router.push(`/category/${cat.id}`);
};

const toggleFavorite = async () => {
  isFavorite.value = !isFavorite.value;
  const toast = await toastController.create({
    message: isFavorite.value ? 'Added to favorites!' : 'Removed from favorites',
    duration: 1500, color: isFavorite.value ? 'success' : 'dark', position: 'top'
  });
  await toast.present();
};

const showStoreInfo = async () => {
  const alert = await alertController.create({
    header: storeData.value.name,
    message: `Rating: ${storeData.value.rating}\nDistance: ${storeData.value.distance}\nDelivery Fee: ₱${storeData.value.deliveryFee}\nDelivery Time: ${storeData.value.deliveryTime}`,
    buttons: ['Close']
  });
  await alert.present();
};

const showReviews = () => {};
const showPromoDetails = async () => {
  const alert = await alertController.create({
    header: 'Promo Details',
    message: `${storeData.value.promo}\nMinimum spend: ${storeData.value.minSpend}`,
    buttons: ['Got it!']
  });
  await alert.present();
};

const onScroll = () => {};
const goBack = () => router.back();
const goToCart = () => router.push('/cart');

const showReportSuccess = ref(false)
const vendorPhoto = ref('')
const vendorPhotoInput = ref(null)

const onVendorPhotoSelected = (e) => {
  const file = e.target.files?.[0]
  if (file) readFileAsDataURL(file).then(url => { vendorPhoto.value = url })
}

const reportVendor = async () => {
  const alert = await alertController.create({
    header: 'Report this Store',
    subHeader: storeData.value.name,
    message: vendorPhoto.value ? 'Photo attached' : '',
    inputs: [
      { name: 'subject', type: 'text', placeholder: 'Short summary' },
      { name: 'description', type: 'textarea', placeholder: 'Describe the issue...' }
    ],
    buttons: [
      { text: vendorPhoto.value ? 'Change Photo' : 'Add Photo', handler: () => { vendorPhotoInput.value?.click(); return false } },
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Submit Report',
        handler: async (data) => {
          if (!data.description) {
            const toast = await toastController.create({
              message: 'Please describe the issue', duration: 2000, color: 'warning', position: 'top'
            });
            await toast.present();
            return false
          }
          submitReport({
            type: 'seller',
            submittedBy: storeData.value.name,
            subject: data.subject || 'Report: ' + storeData.value.name,
            description: data.description,
            image: vendorPhoto.value || undefined,
            priority: 'medium'
          })
          vendorPhoto.value = ''
          showReportSuccess.value = true
        }
      }
    ]
  });
  await alert.present();
};
</script>

<style scoped>
.store-content { --background: #f8f9fa; }

/* ─── Hero Banner ────────────────────────────── */
.hero-section { position: relative; }

.hero-banner {
  height: 200px; display: flex;
  align-items: center; justify-content: center;
  position: relative;
}
.hero-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-emoji { font-size: 80px; }

.closed-overlay {
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex; align-items: center; justify-content: center;
}
.closed-badge {
  background: #f44336; color: white;
  padding: 8px 24px; border-radius: 20px;
  font-size: 16px; font-weight: 700;
}

/* Hero Action Buttons */
.hero-actions {
  position: absolute; top: 0; left: 0; right: 0;
  display: flex; align-items: center; gap: 10px;
  padding: 52px 16px 12px;
}

.hero-btn {
  width: 40px; height: 40px; flex-shrink: 0;
  background: rgba(0,0,0,0.35); border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; backdrop-filter: blur(4px);
}
.hero-btn ion-icon { font-size: 22px; color: white; }

.hero-search {
  flex: 1; background: rgba(255,255,255,0.9);
  border-radius: 12px; padding: 10px 16px;
  display: flex; align-items: center; gap: 8px;
  cursor: pointer;
}
.hero-search ion-icon { font-size: 18px; color: #999; }
.hero-search span { font-size: 14px; color: #999; }

/* ─── Store Sheet ────────────────────────────── */
.store-sheet {
  background: white;
  border-radius: 24px 24px 0 0;
  margin-top: -24px;
  position: relative; z-index: 10;
  min-height: 100vh;
}

/* Store Header */
.store-header {
  padding: 20px 20px 0;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
}

.store-title-row {
  display: flex; justify-content: space-between;
  align-items: center; margin-bottom: 10px;
}
.store-name { font-size: 22px; font-weight: 800; color: #1a1a1a; margin: 0; }
.info-arrow { font-size: 20px; color: #ccc; }

.store-rating-row {
  display: flex; align-items: center; gap: 6px;
  margin-bottom: 12px; cursor: pointer;
}
.store-rating { font-size: 14px; font-weight: 700; color: #1a1a1a; }
.store-reviews { font-size: 13px; color: #666; }
.dot { color: #ccc; }
.ratings-link { font-size: 13px; color: #4CAF50; font-weight: 600; }
.link-arrow { font-size: 14px; color: #4CAF50; }

.store-delivery-row {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 0; border-top: 1px solid #f5f5f5;
}
.delivery-icon { width: 40px; height: 40px; font-size: 24px; display: flex; align-items: center; justify-content: center; }
.delivery-dist { font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
.delivery-label { font-size: 12px; color: #666; }

.closed-notice {
  display: flex; align-items: center; gap: 8px;
  background: #FFF3E0; border-radius: 10px;
  padding: 10px 14px; margin: 12px 0;
}
.closed-notice ion-icon { font-size: 18px; color: #FF9800; }
.closed-notice span { font-size: 13px; color: #E65100; font-weight: 500; }

/* Promo Banner */
.report-link { display: flex; align-items: center; gap: 6px; padding: 8px 0 12px; cursor: pointer; color: #999; font-size: 13px; border-bottom: 1px solid #f0f0f0; margin-bottom: 4px; }
.report-link:hover { color: #f44336; }
.report-link ion-icon { font-size: 16px; }

.promo-banner {
  margin: 0 0 8px; background: #FFF8E1;
  border-radius: 12px; padding: 12px 14px;
  display: flex; align-items: center; gap: 10px;
  cursor: pointer;
}
.promo-icon { font-size: 28px; }
.promo-text { flex: 1; }
.promo-text p { font-size: 14px; font-weight: 700; color: #2E7D32; margin: 0 0 2px 0; }
.promo-text span { font-size: 12px; color: #4CAF50; }
.promo-banner ion-icon { font-size: 18px; color: #4CAF50; }

/* ─── Category Tabs ──────────────────────────── */
.category-tabs-wrap {
  padding: 12px 0 0;
  border-bottom: 1px solid #f0f0f0;
}
.category-tabs {
  display: flex; gap: 0;
  overflow-x: auto; padding: 0 16px 12px;
}
.category-tabs::-webkit-scrollbar { display: none; }

.cat-tab {
  padding: 8px 16px; border-radius: 20px;
  font-size: 13px; font-weight: 700;
  color: #666; cursor: pointer; white-space: nowrap;
  transition: all 0.2s; flex-shrink: 0;
  background: #f5f5f5; margin-right: 8px;
}
.cat-tab.active { background: #4CAF50; color: white; }

/* ─── Store Search ───────────────────────────── */
.store-search {
  position: relative; margin: 12px 16px;
}
.search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 18px; color: #999; z-index: 1; }
.search-input {
  width: 100%; padding: 13px 40px 13px 42px;
  background: #f5f5f5; border: none; border-radius: 12px;
  font-size: 15px; font-family: inherit;
  color: #1a1a1a !important; -webkit-text-fill-color: #1a1a1a !important;
}
.search-input:focus { outline: none; }
.search-input::placeholder { color: #999; }
.clear-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 18px; color: #999; cursor: pointer; }

/* ─── Products ───────────────────────────────── */
.products-container { padding: 8px 0; }

.product-section { margin-bottom: 8px; }

.section-header {
  display: flex; justify-content: space-between;
  align-items: center; padding: 16px 20px 12px;
}
.section-title { font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0; }
.see-all-btn {
  display: flex; align-items: center; gap: 4px;
  padding: 6px 12px;
  background: #E8F5E9; border-radius: 20px;
  cursor: pointer; color: #4CAF50;
  font-size: 13px; font-weight: 600;
}
.see-all-arrow { font-size: 16px; }

/* Products horizontal scroll */
.products-scroll {
  display: flex; gap: 12px;
  padding: 0 16px 16px;
  overflow-x: auto;
}
.products-scroll::-webkit-scrollbar { display: none; }

/* Product Card */
.product-card { flex-shrink: 0; width: 140px; cursor: pointer; }
.product-card.unavailable { opacity: 0.6; }
.product-card:active { transform: scale(0.97); }

.product-img-wrap { position: relative; margin-bottom: 10px; }

.product-img {
  width: 140px; height: 120px;
  background: #f8f9fa; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  border: 1px solid #f0f0f0;
}
.product-img-src {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 14px;
}

.product-emoji { font-size: 52px; }

.add-btn {
  position: absolute; bottom: 8px; right: 8px;
  width: 32px; height: 32px;
  background: #4CAF50; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 3px 10px rgba(76,175,80,0.4);
  cursor: pointer;
}
.add-btn ion-icon { font-size: 20px; color: white; }

.cart-count {
  position: absolute; top: -6px; right: -6px;
  background: #4CAF50; color: white;
  width: 22px; height: 22px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700;
  border: 2px solid white;
}

.product-info { padding: 0 4px; }
.product-price { font-size: 16px; font-weight: 800; color: #1a1a1a; margin: 0 0 3px 0; }
.product-name { font-size: 13px; color: #333; margin: 0 0 2px 0; line-height: 1.3; font-weight: 600; }
.product-unit { font-size: 12px; color: #999; margin: 0 0 2px 0; }
.product-stock { font-size: 11px; color: #4CAF50; font-weight: 600; margin: 0; }
.product-unavailable { font-size: 12px; color: #999; margin: 0; }

/* ─── Floating Basket ────────────────────────── */
.basket-float {
  position: fixed; bottom: 20px; left: 16px; right: 16px;
  background: #4CAF50; border-radius: 16px;
  padding: 16px 20px;
  display: flex; justify-content: space-between; align-items: center;
  cursor: pointer; z-index: 1000;
  box-shadow: 0 6px 24px rgba(76,175,80,0.5);
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from { transform: translateY(100px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.basket-float:active { transform: scale(0.98); }
.basket-left { display: flex; align-items: center; gap: 12px; }

.custom-modal-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5); display: flex;
  align-items: center; justify-content: center;
  z-index: 99999; padding: 32px;
}
.custom-modal {
  background: white; border-radius: 24px;
  padding: 40px 32px 24px; text-align: center;
  width: 100%; max-width: 320px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.success-img { width: 80px; height: 80px; margin: 0 auto 16px; display: block; }
.custom-modal h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px; }
.custom-modal p { font-size: 14px; color: #666; margin: 0 0 24px; }
.modal-btn { --background: #4CAF50; --border-radius: 12px; height: 48px; font-weight: 600; text-transform: none; font-family: inherit; }
.basket-count {
  background: rgba(255,255,255,0.25);
  width: 28px; height: 28px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 800; color: white;
}
.basket-text { font-size: 16px; font-weight: 700; color: white; }
.basket-amount { font-size: 16px; font-weight: 800; color: white; }
</style>