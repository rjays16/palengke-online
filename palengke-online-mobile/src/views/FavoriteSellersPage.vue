<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/profile"></ion-back-button>
        </ion-buttons>
        <ion-title>Favorite Sellers</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="favorites-content">

      <!-- Empty State -->
      <div v-if="favoriteSellers.length === 0" class="empty-state">
        <div class="empty-icon"><ion-icon :icon="heartOutline"></ion-icon></div>
        <h3>No favorites yet</h3>
        <p>Save your favorite sellers to order quickly</p>
        <ion-button color="success" @click="goToBrowse" class="browse-btn">
          Browse Sellers
        </ion-button>
      </div>

      <!-- Favorites List -->
      <div v-else class="favorites-list">
        <div
          v-for="seller in favoriteSellers"
          :key="seller.id"
          class="seller-card"
          @click="openSeller(seller)"
        >
          <div class="seller-banner">
            <img :src="seller.image" class="seller-img" :alt="seller.name">
            <button class="favorite-btn active" @click.stop="toggleFavorite(seller)">
              <ion-icon :icon="heart"></ion-icon>
            </button>
          </div>
          <div class="seller-info">
            <h3>{{ seller.name }}</h3>
            <div class="seller-meta">
              <span class="rating"><ion-icon :icon="starOutline"></ion-icon> {{ seller.rating }}</span>
              <span class="dot">•</span>
              <span class="time">{{ seller.deliveryTime }}</span>
              <span class="dot">•</span>
              <span class="distance">{{ seller.distance }}</span>
            </div>
            <div class="seller-tags">
              <span v-for="tag in seller.tags" :key="tag" class="tag">{{ tag }}</span>
            </div>
          </div>
        </div>
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonBackButton, IonButton, IonIcon, toastController } from '@ionic/vue';
import { starOutline, heartOutline, heart } from 'ionicons/icons';

const router = useRouter();

const favoriteSellers = ref([
  {
    id: 1,
    name: "Juan's Fresh Market",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e',
    color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
    rating: '4.8',
    deliveryTime: '15-25 min',
    distance: '2.3 km',
    tags: ['Vegetables', 'Fruits']
  },
  {
    id: 2,
    name: "Ana's Palengke",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999',
    color: 'linear-gradient(135deg, #FF9800, #E65100)',
    rating: '4.6',
    deliveryTime: '10-20 min',
    distance: '1.5 km',
    tags: ['Fruits', 'Vegetables']
  }
]);

const openSeller = (seller) => {
  router.push(`/store/${seller.id}`);
};

const toggleFavorite = async (seller) => {
  seller.isFavorite = !seller.isFavorite;
  const toast = await toastController.create({
    message: seller.isFavorite ? 'Added to favorites!' : 'Removed from favorites',
    duration: 1500,
    color: 'success',
    position: 'top'
  });
  await toast.present();
};

const goToBrowse = () => router.push('/browse');
</script>

<style scoped>
.favorites-content { --background: #f8f9fa; }

.empty-state {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 80px 40px; text-align: center;
}
.empty-icon { font-size: 80px; margin-bottom: 20px; }
.empty-state h3 { font-size: 22px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 15px; color: #999; margin: 0 0 24px 0; }
.browse-btn { --border-radius: 14px; height: 48px; font-weight: 600; text-transform: none; }

.favorites-list { padding: 16px; display: flex; flex-direction: column; gap: 16px; }

.seller-card {
  background: white; border-radius: 16px;
  overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  cursor: pointer;
}
.seller-card:active { transform: scale(0.98); }

.seller-banner {
  height: 120px; display: flex; align-items: center;
  justify-content: center; position: relative;
  overflow: hidden;
}
.seller-img {
  position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;
}
.seller-emoji { font-size: 56px; }

.favorite-btn {
  position: absolute; top: 12px; right: 12px;
  background: white; border: none;
  border-radius: 50%; width: 40px; height: 40px;
  font-size: 20px; cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.seller-info { padding: 14px 16px; }
.seller-info h3 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 6px 0; }
.seller-meta { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; flex-wrap: wrap; }
.rating, .time, .distance { font-size: 13px; color: #666; }
.dot { color: #ccc; font-size: 12px; }

.seller-tags { display: flex; gap: 6px; flex-wrap: wrap; }
.tag {
  background: #f0f0f0; color: #555;
  padding: 3px 10px; border-radius: 10px;
  font-size: 11px; font-weight: 600;
}
</style>