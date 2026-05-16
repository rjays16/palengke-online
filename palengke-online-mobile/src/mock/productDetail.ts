import type { ProductDetail, SimilarProduct } from './types'

export const product: ProductDetail = {
  id: 1,
  name: 'Bawang/Garlic',
  image: 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=600&h=600&fit=crop',
  price: 66.00,
  unit: '250g',
  category: 'Vegetables',
  available: true,
  discount: null,
  description: 'Fresh garlic sourced directly from local farmers. Perfect for cooking Filipino dishes. Rich in flavor and nutrients.',
  sellerName: "Juan's Fresh Market",
  sellerImage: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=60&h=60&fit=crop',
  sellerRating: '4.8',
  sellerDistance: '2.3 km',
  sellerId: 1
}

export const similarProducts: SimilarProduct[] = [
  { id: 2, name: 'Sibuyas/Onion', image: 'https://images.unsplash.com/photo-1590005354167-6da97870c757?w=200&h=200&fit=crop', price: 110.00, unit: '250g' },
  { id: 3, name: 'Luya/Ginger', image: 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=200&h=200&fit=crop', price: 73.00, unit: '250g' },
  { id: 4, name: 'Kamatis/Tomato', image: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=200&h=200&fit=crop', price: 48.00, unit: '250g' },
  { id: 5, name: 'Pechay', image: 'https://images.unsplash.com/photo-1584270354949-c26b0d5b4a0c?w=200&h=200&fit=crop', price: 66.00, unit: '250g' },
]
