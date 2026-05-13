<template>
  <div class="page-shell">
    <div class="notif-controls">
      <button class="mark-read-btn" @click="markAllRead">Mark All as Read</button>
    </div>

    <div class="table-card">
      <div class="notif-list">
        <div v-for="n in notifications" :key="n.id" class="notif-item" :class="{ unread: !n.read }" @click="n.read = true">
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
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const notifications = ref([
  { id: 1, type: 'order', message: 'New order #ORD-1042 placed by Maria Santos', time: '2 mins ago', read: false },
  { id: 2, type: 'order', message: 'Order #ORD-1041 marked as In Transit', time: '8 mins ago', read: false },
  { id: 3, type: 'system', message: 'System backup completed successfully', time: '14 mins ago', read: false },
  { id: 4, type: 'alert', message: 'Dispute opened on Order #ORD-1038 by Liza Morales', time: '22 mins ago', read: false },
  { id: 5, type: 'order', message: 'New seller registration: Rosa\'s Produce', time: '1 hour ago', read: true },
  { id: 6, type: 'system', message: 'New driver application: Leo Magno', time: '2 hours ago', read: true },
  { id: 7, type: 'promo', message: 'Promo code PALENGKE20 used 45 times today', time: '3 hours ago', read: true },
  { id: 8, type: 'warning', message: 'Payment payout for Pedro Merchants failed', time: '5 hours ago', read: true },
  { id: 9, type: 'order', message: 'Order #ORD-1033 delivered successfully', time: '6 hours ago', read: true },
  { id: 10, type: 'system', message: 'New app version 2.4.1 ready for deployment', time: '1 day ago', read: true },
])

const markAllRead = () => {
  notifications.value.forEach(n => n.read = true)
}
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.notif-controls { display: flex; justify-content: flex-end; }
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
</style>
