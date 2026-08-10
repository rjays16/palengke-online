<template>
  <ion-modal :is-open="isOpen" @did-dismiss="$emit('close')" css-class="notice-modal">
    <ion-header>
      <ion-toolbar color="warning">
        <ion-title>Notice</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="$emit('close')">
            <ion-icon :icon="closeOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>
    <ion-content class="ion-padding">
      <div class="notice-modal-body" v-if="notice">
        <div class="notice-modal-icon">
          <ion-icon :icon="megaphoneOutline"></ion-icon>
        </div>
        <h2 class="notice-modal-title">{{ (notice as any).title }}</h2>
        <p class="notice-modal-msg">{{ (notice as any).message }}</p>
        <ion-button expand="block" class="notice-modal-btn" @click="$emit('close')">Got it</ion-button>
      </div>
    </ion-content>
  </ion-modal>
</template>

<script setup lang="ts">
import { IonModal, IonHeader, IonToolbar, IonTitle, IonButtons, IonButton, IonIcon, IonContent } from '@ionic/vue';
import { closeOutline, megaphoneOutline } from 'ionicons/icons';
import type { Notice } from '@/types';

defineProps<{ isOpen: boolean; notice: Notice | null }>();
defineEmits<{ close: [] }>();
</script>

<style scoped>
.notice-modal-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 32px 16px 16px;
  gap: 12px;
}
.notice-modal-icon {
  font-size: 48px;
  color: #f57f17;
  margin-bottom: 8px;
}
.notice-modal-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}
.notice-modal-msg {
  font-size: 14px;
  color: #666;
  line-height: 1.6;
  margin: 0 0 8px;
}
.notice-modal-btn {
  --border-radius: 12px;
  height: 48px;
  font-weight: 600;
  --background: #f57f17;
}
</style>
