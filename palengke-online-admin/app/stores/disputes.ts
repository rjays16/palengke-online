export const useDisputesStore = defineStore('disputes', () => {
  const disputes = ref([
    { id: 1, orderId: 'ORD-1038', reportedBy: 'Liza Morales', against: "Juan's Farm", reason: 'Wrong items delivered', priority: 'high', status: 'open', date: '2026-05-14' },
    { id: 2, orderId: 'ORD-1035', reportedBy: 'Ben Torres', against: 'Pedro Merchants', reason: 'Missing items', priority: 'medium', status: 'investigating', date: '2026-05-13' },
    { id: 3, orderId: 'ORD-1032', reportedBy: 'Maria Santos', against: "Ana's Store", reason: 'Damaged goods', priority: 'high', status: 'resolved', date: '2026-05-11' },
    { id: 4, orderId: 'ORD-1029', reportedBy: 'Carlo Bautista', against: 'FreshMart', reason: 'Late delivery', priority: 'low', status: 'open', date: '2026-05-10' },
    { id: 5, orderId: 'ORD-1027', reportedBy: 'Rosa Villanueva', against: "Liza's Shop", reason: 'Overcharged', priority: 'medium', status: 'resolved', date: '2026-05-08' },
    { id: 6, orderId: 'ORD-1025', reportedBy: 'Diana Rivera', against: "Juan's Farm", reason: 'Item not fresh', priority: 'high', status: 'investigating', date: '2026-05-06' },
  ])
  let nextId = 7

  function updateStatus(id, newStatus) {
    const d = disputes.value.find(d => d.id === id)
    if (d) d.status = newStatus
  }

  function addDispute(data) {
    disputes.value.push({ id: nextId++, ...data })
  }

  function deleteDispute(id) {
    disputes.value = disputes.value.filter(d => d.id !== id)
  }

  const analytics = computed(() => {
    const total = disputes.value.length
    const open = disputes.value.filter(d => d.status === 'open').length
    const investigating = disputes.value.filter(d => d.status === 'investigating').length
    const resolved = disputes.value.filter(d => d.status === 'resolved').length
    const highPriority = disputes.value.filter(d => d.priority === 'high').length
    const mediumPriority = disputes.value.filter(d => d.priority === 'medium').length
    const lowPriority = disputes.value.filter(d => d.priority === 'low').length
    const byReason = {}
    disputes.value.forEach(d => { byReason[d.reason] = (byReason[d.reason] || 0) + 1 })
    return { total, open, investigating, resolved, highPriority, mediumPriority, lowPriority, byReason }
  })

  return { disputes, updateStatus, addDispute, deleteDispute, analytics }
})
