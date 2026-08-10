<template>
  <ion-modal :is-open="isOpen" @did-dismiss="$emit('close')" css-class="banner-modal">
    <ion-header>
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-button @click="$emit('close')">
            <ion-icon :icon="closeOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Request Promo Banner</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content class="ion-padding">
      <div class="banner-form">
        <ion-item>
          <ion-label position="stacked">Banner Title</ion-label>
          <ion-input v-model="form.title" placeholder="e.g. Fresh Seafood Sale"></ion-input>
        </ion-item>
        <ion-item>
          <ion-label position="stacked">Subtitle</ion-label>
          <ion-input v-model="form.subtitle" placeholder="e.g. Up to 30% off all seafood"></ion-input>
        </ion-item>
        <ion-item>
          <ion-label position="stacked">Link</ion-label>
          <ion-input v-model="form.link" placeholder="e.g. /category/seafood"></ion-input>
        </ion-item>
        <ion-item>
          <ion-label position="stacked">Image URL (optional)</ion-label>
          <ion-input v-model="form.image" placeholder="/images/banner.jpg"></ion-input>
        </ion-item>

        <div class="banner-preview-box" v-if="form.title || form.subtitle">
          <p class="preview-label">Preview</p>
          <div class="preview-banner">
            <span class="preview-title">{{ form.title || 'Banner Title' }}</span>
            <span class="preview-sub">{{ form.subtitle || 'Banner subtitle' }}</span>
          </div>
        </div>

        <ion-button expand="block" class="submit-banner-btn" @click="$emit('submit', { ...form })">
          Submit Request
        </ion-button>
      </div>
    </ion-content>
  </ion-modal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import {
  IonModal,
  IonHeader,
  IonToolbar,
  IonButtons,
  IonButton,
  IonTitle,
  IonContent,
  IonItem,
  IonLabel,
  IonInput,
  IonIcon,
} from '@ionic/vue';
import { closeOutline } from 'ionicons/icons';
import type { BannerRequest } from '@/types';

const props = defineProps<{ isOpen: boolean }>();
defineEmits<{ close: []; submit: [form: BannerRequest] }>();

const form = ref<BannerRequest>({ title: '', subtitle: '', link: '', image: '' });

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      form.value = { title: '', subtitle: '', link: '', image: '' };
    }
  },
);
</script>

<style scoped>
.banner-form {
  padding-top: 8px;
}
.banner-form ion-item {
  --padding-start: 0;
  margin-bottom: 8px;
}
.banner-form ion-label {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 4px;
}
.banner-preview-box {
  margin: 20px 0;
}
.preview-label {
  font-size: 12px;
  color: #999;
  margin-bottom: 8px;
}
.preview-banner {
  background: linear-gradient(135deg, #4caf50, #2e7d32);
  border-radius: 12px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.preview-title {
  font-size: 16px;
  font-weight: 700;
  color: #fff;
}
.preview-sub {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
}
.submit-banner-btn {
  --border-radius: 12px;
  height: 48px;
  font-weight: 600;
  margin-top: 12px;
  --background: #9c27b0;
}
</style>
