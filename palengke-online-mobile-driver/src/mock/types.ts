export interface DeliveryEntry {
  id: string
  customerName: string
  address: string
  amount: number
  time: string
  status: string
  tipped: boolean
}

export interface NotificationItem {
  id: number
  type: string
  title: string
  message: string
  time: string
  read: boolean
  category?: string
}

export interface ChatItem {
  id: number
  name: string
  type: string
  lastMessage: string
  time: string
  unread: number
  orderId: string
}

export interface ProfileData {
  name: string
  phone: string
  email: string
  vehicleType: string
  plateNumber: string
  vehicleModel: string
  vehicleColor: string
  avatar: string
  totalTrips: number
  rating: number
  joinedDays: number
}

export interface RatingCounts {
  [star: number]: number
}

export interface Badge {
  id: number
  name: string
  icon: unknown
  color: string
  isNew: boolean
  count: number
}

export interface Improvement {
  id: number
  title: string
  description: string
  icon: unknown
  score: string
  type: string
}

export interface DocumentPreview {
  id: number
  name: string
  status: string
}

export interface ChartDay {
  label: string
  amount: number
  isToday: boolean
}

export interface WeekDay {
  name: string
  number: number
  date: number
  isToday: boolean
  earnings: number
}

export interface WeekItem {
  id: string
  label: string
  range: string
}

export interface Topic {
  id: string
  name: string
  icon: unknown
}

export interface Faq {
  id: number
  topic: string
  question: string
  answer: string
}

export interface DocumentItem {
  id: number
  name: string
  expiry: string | null
  status: string
}

export interface ServiceItem {
  id: number
  name: string
  description: string | null
  icon: unknown
  color: string
  enabled: boolean
  tag: string | null
  tagType?: string
  alwaysOn: boolean
}

export interface SavedDestination {
  id: number
  name: string
  address: string
  type: string
}

export interface RecentDestination {
  id: number
  name: string
  address: string
}

export interface TipData {
  amount: number
  customerName: string
  message: string
  orderId: string
  deliveryFee: number
}

export interface LicenceData {
  licenceId: string
  trialStartDate: string
  trialEndDate: string
  isPaid: boolean
  paidDate: string | null
  amount: number
}
