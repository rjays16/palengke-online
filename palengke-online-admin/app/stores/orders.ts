export const useOrdersStore = defineStore('orders', () => {
  const orders = ref([
    { id: 'ORD-1042', customer: 'Maria Santos', seller: "Juan's Farm", items: 4, total: 1280, payment: 'cod', status: 'delivered', date: '2026-05-14' },
    { id: 'ORD-1041', customer: 'Jose Rizal', seller: 'Fresh Fish Co.', items: 2, total: 450, payment: 'gcash', status: 'transit', date: '2026-05-14' },
    { id: 'ORD-1040', customer: 'Ana Cruz', seller: "Maria's Produce", items: 6, total: 2340, payment: 'cod', status: 'preparing', date: '2026-05-13' },
    { id: 'ORD-1039', customer: 'Pedro Reyes', seller: "Juan's Farm", items: 1, total: 180, payment: 'gcash', status: 'pending', date: '2026-05-13' },
    { id: 'ORD-1038', customer: 'Luisa Tan', seller: 'Meat Shop Inc.', items: 3, total: 890, payment: 'cod', status: 'cancelled', date: '2026-05-13' },
    { id: 'ORD-1037', customer: 'Rosa Mendiola', seller: "Maria's Produce", items: 5, total: 1560, payment: 'gcash', status: 'delivered', date: '2026-05-12' },
    { id: 'ORD-1036', customer: 'Ben Torres', seller: 'Fresh Fish Co.', items: 2, total: 340, payment: 'cod', status: 'delivered', date: '2026-05-12' },
    { id: 'ORD-1035', customer: 'Liza Morales', seller: 'Meat Shop Inc.', items: 4, total: 1120, payment: 'gcash', status: 'transit', date: '2026-05-12' },
    { id: 'ORD-1034', customer: 'Carlos Gomez', seller: "Juan's Farm", items: 3, total: 670, payment: 'cod', status: 'preparing', date: '2026-05-11' },
    { id: 'ORD-1033', customer: 'Tina Rivera', seller: 'Fresh Fish Co.', items: 7, total: 2890, payment: 'gcash', status: 'pending', date: '2026-05-11' }
  ])

  const orderItems: Record<string, Array<{ name: string; qty: number; price: number }>> = {
    'ORD-1042': [
      { name: 'Fresh Tomatoes', qty: 2, price: 60 },
      { name: 'Eggplant', qty: 3, price: 45 },
      { name: 'Chicken (Whole)', qty: 1, price: 350 },
      { name: 'Rice (1kg)', qty: 2, price: 55 }
    ],
    'ORD-1041': [
      { name: 'Tilapia (Fresh)', qty: 2, price: 120 },
      { name: 'Shrimp (500g)', qty: 1, price: 210 }
    ],
    'ORD-1040': [
      { name: 'Banana (Bunch)', qty: 2, price: 80 },
      { name: 'Mango (Sweet)', qty: 4, price: 65 },
      { name: 'Pork (Liempo)', qty: 1, price: 280 },
      { name: 'Sili (Green)', qty: 1, price: 30 },
      { name: 'Kangkong', qty: 3, price: 25 },
      { name: 'Tofu (Soft)', qty: 2, price: 35 }
    ],
    'ORD-1039': [
      { name: 'Cabbage', qty: 1, price: 180 }
    ]
  }

  function getOrderById(id: string) {
    return orders.value.find(o => o.id === id)
  }

  function getOrderItems(id: string) {
    return orderItems[id] || []
  }

  function getTimeline(status: string, date: string): Array<{ title: string; time: string; done: boolean; current: boolean }> {
    const steps = [
      { title: 'Order Placed', time: `${date} 09:30 AM`, done: true, current: false },
      { title: 'Order Confirmed', time: `${date} 09:45 AM`, done: true, current: false },
      { title: 'Preparing', time: `${date} 10:00 AM`, done: status !== 'pending', current: status === 'preparing' },
      { title: 'Picked Up', time: `${date} 11:15 AM`, done: status === 'transit' || status === 'delivered', current: status === 'transit' },
      { title: 'Delivered', time: `${date} 12:00 PM`, done: status === 'delivered', current: false }
    ]
    if (status === 'cancelled') {
      steps[2] = { ...steps[2], title: 'Cancelled', time: `${date} 10:30 AM`, done: true, current: false }
      steps.splice(3)
    }
    return steps
  }

  return { orders, orderItems, getOrderById, getOrderItems, getTimeline }
})
