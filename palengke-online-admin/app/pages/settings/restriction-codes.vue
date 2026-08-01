<template>
  <div class="page-shell">
    <SettingsSubnav />

    <div class="role-banner driver-banner">
      <span class="role-banner-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="16" r="3"/><circle cx="18" cy="16" r="3"/><path d="M9 16h9"/><path d="M4 9h5l2 4"/><path d="M18 5l-3 2v9"/><rect x="13" y="6" width="5" height="4" rx="1"/></svg></span> Driver Restriction Codes
    </div>

    <div class="settings-grid">
      <div class="settings-section">
        <div class="section-header">
          <div>
            <h3>Driver Restriction Codes</h3>
            <p class="section-desc">
              Enable or disable restriction codes that drivers can select during registration. Only enabled codes will appear in the driver app.
            </p>
          </div>
          <div class="bulk-actions">
            <button class="text-btn" @click="enableAll">Enable All</button>
            <button class="text-btn" @click="disableAll">Disable All</button>
          </div>
        </div>

        <div v-if="store.loading && codes.length === 0" class="loading-state">Loading restriction codes...</div>
        <div v-else class="codes-list">
          <div v-for="code in codes" :key="code.id" class="code-row">
            <div class="code-row-main">
              <label class="toggle-label">
                <input
                  type="checkbox"
                  v-model="code.enabled"
                  @change="onToggle(code)"
                />
                <span class="checkmark"></span>
                <span class="code-badge">{{ code.code }}</span>
                <span class="code-label">{{ code.label }}</span>
              </label>
              <span class="status-badge" :class="code.enabled ? 'active' : 'inactive'">
                {{ code.enabled ? 'Enabled' : 'Disabled' }}
              </span>
            </div>
            <div class="vehicle-types-block">
              <div class="vehicle-types-head">
                <span class="vehicle-types-title">Vehicle Types</span>
                <button
                  v-if="!editingId || editingId !== code.id"
                  class="text-btn"
                  @click="startEdit(code)"
                >
                  {{ code.vehicle_types?.length ? 'Edit' : 'Add' }}
                </button>
              </div>
              <div v-if="editingId === code.id" class="vehicle-types-edit">
                <input
                  v-model="editVehicleTypes"
                  type="text"
                  placeholder="e.g. Motorcycle, Tricycle"
                  class="vehicle-types-input"
                />
                <button class="save-btn" @click="saveEdit(code)" :disabled="store.loading">Save</button>
                <button class="cancel-btn" @click="cancelEdit">Cancel</button>
              </div>
              <div v-else class="vehicle-types-list">
                <span v-for="v in code.vehicle_types || []" :key="v" class="vehicle-type-tag">{{ v }}</span>
                <span v-if="!(code.vehicle_types || []).length" class="vehicle-types-empty">No vehicle types set</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Summary</h3>
        <div class="summary-grid">
          <div class="summary-card">
            <span class="summary-label">Total Codes</span>
            <span class="summary-value">{{ totalCodes }}</span>
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
      ✓ Restriction codes updated successfully
    </div>

    <div class="error-toast" v-if="error">
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

const store = useRestrictionCodesStore();
const { codes } = storeToRefs(store);

const success = ref(false);
const error = ref("");

const editingId = ref<number | null>(null);
const editVehicleTypes = ref("");

function startEdit(code: any) {
  editingId.value = code.id;
  editVehicleTypes.value = (code.vehicle_types || []).join(", ");
}

function cancelEdit() {
  editingId.value = null;
  editVehicleTypes.value = "";
}

async function saveEdit(code: any) {
  const types = editVehicleTypes.value
    .split(",")
    .map((s: string) => s.trim())
    .filter(Boolean);
  error.value = "";
  success.value = false;
  try {
    await store.updateCode(code.id, { vehicle_types: types });
    code.vehicle_types = types;
    cancelEdit();
    success.value = true;
    setTimeout(() => { success.value = false; }, 2500);
  } catch {
    error.value = "Failed to save vehicle types";
  }
}

const totalCodes = computed(() => codes.value.length);
const enabledCount = computed(() => codes.value.filter((c) => c.enabled).length);
const disabledCount = computed(() => codes.value.filter((c) => !c.enabled).length);

async function onToggle(code: any) {
  error.value = "";
  success.value = false;
  try {
    await store.toggleCode(code.id, code.enabled);
    success.value = true;
    setTimeout(() => { success.value = false; }, 2500);
  } catch {
    code.enabled = !code.enabled;
    error.value = "Failed to update restriction code";
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
    for (const code of codes.value) {
      code.enabled = enabled;
      await store.toggleCode(code.id, enabled);
    }
    success.value = true;
    setTimeout(() => { success.value = false; }, 2500);
  } catch {
    error.value = "Failed to update restriction codes";
  }
}

onMounted(() => {
  store.fetchCodes();
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
.loading-state {
  padding: 40px;
  text-align: center;
  font-size: 14px;
  color: #6b7280;
}
.codes-list {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.code-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-radius: 8px;
  transition: background 0.1s;
}
.code-row:hover {
  background: #f9fafb;
}
.code-row-main {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  width: 100%;
}
.vehicle-types-block {
  margin: 10px 0 4px 0;
  padding: 10px 12px;
  background: #f9fafb;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.vehicle-types-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.vehicle-types-title {
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.02em;
}
.vehicle-types-list {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.vehicle-type-tag {
  font-size: 12px;
  font-weight: 500;
  color: #111827;
  background: #fff;
  border: 1px solid #e5e7eb;
  padding: 3px 10px;
  border-radius: 999px;
}
.vehicle-types-empty {
  font-size: 12px;
  color: #9ca3af;
}
.vehicle-types-edit {
  display: flex;
  gap: 8px;
  align-items: center;
}
.vehicle-types-input {
  flex: 1;
  font-size: 13px;
  padding: 8px 10px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-family: "Inter", sans-serif;
}
.vehicle-types-input:focus {
  outline: none;
  border-color: #059669;
}
.save-btn {
  background: #059669;
  color: #fff;
  border: none;
  font-size: 12px;
  font-weight: 600;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
  font-family: "Inter", sans-serif;
}
.save-btn:disabled {
  opacity: 0.6;
  cursor: default;
}
.cancel-btn {
  background: none;
  border: 1px solid #d1d5db;
  color: #6b7280;
  font-size: 12px;
  font-weight: 600;
  padding: 7px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-family: "Inter", sans-serif;
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
.code-badge {
  font-size: 12px;
  font-weight: 700;
  color: #059669;
  background: #f0fdf4;
  padding: 2px 8px;
  border-radius: 4px;
  font-family: monospace;
  flex-shrink: 0;
}
.code-label {
  font-size: 14px;
  font-weight: 500;
  color: #111827;
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
