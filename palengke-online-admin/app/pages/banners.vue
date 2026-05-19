<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <select v-model="activeFilter" class="filter-select">
          <option value="">All</option>
          <option value="true">Active</option>
          <option value="false">Inactive</option>
        </select>
      </div>
      <button class="add-btn" @click="openAddModal">
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Banner
      </button>
    </div>

    <div class="banners-grid">
      <div
        v-for="b in filteredBanners"
        :key="b.id"
        class="banner-card"
        :class="{ inactive: !b.active }"
      >
        <div
          class="banner-preview"
          :style="{ background: 'linear-gradient(135deg, #059669, #047857)' }"
        >
          <div class="banner-placeholder">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <rect x="3" y="3" width="18" height="18" rx="2" />
              <circle cx="8.5" cy="8.5" r="1.5" />
              <path d="M21 15l-5-5L5 21" />
            </svg>
          </div>
          <div class="banner-overlay-text">
            <span class="banner-title-preview">{{ b.title }}</span>
            <span class="banner-sub-preview">{{ b.subtitle }}</span>
          </div>
          <span class="banner-order-badge">#{{ b.sortOrder }}</span>
        </div>
        <div class="banner-body">
          <div class="banner-info">
            <h3 class="banner-title">{{ b.title }}</h3>
            <span class="banner-subtitle">{{ b.subtitle }}</span>
            <span class="banner-link">Link: {{ b.link }}</span>
          </div>
          <div class="banner-status-wrap">
            <span
              class="status-badge"
              :class="b.active ? 'active' : 'inactive'"
              >{{ b.active ? "Active" : "Inactive" }}</span
            >
          </div>
        </div>
        <div class="banner-actions">
          <button
            class="icon-btn"
            @click="toggleActive(b)"
            :title="b.active ? 'Deactivate' : 'Activate'"
          >
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              v-if="!b.active"
            >
              <polygon points="5 3 19 12 5 21 5 3" />
            </svg>
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              v-else
            >
              <rect x="6" y="4" width="4" height="16" />
              <rect x="14" y="4" width="4" height="16" />
            </svg>
          </button>
          <button class="icon-btn" @click="openEditModal(b)" title="Edit">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"
              />
              <path
                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"
              />
            </svg>
          </button>
          <button
            class="icon-btn danger"
            @click="confirmDelete(b)"
            title="Delete"
          >
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <polyline points="3 6 5 6 21 6" />
              <path
                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" v-if="showModal" @click.self="showModal = false">
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ editingBanner ? "Edit Banner" : "New Banner" }}</h3>
          <button class="modal-close" @click="showModal = false">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="modal-form">
            <div class="form-group">
              <label>Title</label>
              <input
                v-model="form.title"
                class="form-input"
                placeholder="e.g. Fresh Seafood Sale"
              />
            </div>
            <div class="form-group">
              <label>Subtitle</label>
              <input
                v-model="form.subtitle"
                class="form-input"
                placeholder="e.g. Up to 30% off"
              />
            </div>
            <div class="form-group">
              <label>Image URL</label>
              <input
                v-model="form.image"
                class="form-input"
                placeholder="/images/banner.jpg"
              />
            </div>
            <div class="form-group">
              <label>Link</label>
              <input
                v-model="form.link"
                class="form-input"
                placeholder="/category/seafood"
              />
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Sort Order</label>
                <input
                  v-model.number="form.sortOrder"
                  class="form-input"
                  type="number"
                  min="1"
                />
              </div>
              <div class="form-group">
                <label>Active</label>
                <div class="toggle-wrap">
                  <label class="toggle-label">
                    <input
                      type="checkbox"
                      v-model="form.active"
                      class="toggle-input"
                    />
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="reset-btn" @click="showModal = false">Cancel</button>
          <button class="save-btn" @click="saveBanner">
            {{ editingBanner ? "Update" : "Create" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div
      class="modal-overlay"
      v-if="showDeleteModal"
      @click.self="showDeleteModal = false"
    >
      <div class="modal-card modal-sm">
        <div class="modal-top">
          <span class="modal-title">Delete Banner</span>
          <button class="modal-close" @click="showDeleteModal = false">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <p style="font-size: 14px; color: #374151; line-height: 1.5">
            Delete "{{ deletingBanner?.title }}"?
          </p>
        </div>
        <div class="modal-actions">
          <button class="modal-btn secondary" @click="showDeleteModal = false">
            Cancel
          </button>
          <button class="modal-btn danger" @click="doDelete">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "default" });

const store = useBannersStore();
const { banners } = storeToRefs(store);

const activeFilter = ref("");
const showModal = ref(false);
const editingBanner = ref(null);
const showDeleteModal = ref(false);
const deletingBanner = ref(null);

const form = ref({
  title: "",
  subtitle: "",
  image: "",
  link: "",
  sortOrder: 1,
  active: true,
});

const filteredBanners = computed(() => {
  let result = banners.value;
  if (activeFilter.value !== "") {
    result = result.filter((b) => b.active === (activeFilter.value === "true"));
  }
  return result;
});

function openAddModal() {
  editingBanner.value = null;
  form.value = {
    title: "",
    subtitle: "",
    image: "",
    link: "",
    sortOrder: banners.value.length + 1,
    active: true,
  };
  showModal.value = true;
}
function openEditModal(b) {
  editingBanner.value = b;
  form.value = {
    title: b.title,
    subtitle: b.subtitle,
    image: b.image,
    link: b.link,
    sortOrder: b.sortOrder,
    active: b.active,
  };
  showModal.value = true;
}
function toggleActive(b) {
  store.toggleActive(b.id);
}
function confirmDelete(b) {
  deletingBanner.value = b;
  showDeleteModal.value = true;
}
function doDelete() {
  if (deletingBanner.value) store.deleteBanner(deletingBanner.value.id);
  showDeleteModal.value = false;
  deletingBanner.value = null;
}

function saveBanner() {
  if (!form.value.title) return;
  if (editingBanner.value) {
    store.updateBanner(editingBanner.value.id, { ...form.value });
  } else {
    store.addBanner({ ...form.value });
  }
  showModal.value = false;
  editingBanner.value = null;
}
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.page-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}
.toolbar-left {
  display: flex;
  align-items: center;
  gap: 10px;
}
.filter-select {
  padding: 8px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #374151;
  background: #fff;
  cursor: pointer;
  outline: none;
}
.filter-select:focus {
  border-color: #059669;
}
.add-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 18px;
  background: #059669;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  transition: background 0.15s;
}
.add-btn:hover {
  background: #047857;
}
.add-btn svg {
  width: 16px;
  height: 16px;
}

.banners-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 16px;
}
.banner-card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  transition: box-shadow 0.15s;
}
.banner-card:hover {
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}
.banner-card.inactive {
  opacity: 0.65;
}
.banner-preview {
  position: relative;
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.banner-placeholder svg {
  width: 32px;
  height: 32px;
  color: rgba(255, 255, 255, 0.4);
}
.banner-overlay-text {
  position: absolute;
  left: 16px;
  right: 60px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.banner-title-preview {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 15px;
  font-weight: 700;
  color: #fff;
}
.banner-sub-preview {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
}
.banner-order-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(0, 0, 0, 0.3);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
}
.banner-body {
  padding: 14px 16px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
}
.banner-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
  min-width: 0;
}
.banner-title {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 13px;
  font-weight: 600;
  color: #111827;
}
.banner-subtitle {
  font-size: 12px;
  color: #6b7280;
}
.banner-link {
  font-size: 11px;
  color: #059669;
}
.banner-status-wrap {
  flex-shrink: 0;
}
.status-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.status-badge.active {
  background: #f0fdf4;
  color: #059669;
}
.status-badge.inactive {
  background: #f3f4f6;
  color: #9ca3af;
}
.banner-actions {
  display: flex;
  gap: 4px;
  padding: 8px 16px 14px;
}
.icon-btn {
  width: 30px;
  height: 30px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  transition: all 0.15s;
}
.icon-btn:hover {
  border-color: #059669;
  color: #059669;
}
.icon-btn.danger:hover {
  border-color: #dc2626;
  color: #dc2626;
}
.icon-btn svg {
  width: 14px;
  height: 14px;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
  backdrop-filter: blur(2px);
}
.modal-card {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 440px;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
  animation: modalIn 0.15s ease;
}
.modal-card.modal-sm {
  max-width: 420px;
}
@keyframes modalIn {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(8px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px 0;
}
.modal-header h3 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #111827;
}
.modal-top {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px 24px;
  border-bottom: 1px solid #f3f4f6;
}
.modal-title {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 15px;
  font-weight: 700;
  color: #111827;
  flex: 1;
}
.modal-close {
  width: 30px;
  height: 30px;
  border: none;
  background: none;
  cursor: pointer;
  color: #9ca3af;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
}
.modal-close:hover {
  background: #f3f4f6;
  color: #374151;
}
.modal-close svg {
  width: 18px;
  height: 18px;
}
.modal-body {
  padding: 20px 24px;
}
.modal-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.form-group label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
}
.form-input {
  padding: 10px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #111827;
  outline: none;
}
.form-input:focus {
  border-color: #059669;
}
.form-input[type="number"] {
  width: 100px;
}
.form-row {
  display: flex;
  gap: 16px;
  align-items: flex-end;
}
.form-row .form-group {
  flex: 1;
}
.toggle-wrap {
  padding-top: 4px;
}
.toggle-label {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 22px;
  cursor: pointer;
}
.toggle-input {
  display: none;
}
.toggle-slider {
  position: absolute;
  inset: 0;
  background: #d1d5db;
  border-radius: 22px;
  transition: 0.2s;
}
.toggle-slider::before {
  content: "";
  position: absolute;
  left: 2px;
  bottom: 2px;
  width: 18px;
  height: 18px;
  background: #fff;
  border-radius: 50%;
  transition: 0.2s;
}
.toggle-input:checked + .toggle-slider {
  background: #059669;
}
.toggle-input:checked + .toggle-slider::before {
  transform: translateX(18px);
}
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 0 24px 20px;
}
.modal-actions {
  display: flex;
  gap: 10px;
  padding: 16px 24px 20px;
  border-top: 1px solid #f3f4f6;
}
.modal-btn {
  flex: 1;
  padding: 11px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  font-family: "Inter", sans-serif;
  cursor: pointer;
  transition: all 0.15s;
  border: none;
}
.modal-btn.secondary {
  background: #f3f4f6;
  color: #6b7280;
}
.modal-btn.secondary:hover {
  background: #e5e7eb;
}
.modal-btn.danger {
  background: #dc2626;
  color: #fff;
}
.modal-btn.danger:hover {
  background: #b91c1c;
}
.save-btn {
  padding: 10px 20px;
  background: #059669;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: "Inter", sans-serif;
}
.save-btn:hover {
  background: #047857;
}
.reset-btn {
  padding: 10px 20px;
  background: #fff;
  color: #374151;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  font-family: "Inter", sans-serif;
}
.reset-btn:hover {
  border-color: #9ca3af;
}

@media (max-width: 600px) {
  .banners-grid {
    grid-template-columns: 1fr;
  }
}
</style>
