import type { StoreData, CategoryTab, ProductCategory } from './types'

export const storeData: StoreData = {
  id: 1,
  name: "Juan's Fresh Market",
  emoji: '',
  color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
  rating: '4.8',
  reviewCount: 124,
  distance: '2.3 km',
  isOpen: true,
  openTime: 'Tomorrow, 06:00:00 AM',
  promo: 'Earn 500 Points!',
  minSpend: '₱300.00',
  deliveryFee: 45,
  deliveryTime: '15-25 min'
}

export const categories: CategoryTab[] = [
  { id: 'all', name: 'All' },
  { id: 'vegetables', name: 'Vegetables' },
  { id: 'fruits', name: 'Fruits' },
  { id: 'seafoods', name: 'Fresh Frozen Seafoods' },
  { id: 'pork', name: 'Fresh Frozen Pork' },
  { id: 'chicken', name: 'Fresh Frozen Chicken' },
  { id: 'bundles', name: 'TIPID Bundle Deals' },
]

export const productCategories: ProductCategory[] = [
  {
    id: 'vegetables',
    name: 'Vegetable',
    products: [
      { id: 1, name: 'Bawang/Garlic', image: 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=200&h=200&fit=crop', price: 66.00, unit: '250g', available: true, stock: 15 },
      { id: 2, name: 'Patatas/Potato', image: 'https://images.unsplash.com/photo-1595231776515-ddffb1f4eb73?w=200&h=200&fit=crop', price: 191.00, unit: '500g', available: true, stock: 8 },
      { id: 3, name: 'Sibuyas/Onion', image: 'https://images.unsplash.com/photo-1590005354167-6da97870c757?w=200&h=200&fit=crop', price: 110.00, unit: '250g', available: true, stock: 20 },
      { id: 4, name: 'Pipino/Cucumber', image: 'https://images.unsplash.com/photo-1449300079323-02e209d9d3a6?w=200&h=200&fit=crop', price: 73.00, unit: '500g', available: true, stock: 12 },
      { id: 5, name: 'Luya/Ginger', image: 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=200&h=200&fit=crop', price: 73.00, unit: '250g', available: true, stock: 6 },
      { id: 6, name: 'Pechay', image: 'https://images.unsplash.com/photo-1582273951693-3b5118239c78?w=200&h=200&fit=crop', price: 66.00, unit: '250g', available: true, stock: 25 },
      { id: 7, name: 'Kamatis/Tomato', image: 'https://images.unsplash.com/photo-1546470427-227c7b3f8fc2?w=200&h=200&fit=crop', price: 48.00, unit: '250g', available: true, stock: 30 },
      { id: 8, name: 'Batong/String Beans', image: 'https://images.unsplash.com/photo-1567375698348-5d9d5ae99de0?w=200&h=200&fit=crop', price: 95.00, unit: '500g', available: true, stock: 10 },
      { id: 9, name: 'Talong/Eggplant', image: 'https://images.unsplash.com/photo-1524593166156-311f36f65f8b?w=200&h=200&fit=crop', price: 88.00, unit: '500g', available: true, stock: 18 },
    ]
  },
  {
    id: 'fruits',
    name: 'Fruits',
    products: [
      { id: 10, name: 'Apple Med. Size (3pcs)', image: 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=200&h=200&fit=crop', price: 162.00, unit: '3pcs', available: true, stock: 20 },
      { id: 11, name: 'Kiat-Kiat', image: 'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?w=200&h=200&fit=crop', price: 184.00, unit: '1 per pack', available: true, stock: 15 },
      { id: 12, name: 'Banana (Large)', image: 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=200&h=200&fit=crop', price: 129.00, unit: '1000g', available: true, stock: 25 },
      { id: 13, name: 'Mango', image: 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=200&h=200&fit=crop', price: 155.00, unit: '500g', available: true, stock: 12 },
      { id: 14, name: 'Watermelon', image: 'https://images.unsplash.com/photo-1589984662646-e7b2e4962f18?w=200&h=200&fit=crop', price: 89.00, unit: '1kg', available: true, stock: 8 },
    ]
  },
  {
    id: 'seafoods',
    name: 'Fresh Frozen Seafoods',
    products: [
      { id: 15, name: 'Tuna Cubes', image: 'https://images.unsplash.com/photo-1574781330855-d0db8cc6a79c?w=200&h=200&fit=crop', price: 253.00, unit: '500g', available: true, stock: 10 },
      { id: 16, name: 'Pampano', image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=200&h=200&fit=crop', price: 303.00, unit: '1 per pack', available: true, stock: 8 },
      { id: 17, name: 'Salmon Strips', image: 'https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?w=200&h=200&fit=crop', price: 203.00, unit: '500g', available: true, stock: 6 },
      { id: 18, name: 'Hipon/Shrimp', image: 'https://images.unsplash.com/photo-1565680018093-ebb6b9d4d864?w=200&h=200&fit=crop', price: 320.00, unit: '500g', available: true, stock: 5 },
    ]
  },
  {
    id: 'pork',
    name: 'Fresh Frozen Pork Meats',
    products: [
      { id: 19, name: 'Pork Belly', image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=200&h=200&fit=crop', price: 287.00, unit: '500g', available: true, stock: 12 },
      { id: 20, name: 'Pork Adobo Cut', image: 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=200&h=200&fit=crop', price: 265.00, unit: '500g', available: true, stock: 8 },
      { id: 21, name: 'Pork Ribs', image: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=200&h=200&fit=crop', price: 310.00, unit: '500g', available: true, stock: 10 },
    ]
  },
  {
    id: 'chicken',
    name: 'Fresh Frozen Chicken',
    products: [
      { id: 22, name: 'Whole Dressed Chicken', image: 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=200&h=200&fit=crop', price: 304.00, unit: '1 per pack', available: true, stock: 15 },
      { id: 23, name: 'Chicken Wings', image: 'https://images.unsplash.com/photo-1606728035253-49e8a23146de?w=200&h=200&fit=crop', price: 320.00, unit: '1 per pack', available: true, stock: 20 },
      { id: 24, name: 'Chicken Drumsticks', image: 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=200&h=200&fit=crop', price: 257.00, unit: '1 per pack', available: true, stock: 18 },
    ]
  },
  {
    id: 'bundles',
    name: 'TIPID Bundle Deals',
    products: [
      { id: 25, name: 'Fresh Vegetable Bundle (Family)', image: 'https://images.unsplash.com/photo-1518843875459-f738682238a6?w=200&h=200&fit=crop', price: 257.00, unit: '1 per bundle', available: true, stock: 10 },
      { id: 26, name: 'Sour Soup Package (Sinigang)', image: 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?w=200&h=200&fit=crop', price: 270.00, unit: '1 per pack', available: true, stock: 8 },
      { id: 27, name: 'Stew Package (Afritada Style)', image: 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=200&h=200&fit=crop', price: 252.00, unit: '1 per pack', available: true, stock: 12 },
    ]
  },
]
