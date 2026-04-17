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

const products = ref([
  { id: 1, name: 'Bawang/Garlic', image: 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=200&h=200&fit=crop', price: 66.00, unit: '250g' },
  { id: 2, name: 'Patatas/Potato', image: 'https://images.unsplash.com/photo-1595231776515-ddffb1f4eb73?w=200&h=200&fit=crop', price: 191.00, unit: '500g' },
  { id: 3, name: 'Sibuyas/Onion', image: 'https://images.unsplash.com/photo-1590005354167-6da97870c757?w=200&h=200&fit=crop', price: 110.00, unit: '250g' },
  { id: 4, name: 'Pipino/Cucumber', image: 'https://images.unsplash.com/photo-1449300079323-02e209d9d3a6?w=200&h=200&fit=crop', price: 73.00, unit: '500g' },
  { id: 5, name: 'Luya/Ginger', image: 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=200&h=200&fit=crop', price: 73.00, unit: '250g' },
  { id: 6, name: 'Pechay', image: 'https://images.unsplash.com/photo-1584270354949-c26b0d5b4a0c?w=200&h=200&fit=crop', price: 66.00, unit: '250g' },
  { id: 7, name: 'Kamatis/Tomato', image: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=200&h=200&fit=crop', price: 48.00, unit: '250g' },
  { id: 8, name: 'Batong/String Beans', image: 'https://images.unsplash.com/photo-1567375698348-5d9d5ae99de0?w=200&h=200&fit=crop', price: 95.00, unit: '500g' },
  { id: 9, name: 'Talong/Eggplant', image: 'https://images.unsplash.com/photo-1529636798458-92182e662485?w=200&h=200&fit=crop', price: 88.00, unit: '500g' },
  { id: 10, name: 'Apple Med. Size (3pcs)', image: 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=200&h=200&fit=crop', price: 162.00, unit: '3pcs' },
  { id: 11, name: 'Kiat-Kiat', image: 'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?w=200&h=200&fit=crop', price: 184.00, unit: '1 per pack' },
  { id: 12, name: 'Banana (Large)', image: 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=200&h=200&fit=crop', price: 129.00, unit: '1000g' },
  { id: 13, name: 'Mango', image: 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=200&h=200&fit=crop', price: 155.00, unit: '500g' },
  { id: 14, name: 'Watermelon', image: 'https://images.unsplash.com/photo-1589984662646-e7b2e4962f18?w=200&h=200&fit=crop', price: 89.00, unit: '1kg' },
  { id: 15, name: 'Tuna Cubes', image: 'https://images.unsplash.com/photo-1574781330855-d0db8cc6a79c?w=200&h=200&fit=crop', price: 253.00, unit: '500g' },
  { id: 16, name: 'Pampano', image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=200&h=200&fit=crop', price: 303.00, unit: '1 per pack' },
  { id: 17, name: 'Salmon Strips', image: 'https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?w=200&h=200&fit=crop', price: 203.00, unit: '500g' },
  { id: 18, name: 'Hipon/Shrimp', image: 'https://images.unsplash.com/photo-1565123409695-7b5ef63a2efb?w=200&h=200&fit=crop', price: 320.00, unit: '500g' },
  { id: 19, name: 'Pork Belly', image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=200&h=200&fit=crop', price: 287.00, unit: '500g' },
  { id: 20, name: 'Pork Adobo Cut', image: 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=200&h=200&fit=crop', price: 265.00, unit: '500g' },
  { id: 21, name: 'Pork Ribs', image: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=200&h=200&fit=crop', price: 310.00, unit: '500g' },
  { id: 22, name: 'Whole Dressed Chicken', image: 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=200&h=200&fit=crop', price: 304.00, unit: '1 per pack' },
  { id: 23, name: 'Chicken Wings', image: 'https://images.unsplash.com/photo-1606728035253-49e8a23146de?w=200&h=200&fit=crop', price: 320.00, unit: '1 per pack' },
  { id: 24, name: 'Chicken Drumsticks', image: 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=200&h=200&fit=crop', price: 257.00, unit: '1 per pack' },
  { id: 25, name: 'Fresh Vegetable Bundle (Family)', image: 'https://images.unsplash.com/photo-1518843875459-f738682238a6?w=200&h=200&fit=crop', price: 257.00, unit: '1 per bundle' },
  { id: 26, name: 'Sour Soup Package (Sinigang)', image: 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?w=200&h=200&fit=crop', price: 270.00, unit: '1 per pack' },
  { id: 27, name: 'Stew Package (Afritada Style)', image: 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=200&h=200&fit=crop', price: 252.00, unit: '1 per pack' },
]);

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