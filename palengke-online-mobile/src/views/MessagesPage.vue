<template>
  <ion-page>
    <!-- Chat View (when chat is open) -->
    <div v-if="selectedChat" class="chat-view">
      <ion-header>
        <ion-toolbar color="success">
          <ion-buttons slot="start">
            <ion-button @click="closeChat">
              <ion-icon :icon="arrowBackOutline"></ion-icon>
            </ion-button>
          </ion-buttons>
          <div class="chat-header-info">
            <div class="chat-avatar-small">
              <img v-if="selectedChat.image" :src="selectedChat.image">
              <ion-icon v-else :icon="personOutline"></ion-icon>
            </div>
            <div>
              <ion-title class="chat-title">{{ selectedChat.name }}</ion-title>
              <p class="chat-subtitle">Online</p>
            </div>
          </div>
        </ion-toolbar>
      </ion-header>

      <ion-content class="chat-messages" ref="chatContent" :fullscreen="true">
        <div class="messages-container">
          <div v-for="(message, index) in selectedChat.messages" :key="index"
            class="message-wrapper" :class="{ 'message-sent': message.sent, 'message-received': !message.sent }">
            <div class="message-bubble" :class="{ sent: message.sent }">
              <p class="message-text">{{ message.text }}</p>
              <span class="message-time">{{ message.time }}</span>
              <ion-icon v-if="message.sent && message.status === 'read'" :icon="checkmarkDoneOutline" class="read-status"></ion-icon>
            </div>
          </div>
        </div>
      </ion-content>

      <ion-footer>
        <ion-toolbar>
          <div class="message-input-container">
            <ion-textarea v-model="newMessage" placeholder="Type a message..." :auto-grow="true" :rows="1" @keyup.enter="sendMessage" class="message-input"></ion-textarea>
            <ion-button :disabled="!newMessage.trim()" @click="sendMessage" class="send-button" :class="{ active: newMessage.trim() }">
              <ion-icon :icon="sendOutline"></ion-icon>
            </ion-button>
          </div>
        </ion-toolbar>
      </ion-footer>
    </div>

    <!-- Messages List View -->
    <div v-else class="messages-view">
      <ion-header>
        <ion-toolbar color="success">
          <ion-buttons slot="start">
            <ion-button @click="goBack">
              <ion-icon :icon="arrowBackOutline"></ion-icon>
            </ion-button>
          </ion-buttons>
          <ion-title>Messages</ion-title>
        </ion-toolbar>
      </ion-header>

      <ion-content class="msg-content" :fullscreen="true">
        <div v-if="chats.length === 0" class="empty-state">
          <div class="empty-icon">💬</div>
          <h3>No chats yet</h3>
          <p>Messages from sellers and drivers will appear here</p>
        </div>
        <div v-else class="chat-list">
          <div v-for="chat in chats" :key="chat.id" class="chat-item" @click="openChat(chat)">
            <div class="chat-avatar" v-if="chat.image"><img :src="chat.image"></div>
            <div class="chat-avatar" v-else><ion-icon :icon="personOutline"></ion-icon></div>
            <div class="chat-info">
              <div class="chat-header">
                <h4>{{ chat.name }}</h4>
                <span class="chat-time">{{ chat.time }}</span>
              </div>
              <div class="chat-preview">
                <p>{{ chat.lastMessage }}</p>
                <span class="unread-badge" v-if="chat.unread > 0">{{ chat.unread }}</span>
              </div>
            </div>
          </div>
        </div>
      </ion-content>
    </div>
  </ion-page>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonFooter,
  IonButtons, IonBackButton, IonButton, IonIcon, IonTextarea
} from '@ionic/vue';
import { arrowBackOutline, personOutline, sendOutline, checkmarkDoneOutline } from 'ionicons/icons';

const router = useRouter();
const selectedChat = ref(null);
const newMessage = ref('');
const chatContent = ref(null);

const chats = ref([
  { 
    id: 1, 
    name: "Juan's Fresh Market", 
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=100&h=100&fit=crop', 
    lastMessage: 'Your order is ready for pickup!', 
    time: '5 mins ago', 
    unread: 2,
    messages: [
      { text: 'Hello! Thank you for your order', sent: false, time: '10:00 AM', status: 'read' },
      { text: 'Hi! When will my order be ready?', sent: true, time: '10:05 AM', status: 'read' },
      { text: 'Your order is ready for pickup!', sent: false, time: '10:10 AM', status: 'read' },
      { text: 'Thank you! I\'ll pick it up now', sent: true, time: '10:15 AM', status: 'read' }
    ]
  },
  { 
    id: 2, 
    name: 'Juan Dela Cruz (Driver)', 
    image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop', 
    lastMessage: 'I am on my way to your location', 
    time: '10 mins ago', 
    unread: 1,
    messages: [
      { text: 'Good morning! I\'m your driver today', sent: false, time: '9:30 AM', status: 'read' },
      { text: 'Hi! How far are you?', sent: true, time: '9:35 AM', status: 'read' },
      { text: 'I am on my way to your location', sent: false, time: '9:40 AM', status: 'delivered' }
    ]
  },
]);

const openChat = (chat) => {
  selectedChat.value = chat;
  chat.unread = 0;
  nextTick(() => {
    if (chatContent.value && chatContent.value.$el) {
      chatContent.value.$el.scrollToBottom(300);
    }
  });
};

const closeChat = () => {
  selectedChat.value = null;
  newMessage.value = '';
};

const goBack = () => {
  router.push('/profile');
};

const sendMessage = () => {
  if (!newMessage.value.trim() || !selectedChat.value) return;
  
  const message = {
    text: newMessage.value.trim(),
    sent: true,
    time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }),
    status: 'delivered'
  };
  
  selectedChat.value.messages.push(message);
  selectedChat.value.lastMessage = message.text;
  newMessage.value = '';
  
  nextTick(() => {
    if (chatContent.value && chatContent.value.$el) {
      chatContent.value.$el.scrollToBottom(300);
    }
  });
};
</script>

<style scoped>
.msg-content { --background: #f8f9fa; overflow-y: auto; flex: 1; }

.empty-state { text-align: center; padding: 80px 40px; }
.empty-icon { font-size: 64px; margin-bottom: 16px; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.empty-state p { font-size: 14px; color: #999; margin: 0; }

.chat-list { padding: 8px 0; }
.chat-item { display: flex; align-items: center; gap: 14px; padding: 14px 20px; background: white; border-bottom: 1px solid #f0f0f0; cursor: pointer; }
.chat-avatar { width: 52px; height: 52px; border-radius: 50%; background: #E8F5E9; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; overflow: hidden; color: #4CAF50; }
.chat-avatar img { width: 100%; height: 100%; object-fit: cover; }
.chat-info { flex: 1; }
.chat-header { display: flex; justify-content: space-between; margin-bottom: 4px; }
.chat-header h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0; }
.chat-time { font-size: 12px; color: #999; }
.chat-preview { display: flex; justify-content: space-between; align-items: center; }
.chat-preview p { font-size: 13px; color: #666; margin: 0; flex: 1; }
.unread-badge { background: #4CAF50; color: white; border-radius: 10px; padding: 2px 7px; font-size: 11px; font-weight: 700; }

/* Chat View */
.chat-view { height: 100%; display: flex; flex-direction: column; overflow: hidden; }
.messages-view { height: 100%; overflow: hidden; display: flex; flex-direction: column; }

.chat-header-info { display: flex; align-items: center; gap: 12px; flex: 1; }

.chat-avatar-small { width: 40px; height: 40px; border-radius: 50%; background: #E8F5E9; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.chat-avatar-small img { width: 100%; height: 100%; object-fit: cover; }
.chat-avatar-small ion-icon { font-size: 20px; color: #4CAF50; }

.chat-title { font-size: 16px; font-weight: 700; padding: 0; }

.chat-subtitle { font-size: 12px; color: rgba(255,255,255,0.8); margin: 0; }

.chat-messages { --background: #f0f2f5; flex: 1; overflow-y: auto; --padding-top: 0; --padding-bottom: 0; }

.messages-container { padding: 16px; }

.message-wrapper { display: flex; margin-bottom: 12px; }
.message-wrapper.message-sent { justify-content: flex-end; }
.message-wrapper.message-received { justify-content: flex-start; }

.message-bubble { max-width: 75%; padding: 10px 14px; border-radius: 18px; position: relative; }
.message-bubble.sent { background: #4CAF50; color: white; border-bottom-right-radius: 4px; }
.message-wrapper.message-received .message-bubble { background: white; color: #1a1a1a; border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }

.message-text { margin: 0; font-size: 15px; line-height: 1.4; }

.message-time { font-size: 11px; opacity: 0.7; margin-top: 4px; display: block; }
.message-bubble.sent .message-time { text-align: right; }

.read-status { font-size: 14px; margin-left: 4px; vertical-align: middle; }

.message-input-container { display: flex; align-items: flex-end; gap: 8px; padding: 8px; background: white; }
.message-input { flex: 1; --padding-start: 12px; --padding-end: 12px; --padding-top: 8px; --padding-bottom: 8px; background: #f0f0f0; border-radius: 20px; font-size: 15px; }
.send-button { --border-radius: 50%; width: 44px; height: 44px; margin: 0; --background: #f0f0f0; --color: #999; }
.send-button.active { --background: #4CAF50; --color: white; }
</style>