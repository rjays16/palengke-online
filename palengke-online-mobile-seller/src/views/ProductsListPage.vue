<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>My Products</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="goToAddProduct" :disabled="expired">
            <ion-icon :icon="addOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="products-content">
      <div class="products-container">
        <!-- Search & Filter Section -->
        <div class="search-section">
          <div class="search-bar">
            <ion-icon :icon="searchOutline" class="search-icon"></ion-icon>
            <input v-model="searchQuery" type="text" placeholder="Search products..." class="search-input" />
            <ion-icon v-if="searchQuery" :icon="closeCircle" class="clear-icon" @click="searchQuery = ''"></ion-icon>
          </div>

          <!-- Category Filter -->
          <div class="filter-chips">
            <div class="filter-chip" :class="{ active: selectedFilter === 'all' }" @click="selectedFilter = 'all'">
              All ({{ products.length }})
            </div>
            <div
              class="filter-chip"
              :class="{ active: selectedFilter === 'active' }"
              @click="selectedFilter = 'active'"
            >
              Active ({{ activeProducts }})
            </div>
            <div
              class="filter-chip"
              :class="{ active: selectedFilter === 'inactive' }"
              @click="selectedFilter = 'inactive'"
            >
              Inactive ({{ inactiveProducts }})
            </div>
            <div
              class="filter-chip"
              :class="{ active: selectedFilter === 'low-stock' }"
              @click="selectedFilter = 'low-stock'"
            >
              Low Stock ({{ lowStockProducts }})
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div class="loading-state" v-if="loading">
          <ion-icon :icon="cubeOutline" class="loading-icon"></ion-icon>
          <p>Loading products...</p>
        </div>

        <!-- Products List -->
        <div class="products-list" v-else>
          <div class="product-card" v-for="product in filteredProducts" :key="product.id">
            <!-- Product Image -->
            <div class="product-image">
              <img
                :src="product.image"
                :alt="product.name"
                @error="($event.target as HTMLImageElement).src = '/assets/images/product-placeholder.svg'"
              />
              <div class="product-badge" :class="product.status">
                {{ product.status }}
              </div>
            </div>

            <!-- Product Info -->
            <div class="product-info">
              <div class="product-header">
                <h3 class="product-name">{{ product.name }}</h3>
                <div class="product-category">{{ product.category }}</div>
              </div>

              <div class="product-details">
                <div class="product-price">
                  <span class="current-price">₱{{ product.price.toLocaleString() }}</span>
                  <span v-if="product.originalPrice" class="original-price">
                    ₱{{ product.originalPrice.toLocaleString() }}
                  </span>
                  <span class="unit">/ {{ product.unit }}</span>
                </div>

                <div class="product-stock">
                  <ion-icon :icon="cubeOutline"></ion-icon>
                  <span :class="{ 'low-stock': product.stock < 10 }">
                    {{ product.stock }} {{ product.unit }}{{ product.stock > 1 ? 's' : '' }} left
                  </span>
                </div>
              </div>

              <div class="product-meta">
                <span class="meta-item">
                  <ion-icon :icon="eyeOutline"></ion-icon>
                  {{ product.views }} views
                </span>
                <span class="meta-item">
                  <ion-icon :icon="cartOutline"></ion-icon>
                  {{ product.sold }} sold
                </span>
              </div>
            </div>

            <!-- Product Actions -->
            <div class="product-actions">
              <ion-button fill="clear" size="small" @click="toggleProductStatus(product)">
                <ion-icon
                  slot="icon-only"
                  :icon="product.status === 'active' ? eyeOffOutline : eyeOutline"
                  :class="product.status === 'active' ? 'text-warning' : 'text-success'"
                ></ion-icon>
              </ion-button>

              <ion-button fill="clear" size="small" @click="editProduct(product)">
                <ion-icon slot="icon-only" :icon="createOutline" class="text-primary"></ion-icon>
              </ion-button>

              <ion-button fill="clear" size="small" @click="deleteProduct(product)">
                <ion-icon slot="icon-only" :icon="trashOutline" class="text-danger"></ion-icon>
              </ion-button>
            </div>
          </div>

          <!-- Empty State -->
          <div class="empty-state" v-if="filteredProducts.length === 0">
            <ion-icon :icon="cubeOutline" class="empty-icon"></ion-icon>
            <h3>No products found</h3>
            <p v-if="searchQuery">Try adjusting your search</p>
            <p v-else-if="selectedFilter !== 'all'">No products in this filter</p>
            <p v-else>Start adding products to your store</p>
            <ion-button @click="goToAddProduct" v-if="!searchQuery && selectedFilter === 'all'" :disabled="expired">
              <ion-icon slot="start" :icon="addOutline"></ion-icon>
              {{ expired ? 'Plan Expired' : 'Add Your First Product' }}
            </ion-button>
          </div>
        </div>
      </div>

      <!-- Deleting Overlay -->
      <div class="saving-overlay" v-if="deleting">
        <div class="saving-spinner">
          <ion-spinner name="crescent" color="success"></ion-spinner>
          <p>Deleting product...</p>
        </div>
      </div>

      <!-- Toggling Overlay -->
      <div class="saving-overlay" v-if="toggling">
        <div class="saving-spinner">
          <ion-spinner name="crescent" color="success"></ion-spinner>
          <p>Updating status...</p>
        </div>
      </div>
    </ion-content>

    <!-- Bottom Navigation -->
    <BottomNav active="products" />
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonFooter,
  IonButton,
  IonButtons,
  IonIcon,
  IonSpinner,
  onIonViewWillEnter,
  alertController,
  toastController,
} from '@ionic/vue';
import {
  addOutline,
  searchOutline,
  closeCircle,
  cubeOutline,
  eyeOutline,
  eyeOffOutline,
  createOutline,
  trashOutline,
  cartOutline,
  homeOutline,
  receiptOutline,
  personOutline,
} from 'ionicons/icons';
import BottomNav from '@/components/BottomNav.vue';
import { api } from '@/utils/apiClient';
import { type Product } from '@/types';
import { useSuccessModal } from '@/composables/useSuccessModal';
import { getSubscription, isExpired } from '@/services/subscriptionService';

const router = useRouter();
const { showSuccessModal } = useSuccessModal();

const expired = computed(() => isExpired(getSubscription()));

const userEmail = localStorage.getItem('userEmail');
const loading = ref(false);
const deleting = ref(false);
const toggling = ref(false);

// Search & Filter
const searchQuery = ref('');
const selectedFilter = ref('all');

const products = ref([] as Product[]);

async function loadProducts() {
  if (!userEmail) return;
  loading.value = true;
  try {
    const data = await api.get(`/seller/products?email=${encodeURIComponent(userEmail)}`);
    products.value = (data.products || []).map((p: any) => ({
      ...p,
      image: p.images?.[0] || '/assets/images/product-placeholder.svg',
      unit: p.selling_unit,
      originalPrice: p.has_discount ? p.price : null,
      stock: p.stock_quantity,
      views: 0,
      sold: 0,
      status: p.is_active ? 'active' : 'inactive',
      price: p.has_discount && p.discount_price ? p.discount_price : p.price,
    }));
  } catch (e) {
    console.error('Failed to load products:', e);
  } finally {
    loading.value = false;
  }
}

onIonViewWillEnter(loadProducts);

// Computed Properties
const filteredProducts = computed(() => {
  let filtered = products.value;

  // Filter by status
  if (selectedFilter.value === 'active') {
    filtered = filtered.filter((p) => p.status === 'active');
  } else if (selectedFilter.value === 'inactive') {
    filtered = filtered.filter((p) => p.status === 'inactive');
  } else if (selectedFilter.value === 'low-stock') {
    filtered = filtered.filter((p) => p.stock < 10);
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter((p) => p.name.toLowerCase().includes(query) || p.category.toLowerCase().includes(query));
  }

  return filtered;
});

const activeProducts = computed(() => products.value.filter((p) => p.status === 'active').length);

const inactiveProducts = computed(() => products.value.filter((p) => p.status === 'inactive').length);

const lowStockProducts = computed(() => products.value.filter((p) => p.stock < 10).length);

// Methods
const goToAddProduct = async () => {
  if (expired.value) {
    const toast = await toastController.create({
      message: 'Your plan has expired. Subscribe to a plan to add products.',
      duration: 3000,
      color: 'warning',
      position: 'top',
    });
    await toast.present();
    router.push('/plans');
    return;
  }
  router.push('/products/add');
};

const editProduct = (product: any) => {
  // TODO: Navigate to edit page with product data
  console.log('Edit product:', product);
  router.push(`/products/edit/${product.id}`);
};

const toggleProductStatus = async (product: any) => {
  const newIsActive = !product.is_active;
  const statusText = newIsActive ? 'activate' : 'deactivate';

  const alert = await alertController.create({
    header: 'Change Status',
    message: `Do you want to ${statusText} "${product.name}"?`,
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: statusText.charAt(0).toUpperCase() + statusText.slice(1),
        handler: async () => {
          toggling.value = true;
          try {
            await api.post(`/seller/products/${product.id}`, { email: userEmail, is_active: newIsActive });
            product.is_active = newIsActive;
            product.status = newIsActive ? 'active' : 'inactive';
            showToast(`Product ${newIsActive ? 'activated' : 'deactivated'}`, 'success');
          } catch (e) {
            showToast('Failed to update status', 'danger');
          } finally {
            toggling.value = false;
          }
        },
      },
    ],
  });

  await alert.present();
};

const deleteProduct = async (product: any) => {
  const alert = await alertController.create({
    header: 'Delete Product',
    message: `Are you sure you want to delete "${product.name}"? This action cannot be undone.`,
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Delete',
        role: 'destructive',
        handler: async () => {
          deleting.value = true;
          try {
            await api.delete(`/seller/products/${product.id}?email=${encodeURIComponent(userEmail || '')}`);
            const index = products.value.findIndex((p) => p.id === product.id);
            if (index > -1) products.value.splice(index, 1);
            await showSuccessModal('Product successfully deleted');
          } catch (e) {
            showToast('Failed to delete product', 'danger');
          } finally {
            deleting.value = false;
          }
        },
      },
    ],
  });

  await alert.present();
};

const showToast = async (message: string, color: string = 'success') => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color,
    position: 'top',
  });
  await toast.present();
};


</script>

<style scoped>
.products-content {
  --background: #f8f9fa;
}

.products-container {
  padding-bottom: 80px;
}

/* Search Section */
.search-section {
  padding: 16px 20px;
  background: white;
  border-bottom: 1px solid #f0f0f0;
}

.search-bar {
  position: relative;
  margin-bottom: 16px;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #999;
}

.search-input {
  width: 100%;
  padding: 14px 48px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
  font-family: inherit;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #4caf50;
}

.clear-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #999;
  cursor: pointer;
}

/* Filter Chips */
.filter-chips {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding-bottom: 4px;
}

.filter-chips::-webkit-scrollbar {
  display: none;
}

.filter-chip {
  padding: 8px 16px;
  background: #f5f5f5;
  border: 2px solid transparent;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  color: #666;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
}

.filter-chip.active {
  background: #e8f5e9;
  border-color: #4caf50;
  color: #4caf50;
}

/* Products List */
.products-list {
  padding: 16px 20px;
}

.product-card {
  background: white;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  display: grid;
  grid-template-columns: 100px 1fr auto;
  gap: 16px;
  align-items: start;
}

/* Product Image */
.product-image {
  position: relative;
  width: 100px;
  height: 100px;
  border-radius: 12px;
  overflow: hidden;
  background: #f5f5f5;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
}

.product-badge.active {
  background: #4caf50;
  color: white;
}

.product-badge.inactive {
  background: #999;
  color: white;
}

/* Product Info */
.product-info {
  flex: 1;
}

.product-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 8px;
}

.product-name {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.product-category {
  font-size: 11px;
  color: #4caf50;
  background: #e8f5e9;
  padding: 4px 8px;
  border-radius: 6px;
  font-weight: 600;
}

.product-details {
  margin-bottom: 8px;
}

.product-price {
  display: flex;
  align-items: baseline;
  gap: 6px;
  margin-bottom: 4px;
}

.current-price {
  font-size: 18px;
  font-weight: 700;
  color: #4caf50;
}

.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
}

.unit {
  font-size: 13px;
  color: #666;
}

.product-stock {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #666;
}

.product-stock ion-icon {
  font-size: 16px;
}

.low-stock {
  color: #ff9800;
  font-weight: 600;
}

.product-meta {
  display: flex;
  gap: 16px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #999;
}

.meta-item ion-icon {
  font-size: 14px;
}

/* Product Actions */
.product-actions {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.text-success {
  color: #4caf50;
}

.text-warning {
  color: #ff9800;
}

.text-primary {
  color: #2196f3;
}

.text-danger {
  color: #f44336;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 60px 20px;
}

.loading-icon {
  font-size: 60px;
  color: #4caf50;
  margin-bottom: 16px;
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 1;
    transform: scale(1.1);
  }
}

.loading-state p {
  font-size: 15px;
  color: #666;
  margin: 0;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  font-size: 80px;
  color: #ddd;
  margin-bottom: 20px;
}

.empty-state h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.empty-state p {
  font-size: 14px;
  color: #666;
  margin: 0 0 24px 0;
}

/* Responsive */
@media (max-width: 360px) {
  .product-card {
    grid-template-columns: 80px 1fr auto;
    gap: 12px;
  }

  .product-image {
    width: 80px;
    height: 80px;
  }

  .product-name {
    font-size: 14px;
  }

  .current-price {
    font-size: 16px;
  }
}

/* Responsive */
</style>
