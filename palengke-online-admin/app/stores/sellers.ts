export const useSellersStore = defineStore('sellers', () => {
  const sellers = ref([
    { id: 1, name: "Juan's Farm", owner: 'Juan dela Cruz', category: 'Vegetables', location: 'Brgy. San Isidro', products: 48, revenue: 45200, rating: 4.8, status: 'active', joined: '2024-01-15', contact: { phone: '0917-111-2233', email: 'juan.farm@email.com' } },
    { id: 2, name: "Maria's Produce", owner: 'Maria Santos', category: 'Fruits', location: 'Brgy. Maligaya', products: 35, revenue: 38400, rating: 4.6, status: 'active', joined: '2024-03-20', contact: { phone: '0917-222-3344', email: 'maria.produce@email.com' } },
    { id: 3, name: 'Fresh Fish Co.', owner: 'Pedro Reyes', category: 'Seafood', location: 'Brgy. Poblacion', products: 52, revenue: 62100, rating: 4.9, status: 'active', joined: '2024-02-01', contact: { phone: '0917-333-4455', email: 'freshfish@email.com' } },
    { id: 4, name: 'Meat Shop Inc.', owner: 'Ana Cruz', category: 'Meat & Poultry', location: 'Brgy. Bagong Silang', products: 28, revenue: 52300, rating: 4.7, status: 'active', joined: '2024-04-10', contact: { phone: '0917-444-5566', email: 'meatshop@email.com' } },
    { id: 5, name: 'Green Valley Farm', owner: 'Ben Torres', category: 'Vegetables', location: 'Brgy. Kasiglahan', products: 41, revenue: 29800, rating: 4.5, status: 'inactive', joined: '2024-05-05', contact: { phone: '0917-555-6677', email: 'greenvalley@email.com' } },
    { id: 6, name: 'Sweet Harvest', owner: 'Luisa Tan', category: 'Fruits', location: 'Brgy. Mabuhay', products: 25, revenue: 21500, rating: 4.3, status: 'active', joined: '2024-06-18', contact: { phone: '0917-666-7788', email: 'sweetharvest@email.com' } },
    { id: 7, name: 'Coastal Catch', owner: 'Rosa Mendiola', category: 'Seafood', location: 'Brgy. Baybay', products: 38, revenue: 45900, rating: 4.8, status: 'active', joined: '2024-03-12', contact: { phone: '0917-777-8899', email: 'coastalcatch@email.com' } },
    { id: 8, name: 'Organic Choice', owner: 'Carlos Gomez', category: 'Bundles', location: 'Brgy. Sampa', products: 20, revenue: 18200, rating: 4.2, status: 'inactive', joined: '2024-08-01', contact: { phone: '0917-888-9900', email: 'organicchoice@email.com' } }
  ])

  function getSellerById(id: number) {
    return sellers.value.find(s => s.id === id)
  }

  function getSellerProducts(id: number) {
    const products = [
      { id: 1, name: 'Fresh Tomatoes', price: 60, unit: 'kg', stock: 120 },
      { id: 2, name: 'Eggplant', price: 45, unit: 'kg', stock: 80 },
      { id: 3, name: 'Cabbage', price: 180, unit: 'pc', stock: 30 },
      { id: 4, name: 'Kangkong', price: 25, unit: 'bundle', stock: 60 },
      { id: 5, name: 'Sili (Green)', price: 30, unit: 'kg', stock: 40 }
    ]
    const seller = getSellerById(id)
    if (!seller) return []
    return products.slice(0, Math.min(seller.products, products.length))
  }

  function getSellerOrders(id: number) {
    const allOrders = [
      { id: 'ORD-1042', customer: 'Maria Santos', items: 4, total: 1280, status: 'delivered', date: '2026-05-14' },
      { id: 'ORD-1039', customer: 'Pedro Reyes', items: 1, total: 180, status: 'pending', date: '2026-05-13' },
      { id: 'ORD-1035', customer: 'Liza Morales', items: 4, total: 1120, status: 'transit', date: '2026-05-12' },
      { id: 'ORD-1031', customer: 'Tina Rivera', items: 3, total: 890, status: 'delivered', date: '2026-05-10' },
      { id: 'ORD-1028', customer: 'Rosa Mendiola', items: 5, total: 1560, status: 'delivered', date: '2026-05-08' }
    ]
    return allOrders.slice(0, 5)
  }

  function toggleStatus(id: number) {
    const seller = sellers.value.find(s => s.id === id)
    if (seller) {
      seller.status = seller.status === 'active' ? 'inactive' : 'active'
    }
  }

  return { sellers, getSellerById, getSellerProducts, getSellerOrders, toggleStatus }
})
