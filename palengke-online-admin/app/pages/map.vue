<template>
  <div class="page-shell">
    <div class="map-container">
      <div class="map-header">
        <div class="map-stats">
          <div class="map-stat"><span class="map-stat-dot active"></span> {{ activeDrivers }} Active Drivers</div>
          <div class="map-stat"><span class="map-stat-dot transit"></span> {{ onDelivery }} On Delivery</div>
          <div class="map-stat"><span class="map-stat-dot idle"></span> {{ idleDrivers }} Idle</div>
        </div>
        <div class="map-controls">
          <button class="map-btn" @click="zoomIn">+</button>
          <button class="map-btn" @click="zoomOut">−</button>
        </div>
      </div>
      <div class="map-area">
        <div class="map-placeholder">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="map-icon">
            <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
            <line x1="9" y1="3" x2="9" y2="18"/>
            <line x1="15" y1="6" x2="15" y2="21"/>
          </svg>
          <h3>Live Map View</h3>
          <p>Interactive map showing real-time locations of all active drivers and deliveries.</p>
          <div class="map-drivers">
            <div class="map-driver" v-for="d in drivers" :key="d.id">
              <div class="mdriver-avatar" :class="d.status">{{ d.name.charAt(0) }}</div>
              <div class="mdriver-info">
                <span class="mdriver-name">{{ d.name }}</span>
                <span class="mdriver-status">{{ d.status === 'active' ? 'Moving' : 'Idle' }}</span>
              </div>
              <span class="mdriver-loc">{{ d.location }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const activeDrivers = ref(5)
const onDelivery = ref(3)
const idleDrivers = ref(2)

const drivers = ref([
  { id: 1, name: 'Mark Reyes', status: 'active', location: 'Brgy. San Isidro → Brgy. Centro' },
  { id: 2, name: 'Nina Salvador', status: 'active', location: 'Brgy. Maligaya → Brgy. New Town' },
  { id: 3, name: 'Tina Fernandez', status: 'active', location: 'Brgy. Baybay → Brgy. Poblacion' },
  { id: 4, name: 'Rico Dimagiba', status: 'idle', location: 'Brgy. Central (Waiting)' },
  { id: 5, name: 'Wendy Pascual', status: 'active', location: 'Brgy. Silangan → Brgy. Kanluran' },
  { id: 6, name: 'Gary Alcantara', status: 'idle', location: 'Brgy. East (Resting)' },
  { id: 7, name: 'Leo Magno', status: 'active', location: 'Brgy. North → Brgy. South' },
])

const zoomIn = () => {}
const zoomOut = () => {}
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; height: calc(100vh - 64px - 56px); }
.map-container { display: flex; flex-direction: column; height: 100%; background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.map-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; }
.map-stats { display: flex; gap: 20px; }
.map-stat { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; color: #374151; }
.map-stat-dot { width: 8px; height: 8px; border-radius: 50%; }
.map-stat-dot.active { background: #059669; animation: livePulse 2s infinite; }
.map-stat-dot.transit { background: #3b82f6; animation: livePulse 2s infinite; }
.map-stat-dot.idle { background: #f59e0b; }
@keyframes livePulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
.map-controls { display: flex; gap: 4px; }
.map-btn { width: 32px; height: 32px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; color: #374151; transition: all 0.15s; }
.map-btn:hover { border-color: #059669; color: #059669; }
.map-area { flex: 1; display: flex; align-items: center; justify-content: center; background: #f9fafb; }
.map-placeholder { text-align: center; max-width: 480px; padding: 40px; }
.map-icon { width: 48px; height: 48px; color: #059669; margin-bottom: 16px; }
.map-placeholder h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 8px; }
.map-placeholder p { font-size: 14px; color: #6b7280; margin-bottom: 24px; }
.map-drivers { display: flex; flex-direction: column; gap: 8px; text-align: left; }
.map-driver { display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; }
.mdriver-avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: white; flex-shrink: 0; }
.mdriver-avatar.active { background: #059669; }
.mdriver-avatar.idle { background: #f59e0b; }
.mdriver-info { flex: 1; }
.mdriver-name { display: block; font-size: 12px; font-weight: 600; color: #111827; }
.mdriver-status { display: block; font-size: 11px; color: #9ca3af; }
.mdriver-loc { font-size: 11px; color: #6b7280; }
</style>
