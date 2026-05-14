<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start"><ion-back-button default-href="/profile"></ion-back-button></ion-buttons>
        <ion-title>Help & Support</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="help-content">

      <!-- Search -->
      <div class="help-search">
        <ion-icon :icon="searchOutline" class="help-search-icon"></ion-icon>
        <input v-model="searchQuery" type="text" placeholder="Search for help..." class="help-search-input">
      </div>

      <!-- Contact Options -->
      <div class="contact-section">
        <h3>Contact Us</h3>
        <div class="contact-grid">
          <div class="contact-card" @click="liveChat">
            <span class="contact-icon"><ion-icon :icon="chatbubblesOutline"></ion-icon></span>
            <p>Live Chat</p>
            <span class="contact-status green">Online</span>
          </div>
          <div class="contact-card" @click="callSupport">
            <span class="contact-icon"><ion-icon :icon="callOutline"></ion-icon></span>
            <p>Call Us</p>
            <span class="contact-status">24/7</span>
          </div>
          <div class="contact-card" @click="emailSupport">
            <span class="contact-icon"><ion-icon :icon="mailOutline"></ion-icon></span>
            <p>Email</p>
            <span class="contact-status">24hrs</span>
          </div>
          <div class="contact-card" @click="reportIssue">
            <span class="contact-icon"><ion-icon :icon="alertCircleOutline"></ion-icon></span>
            <p>Report</p>
            <span class="contact-status">Issue</span>
          </div>
        </div>
      </div>

      <!-- FAQ -->
      <div class="faq-section">
        <h3>Frequently Asked Questions</h3>
        <div class="faq-list">
          <div v-for="faq in filteredFaqs" :key="faq.id" class="faq-item" @click="toggleFaq(faq.id)">
            <div class="faq-q">
              <span>{{ faq.question }}</span>
              <ion-icon :icon="openFaqs.includes(faq.id) ? chevronUpOutline : chevronDownOutline"></ion-icon>
            </div>
            <div class="faq-a" v-if="openFaqs.includes(faq.id)">
              <p>{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>

      <input type="file" ref="photoInputRef" accept="image/*" @change="onPhotoSelected" hidden>
    </ion-content>

    <div class="custom-modal-overlay" v-if="showReportSuccess" @click="showReportSuccess = false">
      <div class="custom-modal" @click.stop>
        <img src="/assets/images/checkmark_success.png" alt="Success" class="success-img">
        <h3>Report submitted!</h3>
        <p>We will review it shortly.</p>
        <ion-button expand="block" class="modal-btn" @click="showReportSuccess = false">OK</ion-button>
      </div>
    </div>

  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonIcon,
  alertController, toastController, actionSheetController
} from '@ionic/vue';
import { searchOutline, chevronUpOutline, chevronDownOutline, alertCircleOutline, chatbubblesOutline, callOutline, mailOutline, storefrontOutline, bicycleOutline, documentTextOutline } from 'ionicons/icons';
import { submitReport } from '../services/reportService';

const router = useRouter();
const searchQuery = ref('');
const openFaqs = ref([]);

const faqs = ref([
  { id: 1, question: 'How do I place an order?', answer: 'Browse sellers, add items to cart, then proceed to checkout. Choose your delivery option and payment method, then tap Place Order.' },
  { id: 2, question: 'How do I track my order?', answer: 'After placing your order, go to Activity tab. Tap your active order to see the real-time tracking map.' },
  { id: 3, question: 'What payment methods are accepted?', answer: 'We accept GCash, Cash on Delivery, and Palengke Wallet. More payment options coming soon!' },
  { id: 4, question: 'Can I cancel my order?', answer: 'You can cancel before the seller confirms your order. Once confirmed, cancellation may not be possible. Contact support for help.' },
  { id: 5, question: 'How do I add a tip for the driver?', answer: 'After checkout, you will see a tip page where you can add ₱25-₱250 or a custom amount. 100% goes to your driver!' },
  { id: 6, question: 'What if items are unavailable?', answer: 'If a seller is closed, their items will show as Unavailable. Try another seller or check back when they open.' },
]);

const filteredFaqs = computed(() => {
  if (!searchQuery.value) return faqs.value;
  const q = searchQuery.value.toLowerCase();
  return faqs.value.filter(f => f.question.toLowerCase().includes(q) || f.answer.toLowerCase().includes(q));
});

const toggleFaq = (id) => {
  const i = openFaqs.value.indexOf(id);
  if (i > -1) openFaqs.value.splice(i, 1);
  else openFaqs.value.push(id);
};

const liveChat = async () => {
  const toast = await toastController.create({ message: 'Connecting to live chat...', duration: 2000, color: 'medium', position: 'top' });
  await toast.present();
};

const callSupport = async () => {
  const alert = await alertController.create({
    header: 'Call Support',
    message: '1800-PALENGKE\nAvailable 24/7',
    buttons: [{ text: 'Cancel', role: 'cancel' }, { text: 'Call Now', handler: () => { window.location.href = 'tel:1800-PALENGKE'; } }]
  });
  await alert.present();
};

const emailSupport = async () => {
  const toast = await toastController.create({ 
    message: 'Opening email app...', 
    duration: 1500, 
    color: 'medium', 
    position: 'top' 
  });
  await toast.present();
  setTimeout(() => {
    window.location.href = 'mailto:support@palengke-online.com';
  }, 500);
};

const reportIssue = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'What do you want to report?',
    buttons: [
      {
        text: 'Report a Vendor',
        icon: storefrontOutline,
        handler: () => showReportForm('seller', 'Report vendor')
      },
      {
        text: 'Report a Driver',
        icon: bicycleOutline,
        handler: () => showReportForm('driver', 'Report driver')
      },
      {
        text: 'Other Issue',
        icon: documentTextOutline,
        handler: () => showReportForm('other', 'Report issue')
      },
      { text: 'Cancel', role: 'cancel' }
    ]
  });
  await actionSheet.present();
};

const showReportSuccess = ref(false)
const reportPhoto = ref('')
const photoInputRef = ref(null)

const triggerPhotoPicker = () => { photoInputRef.value?.click() }

const onPhotoSelected = (e) => {
  const file = e.target.files?.[0]
  if (file) readFileAsDataURL(file).then(url => { reportPhoto.value = url })
}

const showReportForm = async (type, subject) => {
  const alert = await alertController.create({
    header: subject,
    message: reportPhoto.value ? '📷 Photo attached' : '',
    inputs: [
      { name: 'subject', type: 'text', placeholder: 'Short summary (e.g. Rude driver)' },
      { name: 'description', type: 'textarea', placeholder: 'Describe what happened in detail...' }
    ],
    buttons: [
      { text: reportPhoto.value ? '📷 Change Photo' : '📷 Add Photo', handler: () => { triggerPhotoPicker(); return false } },
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Submit',
        handler: async (data) => {
          if (!data.description) {
            const toast = await toastController.create({
              message: 'Please describe the issue', duration: 2000, color: 'warning', position: 'top'
            });
            await toast.present();
            return false
          }
          submitReport({
            type,
            submittedBy: 'Customer',
            subject: data.subject || subject,
            description: data.description,
            image: reportPhoto.value || undefined,
            priority: 'medium'
          })
          reportPhoto.value = ''
          showReportSuccess.value = true
        }
      }
    ]
  });
  await alert.present();
};
</script>

<style scoped>
.help-content { --background: #f8f9fa; }

.help-search { background: white; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; position: relative; }
.help-search-icon { position: absolute; left: 28px; top: 50%; transform: translateY(-50%); font-size: 18px; color: #999; }
.help-search-input { width: 100%; padding: 13px 16px 13px 44px; background: #f5f5f5; border: none; border-radius: 12px; font-size: 15px; font-family: inherit; color: #1a1a1a !important; -webkit-text-fill-color: #1a1a1a !important; }
.help-search-input:focus { outline: none; }
.help-search-input::placeholder { color: #999; }

.contact-section { background: white; padding: 20px; margin-bottom: 8px; border-bottom: 1px solid #f0f0f0; }
.contact-section h3 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }
.contact-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.contact-card { background: #f8f9fa; border-radius: 14px; padding: 14px 8px; text-align: center; cursor: pointer; transition: transform 0.2s; }
.contact-card:active { transform: scale(0.95); }
.contact-icon { font-size: 28px; display: block; margin-bottom: 6px; }
.contact-card p { font-size: 13px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
.contact-status { font-size: 11px; color: #999; }
.contact-status.green { color: #4CAF50; font-weight: 600; }

.faq-section { background: white; padding: 20px; }
.faq-section h3 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }
.faq-list { display: flex; flex-direction: column; }
.faq-item { border-bottom: 1px solid #f0f0f0; }
.faq-item:last-child { border-bottom: none; }
.faq-q { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; cursor: pointer; gap: 12px; }
.faq-q span { font-size: 14px; font-weight: 600; color: #1a1a1a; line-height: 1.4; flex: 1; }
.faq-q ion-icon { font-size: 18px; color: #4CAF50; flex-shrink: 0; }
.faq-a { padding: 0 0 14px 0; }
.faq-a p { font-size: 13px; color: #555; margin: 0; line-height: 1.6; background: #f8f9fa; padding: 12px 14px; border-radius: 10px; border-left: 3px solid #4CAF50; }
</style>