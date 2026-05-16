import type { Chat } from './types'

export const chats: Chat[] = [
  {
    id: 1,
    name: "Juan's Fresh Market",
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=100&h=100&fit=crop',
    lastMessage: 'Your order is ready for pickup!',
    time: '5 mins ago',
    unread: 2,
    online: true,
    messages: [
      { text: 'Hello! Thank you for your order', sent: false, time: '10:00 AM', status: 'read' },
      { text: 'Hi! When will my order be ready?', sent: true, time: '10:05 AM', status: 'read' },
      { text: 'Your order is ready for pickup!', sent: false, time: '10:10 AM', status: 'read' },
      { text: 'Thank you! I\'ll pick it up now', sent: true, time: '10:15 AM', status: 'read' }
    ]
  },
  {
    id: 2,
    name: 'Juan Dela Cruz (Driver)',
    image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop',
    lastMessage: 'I am on my way to your location',
    time: '10 mins ago',
    unread: 1,
    online: true,
    messages: [
      { text: 'Good morning! I\'m your driver today', sent: false, time: '9:30 AM', status: 'read' },
      { text: 'Hi! How far are you?', sent: true, time: '9:35 AM', status: 'read' },
      { text: 'I am on my way to your location', sent: false, time: '9:40 AM', status: 'delivered' }
    ]
  },
  {
    id: 3,
    name: "Rosa's Seafoods",
    image: 'https://images.unsplash.com/photo-1534482421-64566f976cfa?w=100&h=100&fit=crop',
    lastMessage: 'Thank you for ordering!',
    time: '1 hour ago',
    unread: 0,
    online: false,
    messages: [
      { text: 'Good morning!', sent: false, time: '8:00 AM', status: 'read' },
      { text: 'Thanks Rosa!', sent: true, time: '8:05 AM', status: 'read' }
    ]
  },
]
