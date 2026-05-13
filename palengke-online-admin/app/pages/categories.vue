<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <div class="search-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input v-model="search" placeholder="Search categories..." />
        </div>
      </div>
      <button class="add-btn" @click="showAddModal = true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Category
      </button>
    </div>

    <div class="categories-grid">
      <div v-for="cat in filteredCategories" :key="cat.id" class="category-card">
        <div class="cat-icon-wrap" :style="{ background: cat.color + '20', color: cat.color }">
          <span class="cat-icon">{{ cat.icon }}</span>
        </div>
        <div class="cat-body">
          <h3 class="cat-name">{{ cat.name }}</h3>
          <span class="cat-count">{{ cat.products }} products</span>
          <div class="cat-bar-bg"><div class="cat-bar-fill" :style="{ width: cat.pct + '%', background: cat.color }"></div></div>
        </div>
        <div class="cat-actions">
          <button class="icon-btn" @click="editCategory(cat)" title="Edit">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </button>
          <button class="icon-btn danger" @click="deleteCategory(cat)" title="Delete">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" v-if="showAddModal" @click.self="showAddModal = false">
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ editingCat ? 'Edit Category' : 'New Category' }}</h3>
          <button class="modal-close" @click="showAddModal = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="modal-form">
            <div class="form-group">
              <label>Category Name</label>
              <input v-model="form.name" class="form-input" placeholder="e.g. Vegetables" />
            </div>
            <div class="form-group">
              <label>Icon (emoji)</label>
              <input v-model="form.icon" class="form-input" placeholder="e.g. 🥬" maxlength="2" />
            </div>
            <div class="form-group">
              <label>Color</label>
              <div class="color-picker">
                <div v-for="c in colors" :key="c" class="color-swatch" :class="{ active: form.color === c }" :style="{ background: c }" @click="form.color = c"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="reset-btn" @click="showAddModal = false">Cancel</button>
          <button class="save-btn" @click="saveCategory">{{ editingCat ? 'Update' : 'Create' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const search = ref('')
const showAddModal = ref(false)
const editingCat = ref(null)

const colors = ['#059669', '#3b82f6', '#f59e0b', '#dc2626', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316']

const categories = ref([
  { id: 1, name: 'Vegetables', icon: '🥬', color: '#059669', products: 48, pct: 100 },
  { id: 2, name: 'Fruits', icon: '🍎', color: '#dc2626', products: 36, pct: 75 },
  { id: 3, name: 'Seafood', icon: '🐟', color: '#3b82f6', products: 28, pct: 58 },
  { id: 4, name: 'Meat & Poultry', icon: '🍗', color: '#f59e0b', products: 32, pct: 67 },
  { id: 5, name: 'Pork', icon: '🥩', color: '#ec4899', products: 20, pct: 42 },
  { id: 6, name: 'Chicken', icon: '🐔', color: '#f97316', products: 18, pct: 38 },
  { id: 7, name: 'Bundles', icon: '📦', color: '#8b5cf6', products: 12, pct: 25 },
  { id: 8, name: 'Rice & Grains', icon: '🌾', color: '#14b8a6', products: 15, pct: 31 },
])

const form = ref({ name: '', icon: '', color: '#059669' })

const filteredCategories = computed(() => {
  if (!search.value) return categories.value
  const q = search.value.toLowerCase()
  return categories.value.filter(c => c.name.toLowerCase().includes(q))
})

const editCategory = (cat) => {
  editingCat.value = cat
  form.value = { name: cat.name, icon: cat.icon, color: cat.color }
  showAddModal.value = true
}

const deleteCategory = (cat) => {
  if (confirm(`Delete "${cat.name}"? This will not delete the products inside.`)) {
    categories.value = categories.value.filter(c => c.id !== cat.id)
  }
}

const saveCategory = () => {
  if (!form.value.name) return alert('Category name is required')
  if (editingCat.value) {
    const cat = categories.value.find(c => c.id === editingCat.value.id)
    if (cat) Object.assign(cat, form.value)
  } else {
    categories.value.push({
      id: Math.max(...categories.value.map(c => c.id)) + 1,
      name: form.value.name,
      icon: form.value.icon || '📁',
      color: form.value.color,
      products: 0,
      pct: 0,
    })
  }
  showAddModal.value = false
  editingCat.value = null
  form.value = { name: '', icon: '', color: '#059669' }
}
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; }
.search-box { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; min-width: 260px; }
.search-box:focus-within { border-color: #059669; }
.search-box svg { width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0; }
.search-box input { border: none; background: transparent; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; width: 100%; }
.add-btn { display: flex; align-items: center; gap: 6px; padding: 10px 18px; background: #059669; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; }
.add-btn:hover { background: #047857; }
.add-btn svg { width: 16px; height: 16px; }

.categories-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
.category-card { display: flex; align-items: center; gap: 16px; background: #fff; border-radius: 12px; padding: 18px; border: 1px solid #e5e7eb; transition: box-shadow 0.15s; }
.category-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.cat-icon-wrap { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 20px; }
.cat-body { flex: 1; min-width: 0; }
.cat-name { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 2px; }
.cat-count { font-size: 12px; color: #9ca3af; display: block; margin-bottom: 8px; }
.cat-bar-bg { height: 4px; background: #f3f4f6; border-radius: 2px; overflow: hidden; }
.cat-bar-fill { height: 100%; border-radius: 2px; }
.cat-actions { display: flex; gap: 4px; flex-shrink: 0; }
.icon-btn { width: 30px; height: 30px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #9ca3af; transition: all 0.15s; }
.icon-btn:hover { border-color: #059669; color: #059669; }
.icon-btn.danger:hover { border-color: #dc2626; color: #dc2626; }
.icon-btn svg { width: 14px; height: 14px; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; z-index: 200; backdrop-filter: blur(2px); }
.modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 440px; box-shadow: 0 16px 48px rgba(0,0,0,0.15); animation: modalIn 0.15s ease; }
@keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(8px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0; }
.modal-header h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 700; color: #111827; }
.modal-close { width: 30px; height: 30px; border: none; background: none; cursor: pointer; color: #9ca3af; display: flex; align-items: center; justify-content: center; border-radius: 6px; }
.modal-close:hover { background: #f3f4f6; color: #374151; }
.modal-close svg { width: 18px; height: 18px; }
.modal-body { padding: 20px 24px; }
.modal-form { display: flex; flex-direction: column; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 13px; font-weight: 600; color: #374151; }
.form-input { padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; }
.form-input:focus { border-color: #059669; }
.color-picker { display: flex; gap: 8px; flex-wrap: wrap; }
.color-swatch { width: 28px; height: 28px; border-radius: 50%; cursor: pointer; border: 2px solid transparent; transition: all 0.15s; }
.color-swatch:hover { transform: scale(1.15); }
.color-swatch.active { border-color: #111827; transform: scale(1.15); }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 0 24px 20px; }
.save-btn { padding: 10px 20px; background: #059669; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
.save-btn:hover { background: #047857; }
.reset-btn { padding: 10px 20px; background: #fff; color: #374151; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; }
.reset-btn:hover { border-color: #9ca3af; }

@media (max-width: 600px) { .categories-grid { grid-template-columns: 1fr; } }
</style>
