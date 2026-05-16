import type { FavoriteSeller } from './types'

export const favoriteSellers: FavoriteSeller[] = [
  {
    id: 1, name: "Juan's Fresh Market", emoji: '',
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e',
    color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
    rating: '4.8', deliveryTime: '15-25 min', distance: '2.3 km',
    tags: ['Vegetables', 'Fruits']
  },
  {
    id: 2, name: "Ana's Palengke", emoji: '',
    image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999',
    color: 'linear-gradient(135deg, #FF9800, #E65100)',
    rating: '4.6', deliveryTime: '10-20 min', distance: '1.5 km',
    tags: ['Fruits', 'Vegetables']
  }
]
