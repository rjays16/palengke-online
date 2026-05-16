import type { Banner, Category, SellerSmall, SellerNearby } from './types'

export const banners: Banner[] = [
  {
    tag: 'LIMITED TIME',
    title: 'Fresh from Palengke\nUp to 20% OFF!',
    subtitle: 'Direct from local vendors',
    cta: 'Order Now',
    emoji: '',
    gradient: 'linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%)',
    image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=200&h=200&fit=crop'
  },
  {
    tag: 'FLASH SALE',
    title: 'Palengke Bundles\nSave More!',
    subtitle: 'Best deals from the market',
    cta: 'See Bundles',
    emoji: '',
    gradient: 'linear-gradient(135deg, #2196F3 0%, #1565C0 100%)',
    image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=200&h=200&fit=crop'
  }
]

export const homeCategories: Category[] = [
  { id: 1, name: 'Vegetables', emoji: '', color: '#E8F5E9', route: 'vegetables', image: 'https://images.unsplash.com/photo-1518843875459-f738682238a6?w=120&h=120&fit=crop' },
  { id: 2, name: 'Fruits', emoji: '', color: '#FCE4EC', route: 'fruits', image: 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=120&h=120&fit=crop' },
  { id: 3, name: 'Seafoods', emoji: '', color: '#E3F2FD', route: 'seafoods', image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=120&h=120&fit=crop' },
  { id: 4, name: 'Pork', emoji: '', color: '#FFF3E0', route: 'pork', image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=120&h=120&fit=crop' },
  { id: 5, name: 'Chicken', emoji: '', color: '#FFF8E1', route: 'chicken', image: 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=120&h=120&fit=crop' },
  { id: 6, name: 'Bundles', emoji: '', color: '#F3E5F5', route: 'bundles', image: 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?w=120&h=120&fit=crop' },
  { id: 7, name: 'Rice', emoji: '', color: '#E0F2F1', route: 'rice', image: 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=120&h=120&fit=crop' },
  { id: 8, name: 'All Items', emoji: '', color: '#f5f5f5', route: 'all', image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=120&h=120&fit=crop' },
]

export const orderAgain: SellerSmall[] = [
  { id: 1, name: "Juan's Market", distance: '2.3 km', emoji: '', color: '#E8F5E9', isOpen: true, promo: '50 off', image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=200&h=150&fit=crop' },
  { id: 2, name: "Ana's Palengke", distance: '1.5 km', emoji: '', color: '#FCE4EC', isOpen: true, promo: null, image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=200&h=150&fit=crop' },
  { id: 3, name: "Rosa's Seafoods", distance: '3.1 km', emoji: '', color: '#E3F2FD', isOpen: false, promo: null, image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=200&h=150&fit=crop' },
]

export const nearbySellers: SellerNearby[] = [
  {
    id: 1, name: "Juan's Fresh Palengke", emoji: '',
    image: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #4CAF50, #2E7D32)',
    rating: '4.8', deliveryTime: '15-25 min', distance: '2.3 km',
    isOpen: true, discount: '50 OFF',
    tags: ['Vegetables', 'Fruits', 'Fresh']
  },
  {
    id: 2, name: "Ana's Palengke - Cogon", emoji: '',
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #FF9800, #E65100)',
    rating: '4.6', deliveryTime: '10-20 min', distance: '1.5 km',
    isOpen: true, discount: null,
    tags: ['Fruits', 'Vegetables']
  },
  {
    id: 3, name: "Rosa's Seafoods & Meats", emoji: '',
    image: 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #2196F3, #1565C0)',
    rating: '4.9', deliveryTime: '20-35 min', distance: '3.1 km',
    isOpen: false, discount: null,
    tags: ['Seafoods', 'Pork', 'Chicken']
  },
  {
    id: 4, name: "Mario's Meat Shop", emoji: '',
    image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #f44336, #b71c1c)',
    rating: '4.7', deliveryTime: '15-30 min', distance: '2.8 km',
    isOpen: true, discount: '30 OFF',
    tags: ['Pork', 'Beef', 'Chicken']
  },
  {
    id: 5, name: 'Palengke Bundle Deals', emoji: '',
    image: 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?w=400&h=200&fit=crop',
    color: 'linear-gradient(135deg, #9C27B0, #4A148C)',
    rating: '4.5', deliveryTime: '25-40 min', distance: '4.2 km',
    isOpen: true, discount: null,
    tags: ['Bundles', 'Tipid', 'Value']
  },
]
