export const MOCK_CONVERSATIONS = [
  {
    id: 1,
    name: 'Maria Santos',
    type: 'customer',
    avatar: null,
    lastMessage: 'Thank you po! The vegetables are fresh!',
    time: '2 mins ago',
    unread: 0,
    orderId: '1234',
    messages: [
      { text: 'Hello po! Do you have fresh tomatoes?', sent: false, time: '10:30 AM', status: 'read' },
      { text: 'Yes po! Just arrived this morning', sent: true, time: '10:31 AM', status: 'read' },
      { text: 'How much per kilo?', sent: false, time: '10:32 AM', status: 'read' },
      { text: '₱80/kg po', sent: true, time: '10:32 AM', status: 'read' },
      { text: 'Ok, I\'ll order 2kg please', sent: false, time: '10:33 AM', status: 'read' },
      {
        text: 'Sure po! Adding to your order now',
        sent: true,
        time: '10:34 AM',
        status: 'read',
        orderInfo: {
          id: '1234',
          items: [
            { name: 'Tomatoes', qty: '2', unit: 'kg' },
            { name: 'Onions', qty: '1', unit: 'kg' }
          ],
          total: 230
        }
      },
      { text: 'Thank you po! The vegetables are fresh!', sent: false, time: '11:45 AM', status: 'delivered' }
    ]
  },
  {
    id: 2,
    name: 'Driver Juan',
    type: 'driver',
    avatar: null,
    lastMessage: 'Picked up order #1233. On the way!',
    time: '15 mins ago',
    unread: 2,
    orderId: '1233',
    messages: [
      { text: 'Good morning! I\'m arriving in 5 minutes for pickup', sent: false, time: '9:00 AM', status: 'read' },
      { text: 'Ok po, I have the order ready', sent: true, time: '9:01 AM', status: 'read' },
      { text: 'Picked up order #1233. On the way!', sent: false, time: '9:15 AM', status: 'delivered' }
    ]
  },
  {
    id: 3,
    name: 'Pedro Cruz',
    type: 'customer',
    avatar: null,
    lastMessage: 'Is the fish fresh caught today?',
    time: '1 hour ago',
    unread: 1,
    orderId: null,
    messages: [
      { text: 'Is the fish fresh caught today?', sent: false, time: '8:30 AM', status: 'delivered' }
    ]
  },
  {
    id: 4,
    name: 'Driver Ana',
    type: 'driver',
    avatar: null,
    lastMessage: 'Delivered order #1232 successfully!',
    time: '2 hours ago',
    unread: 0,
    orderId: '1232',
    messages: [
      { text: 'On my way to pick up order #1232', sent: false, time: '7:00 AM', status: 'read' },
      { text: 'Please prepare it po, I\'m 5 mins away', sent: false, time: '7:10 AM', status: 'read' },
      { text: 'Ready po!', sent: true, time: '7:11 AM', status: 'read' },
      { text: 'Picked up! Customer location next', sent: false, time: '7:20 AM', status: 'read' },
      { text: 'Delivered order #1232 successfully!', sent: false, time: '7:45 AM', status: 'read' }
    ]
  },
  {
    id: 5,
    name: 'Lisa Ramos',
    type: 'customer',
    avatar: null,
    lastMessage: 'Can you cut the chicken into small pieces?',
    time: 'Yesterday',
    unread: 0,
    orderId: '1231',
    messages: [
      { text: 'I want to order chicken', sent: false, time: 'Yesterday 4:00 PM', status: 'read' },
      { text: 'Ok po! How many kilos?', sent: true, time: 'Yesterday 4:01 PM', status: 'read' },
      { text: '2 kilos please', sent: false, time: 'Yesterday 4:02 PM', status: 'read' },
      { text: 'Can you cut the chicken into small pieces?', sent: false, time: 'Yesterday 4:03 PM', status: 'read' },
      { text: 'No problem po! I\'ll prepare it', sent: true, time: 'Yesterday 4:04 PM', status: 'read' }
    ]
  }
]

export const QUICK_REPLIES = [
  'Yes, available po!',
  'Out of stock po',
  'On the way!',
  'Thank you!'
]

export const AUTO_RESPONSES = [
  'Ok po!',
  'Salamat!',
  'Received po',
  'Copy that!',
  'Noted po'
]
