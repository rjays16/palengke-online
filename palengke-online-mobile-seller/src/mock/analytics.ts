import { cartOutline, cashOutline, alertCircleOutline, closeCircleOutline } from 'ionicons/icons'

export const ANALYTICS_STATS = {
  totalSales: 18750,
  salesGrowth: 12.5,
  totalOrders: 68,
  ordersGrowth: 8.3,
  avgOrderValue: 275,
  completionRate: 94,
  avgDeliveryTime: 45,
  rating: 4.8,
  customers: 142
}

export const SALES_CHART_DATA = [
  { day: 'Mon', sales: 2500, orders: 12 },
  { day: 'Tue', sales: 3200, orders: 15 },
  { day: 'Wed', sales: 2800, orders: 11 },
  { day: 'Thu', sales: 3500, orders: 14 },
  { day: 'Fri', sales: 3100, orders: 13 },
  { day: 'Sat', sales: 1900, orders: 8 },
  { day: 'Sun', sales: 1750, orders: 7 }
]

export const TOP_PRODUCTS = [
  { id: 1, name: 'Tomatoes', sold: 45, revenue: 3600, percentage: 100 },
  { id: 2, name: 'Onions', sold: 38, revenue: 3040, percentage: 84 },
  { id: 3, name: 'Potatoes', sold: 32, revenue: 2880, percentage: 71 },
  { id: 4, name: 'Carrots', sold: 28, revenue: 2240, percentage: 62 },
  { id: 5, name: 'Cabbage', sold: 22, revenue: 1760, percentage: 49 }
]

export const CATEGORY_SALES = [
  { name: 'Vegetables', sales: 8500, orders: 35, percentage: 45, color: 'green' },
  { name: 'Fruits', sales: 6200, orders: 18, percentage: 33, color: 'orange' },
  { name: 'Meat', sales: 2800, orders: 10, percentage: 15, color: 'red' },
  { name: 'Seafood', sales: 1250, orders: 5, percentage: 7, color: 'blue' }
]

export const RECENT_ACTIVITY = [
  {
    type: 'sale',
    icon: cartOutline,
    text: 'New order from Maria Santos',
    time: '5 mins ago',
    amount: 450
  },
  {
    type: 'payment',
    icon: cashOutline,
    text: 'Payment received for Order #1234',
    time: '15 mins ago',
    amount: 890
  },
  {
    type: 'sale',
    icon: cartOutline,
    text: 'Order #1235 completed',
    time: '1 hour ago',
    amount: 320
  },
  {
    type: 'warning',
    icon: alertCircleOutline,
    text: 'Low stock: Tomatoes (5kg remaining)',
    time: '2 hours ago'
  },
  {
    type: 'cancelled',
    icon: closeCircleOutline,
    text: 'Order #1230 cancelled by customer',
    time: '3 hours ago'
  }
]

export const MONTHLY_GOALS = {
  salesTarget: 50000,
  ordersTarget: 100,
  satisfactionTarget: 5.0
}
