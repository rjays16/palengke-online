import type { SellerBrowse } from './types'

export const allSellers: SellerBrowse[] = [
  {
    id: 1, name: "Juan's Fresh Market", emoji: '',
    image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
    rating: '4.8', reviewCount: 124, category: 'Vegetables & Fruits',
    deliveryTime: '15-25 min', distance: '2.3 km',
    deliveryFee: 45, freeDelivery: false,
    isOpen: true, discount: 'OFF', promo: 'off min. 300 order',
    tags: ['vegetables', 'fruits']
  },
  {
    id: 2, name: "Ana's Palengke - Cogon", emoji: '',
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #FF9800, #E65100)',
    rating: '4.6', reviewCount: 89, category: 'Fruits & Vegetables',
    deliveryTime: '10-20 min', distance: '1.5 km',
    deliveryFee: 0, freeDelivery: true,
    isOpen: true, discount: null, promo: null,
    tags: ['fruits', 'vegetables']
  },
  {
    id: 3, name: "Rosa's Seafoods & Meats", emoji: '',
    image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #2196F3, #1565C0)',
    rating: '4.9', reviewCount: 203, category: 'Seafoods & Meats',
    deliveryTime: '20-35 min', distance: '3.1 km',
    deliveryFee: 60, freeDelivery: false,
    isOpen: false, discount: null, promo: null,
    tags: ['seafoods', 'pork', 'chicken']
  },
  {
    id: 4, name: "Mario's Meat Shop", emoji: '',
    image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #f44336, #b71c1c)',
    rating: '4.7', reviewCount: 156, category: 'Pork & Beef',
    deliveryTime: '15-30 min', distance: '2.8 km',
    deliveryFee: 0, freeDelivery: true,
    isOpen: true, discount: 'OFF', promo: 'off your first order',
    tags: ['pork', 'beef', 'chicken']
  },
  {
    id: 5, name: 'Palengke Bundle Deals', emoji: '',
    image: 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #9C27B0, #4A148C)',
    rating: '4.5', reviewCount: 67, category: 'Bundle Deals',
    deliveryTime: '25-40 min', distance: '4.2 km',
    deliveryFee: 55, freeDelivery: false,
    isOpen: true, discount: null, promo: null,
    tags: ['bundles']
  },
  {
    id: 6, name: 'CDO Fresh Chicken Hub', emoji: '',
    image: 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #FF9800, #F57F17)',
    rating: '4.4', reviewCount: 91, category: 'Chicken & Poultry',
    deliveryTime: '15-25 min', distance: '1.8 km',
    deliveryFee: 0, freeDelivery: true,
    isOpen: true, discount: null, promo: 'Buy 2 get 1 free!',
    tags: ['chicken']
  },
]
