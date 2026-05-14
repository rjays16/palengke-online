export const useProductsStore = defineStore('products', () => {
  const products = ref([
    { id: 1, name: 'Fresh Tomatoes', category: 'vegetables', seller: "Juan's Farm", sellerId: 1, price: 60, unit: 'kg', stock: 120, status: 'in_stock' },
    { id: 2, name: 'Organic Eggplant', category: 'vegetables', seller: "Juan's Farm", sellerId: 1, price: 45, unit: 'kg', stock: 85, status: 'in_stock' },
    { id: 3, name: 'Ripe Mangoes', category: 'fruits', seller: "Maria's Produce", sellerId: 2, price: 120, unit: 'kg', stock: 50, status: 'in_stock' },
    { id: 4, name: 'Fresh Bangus', category: 'seafood', seller: 'Fresh Fish Co.', sellerId: 3, price: 180, unit: 'kg', stock: 30, status: 'in_stock' },
    { id: 5, name: 'Pork Belly', category: 'meat', seller: 'Meat Shop Inc.', sellerId: 4, price: 280, unit: 'kg', stock: 0, status: 'out_of_stock' },
    { id: 6, name: 'Pechay Baguio', category: 'vegetables', seller: "Juan's Farm", sellerId: 1, price: 35, unit: 'bundle', stock: 200, status: 'in_stock' },
    { id: 7, name: 'Green Papaya', category: 'vegetables', seller: 'Green Valley Farm', sellerId: 5, price: 40, unit: 'pc', stock: 65, status: 'in_stock' },
    { id: 8, name: 'Chicken Leg Quarters', category: 'meat', seller: 'Meat Shop Inc.', sellerId: 4, price: 190, unit: 'kg', stock: 45, status: 'in_stock' },
    { id: 9, name: 'Imported Grapes', category: 'fruits', seller: "Maria's Produce", sellerId: 2, price: 250, unit: 'kg', stock: 20, status: 'in_stock' },
    { id: 10, name: 'Fresh Tilapia', category: 'seafood', seller: 'Fresh Fish Co.', sellerId: 3, price: 140, unit: 'kg', stock: 55, status: 'in_stock' },
    { id: 11, name: 'Vegetable Bundle', category: 'bundles', seller: 'Green Valley Farm', sellerId: 5, price: 350, unit: 'set', stock: 25, status: 'in_stock' },
    { id: 12, name: 'Beef Lean Meat', category: 'meat', seller: 'Meat Shop Inc.', sellerId: 4, price: 380, unit: 'kg', stock: 0, status: 'out_of_stock' },
    { id: 13, name: 'Coconut', category: 'fruits', seller: 'Sweet Harvest', sellerId: 6, price: 25, unit: 'pc', stock: 100, status: 'in_stock' },
    { id: 14, name: 'Shrimp (Sugpo)', category: 'seafood', seller: 'Coastal Catch', sellerId: 7, price: 320, unit: 'kg', stock: 18, status: 'in_stock' },
    { id: 15, name: 'Organic Brown Rice', category: 'bundles', seller: 'Organic Choice', sellerId: 8, price: 65, unit: 'kg', stock: 0, status: 'out_of_stock' },
  ])

  let nextId = 16

  function addProduct(data) {
    products.value.unshift({
      id: nextId++,
      name: data.name,
      category: data.category,
      seller: data.seller,
      sellerId: data.sellerId,
      price: Number(data.price),
      unit: data.unit,
      stock: Number(data.stock),
      status: Number(data.stock) > 0 ? 'in_stock' : 'out_of_stock',
    })
  }

  function updateProduct(id, data) {
    const p = products.value.find(p => p.id === id)
    if (p) {
      p.name = data.name
      p.category = data.category
      p.seller = data.seller
      p.sellerId = data.sellerId
      p.price = Number(data.price)
      p.unit = data.unit
      p.stock = Number(data.stock)
      p.status = Number(data.stock) > 0 ? 'in_stock' : 'out_of_stock'
    }
  }

  function deleteProduct(id) {
    products.value = products.value.filter(p => p.id !== id)
  }

  const pendingReviews = ref([
    { id: 'PR-001', name: 'Fresh Ampalaya', category: 'vegetables', seller: "Juan's Farm", sellerId: 1, price: 50, unit: 'kg', stock: 80, description: 'Freshly harvested bitter gourd from our organic farm.', submittedAt: '2026-05-15' },
    { id: 'PR-002', name: 'Dried Fish (Daing)', category: 'seafood', seller: 'Fresh Fish Co.', sellerId: 3, price: 200, unit: 'pack', stock: 40, description: 'Sun-dried bangus, ready to fry. 500g per pack.', submittedAt: '2026-05-14' },
    { id: 'PR-003', name: 'Sweet Corn (Native)', category: 'vegetables', seller: "Maria's Produce", sellerId: 2, price: 35, unit: 'pc', stock: 150, description: 'Native sweet corn from Bukidnon. Sweet and juicy.', submittedAt: '2026-05-14' },
    { id: 'PR-004', name: 'Frozen Liempo', category: 'meat', seller: 'Meat Shop Inc.', sellerId: 4, price: 250, unit: 'kg', stock: 60, description: 'Premium pork belly, vacuum-sealed and frozen.', submittedAt: '2026-05-13' },
    { id: 'PR-005', name: 'Banana (Saba)', category: 'fruits', seller: 'Sweet Harvest', sellerId: 6, price: 15, unit: 'pc', stock: 300, description: 'Fresh saba bananas, perfect for turon or boiling.', submittedAt: '2026-05-12' },
  ])

  function approveReview(id) {
    const r = pendingReviews.value.find(p => p.id === id)
    if (r) {
      products.value.unshift({
        id: nextId++,
        name: r.name,
        category: r.category,
        seller: r.seller,
        sellerId: r.sellerId,
        price: r.price,
        unit: r.unit,
        stock: r.stock,
        status: r.stock > 0 ? 'in_stock' : 'out_of_stock',
      })
      pendingReviews.value = pendingReviews.value.filter(p => p.id !== id)
    }
  }

  function rejectReview(id) {
    pendingReviews.value = pendingReviews.value.filter(p => p.id !== id)
  }

  return { products, addProduct, updateProduct, deleteProduct, pendingReviews, approveReview, rejectReview }
})
