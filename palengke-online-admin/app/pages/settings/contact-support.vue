<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div class="settings-grid">
      <div class="settings-section">
        <div class="section-header">
          <div>
            <h3>Contact Support</h3>
            <p class="section-desc">
              Enable or disable contact support options. Only enabled options will appear in the seller app's Help & Support page.
            </p>
          </div>
          <div class="bulk-actions">
            <button class="text-btn" @click="enableAll">Enable All</button>
            <button class="text-btn" @click="disableAll">Disable All</button>
          </div>
        </div>

        <div v-if="isLoading" class="loading-state">Loading contact support options...</div>
        <div v-else-if="error" class="error-state">{{ error }}</div>
        <div v-else class="contacts-list">
          <div v-for="contact in contacts" :key="contact.id" class="contact-row">
            <label class="toggle-label">
              <input
                type="checkbox"
                v-model="contact.enabled"
                @change="onToggle(contact)"
              />
              <span class="checkmark"></span>
              <div class="contact-info">
                <span class="contact-title">{{ contact.title }}</span>
                <span class="contact-subtitle">{{ contact.subtitle }}</span>
              </div>
            </label>
            <span class="status-badge" :class="contact.enabled ? 'active' : 'inactive'">
              {{ contact.enabled ? 'Enabled' : 'Disabled' }}
            </span>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Summary</h3>
        <div class="summary-grid">
          <div class="summary-card">
            <span class="summary-label">Total Options</span>
            <span class="summary-value">{{ totalContacts }}</span>
          </div>
          <div class="summary-card">
            <span class="summary-label">Enabled</span>
            <span class="summary-value">{{ enabledCount }}</span>
          </div>
          <div class="summary-card">
            <span class="summary-label">Disabled</span>
            <span class="summary-value">{{ disabledCount }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">
      ✓ Contact support settings saved successfully
    </div>

    <div class="error-toast" v-if="error && !isLoading">
      ✕ {{ error }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
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

const contacts = ref<any[]>([]);
const isLoading = ref(false);
const success = ref(false);
const error = ref("");

const totalContacts = computed(() => contacts.value.length);
const enabledCount = computed(() => contacts.value.filter((c) => c.enabled).length);
const disabledCount = computed(() => contacts.value.filter((c) => !c.enabled).length);

async function fetchContacts() {
  isLoading.value = true;
  error.value = "";

  try {
    const token = getToken();
    const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/contact-supports`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    contacts.value = response.contacts;
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to load contact support options";
  } finally {
    isLoading.value = false;
  }
}

async function onToggle(contact: any) {
  error.value = "";
  success.value = false;

  try {
    const token = getToken();
    await $fetch(`${getApiBaseUrl()}/api/admin/contact-supports`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${token}`,
      },
      body: {
        contacts: [{ id: contact.id, enabled: contact.enabled }],
      },
    });

    success.value = true;
    setTimeout(() => {
      success.value = false;
    }, 2500);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to update contact support";
    contact.enabled = !contact.enabled;
  }
}

async function enableAll() {
  await updateAll(true);
}

async function disableAll() {
  await updateAll(false);
}

async function updateAll(enabled: boolean) {
  error.value = "";
  success.value = false;

  try {
    const token = getToken();
    const payload = contacts.value.map((c) => ({
      id: c.id,
      enabled,
    }));

    await $fetch(`${getApiBaseUrl()}/api/admin/contact-supports`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${token}`,
      },
      body: { contacts: payload },
    });

    contacts.value.forEach((c) => (c.enabled = enabled));
    success.value = true;
    setTimeout(() => {
      success.value = false;
    }, 2500);
  } catch (err: any) {
    error.value = err?.data?.message || "Failed to update contact support options";
  }
}

onMounted(() => {
  fetchContacts();
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
.bulk-actions {
  display: flex;
  gap: 10px;
  flex-shrink: 0;
}
.text-btn {
  background: none;
  border: none;
  font-size: 12px;
  font-weight: 600;
  color: #059669;
  cursor: pointer;
  padding: 6px 10px;
  border-radius: 6px;
  font-family: "Inter", sans-serif;
  transition: background 0.15s;
}
.text-btn:hover {
  background: #f0fdf4;
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
.contacts-list {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.contact-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-radius: 8px;
  transition: background 0.1s;
}
.contact-row:hover {
  background: #f9fafb;
}
.toggle-label {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  flex: 1;
  min-width: 0;
}
.toggle-label input {
  display: none;
}
.checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid #d1d5db;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.15s;
  position: relative;
}
.toggle-label input:checked + .checkmark {
  background: #059669;
  border-color: #059669;
}
.toggle-label input:checked + .checkmark::after {
  content: "";
  width: 5px;
  height: 9px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}
.contact-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.contact-title {
  font-size: 14px;
  font-weight: 500;
  color: #111827;
}
.contact-subtitle {
  font-size: 12px;
  color: #9ca3af;
}
.status-badge {
  font-size: 11px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 6px;
  white-space: nowrap;
}
.status-badge.active {
  background: #f0fdf4;
  color: #059669;
}
.status-badge.inactive {
  background: #f3f4f6;
  color: #6b7280;
}
.summary-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}
.summary-card {
  background: #f9fafb;
  border-radius: 10px;
  padding: 16px;
  text-align: center;
}
.summary-label {
  display: block;
  font-size: 12px;
  color: #9ca3af;
  margin-bottom: 6px;
}
.summary-value {
  display: block;
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 20px;
  font-weight: 700;
  color: #111827;
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
  .summary-grid {
    grid-template-columns: 1fr;
  }
  .section-header {
    flex-direction: column;
  }
}
</style>
