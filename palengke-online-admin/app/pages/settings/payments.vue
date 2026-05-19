<template>
  <div class="page-shell">
    <div class="settings-subnav">
      <NuxtLink
        to="/settings"
        class="subnav-item"
        active-class="active"
        exact-active-class="active"
        >General</NuxtLink
      >
      <NuxtLink to="/settings/fees" class="subnav-item" active-class="active"
        >Delivery Fees</NuxtLink
      >
      <NuxtLink
        to="/settings/commissions"
        class="subnav-item"
        active-class="active"
        >Commissions</NuxtLink
      >
      <NuxtLink
        to="/settings/payments"
        class="subnav-item"
        active-class="active"
        >Payments</NuxtLink
      >
    </div>

    <div class="settings-section">
      <h3>Payment Methods</h3>
      <p class="settings-desc">
        Toggle visibility and "Coming Soon" status for each payment method.
      </p>

      <div class="payment-methods-list">
        <div v-for="pm in methods" :key="pm.id" class="payment-method-card">
          <div
            class="pm-icon"
            :style="{ background: pm.color + '20', color: pm.color }"
          >
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              v-if="pm.id === 'gcash'"
              style="width: 22px; height: 22px"
            >
              <rect x="2" y="4" width="20" height="16" rx="2" />
              <line x1="8" y1="12" x2="16" y2="12" />
            </svg>
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              v-else-if="pm.id === 'paypal'"
              style="width: 22px; height: 22px"
            >
              <path d="M7 11l2 10h4l-1-5h3c3 0 5-2 5-5s-2-5-5-5H7z" />
            </svg>
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              v-else-if="pm.id === 'stripe'"
              style="width: 22px; height: 22px"
            >
              <path d="M12 2v20M2 12h20" />
            </svg>
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              v-else
              style="width: 22px; height: 22px"
            >
              <circle cx="12" cy="12" r="10" />
              <path d="M8 14s1.5 2 4 2 4-2 4-2" />
              <line x1="9" y1="9" x2="9.01" y2="9" />
              <line x1="15" y1="9" x2="15.01" y2="9" />
            </svg>
          </div>
          <div class="pm-info">
            <span class="pm-name">{{ pm.name }}</span>
            <span class="pm-desc">{{ pm.desc }}</span>
          </div>
          <div class="pm-toggles">
            <div class="toggle-label-wrap">
              <span class="toggle-text">Visible</span>
              <label class="toggle-label">
                <input type="checkbox" v-model="pm.visible" @change="save" />
                <span class="toggle-slider"></span>
              </label>
            </div>
            <div class="toggle-label-wrap">
              <span class="toggle-text coming-soon-text">Coming Soon</span>
              <label class="toggle-label">
                <input type="checkbox" v-model="pm.comingSoon" @change="save" />
                <span class="toggle-slider"></span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="save-bar">
        <span class="save-hint">Changes are saved automatically.</span>
        <span class="save-toast" v-if="saved">✓ Saved</span>
      </div>
    </div>

    <div class="settings-section">
      <h3>Preview</h3>
      <p class="settings-desc">How payment methods will appear to customers:</p>
      <div class="preview-payments">
        <div
          v-for="pm in visibleMethods"
          :key="pm.id"
          class="preview-chip"
          :style="{
            background: pm.color + '20',
            color: pm.color,
            borderColor: pm.color + '40',
          }"
        >
          {{ pm.name }}
          <span v-if="pm.comingSoon" class="coming-soon-tag">Coming Soon</span>
        </div>
        <span class="no-methods" v-if="visibleMethods.length === 0"
          >No payment methods visible</span
        >
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });

const KEY = "palengke_payment_methods";

const defaults = [
  {
    id: "gcash",
    name: "GCash",
    desc: "Mobile wallet payments",
    color: "#059669",
    visible: true,
    comingSoon: false,
  },
  {
    id: "paypal",
    name: "PayPal",
    desc: "Pay with PayPal account",
    color: "#2563eb",
    visible: true,
    comingSoon: true,
  },
  {
    id: "stripe",
    name: "Stripe",
    desc: "Credit/debit card payments",
    color: "#7c3aed",
    visible: true,
    comingSoon: true,
  },
  {
    id: "cod",
    name: "Cash on Delivery",
    desc: "Pay when order arrives",
    color: "#d97706",
    visible: true,
    comingSoon: false,
  },
];

const saved = ref(false);
const methods = ref([]);

function load() {
  const raw = localStorage.getItem(KEY);
  if (raw) {
    methods.value = JSON.parse(raw);
  } else {
    methods.value = JSON.parse(JSON.stringify(defaults));
    save();
  }
}

function save() {
  localStorage.setItem(KEY, JSON.stringify(methods.value));
  saved.value = true;
  setTimeout(() => {
    saved.value = false;
  }, 2000);
}

const visibleMethods = computed(() => methods.value.filter((m) => m.visible));

load();
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.settings-subnav {
  display: flex;
  gap: 4px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 4px;
  overflow-x: auto;
}
.subnav-item {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.15s;
  font-family: "Inter", sans-serif;
}
.subnav-item:hover {
  color: #374151;
  background: #f3f4f6;
}
.subnav-item.active {
  background: #059669;
  color: #fff;
}
.settings-section {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 24px;
}
.settings-section h3 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 4px;
}
.settings-desc {
  font-size: 13px;
  color: #9ca3af;
  margin: 0 0 20px;
}

.payment-methods-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.payment-method-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  transition: box-shadow 0.15s;
}
.payment-method-card:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
.pm-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.pm-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.pm-name {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}
.pm-desc {
  font-size: 12px;
  color: #9ca3af;
}
.pm-toggles {
  display: flex;
  gap: 20px;
  align-items: center;
  flex-shrink: 0;
}
.toggle-label-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
}
.toggle-text {
  font-size: 12px;
  color: #6b7280;
  white-space: nowrap;
}
.coming-soon-text {
  font-size: 11px;
}
.toggle-label {
  position: relative;
  display: inline-block;
  width: 36px;
  height: 20px;
  cursor: pointer;
  flex-shrink: 0;
}
.toggle-label input {
  display: none;
}
.toggle-slider {
  position: absolute;
  inset: 0;
  background: #d1d5db;
  border-radius: 20px;
  transition: 0.2s;
}
.toggle-slider::before {
  content: "";
  position: absolute;
  left: 2px;
  bottom: 2px;
  width: 16px;
  height: 16px;
  background: #fff;
  border-radius: 50%;
  transition: 0.2s;
}
.toggle-label input:checked + .toggle-slider {
  background: #059669;
}
.toggle-label input:checked + .toggle-slider::before {
  transform: translateX(16px);
}

.save-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f3f4f6;
}
.save-hint {
  font-size: 12px;
  color: #9ca3af;
}
.save-toast {
  font-size: 13px;
  font-weight: 600;
  color: #059669;
  animation: fadeIn 0.2s;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.preview-payments {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.preview-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  border: 1.5px solid;
}
.coming-soon-tag {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  opacity: 0.7;
}
.no-methods {
  font-size: 13px;
  color: #9ca3af;
}

@media (max-width: 600px) {
  .payment-method-card {
    flex-wrap: wrap;
  }
  .pm-toggles {
    width: 100%;
    justify-content: flex-end;
    padding-top: 8px;
    border-top: 1px solid #f3f4f6;
  }
}
</style>
