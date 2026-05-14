<template>
  <div class="detail-page">
    <button class="back-btn" @click="goBack">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Sellers
    </button>

    <div v-if="seller" class="detail-content">
      <div class="detail-header">
        <div class="detail-avatar" :style="{ background: '#059669' }">{{ seller.name.charAt(0) }}</div>
        <div class="detail-title">
          <h1>{{ seller.name }}</h1>
          <span class="detail-subtitle">{{ seller.location }} · {{ seller.category }}</span>
        </div>
        <div class="detail-actions">
          <span class="status-badge-lg" :class="seller.status">{{ seller.status === 'active' ? 'Active' : 'Inactive' }}</span>
          <button class="action-btn" :class="{ suspended: seller.status === 'active' }" @click="toggleStatus">
            {{ seller.status === 'active' ? 'Suspend' : 'Activate' }}
          </button>
        </div>
      </div>

      <div class="detail-grid">
        <div class="info-card">
          <h3>Store Information</h3>
          <div class="info-rows">
            <div class="info-row"><span class="info-label">Owner</span><span class="info-value">{{ seller.owner }}</span></div>
            <div class="info-row"><span class="info-label">Category</span><span class="info-value">{{ seller.category }}</span></div>
            <div class="info-row"><span class="info-label">Location</span><span class="info-value">{{ seller.location }}</span></div>
            <div class="info-row"><span class="info-label">Products</span><span class="info-value">{{ seller.products }}</span></div>
            <div class="info-row"><span class="info-label">Revenue</span><span class="info-value">₱{{ seller.revenue.toLocaleString() }}</span></div>
            <div class="info-row"><span class="info-label">Rating</span><span class="info-value stars">★ {{ seller.rating }}</span></div>
            <div class="info-row"><span class="info-label">Joined</span><span class="info-value">{{ seller.joined }}</span></div>
          </div>
        </div>

        <div class="info-card">
          <h3>Contact</h3>
          <div class="info-rows">
            <div class="info-row"><span class="info-label">Phone</span><span class="info-value">{{ seller.contact?.phone || 'N/A' }}</span></div>
            <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ seller.contact?.email || 'N/A' }}</span></div>
          </div>
          <div class="info-card-actions">
            <button class="ghost-btn" @click="alert('Calling ' + seller.owner)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              Call
            </button>
            <button class="ghost-btn" @click="alert('Emailing ' + seller.owner)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              Email
            </button>
          </div>
        </div>
      </div>

      <div class="tabs-card">
        <div class="tabs-header">
          <button class="tab" :class="{ active: activeTab === 'products' }" @click="activeTab = 'products'">Products ({{ seller.products }})</button>
          <button class="tab" :class="{ active: activeTab === 'orders' }" @click="activeTab = 'orders'">Recent Orders</button>
          <button class="tab" :class="{ active: activeTab === 'analytics' }" @click="activeTab = 'analytics'">Analytics</button>
        </div>

        <div v-if="activeTab === 'products'" class="tab-content">
          <div class="table-card">
            <table class="data-table">
              <thead><tr><th>Product</th><th>Price</th><th>Unit</th><th>Stock</th><th></th></tr></thead>
              <tbody>
                <tr v-for="p in mockProducts" :key="p.id">
                  <td><div class="cell-product"><div class="product-thumb">{{ p.name.charAt(0) }}</div><span class="name-cell">{{ p.name }}</span></div></td>
                  <td>₱{{ p.price }}</td>
                  <td>{{ p.unit }}</td>
                  <td>{{ p.stock }}</td>
                  <td><span class="status-badge" :class="p.stock > 0 ? 'in_stock' : 'out_of_stock'">{{ p.stock > 0 ? 'In Stock' : 'Out of Stock' }}</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="activeTab === 'orders'" class="tab-content">
          <div class="table-card">
            <table class="data-table">
              <thead><tr><th>Order ID</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
              <tbody>
                <tr v-for="o in mockOrders" :key="o.id">
                  <td class="id-cell">#{{ o.id }}</td>
                  <td>{{ o.customer }}</td>
                  <td>{{ o.items }}</td>
                  <td>₱{{ o.total.toLocaleString() }}</td>
                  <td><span class="status-badge" :class="o.status">{{ o.status }}</span></td>
                  <td class="date-cell">{{ o.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="activeTab === 'analytics'" class="tab-content">
          <div class="mini-charts">
            <div class="mini-card">
              <span class="mini-label">Monthly Revenue</span>
              <span class="mini-value">₱{{ (seller.revenue / 12).toLocaleString() }}</span>
              <div class="mini-bar-bg"><div class="mini-bar-fill" style="width: 76%"></div></div>
            </div>
            <div class="mini-card">
              <span class="mini-label">Avg Order Value</span>
              <span class="mini-value">₱{{ Math.round(seller.revenue / 30).toLocaleString() }}</span>
              <div class="mini-bar-bg"><div class="mini-bar-fill" style="width: 62%"></div></div>
            </div>
            <div class="mini-card">
              <span class="mini-label">Growth Rate</span>
              <span class="mini-value" style="color:#059669">+18.5%</span>
              <div class="mini-bar-bg"><div class="mini-bar-fill" style="width: 68%; background:#059669"></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="not-found">
      <h2>Seller not found</h2>
      <p>The seller you're looking for doesn't exist.</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
const route = useRoute()
const sellersStore = useSellersStore()

const seller = computed(() => sellersStore.getSellerById(Number(route.params.id)))
const activeTab = ref('products')

const mockProducts = computed(() => {
  if (!seller.value) return []
  return sellersStore.getSellerProducts(seller.value.id)
})

const mockOrders = computed(() => {
  if (!seller.value) return []
  return sellersStore.getSellerOrders(seller.value.id)
})

const toggleStatus = () => {
  if (seller.value) sellersStore.toggleStatus(seller.value.id)
}

const goBack = () => navigateTo('/sellers')
</script>

<style scoped>
.detail-page { display: flex; flex-direction: column; gap: 20px; }
.back-btn { display: inline-flex; align-items: center; gap: 6px; background: none; border: none; font-size: 13px; font-weight: 500; color: #059669; cursor: pointer; font-family: 'Inter', sans-serif; padding: 0; width: fit-content; }
.back-btn svg { width: 16px; height: 16px; }
.back-btn:hover { color: #047857; }
.detail-content { display: flex; flex-direction: column; gap: 20px; }
.detail-header { display: flex; align-items: center; gap: 16px; background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; }
.detail-avatar { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; color: white; flex-shrink: 0; }
.detail-title { flex: 1; }
.detail-title h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px; font-weight: 700; color: #111827; margin: 0; }
.detail-subtitle { font-size: 13px; color: #9ca3af; }
.detail-actions { display: flex; align-items: center; gap: 10px; }
.status-badge-lg { padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; }
.status-badge-lg.active { background: #f0fdf4; color: #059669; }
.status-badge-lg.inactive { background: #f3f4f6; color: #6b7280; }
.action-btn { padding: 8px 16px; border-radius: 8px; border: 1.5px solid #e5e7eb; background: #fff; font-size: 13px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; color: #374151; }
.action-btn:hover { border-color: #9ca3af; }
.action-btn.suspended { border-color: #fecaca; color: #dc2626; }
.action-btn.suspended:hover { background: #fef2f2; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.info-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.info-card h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 16px; }
.info-rows { display: flex; flex-direction: column; }
.info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f9fafb; font-size: 13px; }
.info-row:last-child { border-bottom: none; }
.info-label { color: #9ca3af; font-weight: 500; }
.info-value { color: #111827; font-weight: 600; }
.info-value.stars { color: #f59e0b; }
.info-card-actions { display: flex; gap: 8px; margin-top: 16px; }
.ghost-btn { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px; background: #fff; font-size: 12px; font-weight: 500; color: #374151; cursor: pointer; font-family: 'Inter', sans-serif; }
.ghost-btn:hover { border-color: #059669; color: #059669; }
.ghost-btn svg { width: 14px; height: 14px; }
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
.cell-product { display: flex; align-items: center; gap: 10px; }
.product-thumb { width: 28px; height: 28px; border-radius: 6px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #6b7280; }
.name-cell { font-weight: 600; color: #111827; }
.id-cell { font-weight: 600; color: #059669; }
.date-cell { color: #9ca3af; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.delivered { background: #f0fdf4; color: #059669; }
.status-badge.transit { background: #eff6ff; color: #3b82f6; }
.status-badge.cancelled { background: #fef2f2; color: #dc2626; }
.status-badge.in_stock { background: #f0fdf4; color: #059669; }
.status-badge.out_of_stock { background: #fef2f2; color: #dc2626; }
.mini-charts { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
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
  .mini-charts { grid-template-columns: 1fr; }
  .detail-header { flex-wrap: wrap; }
}
</style>
