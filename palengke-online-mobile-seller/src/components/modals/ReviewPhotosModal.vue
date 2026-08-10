<template>
  <ion-modal :is-open="isOpen" @did-dismiss="$emit('close')" class="review-modal">
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Review Your Photos</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="$emit('close')">
            <ion-icon :icon="closeOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>
    <ion-content class="modal-content">
      <div class="review-container">
        <div class="photo-card">
          <h3>ID Front</h3>
          <div class="photo-frame">
            <img v-if="photos.idFront" :src="photos.idFront" alt="ID Front" />
            <div v-else class="no-photo">
              <ion-icon :icon="imageOutline"></ion-icon>
              <span>No photo</span>
            </div>
          </div>
        </div>
        <div class="photo-card">
          <h3>ID Back</h3>
          <div class="photo-frame">
            <img v-if="photos.idBack" :src="photos.idBack" alt="ID Back" />
            <div v-else class="no-photo">
              <ion-icon :icon="imageOutline"></ion-icon>
              <span>No photo</span>
            </div>
          </div>
        </div>
        <div class="photo-card">
          <h3>Selfie with ID</h3>
          <div class="photo-frame">
            <img v-if="photos.selfie" :src="photos.selfie" alt="Selfie" />
            <div v-else class="no-photo">
              <ion-icon :icon="imageOutline"></ion-icon>
              <span>No photo</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <ion-button expand="block" class="close-review-btn" @click="$emit('close')">
          <ion-icon slot="start" :icon="checkmarkCircleOutline"></ion-icon>
          Done Reviewing
        </ion-button>
      </div>
    </ion-content>
  </ion-modal>
</template>

<script setup lang="ts">
import { IonModal, IonHeader, IonToolbar, IonTitle, IonButtons, IonButton, IonIcon, IonContent } from '@ionic/vue';
import { closeOutline, imageOutline, checkmarkCircleOutline } from 'ionicons/icons';

interface ReviewPhotos {
  idFront: string | null;
  idBack: string | null;
  selfie: string | null;
}

defineProps<{ isOpen: boolean; photos: ReviewPhotos }>();
defineEmits<{ close: [] }>();
</script>
