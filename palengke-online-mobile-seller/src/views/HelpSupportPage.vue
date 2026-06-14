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

    <ion-content :fullscreen="true">
      <div class="help-container">
        <!-- Search Bar -->
        <div class="search-section">
          <div class="search-bar">
            <ion-icon :icon="searchOutline" class="search-icon"></ion-icon>
            <input v-model="searchQuery" type="text" placeholder="Search for help..." class="search-input" />
            <ion-icon v-if="searchQuery" :icon="closeCircle" class="clear-icon" @click="searchQuery = ''"></ion-icon>
          </div>
        </div>

        <!-- Search Results -->
        <div v-if="hasSearchQuery" class="search-results-section">
          <h2 class="section-title">Search Results</h2>
          <div v-if="searchResults.length === 0" class="no-results">
            <ion-icon :icon="searchOutline"></ion-icon>
            <p>No results found for "{{ searchQuery }}"</p>
          </div>
          <div v-else class="search-results-list">
            <div
              v-for="(result, index) in searchResults"
              :key="index"
              class="search-result-card"
              @click="handleSearchResult(result)"
            >
              <div class="result-icon" :class="result.type">
                <ion-icon
                  :icon="
                    result.type === 'faq'
                      ? chatbubbleEllipsesOutline
                      : result.type === 'topic'
                        ? rocketOutline
                        : result.type === 'quick_action'
                          ? warningOutline
                          : chatbubbleEllipsesOutline
                  "
                ></ion-icon>
              </div>
              <div class="result-content">
                <h3>{{ result.title }}</h3>
                <p>{{ result.snippet }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Normal Content (hidden during search) -->
        <template v-if="!hasSearchQuery">
          <!-- Contact Support Cards -->
          <div class="contact-section">
            <h2 class="section-title">Contact Support</h2>

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

          <!-- Quick Actions -->
          <div class="quick-actions-section">
            <h2 class="section-title">Quick Actions</h2>

            <div class="quick-actions">
              <div class="quick-action-card" @click="reportProblem">
                <ion-icon :icon="warningOutline"></ion-icon>
                <span>Report Problem</span>
              </div>
              <div class="quick-action-card" @click="giveFeedback">
                <ion-icon :icon="chatboxOutline"></ion-icon>
                <span>Send Feedback</span>
              </div>
              <div class="quick-action-card" @click="requestFeature">
                <ion-icon :icon="bulbOutline"></ion-icon>
                <span>Request Feature</span>
              </div>
            </div>
          </div>

          <!-- My Reports -->
          <div class="my-reports-section">
            <h2 class="section-title">My Reports</h2>

            <div v-if="loadingReports" class="loading-state">Loading your reports...</div>
            <div v-else-if="myReports.length === 0" class="no-reports">
              <p>You haven't submitted any reports yet.</p>
            </div>
            <div v-else class="reports-list">
              <div v-for="report in myReports" :key="report.id" class="report-card">
                <div class="report-info">
                  <h3>{{ report.subject }}</h3>
                  <p class="report-date">{{ formatDate(report.created_at) }}</p>
                </div>
                <span class="report-status" :class="report.status">
                  {{ statusLabel(report.status) }}
                </span>
              </div>
            </div>
          </div>

          <!-- FAQs -->
          <div class="faqs-section">
            <h2 class="section-title">Frequently Asked Questions</h2>

            <div class="faq-list">
              <div v-for="(faq, index) in faqs" :key="index" class="faq-item" @click="toggleFaq(index)">
                <div class="faq-question">
                  <h3>{{ faq.question }}</h3>
                  <ion-icon :icon="expandedFaq === index ? chevronUpOutline : chevronDownOutline"></ion-icon>
                </div>
                <div class="faq-answer" v-show="expandedFaq === index">
                  <p>{{ faq.answer }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Help Topics -->
          <div class="topics-section">
            <h2 class="section-title">Browse Help Topics</h2>

            <div class="topics-grid">
              <div class="topic-card" @click="showTopic('getting-started')">
                <div class="topic-icon">
                  <ion-icon :icon="rocketOutline"></ion-icon>
                </div>
                <h3>Getting Started</h3>
                <p>Learn the basics</p>
              </div>

              <div class="topic-card" @click="showTopic('orders')">
                <div class="topic-icon">
                  <ion-icon :icon="cartOutline"></ion-icon>
                </div>
                <h3>Managing Orders</h3>
                <p>Order workflows</p>
              </div>

              <div class="topic-card" @click="showTopic('products')">
                <div class="topic-icon">
                  <ion-icon :icon="cubeOutline"></ion-icon>
                </div>
                <h3>Products</h3>
                <p>Add & manage products</p>
              </div>

              <div class="topic-card" @click="showTopic('payments')">
                <div class="topic-icon">
                  <ion-icon :icon="cashOutline"></ion-icon>
                </div>
                <h3>Payments</h3>
                <p>Payment methods</p>
              </div>

              <div class="topic-card" @click="showTopic('account')">
                <div class="topic-icon">
                  <ion-icon :icon="personOutline"></ion-icon>
                </div>
                <h3>Account Settings</h3>
                <p>Manage your account</p>
              </div>

              <div class="topic-card" @click="showTopic('troubleshooting')">
                <div class="topic-icon">
                  <ion-icon :icon="constructOutline"></ion-icon>
                </div>
                <h3>Troubleshooting</h3>
                <p>Fix common issues</p>
              </div>
            </div>
          </div>

          <!-- Video Tutorials -->
          <div class="tutorials-section">
            <h2 class="section-title">Video Tutorials</h2>

            <div class="tutorial-list">
              <div class="tutorial-card" @click="playVideo('onboarding')">
                <div class="tutorial-thumbnail">
                  <ion-icon :icon="playCircleOutline"></ion-icon>
                  <span class="duration">2:30</span>
                </div>
                <div class="tutorial-info">
                  <h3>Getting Started with Palengke Online</h3>
                  <p>Learn how to set up your seller account</p>
                </div>
              </div>

              <div class="tutorial-card" @click="playVideo('products')">
                <div class="tutorial-thumbnail">
                  <ion-icon :icon="playCircleOutline"></ion-icon>
                  <span class="duration">3:15</span>
                </div>
                <div class="tutorial-info">
                  <h3>How to Add Products</h3>
                  <p>Step-by-step guide to adding your products</p>
                </div>
              </div>

              <div class="tutorial-card" @click="playVideo('orders')">
                <div class="tutorial-thumbnail">
                  <ion-icon :icon="playCircleOutline"></ion-icon>
                  <span class="duration">4:00</span>
                </div>
                <div class="tutorial-info">
                  <h3>Managing Your Orders</h3>
                  <p>Accept, process, and complete orders</p>
                </div>
              </div>
            </div>
          </div>

          <!-- App Info -->
          <div class="info-section">
            <div class="info-card">
              <ion-icon :icon="informationCircleOutline"></ion-icon>
              <div class="info-content">
                <h3>App Version</h3>
                <p>1.0.0 (Build 100)</p>
              </div>
            </div>

            <div class="info-card" @click="viewTerms">
              <ion-icon :icon="documentTextOutline"></ion-icon>
              <div class="info-content">
                <h3>Terms & Conditions</h3>
                <p>Read our terms of service</p>
              </div>
              <ion-icon :icon="chevronForwardOutline"></ion-icon>
            </div>

            <div class="info-card" @click="viewPrivacy">
              <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              <div class="info-content">
                <h3>Privacy Policy</h3>
                <p>How we protect your data</p>
              </div>
              <ion-icon :icon="chevronForwardOutline"></ion-icon>
            </div>
          </div>
        </template>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonBackButton,
  IonIcon,
  alertController,
  toastController,
} from '@ionic/vue';
import {
  searchOutline,
  closeCircle,
  chatbubbleEllipsesOutline,
  callOutline,
  mailOutline,
  logoFacebook,
  chevronForwardOutline,
  warningOutline,
  chatboxOutline,
  bulbOutline,
  chevronDownOutline,
  chevronUpOutline,
  rocketOutline,
  cartOutline,
  cubeOutline,
  cashOutline,
  personOutline,
  constructOutline,
  playCircleOutline,
  informationCircleOutline,
  documentTextOutline,
  shieldCheckmarkOutline,
} from 'ionicons/icons';
import { API_BASE } from '@/config/api';

const router = useRouter();

// State
const searchQuery = ref('');
const expandedFaq = ref(null);
const contactSupports = ref([]);
const loadingContacts = ref(true);
const myReports = ref([]);
const loadingReports = ref(false);

// Helpers
function formatDate(dateStr) {
  if (!dateStr) return '';
  return dateStr.slice(0, 10);
}

function statusLabel(status) {
  if (status === 'in_progress') return 'In Progress';
  return status.charAt(0).toUpperCase() + status.slice(1);
}

// Help Topics Data (for search)
const helpTopics = [
  { key: 'getting-started', title: 'Getting Started', description: 'Learn the basics' },
  { key: 'orders', title: 'Managing Orders', description: 'Order workflows' },
  { key: 'products', title: 'Products', description: 'Add & manage products' },
  { key: 'payments', title: 'Payments', description: 'Payment methods' },
  { key: 'account', title: 'Account Settings', description: 'Manage your account' },
  { key: 'troubleshooting', title: 'Troubleshooting', description: 'Fix common issues' },
];

// Quick Actions Data (for search)
const quickActions = [
  { key: 'report', title: 'Report Problem', description: 'Submit a problem report' },
  { key: 'feedback', title: 'Send Feedback', description: 'Share your feedback' },
  { key: 'feature', title: 'Request Feature', description: 'Suggest a new feature' },
];

// FAQs Data
const faqs = ref([]);
const loadingFaqs = ref(true);

const loadFaqs = async () => {
  loadingFaqs.value = true;
  try {
    const response = await fetch(`${API_BASE}/faqs`, {
      headers: { Accept: 'application/json' },
    });
    const data = await response.json();
    if (response.ok && data.faqs) {
      faqs.value = data.faqs;
    }
  } catch {
    // silent
  } finally {
    loadingFaqs.value = false;
  }
};

loadFaqs();

// Computed
const hasSearchQuery = computed(() => searchQuery.value.trim().length > 0);

const searchResults = computed(() => {
  if (!hasSearchQuery.value) return [];
  const q = searchQuery.value.toLowerCase().trim();
  const results = [];

  faqs.value.forEach((faq) => {
    if (faq.question.toLowerCase().includes(q) || faq.answer.toLowerCase().includes(q)) {
      results.push({ type: 'faq', title: faq.question, snippet: faq.answer, faq });
    }
  });

  helpTopics.forEach((topic) => {
    if (topic.title.toLowerCase().includes(q) || topic.description.toLowerCase().includes(q)) {
      results.push({ type: 'topic', title: topic.title, snippet: topic.description, topic });
    }
  });

  contactSupports.value.forEach((contact) => {
    if (contact.title.toLowerCase().includes(q) || contact.subtitle.toLowerCase().includes(q)) {
      results.push({ type: 'contact', title: contact.title, snippet: contact.subtitle, contact });
    }
  });

  quickActions.forEach((action) => {
    if (action.title.toLowerCase().includes(q) || action.description.toLowerCase().includes(q)) {
      results.push({ type: 'quick_action', title: action.title, snippet: action.description, actionKey: action.key });
    }
  });

  return results;
});

// Loading Modal Function
const showLoadingModal = async (message = 'Loading...') => {
  const alert = await alertController.create({
    cssClass: 'loading-modal-simple',
    message: message,
    buttons: [],
  });

  await alert.present();

  setTimeout(() => {
    alert.dismiss();
  }, 1500);
};

// Success Modal Function
const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK'],
  });

  await alert.present();

  // Add the image after modal is presented
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

      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      alertElement.appendChild(wrapper);
    }
  }, 50);

  setTimeout(() => {
    alert.dismiss();
  }, 2000);
};

// Fetch contact supports from backend
async function fetchContactSupports() {
  try {
    const response = await fetch(`${API_BASE}/contact-supports`);
    const data = await response.json();
    contactSupports.value = data.contacts || [];
    // Fallback to defaults if API returns empty
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

function handleSearchResult(result) {
  if (result.type === 'faq') {
    const idx = faqs.value.indexOf(result.faq);
    if (idx !== -1) toggleFaq(idx);
  } else if (result.type === 'topic') {
    showTopic(result.topic.key);
  } else if (result.type === 'contact') {
    handleContactAction(result.contact);
  } else if (result.type === 'quick_action') {
    if (result.actionKey === 'report') reportProblem();
    else if (result.actionKey === 'feedback') giveFeedback();
    else if (result.actionKey === 'feature') requestFeature();
  }
}

// Methods
const toggleFaq = (index) => {
  expandedFaq.value = expandedFaq.value === index ? null : index;
};

async function fetchMyReports() {
  const email = localStorage.getItem('userEmail');
  if (!email) return;
  loadingReports.value = true;
  try {
    const response = await fetch(`${API_BASE}/reports?email=${encodeURIComponent(email)}`);
    const data = await response.json();
    myReports.value = data.reports || [];
  } catch {
    myReports.value = [];
  } finally {
    loadingReports.value = false;
  }
}

onMounted(() => {
  fetchContactSupports();
  fetchMyReports();
});

const reportProblem = async () => {
  const alert = await alertController.create({
    header: 'Report a Problem',
    inputs: [
      {
        name: 'subject',
        type: 'text',
        placeholder: 'Subject (e.g., App bug, account issue)',
      },
      {
        name: 'description',
        type: 'textarea',
        placeholder: 'Describe the problem in detail...',
      },
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Submit',
        handler: async (data) => {
          if (data.subject && data.description) {
            try {
              const response = await fetch(`${API_BASE}/reports`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  type: 'seller',
                  submitted_by: localStorage.getItem('userEmail') || 'Seller',
                  subject: data.subject,
                  description: data.description,
                  priority: 'medium',
                }),
              });
              if (!response.ok) throw new Error('Failed to submit report');
              await showSuccessModal('Problem report submitted!');
            } catch {
              const failAlert = await alertController.create({
                header: 'Submission Failed',
                message: 'Could not submit your report. Please try again later.',
                buttons: ['OK'],
              });
              await failAlert.present();
            }
          }
        },
      },
    ],
  });
  await alert.present();
};

const giveFeedback = async () => {
  const alert = await alertController.create({
    header: 'Send Feedback',
    message: "We'd love to hear from you!",
    inputs: [
      {
        name: 'feedback',
        type: 'textarea',
        placeholder: 'Your feedback...',
      },
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Submit',
        handler: async (data) => {
          if (data.feedback) {
            const loading = await alertController.create({
              cssClass: 'loading-modal-simple',
              message: 'Sending feedback...',
              buttons: [],
            });
            await loading.present();
            try {
              const response = await fetch(`${API_BASE}/feedbacks`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  submitted_by: localStorage.getItem('userEmail') || 'Seller',
                  message: data.feedback,
                }),
              });
              if (!response.ok) throw new Error('Failed to submit feedback');
              await loading.dismiss();
              await showSuccessModal('Thank you for your feedback!');
            } catch {
              await loading.dismiss();
              const failAlert = await alertController.create({
                header: 'Submission Failed',
                message: 'Could not submit your feedback. Please try again later.',
                buttons: ['OK'],
              });
              await failAlert.present();
            }
          }
        },
      },
    ],
  });
  await alert.present();
};

const requestFeature = async () => {
  const alert = await alertController.create({
    header: 'Request a Feature',
    message: 'What feature would you like to see?',
    inputs: [
      {
        name: 'feature',
        type: 'textarea',
        placeholder: 'Describe the feature...',
      },
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Submit',
        handler: async (data) => {
          if (data.feature) {
            const loading = await alertController.create({
              cssClass: 'loading-modal-simple',
              message: 'Sending feature request...',
              buttons: [],
            });
            await loading.present();
            try {
              const response = await fetch(`${API_BASE}/feedbacks`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  type: 'feature_request',
                  submitted_by: localStorage.getItem('userEmail') || 'Seller',
                  message: data.feature,
                }),
              });
              if (!response.ok) throw new Error('Failed to submit feature request');
              await loading.dismiss();
              await showSuccessModal('Feature request submitted!');
            } catch {
              await loading.dismiss();
              const failAlert = await alertController.create({
                header: 'Submission Failed',
                message: 'Could not submit your feature request. Please try again later.',
                buttons: ['OK'],
              });
              await failAlert.present();
            }
          }
        },
      },
    ],
  });
  await alert.present();
};

const showTopic = async (topic) => {
  const topics = {
    'getting-started': 'Getting Started Guide',
    orders: 'Order Management Help',
    products: 'Product Management Help',
    payments: 'Payment Information',
    account: 'Account Settings Help',
    troubleshooting: 'Troubleshooting Guide',
  };

  await showLoadingModal(`Opening ${topics[topic]}...`);
};

const playVideo = async (video) => {
  await showLoadingModal('Loading video tutorial...');
};

const viewTerms = async () => {
  const alert = await alertController.create({
    header: 'Terms & Conditions',
    message: 'Full terms and conditions document will be displayed here.',
    buttons: ['Close'],
  });
  await alert.present();
};

const viewPrivacy = async () => {
  const alert = await alertController.create({
    header: 'Privacy Policy',
    message: 'Full privacy policy document will be displayed here.',
    buttons: ['Close'],
  });
  await alert.present();
};
</script>

<style scoped>
.help-container {
  background: #f8f9fa;
  min-height: 100%;
  padding-bottom: 40px;
}

/* Search Section */
.search-section {
  background: white;
  padding: 20px;
  border-bottom: 1px solid #f0f0f0;
}

.search-bar {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #999;
}

.search-input {
  width: 100%;
  padding: 14px 48px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 15px;
  font-family: inherit;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #4caf50;
}

.clear-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #999;
  cursor: pointer;
}

/* Section Title */
.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

/* Search Results */
.search-results-section {
  padding: 20px;
}
.search-results-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.search-result-card {
  background: white;
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s;
}
.search-result-card:active {
  transform: scale(0.98);
}
.result-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}
.result-icon.faq {
  background: #e8f5e9;
  color: #4caf50;
}
.result-icon.topic {
  background: #e3f2fd;
  color: #2196f3;
}
.result-icon.contact {
  background: #fff3e0;
  color: #ff9800;
}
.result-icon.quick_action {
  background: #fce4ec;
  color: #e91e63;
}
.result-content {
  flex: 1;
  min-width: 0;
}
.result-content h3 {
  font-size: 14px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 3px 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.result-content p {
  font-size: 13px;
  color: #666;
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Contact Section */
.contact-section {
  padding: 24px 20px;
}

.loading-state {
  text-align: center;
  padding: 24px;
  color: #999;
  font-size: 14px;
}

.contact-cards {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.contact-card {
  background: white;
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s;
}

.contact-card:active {
  transform: scale(0.98);
}

.contact-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  flex-shrink: 0;
}

.contact-icon.chat {
  background: #e8f5e9;
  color: #4caf50;
}

.contact-icon.phone {
  background: #e3f2fd;
  color: #2196f3;
}

.contact-icon.email {
  background: #fff3e0;
  color: #ff9800;
}

.contact-icon.facebook {
  background: #e3f2fd;
  color: #1877f2;
}

.contact-content {
  flex: 1;
}

.contact-content h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.contact-content p {
  font-size: 14px;
  color: #666;
  margin: 0 0 4px 0;
}

.contact-status,
.contact-hours,
.contact-response,
.contact-social {
  font-size: 12px;
  color: #999;
}

.contact-status.online {
  color: #4caf50;
  font-weight: 600;
}

.arrow {
  font-size: 20px;
  color: #ccc;
}

/* Quick Actions */
.quick-actions-section {
  padding: 0 20px 24px 20px;
}

.quick-actions {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.quick-action-card {
  background: white;
  border-radius: 16px;
  padding: 20px 12px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s;
}

.quick-action-card:active {
  transform: scale(0.95);
}

.quick-action-card ion-icon {
  font-size: 32px;
  color: #4caf50;
  margin-bottom: 8px;
}

.quick-action-card span {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #333;
}

/* My Reports Section */
.my-reports-section {
  padding: 0 20px 24px 20px;
}
.no-reports {
  text-align: center;
  padding: 24px;
}
.no-reports p {
  font-size: 14px;
  color: #999;
  margin: 0;
}
.reports-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.report-card {
  background: white;
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
.report-info {
  flex: 1;
  min-width: 0;
}
.report-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 4px 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.report-date {
  font-size: 12px;
  color: #999;
  margin: 0;
}
.report-status {
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 8px;
  white-space: nowrap;
  flex-shrink: 0;
}
.report-status.pending {
  background: #fef3c7;
  color: #d97706;
}
.report-status.in_progress {
  background: #eff6ff;
  color: #3b82f6;
}
.report-status.resolved {
  background: #f0fdf4;
  color: #059669;
}
.report-status.dismissed {
  background: #f3f4f6;
  color: #6b7280;
}

/* FAQs Section */
.faqs-section {
  padding: 0 20px 24px 20px;
}

.faq-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.faq-item {
  background: white;
  border-radius: 16px;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
}

.faq-question {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.faq-question h3 {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
  flex: 1;
}

.faq-question ion-icon {
  font-size: 20px;
  color: #4caf50;
  flex-shrink: 0;
}

.faq-answer {
  padding-top: 12px;
  margin-top: 12px;
  border-top: 1px solid #f0f0f0;
}

.faq-answer p {
  font-size: 14px;
  color: #666;
  line-height: 1.6;
  margin: 0;
}

.no-results {
  text-align: center;
  padding: 40px 20px;
}

.no-results ion-icon {
  font-size: 48px;
  color: #ccc;
  margin-bottom: 12px;
}

.no-results p {
  font-size: 14px;
  color: #999;
  margin: 0;
}

/* Topics Section */
.topics-section {
  padding: 0 20px 24px 20px;
}

.topics-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.topic-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s;
}

.topic-card:active {
  transform: scale(0.98);
}

.topic-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: #e8f5e9;
  color: #4caf50;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  margin: 0 auto 12px auto;
}

.topic-card h3 {
  font-size: 15px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.topic-card p {
  font-size: 13px;
  color: #666;
  margin: 0;
}

/* Tutorials Section */
.tutorials-section {
  padding: 0 20px 24px 20px;
}

.tutorial-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.tutorial-card {
  background: white;
  border-radius: 16px;
  padding: 12px;
  display: flex;
  gap: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s;
}

.tutorial-card:active {
  transform: scale(0.98);
}

.tutorial-thumbnail {
  position: relative;
  width: 120px;
  height: 80px;
  border-radius: 12px;
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.tutorial-thumbnail ion-icon {
  font-size: 40px;
  color: white;
}

.duration {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
}

.tutorial-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.tutorial-info h3 {
  font-size: 15px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.tutorial-info p {
  font-size: 13px;
  color: #666;
  margin: 0;
  line-height: 1.4;
}

/* Info Section */
.info-section {
  padding: 0 20px 24px 20px;
}

.info-card {
  background: white;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
}

.info-card ion-icon {
  font-size: 24px;
  color: #4caf50;
}

.info-content {
  flex: 1;
}

.info-content h3 {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 2px 0;
}

.info-content p {
  font-size: 13px;
  color: #666;
  margin: 0;
}

/* Responsive */
@media (max-width: 360px) {
  .topics-grid {
    grid-template-columns: 1fr;
  }

  .quick-actions {
    grid-template-columns: 1fr;
  }
}

/* Success Modal Styling */
:deep(.success-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 320px;
  padding: 30px 20px;
}

:deep(.success-modal .alert-head) {
  padding: 0;
  min-height: 0;
}

:deep(.success-modal .alert-message) {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.success-modal .alert-button-group) {
  padding: 10px 0 0 0;
}

:deep(.success-modal button) {
  background: #4caf50;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
}

/* Loading Modal Styling */
:deep(.loading-modal-simple .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 280px;
  padding: 50px 30px;
  background: white;
}

:deep(.loading-modal-simple .alert-head) {
  display: none;
}

:deep(.loading-modal-simple .alert-message) {
  padding: 0;
  position: relative;
  padding-top: 70px;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

:deep(.loading-modal-simple .alert-message::before) {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  margin-left: -25px;
  width: 50px;
  height: 50px;
  border: 5px solid #f3f3f3;
  border-top: 5px solid #1877f2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

:deep(.loading-modal-simple .alert-button-group) {
  display: none;
}
</style>
