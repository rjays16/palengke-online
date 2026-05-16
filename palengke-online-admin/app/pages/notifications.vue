<template>
  <div class="page-shell">
    <div class="notif-controls">
      <span class="result-count">{{ store.allNotifications.length }} total</span>
      <button class="mark-read-btn" @click="store.markAllRead()">Mark All as Read</button>
    </div>

    <div class="table-card">
      <div class="notif-list">
        <div v-for="n in paginatedNotifications" :key="n.id" class="notif-item" :class="{ unread: !n.read }" @click="store.markRead(n.id)">
          <div class="notif-icon" :class="n.type">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path v-if="n.type === 'order'" d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
              <circle v-if="n.type === 'system'" cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
              <path v-if="n.type === 'alert'" d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
              <path v-if="n.type === 'promo'" d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
              <polygon v-if="n.type === 'warning'" points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/>
            </svg>
          </div>
          <div class="notif-content">
            <p class="notif-text">{{ n.message }}</p>
            <span class="notif-time">{{ n.time }}</span>
          </div>
          <span class="notif-unread-dot" v-if="!n.read"></span>
          <button class="notif-delete" @click.stop="store.deleteNotification(n.id)" title="Delete">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </div>
      </div>
      <div class="pagination-wrap">
        <PaginationBar v-model="notifPage" :total="store.allNotifications.length" :perPage="notifPerPage" />
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const store = useNotificationsStore()

const notifPage = ref(1)
const notifPerPage = ref(5)

const paginatedNotifications = computed(() => {
  const start = (notifPage.value - 1) * notifPerPage.value
  return store.allNotifications.slice(start, start + notifPerPage.value)
})
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.notif-controls { display: flex; justify-content: space-between; align-items: center; }
.result-count { font-size: 13px; color: #9ca3af; }
.mark-read-btn { padding: 8px 16px; border: 1.5px solid #e5e7eb; border-radius: 8px; background: #fff; font-size: 13px; font-weight: 500; color: #059669; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
.mark-read-btn:hover { background: #f0fdf4; border-color: #059669; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.notif-list { display: flex; flex-direction: column; }
.notif-item { display: flex; align-items: flex-start; gap: 14px; padding: 16px 20px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.15s; position: relative; }
.notif-item:hover { background: #f9fafb; }
.notif-item.unread { background: #f0fdf4; }
.notif-item:last-child { border-bottom: none; }
.notif-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.notif-icon svg { width: 18px; height: 18px; }
.notif-icon.order { background: #f0fdf4; color: #059669; }
.notif-icon.system { background: #f3f4f6; color: #6b7280; }
.notif-icon.alert { background: #fef2f2; color: #dc2626; }
.notif-icon.promo { background: #fef3c7; color: #d97706; }
.notif-icon.warning { background: #fef3c7; color: #d97706; }
.notif-content { flex: 1; min-width: 0; }
.notif-text { font-size: 13px; color: #374151; font-weight: 500; line-height: 1.45; margin-bottom: 4px; }
.notif-item.unread .notif-text { font-weight: 600; color: #111827; }
.notif-time { font-size: 11px; color: #9ca3af; }
.notif-unread-dot { width: 8px; height: 8px; border-radius: 50%; background: #059669; flex-shrink: 0; margin-top: 6px; }
.notif-delete { width: 28px; height: 28px; border: none; background: none; cursor: pointer; color: #9ca3af; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; opacity: 0; transition: all 0.15s; }
.notif-item:hover .notif-delete { opacity: 1; }
.notif-delete:hover { background: #fef2f2; color: #dc2626; }
.notif-delete svg { width: 14px; height: 14px; }
.pagination-wrap { padding: 12px 20px; border-top: 1px solid #f3f4f6; }
</style>
