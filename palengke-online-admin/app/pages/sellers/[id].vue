<template>
  <div class="detail-page">
    <button class="back-btn" @click="goBack">
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
      >
        <polyline points="15 18 9 12 15 6" />
      </svg>
      Back to Sellers
    </button>

    <div v-if="loading" class="loading-state">Loading seller...</div>
    <div v-else-if="loadError" class="loading-state error">Failed to load: {{ loadError }}</div>
    <div v-else-if="seller" class="detail-content">
      <div class="detail-header">
        <div class="detail-avatar" :style="{ background: '#059669' }">
          {{ seller.name.charAt(0) }}
        </div>
        <div class="detail-title">
          <h1>{{ seller.name }}</h1>
          <span class="detail-subtitle"
            >{{ seller.location }} · {{ seller.category }}</span
          >
        </div>
        <div class="detail-actions">
          <span class="status-badge-lg" :class="seller.status">{{
            seller.status === "active" ? "Active" : "Inactive"
          }}</span>
          <button
            class="action-btn"
            :class="{ suspended: seller.status === 'active' }"
            @click="toggleStatus"
          >
            {{ seller.status === "active" ? "Suspend" : "Activate" }}
          </button>
        </div>
      </div>

      <div class="detail-grid">
        <div class="info-card">
          <h3>Store Information</h3>
          <div class="info-rows">
            <div class="info-row">
              <span class="info-label">Owner</span
              ><span class="info-value">{{ seller.owner }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Category</span
              ><span class="info-value">{{ seller.category }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Location</span
              ><span class="info-value">{{ seller.location }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Products</span
              ><span class="info-value">{{ seller.products }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Revenue</span
              ><span class="info-value"
                >₱{{ seller.revenue.toLocaleString() }}</span
              >
            </div>
            <div class="info-row">
              <span class="info-label">Rating</span
              ><span class="info-value stars">★ {{ seller.rating }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Joined</span
              ><span class="info-value">{{ seller.joined }}</span>
            </div>
          </div>
        </div>

        <div class="info-card">
          <h3>Contact</h3>
          <div class="info-rows">
            <div class="info-row">
              <span class="info-label">Phone</span
              ><span class="info-value">{{
                seller.contact?.phone || "N/A"
              }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Email</span
              ><span class="info-value">{{
                seller.contact?.email || "N/A"
              }}</span>
            </div>
          </div>
          <div class="info-card-actions">
            <button class="ghost-btn" @click="openContactModal">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                />
              </svg>
              Contact
            </button>
          </div>
        </div>

        <div class="info-card">
          <h3>Palengke Wallet</h3>
          <div class="wallet-balance">
            <span class="wallet-amount">₱{{ seller.walletBalance.toLocaleString() }}</span>
            <span class="wallet-label">Current Balance</span>
          </div>
          <div v-if="auth.isSuperAdmin" class="info-card-actions">
            <button class="ghost-btn" @click="openTopUpModal">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;">
                <line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" />
              </svg>
              Top Up
            </button>
          </div>
        </div>

        <div class="info-card sub-card">
          <h3>Subscription</h3>
          <div class="info-rows">
            <div class="info-row">
              <span class="info-label">Plan</span
              ><span class="info-value">{{ seller.subscription.plan }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Amount</span
              ><span class="info-value"
                >₱{{ seller.subscription.amount.toLocaleString() }}</span
              >
            </div>
            <div class="info-row">
              <span class="info-label">Start</span
              ><span class="info-value">{{ seller.subscription.start }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">End</span
              ><span class="info-value">{{ seller.subscription.end }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Status</span
              ><span class="info-value"
                ><span
                  class="status-badge"
                  :class="seller.subscription.status"
                  >{{ seller.subscription.status }}</span
                ></span
              >
            </div>
          </div>

          <div v-if="subAction === null && auth.isSuperAdmin" class="info-card-actions">
            <button class="ghost-btn" @click="subAction = 'change'">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M12 20h9" />
                <path
                  d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"
                />
              </svg>
              Change Plan
            </button>
            <button class="ghost-btn" @click="subAction = 'extend'">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
              Extend
            </button>
            <button
              v-if="seller.subscription.status !== 'cancelled'"
              class="ghost-btn danger"
              @click="subAction = 'cancel'"
            >
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
              Cancel
            </button>
          </div>

          <div v-if="subAction === 'change'" class="sub-action-area">
            <p class="sub-action-title">Change Subscription Plan</p>
            <div class="plan-options">
              <label
                v-for="plan in sellersStore.AVAILABLE_PLANS"
                :key="plan.name"
                class="plan-option"
                :class="{ selected: selectedPlan === plan.name }"
              >
                <input type="radio" :value="plan.name" v-model="selectedPlan" />
                <div class="plan-option-content">
                  <span class="plan-option-name">{{ plan.name }}</span>
                  <span class="plan-option-price"
                    >₱{{ plan.amount.toLocaleString() }}</span
                  >
                </div>
              </label>
            </div>
            <div v-if="subError" class="sub-error">{{ subError }}</div>
            <div class="sub-action-btns">
              <button class="ghost-btn" @click="cancelSubAction">Cancel</button>
              <button class="primary-btn" @click="confirmChangePlan" :disabled="subProcessing">
                {{ subProcessing ? "Processing..." : "Confirm Change" }}
              </button>
            </div>
          </div>

          <div v-if="subAction === 'extend'" class="sub-action-area">
            <p class="sub-action-title">Extend Subscription</p>
            <p class="sub-action-desc">
              Add extra days to the current subscription period.
            </p>
            <div class="extend-input-group">
              <button
                class="extend-btn"
                @click="extendDays = Math.max(1, extendDays - 7)"
              >
                -7
              </button>
              <input
                type="number"
                v-model.number="extendDays"
                min="1"
                class="extend-input"
              />
              <button class="extend-btn" @click="extendDays += 7">+7</button>
            </div>
            <span class="extend-label">days</span>
            <div v-if="subError" class="sub-error">{{ subError }}</div>
            <div class="sub-action-btns">
              <button class="ghost-btn" @click="cancelSubAction">Cancel</button>
              <button class="primary-btn" @click="confirmExtend" :disabled="subProcessing">
                {{ subProcessing ? "Processing..." : "Extend" }}
              </button>
            </div>
          </div>

          <div v-if="subAction === 'cancel'" class="sub-action-area">
            <p class="sub-action-title">Cancel Subscription</p>
            <p class="sub-action-desc warning">
              This will mark the subscription as cancelled. The seller will lose
              access to premium features.
            </p>
            <div v-if="subError" class="sub-error">{{ subError }}</div>
            <div class="sub-action-btns">
              <button class="ghost-btn" @click="cancelSubAction">
                Keep Active
              </button>
              <button class="danger-btn" @click="confirmCancel" :disabled="subProcessing">
                {{ subProcessing ? "Processing..." : "Yes, Cancel" }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="tabs-card">
        <div class="tabs-header">
          <button
            class="tab"
            :class="{ active: activeTab === 'products' }"
            @click="activeTab = 'products'"
          >
            Products ({{ sellerProducts.length }})
          </button>
          <button
            class="tab"
            :class="{ active: activeTab === 'orders' }"
            @click="activeTab = 'orders'"
          >
            Recent Orders
          </button>
          <button
            class="tab"
            :class="{ active: activeTab === 'analytics' }"
            @click="activeTab = 'analytics'"
          >
            Analytics
          </button>
        </div>

        <div v-if="activeTab === 'products'" class="tab-content">
          <div v-if="sellerProducts.length === 0" class="empty-tab">No products found.</div>
          <div v-else class="table-card">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Unit</th>
                  <th>Stock</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in sellerProducts" :key="p.id">
                  <td>
                    <div class="cell-product">
                      <div class="product-thumb">{{ p.name.charAt(0) }}</div>
                      <span class="name-cell">{{ p.name }}</span>
                    </div>
                  </td>
                  <td>₱{{ p.price }}</td>
                  <td>{{ p.selling_unit }}</td>
                  <td>{{ p.stock_quantity }}</td>
                  <td>
                    <span
                      class="status-badge"
                      :class="p.stock_quantity > 0 ? 'in_stock' : 'out_of_stock'"
                      >{{ p.stock_quantity > 0 ? "In Stock" : "Out of Stock" }}</span
                    >
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="activeTab === 'orders'" class="tab-content">
          <div v-if="sellerOrders.length === 0" class="empty-tab">No orders found.</div>
          <div v-else class="table-card">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Items</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="o in sellerOrders" :key="o.id">
                  <td class="id-cell">#{{ o.id }}</td>
                  <td>{{ o.customer_name }}</td>
                  <td>{{ o.item_count }}</td>
                  <td>₱{{ o.total.toLocaleString() }}</td>
                  <td>
                    <span class="status-badge" :class="o.status">{{
                      o.status
                    }}</span>
                  </td>
                  <td class="date-cell">{{ o.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="activeTab === 'analytics'" class="tab-content">
          <div class="mini-charts">
            <div class="mini-card">
              <span class="mini-label">Monthly Revenue</span>
              <span class="mini-value"
                >₱{{ (seller.revenue / 12).toLocaleString() }}</span
              >
              <div class="mini-bar-bg">
                <div class="mini-bar-fill" style="width: 76%"></div>
              </div>
            </div>
            <div class="mini-card">
              <span class="mini-label">Avg Order Value</span>
              <span class="mini-value"
                >₱{{ Math.round(seller.revenue / 30).toLocaleString() }}</span
              >
              <div class="mini-bar-bg">
                <div class="mini-bar-fill" style="width: 62%"></div>
              </div>
            </div>
            <div class="mini-card">
              <span class="mini-label">Growth Rate</span>
              <span class="mini-value" style="color: #059669">+18.5%</span>
              <div class="mini-bar-bg">
                <div
                  class="mini-bar-fill"
                  style="width: 68%; background: #059669"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="not-found">
      <h2>Seller not found</h2>
      <p>The seller you're looking for doesn't exist.</p>
    </div>

    <div
      class="modal-overlay"
      v-if="showContact"
      @click.self="showContact = false"
    >
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">Contact {{ seller?.owner }}</span>
          <button class="modal-close" @click="showContact = false">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="contact-row">
            <span class="contact-label">Phone</span>
            <span class="contact-value">{{
              seller?.contact?.phone || "N/A"
            }}</span>
            <a :href="'tel:' + seller?.contact?.phone" class="contact-action"
              >Call</a
            >
          </div>
          <div class="contact-row">
            <span class="contact-label">Email</span>
            <span class="contact-value">{{
              seller?.contact?.email || "N/A"
            }}</span>
            <a :href="'mailto:' + seller?.contact?.email" class="contact-action"
              >Email</a
            >
          </div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="showContact = false">
            Close
          </button>
        </div>
      </div>
    </div>

    <div
      class="modal-overlay"
      v-if="showTopUp"
      @click.self="closeTopUp"
    >
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">Top Up Wallet — {{ seller?.name }}</span>
          <button class="modal-close" @click="closeTopUp">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="topup-section">
            <label class="topup-label">Amount to Add</label>
            <div class="topup-input-wrap">
              <span class="topup-currency">₱</span>
              <input type="number" v-model.number="topUpAmount" min="1" class="topup-input" placeholder="0" />
            </div>
          </div>
          <div v-if="topUpError" class="topup-error">{{ topUpError }}</div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="closeTopUp">Cancel</button>
          <button class="modal-btn primary" @click="confirmTopUp" :disabled="topUpping">
            {{ topUpping ? "Processing..." : "Add Balance" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });
const route = useRoute();
const sellersStore = useSellersStore();
const auth = useAuthStore();
const config = useRuntimeConfig();
const apiBase = config.public.apiBaseUrl || "http://localhost:8000";

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem("palengke_auth_token");
}

const loading = ref(true);
const loadError = ref(null);
const seller = ref(null);
const sellerProducts = ref([]);
const sellerOrders = ref([]);

function mapSeller(raw) {
  return {
    id: raw.id,
    name: raw.storeName || "",
    owner: [raw.firstName, raw.lastName].filter(Boolean).join(" ") || "—",
    category: raw.category || "",
    location: [raw.barangay, raw.city].filter(Boolean).join(", ") || raw.completeAddress || "",
    products: raw.productCount || 0,
    revenue: 0,
    rating: 0,
    status: raw.status === "approved" ? "active" : "inactive",
    joined: raw.submittedAt || "",
    contact: {
      phone: raw.contact || "",
      email: raw.email || "",
    },
    walletBalance: raw.walletBalance || 0,
    subscription: raw.subscription || { plan: "Trial", amount: 0, start: "", end: "", status: "trial" },
  };
}

async function fetchSeller() {
  const token = getToken();
  if (!token) { loading.value = false; return; }
  try {
    const res = await fetch(`${apiBase}/api/admin/sellers/${route.params.id}`, {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    if (!res.ok) throw new Error(`Failed to load seller (${res.status})`);
    const json = await res.json();
    seller.value = mapSeller(json.registration);
  } catch (e) {
    loadError.value = e.message;
  } finally {
    loading.value = false;
  }
}

async function fetchProducts() {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${apiBase}/api/admin/sellers/${route.params.id}/products`, {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    if (res.ok) {
      const json = await res.json();
      sellerProducts.value = json.products || [];
    }
  } catch {
    // silent
  }
}

async function fetchOrders() {
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${apiBase}/api/admin/sellers/${route.params.id}/orders`, {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    if (res.ok) {
      const json = await res.json();
      sellerOrders.value = json.orders || [];
    }
  } catch {
    // silent
  }
}

onMounted(async () => {
  await fetchSeller();
  if (seller.value) {
    await Promise.all([fetchProducts(), fetchOrders()]);
  }
});

const activeTab = ref("products");

const subAction = ref(null);
const selectedPlan = ref("");
const extendDays = ref(30);
const subProcessing = ref(false);
const subError = ref(null);

// Wallet top-up
const showTopUp = ref(false);
const topUpAmount = ref(100);
const topUpping = ref(false);
const topUpError = ref(null);

function openTopUpModal() {
  topUpAmount.value = 100;
  topUpError.value = null;
  showTopUp.value = true;
}

function closeTopUp() {
  showTopUp.value = false;
  topUpError.value = null;
  topUpping.value = false;
}

async function confirmTopUp() {
  if (!topUpAmount.value || topUpAmount.value < 1) {
    topUpError.value = "Enter a valid amount.";
    return;
  }
  topUpping.value = true;
  topUpError.value = null;
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${apiBase}/api/admin/sellers/${route.params.id}/wallet/top-up`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: "application/json" },
      body: JSON.stringify({
        amount: topUpAmount.value,
      }),
    });
    const json = await res.json();
    if (!res.ok) {
      topUpError.value = json.message || "Top up failed.";
      return;
    }
    if (seller.value) seller.value.walletBalance = json.balance;
    closeTopUp();
  } catch (e) {
    topUpError.value = e.message || "Network error.";
  } finally {
    topUpping.value = false;
  }
}

const toggleStatus = () => {
  if (seller.value) sellersStore.toggleStatus(seller.value.id);
};

const goBack = () => navigateTo("/sellers");

const showContact = ref(false);
function openContactModal() {
  showContact.value = true;
}

const planIdMap = {
  '1 Month': '1m',
  '2 Months': '2m',
  '4 Months': '4m',
  '8 Months': '8m',
};

function cancelSubAction() {
  subAction.value = null;
  selectedPlan.value = "";
  extendDays.value = 30;
  subError.value = null;
}

async function confirmChangePlan() {
  if (!seller.value || !selectedPlan.value) return;
  const planId = planIdMap[selectedPlan.value];
  if (!planId) return;
  subProcessing.value = true;
  subError.value = null;
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${apiBase}/api/admin/sellers/${route.params.id}/subscription/change`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ plan_id: planId }),
    });
    const json = await res.json();
    if (!res.ok) { subError.value = json.message || 'Failed to change plan.'; return; }
    if (seller.value) seller.value.subscription = json.subscription;
    cancelSubAction();
  } catch (e) {
    subError.value = e.message || 'Network error.';
  } finally {
    subProcessing.value = false;
  }
}

async function confirmExtend() {
  if (!seller.value || !extendDays.value || extendDays.value < 1) return;
  subProcessing.value = true;
  subError.value = null;
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${apiBase}/api/admin/sellers/${route.params.id}/subscription/extend`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ days: extendDays.value }),
    });
    const json = await res.json();
    if (!res.ok) { subError.value = json.message || 'Failed to extend.'; return; }
    if (seller.value) seller.value.subscription = json.subscription;
    cancelSubAction();
  } catch (e) {
    subError.value = e.message || 'Network error.';
  } finally {
    subProcessing.value = false;
  }
}

async function confirmCancel() {
  if (!seller.value) return;
  subProcessing.value = true;
  subError.value = null;
  const token = getToken();
  if (!token) return;
  try {
    const res = await fetch(`${apiBase}/api/admin/sellers/${route.params.id}/subscription/cancel`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
    });
    const json = await res.json();
    if (!res.ok) { subError.value = json.message || 'Failed to cancel.'; return; }
    if (seller.value) seller.value.subscription = json.subscription;
    cancelSubAction();
  } catch (e) {
    subError.value = e.message || 'Network error.';
  } finally {
    subProcessing.value = false;
  }
}
</script>

<style scoped>
.detail-page {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  font-size: 13px;
  font-weight: 500;
  color: #059669;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  padding: 0;
  width: fit-content;
}
.back-btn svg {
  width: 16px;
  height: 16px;
}
.back-btn:hover {
  color: #047857;
}
.detail-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.detail-header {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  border: 1px solid #e5e7eb;
}
.detail-avatar {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}
.detail-title {
  flex: 1;
}
.detail-title h1 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}
.detail-subtitle {
  font-size: 13px;
  color: #9ca3af;
}
.detail-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}
.status-badge-lg {
  padding: 5px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
}
.status-badge-lg.active {
  background: #f0fdf4;
  color: #059669;
}
.status-badge-lg.inactive {
  background: #f3f4f6;
  color: #6b7280;
}
.action-btn {
  padding: 8px 16px;
  border-radius: 8px;
  border: 1.5px solid #e5e7eb;
  background: #fff;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  color: #374151;
}
.action-btn:hover {
  border-color: #9ca3af;
}
.action-btn.suspended {
  border-color: #fecaca;
  color: #dc2626;
}
.action-btn.suspended:hover {
  background: #fef2f2;
}
.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.info-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #e5e7eb;
}
.info-card h3 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 16px;
}
.info-rows {
  display: flex;
  flex-direction: column;
}
.info-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #f9fafb;
  font-size: 13px;
}
.info-row:last-child {
  border-bottom: none;
}
.info-label {
  color: #9ca3af;
  font-weight: 500;
}
.info-value {
  color: #111827;
  font-weight: 600;
}
.info-value.stars {
  color: #f59e0b;
}
.info-card-actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
}
.ghost-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
  font-size: 12px;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  font-family: "Inter", sans-serif;
}
.ghost-btn:hover {
  border-color: #059669;
  color: #059669;
}
.ghost-btn svg {
  width: 14px;
  height: 14px;
}
.tabs-card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
}
.tabs-header {
  display: flex;
  border-bottom: 1px solid #e5e7eb;
}
.tab {
  padding: 12px 20px;
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  background: none;
  border: none;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  border-bottom: 2px solid transparent;
  transition: all 0.15s;
}
.tab:hover {
  color: #374151;
}
.tab.active {
  color: #059669;
  border-bottom-color: #059669;
  font-weight: 600;
}
.tab-content {
  padding: 16px;
}
.empty-tab {
  text-align: center;
  padding: 40px 16px;
  font-size: 13px;
  color: #9ca3af;
}
.table-card {
  overflow: hidden;
}
.data-table {
  width: 100%;
  border-collapse: collapse;
}
.data-table th {
  text-align: left;
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 10px 12px;
  border-bottom: 1px solid #f3f4f6;
}
.data-table td {
  padding: 12px;
  font-size: 13px;
  color: #374151;
  border-bottom: 1px solid #f9fafb;
}
.cell-product {
  display: flex;
  align-items: center;
  gap: 10px;
}
.product-thumb {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  color: #6b7280;
}
.name-cell {
  font-weight: 600;
  color: #111827;
}
.id-cell {
  font-weight: 600;
  color: #059669;
}
.date-cell {
  color: #9ca3af;
}
.status-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.status-badge.delivered {
  background: #f0fdf4;
  color: #059669;
}
.status-badge.transit {
  background: #eff6ff;
  color: #3b82f6;
}
.status-badge.cancelled {
  background: #fef2f2;
  color: #dc2626;
}
.status-badge.in_stock {
  background: #f0fdf4;
  color: #059669;
}
.status-badge.out_of_stock {
  background: #fef2f2;
  color: #dc2626;
}
.mini-charts {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}
.mini-card {
  padding: 16px;
  background: #f9fafb;
  border-radius: 10px;
}
.mini-label {
  display: block;
  font-size: 12px;
  color: #9ca3af;
  margin-bottom: 4px;
}
.mini-value {
  display: block;
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 20px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 12px;
}
.mini-bar-bg {
  height: 6px;
  background: #e5e7eb;
  border-radius: 3px;
  overflow: hidden;
}
.mini-bar-fill {
  height: 100%;
  background: #3b82f6;
  border-radius: 3px;
}
.loading-state {
  text-align: center;
  padding: 60px 20px;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  font-size: 14px;
  color: #6b7280;
}
.loading-state.error {
  color: #dc2626;
}
.not-found {
  text-align: center;
  padding: 60px 20px;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}
.not-found h2 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 20px;
  color: #111827;
  margin-bottom: 8px;
}
.not-found p {
  color: #9ca3af;
}

.wallet-balance {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px 0 8px;
}
.wallet-amount {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 32px;
  font-weight: 700;
  color: #059669;
}
.wallet-label {
  font-size: 12px;
  color: #9ca3af;
  margin-top: 4px;
}
.topup-section {
  margin-bottom: 20px;
}
.topup-label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 8px;
}
.topup-input-wrap {
  display: flex;
  align-items: center;
  gap: 4px;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  padding: 4px 16px;
  transition: border-color 0.15s;
}
.topup-input-wrap:focus-within {
  border-color: #059669;
}
.topup-currency {
  font-size: 20px;
  font-weight: 700;
  color: #9ca3af;
}
.topup-input {
  flex: 1;
  border: none;
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 24px;
  font-weight: 700;
  color: #111827;
  outline: none;
  padding: 8px 0;
  width: 100%;
}
.topup-error {
  font-size: 13px;
  color: #dc2626;
  background: #fef2f2;
  padding: 10px 14px;
  border-radius: 8px;
  margin-bottom: 4px;
}
.sub-card {
  grid-column: 1 / -1;
}
.sub-card .status-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.sub-card .status-badge.active {
  background: #f0fdf4;
  color: #059669;
}
.sub-card .status-badge.trial {
  background: #eff6ff;
  color: #3b82f6;
}
.sub-card .status-badge.expired {
  background: #fef2f2;
  color: #dc2626;
}
.sub-card .status-badge.cancelled {
  background: #f3f4f6;
  color: #6b7280;
}
.sub-action-area {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f3f4f6;
}
.sub-action-title {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  margin: 0 0 4px;
}
.sub-action-desc {
  font-size: 12px;
  color: #9ca3af;
  margin: 0 0 12px;
}
.sub-action-desc.warning {
  color: #dc2626;
}
.plan-options {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 12px;
}
.plan-option {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.15s;
  font-size: 13px;
}
.plan-option:hover {
  border-color: #059669;
}
.plan-option.selected {
  border-color: #059669;
  background: #f0fdf4;
}
.plan-option input {
  accent-color: #059669;
}
.plan-option-content {
  display: flex;
  justify-content: space-between;
  flex: 1;
}
.plan-option-name {
  font-weight: 600;
  color: #111827;
}
.plan-option-price {
  color: #059669;
  font-weight: 700;
}
.sub-action-btns {
  display: flex;
  gap: 8px;
  margin-top: 8px;
}
.primary-btn {
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  background: #059669;
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: "Inter", sans-serif;
}
.primary-btn:hover {
  background: #047857;
}
.danger-btn {
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  background: #dc2626;
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: "Inter", sans-serif;
}
.danger-btn:hover {
  background: #b91c1c;
}
.ghost-btn.danger {
  border-color: #fecaca;
  color: #dc2626;
}
.ghost-btn.danger:hover {
  background: #fef2f2;
}
.extend-input-group {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}
.extend-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: 1.5px solid #e5e7eb;
  background: #fff;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  font-family: "Inter", sans-serif;
}
.extend-btn:hover {
  border-color: #059669;
  color: #059669;
}
.extend-input {
  width: 80px;
  height: 36px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  text-align: center;
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  font-family: "Inter", sans-serif;
  outline: none;
}
.extend-input:focus {
  border-color: #059669;
}
.extend-label {
  display: block;
  font-size: 12px;
  color: #9ca3af;
  margin-bottom: 8px;
}
.sub-error {
  font-size: 13px;
  color: #dc2626;
  background: #fef2f2;
  padding: 10px 14px;
  border-radius: 8px;
  margin-bottom: 4px;
}
.contact-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f9fafb;
}
.contact-row:last-child {
  border-bottom: none;
}
.contact-label {
  font-size: 12px;
  font-weight: 600;
  color: #9ca3af;
  min-width: 60px;
}
.contact-value {
  flex: 1;
  font-size: 13px;
  color: #111827;
  font-weight: 600;
}
.contact-action {
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  background: #f0fdf4;
  color: #059669;
  cursor: pointer;
}
.contact-action:hover {
  background: #059669;
  color: #fff;
}
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 32px;
  backdrop-filter: blur(2px);
}
.modal-card {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 400px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}
.modal-card.modal-sm {
  max-width: 400px;
}
.modal-top {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px 24px;
  border-bottom: 1px solid #f3f4f6;
}
.modal-title {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  flex: 1;
}
.modal-close {
  width: 32px;
  height: 32px;
  border: none;
  background: none;
  cursor: pointer;
  color: #9ca3af;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.modal-close:hover {
  background: #f3f4f6;
  color: #374151;
}
.modal-close svg {
  width: 18px;
  height: 18px;
}
.modal-body {
  padding: 20px 24px;
}
.modal-actions {
  display: flex;
  gap: 10px;
  padding: 16px 24px 20px;
  border-top: 1px solid #f3f4f6;
}
.modal-btn {
  flex: 1;
  padding: 11px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  font-family: "Inter", sans-serif;
  cursor: pointer;
  transition: all 0.15s;
  border: none;
}
.modal-btn.primary {
  background: #059669;
  color: #fff;
}
.modal-btn.primary:hover {
  background: #047857;
}
.modal-btn.secondary {
  background: #f3f4f6;
  color: #6b7280;
}
.modal-btn.secondary:hover {
  background: #e5e7eb;
}

@media (max-width: 768px) {
  .detail-grid {
    grid-template-columns: 1fr;
  }
  .mini-charts {
    grid-template-columns: 1fr;
  }
  .detail-header {
    flex-wrap: wrap;
  }
}
</style>
