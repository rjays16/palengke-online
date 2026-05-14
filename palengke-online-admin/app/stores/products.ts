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

  return { products, addProduct, updateProduct, deleteProduct }
})
