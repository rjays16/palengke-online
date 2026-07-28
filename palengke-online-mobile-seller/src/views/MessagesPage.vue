<template>
  <ion-page @touchstart="unlockAudio" @click="unlockAudio">
    <!-- Hidden audio elements for notifications -->
    <audio ref="messageAudio" preload="auto" src="/assets/messages/10.mp3" style="display: none"></audio>
    <audio ref="callAudio" preload="auto" src="/assets/calls/15.mp3" style="display: none" loop></audio>
    <!-- Conversations List View -->
    <div v-if="!selectedChat" class="messages-view">
      <ion-header>
        <ion-toolbar color="success">
          <ion-buttons slot="start">
            <ion-button @click="goHome">
              <ion-icon :icon="arrowBackOutline"></ion-icon>
            </ion-button>
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
          <div class="tab" :class="{ active: activeFilter === 'all' }" @click="activeFilter = 'all'">
            All
            <span class="count">{{ conversations.length }}</span>
          </div>
          <div class="tab" :class="{ active: activeFilter === 'customers' }" @click="activeFilter = 'customers'">
            Customers
            <span class="count">{{ customerCount }}</span>
          </div>
          <div class="tab" :class="{ active: activeFilter === 'drivers' }" @click="activeFilter = 'drivers'">
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
              <img v-if="conversation.avatar" :src="conversation.avatar" :alt="conversation.name" />
              <ion-icon v-else :icon="conversation.type === 'customer' ? personOutline : carOutline"></ion-icon>
            </div>

            <div class="conversation-content">
              <div class="conversation-header">
                <h3 class="name">{{ conversation.name }}</h3>
                <span class="time">{{ conversation.time }}</span>
                <ion-icon v-if="conversation.muted" :icon="volumeMuteOutline" class="mute-icon-list"></ion-icon>
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
              <img v-if="selectedChat.avatar" :src="selectedChat.avatar" :alt="selectedChat.name" />
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
              <div class="message-bubble" :class="{ sent: message.sent }" @click="confirmDeleteMessage(message)">
                <!-- Image Message -->
                <div v-if="message.image" class="message-image">
                  <img :src="message.image" :alt="message.text || 'Image'" />
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
                  <div class="order-total">Total: ₱{{ message.orderInfo.total.toLocaleString() }}</div>
                </div>

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
            <img v-if="incomingCall.caller_type === 'driver'" :src="callAvatar || ''" alt="" />
            <div v-else class="call-screen-avatar-fallback">
              <ion-icon :icon="personOutline"></ion-icon>
            </div>
          </div>
          <h2 class="call-screen-name">{{ incomingCall.caller_name }}</h2>
          <p class="call-screen-status">{{ incomingCall.caller_type === 'driver' ? 'Driver' : 'Seller' }} is calling you...</p>
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

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonFooter,
  IonButton,
  IonButtons,
  IonIcon,
  IonSearchbar,
  IonSpinner,
  IonTextarea,
  actionSheetController,
  alertController,
  toastController,
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
  volumeHighOutline,
  cubeOutline,
} from 'ionicons/icons';
import { api } from '@/utils/apiClient';
import { type Conversation } from '@/types';
import { useChatWebSocket } from '@/composables/useChatWebSocket';
import { useMesibo, type IncomingCallSignal } from '@/services/mesibo';
import { API_BASE } from '@/config/api';

const router = useRouter();
const apiBaseUrl = API_BASE;

const userEmail = localStorage.getItem('userEmail');

const callingEnabled = ref(true);

async function fetchCallingSetting() {
  try {
    const res = await fetch(`${apiBaseUrl}/settings`, {
      headers: { Accept: 'application/json' },
    });
    if (res.ok) {
      const data = await res.json();
      callingEnabled.value = data.enable_calling !== 'false';
    }
  } catch {}
}

const chatWs = useChatWebSocket(userEmail, {
  onConversationsUpdate(list) {
    conversations.value = list;
    if (selectedChat.value) {
      const updated = list.find((c: any) => c.id === selectedChat.value?.id);
      if (updated) {
        const prevMsgCount = selectedChat.value.messages.length;
        selectedChat.value = { ...selectedChat.value, ...updated };
        if (updated.lastMessage !== selectedChat.value?.lastMessage) {
          if (!updated.muted) playMessageSound();
        }
      }
    }
  },
  onMessagesUpdate(conversationId: any, messages: any) {
    if (selectedChat.value && selectedChat.value.id === conversationId) {
      const prevLen = selectedChat.value.messages.length;
      selectedChat.value.messages = messages;
      if (messages.length > prevLen && !selectedChat.value.muted) {
        playMessageSound();
      }
      nextTick(() => scrollToBottom());
    }
  },
  onNewMessageSound() {
    playMessageSound();
  },
});

const {
  mesiboInitialized,
  callState,
  callDuration,
  activeCallSignalId,
  initMesibo,
  startCall,
  acceptCall,
  declineCall,
  endCall: endMesiboCall,
  formatCallDuration,
} = useMesibo();

// State
const showSearch = ref(false);
const searchQuery = ref('');
const activeFilter = ref('all');
const selectedChat = ref(null as Conversation | null);
const newMessage = ref('');
const isTyping = ref(false);
const chatContent = ref(null as any);
const endCallBtnRef = ref<HTMLButtonElement | null>(null);
const loading = ref(false);
const sendingMessage = ref(false);
const loadingMessages = ref(false);

const conversations = ref([] as Conversation[]);

// Call Modal
const showCallModal = ref(false);
const callName = ref('');
const callAvatar = ref('');

// Incoming Call
const incomingCall = ref<IncomingCallSignal | null>(null);
let incomingCallPollTimer: ReturnType<typeof setInterval> | null = null;

// Quick Replies
const quickReplies = ref([]);

function isMuted(conversation: any) {
  if (!conversation) return false;
  if (conversation.muted !== undefined) return conversation.muted;
  const found = conversations.value.find((c) => c.id === conversation.id);
  return found ? found.muted : false;
}

async function toggleMute(conversation: any) {
  if (!conversation) return;
  try {
    const data = await api.post(`/seller/conversations/${conversation.id}/toggle-mute`, { email: userEmail });
    conversation.muted = data.muted;
    const found = conversations.value.find((c) => c.id === conversation.id);
    if (found) found.muted = data.muted;
  } catch (e) {
    console.error('Failed to toggle mute:', e);
  }
}

// Computed
const filteredConversations = computed(() => {
  let filtered = conversations.value;

  // Filter by type
  if (activeFilter.value === 'customers') {
    filtered = filtered.filter((c) => c.type === 'customer');
  } else if (activeFilter.value === 'drivers') {
    filtered = filtered.filter((c) => c.type === 'driver');
  }

  // Filter by search
  if (searchQuery.value) {
    filtered = filtered.filter(
      (c) =>
        c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        c.lastMessage.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
  }

  return filtered;
});

const customerCount = computed(() => {
  return conversations.value.filter((c) => c.type === 'customer').length;
});

const driverCount = computed(() => {
  return conversations.value.filter((c) => c.type === 'driver').length;
});

const chatDate = computed(() => {
  return 'Today';
});

// API Calls
async function loadConversations() {
  if (!userEmail) return;
  loading.value = true;
  try {
    const data = await api.get(`/seller/conversations?email=${encodeURIComponent(userEmail)}`);
    conversations.value = data.conversations || [];
  } catch (e) {
    console.error('Failed to load conversations:', e);
  } finally {
    loading.value = false;
  }
}

async function loadMessages(conversationId: any) {
  if (!userEmail) return;
  loadingMessages.value = true;
  try {
    const data = await api.get(`/seller/conversations/${conversationId}?email=${encodeURIComponent(userEmail)}`);
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

const openChat = async (conversation: any) => {
  selectedChat.value = { ...conversation, messages: [] };

  const messages = await loadMessages(conversation.id);
  if (selectedChat.value) {
    selectedChat.value.messages = messages;
  }

  conversation.unread = 0;

  // Mark as read on server
  try {
    await api.post(`/seller/conversations/${conversation.id}/read`, { email: userEmail });
  } catch (e) {
    console.error('Failed to mark as read:', e);
  }

  nextTick(() => {
    scrollToBottom();
  });

  chatWs.watchConversation(conversation.id);
};

const closeChat = () => {
  chatWs.watchConversation(null);
  selectedChat.value = null;
  newMessage.value = '';
};

// Audio notification
const messageAudio = ref(null as HTMLAudioElement | null);
const callAudio = ref(null as HTMLAudioElement | null);
let audioUnlocked = false;

const unlockAudio = () => {
  if (audioUnlocked) return;
  if (messageAudio.value) {
    messageAudio.value.volume = 0.01;
    messageAudio.value
      .play()
      .then(() => {
        messageAudio.value?.pause();
        messageAudio.value!.currentTime = 0;
        messageAudio.value!.volume = 0.7;
        audioUnlocked = true;
      })
      .catch((e: any) => console.warn('Audio unlock failed:', e));
  }
};

const playMessageSound = () => {
  if (!audioUnlocked) return;
  try {
    if (messageAudio.value) {
      messageAudio.value.currentTime = 0;
      messageAudio.value.play().catch((e: any) => console.warn('Play msg sound:', e));
    }
  } catch {}
};

const playCallSound = () => {
  if (!audioUnlocked) return;
  try {
    if (callAudio.value) {
      callAudio.value.currentTime = 0;
      callAudio.value.play().catch((e: any) => console.warn('Play call sound:', e));
    }
  } catch {}
};

// Real-time updates via WebSocket (falls back to polling)

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
    status: 'delivered' as const,
  };

  selectedChat.value.messages.push(tempMessage);
  selectedChat.value.lastMessage = text;
  selectedChat.value.time = 'Just now';

  nextTick(() => {
    scrollToBottom();
  });

  sendingMessage.value = true;
  try {
    const data = await api.post(`/seller/conversations/${selectedChat.value.id}/messages`, { email: userEmail, message: text });
    if (data.data) {
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

const sendQuickReply = (reply: any) => {
  newMessage.value = reply;
  sendMessage();
};

const attachImage = async () => {
  if (!selectedChat.value) return;
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/png,image/jpg,image/jpeg,image/webp';
  input.onchange = async (e: any) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;

    sendingMessage.value = true;
    const formData = new FormData();
    formData.append('image', file);
    formData.append('email', userEmail || '');

    try {
      const data = await api.post(`/seller/conversations/${selectedChat.value?.id}/upload-image`, formData);
      if (data.data) {
        selectedChat.value?.messages.push(data.data);
        selectedChat.value!.lastMessage = 'Sent an image';
        selectedChat.value!.time = 'Just now';
        nextTick(() => {
          scrollToBottom();
        });
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

  const signalId = await startCall(
    Number(selectedChat.value.id),
    userEmail || '',
    'Seller',
    'seller',
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
};

const callStatusLabel = computed(() => {
  switch (callState.value) {
    case 'ringing': return 'Ringing...';
    case 'connecting': return 'Connecting...';
    case 'connected': return formatCallDuration(callDuration.value);
    case 'ended': return 'Call Ended';
    default: return 'Calling...';
  }
});

const endCall = () => {
  showCallModal.value = false;
  endMesiboCall();
};

const acceptIncomingCall = async () => {
  if (!incomingCall.value) return;
  callAvatar.value = '';
  callName.value = incomingCall.value.caller_name;
  await acceptCall(incomingCall.value.id);
  incomingCall.value = null;
  showCallModal.value = true;
  stopIncomingCallPolling();
};

const declineIncomingCall = async () => {
  if (!incomingCall.value) return;
  await declineCall(incomingCall.value.id);
  incomingCall.value = null;
  stopIncomingCallPolling();
};

function startIncomingCallPolling() {
  stopIncomingCallPolling();
  incomingCallPollTimer = setInterval(async () => {
    const email = localStorage.getItem('userEmail');
    if (!email) return;

    if (incomingCall.value) {
      try {
        const res = await fetch(`${apiBaseUrl}/calls/signal/${incomingCall.value.id}/status`, {
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
      } catch {}
      return;
    }

    if (showCallModal.value) return;
    try {
      const res = await fetch(`${apiBaseUrl}/calls/signal?email=${encodeURIComponent(email)}`, {
        headers: { Accept: 'application/json' },
      });
      if (!res.ok) return;
      if (showCallModal.value || incomingCall.value) return;
      const data = await res.json();
      if (data.signal) {
        incomingCall.value = data.signal;
      }
    } catch {}
  }, 2000);
}

function stopIncomingCallPolling() {
  if (incomingCallPollTimer) {
    clearInterval(incomingCallPollTimer);
    incomingCallPollTimer = null;
  }
}

const showToast = async (message: string, color = 'dark') => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color,
    position: 'top',
  });
  await toast.present();
};

const showChatInfo = async () => {
  if (!selectedChat.value) return;
  const alert = await alertController.create({
    header: selectedChat.value.name,
    message: `Type: ${selectedChat.value.type === 'customer' ? 'Customer' : 'Driver'}
${selectedChat.value.orderId ? `Order: #${selectedChat.value.orderId}` : ''}`,
    buttons: ['OK'],
  });
  await alert.present();
};

const showChatOptions = async () => {
  if (!selectedChat.value) return;
  const muted = isMuted(selectedChat.value);
  const actionSheet = await actionSheetController.create({
    header: 'Chat Options',
    buttons: [
      {
        text: muted ? 'Unmute Notifications' : 'Mute Notifications',
        icon: muted ? volumeMuteOutline : volumeHighOutline,
        handler: () => {
          toggleMute(selectedChat.value);
        },
      },
      {
        text: 'Delete Chat',
        icon: trashOutline,
        role: 'destructive',
        handler: () => {
          deleteChat();
        },
      },
      {
        text: 'Cancel',
        role: 'cancel',
      },
    ],
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
            await api.delete(`/seller/conversations/${selectedChat.value?.id}?email=${encodeURIComponent(userEmail || '')}`);
          } catch (e) {
            console.error('Failed to delete conversation:', e);
          }
          const index = conversations.value.findIndex((c) => c.id === selectedChat.value?.id);
          if (index > -1) {
            conversations.value.splice(index, 1);
          }
          closeChat();
        },
      },
    ],
  });
  await alert.present();
};

const confirmDeleteMessage = async (message: any) => {
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
          await deleteMessage(message);
        },
      },
    ],
  });
  await alert.present();
};

const showDeleteLoading = async () => {
  const loading = await alertController.create({
    cssClass: 'loading-modal-simple',
    message: 'Deleting...',
    buttons: [],
  });
  await loading.present();
  return loading;
};

const deleteMessage = async (message: any) => {
  if (!message.id) return;
  const loading = await showDeleteLoading();
  try {
    await api.delete(`/seller/messages/${message.id}?email=${encodeURIComponent(userEmail || '')}`);
    if (selectedChat.value) {
      const idx = selectedChat.value.messages.findIndex((m) => m.id === message.id);
      if (idx > -1) {
        selectedChat.value.messages.splice(idx, 1);
      }
    }
  } catch (e) {
    console.error('Failed to delete message:', e);
  } finally {
    await loading.dismiss();
  }
};

const goHome = () => {
  window.location.href = '/home';
};

function handleBackButton(event: any) {
  event.detail.register(0, () => {
    if (showSearch.value) {
      showSearch.value = false;
    } else {
      router.back();
    }
  });
}

watch(callState, (newState) => {
  if (newState === 'ended') {
    showCallModal.value = false;
    if (maxCallTimer) { clearTimeout(maxCallTimer); maxCallTimer = null; }
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

let maxCallTimer: ReturnType<typeof setTimeout> | null = null;
let globalCallCloseHandler: (() => void) | null = null;

onMounted(async () => {
  document.addEventListener('ionBackButton', handleBackButton);
  await fetchCallingSetting();
  loadConversations();
  chatWs.connect();
  if (userEmail) {
    if (callingEnabled.value) {
      initMesibo(userEmail, 'seller');
      startIncomingCallPolling();
    }
  }
});

watch(showCallModal, (val) => {
  if (val) {
    setTimeout(() => {
      globalCallCloseHandler = () => { if (showCallModal.value) endCall(); };
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
  chatWs.disconnect();
  stopIncomingCallPolling();
  if (maxCallTimer) { clearTimeout(maxCallTimer); maxCallTimer = null; }
  if (globalCallCloseHandler) {
    document.removeEventListener('click', globalCallCloseHandler);
    document.removeEventListener('touchend', globalCallCloseHandler);
    globalCallCloseHandler = null;
  }
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

.avatar.driver {
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

.mute-icon-list {
  font-size: 14px;
  color: #999;
  margin-left: 4px;
  vertical-align: middle;
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
  0%,
  60%,
  100% {
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
  background: #4caf50;
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
  --background: #4caf50;
  --color: white;
}

/* Call Screen */
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
  0%, 100% { border-color: rgba(76, 175, 80, 0.4); transform: scale(1); }
  50% { border-color: rgba(76, 175, 80, 0.9); transform: scale(1.03); }
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
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
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
  0%, 100% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.6); }
  50% { box-shadow: 0 0 0 20px rgba(76, 175, 80, 0); }
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
