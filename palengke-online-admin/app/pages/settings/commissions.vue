<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div v-if="isSeller" class="role-banner seller-banner">
      <span class="role-banner-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span> Seller Commission Settings
    </div>
    <div v-if="isDriver" class="role-banner driver-banner">
      <span class="role-banner-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="16" r="3"/><circle cx="18" cy="16" r="3"/><path d="M9 16h9"/><path d="M4 9h5l2 4"/><path d="M18 5l-3 2v9"/><rect x="13" y="6" width="5" height="4" rx="1"/></svg></span> Driver Commission Settings
    </div>

    <div class="settings-grid">
      <div v-if="isSeller" class="settings-section">
        <h3>Platform &amp; Seller Commission</h3>
        <div class="settings-form">
          <div class="form-row">
            <div class="form-group" :class="{ hasError: errors.platform }">
              <label>Platform Commission (%) <span class="req">*</span></label>
              <input
                v-model="comm.platform"
                type="number"
                step="0.5"
                min="0"
                max="100"
                class="form-input"
                required
              />
              <span class="form-hint"
                >Percentage deducted from each order total</span
              >
              <span class="form-error" v-if="errors.platform">{{
                errors.platform
              }}</span>
            </div>
            <div class="form-group">
              <label>Seller Payout Rate (%)</label>
              <input
                :value="100 - comm.platform"
                type="number"
                class="form-input"
                disabled
              />
              <span class="form-hint"
                >Auto-calculated: 100% − platform commission</span
              >
            </div>
          </div>
        </div>
      </div>

      <div v-if="isDriver" class="settings-section">
        <h3>Driver Commission</h3>
        <div class="settings-form">
          <div class="form-row">
            <div
              class="form-group"
              :class="{ hasError: errors.driverPerDelivery }"
            >
              <label
                >Driver Commission per Delivery (₱)
                <span class="req">*</span></label
              >
              <input
                v-model="comm.driverPerDelivery"
                type="number"
                min="0"
                class="form-input"
                required
              />
              <span class="form-hint"
                >Fixed amount paid to driver per completed delivery</span
              >
              <span class="form-error" v-if="errors.driverPerDelivery">{{
                errors.driverPerDelivery
              }}</span>
            </div>
            <div class="form-group">
              <label>Driver Commission per km (₱)</label>
              <input
                v-model="comm.driverPerKm"
                type="number"
                step="0.5"
                min="0"
                class="form-input"
              />
              <span class="form-hint">Additional amount per km traveled</span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="isSeller" class="settings-section">
        <h3>Payout Settings</h3>
        <div class="settings-form">
          <div class="form-row">
            <div class="form-group" :class="{ hasError: errors.minPayout }">
              <label
                >Minimum Payout Amount (₱) <span class="req">*</span></label
              >
              <input
                v-model="comm.minPayout"
                type="number"
                min="0"
                class="form-input"
                required
              />
              <span class="form-hint"
                >Minimum balance before seller can request payout</span
              >
              <span class="form-error" v-if="errors.minPayout">{{
                errors.minPayout
              }}</span>
            </div>
            <div class="form-group" :class="{ hasError: errors.schedule }">
              <label>Payout Schedule <span class="req">*</span></label>
              <select v-model="comm.schedule" class="form-input" required>
                <option value="">Select schedule...</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="biweekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
              <span class="form-error" v-if="errors.schedule">{{
                errors.schedule
              }}</span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" :class="{ hasError: errors.processingFee }">
              <label
                >Payout Processing Fee (₱) <span class="req">*</span></label
              >
              <input
                v-model="comm.processingFee"
                type="number"
                min="0"
                class="form-input"
                required
              />
              <span class="form-hint">Fee charged per payout transaction</span>
              <span class="form-error" v-if="errors.processingFee">{{
                errors.processingFee
              }}</span>
            </div>
            <div class="form-group">
              <label>Referral Bonus (₱)</label>
              <input
                v-model="comm.referralBonus"
                type="number"
                min="0"
                class="form-input"
              />
              <span class="form-hint">Bonus for referring new sellers</span>
            </div>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Commission Breakdown Example</h3>
        <div class="example-box">
          <p class="example-intro">For a <strong>₱500</strong> order:</p>
          <div class="example-row">
            <span>Platform Commission ({{ comm.platform }}%)</span
            ><span>— ₱{{ ((500 * comm.platform) / 100).toFixed(2) }}</span>
          </div>
          <div class="example-row">
            <span>Driver Pay (base)</span
            ><span>— ₱{{ comm.driverPerDelivery }}</span>
          </div>
          <div class="example-row highlight">
            <span>Seller Payout</span
            ><span>₱{{ (500 - (500 * comm.platform) / 100).toFixed(2) }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">
      ✓ Commission settings saved successfully
    </div>

    <div class="settings-actions">
      <button class="save-btn" @click="saveComm">Save Changes</button>
      <button class="reset-btn" @click="resetComm">Reset to Default</button>
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

const STORAGE_KEY = "palengke_settings_commissions";

const defaults = {
  platform: 10,
  driverPerDelivery: 25,
  driverPerKm: 5,
  minPayout: 500,
  schedule: "weekly",
  processingFee: 15,
  referralBonus: 100,
};

function loadComm() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) return JSON.parse(saved);
  } catch {}
  return { ...defaults };
}

const comm = ref(loadComm());
const success = ref(false);

const errors = reactive({
  platform: "",
  driverPerDelivery: "",
  minPayout: "",
  schedule: "",
  processingFee: "",
});

function validate() {
  let valid = true;
  errors.platform = "";
  errors.driverPerDelivery = "";
  errors.minPayout = "";
  errors.schedule = "";
  errors.processingFee = "";

  if (comm.value.platform === "" || comm.value.platform == null) {
    errors.platform = "Platform commission is required";
    valid = false;
  } else if (comm.value.platform < 0 || comm.value.platform > 100) {
    errors.platform = "Must be between 0 and 100";
    valid = false;
  }

  if (
    comm.value.driverPerDelivery === "" ||
    comm.value.driverPerDelivery == null
  ) {
    errors.driverPerDelivery = "Driver commission is required";
    valid = false;
  } else if (comm.value.driverPerDelivery < 0) {
    errors.driverPerDelivery = "Cannot be negative";
    valid = false;
  }

  if (comm.value.minPayout === "" || comm.value.minPayout == null) {
    errors.minPayout = "Minimum payout is required";
    valid = false;
  } else if (comm.value.minPayout < 0) {
    errors.minPayout = "Cannot be negative";
    valid = false;
  }

  if (!comm.value.schedule) {
    errors.schedule = "Please select a payout schedule";
    valid = false;
  }

  if (comm.value.processingFee === "" || comm.value.processingFee == null) {
    errors.processingFee = "Processing fee is required";
    valid = false;
  } else if (comm.value.processingFee < 0) {
    errors.processingFee = "Cannot be negative";
    valid = false;
  }

  return valid;
}

const saveComm = () => {
  if (!validate()) return;
  localStorage.setItem(STORAGE_KEY, JSON.stringify(comm.value));
  success.value = true;
  setTimeout(() => {
    success.value = false;
  }, 2500);
};

const resetComm = () => {
  comm.value = { ...defaults };
  Object.keys(errors).forEach((k) => (errors[k] = ""));
  saveComm();
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
.form-input:disabled {
  background: #f9fafb;
  color: #9ca3af;
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
.example-box {
  background: #f9fafb;
  border-radius: 10px;
  padding: 16px;
}
.example-intro {
  font-size: 13px;
  color: #6b7280;
  margin-bottom: 12px;
}
.example-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  font-size: 13px;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}
.example-row:last-child {
  border-bottom: none;
}
.example-row.highlight {
  font-weight: 700;
  color: #059669;
  font-size: 15px;
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
}
.save-btn:hover {
  background: #047857;
}
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
@media (max-width: 600px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
