<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <div class="search-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input v-model="search" placeholder="Search products..." />
        </div>
        <select v-model="categoryFilter" class="filter-select">
          <option value="">All Categories</option>
          <option value="vegetables">Vegetables</option>
          <option value="fruits">Fruits</option>
          <option value="seafood">Seafood</option>
          <option value="meat">Meat & Poultry</option>
          <option value="bundles">Bundles</option>
        </select>
      </div>
      <span class="result-count">{{ filteredProducts.length }} products</span>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr><th>Product</th><th>Category</th><th>Seller</th><th>Price</th><th>Unit</th><th>Stock</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="p in filteredProducts" :key="p.id">
            <td>
              <div class="cell-product">
                <div class="product-thumb">{{ p.name.charAt(0) }}</div>
                <div class="name-cell">{{ p.name }}</div>
              </div>
            </td>
            <td>{{ p.category }}</td>
            <td>{{ p.seller }}</td>
            <td>₱{{ p.price }}</td>
            <td>{{ p.unit }}</td>
            <td>{{ p.stock }}</td>
            <td><span class="status-badge" :class="p.status">{{ p.status === 'in_stock' ? 'In Stock' : 'Out of Stock' }}</span></td>
            <td><button class="action-btn">...</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const search = ref('')
const categoryFilter = ref('')

const products = ref([
  { id: 1, name: 'Fresh Tomatoes', category: 'Vegetables', seller: "Juan's Farm", price: 60, unit: 'kg', stock: 120, status: 'in_stock' },
  { id: 2, name: 'Organic Eggplant', category: 'Vegetables', seller: "Juan's Farm", price: 45, unit: 'kg', stock: 85, status: 'in_stock' },
  { id: 3, name: 'Ripe Mangoes', category: 'Fruits', seller: "Ana's Store", price: 120, unit: 'kg', stock: 50, status: 'in_stock' },
  { id: 4, name: 'Fresh Bangus', category: 'Seafood', seller: 'Pedro Merchants', price: 180, unit: 'kg', stock: 30, status: 'in_stock' },
  { id: 5, name: 'Pork Belly', category: 'Meat & Poultry', seller: "Liza's Shop", price: 280, unit: 'kg', stock: 0, status: 'out_of_stock' },
  { id: 6, name: 'Pechay Baguio', category: 'Vegetables', seller: 'FreshMart', price: 35, unit: 'bundle', stock: 200, status: 'in_stock' },
  { id: 7, name: 'Green Papaya', category: 'Vegetables', seller: "Rosa's Produce", price: 40, unit: 'pc', stock: 65, status: 'in_stock' },
  { id: 8, name: 'Chicken Leg Quarters', category: 'Meat & Poultry', seller: "Ben's Butchery", price: 190, unit: 'kg', stock: 45, status: 'in_stock' },
  { id: 9, name: 'Imported Grapes', category: 'Fruits', seller: "Ana's Store", price: 250, unit: 'kg', stock: 20, status: 'in_stock' },
  { id: 10, name: 'Fresh Tilapia', category: 'Seafood', seller: 'Pedro Merchants', price: 140, unit: 'kg', stock: 55, status: 'in_stock' },
  { id: 11, name: 'Vegetable Bundle', category: 'Bundles', seller: 'FreshMart', price: 350, unit: 'set', stock: 25, status: 'in_stock' },
  { id: 12, name: 'Beef Lean Meat', category: 'Meat & Poultry', seller: "Ben's Butchery", price: 380, unit: 'kg', stock: 0, status: 'out_of_stock' },
])

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchSearch = !search.value || p.name.toLowerCase().includes(search.value.toLowerCase()) || p.seller.toLowerCase().includes(search.value.toLowerCase())
    const matchCat = !categoryFilter.value || p.category.toLowerCase() === categoryFilter.value
    return matchSearch && matchCat
  })
})
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.search-box { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; min-width: 260px; }
.search-box:focus-within { border-color: #059669; }
.search-box svg { width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0; }
.search-box input { border: none; background: transparent; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; width: 100%; }
.filter-select { padding: 8px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #374151; background: #fff; cursor: pointer; outline: none; }
.filter-select:focus { border-color: #059669; }
.result-count { font-size: 13px; color: #9ca3af; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.cell-product { display: flex; align-items: center; gap: 10px; }
.product-thumb { width: 34px; height: 34px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #6b7280; flex-shrink: 0; }
.name-cell { font-weight: 600; color: #111827; }
.action-btn { background: none; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 10px; cursor: pointer; color: #9ca3af; font-size: 16px; transition: all 0.15s; }
.action-btn:hover { border-color: #059669; color: #059669; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.in_stock { background: #f0fdf4; color: #059669; }
.status-badge.out_of_stock { background: #fef2f2; color: #dc2626; }
</style>
