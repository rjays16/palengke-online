import type { HistoryOrder } from './types'

export const orders: HistoryOrder[] = [
  { id: 'ORD-001', storeName: "Juan's Fresh Market", storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e', date: '22 Mar 2026, 11:59 AM', items: 'Bawang/Garlic, Kamatis/Tomato', total: 207.00, status: 'completed' },
  { id: 'ORD-002', storeName: "Ana's Palengke", storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e', date: '20 Mar 2026, 06:48 PM', items: 'Apple Med. Size, Banana', total: 291.00, status: 'cancelled' },
  { id: 'ORD-003', storeName: "Rosa's Seafoods", storeImage: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3', date: '18 Mar 2026, 05:19 PM', items: 'Tuna Cubes, Pampano', total: 556.00, status: 'completed' },
  { id: 'ORD-004', storeName: "Mario's Meat Shop", storeImage: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791', date: '15 Mar 2026, 02:30 PM', items: 'Pork Belly, Pork Ribs', total: 597.00, status: 'completed' },
  { id: 'ORD-005', storeName: 'Palengke Bundles', storeImage: 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2', date: '10 Mar 2026, 09:15 AM', items: 'Fresh Vegetable Bundle', total: 257.00, status: 'completed' },
]
