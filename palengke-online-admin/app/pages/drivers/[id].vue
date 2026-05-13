<template>
  <div class="detail-page">
    <button class="back-btn" @click="goBack">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Drivers
    </button>

    <div v-if="driver" class="detail-content">
      <div class="detail-header">
        <div class="detail-avatar" style="background: linear-gradient(135deg, #3b82f6, #2563eb)">{{ driver.name.charAt(0) }}</div>
        <div class="detail-title">
          <h1>{{ driver.name }}</h1>
          <span class="detail-subtitle">{{ driver.vehicle }} · {{ driver.plate }}</span>
        </div>
        <div class="detail-actions">
          <span class="status-badge-lg" :class="driver.status">{{ driver.status === 'active' ? 'Active' : driver.status === 'idle' ? 'Idle' : 'Offline' }}</span>
        </div>
      </div>

      <div class="detail-grid">
        <div class="info-card">
          <h3>Driver Information</h3>
          <div class="info-rows">
            <div class="info-row"><span class="info-label">Phone</span><span class="info-value">{{ driver.phone }}</span></div>
            <div class="info-row"><span class="info-label">Vehicle</span><span class="info-value">{{ driver.vehicle }}</span></div>
            <div class="info-row"><span class="info-label">Plate #</span><span class="info-value">{{ driver.plate }}</span></div>
            <div class="info-row"><span class="info-label">Deliveries</span><span class="info-value">{{ driver.deliveries }}</span></div>
            <div class="info-row"><span class="info-label">Rating</span><span class="info-value stars">★ {{ driver.rating }}</span></div>
            <div class="info-row"><span class="info-label">Earnings</span><span class="info-value">₱{{ driver.earnings.toLocaleString() }}</span></div>
            <div class="info-row"><span class="info-label">Joined</span><span class="info-value">{{ driver.joined }}</span></div>
          </div>
        </div>

        <div class="info-card">
          <h3>Documents</h3>
          <div class="doc-list">
            <div v-for="doc in documents" :key="doc.name" class="doc-row">
              <div class="doc-info">
                <span class="doc-name">{{ doc.name }}</span>
                <span class="doc-status" :class="doc.status">{{ doc.status === 'verified' ? 'Verified' : doc.status === 'pending' ? 'Pending' : 'Rejected' }}</span>
              </div>
              <button class="doc-btn" :class="doc.status === 'verified' ? 'verified' : ''">
                {{ doc.status === 'verified' ? 'View' : 'Review' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="tabs-card">
        <div class="tabs-header">
          <button class="tab" :class="{ active: activeTab === 'earnings' }" @click="activeTab = 'earnings'">Earnings</button>
          <button class="tab" :class="{ active: activeTab === 'history' }" @click="activeTab = 'history'">Delivery History</button>
        </div>

        <div v-if="activeTab === 'earnings'" class="tab-content">
          <div class="earnings-grid">
            <div class="mini-card">
              <span class="mini-label">This Week</span>
              <span class="mini-value">₱{{ weeklyEarnings.toLocaleString() }}</span>
              <div class="mini-bar-bg"><div class="mini-bar-fill" style="width: 65%"></div></div>
            </div>
            <div class="mini-card">
              <span class="mini-label">This Month</span>
              <span class="mini-value">₱{{ monthlyEarnings.toLocaleString() }}</span>
              <div class="mini-bar-bg"><div class="mini-bar-fill" style="width: 80%"></div></div>
            </div>
            <div class="mini-card">
              <span class="mini-label">All Time</span>
              <span class="mini-value">₱{{ driver.earnings.toLocaleString() }}</span>
              <div class="mini-bar-bg"><div class="mini-bar-fill" style="width: 100%; background:#059669"></div></div>
            </div>
          </div>
        </div>

        <div v-if="activeTab === 'history'" class="tab-content">
          <div class="table-card">
            <table class="data-table">
              <thead><tr><th>Order ID</th><th>Customer</th><th>Pickup</th><th>Delivery</th><th>Status</th><th>Date</th></tr></thead>
              <tbody>
                <tr v-for="d in deliveryHistory" :key="d.id">
                  <td class="id-cell">#{{ d.id }}</td>
                  <td>{{ d.customer }}</td>
                  <td>{{ d.pickup }}</td>
                  <td>{{ d.delivery }}</td>
                  <td><span class="status-badge" :class="d.status">{{ d.status }}</span></td>
                  <td class="date-cell">{{ d.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="not-found">
      <h2>Driver not found</h2>
      <p>The driver you're looking for doesn't exist.</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
const route = useRoute()

const drivers = [
  { id: 1, name: 'Mark Reyes', phone: '0917-123-4567', vehicle: 'Honda Click 125', plate: 'ABC-1234', deliveries: 342, rating: 4.9, earnings: 45800, status: 'active', joined: '2025-10-01' },
  { id: 2, name: 'Nina Salvador', phone: '0928-234-5678', vehicle: 'Yamaha Mio i125', plate: 'DEF-5678', deliveries: 287, rating: 4.7, earnings: 38200, status: 'active', joined: '2025-11-15' },
  { id: 3, name: 'Rico Dimagiba', phone: '0939-345-6789', vehicle: 'Suzuki Skydrive', plate: 'GHI-9012', deliveries: 198, rating: 4.5, earnings: 26400, status: 'idle', joined: '2026-01-08' },
  { id: 4, name: 'Tina Fernandez', phone: '0940-456-7890', vehicle: 'Honda Beat', plate: 'JKL-3456', deliveries: 415, rating: 4.8, earnings: 52100, status: 'active', joined: '2025-09-20' },
  { id: 5, name: 'Gary Alcantara', phone: '0951-567-8901', vehicle: 'Yamaha Nmax', plate: 'MNO-7890', deliveries: 156, rating: 4.3, earnings: 21200, status: 'offline', joined: '2026-02-14' },
  { id: 6, name: 'Wendy Pascual', phone: '0962-678-9012', vehicle: 'Honda PCX 160', plate: 'PQR-1234', deliveries: 273, rating: 4.6, earnings: 36500, status: 'active', joined: '2025-12-05' },
  { id: 7, name: 'Leo Magno', phone: '0973-789-0123', vehicle: 'Suzuki Burgman', plate: 'STU-5678', deliveries: 89, rating: 4.1, earnings: 12800, status: 'idle', joined: '2026-03-22' },
]

const driver = computed(() => drivers.find(d => d.id === Number(route.params.id)))
const activeTab = ref('earnings')

const weeklyEarnings = computed(() => Math.round(driver.value?.earnings * 0.12) || 0)
const monthlyEarnings = computed(() => Math.round(driver.value?.earnings * 0.45) || 0)

const documents = ref([
  { name: 'Driver\'s License', status: 'verified' },
  { name: 'OR/CR', status: 'verified' },
  { name: 'NBI Clearance', status: 'pending' },
  { name: 'Birth Certificate', status: 'verified' },
])

const deliveryHistory = ref([
  { id: 'ORD-1042', customer: 'Maria Santos', pickup: "Juan's Farm", delivery: 'Brgy. San Isidro', status: 'delivered', date: '2026-05-14' },
  { id: 'ORD-1041', customer: 'Juan dela Cruz', pickup: "Ana's Store", delivery: 'Brgy. Maligaya', status: 'delivered', date: '2026-05-14' },
  { id: 'ORD-1038', customer: 'Liza Morales', pickup: "Ana's Store", delivery: 'Brgy. Central', status: 'cancelled', date: '2026-05-13' },
  { id: 'ORD-1035', customer: 'Ben Torres', pickup: 'Pedro Merchants', delivery: 'Brgy. Kanluran', status: 'delivered', date: '2026-05-12' },
  { id: 'ORD-1032', customer: 'Rosa Villanueva', pickup: "Liza's Shop", delivery: 'Brgy. Silangan', status: 'delivered', date: '2026-05-11' },
])

const goBack = () => navigateTo('/drivers')
</script>

<style scoped>
.detail-page { display: flex; flex-direction: column; gap: 20px; }
.back-btn { display: inline-flex; align-items: center; gap: 6px; background: none; border: none; font-size: 13px; font-weight: 500; color: #059669; cursor: pointer; font-family: 'Inter', sans-serif; padding: 0; width: fit-content; }
.back-btn svg { width: 16px; height: 16px; }
.back-btn:hover { color: #047857; }
.detail-content { display: flex; flex-direction: column; gap: 20px; }
.detail-header { display: flex; align-items: center; gap: 16px; background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; }
.detail-avatar { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; color: white; flex-shrink: 0; }
.detail-title { flex: 1; }
.detail-title h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px; font-weight: 700; color: #111827; margin: 0; }
.detail-subtitle { font-size: 13px; color: #9ca3af; }
.detail-actions { display: flex; align-items: center; gap: 10px; }
.status-badge-lg { padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; }
.status-badge-lg.active { background: #f0fdf4; color: #059669; }
.status-badge-lg.idle { background: #fef3c7; color: #d97706; }
.status-badge-lg.offline { background: #f3f4f6; color: #6b7280; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.info-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.info-card h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 16px; }
.info-rows { display: flex; flex-direction: column; }
.info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f9fafb; font-size: 13px; }
.info-row:last-child { border-bottom: none; }
.info-label { color: #9ca3af; font-weight: 500; }
.info-value { color: #111827; font-weight: 600; }
.info-value.stars { color: #f59e0b; }
.doc-list { display: flex; flex-direction: column; gap: 8px; }
.doc-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #f9fafb; border-radius: 8px; }
.doc-info { display: flex; align-items: center; gap: 10px; }
.doc-name { font-size: 13px; font-weight: 500; color: #374151; }
.doc-status { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; }
.doc-status.verified { background: #f0fdf4; color: #059669; }
.doc-status.pending { background: #fef3c7; color: #d97706; }
.doc-status.rejected { background: #fef2f2; color: #dc2626; }
.doc-btn { padding: 5px 12px; border: 1.5px solid #e5e7eb; border-radius: 6px; font-size: 12px; font-weight: 500; background: #fff; cursor: pointer; font-family: 'Inter', sans-serif; color: #374151; }
.doc-btn:hover { border-color: #059669; color: #059669; }
.doc-btn.verified { border-color: #bbf7d0; color: #059669; }
.tabs-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.tabs-header { display: flex; border-bottom: 1px solid #e5e7eb; }
.tab { padding: 12px 20px; font-size: 13px; font-weight: 500; color: #6b7280; background: none; border: none; cursor: pointer; font-family: 'Inter', sans-serif; border-bottom: 2px solid transparent; transition: all 0.15s; }
.tab:hover { color: #374151; }
.tab.active { color: #059669; border-bottom-color: #059669; font-weight: 600; }
.tab-content { padding: 16px; }
.table-card { overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 12px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 12px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.id-cell { font-weight: 600; color: #059669; }
.date-cell { color: #9ca3af; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.delivered { background: #f0fdf4; color: #059669; }
.status-badge.cancelled { background: #fef2f2; color: #dc2626; }
.earnings-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.mini-card { padding: 16px; background: #f9fafb; border-radius: 10px; }
.mini-label { display: block; font-size: 12px; color: #9ca3af; margin-bottom: 4px; }
.mini-value { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 12px; }
.mini-bar-bg { height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; }
.mini-bar-fill { height: 100%; background: #3b82f6; border-radius: 3px; }
.not-found { text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; }
.not-found h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; color: #111827; margin-bottom: 8px; }
.not-found p { color: #9ca3af; }

@media (max-width: 768px) {
  .detail-grid { grid-template-columns: 1fr; }
  .earnings-grid { grid-template-columns: 1fr; }
  .detail-header { flex-wrap: wrap; }
}
</style>
