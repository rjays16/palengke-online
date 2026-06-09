<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/"></ion-back-button>
        </ion-buttons>
        <ion-title>Add Product</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="add-product-content">
      <div class="add-product-container">

        <!-- Loading State -->
        <div class="loading-state" v-if="pageLoading">
          <ion-icon :icon="cubeOutline" class="loading-icon"></ion-icon>
          <p>Loading...</p>
        </div>

        <template v-else>
        <!-- Product Images Section -->
        <div class="section">
          <h2 class="section-title">
            <ion-icon :icon="imagesOutline"></ion-icon>
            Product Images
          </h2>
          
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
          <h2 class="section-title">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            Basic Information
          </h2>

          <div class="form-group">
            <label>Product Name *</label>
            <input 
              v-model="productName" 
              type="text" 
              placeholder="e.g., Fresh Tilapia, Pork Belly, Tomatoes"
              class="form-input"
            >
          </div>

          <div class="form-group">
            <label>Category *</label>
            <select v-model="category" class="form-select">
              <option value="">Select a category</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div class="form-group">
            <label>Description *</label>
            <textarea 
              v-model="description" 
              placeholder="Describe your product (freshness, quality, origin, etc.)"
              class="form-textarea"
              rows="4"
            ></textarea>
          </div>
        </div>

        <!-- Pricing Section -->
        <div class="section">
          <h2 class="section-title">
            <ion-icon :icon="pricetagOutline"></ion-icon>
            Pricing
          </h2>

          <div class="form-group">
            <label>Selling Unit *</label>
            <div class="unit-selector">
              <div 
                class="unit-option" 
                :class="{ active: sellingUnit === 'kilo' }"
                @click="sellingUnit = 'kilo'"
              >
                <ion-icon :icon="scaleOutline"></ion-icon>
                <span>Per Kilo</span>
              </div>
              <div 
                class="unit-option" 
                :class="{ active: sellingUnit === 'piece' }"
                @click="sellingUnit = 'piece'"
              >
                <ion-icon :icon="cubeOutline"></ion-icon>
                <span>Per Piece</span>
              </div>
              <div 
                class="unit-option" 
                :class="{ active: sellingUnit === 'pack' }"
                @click="sellingUnit = 'pack'"
              >
                <ion-icon :icon="gridOutline"></ion-icon>
                <span>Per Pack</span>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Price (₱) *</label>
              <div class="input-with-prefix">
                <span class="prefix">₱</span>
                <input 
                  v-model="price" 
                  type="number" 
                  placeholder="0.00"
                  class="form-input with-prefix"
                  step="0.01"
                >
              </div>
              <small class="helper-text">Price per {{ sellingUnit }}</small>
            </div>

            <div class="form-group" v-if="sellingUnit === 'kilo'">
              <label>Minimum Order (kg)</label>
              <input 
                v-model="minimumOrder" 
                type="number" 
                placeholder="0.5"
                class="form-input"
                step="0.1"
              >
              <small class="helper-text">Optional</small>
            </div>
          </div>

          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" v-model="hasDiscount">
              <span> Add discount price</span>
            </label>
          </div>

          <div class="form-group" v-if="hasDiscount">
            <label>Discount Price (₱)</label>
            <div class="input-with-prefix">
              <span class="prefix">₱</span>
              <input 
                v-model="discountPrice" 
                type="number" 
                placeholder="0.00"
                class="form-input with-prefix"
                step="0.01"
              >
            </div>
            <div class="discount-badge" v-if="discountPercentage > 0">
              <ion-icon :icon="pricetagOutline"></ion-icon>
              {{ discountPercentage }}% OFF
            </div>
          </div>
        </div>

        <!-- Inventory Section -->
        <div class="section">
          <h2 class="section-title">
            <ion-icon :icon="cubeOutline"></ion-icon>
            Inventory
          </h2>

          <div class="form-group">
            <label>Stock Quantity *</label>
            <input 
              v-model="stockQuantity" 
              type="number" 
              :placeholder="sellingUnit === 'kilo' ? 'e.g., 50 (kilos)' : 'e.g., 100 (pieces)'"
              class="form-input"
            >
            <small class="helper-text">Available {{ sellingUnit }}s in stock</small>
          </div>

          <div class="form-group">
            <label>SKU (Stock Keeping Unit)</label>
            <input 
              v-model="sku" 
              type="text" 
              placeholder="e.g., FISH-001"
              class="form-input"
            >
            <small class="helper-text">Optional - for your reference</small>
          </div>

        </div>

        <!-- Additional Details -->
        <div class="section">
          <h2 class="section-title">
            <ion-icon :icon="listOutline"></ion-icon>
            Additional Details
          </h2>

          <div class="form-group">
            <label>Origin/Source</label>
            <select v-model="origin" class="form-select">
              <option value="">Select origin</option>
              <option v-for="loc in locations" :key="loc" :value="loc">{{ loc }}</option>
            </select>
          </div>

          <div class="form-group">
            <label>Brand (if applicable)</label>
            <input 
              v-model="brand" 
              type="text" 
              placeholder="e.g., Nestle, Magnolia, etc."
              class="form-input"
            >
          </div>

          <div class="form-group" v-if="canFeature">
            <label class="checkbox-label">
              <input type="checkbox" v-model="isFeatured">
              <span> Feature this product (show first)</span>
            </label>
          </div>
        </div>

        <!-- Product Status -->
        <div class="section">
          <h2 class="section-title">
            <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
            Product Status
          </h2>

          <div class="status-toggle">
            <span>Product is {{ isActive ? 'Active' : 'Inactive' }}</span>
            <ion-toggle v-model="isActive" color="success"></ion-toggle>
          </div>
          <small class="helper-text">
            {{ isActive ? 'Customers can see and buy this product' : 'Product is hidden from customers' }}
          </small>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <ion-button expand="block" size="large" class="save-button" @click="saveProduct">
            <ion-icon :icon="checkmarkOutline" slot="start"></ion-icon>
            Save Product
          </ion-button>
          
          <ion-button expand="block" fill="outline" size="large" @click="saveDraft">
            Save as Draft
          </ion-button>
        </div>

      </div>
      </template>

      <!-- Saving Overlay -->
      <div class="saving-overlay" v-if="saving">
        <div class="saving-spinner">
          <ion-spinner name="crescent" color="success"></ion-spinner>
          <p>Saving product...</p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { getSubscription } from '@/services/subscriptionService';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonButton,
  IonIcon,
  IonToggle,
  IonSpinner,
  alertController,
  toastController
} from '@ionic/vue';
import {
  imagesOutline,
  cloudUploadOutline,
  addOutline,
  closeCircle,
  informationCircleOutline,
  pricetagOutline,
  cubeOutline,
  scaleOutline,
  gridOutline,
  listOutline,
  checkmarkCircleOutline,
  checkmarkOutline
} from 'ionicons/icons';

const router = useRouter();

const userEmail = localStorage.getItem('userEmail');
const categories = ref([]);
const locations = ref([]);
const saving = ref(false);
const pageLoading = ref(true);

// Form Data
const productImage = ref('');
const productName = ref('');
const category = ref('');
const description = ref('');
const sellingUnit = ref('kilo');
const price = ref('');
const minimumOrder = ref('');
const hasDiscount = ref(false);
const discountPrice = ref('');
const stockQuantity = ref('');
const sku = ref('');
const origin = ref('');
const brand = ref('');
const isFeatured = ref(false);
const isActive = ref(true);

const canFeature = computed(() => getSubscription().status === 'active');

// Computed
const discountPercentage = computed(() => {
  if (hasDiscount.value && price.value && discountPrice.value) {
    const original = parseFloat(price.value);
    const discount = parseFloat(discountPrice.value);
    if (original > 0 && discount > 0 && discount < original) {
      return Math.round(((original - discount) / original) * 100);
    }
  }
  return 0;
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
      const res = await fetch('http://localhost:8000/api/seller/products/upload-image', {
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

const saveWithStatus = async (status) => {
  if (!productName.value || !category.value || !description.value || !price.value || !stockQuantity.value) {
    const alert = await alertController.create({
      header: 'Missing Information',
      message: 'Please fill in all required fields (*)',
      buttons: ['OK']
    });
    await alert.present();
    return;
  }

  saving.value = true;
  try {
    const res = await fetch('http://localhost:8000/api/seller/products', {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: userEmail,
        name: productName.value,
        category: category.value,
        description: description.value,
        selling_unit: sellingUnit.value,
        price: parseFloat(price.value),
        minimum_order: minimumOrder.value ? parseFloat(minimumOrder.value) : null,
        has_discount: hasDiscount.value,
        discount_price: hasDiscount.value ? parseFloat(discountPrice.value) : null,
        stock_quantity: parseFloat(stockQuantity.value),
        sku: sku.value,
        track_inventory: true,
        origin: origin.value,
        brand: brand.value,
        is_featured: isFeatured.value,
        is_active: isActive.value,
        images: productImage.value ? [productImage.value] : [],
        status,
      }),
    });

    if (!res.ok) {
      const errData = await res.json();
      throw new Error(errData.message || 'Failed to save product');
    }

    await showSuccessModal(status === 'draft' ? 'Draft saved!' : 'Product added successfully!');
    router.push('/products');
  } catch (e) {
    const alert = await alertController.create({
      header: 'Error',
      message: e.message || 'Failed to save product. Please try again.',
      buttons: ['OK']
    });
    await alert.present();
  } finally {
    saving.value = false;
  }
};

const saveProduct = () => saveWithStatus('active');

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
  
  // Wait for modal to close
  await alert.onDidDismiss();
};

const saveDraft = () => saveWithStatus('draft');

onMounted(async () => {
  if (!userEmail) { pageLoading.value = false; return; }
  try {
    const [profileRes, locRes] = await Promise.all([
      fetch(`http://localhost:8000/api/seller/profile?email=${encodeURIComponent(userEmail)}`, {
        headers: { 'Accept': 'application/json' }
      }),
      fetch('http://localhost:8000/api/locations', {
        headers: { 'Accept': 'application/json' }
      }),
    ]);

    const profileData = await profileRes.json();
    let cats = (profileData.user && profileData.user.categories) || [];
    if (!cats.length) {
      const allRes = await fetch('http://localhost:8000/api/categories', {
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
  } catch (e) {
    console.error('Failed to load data:', e);
  } finally {
    pageLoading.value = false;
  }
});
</script>

<style scoped>
.add-product-content {
  --background: #f8f9fa;
}

.add-product-container {
  padding: 20px;
  padding-bottom: 170px;
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

/* Section */
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
  display: flex;
  align-items: center;
  gap: 8px;
}

.section-title ion-icon {
  font-size: 24px;
  color: #4CAF50;
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

/* Form Elements */
.form-group {
  margin-bottom: 20px;
}

.form-group:last-child {
  margin-bottom: 0;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
  font-family: inherit;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: #4CAF50;
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.form-select {
  background: white;
  cursor: pointer;
}

.helper-text {
  display: block;
  font-size: 12px;
  color: #999;
  margin-top: 6px;
}

.input-with-prefix {
  position: relative;
}

.prefix {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 15px;
  font-weight: 600;
  color: #666;
}

.with-prefix {
  padding-left: 40px;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

/* Unit Selector */
.unit-selector {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}

.unit-option {
  padding: 16px 12px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
}

.unit-option ion-icon {
  font-size: 28px;
  color: #666;
  margin-bottom: 8px;
}

.unit-option span {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #666;
}

.unit-option.active {
  border-color: #4CAF50;
  background: #E8F5E9;
}

.unit-option.active ion-icon,
.unit-option.active span {
  color: #4CAF50;
}

/* Checkbox */
.checkbox-label {
  font-size: 15px;
  line-height: 16px;
  color: #333;
}

.checkbox-label input[type="checkbox"] {
  width: 16px;
  height: 16px;
  min-width: 16px;
  margin: 0;
  cursor: pointer;
  accent-color: #4CAF50;
}

.checkbox-label span {
  font-size: 15px;
  color: #333;
  line-height: 1.4;
}

/* Discount Badge */
.discount-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #FFE8E8;
  color: #FF4444;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 700;
  margin-top: 12px;
}

/* Status Toggle */
.status-toggle {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 12px;
  margin-bottom: 8px;
}

.status-toggle span {
  font-size: 15px;
  font-weight: 600;
  color: #333;
}

/* Action Buttons */
.action-buttons {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: white;
  padding: 16px 20px;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
  z-index: 100;
}

.save-button {
  --background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  --border-radius: 12px;
  height: 52px;
  font-weight: 700;
  margin-bottom: 12px;
}

.action-buttons ion-button {
  --border-radius: 12px;
  height: 48px;
  font-weight: 600;
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

/* Responsive */
@media (max-width: 360px) {
  .form-row {
    grid-template-columns: 1fr;
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
</style>