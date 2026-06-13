<template>
  <ion-page @touchstart="unlockAudio" @click="unlockAudio">
    <!-- Hidden audio elements for notifications -->
    <audio ref="messageAudio" preload="auto" src="/assets/messages/10.mp3" style="display:none"></audio>
    <audio ref="callAudio" preload="auto" src="/assets/calls/15.mp3" style="display:none" loop></audio>
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

        <!-- Loading State -->
        <div class="loading-state" v-if="loading">
          <ion-icon :icon="cubeOutline" class="loading-icon"></ion-icon>
          <p>Loading conversations...</p>
        </div>

        <!-- Conversations List -->
        <div class="conversations-list" v-else>
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
        <!-- Loading Messages -->
        <div class="loading-state" v-if="loadingMessages">
          <ion-spinner name="crescent" color="success"></ion-spinner>
          <p>Loading messages...</p>
        </div>

        <template v-else>
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

        </template>

        <!-- Typing Indicator -->
        <div v-if="isTyping && !loadingMessages" class="typing-indicator">
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
              :disabled="!newMessage.trim() || sendingMessage" 
              @click="sendMessage"
              class="send-button"
              :class="{ active: newMessage.trim() && !sendingMessage }"
            >
              <ion-icon v-if="!sendingMessage" :icon="sendOutline"></ion-icon>
              <ion-spinner v-else name="crescent" color="white"></ion-spinner>
            </ion-button>
          </div>
        </ion-toolbar>
      </ion-footer>
    </div>

    <!-- Call Overlay -->
    <div v-if="showCallModal" class="call-overlay" @click.self="endCall">
      <div class="call-card">
        <div class="call-avatar-wrap">
          <img :src="callAvatar" :alt="callName">
        </div>
        <h2 class="call-name-text">{{ callName }}</h2>
        <p class="call-status-text">Calling...</p>
        <button class="call-end-btn" @click="endCall">End Call</button>
      </div>
    </div>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import {
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
  cubeOutline
} from 'ionicons/icons';
const QUICK_REPLIES = [
  'Yes, available po!',
  'Out of stock po',
  'On the way!',
  'Thank you!'
];

const router = useRouter();

const API_BASE = 'http://localhost:8000/api';
const userEmail = localStorage.getItem('userEmail');

// State
const showSearch = ref(false);
const searchQuery = ref('');
const activeFilter = ref('all');
const selectedChat = ref(null);
const newMessage = ref('');
const isTyping = ref(false);
const chatContent = ref(null);
const loading = ref(false);
const sendingMessage = ref(false);
const loadingMessages = ref(false);

const conversations = ref([]);

// Call Modal
const showCallModal = ref(false);
const callName = ref('');
const callAvatar = ref('');

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

// API Calls
async function loadConversations() {
  if (!userEmail) return;
  loading.value = true;
  try {
    const res = await fetch(`${API_BASE}/seller/conversations?email=${encodeURIComponent(userEmail)}`, {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    conversations.value = data.conversations || [];
  } catch (e) {
    console.error('Failed to load conversations:', e);
  } finally {
    loading.value = false;
  }
}

async function loadMessages(conversationId) {
  if (!userEmail) return;
  loadingMessages.value = true;
  try {
    const res = await fetch(`${API_BASE}/seller/conversations/${conversationId}?email=${encodeURIComponent(userEmail)}`, {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    return data.messages || [];
  } catch (e) {
    console.error('Failed to load messages:', e);
    return [];
  } finally {
    loadingMessages.value = false;
  }
}

// Methods
const filterConversations = () => {
  // Triggered on search input
};

const openChat = async (conversation) => {
  selectedChat.value = { ...conversation, messages: [] };

  const messages = await loadMessages(conversation.id);
  if (selectedChat.value) {
    selectedChat.value.messages = messages;
  }

  conversation.unread = 0;

  // Mark as read on server
  try {
    await fetch(`${API_BASE}/seller/conversations/${conversation.id}/read`, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: userEmail }),
    });
  } catch (e) {
    console.error('Failed to mark as read:', e);
  }

  nextTick(() => {
    scrollToBottom();
  });

  startMessagePolling(conversation.id);
};

const closeChat = () => {
  stopMessagePolling();
  selectedChat.value = null;
  newMessage.value = '';
};

// Audio notification
const messageAudio = ref(null);
const callAudio = ref(null);
let audioUnlocked = false;

const unlockAudio = () => {
  if (audioUnlocked) return;
  if (messageAudio.value) {
    messageAudio.value.volume = 0.01;
    messageAudio.value.play().then(() => {
      messageAudio.value.pause();
      messageAudio.value.currentTime = 0;
      messageAudio.value.volume = 0.7;
      audioUnlocked = true;
    }).catch(e => console.warn('Audio unlock failed:', e));
  }
};

const playMessageSound = () => {
  if (!audioUnlocked) return;
  try {
    if (messageAudio.value) {
      messageAudio.value.currentTime = 0;
      messageAudio.value.play().catch(e => console.warn('Play msg sound:', e));
    }
  } catch {}
};

const playCallSound = () => {
  if (!audioUnlocked) return;
  try {
    if (callAudio.value) {
      callAudio.value.currentTime = 0;
      callAudio.value.play().catch(e => console.warn('Play call sound:', e));
    }
  } catch {}
};

// Polling
let conversationCount = 0;
let pollTimer = null;

const startPolling = () => {
  conversationCount = conversations.value.length;
  pollTimer = setInterval(async () => {
    if (!userEmail) return;
    const prev = conversationCount;
    try {
      const res = await fetch(`${API_BASE}/seller/conversations?email=${encodeURIComponent(userEmail)}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      const newList = data.conversations || [];
      if (newList.length > prev && prev > 0) {
        playMessageSound();
      }
      conversations.value = newList;
      conversationCount = newList.length;
    } catch {}
  }, 5000);
};

const stopPolling = () => {
  if (pollTimer) {
    clearInterval(pollTimer);
    pollTimer = null;
  }
};

// Poll for new messages in open chat
let messagePollTimer = null;

const startMessagePolling = (conversationId) => {
  stopMessagePolling();
  messagePollTimer = setInterval(async () => {
    if (!userEmail || !selectedChat.value) return;
    try {
      const res = await fetch(`${API_BASE}/seller/conversations/${conversationId}?email=${encodeURIComponent(userEmail)}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      const serverMessages = data.messages || [];
      if (serverMessages.length > selectedChat.value.messages.length) {
        playMessageSound();
        selectedChat.value.messages = serverMessages;
        nextTick(() => scrollToBottom());
      }
    } catch {}
  }, 3000);
};

const stopMessagePolling = () => {
  if (messagePollTimer) {
    clearInterval(messagePollTimer);
    messagePollTimer = null;
  }
};

const scrollToBottom = () => {
  if (chatContent.value && chatContent.value.$el) {
    chatContent.value.$el.scrollToBottom(300);
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedChat.value || sendingMessage.value) return;

  const text = newMessage.value.trim();
  newMessage.value = '';

  const tempMessage = {
    id: Date.now(),
    text,
    sent: true,
    time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }),
    status: 'delivered'
  };

  selectedChat.value.messages.push(tempMessage);
  selectedChat.value.lastMessage = text;
  selectedChat.value.time = 'Just now';

  nextTick(() => { scrollToBottom(); });

  sendingMessage.value = true;
  try {
    const res = await fetch(`${API_BASE}/seller/conversations/${selectedChat.value.id}/messages`, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: userEmail, message: text }),
    });
    const data = await res.json();
    if (res.ok && data.data) {
      tempMessage.id = data.data.id;
      tempMessage.time = data.data.time;
      tempMessage.status = data.data.status;
    }
  } catch (e) {
    console.error('Failed to send message:', e);
  } finally {
    sendingMessage.value = false;
  }
};

const sendQuickReply = (reply) => {
  newMessage.value = reply;
  sendMessage();
};

const attachImage = async () => {
  if (!selectedChat.value) return;
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/png,image/jpg,image/jpeg,image/webp';
  input.onchange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    sendingMessage.value = true;
    const formData = new FormData();
    formData.append('image', file);
    formData.append('email', userEmail);

    try {
      const res = await fetch(`${API_BASE}/seller/conversations/${selectedChat.value.id}/upload-image`, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: formData,
      });
      const data = await res.json();
      if (res.ok && data.data) {
        selectedChat.value.messages.push(data.data);
        selectedChat.value.lastMessage = 'Sent an image';
        selectedChat.value.time = 'Just now';
        nextTick(() => { scrollToBottom(); });
      }
    } catch (e) {
      console.error('Failed to upload image:', e);
    } finally {
      sendingMessage.value = false;
    }
  };
  input.click();
};

const makeCall = async () => {
  if (!selectedChat.value) return;

  playCallSound();

  callName.value = selectedChat.value.name || 'Contact';
  callAvatar.value = selectedChat.value.avatar || '/assets/images/default.png';
  showCallModal.value = true;
};

const endCall = () => {
  if (callAudio.value) {
    callAudio.value.pause();
    callAudio.value.currentTime = 0;
  }
  showCallModal.value = false;
};

const showToast = async (message, color = 'dark') => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color,
    position: 'top'
  });
  await toast.present();
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
        handler: async () => {
          try {
            await fetch(`${API_BASE}/seller/conversations/${selectedChat.value.id}?email=${encodeURIComponent(userEmail)}`, {
              method: 'DELETE',
              headers: { 'Accept': 'application/json' }
            });
          } catch (e) {
            console.error('Failed to delete conversation:', e);
          }
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
  loadConversations().then(() => {
    conversationCount = conversations.value.length;
    startPolling();
  });
});

onUnmounted(() => {
  stopPolling();
  stopMessagePolling();
});
</script>

<style scoped>
/* Messages View */
.messages-view {
  height: 100%;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 60px 20px;
}

.loading-icon {
  font-size: 60px;
  color: #4CAF50;
  margin-bottom: 16px;
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.1); }
}

.loading-state p {
  font-size: 15px;
  color: #666;
  margin-top: 12px;
}

.loading-state ion-spinner {
  width: 40px;
  height: 40px;
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

/* Call Overlay */
.call-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.call-card {
  background: #fff;
  border-radius: 24px;
  padding: 40px 32px 32px;
  text-align: center;
  min-width: 280px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}

.call-avatar-wrap img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #4CAF50;
  margin-bottom: 12px;
}

.call-name-text {
  font-size: 22px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px;
}

.call-status-text {
  font-size: 15px;
  color: #4CAF50;
  font-weight: 600;
  margin: 0 0 20px;
  animation: pulse-status 1.5s infinite;
}

@keyframes pulse-status {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.call-end-btn {
  background: #ff3b30;
  color: #fff;
  border: none;
  border-radius: 30px;
  height: 48px;
  min-width: 140px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  padding: 0 24px;
}

.call-end-btn:hover {
  background: #d32f2f;
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