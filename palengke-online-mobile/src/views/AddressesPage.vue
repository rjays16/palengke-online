<template>
<!-- AddressesPage.vue - Save as src/views/AddressesPage.vue -->
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start"><ion-back-button default-href="/profile"></ion-back-button></ion-buttons>
        <ion-title>Saved Addresses</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content class="addr-content">
      <div class="addr-list">
        <div v-for="addr in addresses" :key="addr.id" class="addr-card" @click="selectAddress(addr)">
          <div class="addr-icon" :class="addr.type">
            <span>{{ addr.type === 'home' ? '🏠' : addr.type === 'work' ? '🏢' : '📍' }}</span>
          </div>
          <div class="addr-info">
            <div class="addr-title-row">
              <h4>{{ addr.name }}</h4>
              <span class="default-tag" v-if="addr.isDefault">Default</span>
            </div>
            <p>{{ addr.address }}</p>
          </div>
          <div class="addr-actions">
            <ion-button fill="clear" size="small" @click.stop="editAddress(addr)">
              <ion-icon :icon="createOutline"></ion-icon>
            </ion-button>
          </div>
        </div>
      </div>
      <div class="add-addr-btn" @click="addNewAddress">
        <ion-icon :icon="addCircleOutline"></ion-icon>
        <span>Add New Address</span>
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
  alertController, toastController
} from '@ionic/vue';
import { createOutline, addCircleOutline } from 'ionicons/icons';

const router = useRouter();

const addresses = ref([
  { id: 1, type: 'home', name: 'Home', address: 'Brgy. Carmen, Cagayan de Oro City', isDefault: true },
  { id: 2, type: 'work', name: 'Work', address: 'Divisoria, CDO City', isDefault: false },
]);

const selectAddress = async (addr) => {
  addresses.value.forEach(a => a.isDefault = false);
  addr.isDefault = true;
  const toast = await toastController.create({
    message: `${addr.name} set as default address`,
    duration: 2000, color: 'success', position: 'top'
  });
  await toast.present();
  router.back();
};

const editAddress = async (addr) => {
  const alert = await alertController.create({
    header: `Edit ${addr.name}`,
    inputs: [{ name: 'address', type: 'text', value: addr.address, placeholder: 'Address' }],
    buttons: [
      { text: 'Delete', role: 'destructive', handler: () => {
        addresses.value = addresses.value.filter(a => a.id !== addr.id);
      }},
      { text: 'Cancel', role: 'cancel' },
      { text: 'Save', handler: (data) => { if (data.address) addr.address = data.address; } }
    ]
  });
  await alert.present();
};

const addNewAddress = async () => {
  const alert = await alertController.create({
    header: 'Add New Address',
    inputs: [
      { name: 'name', type: 'text', placeholder: 'Label (e.g. Home, Work)' },
      { name: 'address', type: 'text', placeholder: 'Full address' },
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Add', handler: (data) => {
        if (data.name && data.address) {
          addresses.value.push({ id: Date.now(), type: 'other', name: data.name, address: data.address, isDefault: false });
        }
      }}
    ]
  });
  await alert.present();
};
</script>

<style scoped>
.addr-content { --background: #f8f9fa; }
.addr-list { padding: 16px; display: flex; flex-direction: column; gap: 12px; }
.addr-card { background: white; border-radius: 16px; padding: 16px; display: flex; align-items: center; gap: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); cursor: pointer; }
.addr-card:active { transform: scale(0.98); }
.addr-icon { width: 48px; height: 48px; border-radius: 14px; background: #E8F5E9; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
.addr-info { flex: 1; }
.addr-title-row { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
.addr-info h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0; }
.addr-info p { font-size: 13px; color: #666; margin: 0; }
.default-tag { background: #E8F5E9; color: #4CAF50; padding: 2px 8px; border-radius: 8px; font-size: 11px; font-weight: 700; }
.addr-actions ion-button { --padding-start: 4px; --padding-end: 4px; }
.add-addr-btn { display: flex; align-items: center; gap: 12px; margin: 0 16px; padding: 16px 20px; background: white; border-radius: 16px; border: 2px dashed #e0e0e0; cursor: pointer; color: #4CAF50; font-size: 15px; font-weight: 600; }
.add-addr-btn ion-icon { font-size: 24px; }
</style>