<template>
  <ion-modal :is-open="isOpen" @did-dismiss="$emit('close')">
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Custom Date Range</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="$emit('close')">Close</ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>
    <ion-content>
      <div class="date-range-modal">
        <div class="date-field">
          <label class="date-label">Start Date</label>
          <input type="date" class="date-input" :value="startDate" @input="onStartDateInput" />
        </div>
        <div class="date-field">
          <label class="date-label">End Date</label>
          <input type="date" class="date-input" :value="endDate" @input="onEndDateInput" />
        </div>
        <ion-button expand="block" color="success" @click="$emit('apply')" class="apply-btn"> Apply Range </ion-button>
      </div>
    </ion-content>
  </ion-modal>
</template>

<script setup lang="ts">
import { IonModal, IonHeader, IonToolbar, IonTitle, IonButtons, IonButton, IonContent } from '@ionic/vue';

defineProps<{ isOpen: boolean; startDate: string; endDate: string }>();
const emit = defineEmits<{
  close: [];
  apply: [];
  'update:startDate': [value: string];
  'update:endDate': [value: string];
}>();

const onStartDateInput = (event: Event) => {
  emit('update:startDate', (event.target as HTMLInputElement).value);
};

const onEndDateInput = (event: Event) => {
  emit('update:endDate', (event.target as HTMLInputElement).value);
};
</script>

<style scoped>
.date-range-modal {
  padding: 24px 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.date-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.date-label {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.date-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 16px;
  font-family: inherit;
  color: #333;
  background: #f8f9fa;
  outline: none;
  transition: border-color 0.3s;
}

.date-input:focus {
  border-color: #4caf50;
}

.apply-btn {
  margin-top: 8px;
  --border-radius: 12px;
  font-weight: 600;
  height: 48px;
}
</style>
