<template>
  <div class="page-shell">
    <div class="page-header">
      <h2>Subscription Payments</h2>
      <div class="header-total" v-if="totalRevenue > 0">
        Total Revenue: <strong>₱{{ totalRevenue.toLocaleString() }}</strong>
      </div>
    </div>

    <div class="page-toolbar">
      <div class="search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input v-model="search" placeholder="Search by store or email..." />
      </div>
      <span class="result-count">{{ filteredPayments.length }} payments</span>
    </div>

    <div v-if="loading" class="loading-state">Loading payments...</div>
    <div v-else-if="error" class="loading-state error">{{ error }}</div>
    <div v-else-if="filteredPayments.length === 0" class="loading-state">No subscription payments found.</div>
    <div v-else class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Store</th>
            <th>Email</th>
            <th>Type</th>
            <th>Details</th>
            <th>Amount</th>
            <th>Payment Method</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in filteredPayments" :key="p.id">
            <td>
              <NuxtLink :to="'/sellers/' + p.seller_id" class="store-link">
                <div class="store-avatar">{{ p.store_name.charAt(0) }}</div>
                <span class="name-cell">{{ p.store_name }}</span>
              </NuxtLink>
            </td>
            <td class="email-cell">{{ p.seller_email }}</td>
            <td>
              <span class="type-badge" :class="p.type">{{ p.type === 'subscription' ? 'Subscription' : 'Top Up' }}</span>
            </td>
            <td>{{ p.plan_name }}<br><span class="date-cell">{{ p.period }}</span></td>
            <td class="amount-cell">₱{{ p.amount.toLocaleString() }}</td>
            <td>{{ p.payment_method_name }}</td>
            <td class="date-cell">{{ p.date }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });

const config = useRuntimeConfig();
const apiBase = config.public.apiBaseUrl || "http://localhost:8000";

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem("palengke_auth_token");
}

const payments = ref([]);
const loading = ref(true);
const error = ref(null);
const search = ref("");

const totalRevenue = computed(() => payments.value.reduce((sum, p) => sum + p.amount, 0));

const filteredPayments = computed(() => {
  if (!search.value) return payments.value;
  const q = search.value.toLowerCase();
  return payments.value.filter(
    (p) => p.store_name?.toLowerCase().includes(q) || p.seller_email?.toLowerCase().includes(q),
  );
});

async function fetchPayments() {
  const token = getToken();
  if (!token) { loading.value = false; return; }
  try {
    const res = await fetch(`${apiBase}/api/admin/subscription-payments`, {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    if (!res.ok) throw new Error(`Failed (${res.status})`);
    const json = await res.json();
    payments.value = json.payments || [];
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
}

onMounted(fetchPayments);
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}
.page-header h2 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 20px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}
.header-total {
  font-size: 14px;
  color: #6b7280;
}
.header-total strong {
  color: #059669;
  font-size: 18px;
}
.page-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}
.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  padding: 8px 12px;
  min-width: 260px;
}
.search-box:focus-within { border-color: #059669; }
.search-box svg { width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0; }
.search-box input {
  border: none; background: transparent; font-size: 13px;
  font-family: "Inter", sans-serif; color: #111827; outline: none; width: 100%;
}
.result-count { font-size: 13px; color: #9ca3af; }
.loading-state {
  text-align: center; padding: 60px 20px; background: #fff;
  border-radius: 12px; border: 1px solid #e5e7eb; font-size: 14px; color: #6b7280;
}
.loading-state.error { color: #dc2626; }
.table-card {
  background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: auto;
}
.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
  text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af;
  text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
}
.data-table td {
  padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb;
}
.store-link {
  display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;
}
.store-link:hover .name-cell { color: #059669; }
.store-avatar {
  width: 34px; height: 34px; border-radius: 8px;
  background: linear-gradient(135deg, #059669, #047857);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 700; color: white; flex-shrink: 0;
}
.name-cell { font-weight: 600; color: #111827; transition: color 0.15s; }
.email-cell { color: #9ca3af; font-size: 12px; }
.amount-cell { font-weight: 700; color: #059669; }
.date-cell { color: #9ca3af; font-size: 12px; }
.plan-badge {
  display: inline-block; padding: 3px 10px; border-radius: 6px;
  background: #f0fdf4; color: #059669; font-size: 11px; font-weight: 600;
}
.type-badge {
  display: inline-block; padding: 3px 10px; border-radius: 6px;
  font-size: 11px; font-weight: 600;
}
.type-badge.subscription {
  background: #f0fdf4; color: #059669;
}
.type-badge.top_up {
  background: #eff6ff; color: #3b82f6;
}
</style>
