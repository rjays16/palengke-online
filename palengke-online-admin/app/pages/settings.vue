<template>
  <div class="page-shell">
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
              <span class="pref-desc">Receive email alerts for new orders and disputes</span>
            </div>
            <label class="toggle">
              <input type="checkbox" v-model="prefs.emailNotifs" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div class="pref-row">
            <div class="pref-info">
              <span class="pref-label">SMS Alerts</span>
              <span class="pref-desc">Get text messages for critical system events</span>
            </div>
            <label class="toggle">
              <input type="checkbox" v-model="prefs.smsAlerts" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div class="pref-row">
            <div class="pref-info">
              <span class="pref-label">Auto-approve Sellers</span>
              <span class="pref-desc">Automatically approve verified seller registrations</span>
            </div>
            <label class="toggle">
              <input type="checkbox" v-model="prefs.autoApprove" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div class="pref-row">
            <div class="pref-info">
              <span class="pref-label">Dark Mode</span>
              <span class="pref-desc">Switch to dark theme (coming soon)</span>
            </div>
            <label class="toggle">
              <input type="checkbox" v-model="prefs.darkMode" disabled />
              <span class="toggle-slider disabled"></span>
            </label>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Commission Settings</h3>
        <div class="settings-form">
          <div class="form-row">
            <div class="form-group">
              <label>Seller Commission (%)</label>
              <input v-model="commission.seller" type="number" class="form-input" />
            </div>
            <div class="form-group">
              <label>Driver Commission (%)</label>
              <input v-model="commission.driver" type="number" class="form-input" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Minimum Payout (₱)</label>
              <input v-model="commission.minPayout" type="number" class="form-input" />
            </div>
            <div class="form-group">
              <label>Payout Schedule</label>
              <select v-model="commission.schedule" class="form-input">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="biweekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="settings-section">
        <h3>Security</h3>
        <div class="settings-form">
          <div class="form-row">
            <div class="form-group">
              <label>Current Password</label>
              <input type="password" placeholder="Enter current password" class="form-input" />
            </div>
            <div class="form-group">
              <label>New Password</label>
              <input type="password" placeholder="Enter new password" class="form-input" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Confirm Password</label>
              <input type="password" placeholder="Confirm new password" class="form-input" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="settings-actions">
      <button class="save-btn" @click="saveSettings">Save Changes</button>
      <button class="reset-btn" @click="resetSettings">Reset to Default</button>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const profile = ref({ name: 'Super Admin', email: 'admin@palengke.com', phone: '+63 912 345 6789', role: 'Administrator' })
const prefs = ref({ emailNotifs: true, smsAlerts: false, autoApprove: true, darkMode: false })
const commission = ref({ seller: 10, driver: 15, minPayout: 500, schedule: 'weekly' })
const original = JSON.stringify({ profile, prefs, commission })

const saveSettings = () => { alert('Settings saved successfully!') }
const resetSettings = () => {
  const o = JSON.parse(original)
  profile.value = o.profile
  prefs.value = o.prefs
  commission.value = o.commission
}
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 24px; max-width: 900px; }
.settings-grid { display: flex; flex-direction: column; gap: 20px; }
.settings-section { background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; }
.settings-section h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 20px; }
.settings-form { display: flex; flex-direction: column; gap: 16px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 13px; font-weight: 600; color: #374151; }
.form-input { padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; background: #fff; outline: none; transition: border-color 0.2s; }
.form-input:focus { border-color: #059669; }
.form-input:disabled { background: #f9fafb; color: #9ca3af; }
.pref-list { display: flex; flex-direction: column; }
.pref-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid #f9fafb; }
.pref-row:last-child { border-bottom: none; }
.pref-info { flex: 1; }
.pref-label { display: block; font-size: 13px; font-weight: 600; color: #111827; }
.pref-desc { display: block; font-size: 12px; color: #9ca3af; margin-top: 2px; }
.toggle { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
.toggle input { display: none; }
.toggle-slider { position: absolute; inset: 0; background: #d1d5db; border-radius: 12px; cursor: pointer; transition: background 0.2s; }
.toggle-slider::before { content: ''; position: absolute; left: 3px; bottom: 3px; width: 18px; height: 18px; background: white; border-radius: 50%; transition: transform 0.2s; }
.toggle input:checked + .toggle-slider { background: #059669; }
.toggle input:checked + .toggle-slider::before { transform: translateX(20px); }
.toggle-slider.disabled { opacity: 0.5; cursor: not-allowed; }
.settings-actions { display: flex; gap: 12px; }
.save-btn { padding: 12px 24px; background: #059669; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
.save-btn:hover { background: #047857; }
.reset-btn { padding: 12px 24px; background: #fff; color: #374151; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
.reset-btn:hover { border-color: #9ca3af; }

@media (max-width: 600px) {
  .form-row { grid-template-columns: 1fr; }
}
</style>
