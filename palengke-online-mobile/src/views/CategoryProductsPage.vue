<template>
  <ion-page>
    <ion-content :fullscreen="true" class="category-content">

      <!-- Header -->
      <div class="category-header">
        <div class="header-row">
          <div class="back-btn" @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </div>
          <h1 class="category-title">{{ categoryName }}</h1>
          <div class="header-spacer"></div>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="products-grid">
        <div
          v-for="product in products"
          :key="product.id"
          class="product-card"
          @click="openProduct(product)"
        >
          <div class="product-img-wrap">
            <img :src="product.image" :alt="product.name" class="product-img">
          </div>
          <div class="product-info">
            <p class="product-price">₱{{ product.price.toFixed(2) }}</p>
            <p class="product-name">{{ product.name }}</p>
            <p class="product-unit">{{ product.unit }}</p>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div class="empty-state" v-if="products.length === 0">
        <p>No products found</p>
      </div>

      <div style="height: 100px;"></div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  IonPage, IonContent, IonIcon
} from '@ionic/vue';
import { arrowBackOutline } from 'ionicons/icons';

const router = useRouter();
const route = useRoute();

const categoryId = computed(() => route.params.categoryId);
const categoryName = computed(() => {
  const names = {
    vegetables: 'Vegetables',
    fruits: 'Fruits',
    seafoods: 'Fresh Frozen Seafoods',
    pork: 'Fresh Frozen Pork',
    chicken: 'Fresh Frozen Chicken',
    bundles: 'TIPID Bundle Deals'
  };
  return names[categoryId.value] || 'Products';
});

import { products as mockProducts } from '@/mock/categoryProducts';

const products = ref(mockProducts);

const goBack = () => router.back();
const openProduct = (product) => router.push(`/product/${product.id}`);
</script>

<style scoped>
.category-content { --background: #f8f9fa; }

.category-header {
  background: white;
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
}

.header-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.back-btn {
  width: 40px;
  height: 40px;
  background: #f5f5f5;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.back-btn ion-icon { font-size: 22px; color: #333; }

.category-title {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.header-spacer { flex: 1; }

.products-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  padding: 16px;
}

.product-card {
  background: white;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  cursor: pointer;
}

.product-card:active { transform: scale(0.98); }

.product-img-wrap {
  width: 100%;
  height: 140px;
  background: #f8f9fa;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding: 12px;
}

.product-price {
  font-size: 16px;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.product-name {
  font-size: 13px;
  font-weight: 600;
  color: #333;
  margin: 0 0 2px 0;
  line-height: 1.3;
}

.product-unit {
  font-size: 12px;
  color: #999;
  margin: 0;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
}
</style>