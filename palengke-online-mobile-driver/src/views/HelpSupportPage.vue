<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/home"></ion-back-button>
        </ion-buttons>
        <ion-title>Help & Support</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="help-content">

      <!-- Search Bar -->
      <div class="search-section">
        <div class="search-bar">
          <ion-icon :icon="searchOutline" class="search-icon"></ion-icon>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search for help..."
            class="search-input"
            @input="onSearch"
          >
          <ion-icon v-if="searchQuery" :icon="closeCircleOutline" class="clear-icon" @click="searchQuery = ''"></ion-icon>
        </div>
      </div>

      <div class="help-container">

        <!-- Contact Support Cards -->
        <div class="contact-section">
          <h3 class="section-title">Contact Support</h3>
          <div class="contact-grid">
            <div class="contact-card" @click="chatSupport">
              <div class="contact-icon blue">
                <ion-icon :icon="chatbubbleOutline"></ion-icon>
              </div>
              <span>Live Chat</span>
              <span class="contact-status online">Online</span>
            </div>
            <div class="contact-card" @click="callSupport">
              <div class="contact-icon green">
                <ion-icon :icon="callOutline"></ion-icon>
              </div>
              <span>Call Us</span>
              <span class="contact-status">24/7</span>
            </div>
            <div class="contact-card" @click="emailSupport">
              <div class="contact-icon orange">
                <ion-icon :icon="mailOutline"></ion-icon>
              </div>
              <span>Email</span>
              <span class="contact-status">Reply in 24h</span>
            </div>
            <div class="contact-card" @click="reportProblem">
              <div class="contact-icon red">
                <ion-icon :icon="warningOutline"></ion-icon>
              </div>
              <span>Report</span>
              <span class="contact-status">Issue</span>
            </div>
          </div>
        </div>

        <!-- Quick Topics -->
        <div class="topics-section">
          <h3 class="section-title">Quick Topics</h3>
          <div class="topics-grid">
            <div class="topic-chip" v-for="topic in topics" :key="topic.id"
              :class="{ active: selectedTopic === topic.id }"
              @click="filterByTopic(topic.id)">
              <ion-icon :icon="topic.icon"></ion-icon>
              <span>{{ topic.name }}</span>
            </div>
          </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
          <h3 class="section-title">
            {{ selectedTopic === 'all' ? 'Frequently Asked Questions' : topics.find(t => t.id === selectedTopic)?.name }}
          </h3>

          <div v-if="filteredFaqs.length === 0" class="no-results">
            <ion-icon :icon="searchOutline"></ion-icon>
            <p>No results found for "{{ searchQuery }}"</p>
          </div>

          <div v-else class="faq-list">
            <div
              v-for="faq in filteredFaqs"
              :key="faq.id"
              class="faq-item"
              @click="toggleFaq(faq.id)"
            >
              <div class="faq-question">
                <span>{{ faq.question }}</span>
                <ion-icon
                  :icon="openFaqs.includes(faq.id) ? chevronUpOutline : chevronDownOutline"
                  class="faq-arrow"
                ></ion-icon>
              </div>
              <div class="faq-answer" v-if="openFaqs.includes(faq.id)">
                <p>{{ faq.answer }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Useful Links -->
        <div class="links-section">
          <h3 class="section-title">Useful Links</h3>
          <div class="links-list">
            <div class="link-item" @click="openLink('terms')">
              <ion-icon :icon="documentTextOutline"></ion-icon>
              <span>Terms & Conditions</span>
              <ion-icon :icon="chevronForwardOutline" class="link-arrow"></ion-icon>
            </div>
            <div class="link-item" @click="openLink('privacy')">
              <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              <span>Privacy Policy</span>
              <ion-icon :icon="chevronForwardOutline" class="link-arrow"></ion-icon>
            </div>
            <div class="link-item" @click="openLink('safety')">
              <ion-icon :icon="alertCircleOutline"></ion-icon>
              <span>Safety Guidelines</span>
              <ion-icon :icon="chevronForwardOutline" class="link-arrow"></ion-icon>
            </div>
            <div class="link-item" @click="rateApp">
              <ion-icon :icon="starOutline"></ion-icon>
              <span>Rate Our App</span>
              <ion-icon :icon="chevronForwardOutline" class="link-arrow"></ion-icon>
            </div>
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
  alertController, toastController
} from '@ionic/vue';
import {
  searchOutline, closeCircleOutline, chatbubbleOutline,
  callOutline, mailOutline, warningOutline,
  chevronUpOutline, chevronDownOutline, chevronForwardOutline,
  documentTextOutline, shieldCheckmarkOutline, alertCircleOutline,
  starOutline, cashOutline, bicycleOutline, personOutline,
  cubeOutline, helpCircleOutline
} from 'ionicons/icons';

const router = useRouter();

const searchQuery = ref('');
const selectedTopic = ref('all');
const openFaqs = ref([]);

const topics = ref([
  { id: 'all', name: 'All', icon: helpCircleOutline },
  { id: 'earnings', name: 'Earnings', icon: cashOutline },
  { id: 'orders', name: 'Orders', icon: cubeOutline },
  { id: 'account', name: 'Account', icon: personOutline },
  { id: 'delivery', name: 'Delivery', icon: bicycleOutline },
]);

const faqs = ref([
  { id: 1, topic: 'earnings', question: 'When do I get paid?', answer: 'Earnings are credited to your account daily. You can withdraw to your GCash or bank account anytime.' },
  { id: 2, topic: 'earnings', question: 'How is my delivery fee calculated?', answer: 'Delivery fee is based on distance. The base fee starts at ₱50 and increases by ₱10 per kilometer. Tips from customers are added on top.' },
  { id: 3, topic: 'earnings', question: 'What is the commission deduction?', answer: 'Palengke Online deducts 10% commission from each delivery fee. Tips are NOT subject to commission - you keep 100% of tips!' },
  { id: 4, topic: 'orders', question: 'How do I accept an order?', answer: 'When a new order appears, tap the Accept button within 30 seconds. If Auto Accept is ON in settings, orders will be automatically accepted for you.' },
  { id: 5, topic: 'orders', question: 'Can I cancel an accepted order?', answer: 'Yes, but cancellations affect your acceptance rate. To cancel, go to Active Delivery, tap More, then Cancel Order. Please select the correct reason.' },
  { id: 6, topic: 'orders', question: 'What if the seller is not available?', answer: 'Call or chat the seller first. If still unreachable, you can cancel the order with the reason "Seller not available" without affecting your rate.' },
  { id: 7, topic: 'account', question: 'How do I update my documents?', answer: 'Go to Profile > Documents and tap on any document to update it. Make sure documents are not expired to avoid account suspension.' },
  { id: 8, topic: 'account', question: 'Why is my account suspended?', answer: 'Accounts may be suspended for: expired documents, low acceptance rate below 50%, multiple cancellations, or violation of terms of service.' },
  { id: 9, topic: 'delivery', question: 'What if the customer is not home?', answer: 'Try calling the customer first. If unreachable after 10 minutes, take a photo of the location and contact support through the Help Center.' },
  { id: 10, topic: 'delivery', question: 'What happens if items are damaged?', answer: 'Report damaged items immediately through the app. Take photos as evidence. Palengke Online will investigate and coordinate with the seller.' },
]);

const filteredFaqs = computed(() => {
  let list = faqs.value;
  if (selectedTopic.value !== 'all') {
    list = list.filter(f => f.topic === selectedTopic.value);
  }
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    list = list.filter(f =>
      f.question.toLowerCase().includes(query) ||
      f.answer.toLowerCase().includes(query)
    );
  }
  return list;
});

const toggleFaq = (id) => {
  if (openFaqs.value.includes(id)) {
    openFaqs.value = openFaqs.value.filter(f => f !== id);
  } else {
    openFaqs.value.push(id);
  }
};

const filterByTopic = (topicId) => {
  selectedTopic.value = topicId;
  openFaqs.value = [];
};

const onSearch = () => { selectedTopic.value = 'all'; };

const chatSupport = async () => {
  const toast = await toastController.create({
    message: 'Connecting to live chat...', duration: 2000, color: 'medium', position: 'top'
  });
  await toast.present();
};

const callSupport = async () => {
  const alert = await alertController.create({
    header: 'Call Support',
    message: 'Palengke Online Driver Support\n1800-PALENGKE\nAvailable 24/7',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Call Now', handler: () => { window.location.href = 'tel:1800-PALENGKE'; } }
    ]
  });
  await alert.present();
};

const emailSupport = async () => {
  const alert = await alertController.create({
    header: 'Email Support',
    message: 'Send us an email at:\nsupport@palengkeonline.com\n\nWe reply within 24 hours.',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Send Email', handler: () => { window.location.href = 'mailto:support@palengkeonline.com'; } }
    ]
  });
  await alert.present();
};

const reportProblem = async () => {
  const alert = await alertController.create({
    header: 'Report a Problem',
    inputs: [
      { name: 'issue', type: 'textarea', placeholder: 'Describe your problem...' }
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { text: 'Submit', handler: async (data) => {
        if (data.issue) {
          await showSuccessModal('Report submitted!\nWe\'ll get back to you within 24 hours.');
        }
      }}
    ]
  });
  await alert.present();
};

const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      textDiv.style.whiteSpace = 'pre-line';
      
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  await alert.onDidDismiss();
};

const openLink = async (type) => {
  const titles = { terms: 'Terms & Conditions', privacy: 'Privacy Policy', safety: 'Safety Guidelines' };
  const alert = await alertController.create({
    header: titles[type],
    message: `Opening ${titles[type]}...`,
    buttons: ['Close']
  });
  await alert.present();
};

const rateApp = async () => {
  const alert = await alertController.create({
    header: 'Rate Our App',
    message: 'Thank you for using Palengke Online Driver! Your feedback helps us improve.',
    buttons: [
      { text: 'Maybe Later', role: 'cancel' },
      { text: 'Rate Now', handler: () => {} }
    ]
  });
  await alert.present();
};
</script>

<style scoped>
.help-content { --background: #f8f9fa; }

/* Search */
.search-section { background: white; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; }
.search-bar { position: relative; }
.search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #999; z-index: 1; }
.search-input { width: 100%; padding: 14px 48px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 15px; font-family: inherit; transition: border-color 0.2s; }
.search-input:focus { outline: none; border-color: #4CAF50; }
.clear-icon { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #999; cursor: pointer; }

.help-container { padding: 16px; padding-bottom: 40px; }

.section-title { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }

/* Contact Grid */
.contact-section { margin-bottom: 20px; }
.contact-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }

.contact-card {
  background: white; border-radius: 16px; padding: 16px 8px;
  display: flex; flex-direction: column; align-items: center; gap: 6px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06); cursor: pointer; transition: transform 0.2s;
}
.contact-card:active { transform: scale(0.95); }

.contact-icon { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 26px; }
.contact-icon.blue { background: #E3F2FD; color: #2196F3; }
.contact-icon.green { background: #E8F5E9; color: #4CAF50; }
.contact-icon.orange { background: #FFF3E0; color: #FF9800; }
.contact-icon.red { background: #FFEBEE; color: #f44336; }

.contact-card span:nth-child(2) { font-size: 13px; font-weight: 700; color: #1a1a1a; }
.contact-status { font-size: 11px; color: #999; }
.contact-status.online { color: #4CAF50; font-weight: 600; }

/* Topics */
.topics-section { margin-bottom: 20px; }
.topics-grid { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; }
.topics-grid::-webkit-scrollbar { display: none; }

.topic-chip {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 16px; border-radius: 20px;
  background: white; border: 2px solid #e0e0e0;
  font-size: 13px; font-weight: 600; color: #666;
  cursor: pointer; white-space: nowrap; transition: all 0.2s;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.topic-chip.active { background: #4CAF50; border-color: #4CAF50; color: white; }
.topic-chip ion-icon { font-size: 16px; }

/* FAQ */
.faq-section { margin-bottom: 20px; }

.faq-list { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

.faq-item { border-bottom: 1px solid #f0f0f0; }
.faq-item:last-child { border-bottom: none; }

.faq-question {
  display: flex; justify-content: space-between; align-items: center;
  padding: 16px 20px; cursor: pointer; gap: 12px; transition: background 0.2s;
}
.faq-question:active { background: #f8f9fa; }
.faq-question span { font-size: 14px; font-weight: 600; color: #1a1a1a; line-height: 1.4; }
.faq-arrow { font-size: 18px; color: #4CAF50; flex-shrink: 0; transition: transform 0.2s; }

.faq-answer { padding: 0 20px 16px; }
.faq-answer p { font-size: 13px; color: #555; margin: 0; line-height: 1.6; background: #f8f9fa; padding: 12px 14px; border-radius: 10px; border-left: 3px solid #4CAF50; }

.no-results { text-align: center; padding: 40px 20px; }
.no-results ion-icon { font-size: 48px; color: #ddd; margin-bottom: 12px; }
.no-results p { font-size: 14px; color: #999; margin: 0; }

/* Links */
.links-section { margin-bottom: 20px; }
.links-list { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

.link-item {
  display: flex; align-items: center; gap: 14px;
  padding: 16px 20px; border-bottom: 1px solid #f0f0f0;
  cursor: pointer; transition: background 0.2s;
}
.link-item:last-child { border-bottom: none; }
.link-item:active { background: #f8f9fa; }
.link-item > ion-icon:first-child { font-size: 22px; color: #4CAF50; }
.link-item span { flex: 1; font-size: 15px; font-weight: 600; color: #1a1a1a; }
.link-arrow { font-size: 18px; color: #ccc; }
</style>