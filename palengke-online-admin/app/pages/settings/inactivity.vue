<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div class="settings-grid">
      <div class="settings-section">
        <div class="section-header">
          <div>
            <h3>Inactivity / Session Timeout</h3>
            <p class="section-desc">
              Control whether users are automatically logged out after a period of inactivity.
              Applies to the Admin panel, Seller app, and Driver app.
            </p>
          </div>
        </div>

        <div v-if="isLoading" class="loading-state">Loading settings...</div>
        <div v-else-if="error" class="error-state">{{ error }}</div>
        <div v-else class="inactivity-config">
          <div class="setting-row">
            <div class="setting-info">
              <div class="setting-label">Enable Auto-Logout on Inactivity</div>
              <div class="setting-desc">
                When enabled, users will be logged out after {{ timeoutMinutes }} minutes of inactivity.
                When disabled, sessions will persist indefinitely.
              </div>
            </div>
            <label class="toggle-switch">
              <input
                type="checkbox"
                v-model="inactivityEnabled"
                @change="onToggleInactivity"
              />
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="status-banner" :class="inactivityEnabled ? 'enabled' : 'disabled'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="banner-icon">
              <circle cx="12" cy="12" r="10" />
              <polyline v-if="inactivityEnabled" points="12 6 12 12 16 14" />
              <line v-else x1="8" y1="12" x2="16" y2="12" />
            </svg>
            <div class="banner-text">
              <strong v-if="inactivityEnabled">Auto-Logout is Enabled</strong>
              <strong v-else>Auto-Logout is Disabled</strong>
              <span v-if="inactivityEnabled">Users are logged out after {{ timeoutMinutes }} minutes of inactivity across all apps.</span>
              <span v-else>Users will not be logged out due to inactivity. Sessions remain active until manual logout.</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">
      ✓ Inactivity settings saved successfully
    </div>

    <div class="error-toast" v-if="error && !isLoading">
      ✕ {{ error }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import SettingsSubnav from "~/components/SettingsSubnav.vue";

definePageMeta({
  layout: "default",
});

const auth = useAuthStore();
auth.restore();
onMounted(() => {
  if (auth.user?.role !== "superadmin") {
    navigateTo("/dashboard");
  }
});

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem("palengke_auth_token");
}

const isLoading = ref(false);
const success = ref(false);
const error = ref("");
const inactivityEnabled = ref(true);
const timeoutMinutes = ref(10);

async function fetchSettings() {
  isLoading.value = true;
  error.value = "";

  try {
    const token = getToken();
    const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/settings`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    inactivityEnabled.value = response.inactivity_enabled !== "false";
    timeoutMinutes.value = parseInt(response.inactivity_timeout_minutes || "10", 10);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to load inactivity settings";
  } finally {
    isLoading.value = false;
  }
}

async function onToggleInactivity() {
  error.value = "";
  success.value = false;

  try {
    const token = getToken();
    await $fetch(`${getApiBaseUrl()}/api/admin/settings`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${token}`,
      },
      body: {
        settings: {
          inactivity_enabled: inactivityEnabled.value ? "true" : "false",
        },
      },
    });

    success.value = true;
    setTimeout(() => {
      success.value = false;
    }, 2500);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to update inactivity settings";
    inactivityEnabled.value = !inactivityEnabled.value;
  }
}

onMounted(() => {
  fetchSettings();
});
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 900px;
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
  margin: 0;
}
.section-desc {
  font-size: 13px;
  color: #9ca3af;
  margin: 6px 0 0;
}
.section-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 20px;
}
.loading-state,
.error-state {
  padding: 40px;
  text-align: center;
  font-size: 14px;
  color: #6b7280;
}
.error-state {
  color: #dc2626;
}
.inactivity-config {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.setting-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 10px;
}
.setting-info {
  flex: 1;
}
.setting-label {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 4px;
}
.setting-desc {
  font-size: 13px;
  color: #6b7280;
  line-height: 1.5;
}
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 26px;
  flex-shrink: 0;
}
.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #d1d5db;
  border-radius: 26px;
  transition: 0.3s;
}
.toggle-slider::before {
  content: "";
  position: absolute;
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  border-radius: 50%;
  transition: 0.3s;
}
.toggle-switch input:checked + .toggle-slider {
  background-color: #059669;
}
.toggle-switch input:checked + .toggle-slider::before {
  transform: translateX(22px);
}
.status-banner {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  border-radius: 10px;
}
.status-banner.enabled {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #059669;
}
.status-banner.disabled {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
}
.banner-icon {
  width: 22px;
  height: 22px;
  flex-shrink: 0;
}
.banner-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.banner-text strong {
  font-size: 13px;
  font-weight: 600;
}
.banner-text span {
  font-size: 12px;
  opacity: 0.8;
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
.error-toast {
  padding: 10px 16px;
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}
</style>
