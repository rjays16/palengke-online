import type { Notification } from './types'

export const notifications: Notification[] = [
  { id: 1, type: 'system', title: 'Welcome to Palengke Online!', message: 'Start ordering fresh produce from your local market.', time: 'Just now', read: false, image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=100&h=100&fit=crop' },
  { id: 2, type: 'promo', title: 'Flash Sale!', message: 'Get 20% off on all vegetables today only!', time: '1 hour ago', read: false, image: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=100&h=100&fit=crop' },
  { id: 3, type: 'order', title: 'Order Delivered!', message: "Your order from Rosa's Seafoods has been successfully delivered.", time: 'Yesterday', read: true, image: 'https://images.unsplash.com/photo-1534482421-64566f976cfa?w=100&h=100&fit=crop' },
  { id: 4, type: 'promo', title: 'New Promo!', message: 'Use code PALENGKE50 for off your next order.', time: '2 days ago', read: true, image: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=100&h=100&fit=crop' },
  { id: 5, type: 'order', title: 'Order Confirmed!', message: "Your order from Ana's Palengke has been confirmed.", time: '3 days ago', read: true, image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=100&h=100&fit=crop' },
]
