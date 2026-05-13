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
            <div class="form-group">
              <label>Base Delivery Fee (₱)</label>
              <input v-model="fees.base" type="number" class="form-input" />
              <span class="form-hint">Standard fee for orders within 3km</span>
            </div>
            <div class="form-group">
              <label>Per Kilometer Rate (₱)</label>
              <input v-model="fees.perKm" type="number" step="0.5" class="form-input" />
              <span class="form-hint">Additional charge per km beyond base</span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Express Multiplier (×)</label>
              <input v-model="fees.expressMultiplier" type="number" step="0.1" class="form-input" />
              <span class="form-hint">Express delivery = base × multiplier</span>
            </div>
            <div class="form-group">
              <label>Free Delivery Threshold (₱)</label>
              <input v-model="fees.freeThreshold" type="number" class="form-input" />
              <span class="form-hint">Orders above this amount get free delivery</span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Max Delivery Radius (km)</label>
              <input v-model="fees.maxRadius" type="number" class="form-input" />
            </div>
            <div class="form-group">
              <label>Night Surcharge (₱)</label>
              <input v-model="fees.nightSurcharge" type="number" class="form-input" />
              <span class="form-hint">Additional fee for 10PM — 6AM deliveries</span>
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

    <div class="settings-actions">
      <button class="save-btn" @click="saveFees">Save Changes</button>
      <button class="reset-btn" @click="resetFees">Reset to Default</button>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const fees = ref({ base: 45, perKm: 10, expressMultiplier: 1.8, freeThreshold: 500, maxRadius: 15, nightSurcharge: 25 })
const original = JSON.stringify(fees.value)

const saveFees = () => { alert('Delivery fees saved successfully!') }
const resetFees = () => { fees.value = JSON.parse(original) }
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
.preview-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.preview-card { padding: 16px; background: #f9fafb; border-radius: 10px; text-align: center; }
.preview-label { display: block; font-size: 12px; color: #9ca3af; margin-bottom: 6px; }
.preview-value { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px; font-weight: 700; color: #111827; }
.settings-actions { display: flex; gap: 12px; }
.save-btn { padding: 12px 24px; background: #059669; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
.save-btn:hover { background: #047857; }
.reset-btn { padding: 12px 24px; background: #fff; color: #374151; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
.reset-btn:hover { border-color: #9ca3af; }
@media (max-width: 600px) { .form-row, .preview-grid { grid-template-columns: 1fr; } }
</style>
