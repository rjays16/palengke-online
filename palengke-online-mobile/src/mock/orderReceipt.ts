import type { OrderReceipt } from './types'

export const order: OrderReceipt = {
  id: 'ORD-001',
  storeName: "Juan's Fresh Market",
  storeImage: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=100&h=100&fit=crop',
  date: '22 Mar 2026, 11:59 AM',
  itemsList: [
    { name: 'Bawang/Garlic (250g)', qty: 2, price: 66.00 },
    { name: 'Kamatis/Tomato (250g)', qty: 1, price: 48.00 },
  ],
  subtotal: 180.00,
  deliveryFee: 15.00,
  serviceFee: 12.00,
  discount: 0,
  total: 207.00,
  paymentMethod: 'Cash on Delivery',
  deliveryAddress: '123 Main Street, Brgy. Carmen, CDO City'
}
