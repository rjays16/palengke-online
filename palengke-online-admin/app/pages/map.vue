<template>
  <div class="page-shell">
    <div class="map-container">
      <div class="map-header">
        <div class="map-stats">
          <div class="map-stat"><span class="map-stat-dot active"></span> {{ activeDrivers }} Active</div>
          <div class="map-stat"><span class="map-stat-dot transit"></span> {{ onDelivery }} On Delivery</div>
          <div class="map-stat"><span class="map-stat-dot idle"></span> {{ idleDrivers }} Idle</div>
        </div>
        <div class="map-controls">
          <button class="map-btn" @click="fitAll">⊞</button>
        </div>
      </div>
      <div class="map-body">
        <div ref="mapEl" class="map-leaflet"></div>
        <div class="map-sidebar">
          <div class="map-sidebar-header">Online Drivers</div>
          <div class="map-drivers">
            <div v-for="d in drivers" :key="d.id" class="map-driver" :class="{ active: d.status !== 'idle' }" @click="panToDriver(d)">
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

const drivers = ref([
  { id: 1, name: 'Mark Reyes', status: 'active', lat: 14.576, lng: 121.037, location: 'Brgy. San Isidro → Brgy. Centro' },
  { id: 2, name: 'Nina Salvador', status: 'active', lat: 14.558, lng: 121.015, location: 'Brgy. Maligaya → Brgy. New Town' },
  { id: 3, name: 'Tina Fernandez', status: 'active', lat: 14.545, lng: 121.048, location: 'Brgy. Baybay → Brgy. Poblacion' },
  { id: 4, name: 'Rico Dimagiba', status: 'idle', lat: 14.563, lng: 121.025, location: 'Brgy. Central (Waiting)' },
  { id: 5, name: 'Wendy Pascual', status: 'active', lat: 14.535, lng: 121.008, location: 'Brgy. Silangan → Brgy. Kanluran' },
  { id: 6, name: 'Gary Alcantara', status: 'idle', lat: 14.572, lng: 121.055, location: 'Brgy. East (Resting)' },
  { id: 7, name: 'Leo Magno', status: 'active', lat: 14.548, lng: 121.032, location: 'Brgy. North → Brgy. South' },
])

const activeDrivers = computed(() => drivers.value.filter(d => d.status === 'active').length)
const onDelivery = computed(() => drivers.value.filter(d => d.status === 'active').length)
const idleDrivers = computed(() => drivers.value.filter(d => d.status === 'idle').length)

const mapEl = ref(null)
let map = null
let markers = []
let markerLayer = null

if (import.meta.client) {
  import('leaflet').then(Lmod => {
    const L = Lmod.default || Lmod

    nextTick(() => {
      if (!mapEl.value) return
      map = L.map(mapEl.value, { zoomControl: false }).setView([14.558, 121.032], 13)

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
      }).addTo(map)

      L.control.zoom({ position: 'topright' }).addTo(map)

      markerLayer = L.layerGroup().addTo(map)

      drivers.value.forEach(d => {
        const color = d.status === 'active' ? '#059669' : '#f59e0b'
        const icon = L.divIcon({
          html: `<div style="width:32px;height:32px;border-radius:50%;background:${color};color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.25)">${d.name.charAt(0)}</div>`,
          className: '',
          iconSize: [32, 32],
          iconAnchor: [16, 16],
        })
        const marker = L.marker([d.lat, d.lng], { icon }).bindPopup(`
          <div style="font-family:Inter,sans-serif;font-size:13px">
            <strong style="font-size:15px">${d.name}</strong><br/>
            <span style="color:#6b7280">${d.status === 'active' ? '🟢 Moving' : '🟡 Idle'}</span><br/>
            <span style="color:#9ca3af;font-size:12px">${d.location}</span>
          </div>
        `)
        marker._driverId = d.id
        markers.push(marker)
        markerLayer.addLayer(marker)
      })
    })
  })
}

function panToDriver(d) {
  if (!map) return
  map.setView([d.lat, d.lng], 15)
  const m = markers.find(m => m._driverId === d.id)
  if (m) m.openPopup()
}

function fitAll() {
  if (!map || markers.length === 0) return
  const markersArr = markers
  import('leaflet').then(Lmod => {
    const L = Lmod.default || Lmod
    const group = L.featureGroup(markersArr)
    map.fitBounds(group.getBounds().pad(0.15))
  })
}

onUnmounted(() => {
  if (map) { map.remove(); map = null }
})
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; height: calc(100vh - 64px - 56px); min-height: 400px; }
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
.map-btn { width: 32px; height: 32px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; color: #374151; transition: all 0.15s; }
.map-btn:hover { border-color: #059669; color: #059669; }
.map-body { flex: 1; display: flex; overflow: hidden; }
.map-leaflet { flex: 1; min-height: 300px; z-index: 0; }
.map-sidebar { width: 280px; border-left: 1px solid #e5e7eb; display: flex; flex-direction: column; overflow-y: auto; flex-shrink: 0; }
.map-sidebar-header { padding: 14px 16px; font-size: 12px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #f3f4f6; }
.map-drivers { display: flex; flex-direction: column; }
.map-driver { display: flex; align-items: center; gap: 10px; padding: 12px 16px; cursor: pointer; transition: background 0.15s; border-bottom: 1px solid #f9fafb; }
.map-driver:hover { background: #f9fafb; }
.map-driver.active { border-left: 3px solid #059669; }
.mdriver-avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: white; flex-shrink: 0; }
.mdriver-avatar.active { background: #059669; }
.mdriver-avatar.idle { background: #f59e0b; }
.mdriver-info { flex: 1; min-width: 0; }
.mdriver-name { display: block; font-size: 12px; font-weight: 600; color: #111827; }
.mdriver-status { display: block; font-size: 11px; color: #9ca3af; }
.mdriver-loc { font-size: 11px; color: #6b7280; text-align: right; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
:deep(.leaflet-popup-content-wrapper) { border-radius: 10px; }
:deep(.leaflet-popup-content) { margin: 10px 14px; }
</style>
