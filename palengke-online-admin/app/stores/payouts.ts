export const usePayoutsStore = defineStore('payouts', () => {
  const payouts = ref([
    { id: 'PO-001', recipient: "Juan's Farm", type: 'Seller', amount: 45200, account: 'GCash - ****1234', status: 'completed', date: '2026-05-14' },
    { id: 'PO-002', recipient: 'Mark Reyes', type: 'Driver', amount: 12500, account: 'GCash - ****5678', status: 'completed', date: '2026-05-14' },
    { id: 'PO-003', recipient: "Ana's Store", type: 'Seller', amount: 28400, account: 'Bank - ****9012', status: 'pending', date: '2026-05-13' },
    { id: 'PO-004', recipient: 'Nina Salvador', type: 'Driver', amount: 9800, account: 'GCash - ****3456', status: 'completed', date: '2026-05-13' },
    { id: 'PO-005', recipient: 'Pedro Merchants', type: 'Seller', amount: 52100, account: 'Bank - ****7890', status: 'failed', date: '2026-05-12' },
    { id: 'PO-006', recipient: 'Tina Fernandez', type: 'Driver', amount: 15600, account: 'GCash - ****2345', status: 'completed', date: '2026-05-12' },
    { id: 'PO-007', recipient: 'FreshMart', type: 'Seller', amount: 36200, account: 'GCash - ****6789', status: 'pending', date: '2026-05-11' },
    { id: 'PO-008', recipient: "Liza's Shop", type: 'Seller', amount: 19500, account: 'Bank - ****0123', status: 'completed', date: '2026-05-10' },
  ])

  function processPayout(id) {
    const p = payouts.value.find(p => p.id === id)
    if (p && p.status === 'pending') p.status = 'completed'
  }

  function retryPayout(id) {
    const p = payouts.value.find(p => p.id === id)
    if (p && p.status === 'failed') p.status = 'pending'
  }

  function cancelPayout(id) {
    const p = payouts.value.find(p => p.id === id)
    if (p && (p.status === 'pending' || p.status === 'failed')) {
      payouts.value = payouts.value.filter(po => po.id !== id)
    }
  }

  const analytics = computed(() => {
    const total = payouts.value.reduce((sum, p) => sum + p.amount, 0)
    const pending = payouts.value.filter(p => p.status === 'pending').reduce((sum, p) => sum + p.amount, 0)
    const completed = payouts.value.filter(p => p.status === 'completed').reduce((sum, p) => sum + p.amount, 0)
    const failed = payouts.value.filter(p => p.status === 'failed').reduce((sum, p) => sum + p.amount, 0)
    const pendingCount = payouts.value.filter(p => p.status === 'pending').length
    const failedCount = payouts.value.filter(p => p.status === 'failed').length
    return { total, pending, completed, failed, pendingCount, failedCount }
  })

  return { payouts, processPayout, retryPayout, cancelPayout, analytics }
})
