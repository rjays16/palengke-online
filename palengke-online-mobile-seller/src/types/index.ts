export interface Product {
  id: number
  name: string
  category: string
  description: string
  image: string
  price: number
  originalPrice: number | null
  discountPrice: number | null
  hasDiscount: boolean
  unit: string
  stock: number
  minimumOrder: number | null
  sku: string
  origin: string
  brand: string
  isFeatured: boolean
  isActive: boolean
  status: 'active' | 'inactive'
  views: number
  sold: number
  images: string[]
  trackInventory: boolean
}

export interface OrderItem {
  name: string
  qty: number
  unit: string
  price: number
}

export interface Order {
  id: number | string
  customerName: string
  customerPhone: string
  deliveryAddress: string
  latitude: number
  longitude: number
  items: OrderItem[]
  itemCount: number
  total: number
  status: 'pending' | 'processing' | 'completed' | 'cancelled'
  date: string
  paymentMethod: string
}

export interface Message {
  id: number | string
  text: string
  sent?: boolean
  sender?: string
  time: string
  status?: 'delivered' | 'read'
  image?: string
  orderInfo?: {
    id: number | string
    items: { name: string; qty: number; unit: string }[]
    total: number
  }
}

export interface Conversation {
  id: number | string
  name: string
  type: 'customer' | 'driver'
  lastMessage: string
  unread: number
  muted: boolean
  avatar: string
  messages: Message[]
  time?: string
  orderId?: string | number
}

export interface Notification {
  id: number | string
  title: string
  message: string
  type: 'order' | 'message' | 'promo' | 'system' | string
  read: boolean
  time: string
}

export interface Transaction {
  id: number | string
  type: 'top_up' | 'subscription_payment' | string
  amount: number
  created_at: string
  payment_method_name?: string
  notes?: string
}

export interface PaymentMethod {
  id: number
  name: string
  description?: string
  color?: string
  coming_soon?: boolean
  enabled?: boolean
  type?: string
  account_name?: string
  account_number?: string
}

export interface Device {
  id: number | string
  device_name: string
  device_id: string
  is_current: boolean
  last_used_at: string
  created_at: string
}

export interface CountryCode {
  code: string
  country: string
  abbr: string
  digits: number
}

export interface Country {
  code: string
  name: string
}

export interface SellerProfile {
  name: string
  phone: string
  email: string
  address: string
  businessType: string
  shopName: string
  shopAddress: string
  shopHours: string
  description: string
  categories: string[]
  avatar: string | null
  totalOrders: number
  rating: number
  joinedDays: number
}

export interface DayHours {
  openHour: number
  openMin: string
  openAmPm: 'AM' | 'PM'
  closeHour: number
  closeMin: string
  closeAmPm: 'AM' | 'PM'
  closed: boolean
}

export interface OperatingHours {
  [key: string]: DayHours
  monday: DayHours
  tuesday: DayHours
  wednesday: DayHours
  thursday: DayHours
  friday: DayHours
  saturday: DayHours
  sunday: DayHours
}

export interface BannerRequest {
  title: string
  subtitle: string
  link: string
  image: string
}

export interface Notice {
  id: number | string
  active: boolean
  target: 'all' | 'seller' | string
}

export interface DriverInfo {
  name: string
  vehicle: string
  plateNumber: string
  phone: string
  avatar: string | null
  status: string
  statusText: string
  currentLocation: [number, number]
}

export interface OrderTrackingInfo {
  id: string
  deliveryAddress: string
  eta: string
  customerName: string
}

export interface TrackingMetrics {
  distance: string
  duration: string
}

export interface DeliveryStep {
  id: number
  icon: string
  label: string
  time: string | null
  completed: boolean
  active: boolean
}

export interface AnalyticsStats {
  totalSales: number
  salesGrowth: number
  totalOrders: number
  ordersGrowth: number
  avgOrderValue: number
  avgOrderValueGrowth: number
  completionRate: number
  avgDeliveryTime: number
  rating: number
  customers: number
}

export interface ChartDataPoint {
  day?: string
  label?: string
  sales: number
  orders: number
}

export interface TopProduct {
  id: number | string
  name: string
  sold: number
  revenue: number
  percentage: number
}

export interface CategorySales {
  name: string
  sales: number
  orders: number
  percentage: number
  color: string
}

export interface Activity {
  type: string
  icon?: string
  text: string
  time: string
  amount?: number
}

export interface MonthlyGoals {
  salesTarget: number
  ordersTarget: number
  satisfactionTarget: number
  currentSales: number
  currentOrders: number
}

export interface ContactSupport {
  key: string
  title: string
  subtitle: string
  type: 'chat' | 'phone' | 'email' | 'facebook'
  value: string | null
  enabled: boolean
}

export interface FAQ {
  question: string
  answer: string
}

export interface Report {
  id: number | string
  subject: string
  created_at: string
  status: string
}

export interface PersonalInfoForm {
  firstName: string
  middleName: string
  lastName: string
  country: string
  birthdate: string
  birthplace: string
  currentAddress: string
}

export interface StoreInfoForm {
  storeName: string
  region: string
  city: string
  barangay: string
  storePlace: string
  completeAddress: string
}

export interface AccountSummary {
  fullName: string
  email: string
  phone: string
  storeName: string
  verificationStatus: string
}
