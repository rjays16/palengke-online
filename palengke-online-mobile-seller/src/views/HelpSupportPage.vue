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
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Search for help..."
              class="search-input"
            >
            <ion-icon 
              v-if="searchQuery" 
              :icon="closeCircle" 
              class="clear-icon"
              @click="searchQuery = ''"
            ></ion-icon>
          </div>
        </div>

        <!-- Contact Support Cards -->
        <div class="contact-section">
          <h2 class="section-title">Contact Support</h2>
          
          <div class="contact-cards">
            <div class="contact-card" @click="openLiveChat">
              <div class="contact-icon chat">
                <ion-icon :icon="chatbubbleEllipsesOutline"></ion-icon>
              </div>
              <div class="contact-content">
                <h3>Live Chat</h3>
                <p>Chat with our support team</p>
                <span class="contact-status online">● Online</span>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>

            <div class="contact-card" @click="callSupport">
              <div class="contact-icon phone">
                <ion-icon :icon="callOutline"></ion-icon>
              </div>
              <div class="contact-content">
                <h3>Call Us</h3>
                <p>+63 912 345 6789</p>
                <span class="contact-hours">Mon-Sat, 8AM-6PM</span>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>

            <div class="contact-card" @click="emailSupport">
              <div class="contact-icon email">
                <ion-icon :icon="mailOutline"></ion-icon>
              </div>
              <div class="contact-content">
                <h3>Email Support</h3>
                <p>support@palengke-online.com</p>
                <span class="contact-response">Response within 24 hours</span>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="arrow"></ion-icon>
            </div>

            <div class="contact-card" @click="openFacebook">
              <div class="contact-icon facebook">
                <ion-icon :icon="logoFacebook"></ion-icon>
              </div>
              <div class="contact-content">
                <h3>Facebook</h3>
                <p>Message us on Facebook</p>
                <span class="contact-social">@PalengkeOnlinePH</span>
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

        <!-- FAQs -->
        <div class="faqs-section">
          <h2 class="section-title">Frequently Asked Questions</h2>
          
          <div class="faq-list">
            <div 
              v-for="(faq, index) in filteredFaqs" 
              :key="index" 
              class="faq-item"
              @click="toggleFaq(index)"
            >
              <div class="faq-question">
                <h3>{{ faq.question }}</h3>
                <ion-icon 
                  :icon="expandedFaq === index ? chevronUpOutline : chevronDownOutline"
                ></ion-icon>
              </div>
              <div class="faq-answer" v-show="expandedFaq === index">
                <p>{{ faq.answer }}</p>
              </div>
            </div>
          </div>

          <div v-if="filteredFaqs.length === 0" class="no-results">
            <ion-icon :icon="searchOutline"></ion-icon>
            <p>No results found for "{{ searchQuery }}"</p>
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

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
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
  toastController
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
  shieldCheckmarkOutline
} from 'ionicons/icons';

const router = useRouter();

// State
const searchQuery = ref('');
const expandedFaq = ref(null);

// FAQs Data
const faqs = ref([
  {
    question: 'How do I add a new product?',
    answer: 'Go to the Products page, tap the + button in the top right, fill in the product details (name, price, category, stock), add a photo, and tap Save.'
  },
  {
    question: 'How do I accept an order?',
    answer: 'When you receive a new order notification, go to the Orders page, tap on the pending order, review the details, and tap the "Accept" button.'
  },
  {
    question: 'How do I receive payments?',
    answer: 'Set up your payment methods (GCash or Bank Account) in your Profile settings. Payments will be transferred to your account after order completion.'
  },
  {
    question: 'What are the supported units?',
    answer: 'You can sell products by kilogram (kg), liter (L), or piece (pc). Select the appropriate unit when adding your product.'
  },
  {
    question: 'How do I chat with customers?',
    answer: 'Go to the Messages page from the dashboard. You can view all your conversations with customers and drivers there.'
  },
  {
    question: 'How do I mark an order as completed?',
    answer: 'Open the order details, verify that the order has been delivered, then tap the "Mark as Completed" button.'
  },
  {
    question: 'Can I edit my product information?',
    answer: 'Yes! Go to Products, find your product, tap the edit icon (pencil), make your changes, and save.'
  },
  {
    question: 'How do I handle low stock?',
    answer: 'You\'ll receive notifications when stock is low. Update your product stock in the Products page by editing the product.'
  },
  {
    question: 'What if I need to decline an order?',
    answer: 'Open the order details and tap "Decline Order". You can optionally provide a reason for declining.'
  },
  {
    question: 'How do I view my sales analytics?',
    answer: 'Tap the Settings icon on the home page, then select "Analytics" to view your sales reports, top products, and performance metrics.'
  },
  {
    question: 'How do I change my shop information?',
    answer: 'Go to Profile, tap on any field you want to edit (shop name, location, hours, etc.), make your changes, and save.'
  },
  {
    question: 'What is the delivery fee?',
    answer: 'The standard delivery fee is ₱50. This is added to the customer\'s total at checkout.'
  }
]);

// Computed
const filteredFaqs = computed(() => {
  if (!searchQuery.value) {
    return faqs.value;
  }
  
  const query = searchQuery.value.toLowerCase();
  return faqs.value.filter(faq => 
    faq.question.toLowerCase().includes(query) ||
    faq.answer.toLowerCase().includes(query)
  );
});

// ✨ NEW: Loading Modal Function
const showLoadingModal = async (message = 'Loading...') => {
  const alert = await alertController.create({
    cssClass: 'loading-modal-simple',
    message: message,
    buttons: []
  });
  
  await alert.present();
  
  setTimeout(() => {
    alert.dismiss();
  }, 1500);
};

// ✨ NEW: Success Modal Function
const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
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

// Methods
const toggleFaq = (index) => {
  expandedFaq.value = expandedFaq.value === index ? null : index;
};

const openLiveChat = async () => {
  await showLoadingModal('Opening live chat...');
};

const callSupport = () => {
  window.location.href = 'tel:+639123456789';
};

const emailSupport = () => {
  window.location.href = 'mailto:support@palengke-online.com';
};

const openFacebook = () => {
  window.open('https://facebook.com/PalengkeOnlinePH', '_blank');
};

const reportProblem = async () => {
  const alert = await alertController.create({
    header: 'Report a Problem',
    message: 'Describe the issue you\'re experiencing:',
    inputs: [
      {
        name: 'problem',
        type: 'textarea',
        placeholder: 'Describe the problem...'
      }
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Submit',
        handler: async (data) => {
          if (data.problem) {
            await showSuccessModal('Problem report submitted!');
          }
        }
      }
    ]
  });
  await alert.present();
};

const giveFeedback = async () => {
  const alert = await alertController.create({
    header: 'Send Feedback',
    message: 'We\'d love to hear from you!',
    inputs: [
      {
        name: 'feedback',
        type: 'textarea',
        placeholder: 'Your feedback...'
      }
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Submit',
        handler: async (data) => {
          if (data.feedback) {
            await showSuccessModal('Thank you for your feedback!');
          }
        }
      }
    ]
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
        placeholder: 'Describe the feature...'
      }
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Submit',
        handler: async (data) => {
          if (data.feature) {
            await showSuccessModal('Feature request submitted!');
          }
        }
      }
    ]
  });
  await alert.present();
};

const showTopic = async (topic) => {
  const topics = {
    'getting-started': 'Getting Started Guide',
    'orders': 'Order Management Help',
    'products': 'Product Management Help',
    'payments': 'Payment Information',
    'account': 'Account Settings Help',
    'troubleshooting': 'Troubleshooting Guide'
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
    buttons: ['Close']
  });
  await alert.present();
};

const viewPrivacy = async () => {
  const alert = await alertController.create({
    header: 'Privacy Policy',
    message: 'Full privacy policy document will be displayed here.',
    buttons: ['Close']
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
  border-color: #4CAF50;
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

/* Contact Section */
.contact-section {
  padding: 24px 20px;
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
  background: #E8F5E9;
  color: #4CAF50;
}

.contact-icon.phone {
  background: #E3F2FD;
  color: #2196F3;
}

.contact-icon.email {
  background: #FFF3E0;
  color: #FF9800;
}

.contact-icon.facebook {
  background: #E3F2FD;
  color: #1877F2;
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
  color: #4CAF50;
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
  color: #4CAF50;
  margin-bottom: 8px;
}

.quick-action-card span {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #333;
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
  color: #4CAF50;
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
  background: #E8F5E9;
  color: #4CAF50;
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
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
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
  color: #4CAF50;
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

/* ✨ NEW: Success Modal Styling */
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
  background: #4CAF50;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
}

/* ✨ NEW: Loading Modal Styling */
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
  border-top: 5px solid #1877F2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

:deep(.loading-modal-simple .alert-button-group) {
  display: none;
}
</style>