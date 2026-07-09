<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div v-if="isSeller" class="role-banner seller-banner">
      <span class="role-banner-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span> Seller Delivery Fees
    </div>
    <div v-if="isDriver" class="role-banner driver-banner">
      <span class="role-banner-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="16" r="3"/><circle cx="18" cy="16" r="3"/><path d="M9 16h9"/><path d="M4 9h5l2 4"/><path d="M18 5l-3 2v9"/><rect x="13" y="6" width="5" height="4" rx="1"/></svg></span> Driver Delivery Fees
    </div>

    <div class="settings-grid">
      <div class="settings-section">
        <h3>Delivery Fee Configuration</h3>
        <div class="settings-form">
          <div class="form-row toggle-row">
            <div class="form-group toggle-group">
              <label class="toggle-label">
                <span>Enable Express Delivery</span>
                <label class="switch">
                  <input type="checkbox" v-model="expressEnabled" />
                  <span class="slider"></span>
                </label>
              </label>
              <span class="form-hint">When disabled, Express Delivery is hidden from the driver app</span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" :class="{ hasError: errors.base }">
              <label>Base Delivery Fee (₱) <span class="req">*</span></label>
              <input
                v-model="fees.base"
                type="number"
                min="0"
                class="form-input"
                required
              />
              <span class="form-hint">Standard fee for orders within 3km</span>
              <span class="form-error" v-if="errors.base">{{
                errors.base
              }}</span>
            </div>
            <div class="form-group" :class="{ hasError: errors.perKm }">
              <label>Per Kilometer Rate (₱) <span class="req">*</span></label>
              <input
                v-model="fees.perKm"
                type="number"
                step="0.5"
                min="0"
                class="form-input"
                required
              />
              <span class="form-hint"
                >Additional charge per km beyond base</span
              >
              <span class="form-error" v-if="errors.perKm">{{
                errors.perKm
              }}</span>
            </div>
          </div>
          <div class="form-row">
            <div
              class="form-group"
              :class="{ hasError: errors.expressMultiplier }"
            >
              <label>Express Multiplier (×) <span class="req">*</span></label>
              <input
                v-model="fees.expressMultiplier"
                type="number"
                step="0.1"
                min="1"
                class="form-input"
                required
              />
              <span class="form-hint"
                >Express delivery = base × multiplier</span
              >
              <span class="form-error" v-if="errors.expressMultiplier">{{
                errors.expressMultiplier
              }}</span>
            </div>
            <div class="form-group" :class="{ hasError: errors.freeThreshold }">
              <label
                >Free Delivery Threshold (₱) <span class="req">*</span></label
              >
              <input
                v-model="fees.freeThreshold"
                type="number"
                min="0"
                class="form-input"
                required
              />
              <span class="form-hint"
                >Orders above this amount get free delivery</span
              >
              <span class="form-error" v-if="errors.freeThreshold">{{
                errors.freeThreshold
              }}</span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" :class="{ hasError: errors.maxRadius }">
              <label>Max Delivery Radius (km) <span class="req">*</span></label>
              <input
                v-model="fees.maxRadius"
                type="number"
                min="0"
                class="form-input"
                required
              />
              <span class="form-error" v-if="errors.maxRadius">{{
                errors.maxRadius
              }}</span>
            </div>
            <div
              class="form-group"
              :class="{ hasError: errors.nightSurcharge }"
            >
              <label>Night Surcharge (₱) <span class="req">*</span></label>
              <input
                v-model="fees.nightSurcharge"
                type="number"
                min="0"
                class="form-input"
                required
              />
              <span class="form-hint"
                >Additional fee for 10PM — 6AM deliveries</span
              >
              <span class="form-error" v-if="errors.nightSurcharge">{{
                errors.nightSurcharge
              }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Fee Preview</h3>
        <div class="preview-grid">
          <div class="preview-card">
            <span class="preview-label">Standard (3km)</span>
            <span class="preview-value">₱{{ fees.base }}</span>
          </div>
          <div class="preview-card">
            <span class="preview-label">Standard (5km)</span>
            <span class="preview-value"
              >₱{{ (fees.base + fees.perKm * 2).toFixed(0) }}</span
            >
          </div>
          <div v-if="expressEnabled" class="preview-card">
            <span class="preview-label">Express (3km)</span>
            <span class="preview-value"
              >₱{{ (fees.base * fees.expressMultiplier).toFixed(0) }}</span
            >
          </div>
          <div class="preview-card">
            <span class="preview-label">Free Delivery</span>
            <span class="preview-value">₱0</span>
          </div>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">
      ✓ Delivery fees saved successfully
    </div>

    <div class="settings-actions">
      <button class="save-btn" :disabled="saving" @click="saveFees">
        <span v-if="saving" class="spinner"></span>
        {{ saving ? 'Saving...' : 'Save Changes' }}
      </button>
      <button class="reset-btn" @click="resetFees">Reset to Default</button>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "default",
});

const auth = useAuthStore();
const { isDriver, isSeller } = useSettingsRole();
auth.restore();
onMounted(() => {
  if (auth.user?.role !== "superadmin") {
    navigateTo("/dashboard");
  }
});

const STORAGE_KEY = "palengke_settings_fees";

const defaults = {
  base: 45,
  perKm: 10,
  expressMultiplier: 1.8,
  freeThreshold: 500,
  maxRadius: 15,
  nightSurcharge: 25,
};

const FEE_KEYS = {
  base: 'delivery_base_fee',
  perKm: 'delivery_per_km',
  expressMultiplier: 'delivery_express_multiplier',
  freeThreshold: 'delivery_free_threshold',
  maxRadius: 'delivery_max_radius',
  nightSurcharge: 'delivery_night_surcharge',
};

function loadFees() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) return JSON.parse(saved);
  } catch {}
  return { ...defaults };
}

const fees = ref(loadFees());
const expressEnabled = ref(false);
const success = ref(false);
const saving = ref(false);
const loading = ref(true);

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem("palengke_auth_token");
}

onMounted(async () => {
  try {
    const res = await fetch(`${getApiBaseUrl()}/api/admin/settings`, {
      headers: { Authorization: `Bearer ${getToken()}` },
    });
    if (res.ok) {
      const data = await res.json();
      expressEnabled.value = data.express_delivery_enabled === "true";
      const backendFees = {};
      let hasFeeData = false;
      for (const [field, key] of Object.entries(FEE_KEYS)) {
        if (data[key] !== undefined && data[key] !== null) {
          backendFees[field] = parseFloat(data[key]);
          hasFeeData = true;
        }
      }
      if (hasFeeData) {
        fees.value = { ...defaults, ...backendFees };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(fees.value));
      }
    }
  } catch {}
  loading.value = false;
});

const errors = reactive({
  base: "",
  perKm: "",
  expressMultiplier: "",
  freeThreshold: "",
  maxRadius: "",
  nightSurcharge: "",
});

function validate() {
  let valid = true;
  Object.keys(errors).forEach((k) => (errors[k] = ""));

  if (fees.value.base === "" || fees.value.base == null) {
    errors.base = "Base delivery fee is required";
    valid = false;
  } else if (fees.value.base < 0) {
    errors.base = "Cannot be negative";
    valid = false;
  }

  if (fees.value.perKm === "" || fees.value.perKm == null) {
    errors.perKm = "Per km rate is required";
    valid = false;
  } else if (fees.value.perKm < 0) {
    errors.perKm = "Cannot be negative";
    valid = false;
  }

  if (
    fees.value.expressMultiplier === "" ||
    fees.value.expressMultiplier == null
  ) {
    errors.expressMultiplier = "Express multiplier is required";
    valid = false;
  } else if (fees.value.expressMultiplier < 1) {
    errors.expressMultiplier = "Must be at least 1";
    valid = false;
  }

  if (fees.value.freeThreshold === "" || fees.value.freeThreshold == null) {
    errors.freeThreshold = "Free delivery threshold is required";
    valid = false;
  } else if (fees.value.freeThreshold < 0) {
    errors.freeThreshold = "Cannot be negative";
    valid = false;
  }

  if (fees.value.maxRadius === "" || fees.value.maxRadius == null) {
    errors.maxRadius = "Max delivery radius is required";
    valid = false;
  } else if (fees.value.maxRadius < 0) {
    errors.maxRadius = "Cannot be negative";
    valid = false;
  }

  if (fees.value.nightSurcharge === "" || fees.value.nightSurcharge == null) {
    errors.nightSurcharge = "Night surcharge is required";
    valid = false;
  } else if (fees.value.nightSurcharge < 0) {
    errors.nightSurcharge = "Cannot be negative";
    valid = false;
  }

  return valid;
}

const saveFees = async () => {
  if (!validate()) return;
  saving.value = true;
  localStorage.setItem(STORAGE_KEY, JSON.stringify(fees.value));
  try {
    const settings = {
      express_delivery_enabled: expressEnabled.value ? "true" : "false",
    };
    for (const [field, key] of Object.entries(FEE_KEYS)) {
      settings[key] = String(fees.value[field]);
    }
    await fetch(`${getApiBaseUrl()}/api/admin/settings`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${getToken()}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ settings }),
    });
  } catch {}
  saving.value = false;
  success.value = true;
  setTimeout(() => {
    success.value = false;
  }, 2500);
};

const resetFees = () => {
  fees.value = { ...defaults };
  Object.keys(errors).forEach((k) => (errors[k] = ""));
  saveFees();
};
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 900px;
}
.settings-subnav {
  display: flex;
  gap: 4px;
  background: #fff;
  border-radius: 12px;
  padding: 6px;
  border: 1px solid #e5e7eb;
}
.subnav-item {
  padding: 9px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.15s;
}
.subnav-item:hover {
  color: #374151;
  background: #f9fafb;
}
.subnav-item.active {
  background: #f0fdf4;
  color: #059669;
  font-weight: 600;
}
.settings-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.role-banner {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
}
.role-banner-icon {
  display: inline-flex;
  align-items: center;
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}
.role-banner-icon svg {
  width: 100%;
  height: 100%;
}
.seller-banner {
  background: #f0fdf4;
  color: #059669;
  border: 1px solid #bbf7d0;
}
.driver-banner {
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
}
.settings-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  border: 1px solid #e5e7eb;
}
.settings-section h3 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 15px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 20px;
}
.settings-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.form-group label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
}
.form-input {
  padding: 10px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #111827;
  background: #fff;
  outline: none;
  transition: border-color 0.2s;
}
.form-input:focus {
  border-color: #059669;
}
.form-hint {
  font-size: 11px;
  color: #9ca3af;
}
.req {
  color: #dc2626;
}
.form-group.hasError .form-input {
  border-color: #dc2626;
  background: #fef2f2;
}
.form-error {
  font-size: 11px;
  color: #dc2626;
  font-weight: 500;
}
.preview-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}
.preview-card {
  padding: 16px;
  background: #f9fafb;
  border-radius: 10px;
  text-align: center;
}
.preview-label {
  display: block;
  font-size: 12px;
  color: #9ca3af;
  margin-bottom: 6px;
}
.preview-value {
  display: block;
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: #111827;
}
.settings-actions {
  display: flex;
  gap: 12px;
}
.save-toast {
  padding: 10px 16px;
  background: #f0fdf4;
  color: #059669;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}
.save-btn {
  padding: 12px 24px;
  background: #059669;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  transition: all 0.15s;
  display: inline-flex; align-items: center; gap: 8px;
}
.save-btn:hover:not(:disabled) {
  background: #047857;
}
.save-btn:disabled {
  opacity: 0.6; cursor: not-allowed;
}
.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.reset-btn {
  padding: 12px 24px;
  background: #fff;
  color: #374151;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  transition: all 0.15s;
}
.reset-btn:hover {
  border-color: #9ca3af;
}
.toggle-row { display: flex; }
.toggle-group { width: 100%; }
.toggle-label {
  display: flex; align-items: center; justify-content: space-between;
  font-size: 14px; font-weight: 600; color: #374151; cursor: pointer;
}
.switch {
  position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
  position: absolute; cursor: pointer; inset: 0;
  background: #e5e7eb; border-radius: 24px; transition: 0.2s;
}
.slider::before {
  content: ""; position: absolute; height: 18px; width: 18px;
  left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.2s;
}
.switch input:checked + .slider { background: #059669; }
.switch input:checked + .slider::before { transform: translateX(20px); }

@media (max-width: 600px) {
  .form-row,
  .preview-grid {
    grid-template-columns: 1fr;
  }
}
</style>
