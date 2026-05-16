import type { LinkedPaymentMethod, AddPaymentMethod } from './types'

export const linkedMethods: LinkedPaymentMethod[] = [
  { id: 'wallet', name: 'Palengke Wallet', type: 'wallet', balance: '152.00', isDefault: true, image: '/assets/images/payment-methods-logo/palengke-wallet.png' },
  { id: 'gcash', name: 'GCash', type: 'gcash', balance: null, isDefault: false, image: '/assets/images/payment-methods-logo/gcash.png' },
  { id: 'cash', name: 'Cash On Delivery', type: 'cash', balance: null, isDefault: false, image: null },
]

export const addMethods: AddPaymentMethod[] = [
  { id: 'add-wallet', name: 'Cash in Balance', type: 'wallet', description: 'Earn up to 0.8% back in PalengkePoints', image: '/assets/images/payment-methods-logo/palengke-wallet.png' },
  { id: 'add-card', name: 'Cards', type: 'card', description: null, image: '/assets/images/payment-methods-logo/cards.png', comingSoon: true },
  { id: 'add-maya', name: 'Maya', type: 'maya', description: null, image: '/assets/images/payment-methods-logo/maya.png' },
  { id: 'add-paypal', name: 'PayPal', type: 'paypal', description: null, image: '/assets/images/payment-methods-logo/paypal.png', comingSoon: true },
]
