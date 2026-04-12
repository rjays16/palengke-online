<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button default-href="/checkout"></ion-back-button>
        </ion-buttons>
        <ion-title>Payment Methods</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="payment-content">

      <!-- Cashless Banner -->
      <div class="cashless-banner">
        <p>💳 Enjoy easier, cashless payments</p>
      </div>

      <!-- QR Payment -->
      <div class="section-group">
        <div class="payment-item" @click="selectMethod('qrph')">
          <div class="payment-logo qrph">QR</div>
          <div class="payment-info">
            <h4>QRPh</h4>
            <p>Scan the driver's QR to pay</p>
          </div>
          <div class="radio-btn" :class="{ selected: selectedMethod === 'qrph' }"></div>
        </div>
      </div>

      <!-- Linked Methods -->
      <div class="section-group">
        <div class="section-label">Linked Methods</div>
        <p class="section-hint">Tap to set as default</p>

        <div
          v-for="method in linkedMethods"
          :key="method.id"
          class="payment-item"
          @click="selectMethod(method.id)"
        >
          <div class="payment-logo" :class="method.type">
            <span>{{ method.icon }}</span>
          </div>
          <div class="payment-info">
            <h4>{{ method.name }}</h4>
            <p v-if="method.balance">Balance • ₱{{ method.balance }}</p>
            <span class="default-badge" v-if="method.isDefault">Default</span>
          </div>
          <div class="radio-btn" :class="{ selected: selectedMethod === method.id }"></div>
        </div>
      </div>

      <!-- Add Methods -->
      <div class="section-group">
        <div class="section-label">Add Methods</div>

        <div
          v-for="method in addMethods"
          :key="method.id"
          class="payment-item"
          @click="addPaymentMethod(method)"
        >
          <div class="payment-logo" :class="method.type">
            <span>{{ method.icon }}</span>
          </div>
          <div class="payment-info">
            <h4>{{ method.name }}</h4>
            <p v-if="method.description">{{ method.description }}</p>
          </div>
          <ion-icon :icon="chevronForwardOutline" class="arrow-icon"></ion-icon>
        </div>
      </div>

      <!-- Insufficient Balance Modal -->
      <div class="insufficient-overlay" v-if="showInsufficientModal">
        <div class="insufficient-modal">
          <h3>You don't have enough balance</h3>
          <p>To complete your transaction, please top-up your wallet or use another payment method.</p>
          <ion-button expand="block" color="success" class="change-method-btn" @click="dismissModal">
            Change Payment Method
          </ion-button>
          <ion-button expand="block" fill="clear" color="medium" @click="topUpNow">
            Top Up Now
          </ion-button>
        </div>
      </div>

    </ion-content>

    <!-- Save Button -->
    <div class="save-bar">
      <ion-button expand="block" color="success" class="save-btn" @click="saveMethod">
        Use This Payment Method
      </ion-button>
    </div>

  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButtons, IonBackButton, IonButton, IonIcon,
  toastController
} from '@ionic/vue';
import { chevronForwardOutline } from 'ionicons/icons';

const router = useRouter();
const selectedMethod = ref('gcash');
const showInsufficientModal = ref(false);

const linkedMethods = ref([
  { id: 'wallet', name: 'Palengke Wallet', icon: '💚', type: 'wallet', balance: '152.00', isDefault: true },
  { id: 'gcash', name: 'GCash', icon: '', type: 'gcash', balance: null, isDefault: false },
  { id: 'cash', name: 'Cash On Delivery', icon: '💵', type: 'cash', balance: null, isDefault: false },
]);

const addMethods = ref([
  { id: 'add-wallet', name: 'Cash in Balance', icon: '💚', type: 'wallet', description: 'Earn up to 0.8% back in PalengkePoints' },
  { id: 'add-card', name: 'Cards', icon: '💳', type: 'card', description: null },
  { id: 'add-maya', name: 'Maya', icon: '🟣', type: 'maya', description: null },
]);

const selectMethod = (methodId) => { selectedMethod.value = methodId; };

const addPaymentMethod = async (method) => {
  const toast = await toastController.create({
    message: `Adding ${method.name}...`,
    duration: 1500, color: 'primary', position: 'top'
  });
  await toast.present();
};

const saveMethod = async () => {
  const method = linkedMethods.value.find(m => m.id === selectedMethod.value);
  if (method?.id === 'wallet' && parseFloat(method.balance) < 100) {
    showInsufficientModal.value = true;
    return;
  }
  const toast = await toastController.create({
    message: 'Payment method saved!',
    duration: 1500, color: 'success', position: 'top'
  });
  await toast.present();
  router.back();
};

const dismissModal = () => { showInsufficientModal.value = false; };
const topUpNow = () => {
  showInsufficientModal.value = false;
  // Navigate to top up page
};
</script>

<style scoped>
.payment-content { --background: #f8f9fa; }

.cashless-banner {
  background: #E8F5E9; padding: 14px 20px;
  border-bottom: 1px solid #C8E6C9;
}
.cashless-banner p { font-size: 14px; font-weight: 600; color: #2E7D32; margin: 0; }

.section-group { background: white; margin-bottom: 8px; }
.section-label {
  font-size: 15px; font-weight: 700; color: #1a1a1a;
  padding: 16px 20px 4px;
}
.section-hint { font-size: 12px; color: #999; padding: 0 20px 12px; margin: 0; }

.payment-item {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 20px; border-bottom: 1px solid #f5f5f5;
  cursor: pointer; transition: background 0.2s;
}
.payment-item:last-child { border-bottom: none; }
.payment-item:active { background: #f8f9fa; }

.payment-logo {
  width: 48px; height: 48px; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; flex-shrink: 0; font-weight: 800;
}
.payment-logo.qrph { background: #E3F2FD; color: #2196F3; font-size: 14px; }
.payment-logo.gcash { background: #E3F2FD; }
.payment-logo.cash { background: #E8F5E9; }
.payment-logo.wallet { background: #E8F5E9; }
.payment-logo.card { background: #F3E5F5; }
.payment-logo.maya { background: #EDE7F6; }

.payment-info { flex: 1; }
.payment-info h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px 0; }
.payment-info p { font-size: 13px; color: #666; margin: 0; }

.default-badge {
  display: inline-block; background: #f0f0f0; color: #666;
  padding: 2px 8px; border-radius: 8px; font-size: 11px; font-weight: 600;
  margin-top: 4px;
}

.radio-btn {
  width: 22px; height: 22px; border-radius: 50%;
  border: 2px solid #e0e0e0; flex-shrink: 0; transition: all 0.2s;
}
.radio-btn.selected {
  border-color: #4CAF50; background: #4CAF50;
  box-shadow: 0 0 0 3px rgba(76,175,80,0.2);
}

.arrow-icon { font-size: 20px; color: #ccc; }

/* Insufficient Modal */
.insufficient-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5); z-index: 1000;
  display: flex; align-items: flex-end;
}
.insufficient-modal {
  background: white; border-radius: 24px 24px 0 0;
  padding: 32px 24px; width: 100%;
}
.insufficient-modal h3 { font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0 0 12px 0; }
.insufficient-modal p { font-size: 14px; color: #666; margin: 0 0 24px 0; line-height: 1.5; }
.change-method-btn { --border-radius: 14px; height: 52px; font-weight: 700; text-transform: none; margin-bottom: 8px; }

/* Save Bar */
.save-bar {
  background: white; padding: 16px 20px;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
}
.save-btn { --border-radius: 14px; height: 52px; font-size: 15px; font-weight: 700; text-transform: none; }
</style>