import type { ActiveOrder, RecentOrder } from './types'

export const activeOrder: ActiveOrder = {
  id: 'ORD-001',
  storeName: "Juan's Fresh Market",
  storeEmoji: '',
  storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e',
  status: 'Picking up from Palengke',
  eta: 'Arriving by 1:05 PM'
}

export const recentOrders: RecentOrder[] = [
  {
    id: 'ORD-006', storeName: "Ana's Palengke - Cogon",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e', type: 'fruits',
    date: 'Today, 11:59 AM', total: 291.00, status: 'pending',
    items: ['Apple', 'Banana']
  },
  {
    id: 'ORD-005', storeName: "Juan's Fresh Market",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e', type: 'market',
    date: 'Today, 10:30 AM', total: 207.00, status: 'delivering',
    items: ['Bawang/Garlic', 'Kamatis/Tomato']
  },
  {
    id: 'ORD-004', storeName: "Mario's Meat Shop",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791', type: 'meat',
    date: 'Today, 09:30 AM', total: 450.00, status: 'completed',
    items: ['Pork Belly', 'Chicken']
  },
  {
    id: 'ORD-003', storeName: "Rosa's Seafoods",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3', type: 'seafood',
    date: 'Yesterday, 06:48 PM', total: 320.00, status: 'cancelled',
    items: ['Tilapia', 'Shrimp']
  },
  {
    id: 'ORD-002', storeName: "Lily's Vegetables",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1540420773420-3366772f4999', type: 'vegetables',
    date: 'Mar 18, 2026, 05:19 PM', total: 180.00, status: 'completed',
    items: ['Kangkong', 'Spinach']
  },
  {
    id: 'ORD-001', storeName: "Tito's Variety Store",
    storeEmoji: '', storeImage: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9', type: 'variety',
    date: 'Mar 15, 2026, 02:30 PM', total: 250.00, status: 'completed',
    items: ['Sugar', 'Salt']
  },
]
