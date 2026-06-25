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

          <div v-if="loadingContacts" class="loading-state">Loading support options...</div>
          <div v-else class="contact-cards">
            <div
              v-for="contact in contactSupports"
              :key="contact.key"
              class="contact-card"
              @click="handleContactAction(contact)"
            >
              <div class="contact-icon" :class="getIconClass(contact.type)">
                <ion-icon :icon="getIconForType(contact.type)"></ion-icon>
              </div>
              <div class="contact-content">
                <h3>{{ contact.title }}</h3>
                <p>{{ contact.subtitle }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Quick Topics -->
        <div class="topics-section">
          <h3 class="section-title">Quick Topics</h3>
          <div class="topics-grid">
            <div v-if="loadingTopics" class="loading-state">Loading topics...</div>
            <div class="topic-chip" v-for="topic in topics" :key="topic.key"
              :class="{ active: selectedTopic === topic.key }"
              @click="filterByTopic(topic.key)">
              <ion-icon :icon="getTopicIcon(topic.icon)"></ion-icon>
              <span>{{ topic.name }}</span>
            </div>
          </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
          <h3 class="section-title">
            {{ selectedTopic === 'all' ? 'Frequently Asked Questions' : topics.find(t => t.key === selectedTopic)?.name }}
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
import { ref, computed, onMounted } from 'vue';
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
  cubeOutline, helpCircleOutline, chatbubbleEllipsesOutline,
  logoFacebook, rocketOutline, storefrontOutline, alertCircle,
} from 'ionicons/icons';
const router = useRouter();

const searchQuery = ref('');
const selectedTopic = ref('all');
const openFaqs = ref([]);
const contactSupports = ref([]);
const loadingContacts = ref(true);

const topics = ref([]);
const faqs = ref([]);
const loadingFaqs = ref(true);
const loadingTopics = ref(true);

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

// Contact Support - API driven
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

async function fetchContactSupports() {
  try {
    const response = await fetch(`${API_BASE}/api/contact-supports`);
    const data = await response.json();
    contactSupports.value = data.contacts || [];
    if (contactSupports.value.length === 0) {
      contactSupports.value = getDefaultContacts();
    }
  } catch {
    contactSupports.value = getDefaultContacts();
  } finally {
    loadingContacts.value = false;
  }
}

function getDefaultContacts() {
  return [
    {
      key: 'live_chat',
      title: 'Live Chat',
      subtitle: 'Chat with our support team',
      type: 'chat',
      value: null,
      enabled: true,
    },
    {
      key: 'call',
      title: 'Call Us',
      subtitle: '+63 912 345 6789',
      type: 'phone',
      value: '+639123456789',
      enabled: true,
    },
    {
      key: 'email',
      title: 'Email Support',
      subtitle: 'support@palengke-online.com',
      type: 'email',
      value: 'support@palengke-online.com',
      enabled: true,
    },
    {
      key: 'facebook',
      title: 'Facebook',
      subtitle: 'Message us on Facebook',
      type: 'facebook',
      value: 'https://facebook.com/PalengkeOnlinePH',
      enabled: true,
    },
  ];
}

function getIconForType(type) {
  const icons = {
    chat: chatbubbleEllipsesOutline,
    phone: callOutline,
    email: mailOutline,
    facebook: logoFacebook,
  };
  return icons[type] || chatbubbleEllipsesOutline;
}

function getIconClass(type) {
  const classes = {
    chat: 'chat',
    phone: 'phone',
    email: 'email',
    facebook: 'facebook',
  };
  return classes[type] || 'chat';
}

function handleContactAction(contact) {
  switch (contact.type) {
    case 'chat':
      showLoadingModal('Opening live chat...');
      break;
    case 'phone':
      if (contact.value) window.location.href = `tel:${contact.value}`;
      break;
    case 'email':
      if (contact.value) window.location.href = `mailto:${contact.value}`;
      break;
    case 'facebook':
      if (contact.value) window.open(contact.value, '_blank');
      break;
    default:
      break;
  }
}

function getTopicIcon(iconName) {
  const icons = {
    'help-circle': helpCircleOutline,
    'cash': cashOutline,
    'cube': cubeOutline,
    'person': personOutline,
    'bicycle': bicycleOutline,
    'rocket': rocketOutline,
    'storefront': storefrontOutline,
    'warning': alertCircle,
  };
  return icons[iconName] || helpCircleOutline;
}

async function fetchHelpTopics() {
  try {
    const response = await fetch(`${API_BASE}/api/help-topics?role=driver`);
    const data = await response.json();
    topics.value = data.topics || [];
  } catch {
    topics.value = [];
  } finally {
    loadingTopics.value = false;
  }
}

async function fetchFaqs() {
  try {
    const response = await fetch(`${API_BASE}/api/faqs?role=driver`);
    const data = await response.json();
    faqs.value = data.faqs || [];
  } catch {
    faqs.value = [];
  } finally {
    loadingFaqs.value = false;
  }
}

const showLoadingModal = async (message) => {
  const toast = await toastController.create({
    message, duration: 2000, color: 'medium', position: 'top'
  });
  await toast.present();
};

onMounted(() => {
  fetchContactSupports();
  fetchHelpTopics();
  fetchFaqs();
});

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

/* Contact Section */
.contact-section { margin-bottom: 20px; }
.contact-cards { display: flex; flex-direction: column; gap: 10px; }

.contact-card {
  background: white; border-radius: 14px; padding: 14px 16px;
  display: flex; align-items: center; gap: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06); cursor: pointer; transition: transform 0.2s;
}
.contact-card:active { transform: scale(0.97); }

.contact-icon {
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; flex-shrink: 0;
}
.contact-icon.chat { background: #E3F2FD; color: #2196F3; }
.contact-icon.phone { background: #E8F5E9; color: #4CAF50; }
.contact-icon.email { background: #FFF3E0; color: #FF9800; }
.contact-icon.facebook { background: #E7F3FF; color: #1877F2; }

.contact-content { flex: 1; min-width: 0; }
.contact-content h3 { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0 0 2px 0; }
.contact-content p { font-size: 12px; color: #888; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.contact-card .arrow { font-size: 18px; color: #ccc; flex-shrink: 0; }

.loading-state { text-align: center; padding: 20px; color: #999; font-size: 14px; }

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