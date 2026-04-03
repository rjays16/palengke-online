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
          @click="goToCheckout(store)"
        >
          <!-- Store Header -->
          <div class="store-cart-header">
            <div class="store-cart-icon">{{ store.emoji }}</div>
            <div class="store-cart-info">
              <h3>{{ store.name }}</h3>
              <p>{{ store.itemCount }} items • {{ store.deliveryTime }} • {{ store.distance }}</p>
            </div>
            <div class="store-cart-images">
              <div
                v-for="(item, i) in store.items.slice(0, 3)"
                :key="i"
                class="cart-thumb"
              >
                {{ item.emoji }}
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
import { searchOutline, addCircleOutline } from 'ionicons/icons';

const router = useRouter();

const cartStores = ref([
  {
    id: 1,
    name: "Juan's Fresh Market",
    emoji: '🥦',
    itemCount: 2,
    deliveryTime: 'From 25 mins',
    distance: '2.3 km',
    items: [
      { id: 1, name: 'Bawang/Garlic', emoji: '🧄', price: 66.00, quantity: 1 },
      { id: 2, name: 'Kamatis/Tomato', emoji: '🍅', price: 48.00, quantity: 2 }
    ]
  },
  {
    id: 2,
    name: "Ana's Palengke - Cogon",
    emoji: '🍎',
    itemCount: 2,
    deliveryTime: 'From 20 mins',
    distance: '1.5 km',
    items: [
      { id: 10, name: 'Apple Med. Size', emoji: '🍎', price: 162.00, quantity: 1 },
      { id: 11, name: 'Banana (Large)', emoji: '🍌', price: 129.00, quantity: 1 }
    ]
  }
]);

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

const goToCheckout = (store) => router.push(`/checkout/${store.id}`);
const goToBrowse = () => router.push('/browse');
</script>

<style scoped>
.cart-content { --background: #f8f9fa; }

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
</style>