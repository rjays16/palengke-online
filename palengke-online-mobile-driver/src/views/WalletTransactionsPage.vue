<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Transaction History</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="balance-card">
        <div class="balance-icon">
          <ion-icon :icon="walletOutline"></ion-icon>
        </div>
        <span class="balance-label">Current Balance</span>
        <span class="balance-amount">₱{{ balance.toLocaleString() }}</span>
      </div>

      <div class="filter-bar">
        <button
          v-for="f in filters"
          :key="f.value"
          class="filter-chip"
          :class="{ active: activeFilter === f.value }"
          @click="activeFilter = f.value"
        >
          {{ f.label }}
        </button>
      </div>

      <div v-if="loading" class="center-state">
        <div class="spinner"></div>
        <p>Loading transactions...</p>
      </div>

      <div v-else-if="filteredTransactions.length === 0" class="center-state">
        <ion-icon :icon="receiptOutline" class="empty-icon"></ion-icon>
        <h3>No transactions</h3>
        <p v-if="activeFilter === 'all'">No wallet activity yet. Top up your wallet to get started.</p>
        <p v-else>No {{ activeFilter }} transactions found.</p>
      </div>

      <div v-else class="txn-list">
        <div v-for="txn in filteredTransactions" :key="txn.id" class="txn-item">
          <div class="txn-left">
            <div class="txn-icon" :class="txn.type === 'top_up' ? 'icon-green' : 'icon-red'">
              <ion-icon :icon="txnIcon(txn.type)"></ion-icon>
            </div>
            <div class="txn-info">
              <span class="txn-title">{{ txnLabel(txn) }}</span>
              <span class="txn-meta">
                {{ formatDate(txn.created_at) }}
                <span v-if="txn.payment_method_name">&middot; {{ txn.payment_method_name }}</span>
              </span>
              <span class="txn-notes" v-if="txn.notes">{{ txn.notes }}</span>
            </div>
          </div>
          <span class="txn-amount" :class="txn.type === 'top_up' ? 'text-green' : 'text-red'">
            {{ txn.type === 'top_up' ? '+' : '-' }}₱{{ txn.amount.toLocaleString() }}
          </span>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButton, IonButtons, IonIcon } from '@ionic/vue';
import { arrowBackOutline, walletOutline, addCircleOutline, removeCircleOutline, receiptOutline } from 'ionicons/icons';
import { API_BASE } from '@/services/api';

const router = useRouter();

function getToken() {
  if (typeof localStorage === 'undefined') return null;
  return localStorage.getItem('driver_token');
}

const balance = ref(0);
const transactions = ref([]);
const loading = ref(true);
const activeFilter = ref('all');

const filters = [
  { label: 'All', value: 'all' },
  { label: 'Top-Up', value: 'top_up' },
  { label: 'Cash-Out', value: 'cash_out' },
];

const filteredTransactions = computed(() => {
  if (activeFilter.value === 'all') return transactions.value;
  return transactions.value.filter((t) => t.type === activeFilter.value);
});

function txnIcon(type) {
  if (type === 'top_up') return addCircleOutline;
  return removeCircleOutline;
}

function txnLabel(txn) {
  switch (txn.type) {
    case 'top_up':
      return 'Wallet Top-Up';
    case 'cash_out':
      return 'Cash Out';
    default:
      return txn.type.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
  }
}

function formatDate(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

async function fetchTransactions() {
  const token = getToken();
  if (!token) return;
  loading.value = true;
  try {
    const res = await fetch(`${API_BASE}/api/driver/wallet/transactions`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (res.ok) {
      const data = await res.json();
      transactions.value = data.transactions || [];
      balance.value = data.balance || 0;
    } else {
      transactions.value = [];
    }
  } catch {
    transactions.value = [];
  } finally {
    loading.value = false;
  }
}

function goBack() {
  router.back();
}

onMounted(fetchTransactions);
</script>

<style scoped>
.balance-card {
  background: linear-gradient(135deg, #059669, #047857);
  border-radius: 16px;
  padding: 24px 20px;
  text-align: center;
  color: white;
  margin-bottom: 20px;
}
.balance-icon {
  font-size: 28px;
  margin-bottom: 6px;
  opacity: 0.9;
}
.balance-label {
  font-size: 13px;
  opacity: 0.8;
  display: block;
}
.balance-amount {
  font-size: 32px;
  font-weight: 700;
  display: block;
  margin-top: 4px;
}
.filter-bar {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}
.filter-chip {
  padding: 6px 14px;
  border-radius: 16px;
  border: 1.5px solid #e5e7eb;
  background: #fff;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.15s;
  font-family: inherit;
}
.filter-chip.active {
  background: #059669;
  border-color: #059669;
  color: #fff;
}
.txn-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-bottom: 24px;
}
.txn-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 14px 16px;
}
.txn-left {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  flex: 1;
  min-width: 0;
}
.txn-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}
.txn-icon.icon-green {
  background: #f0fdf4;
  color: #059669;
}
.txn-icon.icon-red {
  background: #fef2f2;
  color: #dc2626;
}
.txn-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.txn-title {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}
.txn-meta {
  font-size: 12px;
  color: #9ca3af;
  margin-top: 2px;
}
.txn-notes {
  font-size: 12px;
  color: #6b7280;
  margin-top: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.txn-amount {
  font-size: 14px;
  font-weight: 700;
  white-space: nowrap;
  margin-left: 12px;
}
.text-green {
  color: #059669;
}
.text-red {
  color: #dc2626;
}
.center-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}
.center-state h3 {
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  margin: 12px 0 4px;
}
.center-state p {
  font-size: 13px;
  color: #9ca3af;
  margin: 0;
}
.empty-icon {
  font-size: 48px;
  color: #d1d5db;
}
.spinner {
  width: 36px;
  height: 36px;
  border: 4px solid #e5e7eb;
  border-top-color: #059669;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  margin-bottom: 12px;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
