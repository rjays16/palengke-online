export const useCustomersStore = defineStore('customers', () => {
  const customers = ref([
    { id: 1, name: 'Maria Santos', email: 'maria.santos@email.com', phone: '0912-345-6789', orders: 28, spent: 18400, joined: '2024-01-15' },
    { id: 2, name: 'Jose Rizal', email: 'jose.rizal@email.com', phone: '0913-456-7890', orders: 15, spent: 9200, joined: '2024-02-20' },
    { id: 3, name: 'Ana Cruz', email: 'ana.cruz@email.com', phone: '0914-567-8901', orders: 42, spent: 32100, joined: '2023-11-05' },
    { id: 4, name: 'Pedro Reyes', email: 'pedro.reyes@email.com', phone: '0915-678-9012', orders: 8, spent: 3400, joined: '2024-04-10' },
    { id: 5, name: 'Luisa Tan', email: 'luisa.tan@email.com', phone: '0916-789-0123', orders: 21, spent: 15600, joined: '2024-01-28' },
    { id: 6, name: 'Rosa Mendiola', email: 'rosa.mendiola@email.com', phone: '0917-890-1234', orders: 35, spent: 27800, joined: '2023-12-01' },
    { id: 7, name: 'Ben Torres', email: 'ben.torres@email.com', phone: '0918-901-2345', orders: 12, spent: 5600, joined: '2024-03-15' },
    { id: 8, name: 'Liza Morales', email: 'liza.morales@email.com', phone: '0919-012-3456', orders: 19, spent: 12300, joined: '2024-02-05' },
    { id: 9, name: 'Carlos Gomez', email: 'carlos.gomez@email.com', phone: '0920-123-4567', orders: 6, spent: 2100, joined: '2024-05-20' },
    { id: 10, name: 'Tina Rivera', email: 'tina.rivera@email.com', phone: '0921-234-5678', orders: 31, spent: 24500, joined: '2023-10-10' }
  ])

  function getCustomerById(id: number) {
    return customers.value.find(c => c.id === id)
  }

  function getOrderHistory(id: number) {
    const allHistory = [
      { id: 'ORD-1042', seller: "Juan's Farm", items: 4, total: 1280, status: 'delivered', date: '2026-05-14' },
      { id: 'ORD-1038', seller: 'Meat Shop Inc.', items: 3, total: 890, status: 'cancelled', date: '2026-05-13' },
      { id: 'ORD-1035', seller: 'Fresh Fish Co.', items: 2, total: 450, status: 'delivered', date: '2026-05-12' },
      { id: 'ORD-1030', seller: "Juan's Farm", items: 5, total: 1560, status: 'delivered', date: '2026-05-09' },
      { id: 'ORD-1025', seller: "Maria's Produce", items: 3, total: 890, status: 'delivered', date: '2026-05-06' }
    ]
    return allHistory
  }

  function getActivities() {
    return [
      { id: 1, type: 'order', text: 'Placed order #ORD-1042', time: '2 hours ago' },
      { id: 2, type: 'payment', text: 'Completed payment via GCash', time: '2 hours ago' },
      { id: 3, type: 'review', text: 'Left a 5-star review for Juan\'s Farm', time: '1 day ago' },
      { id: 4, type: 'account', text: 'Updated profile information', time: '3 days ago' },
      { id: 5, type: 'order', text: 'Placed order #ORD-1038', time: '5 days ago' }
    ]
  }

  return { customers, getCustomerById, getOrderHistory, getActivities }
})
