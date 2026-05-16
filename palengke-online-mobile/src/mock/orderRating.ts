import type { OrderRatingData } from './types'

export const orderData: OrderRatingData = {
  storeName: "Juan's Fresh Market",
  storeImage: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=100&h=100&fit=crop',
  items: 'Bawang/Garlic, Kamatis/Tomato',
  total: 207.00
}

export const positiveTags = [
  'Professional', 'Punctuality', 'Friendly',
  'Contactless Delivery', 'Followed Instructions', 'Hygienic'
]

export const negativeTags = [
  'Late Delivery', 'Wrong Items', 'Poor Packaging',
  'Unprofessional', 'Missing Items', 'Cold Food'
]
