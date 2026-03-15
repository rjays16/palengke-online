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

        <!-- Product Images Section -->
        <div class="section">
          <h2 class="section-title">
            <ion-icon :icon="imagesOutline"></ion-icon>
            Product Images
          </h2>
          
          <div class="image-upload-area" @click="selectImages">
            <div class="uploaded-images" v-if="productImages.length > 0">
              <div class="image-item" v-for="(image, index) in productImages" :key="index">
                <img :src="image" alt="Product">
                <div class="remove-image" @click.stop="removeImage(index)">
                  <ion-icon :icon="closeCircle"></ion-icon>
                </div>
              </div>
              <div class="add-more-image">
                <ion-icon :icon="addOutline"></ion-icon>
                <span>Add More</span>
              </div>
            </div>
            <div class="upload-placeholder" v-else>
              <ion-icon :icon="cloudUploadOutline" class="upload-icon"></ion-icon>
              <p>Click to upload product images</p>
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
              <option value="fish"><font-awesome-icon icon="fish" /> Fish & Seafood</option>
              <option value="pork"><font-awesome-icon icon="piggy-bank" /> Pork</option>
              <option value="chicken"><font-awesome-icon icon="crow" /> Chicken</option>
              <option value="vegetables"><font-awesome-icon icon="carrot" /> Vegetables</option>
              <option value="fruits"><font-awesome-icon icon="apple-alt" /> Fruits</option>
              <option value="condiments"><font-awesome-icon icon="wine-bottle" /> Condiments</option>
              <option value="dairy"><font-awesome-icon icon="cow" /> Dairy & Goods</option>
              <option value="rice"><font-awesome-icon icon="seedling" /> Rice & Grains</option>
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

          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" v-model="trackInventory" checked>
              <span> Track inventory (reduce stock when sold)</span>
            </label>
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
            <input 
              v-model="origin" 
              type="text" 
              placeholder="e.g., Davao, Baguio, Local Farm"
              class="form-input"
            >
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

          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" v-model="isOrganic">
              <span> Organic product</span>
            </label>
          </div>

          <div class="form-group">
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
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
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

// Form Data
const productImages = ref([]);
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
const trackInventory = ref(true);
const origin = ref('');
const brand = ref('');
const isOrganic = ref(false);
const isFeatured = ref(false);
const isActive = ref(true);

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
const selectImages = () => {
  // TODO: Implement actual image picker
  console.log('Select images');
  // For demo, add a placeholder
  const demoImage = 'https://via.placeholder.com/300x300?text=Product+Image';
  if (productImages.value.length < 5) {
    productImages.value.push(demoImage);
  }
};

const removeImage = (index) => {
  productImages.value.splice(index, 1);
};

const saveProduct = async () => {
  // Validation
  if (!productName.value || !category.value || !description.value || !price.value || !stockQuantity.value) {
    const alert = await alertController.create({
      header: 'Missing Information',
      message: 'Please fill in all required fields (*)',
      buttons: ['OK']
    });
    await alert.present();
    return;
  }

  // TODO: Save to backend
  const productData = {
    images: productImages.value,
    name: productName.value,
    category: category.value,
    description: description.value,
    sellingUnit: sellingUnit.value,
    price: parseFloat(price.value),
    minimumOrder: minimumOrder.value ? parseFloat(minimumOrder.value) : null,
    discountPrice: hasDiscount.value ? parseFloat(discountPrice.value) : null,
    stockQuantity: parseFloat(stockQuantity.value),
    sku: sku.value,
    trackInventory: trackInventory.value,
    origin: origin.value,
    brand: brand.value,
    isOrganic: isOrganic.value,
    isFeatured: isFeatured.value,
    isActive: isActive.value
  };

  console.log('Saving product:', productData);

  // Show success modal
  await showSuccessModal('Product added successfully!');

  // Navigate back to products
  router.push('/products');
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
  
  // Wait for modal to close
  await alert.onDidDismiss();
};

const saveDraft = async () => {
  const toast = await toastController.create({
    message: 'Draft saved!',
    duration: 2000,
    color: 'medium',
    position: 'top'
  });
  await toast.present();
};
</script>

<style scoped>
.add-product-content {
  --background: #f8f9fa;
}

.add-product-container {
  padding: 20px;
  padding-bottom: 100px;
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

.uploaded-images {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.image-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 12px;
  overflow: hidden;
  background: #f5f5f5;
}

.image-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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

.add-more-image {
  aspect-ratio: 1;
  border: 2px dashed #e0e0e0;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #4CAF50;
  cursor: pointer;
}

.add-more-image ion-icon {
  font-size: 32px;
}

.add-more-image span {
  font-size: 12px;
  font-weight: 600;
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
  .uploaded-images {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .unit-selector {
    grid-template-columns: 1fr;
  }
}
</style>