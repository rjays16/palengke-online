export const useComplaintsStore = defineStore('complaints', () => {
  const complaints = ref([
    { id: 'CMP-001', complainant: 'Maria Santos', complainantType: 'customer', respondent: "Juan's Fresh Market", respondentType: 'seller', subject: 'Spoiled vegetables delivered', description: 'Received a delivery of spoiled lettuce and tomatoes. The vegetables were clearly past their shelf life. Requesting a full refund.', category: 'product_quality', status: 'investigating', priority: 'high', date: '2026-05-15', resolution: '' },
    { id: 'CMP-002', complainant: 'Pedro Reyes', complainantType: 'customer', respondent: 'Mark Reyes', respondentType: 'driver', subject: 'Driver was 45 minutes late', description: 'The driver arrived 45 minutes after the estimated delivery window. My food was cold and the driver did not apologize.', category: 'delivery', status: 'pending', priority: 'medium', date: '2026-05-14', resolution: '' },
    { id: 'CMP-003', complainant: "Fresh Fish Co.", complainantType: 'seller', respondent: 'Nina Salvador', respondentType: 'driver', subject: 'Driver refused to pick up order', description: 'Driver claimed our location was too far and refused to pick up the order. We lost sales because of this.', category: 'service', status: 'investigating', priority: 'high', date: '2026-05-14', resolution: '' },
    { id: 'CMP-004', complainant: 'Luisa Tan', complainantType: 'customer', respondent: "Meat Shop Inc.", respondentType: 'seller', subject: 'Overcharged for pork', description: 'Was charged ₱350/kg for pork belly but the posted price was ₱290/kg. The seller said it was a "system error".', category: 'pricing', status: 'resolved', priority: 'high', date: '2026-05-13', resolution: 'Refunded the difference of ₱60. Seller updated pricing. Both parties informed.' },
    { id: 'CMP-005', complainant: "Maria's Produce", complainantType: 'seller', respondent: 'Juan Dela Cruz', respondentType: 'driver', subject: 'Rude behavior during pickup', description: 'Driver used profanity against our staff when asked to wait 2 minutes while we finished packing.', category: 'behavior', status: 'dismissed', priority: 'medium', date: '2026-05-12', resolution: 'Insufficient evidence. No CCTV footage available from that time.' },
    { id: 'CMP-006', complainant: 'Tommy Cruz', complainantType: 'driver', respondent: 'Ana Santos', respondentType: 'customer', subject: 'Customer falsely claimed non-delivery', description: 'Customer said they did not receive the order, but I handed it to them personally. Now they want a refund.', category: 'delivery', status: 'pending', priority: 'high', date: '2026-05-12', resolution: '' },
    { id: 'CMP-007', complainant: 'Elena Garcia', complainantType: 'customer', respondent: "Ana's Palengke", respondentType: 'seller', subject: 'Missing half my order', description: 'Ordered 8 items but only received 4. Missing chicken, pork, shrimp, and onions. Tried calling the seller but no answer.', category: 'product_quality', status: 'investigating', priority: 'high', date: '2026-05-11', resolution: '' },
    { id: 'CMP-008', complainant: "Juan's Fresh Market", complainantType: 'seller', respondent: 'Customer from Order #ORD-1023', respondentType: 'customer', subject: 'Customer filed false complaint', description: 'Customer claimed we short-changed them. Our packing list shows all items were included. Suspect they are trying to get free items.', category: 'other', status: 'resolved', priority: 'low', date: '2026-05-10', resolution: 'Reviewed packing video footage. All items were included. Complaint dismissed.' },
    { id: 'CMP-009', complainant: 'Nina Salvador', complainantType: 'driver', respondent: 'Customer from Order #ORD-1015', respondentType: 'customer', subject: 'Unsafe delivery location', description: 'Customer requested delivery to an alley with aggressive stray dogs. Asked customer to meet at the main road but they refused.', category: 'delivery', status: 'dismissed', priority: 'low', date: '2026-05-09', resolution: 'Driver advised to refuse delivery if location is unsafe. Customer was notified.' },
    { id: 'CMP-010', complainant: 'Carlo M.', complainantType: 'customer', respondent: 'Palengke Online Support', respondentType: 'system', subject: 'App charged me twice', description: 'The app deducted payment twice for the same order. One GCash charge and one wallet deduction.', category: 'pricing', status: 'pending', priority: 'high', date: '2026-05-09', resolution: '' },
  ])

  function updateStatus(id, status) {
    const c = complaints.value.find(c => c.id === id)
    if (c) c.status = status
  }

  function updateResolution(id, notes) {
    const c = complaints.value.find(c => c.id === id)
    if (c) c.resolution = notes
  }

  const analytics = computed(() => {
    const total = complaints.value.length
    const pending = complaints.value.filter(c => c.status === 'pending').length
    const investigating = complaints.value.filter(c => c.status === 'investigating').length
    const resolved = complaints.value.filter(c => c.status === 'resolved').length
    const dismissed = complaints.value.filter(c => c.status === 'dismissed').length
    const byCategory = {
      product_quality: complaints.value.filter(c => c.category === 'product_quality').length,
      delivery: complaints.value.filter(c => c.category === 'delivery').length,
      pricing: complaints.value.filter(c => c.category === 'pricing').length,
      service: complaints.value.filter(c => c.category === 'service').length,
      behavior: complaints.value.filter(c => c.category === 'behavior').length,
      other: complaints.value.filter(c => c.category === 'other').length,
    }
    const byPriority = {
      high: complaints.value.filter(c => c.priority === 'high').length,
      medium: complaints.value.filter(c => c.priority === 'medium').length,
      low: complaints.value.filter(c => c.priority === 'low').length,
    }
    return { total, pending, investigating, resolved, dismissed, byCategory, byPriority }
  })

  const pendingHighPriority = computed(() =>
    complaints.value.filter(c => c.priority === 'high' && (c.status === 'pending' || c.status === 'investigating'))
  )

  return { complaints, updateStatus, updateResolution, analytics, pendingHighPriority }
})
