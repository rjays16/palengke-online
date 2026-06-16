<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div class="settings-grid">
      <div class="settings-section">
        <h3>Profile Settings</h3>
        <div class="settings-form">
          <div class="form-row">
            <div class="form-group">
              <label>Full Name</label>
              <input v-model="profile.name" class="form-input" />
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input v-model="profile.email" class="form-input" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Phone Number</label>
              <input v-model="profile.phone" class="form-input" />
            </div>
            <div class="form-group">
              <label>Role</label>
              <input :value="profile.role" class="form-input" disabled />
            </div>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>System Preferences</h3>
        <div class="pref-list">
          <div class="pref-row">
            <div class="pref-info">
              <span class="pref-label">Email Notifications</span>
              <span class="pref-desc"
                >Receive email alerts for new orders and disputes</span
              >
            </div>
            <label class="toggle">
              <input type="checkbox" v-model="prefs.emailNotifs" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div class="pref-row">
            <div class="pref-info">
              <span class="pref-label">SMS Alerts</span>
              <span class="pref-desc"
                >Get text messages for critical system events</span
              >
            </div>
            <label class="toggle">
              <input type="checkbox" v-model="prefs.smsAlerts" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div class="pref-row">
            <div class="pref-info">
              <span class="pref-label">Auto-approve Sellers</span>
              <span class="pref-desc"
                >Automatically approve verified seller registrations</span
              >
            </div>
            <label class="toggle">
              <input type="checkbox" v-model="prefs.autoApprove" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div class="pref-row">
            <div class="pref-info">
              <span class="pref-label">Allow Skip Verification</span>
              <span class="pref-desc"
                >Let sellers skip ID verification during registration (basic account)</span
              >
            </div>
            <label class="toggle">
              <input type="checkbox" v-model="prefs.allowSkipVerification" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div class="pref-row">
            <div class="pref-info">
              <span class="pref-label">Dark Mode</span>
              <span class="pref-desc">Switch to dark theme</span>
            </div>
            <label class="toggle">
              <input
                type="checkbox"
                :checked="theme.isDark"
                @change="theme.toggle()"
              />
              <span class="toggle-slider"></span>
            </label>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Security</h3>
        <div class="settings-form">
          <div class="form-row">
            <div class="form-group">
              <label>Current Password</label>
              <input
                type="password"
                placeholder="Enter current password"
                class="form-input"
              />
            </div>
            <div class="form-group">
              <label>New Password</label>
              <input
                type="password"
                placeholder="Enter new password"
                class="form-input"
              />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Confirm Password</label>
              <input
                type="password"
                placeholder="Confirm new password"
                class="form-input"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">✓ Settings saved successfully</div>

    <div class="settings-actions">
      <button class="save-btn" :disabled="saving" @click="saveSettings">
        <span v-if="saving" class="btn-spinner"></span>
        {{ saving ? 'Saving...' : 'Save Changes' }}
      </button>
      <button class="reset-btn" @click="resetSettings">Reset to Default</button>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "default",
});

const auth = useAuthStore();
const theme = useThemeStore();
auth.restore();
onMounted(async () => {
  if (auth.user?.role !== "superadmin") {
    navigateTo("/dashboard");
  }
  await loadBackendSettings();
});

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem("palengke_auth_token");
}

const STORAGE_KEY = "palengke_settings_general";

const defaults = {
  profile: {
    name: "Super Admin",
    email: "admin@palengke.com",
    phone: "+63 912 345 6789",
    role: "Administrator",
  },
  prefs: {
    emailNotifs: true,
    smsAlerts: false,
    autoApprove: true,
    allowSkipVerification: true,
    darkMode: false,
  },
};

function loadSettings() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) return JSON.parse(saved);
  } catch {}
  return defaults;
}

const saved = loadSettings();
const profile = ref(saved.profile);
const prefs = ref(saved.prefs);
const success = ref(false);
const saving = ref(false);

async function loadBackendSettings() {
  try {
    const token = getToken();
    if (!token) return;
    const res = await fetch(`${getApiBaseUrl()}/api/admin/settings`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (!res.ok) return;
    const data = await res.json();
    if (data.allow_skip_verification !== undefined) {
      prefs.value.allowSkipVerification = data.allow_skip_verification === 'true';
    }
  } catch {}
}

async function saveSettings() {
  saving.value = true;
  const data = { profile: profile.value, prefs: prefs.value };
  localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  try {
    const token = getToken();
    if (token) {
      await fetch(`${getApiBaseUrl()}/api/admin/settings`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
        body: JSON.stringify({
          settings: {
            allow_skip_verification: prefs.value.allowSkipVerification ? 'true' : 'false',
          },
        }),
      });
    }
  } catch {}
  saving.value = false;
  success.value = true;
  setTimeout(() => {
    success.value = false;
  }, 2500);
}

function resetSettings() {
  profile.value = { ...defaults.profile };
  prefs.value = { ...defaults.prefs };
  saveSettings();
}
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
.pref-list {
  display: flex;
  flex-direction: column;
}
.pref-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 0;
  border-bottom: 1px solid #f9fafb;
}
.pref-row:last-child {
  border-bottom: none;
}
.pref-info {
  flex: 1;
}
.pref-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #111827;
}
.pref-desc {
  display: block;
  font-size: 12px;
  color: #9ca3af;
  margin-top: 2px;
}
.toggle {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
  flex-shrink: 0;
}
.toggle input {
  display: none;
}
.toggle-slider {
  position: absolute;
  inset: 0;
  background: #d1d5db;
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.2s;
}
.toggle-slider::before {
  content: "";
  position: absolute;
  left: 3px;
  bottom: 3px;
  width: 18px;
  height: 18px;
  background: white;
  border-radius: 50%;
  transition: transform 0.2s;
}
.toggle input:checked + .toggle-slider {
  background: #059669;
}
.toggle input:checked + .toggle-slider::before {
  transform: translateX(20px);
}
.toggle-slider.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.settings-actions {
  display: flex;
  gap: 12px;
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
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.save-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.btn-spinner {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin {
  to { transform: rotate(360deg); }
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
.save-toast {
  padding: 10px 16px;
  background: #f0fdf4;
  color: #059669;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}
@media (max-width: 600px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
