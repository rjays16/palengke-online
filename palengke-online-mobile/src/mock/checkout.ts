import type { CheckoutStore, OrderItem, SuggestedItem, DeliveryAddress, DeliveryOption } from './types'

export const checkoutStoreData: CheckoutStore = {
  id: 1,
  name: "Juan's Fresh Market",
  address: 'Stall #15, CDO City Palengke',
  distance: '2.3 km',
  pickupTime: '13',
  image: 'https://images.unsplash.com/photo-1542838132-92c53300491e'
}

export const orderItems: OrderItem[] = [
  { id: 1, name: 'Bawang/Garlic', emoji: '', image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999', price: 66.00, quantity: 1, variant: '250g' },
  { id: 2, name: 'Kamatis/Tomato', emoji: '', image: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea', price: 48.00, quantity: 2, variant: '250g' },
]

export const suggestedItems: SuggestedItem[] = [
  { id: 5, name: 'Sibuyas/Onion', emoji: '', image: 'https://images.unsplash.com/photo-1607305387299-a3d9611cd469', price: 110.00 },
  { id: 6, name: 'Luya/Ginger', emoji: '', image: 'https://images.unsplash.com/photo-1615485500704-8e990f9900f7', price: 73.00 },
  { id: 7, name: 'Pechay', emoji: '', image: 'https://images.unsplash.com/photo-1582284540020-8acbe03f4924', price: 66.00 },
]

export const deliveryAddress: DeliveryAddress = {
  main: 'Brgy. Carmen, CDO City',
  sub: 'Cagayan de Oro City, Misamis Oriental'
}

export const deliveryOptions: DeliveryOption[] = [
  { id: 'standard', name: 'Standard', time: '25-35', price: 45, promise: null },
  { id: 'express', name: 'Express', time: '15-20', price: 75, promise: '⚡ Faster delivery' },
  { id: 'saver', name: 'Saver', time: '40-55', price: 30, promise: null },
]

export const selectedPayment = { icon: 'card', label: 'GCash', image: '/assets/images/payment-methods-logo/gcash.png' }
