<template>
  <div class="detail-page">
    <button class="back-btn" @click="goBack">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Orders
    </button>

    <div v-if="order" class="detail-content">
      <div class="detail-header">
        <div class="order-id-badge">#{{ order.id }}</div>
        <div class="detail-title">
          <h1>Order #{{ order.id }}</h1>
          <span class="detail-subtitle">{{ order.date }} · {{ order.items }} item(s)</span>
        </div>
        <div class="detail-actions">
          <span class="status-badge-lg" :class="order.status">{{ order.status }}</span>
          <button class="action-btn" v-if="order.status !== 'cancelled'" @click="alert('Order cancelled')">Cancel</button>
        </div>
      </div>

      <div class="detail-grid">
        <div class="info-card">
          <h3>Order Summary</h3>
          <div class="order-items">
            <div v-for="item in orderItems" :key="item.name" class="order-item-row">
              <div class="order-item-info">
                <span class="order-item-name">{{ item.name }}</span>
                <span class="order-item-qty">{{ item.qty }} × ₱{{ item.price }}</span>
              </div>
              <span class="order-item-total">₱{{ item.qty * item.price }}</span>
            </div>
          </div>
          <div class="order-totals">
            <div class="total-row"><span>Subtotal</span><span>₱{{ order.total }}</span></div>
            <div class="total-row"><span>Delivery Fee</span><span>₱45</span></div>
            <div class="total-row"><span>Service Fee</span><span>₱15</span></div>
            <div class="total-row grand"><span>Total</span><span>₱{{ order.total + 60 }}</span></div>
          </div>
        </div>

        <div class="info-card">
          <h3>Payment Info</h3>
          <div class="info-rows">
            <div class="info-row"><span class="info-label">Method</span><span class="info-value" style="text-transform:capitalize">{{ order.payment }}</span></div>
            <div class="info-row"><span class="info-label">Status</span><span class="info-value" style="color:#059669">Paid</span></div>
            <div class="info-row"><span class="info-label">Amount</span><span class="info-value">₱{{ order.total + 60 }}</span></div>
          </div>
        </div>
      </div>

      <div class="detail-grid">
        <div class="info-card">
          <h3>Customer</h3>
          <div class="info-rows">
            <div class="info-row"><span class="info-label">Name</span><span class="info-value">{{ order.customer }}</span></div>
            <div class="info-row"><span class="info-label">Contact</span><span class="info-value">0912-345-6789</span></div>
            <div class="info-row"><span class="info-label">Delivery</span><span class="info-value">123 Rizal St., Brgy. San Isidro</span></div>
          </div>
        </div>
        <div class="info-card">
          <h3>Seller</h3>
          <div class="info-rows">
            <div class="info-row"><span class="info-label">Store</span><span class="info-value">{{ order.seller }}</span></div>
            <div class="info-row"><span class="info-label">Contact</span><span class="info-value">0917-111-2233</span></div>
            <div class="info-row"><span class="info-label">Pickup</span><span class="info-value">{{ order.seller }} Store</span></div>
          </div>
        </div>
      </div>

      <div class="tabs-card">
        <div class="tabs-header">
          <button class="tab" :class="{ active: activeTab === 'timeline' }" @click="activeTab = 'timeline'">Timeline</button>
          <button class="tab" :class="{ active: activeTab === 'driver' }" @click="activeTab = 'driver'">Driver Info</button>
        </div>

        <div v-if="activeTab === 'timeline'" class="tab-content">
          <div class="timeline">
            <div v-for="(step, i) in timeline" :key="i" class="timeline-step" :class="{ done: step.done, current: step.current }">
              <div class="tl-dot"></div>
              <div class="tl-content">
                <span class="tl-title">{{ step.title }}</span>
                <span class="tl-time">{{ step.time }}</span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="activeTab === 'driver'" class="tab-content">
          <div v-if="driverInfo" class="driver-card">
            <div class="driver-avatar">{{ driverInfo.name.charAt(0) }}</div>
            <div class="driver-details">
              <span class="driver-name">{{ driverInfo.name }}</span>
              <span class="driver-vehicle">{{ driverInfo.vehicle }} · {{ driverInfo.plate }}</span>
              <span class="driver-phone">{{ driverInfo.phone }}</span>
            </div>
            <button class="ghost-btn" @click="alert('Contacting driver')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              Contact
            </button>
          </div>
          <div v-else class="no-driver">
            <p>No driver assigned yet.</p>
            <button class="ghost-btn" @click="alert('Searching for available driver')">Assign Driver</button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="not-found">
      <h2>Order not found</h2>
      <p>The order you're looking for doesn't exist.</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
const route = useRoute()

const orders = [
  { id: 'ORD-1042', customer: 'Maria Santos', seller: "Juan's Farm", items: 4, total: 1280, payment: 'gcash', status: 'delivered', date: '2026-05-14' },
  { id: 'ORD-1041', customer: 'Juan dela Cruz', seller: "Ana's Store", items: 2, total: 540, payment: 'cod', status: 'transit', date: '2026-05-14' },
  { id: 'ORD-1040', customer: 'Ana Gonzales', seller: 'Pedro Merchants', items: 6, total: 2150, payment: 'gcash', status: 'preparing', date: '2026-05-14' },
  { id: 'ORD-1039', customer: 'Pedro Reyes', seller: "Liza's Shop", items: 1, total: 180, payment: 'cod', status: 'delivered', date: '2026-05-13' },
  { id: 'ORD-1038', customer: 'Liza Morales', seller: "Juan's Farm", items: 3, total: 960, payment: 'gcash', status: 'cancelled', date: '2026-05-13' },
  { id: 'ORD-1037', customer: 'Carlo Bautista', seller: 'FreshMart', items: 5, total: 1640, payment: 'cod', status: 'pending', date: '2026-05-13' },
  { id: 'ORD-1036', customer: 'Rosa Villanueva', seller: "Ana's Store", items: 2, total: 320, payment: 'gcash', status: 'delivered', date: '2026-05-12' },
  { id: 'ORD-1035', customer: 'Ben Torres', seller: 'Pedro Merchants', items: 8, total: 2890, payment: 'cod', status: 'transit', date: '2026-05-12' },
  { id: 'ORD-1034', customer: 'Diana Rivera', seller: "Juan's Farm", items: 3, total: 750, payment: 'gcash', status: 'delivered', date: '2026-05-12' },
  { id: 'ORD-1033', customer: 'Mark Lim', seller: 'FreshMart', items: 4, total: 1120, payment: 'cod', status: 'preparing', date: '2026-05-11' },
]

const order = computed(() => orders.find(o => o.id === route.params.id))
const activeTab = ref('timeline')

const orderItems = computed(() => {
  if (!order.value) return []
  const items = {
    'ORD-1042': [{ name: 'Fresh Tomatoes', qty: 2, price: 60 }, { name: 'Organic Eggplant', qty: 1, price: 45 }, { name: 'Pechay Baguio', qty: 1, price: 35 }],
    'ORD-1041': [{ name: 'Ripe Mangoes', qty: 1, price: 120 }, { name: 'Green Papaya', qty: 1, price: 40 }],
    'ORD-1040': [{ name: 'Fresh Bangus', qty: 2, price: 180 }, { name: 'Chicken Leg Quarters', qty: 1, price: 190 }],
  }
  return items[order.value.id] || [{ name: 'Item', qty: order.value.items, price: Math.round(order.value.total / order.value.items) }]
})

const timeline = computed(() => {
  const status = order.value?.status || 'pending'
  const steps = [
    { title: 'Order Placed', time: order.value?.date + ' 09:30 AM', done: true, current: false },
    { title: 'Order Confirmed', time: order.value?.date + ' 09:35 AM', done: status !== 'pending', current: status === 'pending' },
    { title: 'Preparing', time: order.value?.date + ' 09:50 AM', done: ['delivered', 'transit', 'cancelled'].includes(status), current: status === 'preparing' },
    { title: 'Picked Up', time: order.value?.date + ' 10:15 AM', done: ['delivered', 'transit'].includes(status), current: status === 'transit' },
    { title: 'Delivered', time: order.value?.date + ' 10:45 AM', done: status === 'delivered', current: status === 'delivered' },
  ]
  return steps
})

const driverInfo = computed(() => {
  if (!order.value || order.value.status === 'pending' || order.value.status === 'cancelled') return null
  return { name: 'Mark Reyes', vehicle: 'Honda Click 125', plate: 'ABC-1234', phone: '0917-123-4567' }
})

const goBack = () => navigateTo('/orders')
</script>

<style scoped>
.detail-page { display: flex; flex-direction: column; gap: 20px; }
.back-btn { display: inline-flex; align-items: center; gap: 6px; background: none; border: none; font-size: 13px; font-weight: 500; color: #059669; cursor: pointer; font-family: 'Inter', sans-serif; padding: 0; width: fit-content; }
.back-btn svg { width: 16px; height: 16px; }
.back-btn:hover { color: #047857; }
.detail-content { display: flex; flex-direction: column; gap: 20px; }
.detail-header { display: flex; align-items: center; gap: 16px; background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; }
.order-id-badge { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; font-weight: 800; color: #059669; background: #f0fdf4; padding: 8px 16px; border-radius: 10px; }
.detail-title { flex: 1; }
.detail-title h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px; font-weight: 700; color: #111827; margin: 0; }
.detail-subtitle { font-size: 13px; color: #9ca3af; }
.detail-actions { display: flex; align-items: center; gap: 10px; }
.status-badge-lg { padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
.status-badge-lg.delivered { background: #f0fdf4; color: #059669; }
.status-badge-lg.transit { background: #eff6ff; color: #3b82f6; }
.status-badge-lg.preparing { background: #fef3c7; color: #d97706; }
.status-badge-lg.pending { background: #f3f4f6; color: #6b7280; }
.status-badge-lg.cancelled { background: #fef2f2; color: #dc2626; }
.action-btn { padding: 8px 16px; border-radius: 8px; border: 1.5px solid #e5e7eb; background: #fff; font-size: 13px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; color: #374151; }
.action-btn:hover { border-color: #dc2626; color: #dc2626; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.info-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.info-card h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 16px; }
.info-rows { display: flex; flex-direction: column; }
.info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f9fafb; font-size: 13px; }
.info-row:last-child { border-bottom: none; }
.info-label { color: #9ca3af; font-weight: 500; }
.info-value { color: #111827; font-weight: 600; }
.order-items { display: flex; flex-direction: column; margin-bottom: 16px; }
.order-item-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f9fafb; }
.order-item-info { display: flex; flex-direction: column; }
.order-item-name { font-size: 13px; font-weight: 600; color: #111827; }
.order-item-qty { font-size: 12px; color: #9ca3af; }
.order-item-total { font-size: 13px; font-weight: 600; color: #111827; }
.order-totals { display: flex; flex-direction: column; gap: 6px; padding-top: 8px; border-top: 2px solid #f3f4f6; }
.total-row { display: flex; justify-content: space-between; font-size: 13px; color: #6b7280; }
.total-row.grand { font-size: 15px; font-weight: 700; color: #111827; padding-top: 8px; border-top: 1px solid #e5e7eb; }
.tabs-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.tabs-header { display: flex; border-bottom: 1px solid #e5e7eb; }
.tab { padding: 12px 20px; font-size: 13px; font-weight: 500; color: #6b7280; background: none; border: none; cursor: pointer; font-family: 'Inter', sans-serif; border-bottom: 2px solid transparent; transition: all 0.15s; }
.tab:hover { color: #374151; }
.tab.active { color: #059669; border-bottom-color: #059669; font-weight: 600; }
.tab-content { padding: 16px; }
.timeline { display: flex; flex-direction: column; gap: 0; position: relative; padding-left: 20px; }
.timeline::before { content: ''; position: absolute; left: 7px; top: 8px; bottom: 8px; width: 2px; background: #e5e7eb; }
.timeline-step { display: flex; align-items: flex-start; gap: 14px; padding: 12px 0; position: relative; }
.tl-dot { width: 16px; height: 16px; border-radius: 50%; background: #e5e7eb; border: 3px solid #fff; flex-shrink: 0; z-index: 1; margin-left: -20px; }
.timeline-step.done .tl-dot { background: #059669; }
.timeline-step.current .tl-dot { background: #059669; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.2); }
.tl-content { display: flex; flex-direction: column; }
.tl-title { font-size: 13px; font-weight: 600; color: #111827; }
.timeline-step.current .tl-title { color: #059669; }
.tl-time { font-size: 12px; color: #9ca3af; }
.driver-card { display: flex; align-items: center; gap: 16px; }
.driver-avatar { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #2563eb); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; color: white; flex-shrink: 0; }
.driver-details { flex: 1; display: flex; flex-direction: column; }
.driver-name { font-size: 15px; font-weight: 600; color: #111827; }
.driver-vehicle { font-size: 13px; color: #6b7280; }
.driver-phone { font-size: 12px; color: #9ca3af; }
.ghost-btn { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px; background: #fff; font-size: 12px; font-weight: 500; color: #374151; cursor: pointer; font-family: 'Inter', sans-serif; }
.ghost-btn:hover { border-color: #059669; color: #059669; }
.ghost-btn svg { width: 14px; height: 14px; }
.no-driver { text-align: center; padding: 20px; }
.no-driver p { font-size: 13px; color: #9ca3af; margin-bottom: 12px; }
.not-found { text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; }
.not-found h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; color: #111827; margin-bottom: 8px; }
.not-found p { color: #9ca3af; }

@media (max-width: 768px) {
  .detail-grid { grid-template-columns: 1fr; }
  .detail-header { flex-wrap: wrap; }
}
</style>
