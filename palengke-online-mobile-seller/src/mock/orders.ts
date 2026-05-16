export interface MockOrderItem {
  name: string
  qty: string
  unit: string
  price: number
}

export interface MockOrder {
  id: string
  customerName: string
  date: string
  status: 'pending' | 'processing' | 'completed' | 'cancelled'
  itemCount: number
  total: number
  items: MockOrderItem[]
  customerPhone: string
  deliveryAddress: string
}

export const MOCK_ORDERS: Record<string, MockOrder> = {
  '001': {
    id: '001',
    customerName: 'Maria Santos',
    date: '2026-01-02T10:30:00',
    status: 'pending',
    itemCount: 5,
    total: 1250.50,
    items: [
      { name: 'Tomatoes', qty: '1', unit: 'kg', price: 80 },
      { name: 'Lettuce', qty: '1/2', unit: 'kg', price: 45 },
      { name: 'Carrots', qty: '1.5', unit: 'kg', price: 60 },
      { name: 'Onions', qty: '2', unit: 'kg', price: 50 },
      { name: 'Potatoes', qty: '3', unit: 'kg', price: 100 }
    ],
    customerPhone: '+63 917 123 4567',
    deliveryAddress: '123 Main St, Cagayan de Oro City'
  },
  '002': {
    id: '002',
    customerName: 'Juan Dela Cruz',
    date: '2026-01-02T09:15:00',
    status: 'processing',
    itemCount: 3,
    total: 850.00,
    items: [
      { name: 'Cabbage', qty: '2', unit: 'kg', price: 70 },
      { name: 'Eggplant', qty: '1.5', unit: 'kg', price: 120 },
      { name: 'Bell Peppers', qty: '1', unit: 'kg', price: 150 }
    ],
    customerPhone: '+63 918 234 5678',
    deliveryAddress: '456 Oak Ave, Cagayan de Oro City'
  },
  '003': {
    id: '003',
    customerName: 'Ana Reyes',
    date: '2026-01-01T14:20:00',
    status: 'completed',
    itemCount: 7,
    total: 2100.75,
    items: [
      { name: 'Mangoes', qty: '2', unit: 'kg', price: 300 },
      { name: 'Bananas', qty: '3', unit: 'kg', price: 150 },
      { name: 'Oranges', qty: '2.5', unit: 'kg', price: 200 },
      { name: 'Apples', qty: '1.5', unit: 'kg', price: 250 },
      { name: 'Grapes', qty: '1', unit: 'kg', price: 180 },
      { name: 'Watermelon', qty: '1', unit: 'pc', price: 200 },
      { name: 'Pineapple', qty: '2', unit: 'pcs', price: 160 }
    ],
    customerPhone: '+63 919 345 6789',
    deliveryAddress: '789 Pine Rd, Cagayan de Oro City'
  },
  '004': {
    id: '004',
    customerName: 'Pedro Garcia',
    date: '2026-01-01T11:45:00',
    status: 'cancelled',
    itemCount: 2,
    total: 450.00,
    items: [
      { name: 'Garlic', qty: '1/2', unit: 'kg', price: 90 },
      { name: 'Ginger', qty: '1/4', unit: 'kg', price: 80 }
    ],
    customerPhone: '+63 920 456 7890',
    deliveryAddress: '321 Elm St, Cagayan de Oro City'
  },
  '005': {
    id: '005',
    customerName: 'Lisa Ramos',
    date: '2026-01-02T12:00:00',
    status: 'pending',
    itemCount: 4,
    total: 980.00,
    items: [
      { name: 'Pork (Liempo)', qty: '1.5', unit: 'kg', price: 350 },
      { name: 'Chicken', qty: '2', unit: 'kg', price: 280 },
      { name: 'Bangus', qty: '3', unit: 'pcs', price: 200 },
      { name: 'Shrimp', qty: '1/2', unit: 'kg', price: 150 }
    ],
    customerPhone: '+63 921 567 8901',
    deliveryAddress: '654 Maple Dr, Cagayan de Oro City'
  }
}
