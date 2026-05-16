import type { NotificationItem } from './types'

export const mockNotifications: NotificationItem[] = [
  { id: 1, type: 'order', title: 'New Order Available!', message: 'Order #1234 from Juan\'s Fresh Market. Earnings: ₱85', time: '2 mins ago', read: false, category: 'orders' },
  { id: 2, type: 'tip', title: 'You Got a Tip!', message: 'Maria Santos gave you ₱50 tip for Order #1233!', time: '1 hour ago', read: false, category: 'earnings' },
  { id: 3, type: 'payment', title: 'Payment Received', message: 'You received ₱250 for Order #1232', time: '2 hours ago', read: false, category: 'earnings' },
  { id: 4, type: 'order', title: 'Order Completed', message: 'Order #1231 has been delivered successfully', time: '3 hours ago', read: true, category: 'orders' },
  { id: 5, type: 'warning', title: 'Low Acceptance Rate', message: 'Your acceptance rate is at 75%. Try to accept more orders.', time: 'Yesterday', read: true, category: 'orders' },
  { id: 6, type: 'achievement', title: 'New Badge Earned!', message: 'You earned the "Top Rated" badge! Keep it up!', time: '2 days ago', read: true, category: 'all' },
  { id: 7, type: 'info', title: 'Weekly Earnings Report', message: 'You earned ₱11,196 this week with 67 jobs completed!', time: '3 days ago', read: true, category: 'earnings' },
  { id: 8, type: 'order', title: 'Order Cancelled', message: 'Order #1228 was cancelled by the seller', time: '4 days ago', read: true, category: 'orders' },
]
