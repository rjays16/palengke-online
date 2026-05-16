<template>
  <ion-page>
    <!-- Conversations List View -->
    <div v-if="!selectedChat" class="messages-view">
      <ion-header>
        <ion-toolbar color="success">
          <ion-buttons slot="start">
            <ion-back-button default-href="/home"></ion-back-button>
          </ion-buttons>
          <ion-title>Messages</ion-title>
          <ion-buttons slot="end">
            <ion-button @click="showSearch = !showSearch">
              <ion-icon :icon="searchOutline"></ion-icon>
            </ion-button>
          </ion-buttons>
        </ion-toolbar>
        
        <!-- Search Bar -->
        <ion-toolbar v-if="showSearch">
          <ion-searchbar 
            v-model="searchQuery" 
            placeholder="Search conversations..."
            animated
            @ionInput="filterConversations"
          ></ion-searchbar>
        </ion-toolbar>
      </ion-header>

      <ion-content :fullscreen="true">
        <!-- Filter Tabs -->
        <div class="filter-tabs">
          <div 
            class="tab" 
            :class="{ active: activeFilter === 'all' }"
            @click="activeFilter = 'all'"
          >
            All
            <span class="count">{{ conversations.length }}</span>
          </div>
          <div 
            class="tab" 
            :class="{ active: activeFilter === 'customers' }"
            @click="activeFilter = 'customers'"
          >
            Customers
            <span class="count">{{ customerCount }}</span>
          </div>
          <div 
            class="tab" 
            :class="{ active: activeFilter === 'drivers' }"
            @click="activeFilter = 'drivers'"
          >
            Drivers
            <span class="count">{{ driverCount }}</span>
          </div>
        </div>

        <!-- Conversations List -->
        <div class="conversations-list">
          <div 
            v-for="conversation in filteredConversations" 
            :key="conversation.id"
            class="conversation-item"
            :class="{ unread: conversation.unread }"
            @click="openChat(conversation)"
          >
            <div class="avatar" :class="conversation.type">
              <img v-if="conversation.avatar" :src="conversation.avatar" :alt="conversation.name">
              <ion-icon v-else :icon="conversation.type === 'customer' ? personOutline : carOutline"></ion-icon>
            </div>
            
            <div class="conversation-content">
              <div class="conversation-header">
                <h3 class="name">{{ conversation.name }}</h3>
                <span class="time">{{ conversation.time }}</span>
              </div>
              
              <div class="conversation-preview">
                <p class="last-message">{{ conversation.lastMessage }}</p>
                <div class="meta">
                  <span v-if="conversation.orderId" class="order-badge">#{{ conversation.orderId }}</span>
                  <span v-if="conversation.unread" class="unread-badge">{{ conversation.unread }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="filteredConversations.length === 0" class="empty-state">
            <ion-icon :icon="chatbubblesOutline" class="empty-icon"></ion-icon>
            <h3>No messages yet</h3>
            <p>Start chatting with customers and drivers</p>
          </div>
        </div>
      </ion-content>
    </div>

    <!-- Chat View -->
    <div v-else class="chat-view">
      <ion-header>
        <ion-toolbar color="success">
          <ion-buttons slot="start">
            <ion-button @click="closeChat">
              <ion-icon :icon="arrowBackOutline"></ion-icon>
            </ion-button>
          </ion-buttons>
          
          <div class="chat-header-info" @click="showChatInfo">
            <div class="avatar-small" :class="selectedChat.type">
              <img v-if="selectedChat.avatar" :src="selectedChat.avatar" :alt="selectedChat.name">
              <ion-icon v-else :icon="selectedChat.type === 'customer' ? personOutline : carOutline"></ion-icon>
            </div>
            <div>
              <ion-title class="chat-title">{{ selectedChat.name }}</ion-title>
              <p class="chat-subtitle">{{ selectedChat.type === 'customer' ? 'Customer' : 'Driver' }}</p>
            </div>
          </div>
          
          <ion-buttons slot="end">
            <ion-button @click="makeCall">
              <ion-icon :icon="callOutline"></ion-icon>
            </ion-button>
            <ion-button @click="showChatOptions">
              <ion-icon :icon="ellipsisVerticalOutline"></ion-icon>
            </ion-button>
          </ion-buttons>
        </ion-toolbar>
      </ion-header>

      <ion-content ref="chatContent" class="chat-content">
        <!-- Date Divider -->
        <div class="date-divider">
          <span>{{ chatDate }}</span>
        </div>

        <!-- Messages -->
        <div class="messages-container">
          <div 
            v-for="(message, index) in selectedChat.messages" 
            :key="index"
            class="message-wrapper"
            :class="{ 'message-sent': message.sent, 'message-received': !message.sent }"
          >
            <div class="message-bubble" :class="{ sent: message.sent }">
              <!-- Image Message -->
              <div v-if="message.image" class="message-image">
                <img :src="message.image" :alt="message.text || 'Image'">
              </div>
              
              <!-- Text Message -->
              <p v-if="message.text" class="message-text">{{ message.text }}</p>
              
              <!-- Order Info -->
              <div v-if="message.orderInfo" class="order-info-card">
                <div class="order-header">
                  <ion-icon :icon="receiptOutline"></ion-icon>
                  <span>Order #{{ message.orderInfo.id }}</span>
                </div>
                <div class="order-items">
                  <p v-for="(item, i) in message.orderInfo.items" :key="i">
                    {{ item.name }} - {{ item.qty }}{{ item.unit }}
                  </p>
                </div>
                <div class="order-total">
                  Total: ₱{{ message.orderInfo.total.toLocaleString() }}
                </div>
              </div>
              
              <span class="message-time">{{ message.time }}</span>
              <ion-icon v-if="message.sent && message.status === 'read'" :icon="checkmarkDoneOutline" class="read-status"></ion-icon>
              <ion-icon v-else-if="message.sent && message.status === 'delivered'" :icon="checkmarkOutline" class="delivered-status"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Typing Indicator -->
        <div v-if="isTyping" class="typing-indicator">
          <div class="typing-bubble">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </ion-content>

      <!-- Quick Replies (if available) -->
      <div v-if="quickReplies.length > 0" class="quick-replies">
        <div 
          v-for="(reply, index) in quickReplies" 
          :key="index"
          class="quick-reply-chip"
          @click="sendQuickReply(reply)"
        >
          {{ reply }}
        </div>
      </div>

      <!-- Message Input -->
      <ion-footer>
        <ion-toolbar>
          <div class="message-input-container">
            <ion-button fill="clear" @click="attachImage">
              <ion-icon :icon="imageOutline" color="medium"></ion-icon>
            </ion-button>
            
            <ion-textarea
              v-model="newMessage"
              placeholder="Type a message..."
              :auto-grow="true"
              :rows="1"
              @keyup.enter="sendMessage"
              class="message-input"
            ></ion-textarea>
            
            <ion-button 
              :disabled="!newMessage.trim()" 
              @click="sendMessage"
              class="send-button"
              :class="{ active: newMessage.trim() }"
            >
              <ion-icon :icon="sendOutline"></ion-icon>
            </ion-button>
          </div>
        </ion-toolbar>
      </ion-footer>
    </div>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonFooter,
  IonButtons,
  IonButton,
  IonBackButton,
  IonIcon,
  IonSearchbar,
  IonTextarea,
  actionSheetController,
  alertController,
  toastController
} from '@ionic/vue';
import {
  searchOutline,
  personOutline,
  carOutline,
  chatbubblesOutline,
  arrowBackOutline,
  callOutline,
  ellipsisVerticalOutline,
  receiptOutline,
  checkmarkDoneOutline,
  checkmarkOutline,
  imageOutline,
  sendOutline,
  trashOutline,
  volumeMuteOutline,
  informationCircleOutline
} from 'ionicons/icons';
import { MOCK_CONVERSATIONS, QUICK_REPLIES, AUTO_RESPONSES } from '@/mock/conversations';

const router = useRouter();

// State
const showSearch = ref(false);
const searchQuery = ref('');
const activeFilter = ref('all');
const selectedChat = ref(null);
const newMessage = ref('');
const isTyping = ref(false);
const chatContent = ref(null);

// Mock Conversations Data
const conversations = ref(MOCK_CONVERSATIONS);

// Quick Replies
const quickReplies = ref(QUICK_REPLIES);

// Computed
const filteredConversations = computed(() => {
  let filtered = conversations.value;
  
  // Filter by type
  if (activeFilter.value === 'customers') {
    filtered = filtered.filter(c => c.type === 'customer');
  } else if (activeFilter.value === 'drivers') {
    filtered = filtered.filter(c => c.type === 'driver');
  }
  
  // Filter by search
  if (searchQuery.value) {
    filtered = filtered.filter(c => 
      c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      c.lastMessage.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }
  
  return filtered;
});

const customerCount = computed(() => {
  return conversations.value.filter(c => c.type === 'customer').length;
});

const driverCount = computed(() => {
  return conversations.value.filter(c => c.type === 'driver').length;
});

const chatDate = computed(() => {
  return 'Today';
});

// Methods
const filterConversations = () => {
  // Triggered on search input
};

const openChat = (conversation) => {
  selectedChat.value = conversation;
  // Mark as read
  conversation.unread = 0;
  
  // Scroll to bottom
  nextTick(() => {
    scrollToBottom();
  });
};

const closeChat = () => {
  selectedChat.value = null;
  newMessage.value = '';
};

const scrollToBottom = () => {
  if (chatContent.value && chatContent.value.$el) {
    chatContent.value.$el.scrollToBottom(300);
  }
};

const sendMessage = () => {
  if (!newMessage.value.trim()) return;
  
  const message = {
    text: newMessage.value.trim(),
    sent: true,
    time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }),
    status: 'delivered'
  };
  
  selectedChat.value.messages.push(message);
  
  // Update last message in conversation
  selectedChat.value.lastMessage = message.text;
  selectedChat.value.time = 'Just now';
  
  newMessage.value = '';
  
  nextTick(() => {
    scrollToBottom();
  });
  
  // Simulate typing response
  simulateTyping();
};

const sendQuickReply = (reply) => {
  newMessage.value = reply;
  sendMessage();
};

const simulateTyping = () => {
  isTyping.value = true;
  
  setTimeout(() => {
    isTyping.value = false;
    
    // Simulate received message
    const response = {
      text: AUTO_RESPONSES[Math.floor(Math.random() * AUTO_RESPONSES.length)],
      sent: false,
      time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }),
      status: 'delivered'
    };
    
    selectedChat.value.messages.push(response);
    selectedChat.value.lastMessage = response.text;
    selectedChat.value.time = 'Just now';
    
    nextTick(() => {
      scrollToBottom();
    });
  }, 2000);
};

const attachImage = async () => {
  const toast = await toastController.create({
    message: 'Image upload feature coming soon!',
    duration: 2000,
    color: 'primary',
    position: 'top'
  });
  await toast.present();
};

const makeCall = async () => {
  if (!selectedChat.value) return;
  
  const alert = await alertController.create({
    header: 'Call ' + selectedChat.value.name,
    message: 'This will initiate a phone call',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      { 
        text: 'Call', 
        handler: () => {
          // TODO: Implement call functionality
          console.log('Calling:', selectedChat.value.name);
        }
      }
    ]
  });
  await alert.present();
};

const showChatInfo = async () => {
  const alert = await alertController.create({
    header: selectedChat.value.name,
    message: `Type: ${selectedChat.value.type === 'customer' ? 'Customer' : 'Driver'}
${selectedChat.value.orderId ? `Order: #${selectedChat.value.orderId}` : ''}`,
    buttons: ['OK']
  });
  await alert.present();
};

const showChatOptions = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Chat Options',
    buttons: [
      {
        text: 'View Profile',
        icon: informationCircleOutline,
        handler: () => {
          console.log('View profile');
        }
      },
      {
        text: 'Mute Notifications',
        icon: volumeMuteOutline,
        handler: () => {
          console.log('Mute');
        }
      },
      {
        text: 'Delete Chat',
        icon: trashOutline,
        role: 'destructive',
        handler: () => {
          deleteChat();
        }
      },
      {
        text: 'Cancel',
        role: 'cancel'
      }
    ]
  });
  await actionSheet.present();
};

const deleteChat = async () => {
  const alert = await alertController.create({
    header: 'Delete Chat',
    message: 'Are you sure you want to delete this conversation?',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Delete',
        role: 'destructive',
        handler: () => {
          const index = conversations.value.findIndex(c => c.id === selectedChat.value.id);
          if (index > -1) {
            conversations.value.splice(index, 1);
          }
          closeChat();
        }
      }
    ]
  });
  await alert.present();
};

onMounted(() => {
  // Initial setup if needed
});
</script>

<style scoped>
/* Messages View */
.messages-view {
  height: 100%;
}

/* Filter Tabs */
.filter-tabs {
  display: flex;
  background: white;
  border-bottom: 1px solid #e0e0e0;
  padding: 0 16px;
  gap: 8px;
}

.tab {
  flex: 1;
  text-align: center;
  padding: 14px 12px;
  font-size: 14px;
  font-weight: 600;
  color: #666;
  cursor: pointer;
  position: relative;
  border-bottom: 3px solid transparent;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.tab.active {
  color: #4CAF50;
  border-bottom-color: #4CAF50;
}

.tab .count {
  background: #f0f0f0;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 700;
}

.tab.active .count {
  background: #E8F5E9;
  color: #4CAF50;
}

/* Conversations List */
.conversations-list {
  background: #f8f9fa;
}

.conversation-item {
  background: white;
  padding: 16px;
  display: flex;
  gap: 12px;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
  transition: background 0.2s;
}

.conversation-item:active {
  background: #f8f9fa;
}

.conversation-item.unread {
  background: #f9fef9;
}

.avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 28px;
  background: #f0f0f0;
  color: #999;
}

.avatar.customer {
  background: #E8F5E9;
  color: #4CAF50;
}

.avatar.driver {
  background: #E3F2FD;
  color: #2196F3;
}

.avatar img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.conversation-content {
  flex: 1;
  min-width: 0;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 6px;
}

.name {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.time {
  font-size: 12px;
  color: #999;
}

.conversation-preview {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
}

.last-message {
  font-size: 14px;
  color: #666;
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  flex: 1;
}

.conversation-item.unread .last-message {
  font-weight: 600;
  color: #333;
}

.meta {
  display: flex;
  gap: 8px;
  align-items: center;
}

.order-badge {
  background: #E3F2FD;
  color: #2196F3;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 600;
}

.unread-badge {
  background: #4CAF50;
  color: white;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  font-size: 80px;
  color: #ddd;
  margin-bottom: 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 700;
  color: #333;
  margin: 0 0 8px 0;
}

.empty-state p {
  font-size: 14px;
  color: #999;
  margin: 0;
}

/* Chat View */
.chat-view {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.chat-header-info {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  cursor: pointer;
}

.avatar-small {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.avatar-small img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.chat-title {
  font-size: 16px;
  font-weight: 700;
  padding: 0;
}

.chat-subtitle {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
  margin: 0;
}

/* Chat Content */
.chat-content {
  --background: #f0f2f5;
}

.date-divider {
  text-align: center;
  padding: 16px 0;
}

.date-divider span {
  background: white;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 12px;
  color: #666;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.messages-container {
  padding: 0 16px 16px 16px;
}

.message-wrapper {
  display: flex;
  margin-bottom: 12px;
}

.message-wrapper.message-sent {
  justify-content: flex-end;
}

.message-wrapper.message-received {
  justify-content: flex-start;
}

.message-bubble {
  max-width: 75%;
  padding: 10px 14px;
  border-radius: 18px;
  position: relative;
  animation: messageSlideIn 0.3s ease;
}

.message-bubble.sent {
  background: #4CAF50;
  color: white;
  border-bottom-right-radius: 4px;
}

.message-wrapper.message-received .message-bubble {
  background: white;
  color: #1a1a1a;
  border-bottom-left-radius: 4px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.message-text {
  margin: 0;
  font-size: 15px;
  line-height: 1.4;
  word-wrap: break-word;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
  margin-top: 4px;
  display: block;
}

.message-bubble.sent .message-time {
  text-align: right;
}

.read-status,
.delivered-status {
  font-size: 14px;
  margin-left: 4px;
  vertical-align: middle;
}

.message-image {
  margin-bottom: 8px;
}

.message-image img {
  width: 100%;
  border-radius: 12px;
  max-width: 250px;
}

/* Order Info Card */
.order-info-card {
  background: rgba(255, 255, 255, 0.2);
  padding: 12px;
  border-radius: 12px;
  margin-top: 8px;
}

.message-wrapper.message-received .order-info-card {
  background: #f8f9fa;
}

.order-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  font-weight: 600;
  font-size: 13px;
}

.order-items {
  margin: 8px 0;
  font-size: 13px;
}

.order-items p {
  margin: 4px 0;
}

.order-total {
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid rgba(255, 255, 255, 0.3);
  font-weight: 700;
  font-size: 14px;
}

.message-wrapper.message-received .order-total {
  border-top-color: #e0e0e0;
}

/* Typing Indicator */
.typing-indicator {
  padding: 0 16px;
  margin-bottom: 12px;
}

.typing-bubble {
  background: white;
  padding: 12px 16px;
  border-radius: 18px;
  border-bottom-left-radius: 4px;
  display: inline-flex;
  gap: 6px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.typing-bubble span {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #999;
  animation: typing 1.4s infinite;
}

.typing-bubble span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-bubble span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes typing {
  0%, 60%, 100% {
    opacity: 0.3;
    transform: translateY(0);
  }
  30% {
    opacity: 1;
    transform: translateY(-4px);
  }
}

@keyframes messageSlideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Quick Replies */
.quick-replies {
  padding: 12px 16px;
  background: white;
  border-top: 1px solid #e0e0e0;
  display: flex;
  gap: 8px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.quick-reply-chip {
  background: #f0f0f0;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13px;
  color: #333;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
}

.quick-reply-chip:active {
  background: #4CAF50;
  color: white;
}

/* Message Input */
.message-input-container {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  padding: 8px;
  background: white;
}

.message-input {
  flex: 1;
  --padding-start: 12px;
  --padding-end: 12px;
  --padding-top: 8px;
  --padding-bottom: 8px;
  background: #f0f0f0;
  border-radius: 20px;
  font-size: 15px;
}

.send-button {
  --border-radius: 50%;
  width: 44px;
  height: 44px;
  margin: 0;
  --background: #f0f0f0;
  --color: #999;
}

.send-button.active {
  --background: #4CAF50;
  --color: white;
}

/* Responsive */
@media (max-width: 360px) {
  .message-bubble {
    max-width: 85%;
  }
  
  .avatar {
    width: 48px;
    height: 48px;
  }
}
</style>