<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div class="settings-grid">
      <div class="settings-section">
        <div class="section-header">
          <div>
            <h3>Calling / VoIP Settings</h3>
            <p class="section-desc">
              Control whether in-app voice calling (VoIP) is enabled for sellers and drivers. When disabled, users will call each other directly via their phone's dialer instead.
            </p>
          </div>
        </div>

        <div v-if="isLoading" class="loading-state">Loading settings...</div>
        <div v-else-if="error" class="error-state">{{ error }}</div>
        <div v-else class="calling-config">
          <div class="setting-row">
            <div class="setting-info">
              <div class="setting-label">Enable In-App Calling</div>
              <div class="setting-desc">
                When enabled, sellers and drivers can make voice calls directly within the app using VoIP.
              </div>
            </div>
            <label class="toggle-switch">
              <input
                type="checkbox"
                v-model="callingEnabled"
                @change="onToggleCalling"
              />
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="status-banner" :class="callingEnabled ? 'enabled' : 'disabled'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="banner-icon">
              <path v-if="callingEnabled" d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
              <g v-else>
                <path d="M10.68 13.31a16 16 0 0 0 3.41 2.6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7 2 2 0 0 1 1.72 2v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72" />
                <line x1="1" y1="1" x2="23" y2="23" />
              </g>
            </svg>
            <div class="banner-text">
              <strong v-if="callingEnabled">In-App Calling is Enabled</strong>
              <strong v-else>In-App Calling is Disabled</strong>
              <span v-if="callingEnabled">Users can make VoIP calls within the app.</span>
              <span v-else>Users will be directed to their phone's dialer to call the other party directly.</span>
            </div>
          </div>

          <div class="behavior-section">
            <h4>Current Behavior</h4>
            <div class="behavior-grid">
              <div class="behavior-card">
                <div class="behavior-header">
                  <span class="behavior-badge enabled" v-if="callingEnabled">VoIP</span>
                  <span class="behavior-badge disabled" v-else>Phone Dialer</span>
                </div>
                <div class="behavior-title">When user taps Call</div>
                <div class="behavior-desc" v-if="callingEnabled">
                  Initiates an in-app voice call via Mesibo VoIP. The other party receives an incoming call screen with accept/decline options.
                </div>
                <div class="behavior-desc" v-else>
                  Opens the phone's native dialer with the other party's registered phone number pre-filled. No in-app call UI is shown.
                </div>
              </div>
              <div class="behavior-card">
                <div class="behavior-header">
                  <span class="behavior-badge enabled" v-if="callingEnabled">Active</span>
                  <span class="behavior-badge disabled" v-else>Disabled</span>
                </div>
                <div class="behavior-title">Incoming calls</div>
                <div class="behavior-desc" v-if="callingEnabled">
                  Users receive incoming call notifications with a full-screen call modal. They can accept, decline, or let it auto-reject after 30 seconds.
                </div>
                <div class="behavior-desc" v-else>
                  Incoming call polling is disabled. Users will not receive in-app call notifications.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">
      ✓ Calling settings saved successfully
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
const callingEnabled = ref(true);

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

    callingEnabled.value = response.enable_calling !== "false";
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to load calling settings";
  } finally {
    isLoading.value = false;
  }
}

async function onToggleCalling() {
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
          enable_calling: callingEnabled.value ? "true" : "false",
        },
      },
    });

    success.value = true;
    setTimeout(() => {
      success.value = false;
    }, 2500);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to update calling settings";
    callingEnabled.value = !callingEnabled.value;
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
.calling-config {
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
.behavior-section h4 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin: 0 0 12px;
}
.behavior-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.behavior-card {
  background: #f9fafb;
  border-radius: 10px;
  padding: 16px;
  border: 1px solid #f3f4f6;
}
.behavior-header {
  margin-bottom: 8px;
}
.behavior-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.behavior-badge.enabled {
  background: #f0fdf4;
  color: #059669;
}
.behavior-badge.disabled {
  background: #fef2f2;
  color: #dc2626;
}
.behavior-title {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 6px;
}
.behavior-desc {
  font-size: 12px;
  color: #6b7280;
  line-height: 1.5;
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
@media (max-width: 600px) {
  .behavior-grid {
    grid-template-columns: 1fr;
  }
}
</style>
