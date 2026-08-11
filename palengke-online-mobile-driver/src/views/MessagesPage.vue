<template>
  <ion-page @touchstart="unlockAudio" @click="unlockAudio">
    <audio ref="messageAudio" preload="auto" src="/assets/messages/10.mp3" style="display: none"></audio>
    <audio ref="callAudio" preload="auto" src="/assets/calls/15.mp3" style="display: none" loop></audio>

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

        <ion-toolbar v-if="showSearch">
          <ion-searchbar v-model="searchQuery" placeholder="Search conversations..." animated></ion-searchbar>
        </ion-toolbar>

        <!-- Filter Tabs -->
        <ion-toolbar>
          <div class="filter-tabs">
            <div class="tab" :class="{ active: activeFilter === 'all' }" @click="activeFilter = 'all'">
              All
              <span class="count">{{ conversations.length }}</span>
            </div>
            <div class="tab" :class="{ active: activeFilter === 'customers' }" @click="activeFilter = 'customers'">
              Customers
              <span class="count">{{ customerCount }}</span>
            </div>
            <div class="tab" :class="{ active: activeFilter === 'sellers' }" @click="activeFilter = 'sellers'">
              Sellers
              <span class="count">{{ sellerCount }}</span>
            </div>
          </div>
        </ion-toolbar>
      </ion-header>

      <ion-content :fullscreen="true">
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
              <img v-if="conversation.avatar" :src="conversation.avatar" :alt="conversation.name" />
              <ion-icon v-else :icon="conversation.type === 'seller' ? storefrontOutline : personOutline"></ion-icon>
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
            <p>Conversations with sellers and customers will appear here</p>
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

          <div class="chat-header-info">
            <div class="avatar-small" :class="selectedChat.type">
              <img v-if="selectedChat.avatar" :src="selectedChat.avatar" :alt="selectedChat.name" />
              <ion-icon v-else :icon="selectedChat.type === 'seller' ? storefrontOutline : personOutline"></ion-icon>
            </div>
            <div>
              <ion-title class="chat-title">{{ selectedChat.name }}</ion-title>
              <p class="chat-subtitle">{{ selectedChat.type === 'seller' ? 'Seller' : 'Customer' }}</p>
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
          <div class="date-divider">
            <span>Today</span>
          </div>

          <div class="messages-container">
            <div
              v-for="(message, index) in selectedChat.messages"
              :key="index"
              class="message-wrapper"
              :class="{ 'message-sent': message.sent, 'message-received': !message.sent }"
            >
              <div class="message-bubble" :class="{ sent: message.sent }" @click="confirmDeleteMessage(message)">
                <div v-if="message.image" class="message-image">
                  <img :src="message.image" :alt="message.text || 'Image'" />
                </div>
                <p v-if="message.text" class="message-text">{{ message.text }}</p>
                <span class="message-time">{{ message.time }}</span>
                <ion-icon
                  v-if="message.sent && message.status === 'read'"
                  :icon="checkmarkDoneOutline"
                  class="read-status"
                ></ion-icon>
                <ion-icon
                  v-else-if="message.sent && message.status === 'delivered'"
                  :icon="checkmarkOutline"
                  class="delivered-status"
                ></ion-icon>
              </div>
            </div>
          </div>
        </template>
      </ion-content>

      <!-- Message Input -->
      <ion-footer>
        <ion-toolbar>
          <div class="message-input-container">
            <ion-button fill="clear" @click="attachImage">
              <ion-icon :icon="imageOutline" color="medium"></ion-icon>
            </ion-button>

            <ion-input
              v-model="newMessage"
              placeholder="Type a message..."
              class="message-input"
              @keyup.enter="sendMessage"
            ></ion-input>

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

    <!-- Full-Screen Outgoing Call Screen -->
    <div v-if="showCallModal && !incomingCall" class="call-screen" @click="endCall" @touchend.prevent="endCall">
      <div class="call-screen-bg">
        <div class="call-screen-top">
          <p class="call-screen-label">Palengke Online</p>
        </div>

        <div class="call-screen-profile">
          <div class="call-screen-avatar">
            <img v-if="callAvatar" :src="callAvatar" :alt="callName" />
            <div v-else class="call-screen-avatar-fallback">
              <ion-icon :icon="personOutline"></ion-icon>
            </div>
          </div>
          <h2 class="call-screen-name">{{ callName }}</h2>
          <p class="call-screen-status">{{ callStatusLabel }}</p>
        </div>

        <div class="call-screen-actions">
          <button class="call-action-btn">
            <ion-icon :icon="callOutline" class="call-action-icon"></ion-icon>
            <span>End Call</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Full-Screen Incoming Call Screen -->
    <div v-if="callingEnabled && incomingCall" class="call-screen">
      <div class="call-screen-bg">
        <div class="call-screen-top">
          <p class="call-screen-label">Incoming Call</p>
        </div>

        <div class="call-screen-profile">
          <div class="call-screen-avatar incoming-pulse">
            <div class="call-screen-avatar-fallback">
              <ion-icon :icon="personOutline"></ion-icon>
            </div>
          </div>
          <h2 class="call-screen-name">{{ incomingCall.caller_name }}</h2>
          <p class="call-screen-status">
            {{ incomingCall.caller_type === 'seller' ? 'Seller' : 'Driver' }} is calling you...
          </p>
        </div>

        <div class="call-screen-actions incoming-actions">
          <button class="call-action-btn decline-btn" @click="declineIncomingCall">
            <ion-icon :icon="callOutline" class="call-action-icon"></ion-icon>
            <span>Decline</span>
          </button>
          <button class="call-action-btn accept-btn" @click="acceptIncomingCall">
            <ion-icon :icon="callOutline" class="call-action-icon"></ion-icon>
            <span>Accept</span>
          </button>
        </div>
      </div>
    </div>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonFooter,
  IonButtons,
  IonBackButton,
  IonButton,
  IonIcon,
  IonInput,
  IonSpinner,
  alertController,
  toastController,
  actionSheetController,
} from '@ionic/vue';
import {
  searchOutline,
  personOutline,
  storefrontOutline,
  chatbubblesOutline,
  arrowBackOutline,
  callOutline,
  ellipsisVerticalOutline,
  checkmarkDoneOutline,
  checkmarkOutline,
  imageOutline,
  sendOutline,
  trashOutline,
  cubeOutline,
} from 'ionicons/icons';
import { useMesibo } from '@/services/mesibo';

import { API_BASE } from '@/services/api';

const callingEnabled = ref(true);

async function fetchCallingSetting() {
  try {
    const res = await fetch(`${API_BASE}/api/settings`, {
      headers: { Accept: 'application/json' },
    });
    if (res.ok) {
      const data = await res.json();
      callingEnabled.value = data.enable_calling !== 'false';
    }
  } catch {
    /* ignore */
  }
}

const {
  callState,
  callDuration,
  initMesibo,
  startCall,
  acceptCall,
  declineCall,
  endCall: endMesiboCall,
  formatCallDuration,
} = useMesibo();

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('driver_token');
}

function getUserEmail() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('userEmail');
}

const router = useRouter();

// State
const showSearch = ref(false);
const searchQuery = ref('');
const activeFilter = ref('all');
const selectedChat = ref(null);
const newMessage = ref('');
const chatContent = ref(null);
const loading = ref(false);
const sendingMessage = ref(false);
const loadingMessages = ref(false);
const conversations = ref([]);

// Call Modal
const showCallModal = ref(false);
const callName = ref('');
const callAvatar = ref('');

// Incoming Call
const incomingCall = ref(null);
let incomingCallPollTimer = null;

// Polling
let conversationPollTimer = null;
let messagePollTimer = null;

// Audio
const messageAudio = ref(null);
const callAudio = ref(null);
let audioUnlocked = false;

function unlockAudio() {
  if (audioUnlocked) return;
  if (messageAudio.value) {
    messageAudio.value.volume = 0.01;
    messageAudio.value
      .play()
      .then(() => {
        messageAudio.value?.pause();
        messageAudio.value.currentTime = 0;
        messageAudio.value.volume = 0.7;
        audioUnlocked = true;
      })
      .catch(() => {});
  }
}

function playMessageSound() {
  if (!audioUnlocked) return;
  try {
    if (messageAudio.value) {
      messageAudio.value.currentTime = 0;
      messageAudio.value.play().catch(() => {});
    }
  } catch {
    /* ignore */
  }
}

// Computed
const filteredConversations = computed(() => {
  let filtered = conversations.value;
  if (activeFilter.value === 'customers') {
    filtered = filtered.filter((c) => c.type === 'customer');
  } else if (activeFilter.value === 'sellers') {
    filtered = filtered.filter((c) => c.type === 'seller');
  }
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter((c) => c.name.toLowerCase().includes(q) || c.lastMessage.toLowerCase().includes(q));
  }
  return filtered;
});

const customerCount = computed(() => conversations.value.filter((c) => c.type === 'customer').length);
const sellerCount = computed(() => conversations.value.filter((c) => c.type === 'seller').length);

// API Calls
async function apiGet(endpoint) {
  const token = getToken();
  if (!token) return null;
  try {
    const res = await fetch(`${API_BASE}${endpoint}`, {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    });
    if (res.ok) return await res.json();
  } catch {
    /* ignore */
  }
  return null;
}

async function apiPost(endpoint, body) {
  const token = getToken();
  if (!token) return null;
  try {
    const isFormData = body instanceof FormData;
    const res = await fetch(`${API_BASE}${endpoint}`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
        ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
      },
      body: isFormData ? body : JSON.stringify(body),
    });
    if (res.ok) return await res.json();
  } catch {
    /* ignore */
  }
  return null;
}

async function apiDelete(endpoint) {
  const token = getToken();
  if (!token) return null;
  try {
    const res = await fetch(`${API_BASE}${endpoint}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    });
    if (res.ok) return await res.json();
  } catch {
    /* ignore */
  }
  return null;
}

async function loadConversations() {
  const email = getUserEmail();
  if (!email) return;
  loading.value = true;
  try {
    const data = await apiGet(`/api/driver/conversations?email=${encodeURIComponent(email)}`);
    if (data) conversations.value = data.conversations || [];
  } catch (e) {
    console.error('Failed to load conversations:', e);
  } finally {
    loading.value = false;
  }
}

async function loadMessages(conversationId) {
  const email = getUserEmail();
  if (!email) return [];
  loadingMessages.value = true;
  try {
    const data = await apiGet(`/api/driver/conversations/${conversationId}?email=${encodeURIComponent(email)}`);
    return data?.messages || [];
  } catch (e) {
    console.error('Failed to load messages:', e);
    return [];
  } finally {
    loadingMessages.value = false;
  }
}

// Chat Methods
async function openChat(conversation) {
  selectedChat.value = { ...conversation, messages: [] };
  const messages = await loadMessages(conversation.id);
  if (selectedChat.value) {
    selectedChat.value.messages = messages;
  }
  conversation.unread = 0;

  await apiPost(`/api/driver/conversations/${conversation.id}/read`, { email: getUserEmail() });

  nextTick(() => scrollToBottom());
  startMessagePolling(conversation.id);
}

function closeChat() {
  stopMessagePolling();
  selectedChat.value = null;
  newMessage.value = '';
}

function scrollToBottom() {
  if (chatContent.value && chatContent.value.$el) {
    chatContent.value.$el.scrollToBottom(300);
  }
}

async function sendMessage() {
  if (!newMessage.value.trim() || !selectedChat.value || sendingMessage.value) return;

  const email = getUserEmail();
  const text = newMessage.value.trim();
  newMessage.value = '';

  const tempMessage = {
    id: Date.now(),
    text,
    sent: true,
    time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }),
    status: 'delivered',
  };

  selectedChat.value.messages.push(tempMessage);
  selectedChat.value.lastMessage = text;

  nextTick(() => scrollToBottom());

  sendingMessage.value = true;
  try {
    const data = await apiPost(`/api/driver/conversations/${selectedChat.value.id}/messages`, {
      email,
      message: text,
    });
    if (data?.data) {
      tempMessage.id = data.data.id;
      tempMessage.time = data.data.time;
      tempMessage.status = data.data.status;
    }
  } catch (e) {
    console.error('Failed to send message:', e);
  } finally {
    sendingMessage.value = false;
  }
}

async function attachImage() {
  if (!selectedChat.value) return;
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/png,image/jpg,image/jpeg,image/webp';
  input.onchange = async (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    sendingMessage.value = true;
    const formData = new FormData();
    formData.append('image', file);
    formData.append('email', getUserEmail() || '');

    try {
      const data = await apiPost(`/api/driver/conversations/${selectedChat.value.id}/upload-image`, formData);
      if (data?.data) {
        selectedChat.value.messages.push(data.data);
        selectedChat.value.lastMessage = 'Sent an image';
        nextTick(() => scrollToBottom());
      }
    } catch (e) {
      console.error('Failed to upload image:', e);
    } finally {
      sendingMessage.value = false;
    }
  };
  input.click();
}

// Call
async function makeCall() {
  if (!selectedChat.value) return;

  const participantPhone = selectedChat.value.phone;

  if (!callingEnabled.value) {
    if (participantPhone) {
      window.location.href = `tel:${participantPhone}`;
    } else {
      showToast('Phone number not available', 'warning');
    }
    return;
  }

  const participantEmail = selectedChat.value.email || selectedChat.value.participantEmail;
  const participantName = selectedChat.value.name;
  const participantAvatar = selectedChat.value.avatar || '';

  callName.value = participantName;
  callAvatar.value = participantAvatar;
  showCallModal.value = true;

  const email = getUserEmail();
  const signalId = await startCall(
    Number(selectedChat.value.id),
    email || '',
    'Driver',
    'driver',
    participantEmail || '',
  );

  if (!signalId) {
    showCallModal.value = false;
    if (participantPhone) {
      window.location.href = `tel:${participantPhone}`;
    } else {
      showToast('Failed to start call', 'warning');
    }
  }
}

const callStatusLabel = computed(() => {
  switch (callState.value) {
    case 'ringing':
      return 'Ringing...';
    case 'connecting':
      return 'Connecting...';
    case 'connected':
      return formatCallDuration(callDuration.value);
    case 'ended':
      return 'Call Ended';
    default:
      return 'Calling...';
  }
});

function endCall() {
  showCallModal.value = false;
  endMesiboCall();
}

async function acceptIncomingCall() {
  if (!incomingCall.value) return;
  callAvatar.value = '';
  callName.value = incomingCall.value.caller_name;
  await acceptCall(incomingCall.value.id);
  incomingCall.value = null;
  showCallModal.value = true;
  stopIncomingCallPolling();
}

async function declineIncomingCall() {
  if (!incomingCall.value) return;
  await declineCall(incomingCall.value.id);
  incomingCall.value = null;
  stopIncomingCallPolling();
}

function startIncomingCallPolling() {
  stopIncomingCallPolling();
  incomingCallPollTimer = setInterval(async () => {
    const email = getUserEmail();
    if (!email) return;

    if (incomingCall.value) {
      try {
        const res = await fetch(`${API_BASE}/api/calls/signal/${incomingCall.value.id}/status`, {
          headers: { Accept: 'application/json' },
        });
        if (res.ok) {
          const data = await res.json();
          const status = data.signal?.status;
          if (status === 'ended' || status === 'declined' || status === 'accepted') {
            incomingCall.value = null;
            stopIncomingCallPolling();
            startIncomingCallPolling();
          }
        }
      } catch {
        /* ignore */
      }
      return;
    }

    if (showCallModal.value) return;
    try {
      const res = await fetch(`${API_BASE}/api/calls/signal?email=${encodeURIComponent(email)}`, {
        headers: { Accept: 'application/json' },
      });
      if (!res.ok) return;
      if (showCallModal.value || incomingCall.value) return;
      const data = await res.json();
      if (data.signal) {
        incomingCall.value = data.signal;
      }
    } catch {
      /* ignore */
    }
  }, 2000);
}

function stopIncomingCallPolling() {
  if (incomingCallPollTimer) {
    clearInterval(incomingCallPollTimer);
    incomingCallPollTimer = null;
  }
}

function playCallSound() {
  if (!audioUnlocked) return;
  try {
    if (callAudio.value) {
      callAudio.value.currentTime = 0;
      callAudio.value.play().catch(() => {});
    }
  } catch {
    /* ignore */
  }
}

// Action Sheets & Alerts
async function showToast(message, color = 'dark') {
  const toast = await toastController.create({ message, duration: 2000, color, position: 'top' });
  await toast.present();
}

async function showChatOptions() {
  if (!selectedChat.value) return;
  const actionSheet = await actionSheetController.create({
    header: 'Chat Options',
    buttons: [
      {
        text: 'Delete Chat',
        icon: trashOutline,
        role: 'destructive',
        handler: () => deleteChat(),
      },
      { text: 'Cancel', role: 'cancel' },
    ],
  });
  await actionSheet.present();
}

async function deleteChat() {
  const alert = await alertController.create({
    header: 'Delete Chat',
    message: 'Are you sure you want to delete this conversation?',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Delete',
        role: 'destructive',
        handler: async () => {
          const email = getUserEmail();
          await apiDelete(
            `/api/driver/conversations/${selectedChat.value?.id}?email=${encodeURIComponent(email || '')}`,
          );
          const index = conversations.value.findIndex((c) => c.id === selectedChat.value?.id);
          if (index > -1) conversations.value.splice(index, 1);
          closeChat();
        },
      },
    ],
  });
  await alert.present();
}

async function confirmDeleteMessage(message) {
  if (!selectedChat.value) return;
  const alert = await alertController.create({
    header: 'Delete Message',
    message: 'Delete this message?',
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Delete',
        role: 'destructive',
        handler: async () => {
          const email = getUserEmail();
          await apiDelete(`/api/driver/messages/${message.id}?email=${encodeURIComponent(email || '')}`);
          if (selectedChat.value) {
            const idx = selectedChat.value.messages.findIndex((m) => m.id === message.id);
            if (idx > -1) selectedChat.value.messages.splice(idx, 1);
          }
        },
      },
    ],
  });
  await alert.present();
}

// Polling
function startConversationPolling() {
  stopConversationPolling();
  conversationPollTimer = setInterval(async () => {
    const email = getUserEmail();
    if (!email || selectedChat.value) return;
    const data = await apiGet(`/api/driver/conversations?email=${encodeURIComponent(email)}`);
    if (data) conversations.value = data.conversations || [];
  }, 5000);
}

function stopConversationPolling() {
  if (conversationPollTimer) {
    clearInterval(conversationPollTimer);
    conversationPollTimer = null;
  }
}

function startMessagePolling(conversationId) {
  stopMessagePolling();
  messagePollTimer = setInterval(async () => {
    const email = getUserEmail();
    if (!email || !selectedChat.value || selectedChat.value.id !== conversationId) return;
    const data = await apiGet(`/api/driver/conversations/${conversationId}?email=${encodeURIComponent(email)}`);
    if (data?.messages) {
      const prevLen = selectedChat.value.messages.length;
      selectedChat.value.messages = data.messages;
      if (data.messages.length > prevLen) playMessageSound();
      nextTick(() => scrollToBottom());
    }
  }, 3000);
}

function stopMessagePolling() {
  if (messagePollTimer) {
    clearInterval(messagePollTimer);
    messagePollTimer = null;
  }
}

// Back button
function handleBackButton(event) {
  event.detail.register(0, () => {
    if (selectedChat.value) {
      closeChat();
    } else {
      router.back();
    }
  });
}

watch(callState, (newState) => {
  if (newState === 'ended') {
    showCallModal.value = false;
    if (maxCallTimer) {
      clearTimeout(maxCallTimer);
      maxCallTimer = null;
    }
  } else if (newState === 'connected') {
    if (maxCallTimer) clearTimeout(maxCallTimer);
    maxCallTimer = setTimeout(() => {
      endCall();
    }, 300000);
  }
});

watch(incomingCall, (val) => {
  if (val) {
    playCallSound();
  } else {
    if (callAudio.value) {
      callAudio.value.pause();
      callAudio.value.currentTime = 0;
    }
  }
});

let maxCallTimer = null;
let globalCallCloseHandler = null;

onMounted(async () => {
  document.addEventListener('ionBackButton', handleBackButton);
  await fetchCallingSetting();
  loadConversations();
  startConversationPolling();
  const email = getUserEmail();
  if (email) {
    if (callingEnabled.value) {
      initMesibo(email, 'driver');
      startIncomingCallPolling();
    }
  }
});

watch(showCallModal, (val) => {
  if (val) {
    setTimeout(() => {
      globalCallCloseHandler = () => {
        if (showCallModal.value) endCall();
      };
      document.addEventListener('click', globalCallCloseHandler);
      document.addEventListener('touchend', globalCallCloseHandler);
    }, 300);
  } else {
    if (globalCallCloseHandler) {
      document.removeEventListener('click', globalCallCloseHandler);
      document.removeEventListener('touchend', globalCallCloseHandler);
      globalCallCloseHandler = null;
    }
  }
});

onUnmounted(() => {
  document.removeEventListener('ionBackButton', handleBackButton);
  stopConversationPolling();
  stopMessagePolling();
  stopIncomingCallPolling();
  if (maxCallTimer) {
    clearTimeout(maxCallTimer);
    maxCallTimer = null;
  }
  if (globalCallCloseHandler) {
    document.removeEventListener('click', globalCallCloseHandler);
    document.removeEventListener('touchend', globalCallCloseHandler);
    globalCallCloseHandler = null;
  }
});
</script>

<style scoped>
.messages-view {
  height: 100%;
}

/* Loading */
.loading-state {
  text-align: center;
  padding: 60px 20px;
}
.loading-icon {
  font-size: 60px;
  color: #4caf50;
  margin-bottom: 16px;
  animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
  0%,
  100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 1;
    transform: scale(1.1);
  }
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
  color: #4caf50;
  border-bottom-color: #4caf50;
}
.tab .count {
  background: #f0f0f0;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 700;
}
.tab.active .count {
  background: #e8f5e9;
  color: #4caf50;
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
  background: #e8f5e9;
  color: #4caf50;
}
.avatar.seller {
  background: #e3f2fd;
  color: #2196f3;
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
  background: #e3f2fd;
  color: #2196f3;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 600;
}
.unread-badge {
  background: #4caf50;
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
  background: #4caf50;
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

/* Message Input */
.message-input-container {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  background: white;
}
.message-input {
  flex: 1;
  --padding-start: 12px;
  --padding-end: 12px;
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
  --background: #4caf50;
  --color: white;
}

/* Full-Screen Call Screen */
.call-screen {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
}

.call-screen-bg {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  padding: 60px 24px 40px;
  color: white;
}

.call-screen-top {
  text-align: center;
}
.call-screen-label {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 2px;
}

.call-screen-profile {
  text-align: center;
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.call-screen-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 24px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  animation: callPulse 2s ease-in-out infinite;
}

@keyframes callPulse {
  0%,
  100% {
    border-color: rgba(76, 175, 80, 0.4);
    transform: scale(1);
  }
  50% {
    border-color: rgba(76, 175, 80, 0.9);
    transform: scale(1.03);
  }
}

.call-screen-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.call-screen-avatar-fallback {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.15);
  font-size: 48px;
}

.call-screen-name {
  font-size: 26px;
  font-weight: 700;
  margin: 0 0 8px;
  color: white;
}
.call-screen-status {
  font-size: 16px;
  color: #4caf50;
  font-weight: 500;
  margin: 0;
  animation: callPulseStatus 1.5s ease-in-out infinite;
}

@keyframes callPulseStatus {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.call-screen-actions {
  padding-bottom: 20px;
  width: 100%;
  display: flex;
  justify-content: center;
}

.call-action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  background: #ff3b30;
  color: white;
  border: none;
  border-radius: 50%;
  width: 72px;
  height: 72px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  padding: 0;
  transition: transform 0.2s;
}

.call-action-btn .call-action-icon {
  font-size: 28px;
  margin-top: 16px;
  pointer-events: none;
}
.call-action-btn:active {
  transform: scale(0.9);
}
.call-action-btn span {
  pointer-events: none;
}

.incoming-actions {
  gap: 40px;
}
.decline-btn {
  background: #ff3b30;
}
.accept-btn {
  background: #4caf50;
}

.incoming-pulse {
  animation: incomingPulse 1.5s ease-in-out infinite;
}
@keyframes incomingPulse {
  0%,
  100% {
    box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.6);
  }
  50% {
    box-shadow: 0 0 0 20px rgba(76, 175, 80, 0);
  }
}

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
