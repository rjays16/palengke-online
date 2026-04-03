<template>
  <ion-page>
    <ion-content :fullscreen="true" class="rating-content">

      <!-- Header -->
      <div class="rating-header">
        <div class="close-btn" @click="skipRating">
          <ion-icon :icon="closeOutline"></ion-icon>
        </div>
        <span class="contact-support" @click="contactSupport">Contact Support</span>
      </div>

      <div class="rating-container">

        <!-- Rating Label + Stars -->
        <div class="stars-section">
          <h2 class="rating-label">{{ ratingLabel }}</h2>
          <div class="stars-row">
            <div
              v-for="star in 5"
              :key="star"
              class="star"
              :class="{ filled: star <= selectedRating }"
              @click="selectRating(star)"
            >
              ⭐
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <!-- What did you like -->
        <div class="feedback-section" v-if="selectedRating >= 4">
          <h3 class="feedback-title">What did you like about the delivery?</h3>
          <div class="tags-grid">
            <div
              v-for="tag in positiveTags"
              :key="tag"
              class="tag-chip"
              :class="{ selected: selectedTags.includes(tag) }"
              @click="toggleTag(tag)"
            >
              {{ tag }}
            </div>
          </div>
        </div>

        <!-- What went wrong (low rating) -->
        <div class="feedback-section" v-if="selectedRating > 0 && selectedRating < 4">
          <h3 class="feedback-title">What went wrong?</h3>
          <div class="tags-grid">
            <div
              v-for="tag in negativeTags"
              :key="tag"
              class="tag-chip negative"
              :class="{ selected: selectedTags.includes(tag) }"
              @click="toggleTag(tag)"
            >
              {{ tag }}
            </div>
          </div>
        </div>

        <!-- Tell us more -->
        <div class="tell-more-section">
          <div class="tell-more-input">
            <input
              v-model="feedback"
              type="text"
              placeholder="Tell us more (Optional)"
              class="feedback-input"
            >
          </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary-card">
          <div class="summary-store">
            <div class="summary-store-icon">{{ orderData.storeEmoji }}</div>
            <div class="summary-info">
              <p class="summary-store-name">{{ orderData.storeName }}</p>
              <p class="summary-items">{{ orderData.items }}</p>
            </div>
            <p class="summary-amount">₱{{ orderData.total.toFixed(2) }}</p>
          </div>
        </div>

      </div>

    </ion-content>

    <!-- Submit Button -->
    <div class="submit-bar">
      <ion-button
        expand="block"
        class="submit-btn"
        :disabled="selectedRating === 0 || isSubmitting"
        @click="submitRating"
      >
        <span v-if="!isSubmitting">Submit</span>
        <ion-spinner v-else name="crescent"></ion-spinner>
      </ion-button>
    </div>

  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonContent, IonButton, IonIcon, IonSpinner,
  alertController, toastController
} from '@ionic/vue';
import { closeOutline } from 'ionicons/icons';

const router = useRouter();

const selectedRating = ref(0);
const selectedTags = ref([]);
const feedback = ref('');
const isSubmitting = ref(false);

const orderData = ref({
  storeName: "Juan's Fresh Market",
  storeEmoji: '🥦',
  items: 'Bawang/Garlic, Kamatis/Tomato',
  total: 207.00
});

const positiveTags = [
  'Professional', 'Punctuality', 'Friendly',
  'Contactless Delivery', 'Followed Instructions', 'Hygienic'
];

const negativeTags = [
  'Late Delivery', 'Wrong Items', 'Poor Packaging',
  'Unprofessional', 'Missing Items', 'Cold Food'
];

const ratingLabel = computed(() => {
  const labels = ['', 'Poor 😞', 'Fair 😐', 'Good 😊', 'Great 😄', 'Perfect 🤩'];
  return selectedRating.value > 0 ? labels[selectedRating.value] : 'Rate your experience';
});

const selectRating = (rating) => {
  selectedRating.value = rating;
  selectedTags.value = [];
};

const toggleTag = (tag) => {
  const index = selectedTags.value.indexOf(tag);
  if (index > -1) { selectedTags.value.splice(index, 1); }
  else { selectedTags.value.push(tag); }
};

const submitRating = async () => {
  if (selectedRating.value === 0) return;
  isSubmitting.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 1500));
    const toast = await toastController.create({
      message: '✅ Thank you for your feedback!',
      duration: 2000, color: 'success', position: 'top'
    });
    await toast.present();
    router.push('/activity');
  } finally {
    isSubmitting.value = false;
  }
};

const skipRating = async () => {
  const alert = await alertController.create({
    header: 'Skip Rating?',
    message: 'Your feedback helps us improve our service.',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Skip', handler: () => router.push('/activity') }
    ]
  });
  await alert.present();
};

const contactSupport = () => router.push('/help');
</script>

<style scoped>
.rating-content { --background: #ffffff; }

/* Header */
.rating-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 52px 20px 16px;
  border-bottom: 1px solid #f0f0f0;
}
.close-btn {
  width: 36px; height: 36px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
}
.close-btn ion-icon { font-size: 26px; color: #1a1a1a; }
.contact-support { font-size: 14px; color: #4CAF50; font-weight: 600; cursor: pointer; }

/* Rating Container */
.rating-container { padding: 24px 20px; }

/* Stars Section */
.stars-section { text-align: center; margin-bottom: 24px; }
.rating-label { font-size: 24px; font-weight: 700; color: #1a1a1a; margin: 0 0 20px 0; }
.stars-row { display: flex; justify-content: center; gap: 12px; }
.star {
  font-size: 44px; cursor: pointer;
  transition: transform 0.2s; filter: grayscale(100%); opacity: 0.4;
}
.star:active { transform: scale(0.9); }
.star.filled { filter: grayscale(0%); opacity: 1; }

/* Divider */
.divider { height: 1px; background: #f0f0f0; margin: 0 0 24px 0; }

/* Feedback */
.feedback-section { margin-bottom: 24px; }
.feedback-title { font-size: 16px; font-weight: 600; color: #666; margin: 0 0 16px 0; }

.tags-grid { display: flex; flex-wrap: wrap; gap: 10px; }
.tag-chip {
  padding: 10px 18px; border-radius: 24px;
  border: 2px solid #e0e0e0; background: white;
  font-size: 14px; font-weight: 600; color: #555;
  cursor: pointer; transition: all 0.2s;
}
.tag-chip:active { transform: scale(0.97); }
.tag-chip.selected { border-color: #1a1a1a; background: #1a1a1a; color: white; }
.tag-chip.negative.selected { border-color: #f44336; background: #f44336; color: white; }

/* Tell More */
.tell-more-section { margin-bottom: 24px; }
.tell-more-input { border-bottom: 2px solid #e0e0e0; padding-bottom: 12px; }
.feedback-input {
  width: 100%; border: none; background: transparent;
  font-size: 16px; font-family: inherit; padding: 8px 0;
  color: #1a1a1a !important; -webkit-text-fill-color: #1a1a1a !important;
}
.feedback-input:focus { outline: none; }
.feedback-input::placeholder { color: #999; }

/* Order Summary */
.order-summary-card {
  background: #f8f9fa; border-radius: 16px; padding: 16px;
}
.summary-store { display: flex; align-items: center; gap: 14px; }
.summary-store-icon {
  width: 52px; height: 52px; background: #E8F5E9;
  border-radius: 14px; display: flex; align-items: center;
  justify-content: center; font-size: 28px; flex-shrink: 0;
}
.summary-info { flex: 1; }
.summary-store-name { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.summary-items { font-size: 13px; color: #666; margin: 0; }
.summary-amount { font-size: 16px; font-weight: 800; color: #1a1a1a; }

/* Submit Bar */
.submit-bar {
  background: white; padding: 16px 20px;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
}
.submit-btn {
  --background: #4CAF50; --border-radius: 16px;
  height: 56px; font-size: 16px; font-weight: 700; text-transform: none;
}
.submit-btn:disabled { --background: #e0e0e0; }
</style>