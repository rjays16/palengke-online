export const MOCK_NOTIFICATIONS = [
  {
    id: 1,
    type: 'order',
    title: 'New Order Received',
    message: 'Maria Santos placed an order for ₱1,250.50',
    time: '2026-01-03T14:30:00',
    read: false,
    orderId: '001'
  },
  {
    id: 2,
    type: 'message',
    title: 'New Message',
    message: 'Juan: "Is the Bangus still available?"',
    time: '2026-01-03T14:15:00',
    read: false,
    customerId: 'customer_002'
  },
  {
    id: 3,
    type: 'payment',
    title: 'Payment Received',
    message: 'Payment of ₱850.00 received for Order #002',
    time: '2026-01-03T13:45:00',
    read: true,
    orderId: '002'
  },
  {
    id: 4,
    type: 'order',
    title: 'Order Completed',
    message: 'Order #003 has been delivered successfully',
    time: '2026-01-03T12:30:00',
    read: true,
    orderId: '003'
  },
  {
    id: 5,
    type: 'warning',
    title: 'Low Stock Alert',
    message: 'Pork Belly is running low (8 kg left)',
    time: '2026-01-03T11:00:00',
    read: false,
    productId: 'prod_002'
  },
  {
    id: 6,
    type: 'message',
    title: 'New Message',
    message: 'Ana: "Thank you for the fresh vegetables!"',
    time: '2026-01-03T10:15:00',
    read: true,
    customerId: 'customer_003'
  },
  {
    id: 7,
    type: 'order',
    title: 'Order Cancelled',
    message: 'Order #004 was cancelled by customer',
    time: '2026-01-03T09:30:00',
    read: true,
    orderId: '004'
  },
  {
    id: 8,
    type: 'info',
    title: 'Weekly Sales Report',
    message: 'Your sales this week: ₱18,750 (+12.5%)',
    time: '2026-01-02T08:00:00',
    read: true
  },
  {
    id: 9,
    type: 'order',
    title: 'New Order Received',
    message: 'Lisa Ramos placed an order for ₱980.00',
    time: '2026-01-02T16:45:00',
    read: true,
    orderId: '005'
  },
  {
    id: 10,
    type: 'warning',
    title: 'Low Stock Alert',
    message: 'Banana (Lakatan) is running low (5 kg left)',
    time: '2026-01-02T14:20:00',
    read: true,
    productId: 'prod_005'
  }
]
