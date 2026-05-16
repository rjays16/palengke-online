import type { ChartDay } from './types'

export const mockChartData: ChartDay[] = [
  { label: 'Mon', amount: 1200, isToday: false },
  { label: 'Tue', amount: 1687, isToday: false },
  { label: 'Wed', amount: 1850, isToday: false },
  { label: 'Thu', amount: 2100, isToday: false },
  { label: 'Fri', amount: 1950, isToday: false },
  { label: 'Sat', amount: 850, isToday: true },
  { label: 'Sun', amount: 0, isToday: false }
]

export const mockStatsData = {
  today: {
    netEarnings: 850.00,
    jobs: 5,
    tips: 120,
    onlineHours: 4.5,
    avgPerJob: 170,
    deliveryFee: 850,
    commission: 120,
    completionRate: 100,
    rating: 5.0,
    acceptRate: 80,
    totalKm: 18.5
  },
  yesterday: {
    netEarnings: 1687.00,
    jobs: 12,
    tips: 250,
    onlineHours: 8,
    avgPerJob: 140,
    deliveryFee: 1850,
    commission: 163,
    completionRate: 95,
    rating: 4.9,
    acceptRate: 85,
    totalKm: 42.3
  },
  week: {
    netEarnings: 11196.73,
    jobs: 67,
    tips: 1200,
    onlineHours: 45,
    avgPerJob: 167,
    deliveryFee: 12500,
    commission: 1303.27,
    completionRate: 96,
    rating: 4.9,
    acceptRate: 83,
    totalKm: 245.8
  },
  month: {
    netEarnings: 42500.00,
    jobs: 280,
    tips: 4800,
    onlineHours: 180,
    avgPerJob: 151,
    deliveryFee: 48000,
    commission: 5500,
    completionRate: 95,
    rating: 4.9,
    acceptRate: 82,
    totalKm: 980.5
  }
}
