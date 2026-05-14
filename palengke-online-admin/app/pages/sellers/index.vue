<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <div class="search-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input v-model="search" placeholder="Search sellers..." />
        </div>
      </div>
      <span class="result-count">{{ filteredSellers.length }} sellers</span>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr><th>Store</th><th>Owner</th><th>Category</th><th>Products</th><th>Revenue</th><th>Rating</th><th>Status</th><th>Joined</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="s in filteredSellers" :key="s.id">
            <td>
              <div class="cell-store">
                <div class="store-avatar">{{ s.name.charAt(0) }}</div>
                <div><div class="name-cell">{{ s.name }}</div><div class="sub-cell">{{ s.location }}</div></div>
              </div>
            </td>
            <td>{{ s.owner }}</td>
            <td>{{ s.category }}</td>
            <td>{{ s.products }}</td>
            <td>₱{{ s.revenue.toLocaleString() }}</td>
            <td><span class="stars">★</span> {{ s.rating }}</td>
            <td><span class="status-badge" :class="s.status">{{ s.status === 'active' ? 'Active' : 'Inactive' }}</span></td>
            <td class="date-cell">{{ s.joined }}</td>
            <td><button class="action-btn" @click="goToSeller(s.id)">...</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
const sellersStore = useSellersStore()

const search = ref('')

const filteredSellers = computed(() => {
  if (!search.value) return sellersStore.sellers
  const q = search.value.toLowerCase()
  return sellersStore.sellers.filter(s => s.name.toLowerCase().includes(q) || s.owner.toLowerCase().includes(q) || s.category.toLowerCase().includes(q))
})

const goToSeller = (id) => navigateTo('/sellers/' + id)
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.search-box { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; min-width: 260px; }
.search-box:focus-within { border-color: #059669; }
.search-box svg { width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0; }
.search-box input { border: none; background: transparent; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; width: 100%; }
.result-count { font-size: 13px; color: #9ca3af; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.cell-store { display: flex; align-items: center; gap: 10px; }
.store-avatar { width: 34px; height: 34px; border-radius: 8px; background: linear-gradient(135deg, #059669, #047857); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: white; flex-shrink: 0; }
.name-cell { font-weight: 600; color: #111827; }
.sub-cell { font-size: 12px; color: #9ca3af; }
.date-cell { color: #9ca3af; }
.stars { color: #f59e0b; }
.action-btn { background: none; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 10px; cursor: pointer; color: #9ca3af; font-size: 16px; transition: all 0.15s; }
.action-btn:hover { border-color: #059669; color: #059669; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.active { background: #f0fdf4; color: #059669; }
.status-badge.inactive { background: #f3f4f6; color: #6b7280; }
</style>
