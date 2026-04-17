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
            <span class="store-rating">⭐ {{ storeData.rating }}</span>
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
                <ion-icon :icon="arrowForwardOutline"></ion-icon>
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

  </ion-page>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage, IonContent, IonIcon,
  alertController, toastController
} from '@ionic/vue';
import {
  arrowBackOutline, searchOutline, heartOutline, heart,
  chevronForwardOutline, timeOutline, addOutline,
  closeCircleOutline, arrowForwardOutline
} from 'ionicons/icons';

const router = useRouter();
const route = useRoute();

const isFavorite = ref(false);
const showSearch = ref(true);
const searchQuery = ref('');
const selectedCategory = ref('all');
const searchInput = ref(null);
const cartItems = ref([]);

// Store Data
const storeData = ref({
  id: route.params.id || 1,
  name: "Juan's Fresh Market",
  emoji: '',
  color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
  rating: '4.8',
  reviewCount: 124,
  distance: '2.3 km',
  isOpen: true,
  openTime: 'Tomorrow, 06:00:00 AM',
  promo: 'Earn 500 Points!',
  minSpend: '₱300.00',
  deliveryFee: 45,
  deliveryTime: '15-25 min'
});

// Categories with products
const categories = ref([
  { id: 'all', name: 'All' },
  { id: 'vegetables', name: 'Vegetables' },
  { id: 'fruits', name: 'Fruits' },
  { id: 'seafoods', name: 'Fresh Frozen Seafoods' },
  { id: 'pork', name: 'Fresh Frozen Pork' },
  { id: 'chicken', name: 'Fresh Frozen Chicken' },
  { id: 'bundles', name: 'TIPID Bundle Deals' },
]);

const productCategories = ref([
  {
    id: 'vegetables',
    name: 'Vegetable',
    products: [
      { id: 1, name: 'Bawang/Garlic', image: 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=200&h=200&fit=crop', price: 66.00, unit: '250g', available: true, stock: 15 },
      { id: 2, name: 'Patatas/Potato', image: 'https://images.unsplash.com/photo-1595231776515-ddffb1f4eb73?w=200&h=200&fit=crop', price: 191.00, unit: '500g', available: true, stock: 8 },
      { id: 3, name: 'Sibuyas/Onion', image: 'https://images.unsplash.com/photo-1590005354167-6da97870c757?w=200&h=200&fit=crop', price: 110.00, unit: '250g', available: true, stock: 20 },
      { id: 4, name: 'Pipino/Cucumber', image: 'https://images.unsplash.com/photo-1449300079323-02e209d9d3a6?w=200&h=200&fit=crop', price: 73.00, unit: '500g', available: true, stock: 12 },
      { id: 5, name: 'Luya/Ginger', image: 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=200&h=200&fit=crop', price: 73.00, unit: '250g', available: true, stock: 6 },
      { id: 6, name: 'Pechay', image: 'https://images.unsplash.com/photo-1582273951693-3b5118239c78?w=200&h=200&fit=crop', price: 66.00, unit: '250g', available: true, stock: 25 },
      { id: 7, name: 'Kamatis/Tomato', image: 'https://images.unsplash.com/photo-1546470427-227c7b3f8fc2?w=200&h=200&fit=crop', price: 48.00, unit: '250g', available: true, stock: 30 },
      { id: 8, name: 'Batong/String Beans', image: 'https://images.unsplash.com/photo-1567375698348-5d9d5ae99de0?w=200&h=200&fit=crop', price: 95.00, unit: '500g', available: true, stock: 10 },
      { id: 9, name: 'Talong/Eggplant', image: 'https://images.unsplash.com/photo-1524593166156-311f36f65f8b?w=200&h=200&fit=crop', price: 88.00, unit: '500g', available: true, stock: 18 },
    ]
  },
  {
    id: 'fruits',
    name: 'Fruits',
    products: [
      { id: 10, name: 'Apple Med. Size (3pcs)', image: 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=200&h=200&fit=crop', price: 162.00, unit: '3pcs', available: true, stock: 20 },
      { id: 11, name: 'Kiat-Kiat', image: 'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?w=200&h=200&fit=crop', price: 184.00, unit: '1 per pack', available: true, stock: 15 },
      { id: 12, name: 'Banana (Large)', image: 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=200&h=200&fit=crop', price: 129.00, unit: '1000g', available: true, stock: 25 },
      { id: 13, name: 'Mango', image: 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=200&h=200&fit=crop', price: 155.00, unit: '500g', available: true, stock: 12 },
      { id: 14, name: 'Watermelon', image: 'https://images.unsplash.com/photo-1589984662646-e7b2e4962f18?w=200&h=200&fit=crop', price: 89.00, unit: '1kg', available: true, stock: 8 },
    ]
  },
  {
    id: 'seafoods',
    name: 'Fresh Frozen Seafoods',
    products: [
      { id: 15, name: 'Tuna Cubes', image: 'https://images.unsplash.com/photo-1574781330855-d0db8cc6a79c?w=200&h=200&fit=crop', price: 253.00, unit: '500g', available: true, stock: 10 },
      { id: 16, name: 'Pampano', image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=200&h=200&fit=crop', price: 303.00, unit: '1 per pack', available: true, stock: 8 },
      { id: 17, name: 'Salmon Strips', image: 'https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?w=200&h=200&fit=crop', price: 203.00, unit: '500g', available: true, stock: 6 },
      { id: 18, name: 'Hipon/Shrimp', image: 'https://images.unsplash.com/photo-1565680018093-ebb6b9d4d864?w=200&h=200&fit=crop', price: 320.00, unit: '500g', available: true, stock: 5 },
    ]
  },
  {
    id: 'pork',
    name: 'Fresh Frozen Pork Meats',
    products: [
      { id: 19, name: 'Pork Belly', image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=200&h=200&fit=crop', price: 287.00, unit: '500g', available: true, stock: 12 },
      { id: 20, name: 'Pork Adobo Cut', image: 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=200&h=200&fit=crop', price: 265.00, unit: '500g', available: true, stock: 8 },
      { id: 21, name: 'Pork Ribs', image: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=200&h=200&fit=crop', price: 310.00, unit: '500g', available: true, stock: 10 },
    ]
  },
  {
    id: 'chicken',
    name: 'Fresh Frozen Chicken',
    products: [
      { id: 22, name: 'Whole Dressed Chicken', image: 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=200&h=200&fit=crop', price: 304.00, unit: '1 per pack', available: true, stock: 15 },
      { id: 23, name: 'Chicken Wings', image: 'https://images.unsplash.com/photo-1606728035253-49e8a23146de?w=200&h=200&fit=crop', price: 320.00, unit: '1 per pack', available: true, stock: 20 },
      { id: 24, name: 'Chicken Drumsticks', image: 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=200&h=200&fit=crop', price: 257.00, unit: '1 per pack', available: true, stock: 18 },
    ]
  },
  {
    id: 'bundles',
    name: 'TIPID Bundle Deals',
    products: [
      { id: 25, name: 'Fresh Vegetable Bundle (Family)', image: 'https://images.unsplash.com/photo-1518843875459-f738682238a6?w=200&h=200&fit=crop', price: 257.00, unit: '1 per bundle', available: true, stock: 10 },
      { id: 26, name: 'Sour Soup Package (Sinigang)', image: 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?w=200&h=200&fit=crop', price: 270.00, unit: '1 per pack', available: true, stock: 8 },
      { id: 27, name: 'Stew Package (Afritada Style)', image: 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=200&h=200&fit=crop', price: 252.00, unit: '1 per pack', available: true, stock: 12 },
    ]
  },
]);

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
    duration: 1500, color: 'success', position: 'bottom'
  });
  await toast.present();
};

const openProduct = (product) => {
  if (!storeData.value.isOpen || !product.available) return;
  router.push(`/product/${product.id}`);
};

const viewAllCategory = (cat) => {
  selectedCategory.value = cat.id;
};

const toggleFavorite = async () => {
  isFavorite.value = !isFavorite.value;
  const toast = await toastController.create({
    message: isFavorite.value ? '❤️ Added to favorites!' : 'Removed from favorites',
    duration: 1500, color: isFavorite.value ? 'success' : 'dark', position: 'top'
  });
  await toast.present();
};

const showStoreInfo = async () => {
  const alert = await alertController.create({
    header: storeData.value.name,
    message: `⭐ Rating: ${storeData.value.rating}\n📍 Distance: ${storeData.value.distance}\n🛵 Delivery Fee: ₱${storeData.value.deliveryFee}\n⏱️ Delivery Time: ${storeData.value.deliveryTime}`,
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
.promo-banner {
  display: flex; align-items: center; gap: 12px;
  margin: 12px 20px;
  background: #E8F5E9; border-radius: 14px;
  padding: 12px 16px; cursor: pointer;
  border: 1px solid #C8E6C9;
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
  width: 32px; height: 32px;
  background: #E8F5E9; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #4CAF50;
}
.see-all-btn ion-icon { font-size: 18px; }

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
.basket-count {
  background: rgba(255,255,255,0.25);
  width: 28px; height: 28px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 800; color: white;
}
.basket-text { font-size: 16px; font-weight: 700; color: white; }
.basket-amount { font-size: 16px; font-weight: 800; color: white; }
</style>