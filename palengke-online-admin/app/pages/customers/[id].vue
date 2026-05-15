<template>
  <div class="detail-page">
    <button class="back-btn" @click="goBack">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Customers
    </button>

    <div v-if="customer" class="detail-content">
      <div class="detail-header">
        <div class="detail-avatar" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed)">
          {{ customer.name.charAt(0) }}{{ customer.name.split(' ')[1]?.charAt(0) }}
        </div>
        <div class="detail-title">
          <h1>{{ customer.name }}</h1>
          <span class="detail-subtitle">{{ customer.email }} · {{ customer.phone }}</span>
        </div>
        <div class="detail-actions">
          <span class="status-badge-lg verified">Verified</span>
          <button class="action-btn danger" @click="showSuspendConfirm = true">Suspend</button>
        </div>
      </div>

      <div class="detail-grid">
        <div class="info-card">
          <h3>Account Information</h3>
          <div class="info-rows">
            <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ customer.email }}</span></div>
            <div class="info-row"><span class="info-label">Phone</span><span class="info-value">{{ customer.phone }}</span></div>
            <div class="info-row"><span class="info-label">Orders</span><span class="info-value">{{ customer.orders }}</span></div>
            <div class="info-row"><span class="info-label">Total Spent</span><span class="info-value">₱{{ customer.spent.toLocaleString() }}</span></div>
            <div class="info-row"><span class="info-label">Avg Order</span><span class="info-value">₱{{ Math.round(customer.spent / customer.orders).toLocaleString() }}</span></div>
            <div class="info-row"><span class="info-label">Joined</span><span class="info-value">{{ customer.joined }}</span></div>
          </div>
        </div>

        <div class="info-card">
          <h3>Saved Addresses</h3>
          <div class="address-list">
            <div class="address-item">
              <div class="address-tag home">Home</div>
              <span class="address-text">123 Rizal St., Brgy. San Isidro, Manila</span>
            </div>
            <div class="address-item">
              <div class="address-tag work">Work</div>
              <span class="address-text">456 Ayala Ave., Makati City</span>
            </div>
          </div>
        </div>
      </div>

      <div class="tabs-card">
        <div class="tabs-header">
          <button class="tab" :class="{ active: activeTab === 'orders' }" @click="activeTab = 'orders'">Order History ({{ customer.orders }})</button>
          <button class="tab" :class="{ active: activeTab === 'activity' }" @click="activeTab = 'activity'">Recent Activity</button>
        </div>

        <div v-if="activeTab === 'orders'" class="tab-content">
          <div class="table-card">
            <table class="data-table">
              <thead><tr><th>Order ID</th><th>Seller</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
              <tbody>
                <tr v-for="o in orderHistory" :key="o.id">
                  <td class="id-cell">#{{ o.id }}</td>
                  <td>{{ o.seller }}</td>
                  <td>{{ o.items }}</td>
                  <td>₱{{ o.total.toLocaleString() }}</td>
                  <td><span class="status-badge" :class="o.status">{{ o.status }}</span></td>
                  <td class="date-cell">{{ o.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="activeTab === 'activity'" class="tab-content">
          <div class="activity-list">
            <div v-for="a in activities" :key="a.id" class="activity-item">
              <div class="activity-dot" :class="a.type"></div>
              <div class="activity-body">
                <p class="activity-text">{{ a.text }}</p>
                <span class="activity-time">{{ a.time }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="not-found">
      <h2>Customer not found</h2>
      <p>The customer you're looking for doesn't exist.</p>
    </div>

    <div class="modal-overlay" v-if="showSuspendConfirm" @click="showSuspendConfirm = false">
      <div class="modal-card" @click.stop>
        <div class="modal-top">
          <span class="modal-title">Suspend Customer</span>
          <button class="modal-close" @click="showSuspendConfirm = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <p class="confirm-text">Are you sure you want to suspend <strong>{{ customer?.name }}</strong>?</p>
          <p class="confirm-hint">They will not be able to place orders while suspended.</p>
        </div>
        <div class="modal-actions">
          <button class="modal-btn cancel" @click="showSuspendConfirm = false">Cancel</button>
          <button class="modal-btn danger" @click="suspendCustomer">Suspend</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
const route = useRoute()
const customersStore = useCustomersStore()

const customer = computed(() => customersStore.getCustomerById(Number(route.params.id)))
const activeTab = ref('orders')

const orderHistory = computed(() => {
  if (!customer.value) return []
  return customersStore.getOrderHistory(customer.value.id)
})

const activities = computed(() => customersStore.getActivities())

const showSuspendConfirm = ref(false)

function suspendCustomer() {
  showSuspendConfirm.value = false
}

const goBack = () => navigateTo('/customers')
</script>

<style scoped>
.detail-page { display: flex; flex-direction: column; gap: 20px; }
.back-btn { display: inline-flex; align-items: center; gap: 6px; background: none; border: none; font-size: 13px; font-weight: 500; color: #059669; cursor: pointer; font-family: 'Inter', sans-serif; padding: 0; width: fit-content; }
.back-btn svg { width: 16px; height: 16px; }
.back-btn:hover { color: #047857; }
.detail-content { display: flex; flex-direction: column; gap: 20px; }
.detail-header { display: flex; align-items: center; gap: 16px; background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; }
.detail-avatar { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; color: white; flex-shrink: 0; }
.detail-title { flex: 1; }
.detail-title h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px; font-weight: 700; color: #111827; margin: 0; }
.detail-subtitle { font-size: 13px; color: #9ca3af; }
.detail-actions { display: flex; align-items: center; gap: 10px; }
.status-badge-lg { padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; }
.status-badge-lg.verified { background: #f0fdf4; color: #059669; }
.action-btn { padding: 8px 16px; border-radius: 8px; border: 1.5px solid #e5e7eb; background: #fff; font-size: 13px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; color: #374151; }
.action-btn.danger { border-color: #fecaca; color: #dc2626; }
.action-btn.danger:hover { background: #fef2f2; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.info-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.info-card h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 16px; }
.info-rows { display: flex; flex-direction: column; }
.info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f9fafb; font-size: 13px; }
.info-row:last-child { border-bottom: none; }
.info-label { color: #9ca3af; font-weight: 500; }
.info-value { color: #111827; font-weight: 600; }
.address-list { display: flex; flex-direction: column; gap: 10px; }
.address-item { display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; background: #f9fafb; border-radius: 8px; }
.address-tag { padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0; margin-top: 2px; }
.address-tag.home { background: #f0fdf4; color: #059669; }
.address-tag.work { background: #eff6ff; color: #3b82f6; }
.address-text { font-size: 13px; color: #374151; }
.tabs-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.tabs-header { display: flex; border-bottom: 1px solid #e5e7eb; }
.tab { padding: 12px 20px; font-size: 13px; font-weight: 500; color: #6b7280; background: none; border: none; cursor: pointer; font-family: 'Inter', sans-serif; border-bottom: 2px solid transparent; transition: all 0.15s; }
.tab:hover { color: #374151; }
.tab.active { color: #059669; border-bottom-color: #059669; font-weight: 600; }
.tab-content { padding: 16px; }
.table-card { overflow: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 12px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 12px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.id-cell { font-weight: 600; color: #059669; }
.date-cell { color: #9ca3af; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.delivered { background: #f0fdf4; color: #059669; }
.status-badge.cancelled { background: #fef2f2; color: #dc2626; }
.activity-list { display: flex; flex-direction: column; }
.activity-item { display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f9fafb; }
.activity-item:last-child { border-bottom: none; }
.activity-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
.activity-dot.order { background: #059669; }
.activity-dot.payment { background: #3b82f6; }
.activity-dot.review { background: #f59e0b; }
.activity-dot.account { background: #8b5cf6; }
.activity-body { flex: 1; }
.activity-text { font-size: 13px; color: #374151; margin-bottom: 2px; }
.activity-time { font-size: 11px; color: #9ca3af; }
.not-found { text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; }
.not-found h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; color: #111827; margin-bottom: 8px; }
.not-found p { color: #9ca3af; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 32px; backdrop-filter: blur(2px); }
.modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.modal-top { display: flex; align-items: center; gap: 10px; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; }
.modal-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 700; color: #111827; flex: 1; }
.modal-close { width: 32px; height: 32px; border: none; background: none; cursor: pointer; color: #9ca3af; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-close:hover { background: #f3f4f6; color: #374151; }
.modal-close svg { width: 18px; height: 18px; }
.modal-body { padding: 20px 24px; }
.confirm-text { font-size: 14px; color: #374151; margin: 0 0 8px; line-height: 1.5; }
.confirm-hint { font-size: 12px; color: #9ca3af; margin: 0; }
.modal-actions { display: flex; gap: 10px; padding: 16px 24px 20px; border-top: 1px solid #f3f4f6; }
.modal-btn { flex: 1; padding: 11px; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.15s; border: none; }
.modal-btn.cancel { background: #f3f4f6; color: #6b7280; }
.modal-btn.cancel:hover { background: #e5e7eb; }
.modal-btn.danger { background: #dc2626; color: #fff; }
.modal-btn.danger:hover { background: #b91c1c; }

@media (max-width: 768px) {
  .detail-grid { grid-template-columns: 1fr; }
  .detail-header { flex-wrap: wrap; }
  .data-table { min-width: 500px; }
}
@media (max-width: 480px) {
  .detail-actions { flex-wrap: wrap; }
}
</style>
