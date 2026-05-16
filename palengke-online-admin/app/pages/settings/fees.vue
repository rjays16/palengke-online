<template>
  <div class="page-shell">
    <div class="settings-subnav">
      <NuxtLink to="/settings" class="subnav-item" active-class="active">General</NuxtLink>
      <NuxtLink to="/settings/fees" class="subnav-item" active-class="active" exact-active-class="active">Delivery Fees</NuxtLink>
      <NuxtLink to="/settings/commissions" class="subnav-item" active-class="active">Commissions</NuxtLink>
    </div>

    <div class="settings-grid">
      <div class="settings-section">
        <h3>Delivery Fee Configuration</h3>
        <div class="settings-form">
          <div class="form-row">
            <div class="form-group" :class="{ hasError: errors.base }">
              <label>Base Delivery Fee (₱) <span class="req">*</span></label>
              <input v-model="fees.base" type="number" min="0" class="form-input" required />
              <span class="form-hint">Standard fee for orders within 3km</span>
              <span class="form-error" v-if="errors.base">{{ errors.base }}</span>
            </div>
            <div class="form-group" :class="{ hasError: errors.perKm }">
              <label>Per Kilometer Rate (₱) <span class="req">*</span></label>
              <input v-model="fees.perKm" type="number" step="0.5" min="0" class="form-input" required />
              <span class="form-hint">Additional charge per km beyond base</span>
              <span class="form-error" v-if="errors.perKm">{{ errors.perKm }}</span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" :class="{ hasError: errors.expressMultiplier }">
              <label>Express Multiplier (×) <span class="req">*</span></label>
              <input v-model="fees.expressMultiplier" type="number" step="0.1" min="1" class="form-input" required />
              <span class="form-hint">Express delivery = base × multiplier</span>
              <span class="form-error" v-if="errors.expressMultiplier">{{ errors.expressMultiplier }}</span>
            </div>
            <div class="form-group" :class="{ hasError: errors.freeThreshold }">
              <label>Free Delivery Threshold (₱) <span class="req">*</span></label>
              <input v-model="fees.freeThreshold" type="number" min="0" class="form-input" required />
              <span class="form-hint">Orders above this amount get free delivery</span>
              <span class="form-error" v-if="errors.freeThreshold">{{ errors.freeThreshold }}</span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" :class="{ hasError: errors.maxRadius }">
              <label>Max Delivery Radius (km) <span class="req">*</span></label>
              <input v-model="fees.maxRadius" type="number" min="0" class="form-input" required />
              <span class="form-error" v-if="errors.maxRadius">{{ errors.maxRadius }}</span>
            </div>
            <div class="form-group" :class="{ hasError: errors.nightSurcharge }">
              <label>Night Surcharge (₱) <span class="req">*</span></label>
              <input v-model="fees.nightSurcharge" type="number" min="0" class="form-input" required />
              <span class="form-hint">Additional fee for 10PM — 6AM deliveries</span>
              <span class="form-error" v-if="errors.nightSurcharge">{{ errors.nightSurcharge }}</span>
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
            <span class="preview-value">₱{{ (fees.base + fees.perKm * 2).toFixed(0) }}</span>
          </div>
          <div class="preview-card">
            <span class="preview-label">Express (3km)</span>
            <span class="preview-value">₱{{ (fees.base * fees.expressMultiplier).toFixed(0) }}</span>
          </div>
          <div class="preview-card">
            <span class="preview-label">Free Delivery</span>
            <span class="preview-value">₱0</span>
          </div>
        </div>
      </div>
    </div>

    <div class="save-toast" v-if="success">✓ Delivery fees saved successfully</div>

    <div class="settings-actions">
      <button class="save-btn" @click="saveFees">Save Changes</button>
      <button class="reset-btn" @click="resetFees">Reset to Default</button>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default',
})

const auth = useAuthStore()
auth.restore()
onMounted(() => {
  if (auth.user?.role !== 'superadmin') {
    navigateTo('/dashboard')
  }
})

const STORAGE_KEY = 'palengke_settings_fees'

const defaults = { base: 45, perKm: 10, expressMultiplier: 1.8, freeThreshold: 500, maxRadius: 15, nightSurcharge: 25 }

function loadFees() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved) return JSON.parse(saved)
  } catch {}
  return { ...defaults }
}

const fees = ref(loadFees())
const success = ref(false)

const errors = reactive({
  base: '', perKm: '', expressMultiplier: '', freeThreshold: '', maxRadius: '', nightSurcharge: ''
})

function validate() {
  let valid = true
  Object.keys(errors).forEach(k => errors[k] = '')

  if (fees.value.base === '' || fees.value.base == null) {
    errors.base = 'Base delivery fee is required'; valid = false
  } else if (fees.value.base < 0) {
    errors.base = 'Cannot be negative'; valid = false
  }

  if (fees.value.perKm === '' || fees.value.perKm == null) {
    errors.perKm = 'Per km rate is required'; valid = false
  } else if (fees.value.perKm < 0) {
    errors.perKm = 'Cannot be negative'; valid = false
  }

  if (fees.value.expressMultiplier === '' || fees.value.expressMultiplier == null) {
    errors.expressMultiplier = 'Express multiplier is required'; valid = false
  } else if (fees.value.expressMultiplier < 1) {
    errors.expressMultiplier = 'Must be at least 1'; valid = false
  }

  if (fees.value.freeThreshold === '' || fees.value.freeThreshold == null) {
    errors.freeThreshold = 'Free delivery threshold is required'; valid = false
  } else if (fees.value.freeThreshold < 0) {
    errors.freeThreshold = 'Cannot be negative'; valid = false
  }

  if (fees.value.maxRadius === '' || fees.value.maxRadius == null) {
    errors.maxRadius = 'Max delivery radius is required'; valid = false
  } else if (fees.value.maxRadius < 0) {
    errors.maxRadius = 'Cannot be negative'; valid = false
  }

  if (fees.value.nightSurcharge === '' || fees.value.nightSurcharge == null) {
    errors.nightSurcharge = 'Night surcharge is required'; valid = false
  } else if (fees.value.nightSurcharge < 0) {
    errors.nightSurcharge = 'Cannot be negative'; valid = false
  }

  return valid
}

const saveFees = () => {
  if (!validate()) return
  localStorage.setItem(STORAGE_KEY, JSON.stringify(fees.value))
  success.value = true
  setTimeout(() => { success.value = false }, 2500)
}

const resetFees = () => {
  fees.value = { ...defaults }
  Object.keys(errors).forEach(k => errors[k] = '')
  saveFees()
}
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 24px; max-width: 900px; }
.settings-subnav { display: flex; gap: 4px; background: #fff; border-radius: 12px; padding: 6px; border: 1px solid #e5e7eb; }
.subnav-item { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 500; color: #6b7280; text-decoration: none; transition: all 0.15s; }
.subnav-item:hover { color: #374151; background: #f9fafb; }
.subnav-item.active { background: #f0fdf4; color: #059669; font-weight: 600; }
.settings-grid { display: flex; flex-direction: column; gap: 20px; }
.settings-section { background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; }
.settings-section h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 20px; }
.settings-form { display: flex; flex-direction: column; gap: 16px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 13px; font-weight: 600; color: #374151; }
.form-input { padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; background: #fff; outline: none; transition: border-color 0.2s; }
.form-input:focus { border-color: #059669; }
.form-hint { font-size: 11px; color: #9ca3af; }
.req { color: #dc2626; }
.form-group.hasError .form-input { border-color: #dc2626; background: #fef2f2; }
.form-error { font-size: 11px; color: #dc2626; font-weight: 500; }
.preview-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.preview-card { padding: 16px; background: #f9fafb; border-radius: 10px; text-align: center; }
.preview-label { display: block; font-size: 12px; color: #9ca3af; margin-bottom: 6px; }
.preview-value { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px; font-weight: 700; color: #111827; }
.settings-actions { display: flex; gap: 12px; }
.save-toast { padding: 10px 16px; background: #f0fdf4; color: #059669; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; font-weight: 600; }
.save-btn { padding: 12px 24px; background: #059669; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
.save-btn:hover { background: #047857; }
.reset-btn { padding: 12px 24px; background: #fff; color: #374151; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
.reset-btn:hover { border-color: #9ca3af; }
@media (max-width: 600px) { .form-row, .preview-grid { grid-template-columns: 1fr; } }
</style>
