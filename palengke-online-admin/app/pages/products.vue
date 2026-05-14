<template>
  <div class="page-shell">
    <div class="tabs-nav">
      <button class="tab-btn" :class="{ active: tab === 'all' }" @click="tab = 'all'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        All Products
        <span class="tab-badge">{{ store.products.length }}</span>
      </button>
      <button class="tab-btn" :class="{ active: tab === 'pending' }" @click="tab = 'pending'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Pending Reviews
        <span class="tab-badge warn" v-if="store.pendingReviews.length > 0">{{ store.pendingReviews.length }}</span>
      </button>
    </div>

    <div v-if="tab === 'all'">
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
          <select v-model="stockFilter" class="filter-select">
            <option value="">All Stock</option>
            <option value="in_stock">In Stock</option>
            <option value="out_of_stock">Out of Stock</option>
          </select>
        </div>
        <div class="toolbar-right">
          <span class="result-count">{{ filtered.length }} products</span>
          <button class="add-btn" @click="openAdd">+ Add Product</button>
        </div>
      </div>

      <div class="table-card">
        <table class="data-table">
          <thead>
            <tr><th>Product</th><th>Category</th><th>Seller</th><th>Price</th><th>Unit</th><th>Stock</th><th>Status</th><th></th></tr>
          </thead>
          <tbody>
            <tr v-for="p in filtered" :key="p.id">
              <td>
                  <div class="cell-product" @click="openDetail(p)" style="cursor:pointer">
                    <img v-if="p.image" :src="p.image" class="product-thumb-img" />
                    <div v-else class="product-thumb">{{ p.name.charAt(0) }}</div>
                    <div class="name-cell">{{ p.name }}</div>
                  </div>
              </td>
              <td>{{ categoryLabel(p.category) }}</td>
              <td>{{ p.seller }}</td>
              <td>₱{{ p.price }}</td>
              <td>{{ p.unit }}</td>
              <td>
                <span :class="{ 'stock-low': p.stock > 0 && p.stock < 30, 'stock-out': p.stock === 0 }">{{ p.stock }}</span>
              </td>
              <td><span class="status-badge" :class="p.status">{{ p.status === 'in_stock' ? 'In Stock' : 'Out of Stock' }}</span></td>
              <td>
                <div class="row-actions">
                  <button class="row-btn edit" @click="openEdit(p)" title="Edit">✎</button>
                  <button class="row-btn delete" @click="confirmDelete(p)" title="Delete">✕</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="tab === 'pending'">
      <div class="page-toolbar">
        <div class="toolbar-left">
          <div class="search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input v-model="pendingSearch" placeholder="Search pending products..." />
          </div>
          <select v-model="pendingCategory" class="filter-select">
            <option value="">All Categories</option>
            <option value="vegetables">Vegetables</option>
            <option value="fruits">Fruits</option>
            <option value="seafood">Seafood</option>
            <option value="meat">Meat & Poultry</option>
            <option value="bundles">Bundles</option>
          </select>
        </div>
        <span class="result-count">{{ filteredPending.length }} pending</span>
      </div>

      <div class="table-card">
        <table class="data-table">
          <thead>
            <tr><th>Product</th><th>Category</th><th>Seller</th><th>Price</th><th>Unit</th><th>Stock</th><th>Submitted</th><th></th></tr>
          </thead>
          <tbody>
            <tr v-for="r in filteredPending" :key="r.id">
              <td>
                <div class="cell-product">
                    <img v-if="r.image" :src="r.image" class="product-thumb-img" />
                    <div v-else class="product-thumb pending">{{ r.name.charAt(0) }}</div>
                    <div><div class="name-cell">{{ r.name }}</div><div class="sub-cell">{{ r.description }}</div></div>
                  </div>
              </td>
              <td>{{ categoryLabel(r.category) }}</td>
              <td>{{ r.seller }}</td>
              <td>₱{{ r.price }}</td>
              <td>{{ r.unit }}</td>
              <td>{{ r.stock }}</td>
              <td class="date-cell">{{ r.submittedAt }}</td>
              <td>
                <div class="review-actions">
                  <button class="review-btn approve" @click="store.approveReview(r.id)" title="Approve">✓</button>
                  <button class="review-btn reject" @click="openReject(r)" title="Reject">✕</button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredPending.length === 0">
              <td colspan="8" class="empty-cell">No pending product reviews</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal-overlay" v-if="showDetail" @click="showDetail = false">
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">{{ detailProduct.name }}</span>
          <span class="status-badge" :class="detailProduct.status">{{ detailProduct.status === 'in_stock' ? 'In Stock' : 'Out of Stock' }}</span>
          <button class="modal-close" @click="showDetail = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <img v-if="detailProduct.image" :src="detailProduct.image" class="detail-img" />
          <div class="detail-grid">
            <div class="detail-item"><span class="dl">Category</span><span class="dv">{{ categoryLabel(detailProduct.category) }}</span></div>
            <div class="detail-item"><span class="dl">Seller</span><span class="dv">{{ detailProduct.seller }}</span></div>
            <div class="detail-item"><span class="dl">Price</span><span class="dv">₱{{ detailProduct.price }} / {{ detailProduct.unit }}</span></div>
            <div class="detail-item"><span class="dl">Stock</span><span class="dv" :class="{ low: detailProduct.stock < 30 }">{{ detailProduct.stock }} {{ detailProduct.unit }}</span></div>
          </div>
          <div class="stock-bar">
            <div class="stock-bar-track">
              <div class="stock-bar-fill" :style="{ width: Math.min(100, (detailProduct.stock / 200) * 100) + '%' }" :class="{ low: detailProduct.stock < 30 && detailProduct.stock > 0, empty: detailProduct.stock === 0 }"></div>
            </div>
            <span class="stock-status" v-if="detailProduct.stock === 0">Out of Stock</span>
            <span class="stock-status" v-else-if="detailProduct.stock < 30">Low Stock</span>
            <span class="stock-status good" v-else>In Stock</span>
          </div>
        </div>
        <div class="modal-actions-single">
          <button class="modal-btn secondary" @click="showDetail = false">Close</button>
          <button class="modal-btn primary" @click="showDetail = false; openEdit(detailProduct)">Edit Product</button>
        </div>
      </div>
    </div>

    <div class="modal-overlay" v-if="showForm" @click="closeForm">
      <div class="modal-card" @click.stop>
        <div class="modal-top">
          <span class="modal-title">{{ editing ? 'Edit Product' : 'Add Product' }}</span>
          <button class="modal-close" @click="closeForm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body form-body">
          <div class="form-row">
            <div class="form-group flex-1">
              <label>Product Name <span class="req">*</span></label>
              <input v-model="form.name" type="text" placeholder="e.g. Fresh Tomatoes" class="form-input" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group flex-1">
              <label>Category <span class="req">*</span></label>
              <select v-model="form.category" class="form-input">
                <option value="">Select category...</option>
                <option value="vegetables">Vegetables</option>
                <option value="fruits">Fruits</option>
                <option value="seafood">Seafood</option>
                <option value="meat">Meat & Poultry</option>
                <option value="bundles">Bundles</option>
              </select>
            </div>
            <div class="form-group flex-1">
              <label>Seller <span class="req">*</span></label>
              <select v-model="form.sellerId" class="form-input" @change="onSellerChange">
                <option value="">Select seller...</option>
                <option v-for="s in sellers" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>
          </div>
          <div class="form-row three-col">
            <div class="form-group">
              <label>Price (₱) <span class="req">*</span></label>
              <input v-model="form.price" type="number" min="0" placeholder="0" class="form-input" />
            </div>
            <div class="form-group">
              <label>Unit <span class="req">*</span></label>
              <select v-model="form.unit" class="form-input">
                <option value="">Unit...</option>
                <option value="kg">kg</option>
                <option value="pc">pc</option>
                <option value="bundle">bundle</option>
                <option value="set">set</option>
                <option value="pack">pack</option>
                <option value="sack">sack</option>
              </select>
            </div>
            <div class="form-group">
              <label>Stock <span class="req">*</span></label>
              <input v-model="form.stock" type="number" min="0" placeholder="0" class="form-input" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group flex-1">
              <label>Image URL</label>
              <input v-model="form.image" type="url" placeholder="https://images.unsplash.com/photo-..." class="form-input" />
            </div>
          </div>
          <div class="image-preview" v-if="form.image">
            <img :src="form.image" class="preview-img" />
            <span class="preview-label">Preview</span>
          </div>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="closeForm">Cancel</button>
          <button class="modal-btn primary" :disabled="!formValid" @click="saveProduct">{{ editing ? 'Update' : 'Add' }} Product</button>
        </div>
      </div>
    </div>

    <div class="modal-overlay" v-if="showRejectModal" @click="closeReject">
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">Reject Product</span>
          <button class="modal-close" @click="closeReject">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <p class="confirm-text">Reject <strong>{{ rejectTarget?.name }}</strong> from <strong>{{ rejectTarget?.seller }}</strong>?</p>
          <textarea v-model="rejectReason" class="reject-input" placeholder="Reason for rejection (optional)..." rows="3"></textarea>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="closeReject">Cancel</button>
          <button class="modal-btn danger" @click="handleReject">Reject Product</button>
        </div>
      </div>
    </div>

    <div class="modal-overlay" v-if="showDelete" @click="showDelete = false">
      <div class="modal-card modal-sm" @click.stop>
        <div class="modal-top">
          <span class="modal-title">Delete Product</span>
          <button class="modal-close" @click="showDelete = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <p class="confirm-text">Are you sure you want to delete <strong>{{ deleteTarget?.name }}</strong>?</p>
          <p class="confirm-hint">This action cannot be undone.</p>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="showDelete = false">Cancel</button>
          <button class="modal-btn danger" @click="handleDelete">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const store = useProductsStore()
const sellersStore = useSellersStore()
const sellers = computed(() => sellersStore.sellers)

const tab = ref('all')
const search = ref('')
const categoryFilter = ref('')
const stockFilter = ref('')
const pendingSearch = ref('')
const pendingCategory = ref('')

const filteredPending = computed(() => {
  return store.pendingReviews.filter(r => {
    const q = pendingSearch.value.toLowerCase()
    const matchSearch = !q || r.name.toLowerCase().includes(q) || r.seller.toLowerCase().includes(q)
    const matchCat = !pendingCategory.value || r.category === pendingCategory.value
    return matchSearch && matchCat
  })
})

const filtered = computed(() => {
  return store.products.filter(p => {
    const q = search.value.toLowerCase()
    const matchSearch = !q || p.name.toLowerCase().includes(q) || p.seller.toLowerCase().includes(q)
    const matchCat = !categoryFilter.value || p.category === categoryFilter.value
    const matchStock = !stockFilter.value || p.status === stockFilter.value
    return matchSearch && matchCat && matchStock
  })
})

function categoryLabel(cat) {
  const map = { vegetables: 'Vegetables', fruits: 'Fruits', seafood: 'Seafood', meat: 'Meat & Poultry', bundles: 'Bundles' }
  return map[cat] || cat
}

const showDetail = ref(false)
const detailProduct = ref(null)
function openDetail(p) {
  detailProduct.value = p
  showDetail.value = true
}

const showForm = ref(false)
const editing = ref(false)
const form = ref({ name: '', category: '', sellerId: '', price: '', unit: '', stock: '', image: '' })

function resetForm() {
  form.value = { name: '', category: '', sellerId: '', price: '', unit: '', stock: '', image: '' }
}

function openAdd() {
  editing.value = false
  resetForm()
  showForm.value = true
}

function openEdit(p) {
  editing.value = p
  form.value = {
    name: p.name,
    category: p.category,
    sellerId: p.sellerId,
    price: p.price,
    unit: p.unit,
    stock: p.stock,
    image: p.image || '',
  }
  showForm.value = true
}

function closeForm() {
  showForm.value = false
  resetForm()
}

function onSellerChange() {
  const s = sellers.value.find(s => s.id === Number(form.value.sellerId))
}

const formValid = computed(() => {
  return form.value.name && form.value.category && form.value.sellerId && form.value.price !== '' && form.value.unit && form.value.stock !== ''
})

function saveProduct() {
  if (!formValid.value) return
  const s = sellers.value.find(s => s.id === Number(form.value.sellerId))
  const data = {
    ...form.value,
    seller: s ? s.name : '',
    sellerId: Number(form.value.sellerId),
    price: Number(form.value.price),
    stock: Number(form.value.stock),
    image: form.value.image,
  }
  if (editing.value) {
    store.updateProduct(editing.value.id, data)
  } else {
    store.addProduct(data)
  }
  closeForm()
}

const showDelete = ref(false)
const deleteTarget = ref(null)
function confirmDelete(p) {
  deleteTarget.value = p
  showDelete.value = true
}
function handleDelete() {
  if (deleteTarget.value) store.deleteProduct(deleteTarget.value.id)
  showDelete.value = false
  deleteTarget.value = null
}

const showRejectModal = ref(false)
const rejectTarget = ref(null)
const rejectReason = ref('')
function openReject(r) {
  rejectTarget.value = r
  rejectReason.value = ''
  showRejectModal.value = true
}
function closeReject() {
  showRejectModal.value = false
  rejectTarget.value = null
  rejectReason.value = ''
}
function handleReject() {
  if (rejectTarget.value) store.rejectReview(rejectTarget.value.id)
  closeReject()
}
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.toolbar-right { display: flex; align-items: center; gap: 12px; }
.search-box { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; min-width: 260px; }
.search-box:focus-within { border-color: #059669; }
.search-box svg { width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0; }
.search-box input { border: none; background: transparent; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; width: 100%; }
.filter-select { padding: 8px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #374151; background: #fff; cursor: pointer; outline: none; }
.filter-select:focus { border-color: #059669; }
.result-count { font-size: 13px; color: #9ca3af; white-space: nowrap; }
.add-btn { padding: 9px 18px; background: #059669; color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; transition: background 0.15s; white-space: nowrap; }
.add-btn:hover { background: #047857; }

.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; white-space: nowrap; }
.data-table td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.cell-product { display: flex; align-items: center; gap: 10px; }
.product-thumb { width: 34px; height: 34px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #6b7280; flex-shrink: 0; }
.product-thumb-img { width: 34px; height: 34px; border-radius: 8px; object-fit: cover; flex-shrink: 0; }
.name-cell { font-weight: 600; color: #111827; }
.stock-low { color: #d97706; font-weight: 600; }
.stock-out { color: #dc2626; font-weight: 600; }

.row-actions { display: flex; gap: 4px; }
.row-btn { width: 28px; height: 28px; border-radius: 5px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px; transition: all 0.15s; }
.row-btn.edit { color: #059669; }
.row-btn.edit:hover { border-color: #059669; background: #f0fdf4; }
.row-btn.delete { color: #dc2626; }
.row-btn.delete:hover { border-color: #dc2626; background: #fef2f2; }
.sub-cell { font-size: 11px; color: #9ca3af; margin-top: 2px; line-height: 1.3; max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.empty-cell { text-align: center; color: #9ca3af; padding: 40px 16px !important; font-style: italic; }
.product-thumb.pending { background: #fef3c7; color: #d97706; }

.review-actions { display: flex; gap: 4px; }
.review-btn { width: 32px; height: 32px; border-radius: 6px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; transition: all 0.15s; }
.review-btn.approve { color: #059669; }
.review-btn.approve:hover { border-color: #059669; background: #f0fdf4; }
.review-btn.reject { color: #dc2626; }
.review-btn.reject:hover { border-color: #dc2626; background: #fef2f2; }

.reject-input { width: 100%; padding: 12px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #374151; outline: none; resize: vertical; margin-top: 12px; }
.reject-input:focus { border-color: #dc2626; }

.tabs-nav { display: flex; gap: 4px; background: #fff; border-radius: 12px; padding: 4px; border: 1px solid #e5e7eb; width: fit-content; }
.tab-btn { display: flex; align-items: center; gap: 6px; padding: 10px 18px; border: none; background: transparent; border-radius: 8px; font-size: 13px; font-weight: 500; font-family: 'Inter', sans-serif; color: #6b7280; cursor: pointer; transition: all 0.15s; }
.tab-btn:hover { color: #374151; }
.tab-btn.active { background: #059669; color: #fff; }
.tab-btn svg { width: 16px; height: 16px; }
.tab-badge { background: #fff; color: #059669; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 4px; min-width: 18px; text-align: center; }
.tab-badge.warn { background: #fef3c7; color: #d97706; }
.tab-btn.active .tab-badge { background: rgba(255,255,255,0.25); color: #fff; }
.tab-btn.active .tab-badge.warn { background: rgba(255,255,255,0.25); color: #fff; }

.status-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.status-badge.in_stock { background: #f0fdf4; color: #059669; }
.status-badge.out_of_stock { background: #fef2f2; color: #dc2626; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 32px; backdrop-filter: blur(2px); }
.modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.modal-card.modal-sm { max-width: 420px; }
.modal-top { display: flex; align-items: center; gap: 10px; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; }
.modal-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 700; color: #111827; flex: 1; }
.modal-close { width: 32px; height: 32px; border: none; background: none; cursor: pointer; color: #9ca3af; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-close:hover { background: #f3f4f6; color: #374151; }
.modal-close svg { width: 18px; height: 18px; }
.modal-body { padding: 20px 24px; }
.detail-img { width: 100%; max-height: 180px; object-fit: cover; border-radius: 10px; margin-bottom: 16px; }
.image-preview { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; }
.preview-img { width: 48px; height: 48px; border-radius: 6px; object-fit: cover; }
.preview-label { font-size: 12px; color: #9ca3af; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
.detail-item { display: flex; flex-direction: column; gap: 2px; }
.dl { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.3px; }
.dv { font-size: 14px; font-weight: 600; color: #111827; }
.dv.low { color: #dc2626; }
.stock-bar { margin-bottom: 20px; }
.stock-bar-track { height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden; margin-bottom: 6px; }
.stock-bar-fill { height: 100%; border-radius: 4px; background: #059669; transition: width 0.3s; }
.stock-bar-fill.low { background: #d97706; }
.stock-bar-fill.empty { background: #dc2626; }
.stock-status { font-size: 12px; font-weight: 600; color: #dc2626; }
.stock-status.good { color: #059669; }
.confirm-text { font-size: 14px; color: #374151; margin: 0 0 8px; line-height: 1.5; }
.confirm-hint { font-size: 12px; color: #9ca3af; margin: 0; }

.form-body { display: flex; flex-direction: column; gap: 14px; }
.form-row { display: flex; gap: 12px; }
.form-row.three-col > .form-group { flex: 1; flex-basis: 0; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.flex-1 { flex: 1; }
.form-group label { font-size: 12px; font-weight: 600; color: #374151; }
.req { color: #dc2626; }
.form-input { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; background: #fff; outline: none; transition: border-color 0.2s; }
.form-input:focus { border-color: #059669; }
.form-input::placeholder { color: #9ca3af; }
select.form-input { cursor: pointer; }

.modal-actions { display: flex; gap: 10px; padding: 16px 24px 20px; border-top: 1px solid #f3f4f6; }
.modal-actions-single { display: flex; gap: 10px; padding: 16px 24px 20px; border-top: 1px solid #f3f4f6; }
.modal-btn { flex: 1; padding: 11px; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.15s; border: none; }
.modal-btn.primary { background: #059669; color: #fff; }
.modal-btn.primary:hover { background: #047857; }
.modal-btn.primary:disabled { opacity: 0.4; cursor: not-allowed; }
.modal-btn.secondary { background: #f3f4f6; color: #6b7280; }
.modal-btn.secondary:hover { background: #e5e7eb; }
.modal-btn.danger { background: #dc2626; color: #fff; }
.modal-btn.danger:hover { background: #b91c1c; }

@media (max-width: 768px) {
  .form-row { flex-direction: column; }
  .form-row.three-col { flex-direction: column; }
}
</style>
