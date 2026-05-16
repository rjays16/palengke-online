import type { CartStore } from './types'

export const cartStores: CartStore[] = [
  {
    id: 1,
    name: "Juan's Fresh Market",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e',
    color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
    itemCount: 3,
    deliveryTime: 'From 25 mins',
    distance: '2.3 km',
    deliveryFee: 45,
    freeDelivery: false,
    items: [
      { id: 1, name: 'Bawang/Garlic (100g)', emoji: '', image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999', price: 66.00, quantity: 1 },
      { id: 2, name: 'Kamatis/Tomato (1kg)', emoji: '', image: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea', price: 48.00, quantity: 2 },
      { id: 3, name: 'Siling Labuyo (50g)', emoji: '', image: 'https://images.unsplash.com/photo-1582284540020-8acbe03f4924', price: 35.00, quantity: 1 }
    ]
  },
  {
    id: 2,
    name: "Ana's Palengke - Cogon",
    emoji: '',
    image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9',
    color: 'linear-gradient(135deg, #FF9800, #E65100)',
    itemCount: 2,
    deliveryTime: 'From 20 mins',
    distance: '1.5 km',
    deliveryFee: 0,
    freeDelivery: true,
    items: [
      { id: 10, name: 'Apple Med. Size', emoji: '', image: 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6', price: 162.00, quantity: 1 },
      { id: 11, name: 'Banana (Large)', emoji: '', image: 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e', price: 129.00, quantity: 1 }
    ]
  }
]
