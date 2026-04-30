<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/settings"></ion-back-button>
        </ion-buttons>
        <ion-title>My Destination</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="dest-content">

      <!-- Search Bar -->
      <div class="search-section">
        <div class="search-bar">
          <ion-icon :icon="searchOutline" class="search-icon"></ion-icon>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Where are you going?"
            class="search-input"
          >
          <ion-icon
            v-if="searchQuery"
            :icon="closeCircle"
            class="clear-icon"
            @click="searchQuery = ''"
          ></ion-icon>
        </div>
      </div>

      <div class="dest-container">

        <!-- Saved Destinations -->
        <div class="dest-section">
          <h3 class="dest-group-title">Saved</h3>

          <div class="dest-item" v-for="saved in savedDestinations" :key="saved.id" @click="selectDestination(saved)">
            <div class="dest-icon-wrap" :class="saved.type">
              <ion-icon :icon="saved.type === 'home' ? homeOutline : storefrontOutline"></ion-icon>
            </div>
            <div class="dest-info">
              <h4>{{ saved.name }}</h4>
              <p>{{ saved.address }}</p>
            </div>
            <ion-button fill="clear" size="small" @click.stop="editSaved(saved)">
              <ion-icon :icon="ellipsisHorizontalOutline"></ion-icon>
            </ion-button>
          </div>

          <div class="add-saved-btn" @click="addSavedPlace">
            <ion-icon :icon="addCircleOutline"></ion-icon>
            <span>Add a place</span>
          </div>
        </div>

        <!-- Recent Destinations -->
        <div class="dest-section">
          <h3 class="dest-group-title">Recent</h3>

          <div class="dest-item" v-for="recent in filteredRecent" :key="recent.id" @click="selectDestination(recent)">
            <div class="dest-icon-wrap recent">
              <ion-icon :icon="timeOutline"></ion-icon>
            </div>
            <div class="dest-info">
              <h4>{{ recent.name }}</h4>
              <p>{{ recent.address }}</p>
            </div>
            <ion-button fill="clear" size="small" @click.stop="showRecentOptions(recent)">
              <ion-icon :icon="ellipsisHorizontalOutline"></ion-icon>
            </ion-button>
          </div>

          <div v-if="filteredRecent.length === 0" class="no-recent">
            <ion-icon :icon="locationOutline"></ion-icon>
            <p>No recent destinations</p>
          </div>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonButton, IonIcon,
  alertController, actionSheetController, toastController
} from '@ionic/vue';
import {
  searchOutline, closeCircle, homeOutline, storefrontOutline,
  timeOutline, locationOutline, ellipsisHorizontalOutline,
  addCircleOutline
} from 'ionicons/icons';

const router = useRouter();
const searchQuery = ref('');

const savedDestinations = ref([
  { id: 1, name: 'Home', address: 'Brgy. San Isidro, CDO City', type: 'home' },
  { id: 2, name: 'CDO City Palengke', address: 'Cogon Market, CDO City', type: 'work' },
]);

const recentDestinations = ref([
  { id: 1, name: 'SM City CDO', address: 'Mastersons Ave, CDO City' },
  { id: 2, name: 'Brgy. Carmen', address: 'Carmen, Cagayan de Oro City' },
  { id: 3, name: 'Robinsons CDO', address: 'Capitol, CDO City' },
  { id: 4, name: 'Brgy. Lapasan', address: 'Lapasan, CDO City' },
]);

const filteredRecent = computed(() => {
  if (!searchQuery.value) return recentDestinations.value;
  return recentDestinations.value.filter(r =>
    r.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    r.address.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const selectDestination = async (dest) => {
  const toast = await toastController.create({
    message: `Destination set: ${dest.name}`,
    duration: 2000, color: 'medium', position: 'top'
  });
  await toast.present();
  router.back();
};

const editSaved = async (saved) => {
  const actionSheet = await actionSheetController.create({
    header: saved.name,
    buttons: [
      { text: 'Edit', handler: () => {} },
      { text: 'Delete', role: 'destructive', handler: () => {
        savedDestinations.value = savedDestinations.value.filter(s => s.id !== saved.id);
      }},
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const showRecentOptions = async (recent) => {
  const actionSheet = await actionSheetController.create({
    header: recent.name,
    buttons: [
      { text: 'Save this place', handler: () => {} },
      { text: 'Remove from Recent', role: 'destructive', handler: () => {
        recentDestinations.value = recentDestinations.value.filter(r => r.id !== recent.id);
      }},
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const addSavedPlace = async () => {
  const alert = await alertController.create({
    header: 'Add a Place',
    inputs: [
      { name: 'name', type: 'text', placeholder: 'Place name (e.g. Home, Work)' },
      { name: 'address', type: 'text', placeholder: 'Address' }
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Save', handler: (data) => {
        if (data.name && data.address) {
          savedDestinations.value.push({
            id: Date.now(), name: data.name, address: data.address, type: 'work'
          });
        }
      }}
    ]
  });
  await alert.present();
};
</script>

<style scoped>
.dest-content { --background: #f8f9fa; }

.search-section { background: white; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; }

.search-bar { position: relative; }
.search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #999; }
.search-input { width: 100%; padding: 14px 48px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 15px; font-family: inherit; transition: border-color 0.2s; }
.search-input:focus { outline: none; border-color: #4CAF50; }
.clear-icon { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #999; cursor: pointer; }

.dest-container { padding: 16px; }

.dest-section { background: white; border-radius: 16px; padding: 20px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

.dest-group-title { font-size: 13px; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; }

.dest-item {
  display: flex; align-items: center; gap: 14px;
  padding: 12px 0; border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
}
.dest-item:last-of-type { border-bottom: none; }
.dest-item:active { opacity: 0.7; }

.dest-icon-wrap {
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; flex-shrink: 0;
}
.dest-icon-wrap.home { background: #E8F5E9; color: #4CAF50; }
.dest-icon-wrap.work { background: #E3F2FD; color: #2196F3; }
.dest-icon-wrap.recent { background: #f5f5f5; color: #999; }

.dest-info { flex: 1; min-width: 0; }
.dest-info h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.dest-info p { font-size: 13px; color: #666; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.add-saved-btn {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 0; cursor: pointer; color: #4CAF50; font-size: 15px; font-weight: 600;
}
.add-saved-btn ion-icon { font-size: 24px; }

.no-recent { text-align: center; padding: 32px 20px; }
.no-recent ion-icon { font-size: 48px; color: #ddd; margin-bottom: 12px; }
.no-recent p { font-size: 14px; color: #999; margin: 0; }
</style>