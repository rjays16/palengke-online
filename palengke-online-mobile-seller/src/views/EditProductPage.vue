<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/products"></ion-back-button>
        </ion-buttons>
        <ion-title>Edit Product</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="deleteProduct">
            <ion-icon :icon="trashOutline" color="danger"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="edit-product-content">
      <div class="loading-state" v-if="loading">
        <ion-icon :icon="cubeOutline" class="loading-icon"></ion-icon>
        <p>Loading product...</p>
      </div>

      <div class="edit-product-container" v-else>

        <!-- Product Images -->
        <div class="section">
          <h2 class="section-title">Product Images</h2>
          
          <div class="image-upload-area" @click="selectImage">
            <div class="uploaded-image" v-if="productImage">
              <img :src="productImage" alt="Product">
              <div class="remove-image" @click.stop="productImage = ''">
                <ion-icon :icon="closeCircle"></ion-icon>
              </div>
            </div>
            <div class="upload-placeholder" v-else>
              <ion-icon :icon="cloudUploadOutline" class="upload-icon"></ion-icon>
              <p>Click to upload product image</p>
              <span>PNG, JPG, WEBP (Max 5MB)</span>
            </div>
          </div>
        </div>

        <!-- Basic Information -->
        <div class="section">
          <h2 class="section-title">Basic Information</h2>
          
          <div class="input-group">
            <label>Product Name <span class="required">*</span></label>
            <input 
              v-model="productData.name" 
              type="text" 
              placeholder="e.g., Fresh Tilapia"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <label>Category <span class="required">*</span></label>
            <select v-model="productData.category" class="custom-select">
              <option value="">Select category</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div class="input-group">
            <label>Description</label>
            <textarea 
              v-model="productData.description" 
              placeholder="Describe your product..."
              rows="4"
              class="custom-textarea"
            ></textarea>
          </div>
        </div>

        <!-- Pricing -->
        <div class="section">
          <h2 class="section-title">Pricing</h2>
          
          <div class="input-group">
            <label>Selling Unit <span class="required">*</span></label>
            <div class="unit-selector">
              <div 
                class="unit-option" 
                :class="{ active: productData.unit === 'kilo' }"
                @click="productData.unit = 'kilo'"
              >
                <div class="unit-icon"><font-awesome-icon icon="weight-hanging" /></div>
                <div class="unit-text">Per Kilo</div>
              </div>
              <div 
                class="unit-option" 
                :class="{ active: productData.unit === 'piece' }"
                @click="productData.unit = 'piece'"
              >
                <div class="unit-icon"><font-awesome-icon icon="box" /></div>
                <div class="unit-text">Per Piece</div>
              </div>
              <div 
                class="unit-option" 
                :class="{ active: productData.unit === 'pack' }"
                @click="productData.unit = 'pack'"
              >
                <div class="unit-icon"><font-awesome-icon icon="clipboard-list" /></div>
                <div class="unit-text">Per Pack</div>
              </div>
            </div>
          </div>

          <div class="input-group">
            <label>Price <span class="required">*</span></label>
            <div class="price-input">
              <span class="currency">₱</span>
              <input 
                v-model.number="productData.price" 
                type="number" 
                placeholder="0.00"
                class="custom-input"
                step="0.01"
              >
              <span class="per-unit">/ {{ productData.unit }}</span>
            </div>
          </div>

          <div class="input-group" v-if="productData.unit === 'kilo'">
            <label>Minimum Order (kg)</label>
            <input 
              v-model.number="productData.minOrder" 
              type="number" 
              placeholder="e.g., 0.5"
              class="custom-input"
              step="0.1"
            >
          </div>

          <div class="checkbox-group">
            <label class="checkbox-label">
              <input type="checkbox" v-model="productData.hasDiscount">
              <span>This product has a discount</span>
            </label>
          </div>

          <div class="input-group" v-if="productData.hasDiscount">
            <label>Discounted Price <span class="required">*</span></label>
            <div class="price-input">
              <span class="currency">₱</span>
              <input 
                v-model.number="productData.discountPrice" 
                type="number" 
                placeholder="0.00"
                class="custom-input"
                step="0.01"
              >
              <span class="discount-badge" v-if="discountPercentage">
                {{ discountPercentage }}% OFF
              </span>
            </div>
          </div>
        </div>

        <!-- Inventory -->
        <div class="section">
          <h2 class="section-title">Inventory</h2>
          
          <div class="input-group">
            <label>Stock Quantity <span class="required">*</span></label>
            <input 
              v-model.number="productData.stock" 
              type="number" 
              :placeholder="stockPlaceholder"
              class="custom-input"
            >
          </div>

          <div class="input-group">
            <label>SKU (Stock Keeping Unit)</label>
            <input 
              v-model="productData.sku" 
              type="text" 
              placeholder="e.g., FISH-TIL-001"
              class="custom-input"
            >
          </div>

        </div>

        <!-- Additional Details -->
        <div class="section">
          <h2 class="section-title">Additional Details</h2>
          
          <div class="input-group">
            <label>Origin/Source</label>
            <select v-model="productData.origin" class="custom-select">
              <option value="">Select origin</option>
              <option v-for="loc in locations" :key="loc" :value="loc">{{ loc }}</option>
            </select>
          </div>

          <div class="input-group">
            <label>Brand</label>
            <input 
              v-model="productData.brand" 
              type="text" 
              placeholder="e.g., Del Monte"
              class="custom-input"
            >
          </div>

          <div class="checkbox-group" v-if="canFeature">
            <label class="checkbox-label">
              <input type="checkbox" v-model="productData.isFeatured">
              <span>Feature this product</span>
            </label>
          </div>
        </div>

        <!-- Product Status -->
        <div class="section">
          <h2 class="section-title">Product Status</h2>
          
          <div class="status-toggle">
            <div class="status-info">
              <div class="status-label">{{ productData.isActive ? 'Active' : 'Inactive' }}</div>
              <div class="status-description">
                {{ productData.isActive ? 'Product is visible to customers' : 'Product is hidden from customers' }}
              </div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" v-model="productData.isActive">
              <span class="slider"></span>
            </label>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <ion-button 
            expand="block" 
            color="success"
            class="update-button"
            @click="updateProduct"
          >
            Update Product
          </ion-button>
          
          <ion-button 
            expand="block" 
            fill="outline"
            color="medium"
            class="cancel-button"
            @click="goBack"
          >
            Cancel
          </ion-button>

          <ion-button 
            expand="block" 
            color="danger"
            fill="outline"
            class="delete-button"
            @click="deleteProduct"
          >
            <ion-icon slot="start" :icon="trashOutline"></ion-icon>
            Delete Product
          </ion-button>
        </div>

      </div>

      <!-- Saving Overlay -->
      <div class="saving-overlay" v-if="saving">
        <div class="saving-spinner">
          <ion-spinner name="crescent" color="success"></ion-spinner>
          <p>Saving changes...</p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { getSubscription } from '@/services/subscriptionService';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButton,
  IonButtons,
  IonBackButton,
  IonIcon,
  IonSpinner,
  alertController,
  toastController
} from '@ionic/vue';
import {
  trashOutline,
  closeCircle,
  addOutline,
  cloudUploadOutline,
  checkmarkCircle
} from 'ionicons/icons';
import { API_BASE } from '@/config/api';

const router = useRouter();
const route = useRoute();

const userEmail = localStorage.getItem('userEmail');
const loading = ref(true);
const categories = ref([]);
const locations = ref([]);
const saving = ref(false);

const canFeature = computed(() => getSubscription().status === 'active');

const productData = ref({
  id: null,
  name: '',
  category: '',
  description: '',
  unit: 'kilo',
  price: 0,
  minOrder: null,
  hasDiscount: false,
  discountPrice: null,
  stock: 0,
  sku: '',
  origin: '',
  brand: '',
  isFeatured: false,
  isActive: true,
});

const productImage = ref('');

async function loadProduct() {
  const productId = route.params.id;
  if (!userEmail || !productId) return;
  loading.value = true;
  try {
    const [profileRes, prodRes, locRes] = await Promise.all([
      fetch(`${API_BASE}/seller/profile?email=${encodeURIComponent(userEmail)}`, {
        headers: { 'Accept': 'application/json' }
      }),
      fetch(`${API_BASE}/seller/products/${productId}?email=${encodeURIComponent(userEmail)}`, {
        headers: { 'Accept': 'application/json' }
      }),
      fetch(`${API_BASE}/locations`, {
        headers: { 'Accept': 'application/json' }
      }),
    ]);

    const profileData = await profileRes.json();
    let cats = (profileData.user && profileData.user.categories) || [];
    if (!cats.length) {
      const allRes = await fetch(`${API_BASE}/categories`, {
        headers: { 'Accept': 'application/json' }
      });
      const allData = await allRes.json();
      cats = (Array.isArray(allData) ? allData : (allData.categories || [])).map(c => c.name);
    }
    categories.value = cats;

    const locData = await locRes.json();
    const locs = locData.locations || [];
    const cityNames = [];
    locs.forEach(region => {
      (region.cities || []).forEach(city => {
        cityNames.push(city.name);
      });
    });
    locations.value = cityNames;

    const prodData = await prodRes.json();
    const p = prodData.product;
    if (p) {
      productData.value = {
        id: p.id,
        name: p.name,
        category: p.category,
        description: p.description || '',
        unit: p.selling_unit,
        price: p.price,
        minOrder: p.minimum_order,
        hasDiscount: !!p.has_discount,
        discountPrice: p.discount_price,
        stock: p.stock_quantity,
        sku: p.sku || '',
        origin: p.origin || '',
        brand: p.brand || '',
        isFeatured: !!p.is_featured,
        isActive: !!p.is_active,
      };
      productImage.value = p.images?.[0] || '';

      if (p.category && !categories.value.includes(p.category)) {
        categories.value.push(p.category);
      }
    }
  } catch (e) {
    console.error('Failed to load product:', e);
  } finally {
    loading.value = false;
  }
}

// Computed
const stockPlaceholder = computed(() => {
  if (productData.value.unit === 'kilo') return 'e.g., 50 kilos';
  if (productData.value.unit === 'piece') return 'e.g., 100 pieces';
  return 'e.g., 20 packs';
});

const discountPercentage = computed(() => {
  if (!productData.value.hasDiscount || !productData.value.discountPrice) return null;
  const original = productData.value.price;
  const discounted = productData.value.discountPrice;
  return Math.round(((original - discounted) / original) * 100);
});

// Methods
const selectImage = () => {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/png,image/jpg,image/jpeg,image/webp';
  input.onchange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append('image', file);
    formData.append('email', userEmail);
    try {
      const res = await fetch(`${API_BASE}/seller/products/upload-image`, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: formData,
      });
      const data = await res.json();
      if (res.ok) {
        productImage.value = data.url;
      }
    } catch (e) {
      console.error('Upload failed:', e);
    }
  };
  input.click();
};

const updateProduct = async () => {
  if (!productData.value.name || !productData.value.category || !productData.value.price || !productData.value.stock) {
    const alert = await alertController.create({
      header: 'Missing Information',
      message: 'Please fill in all required fields marked with *',
      buttons: ['OK']
    });
    await alert.present();
    return;
  }

  saving.value = true;
  try {
    const res = await fetch(`${API_BASE}/seller/products/${productData.value.id}`, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: userEmail,
        name: productData.value.name,
        category: productData.value.category,
        description: productData.value.description,
        selling_unit: productData.value.unit,
        price: productData.value.price,
        minimum_order: productData.value.minOrder,
        has_discount: productData.value.hasDiscount,
        discount_price: productData.value.hasDiscount ? productData.value.discountPrice : null,
        stock_quantity: productData.value.stock,
        sku: productData.value.sku,
        track_inventory: productData.value.trackInventory,
        origin: productData.value.origin,
        brand: productData.value.brand,
        is_featured: productData.value.isFeatured,
        is_active: productData.value.isActive,
        images: productImage.value ? [productImage.value] : [],
      }),
    });

    if (!res.ok) {
      const errData = await res.json();
      throw new Error(errData.message || 'Failed to update product');
    }

    await showSuccessModal('Product updated successfully!');
  } catch (e) {
    const alert = await alertController.create({
      header: 'Error',
      message: e.message || 'Failed to update product. Please try again.',
      buttons: ['OK']
    });
    await alert.present();
  } finally {
    saving.value = false;
  }
};

const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  // Add the image after modal is presented
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      // Clear the text content
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      // Create wrapper for centering
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      // Create and add the image
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      // Create and add the text
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      
      // Append to wrapper
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      
      // Append wrapper to alert
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  // Auto dismiss after 2 seconds
  setTimeout(() => {
    alert.dismiss();
    router.push('/products');
  }, 2000);
};

const deleteProduct = async () => {
  const alert = await alertController.create({
    header: 'Delete Product',
    message: `Are you sure you want to delete "${productData.value.name}"? This action cannot be undone.`,
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Delete',
        role: 'destructive',
        handler: async () => {
          saving.value = true;
          try {
            const res = await fetch(`${API_BASE}/seller/products/${productData.value.id}?email=${encodeURIComponent(userEmail)}`, {
              method: 'DELETE',
              headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Failed to delete product');
            await showSuccessModal('Product successfully deleted');
          } catch (e) {
            const alert = await alertController.create({
              header: 'Error',
              message: e.message || 'Failed to delete product.',
              buttons: ['OK']
            });
            await alert.present();
          } finally {
            saving.value = false;
          }
        }
      }
    ]
  });
  await alert.present();
};

const goBack = () => {
  router.push('/products');
};

const showToast = async (message, color = 'success', icon = null) => {
  const toast = await toastController.create({
    message: icon ? `${icon} ${message}` : message,
    duration: 2500,
    color,
    position: 'top',
    cssClass: 'custom-toast'
  });
  await toast.present();
};

onMounted(loadProduct);
</script>

<style scoped>
.edit-product-content {
  --background: #f8f9fa;
}

.edit-product-container {
  padding: 20px;
  padding-bottom: 40px;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 80px 20px;
}

.loading-icon {
  font-size: 60px;
  color: #4CAF50;
  margin-bottom: 16px;
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.1); }
}

.loading-state p {
  font-size: 15px;
  color: #666;
  margin: 0;
}

/* Sections */
.section {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 20px 0;
}

/* Image Upload */
.image-upload-area {
  border: 2px dashed #e0e0e0;
  border-radius: 12px;
  padding: 20px;
  cursor: pointer;
  transition: border-color 0.2s;
}

.image-upload-area:hover {
  border-color: #4CAF50;
}

.upload-placeholder {
  text-align: center;
  padding: 40px 20px;
}

.upload-icon {
  font-size: 64px;
  color: #ccc;
  margin-bottom: 16px;
}

.upload-placeholder p {
  font-size: 16px;
  color: #333;
  margin: 0 0 8px 0;
}

.upload-placeholder span {
  font-size: 13px;
  color: #999;
}

.uploaded-image {
  position: relative;
  max-width: 300px;
  margin: 0 auto;
  border-radius: 12px;
  overflow: hidden;
  background: #f5f5f5;
}

.uploaded-image img {
  width: 100%;
  aspect-ratio: 1;
  object-fit: cover;
  display: block;
}

.remove-image {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 28px;
  height: 28px;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  cursor: pointer;
  font-size: 20px;
}

/* Input Groups */
.input-group {
  margin-bottom: 20px;
}

.input-group:last-child {
  margin-bottom: 0;
}

.input-group label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.required {
  color: #f44336;
}

.custom-input,
.custom-select,
.custom-textarea {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
  font-family: inherit;
  transition: border-color 0.2s;
}

.custom-input:focus,
.custom-select:focus,
.custom-textarea:focus {
  outline: none;
  border-color: #4CAF50;
}

.custom-textarea {
  resize: vertical;
}

/* Unit Selector */
.unit-selector {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.unit-option {
  padding: 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
}

.unit-option:active {
  transform: scale(0.95);
}

.unit-option.active {
  border-color: #4CAF50;
  background: #E8F5E9;
}

.unit-icon {
  font-size: 28px;
  margin-bottom: 8px;
}

.unit-text {
  font-size: 13px;
  font-weight: 600;
  color: #333;
}

/* Price Input */
.price-input {
  position: relative;
  display: flex;
  align-items: center;
  gap: 8px;
}

.currency {
  position: absolute;
  left: 16px;
  font-size: 15px;
  font-weight: 600;
  color: #666;
}

.price-input .custom-input {
  padding-left: 36px;
  flex: 1;
}

.per-unit {
  font-size: 13px;
  color: #666;
  white-space: nowrap;
}

.discount-badge {
  background: #FF9800;
  color: white;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}

/* Checkbox */
.checkbox-group {
  margin-bottom: 20px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-size: 14px;
  color: #333;
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #4CAF50;
}

/* Status Toggle */
.status-toggle {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.status-info {
  flex: 1;
}

.status-label {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.status-description {
  font-size: 13px;
  color: #666;
}

.toggle-switch {
  position: relative;
  width: 56px;
  height: 32px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  border-radius: 32px;
  transition: 0.3s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 24px;
  width: 24px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  border-radius: 50%;
  transition: 0.3s;
}

input:checked + .slider {
  background-color: #4CAF50;
}

input:checked + .slider:before {
  transform: translateX(24px);
}

/* Action Buttons */
.action-buttons {
  margin-top: 24px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.update-button,
.cancel-button,
.delete-button {
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  text-transform: none;
  --border-radius: 12px;
}

/* Responsive */
@media (max-width: 360px) {
  .edit-product-container {
    padding: 16px;
  }
  
  .unit-selector {
    grid-template-columns: 1fr;
  }
}

/* Saving Overlay */
.saving-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.saving-spinner {
  background: white;
  padding: 32px 40px;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);
}

.saving-spinner ion-spinner {
  width: 40px;
  height: 40px;
  margin-bottom: 12px;
}

.saving-spinner p {
  margin: 0;
  font-size: 15px;
  font-weight: 600;
  color: #333;
}

/* Custom Toast Styling */
:deep(.custom-toast) {
  --background: white;
  --color: #1a1a1a;
  --border-radius: 12px;
  --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  font-weight: 600;
  border: 2px solid #4CAF50;
}

:deep(.custom-toast::part(message)) {
  font-size: 15px;
}

/* Success Modal Styling */
:deep(.success-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 320px;
  padding: 30px 20px;
}

:deep(.success-modal .alert-head) {
  padding: 0;
  min-height: 0;
}

:deep(.success-modal .alert-message) {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.success-modal .alert-button-group) {
  padding: 10px 0 0 0;
}

:deep(.success-modal button) {
  background: #4CAF50;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
}
</style>