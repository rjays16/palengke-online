export const usePromosStore = defineStore('promos', () => {
  const promos = ref([
    { id: 1, code: 'PALENGKE20', discount: 20, type: 'percent', minSpend: 200, maxUses: 100, used: 45, validUntil: '2026-06-30', active: true },
    { id: 2, code: 'FREESHIP', discount: 50, type: 'flat', minSpend: 300, maxUses: 50, used: 23, validUntil: '2026-06-15', active: true },
    { id: 3, code: 'WELCOME10', discount: 10, type: 'percent', minSpend: 0, maxUses: 200, used: 78, validUntil: '2026-07-01', active: true },
    { id: 4, code: 'SEAFOOD25', discount: 25, type: 'percent', minSpend: 400, maxUses: 30, used: 12, validUntil: '2026-05-31', active: false },
    { id: 5, code: 'FLASH50', discount: 50, type: 'flat', minSpend: 500, maxUses: 20, used: 20, validUntil: '2026-05-20', active: false },
  ])
  let nextId = 6

  function addPromo(data) {
    promos.value.push({ id: nextId++, ...data })
  }
  function updatePromo(id, data) {
    const p = promos.value.find(p => p.id === id)
    if (p) Object.assign(p, data)
  }
  function deletePromo(id) {
    promos.value = promos.value.filter(p => p.id !== id)
  }
  function toggleActive(id) {
    const p = promos.value.find(p => p.id === id)
    if (p) p.active = !p.active
  }

  return { promos, addPromo, updatePromo, deletePromo, toggleActive }
})
