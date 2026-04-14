<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>My Cart</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="manageCart" v-if="cartStores.length > 0">
            Manage
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="cart-content">

      <!-- Empty Cart -->
      <div v-if="cartStores.length === 0" class="empty-cart">
        <div class="empty-icon">🛒</div>
        <h3>Your cart is empty</h3>
        <p>Add items from your favorite sellers</p>
        <ion-button color="success" @click="goToBrowse" class="browse-btn">
          <ion-icon slot="start" :icon="searchOutline"></ion-icon>
          Browse Sellers
        </ion-button>
      </div>

      <!-- Cart Items by Store -->
      <div v-else class="cart-list">
        <div
          v-for="store in cartStores"
          :key="store.id"
          class="store-cart-card"
        >
          <!-- Store Header -->
          <div class="store-cart-header">
            <img :src="store.image" :alt="store.name" class="store-cart-img">
            <div class="store-cart-info">
              <h3>{{ store.name }}</h3>
              <p>{{ store.itemCount }} items • {{ store.deliveryTime }} • {{ store.distance }}</p>
            </div>
            <div class="store-total" @click="goToCheckout(store)">
              <span class="total-amount">₱{{ getStoreTotal(store).toFixed(2) }}</span>
              <ion-icon :icon="chevronForwardOutline"></ion-icon>
            </div>
          </div>

          <!-- Items List -->
          <div class="items-list">
            <div v-for="item in store.items" :key="item.id" class="cart-item">
              <img :src="item.image" :alt="item.name" class="item-img">
              <div class="item-details">
                <span class="item-name">{{ item.name }}</span>
                <span class="item-price">₱{{ item.price.toFixed(2) }}</span>
              </div>
              <div class="quantity-controls">
                <button class="qty-btn" @click.stop="updateQuantity(store.id, item.id, -1)">
                  {{ item.quantity > 1 ? '−' : '' }}
                </button>
                <span class="qty-value">{{ item.quantity }}</span>
                <button class="qty-btn" @click.stop="updateQuantity(store.id, item.id, 1)">+</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Add more sellers -->
        <div class="add-more-btn" @click="goToBrowse">
          <ion-icon :icon="addCircleOutline"></ion-icon>
          <span>Order from another seller</span>
        </div>
      </div>

      <!-- Cart Summary -->
      <div class="cart-summary">
        <div class="summary-row">
          <span>Items ({{ getItemCount() }})</span>
          <span>₱{{ getCartTotal().toFixed(2) }}</span>
        </div>
        <div class="summary-row">
          <span>Delivery Fee</span>
          <span :class="{ free: getTotalDeliveryFee() === 0 }">
            {{ getTotalDeliveryFee() === 0 ? 'Free' : '₱' + getTotalDeliveryFee().toFixed(2) }}
          </span>
        </div>
        <div class="summary-total">
          <span>Total</span>
          <span>₱{{ (getCartTotal() + getTotalDeliveryFee()).toFixed(2) }}</span>
        </div>
        <ion-button expand="block" color="success" class="checkout-btn" @click="goToCheckoutFirst">
          Proceed to Checkout
        </ion-button>
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonButton, IonIcon,
  actionSheetController
} from '@ionic/vue';
import { searchOutline, addCircleOutline, chevronForwardOutline } from 'ionicons/icons';

const router = useRouter();

const cartStores = ref([
  {
    id: 1,
    name: "Juan's Fresh Market",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=200&h=200&fit=crop',
    color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
    itemCount: 3,
    deliveryTime: 'From 25 mins',
    distance: '2.3 km',
    deliveryFee: 45,
    freeDelivery: false,
    items: [
      { id: 1, name: 'Bawang/Garlic (100g)', emoji: '', image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=100&h=100&fit=crop', price: 66.00, quantity: 1 },
      { id: 2, name: 'Kamatis/Tomato (1kg)', emoji: '', image: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=100&h=100&fit=crop', price: 48.00, quantity: 2 },
      { id: 3, name: 'Siling Labuyo (50g)', emoji: '', image: 'https://images.unsplash.com/photo-1518843875459-f738682238a6?w=100&h=100&fit=crop', price: 35.00, quantity: 1 }
    ]
  },
  {
    id: 2,
    name: "Ana's Palengke - Cogon",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=200&h=200&fit=crop',
    color: 'linear-gradient(135deg, #FF9800, #E65100)',
    itemCount: 2,
    deliveryTime: 'From 20 mins',
    distance: '1.5 km',
    deliveryFee: 0,
    freeDelivery: true,
    items: [
      { id: 10, name: 'Apple Med. Size', emoji: '', image: 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=100&h=100&fit=crop', price: 162.00, quantity: 1 },
      { id: 11, name: 'Banana (Large)', emoji: '', image: 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=100&h=100&fit=crop', price: 129.00, quantity: 1 }
    ]
  }
]);

const getStoreTotal = (store) => {
  return store.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
};

const getCartTotal = () => {
  return cartStores.value.reduce((sum, store) => sum + getStoreTotal(store), 0);
};

const getTotalDeliveryFee = () => {
  return cartStores.value.reduce((sum, store) => sum + store.deliveryFee, 0);
};

const getItemCount = () => {
  return cartStores.value.reduce((sum, store) => sum + store.items.reduce((s, item) => s + item.quantity, 0), 0);
};

const updateQuantity = (storeId, itemId, delta) => {
  const store = cartStores.value.find(s => s.id === storeId);
  if (store) {
    const item = store.items.find(i => i.id === itemId);
    if (item) {
      item.quantity = Math.max(0, item.quantity + delta);
      if (item.quantity === 0) {
        store.items = store.items.filter(i => i.id !== itemId);
      }
      store.itemCount = store.items.reduce((s, i) => s + i.quantity, 0);
    }
  }
};

const manageCart = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Manage Cart',
    buttons: [
      { text: 'Clear All Carts', role: 'destructive', handler: () => { cartStores.value = []; } },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const goToCheckoutFirst = () => {
  router.push('/checkout');
};

const goToCheckout = (store) => router.push('/checkout');
const goToBrowse = () => router.push('/browse');
</script>

<style scoped>
.cart-content { 
  --background: #f8f9fa;
  --padding-top: 0px;
}

/* Empty Cart */
.empty-cart {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 80px 40px; text-align: center;
}
.empty-icon { font-size: 80px; margin-bottom: 20px; }
.empty-cart h3 { font-size: 22px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-cart p { font-size: 15px; color: #999; margin: 0 0 32px 0; }
.browse-btn { --border-radius: 14px; height: 52px; font-weight: 600; text-transform: none; }

/* Cart List */
.cart-list { padding: 16px; display: flex; flex-direction: column; gap: 12px; }

.store-cart-card {
  background: white; border-radius: 16px;
  padding: 16px 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  cursor: pointer; transition: transform 0.2s;
}
.store-cart-card:active { transform: scale(0.98); }

.store-cart-header { display: flex; align-items: center; gap: 14px; }

.store-cart-img {
  width: 52px; height: 52px;
  border-radius: 14px; object-fit: cover;
  flex-shrink: 0;
}

.store-cart-icon {
  width: 52px; height: 52px; background: #E8F5E9;
  border-radius: 14px; display: flex; align-items: center;
  justify-content: center; font-size: 28px; flex-shrink: 0;
}

.store-cart-info { flex: 1; }
.store-cart-info h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.store-cart-info p { font-size: 12px; color: #666; margin: 0; }

.store-cart-images { display: flex; gap: -8px; }
.cart-thumb {
  width: 36px; height: 36px; background: #f5f5f5;
  border-radius: 10px; display: flex; align-items: center;
  justify-content: center; font-size: 20px;
  border: 2px solid white; margin-left: -8px;
}
.cart-thumb:first-child { margin-left: 0; }

/* Add More */
.add-more-btn {
  display: flex; align-items: center; gap: 12px;
  padding: 16px 20px; background: white;
  border-radius: 16px; cursor: pointer;
  border: 2px dashed #e0e0e0;
  color: #4CAF50; font-size: 15px; font-weight: 600;
}
.add-more-btn ion-icon { font-size: 24px; }

/* Store Total */
.store-total {
  display: flex; align-items: center; gap: 4px;
  cursor: pointer;
}
.total-amount {
  font-size: 16px; font-weight: 700; color: #4CAF50;
}
.store-total ion-icon { font-size: 18px; color: #999; }

/* Items List */
.items-list {
  padding: 0 16px 16px;
  display: flex; flex-direction: column; gap: 0;
}

.cart-item {
  display: flex; align-items: center;
  padding: 12px 0; border-bottom: 1px solid #f0f0f0;
}
.cart-item:last-child { border-bottom: none; }

.item-img {
  width: 48px; height: 48px;
  border-radius: 10px; object-fit: cover;
  margin-right: 12px;
}

.item-emoji { font-size: 28px; margin-right: 12px; }

.item-details {
  flex: 1; display: flex; flex-direction: column;
}
.item-name { font-size: 14px; font-weight: 600; color: #1a1a1a; }
.item-price { font-size: 13px; color: #666; }

.quantity-controls {
  display: flex; align-items: center; gap: 8px;
}
.qty-btn {
  width: 32px; height: 32px; border-radius: 8px;
  border: none; background: #f5f5f5;
  font-size: 18px; font-weight: 700;
  cursor: pointer; display: flex;
  align-items: center; justify-content: center;
}
.qty-value {
  width: 28px; text-align: center;
  font-size: 15px; font-weight: 700;
}

/* Cart Summary */
.cart-summary {
  background: white; padding: 20px;
  border-top: 1px solid #e0e0e0;
}
.summary-row {
  display: flex; justify-content: space-between;
  padding: 8px 0; font-size: 14px; color: #666;
}
.summary-row .free { color: #4CAF50; font-weight: 600; }

.summary-total {
  display: flex; justify-content: space-between;
  padding: 16px 0; border-top: 1px solid #e0e0e0;
  margin-top: 8px; font-size: 18px; font-weight: 700;
  color: #1a1a1a;
}

.checkout-btn {
  --background: #4CAF50;
  --border-radius: 12px; height: 52px;
  font-size: 16px; font-weight: 700;
  margin-top: 16px;
}
</style>