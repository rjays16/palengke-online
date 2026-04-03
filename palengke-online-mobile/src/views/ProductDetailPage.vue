<template>
  <ion-page>
    <ion-content :fullscreen="true" class="product-content">

      <!-- ─── Product Image ──────────────────── -->
      <div class="product-hero">
        <div class="product-image-bg">
          <span class="product-emoji-large">{{ product.emoji }}</span>
        </div>

        <!-- Actions -->
        <div class="hero-actions">
          <div class="action-btn" @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </div>
          <div class="action-btn" @click="shareProduct">
            <ion-icon :icon="shareOutline"></ion-icon>
          </div>
        </div>
      </div>

      <!-- ─── Product Info ───────────────────── -->
      <div class="product-sheet">

        <!-- Price & Name -->
        <div class="product-header">
          <div class="price-row">
            <h1 class="product-price">₱{{ product.price.toFixed(2) }}</h1>
            <div class="price-badge" v-if="product.discount">
              <span>{{ product.discount }}% OFF</span>
            </div>
          </div>
          <h2 class="product-name">{{ product.name }}</h2>
          <p class="product-unit">{{ product.unit }}</p>
        </div>

        <!-- Seller Info -->
        <div class="seller-row" @click="goToStore">
          <div class="seller-icon">{{ product.sellerEmoji }}</div>
          <div class="seller-info">
            <p class="seller-name">{{ product.sellerName }}</p>
            <p class="seller-meta">⭐ {{ product.sellerRating }} • {{ product.sellerDistance }}</p>
          </div>
          <ion-icon :icon="chevronForwardOutline" class="seller-arrow"></ion-icon>
        </div>

        <!-- Description -->
        <div class="section-block" v-if="product.description">
          <h3 class="block-title">Description</h3>
          <p class="product-description">{{ product.description }}</p>
        </div>

        <!-- Details -->
        <div class="section-block">
          <h3 class="block-title">Product Details</h3>
          <div class="details-list">
            <div class="detail-row">
              <span class="detail-label">Unit</span>
              <span class="detail-value">{{ product.unit }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Category</span>
              <span class="detail-value">{{ product.category }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Availability</span>
              <span class="detail-value" :class="product.available ? 'available' : 'unavailable'">
                {{ product.available ? '✅ Available' : '❌ Unavailable' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Similar Products -->
        <div class="section-block">
          <h3 class="block-title">You May Also Like</h3>
          <div class="similar-scroll">
            <div
              v-for="item in similarProducts"
              :key="item.id"
              class="similar-card"
              @click="openSimilar(item)"
            >
              <div class="similar-img">
                <span>{{ item.emoji }}</span>
              </div>
              <p class="similar-price">₱{{ item.price.toFixed(2) }}</p>
              <p class="similar-name">{{ item.name }}</p>
              <p class="similar-unit">{{ item.unit }}</p>
              <div class="similar-add" @click.stop="addSimilarToCart(item)">
                <ion-icon :icon="addOutline"></ion-icon>
              </div>
            </div>
          </div>
        </div>

        <div style="height: 120px;"></div>
      </div>

    </ion-content>

    <!-- ─── Bottom Bar ─────────────────────── -->
    <div class="bottom-bar">
      <!-- Quantity Selector -->
      <div class="quantity-selector">
        <div class="qty-btn" @click="decreaseQty">
          <ion-icon :icon="removeOutline"></ion-icon>
        </div>
        <span class="qty-value">{{ quantity }}</span>
        <div class="qty-btn" @click="increaseQty">
          <ion-icon :icon="addOutline"></ion-icon>
        </div>
      </div>

      <!-- Add to Cart -->
      <ion-button
        expand="block"
        class="add-cart-btn"
        :disabled="!product.available"
        @click="addToCart"
      >
        <ion-icon slot="start" :icon="cartOutline"></ion-icon>
        Add to Basket • ₱{{ (product.price * quantity).toFixed(2) }}
      </ion-button>
    </div>

  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage, IonContent, IonButton, IonIcon,
  toastController
} from '@ionic/vue';
import {
  arrowBackOutline, shareOutline, chevronForwardOutline,
  addOutline, removeOutline, cartOutline
} from 'ionicons/icons';

const router = useRouter();
const route = useRoute();

const quantity = ref(1);

// Product data (in real app, fetch from API using route.params.id)
const product = ref({
  id: route.params.id || 1,
  name: 'Bawang/Garlic',
  emoji: '🧄',
  price: 66.00,
  unit: '250g',
  category: 'Vegetables',
  available: true,
  discount: null,
  description: 'Fresh garlic sourced directly from local farmers. Perfect for cooking Filipino dishes. Rich in flavor and nutrients.',
  sellerName: "Juan's Fresh Market",
  sellerEmoji: '🥦',
  sellerRating: '4.8',
  sellerDistance: '2.3 km',
  sellerId: 1
});

const similarProducts = ref([
  { id: 2, name: 'Sibuyas/Onion', emoji: '🧅', price: 110.00, unit: '250g' },
  { id: 3, name: 'Luya/Ginger', emoji: '🫚', price: 73.00, unit: '250g' },
  { id: 4, name: 'Kamatis/Tomato', emoji: '🍅', price: 48.00, unit: '250g' },
  { id: 5, name: 'Pechay', emoji: '🥬', price: 66.00, unit: '250g' },
]);

// Methods
const increaseQty = () => { quantity.value++; };
const decreaseQty = () => { if (quantity.value > 1) quantity.value--; };

const addToCart = async () => {
  const toast = await toastController.create({
    message: `✅ ${quantity.value}x ${product.value.name} added to basket!`,
    duration: 2000, color: 'success', position: 'bottom'
  });
  await toast.present();
  router.back();
};

const addSimilarToCart = async (item) => {
  const toast = await toastController.create({
    message: `✅ ${item.name} added to basket!`,
    duration: 1500, color: 'success', position: 'bottom'
  });
  await toast.present();
};

const openSimilar = (item) => { router.push(`/product/${item.id}`); };
const goToStore = () => router.push(`/seller/${product.value.sellerId}`);
const goBack = () => router.back();
const shareProduct = () => {};
</script>

<style scoped>
.product-content { --background: #f8f9fa; }

/* ─── Product Hero ───────────────────────────── */
.product-hero { position: relative; }

.product-image-bg {
  height: 280px; background: white;
  display: flex; align-items: center; justify-content: center;
}
.product-emoji-large { font-size: 120px; }

.hero-actions {
  position: absolute; top: 0; left: 0; right: 0;
  display: flex; justify-content: space-between;
  padding: 52px 16px 12px;
}

.action-btn {
  width: 40px; height: 40px;
  background: rgba(0,0,0,0.3); border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; backdrop-filter: blur(4px);
}
.action-btn ion-icon { font-size: 22px; color: white; }

/* ─── Product Sheet ──────────────────────────── */
.product-sheet {
  background: white;
  border-radius: 24px 24px 0 0;
  margin-top: -24px; position: relative; z-index: 10;
}

/* Product Header */
.product-header { padding: 24px 20px 16px; border-bottom: 1px solid #f0f0f0; }

.price-row { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
.product-price { font-size: 32px; font-weight: 900; color: #1a1a1a; margin: 0; }
.price-badge { background: #E8F5E9; border-radius: 8px; padding: 4px 10px; }
.price-badge span { font-size: 13px; font-weight: 700; color: #4CAF50; }

.product-name { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 6px 0; }
.product-unit { font-size: 14px; color: #999; margin: 0; }

/* Seller Row */
.seller-row {
  display: flex; align-items: center; gap: 14px;
  padding: 16px 20px; border-bottom: 1px solid #f0f0f0; cursor: pointer;
}
.seller-icon {
  width: 48px; height: 48px; background: #E8F5E9;
  border-radius: 14px; display: flex; align-items: center;
  justify-content: center; font-size: 26px; flex-shrink: 0;
}
.seller-info { flex: 1; }
.seller-name { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.seller-meta { font-size: 13px; color: #666; margin: 0; }
.seller-arrow { font-size: 20px; color: #ccc; }

/* Section Block */
.section-block { padding: 16px 20px; border-bottom: 1px solid #f0f0f0; }
.block-title { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 12px 0; }

.product-description { font-size: 14px; color: #555; line-height: 1.6; margin: 0; }

/* Details */
.details-list { display: flex; flex-direction: column; gap: 10px; }
.detail-row { display: flex; justify-content: space-between; align-items: center; }
.detail-label { font-size: 14px; color: #666; }
.detail-value { font-size: 14px; font-weight: 600; color: #1a1a1a; }
.detail-value.available { color: #4CAF50; }
.detail-value.unavailable { color: #f44336; }

/* Similar Products */
.similar-scroll { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 4px; }
.similar-scroll::-webkit-scrollbar { display: none; }

.similar-card { flex-shrink: 0; width: 120px; cursor: pointer; position: relative; }
.similar-card:active { transform: scale(0.97); }

.similar-img {
  width: 120px; height: 100px; background: #f8f9fa;
  border-radius: 12px; display: flex; align-items: center;
  justify-content: center; font-size: 44px; margin-bottom: 8px;
  border: 1px solid #f0f0f0;
}
.similar-price { font-size: 15px; font-weight: 800; color: #1a1a1a; margin: 0 0 2px 0; }
.similar-name { font-size: 12px; color: #333; margin: 0 0 2px 0; font-weight: 600; }
.similar-unit { font-size: 11px; color: #999; margin: 0; }

.similar-add {
  position: absolute; bottom: 32px; right: 4px;
  width: 28px; height: 28px; background: #4CAF50;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; box-shadow: 0 2px 8px rgba(76,175,80,0.4);
}
.similar-add ion-icon { font-size: 18px; color: white; }

/* ─── Bottom Bar ─────────────────────────────── */
.bottom-bar {
  position: fixed; bottom: 0; left: 0; right: 0;
  background: white; padding: 16px 20px;
  display: flex; align-items: center; gap: 16px;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
  z-index: 1000;
}

.quantity-selector {
  display: flex; align-items: center; gap: 12px;
  background: #f5f5f5; border-radius: 14px; padding: 8px 12px;
}
.qty-btn {
  width: 32px; height: 32px; background: white;
  border-radius: 10px; display: flex; align-items: center;
  justify-content: center; cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.qty-btn ion-icon { font-size: 18px; color: #4CAF50; }
.qty-value { font-size: 18px; font-weight: 800; color: #1a1a1a; min-width: 24px; text-align: center; }

.add-cart-btn {
  flex: 1; --background: #4CAF50; --border-radius: 14px;
  height: 52px; font-size: 15px; font-weight: 700; text-transform: none;
}
</style>