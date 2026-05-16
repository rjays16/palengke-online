export interface Banner {
  tag: string
  title: string
  subtitle: string
  cta: string
  emoji: string
  gradient: string
  image: string
}

export interface Category {
  id: number
  name: string
  emoji: string
  color: string
  route: string
  image: string
}

export interface SellerSmall {
  id: number
  name: string
  distance: string
  emoji: string
  color: string
  isOpen: boolean
  promo: string | null
  image: string
}

export interface SellerNearby {
  id: number
  name: string
  emoji: string
  image: string
  color: string
  rating: string
  deliveryTime: string
  distance: string
  isOpen: boolean
  discount: string | null
  tags: string[]
}

export interface SellerBrowse {
  id: number
  name: string
  emoji: string
  image: string
  color: string
  rating: string
  reviewCount: number
  category: string
  deliveryTime: string
  distance: string
  deliveryFee: number
  freeDelivery: boolean
  isOpen: boolean
  discount: string | null
  promo: string | null
  tags: string[]
}

export interface CartItem {
  id: number
  name: string
  emoji: string
  image: string
  price: number
  quantity: number
}

export interface CartStore {
  id: number
  name: string
  emoji: string
  image: string
  color: string
  itemCount: number
  deliveryTime: string
  distance: string
  deliveryFee: number
  freeDelivery: boolean
  items: CartItem[]
}

export interface CheckoutStore {
  id: string | number
  name: string
  address: string
  distance: string
  pickupTime: string
  image: string
}

export interface OrderItem {
  id: number
  name: string
  emoji: string
  image: string
  price: number
  quantity: number
  variant?: string
}

export interface SuggestedItem {
  id: number
  name: string
  emoji: string
  image: string
  price: number
}

export interface DeliveryAddress {
  main: string
  sub: string
}

export interface DeliveryOption {
  id: string
  name: string
  time: string
  price: number
  promise: string | null
}

export interface ActiveOrder {
  id: string
  storeName: string
  storeEmoji: string
  storeImage: string
  status: string
  eta: string
}

export interface RecentOrder {
  id: string
  storeName: string
  storeEmoji: string
  storeImage: string
  type: string
  date: string
  total: number
  status: string
  items: string[]
}

export interface FavoriteSeller {
  id: number
  name: string
  emoji: string
  image: string
  color: string
  rating: string
  deliveryTime: string
  distance: string
  tags: string[]
}

export interface Address {
  id: number
  type: string
  name: string
  address: string
  isDefault: boolean
}

export interface Notification {
  id: number
  type: string
  title: string
  message: string
  time: string
  read: boolean
  image: string
}

export interface Message {
  text: string
  sent: boolean
  time: string
  status: string
}

export interface Chat {
  id: number
  name: string
  image: string
  lastMessage: string
  time: string
  unread: number
  online: boolean
  messages: Message[]
}

export interface Faq {
  id: number
  question: string
  answer: string
}

export interface Offer {
  id: number
  title: string
  color: string
  description: string
  available: boolean
}

export interface HistoryOrder {
  id: string
  storeName: string
  storeImage: string
  date: string
  items: string
  total: number
  status: string
}

export interface OrderStatus {
  eta: string
  statusText: string
  statusColor: string
  deliveryType: string
}

export interface DriverInfo {
  name: string
  rating: string
  vehicleId: string
  image: string
}

export interface PromoBanner {
  title: string
  subtitle: string
}

export interface LinkedPaymentMethod {
  id: string
  name: string
  type: string
  balance: string | null
  isDefault: boolean
  image: string | null
}

export interface AddPaymentMethod {
  id: string
  name: string
  type: string
  description: string | null
  image: string | null
  comingSoon?: boolean
}

export interface ProfileData {
  name: string
  avatar: string
  email: string
  phone: string
  totalOrders: number
  points: number
  favoriteCount: number
}

export interface ProductDetail {
  id: string | number
  name: string
  image: string
  price: number
  unit: string
  category: string
  available: boolean
  discount: number | null
  description: string
  sellerName: string
  sellerImage: string
  sellerRating: string
  sellerDistance: string
  sellerId: number
}

export interface SimilarProduct {
  id: number
  name: string
  image: string
  price: number
  unit: string
}

export interface StoreData {
  id: string | number
  name: string
  emoji: string
  color: string
  rating: string
  reviewCount: number
  distance: string
  isOpen: boolean
  openTime: string
  promo: string
  minSpend: string
  deliveryFee: number
  deliveryTime: string
}

export interface CategoryTab {
  id: string
  name: string
}

export interface StoreProduct {
  id: number
  name: string
  image: string
  price: number
  unit: string
  available: boolean
  stock?: number
}

export interface ProductCategory {
  id: string
  name: string
  products: StoreProduct[]
}

export interface OrderRatingData {
  storeName: string
  storeImage: string
  items: string
  total: number
}

export interface ReceiptItem {
  name: string
  qty: number
  price: number
}

export interface OrderReceipt {
  id: string | number
  storeName: string
  storeImage: string
  date: string
  itemsList: ReceiptItem[]
  subtotal: number
  deliveryFee: number
  serviceFee: number
  discount: number
  total: number
  paymentMethod: string
  deliveryAddress: string
}

export interface TipOrderData {
  storeName: string
  storeEmoji: string
  storeImage: string
  storeAddress: string
  paymentIcon: string
  paymentLabel: string
  paymentImage: string
  subtotal: number
  deliveryFee: number
  deliveryAddress: string
  deliveryAddressSub: string
}
