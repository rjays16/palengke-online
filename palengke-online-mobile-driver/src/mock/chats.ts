import type { ChatItem } from './types'

export const mockChatNotifications = [
  { id: 1, type: 'order', title: 'New Order Available!', message: 'Order #1234 from Juan\'s Fresh Market - ₱85 earnings', time: '2 mins ago', read: false },
  { id: 2, type: 'payment', title: 'Payment Received', message: 'You received ₱250 for Order #1233', time: '1 hour ago', read: false },
  { id: 3, type: 'tip', title: 'You Got a Tip!', message: 'Maria Santos gave you a ₱50 tip. Thank you!', time: '2 hours ago', read: true },
  { id: 4, type: 'warning', title: 'Low Acceptance Rate', message: 'Your acceptance rate dropped to 75%. Try to accept more orders.', time: 'Yesterday', read: true },
  { id: 5, type: 'info', title: 'Weekly Earnings Report', message: 'You earned ₱11,196 this week! Great job!', time: '2 days ago', read: true },
  { id: 6, type: 'achievement', title: 'New Badge Earned!', message: 'You earned the "Fast Delivery" badge!', time: '3 days ago', read: true },
]

export const mockChats: ChatItem[] = [
  { id: 1, name: 'Maria Santos', type: 'customer', lastMessage: 'Thank you kuya! Very fast!', time: '5 mins ago', unread: 2, orderId: '1234' },
  { id: 2, name: "Juan's Fresh Market", type: 'seller', lastMessage: 'Order is ready for pickup!', time: '30 mins ago', unread: 1, orderId: '1234' },
  { id: 3, name: 'Pedro Cruz', type: 'customer', lastMessage: 'How long will it take?', time: '1 hour ago', unread: 0, orderId: '1233' },
  { id: 4, name: "Ana's Palengke", type: 'seller', lastMessage: 'Please come to stall 8', time: 'Yesterday', unread: 0, orderId: '1232' },
]
