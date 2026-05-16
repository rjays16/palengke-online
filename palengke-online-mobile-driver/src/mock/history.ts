import type { WeekDay, WeekItem, ChartDay } from './types'

export const mockWeekDays: WeekDay[] = [
  { name: 'Sun', number: 31, date: 31, isToday: false, earnings: 0 },
  { name: 'Mon', number: 1, date: 1, isToday: false, earnings: 1200 },
  { name: 'Tue', number: 2, date: 2, isToday: false, earnings: 1687 },
  { name: 'Wed', number: 3, date: 3, isToday: false, earnings: 1850 },
  { name: 'Thu', number: 4, date: 4, isToday: false, earnings: 2100 },
  { name: 'Fri', number: 5, date: 5, isToday: false, earnings: 1950 },
  { name: 'Sat', number: 6, date: 6, isToday: true, earnings: 850 },
]

export const mockWeekList: WeekItem[] = [
  { id: 'week10', label: 'Week 10', range: '11 - 17' },
  { id: 'week11', label: 'Week 11', range: '18 - 24' },
  { id: 'week12', label: 'Week 12', range: '25 - 31' },
  { id: 'week13', label: 'Week 13', range: '1 - 7' },
]

export const mockMiniChartData: ChartDay[] = [
  { label: 'Mon', amount: 1200, isToday: false },
  { label: 'Tue', amount: 1687, isToday: false },
  { label: 'Wed', amount: 1850, isToday: false },
  { label: 'Thu', amount: 2100, isToday: false },
  { label: 'Fri', amount: 1950, isToday: false },
  { label: 'Sat', amount: 850, isToday: true },
  { label: 'Sun', amount: 0, isToday: false },
]

export const mockAllJobs = {
  2: [
    { id: 'j1', time: '5:31 PM', sellerName: 'Greenwich - SM CDO', deliveryAddress: 'Brgy. Carmen, CDO', amount: 230.00, paymentMethod: 'gcash' as const, tipped: true, bonus: 0 },
    { id: 'j2', time: '5:00 PM', sellerName: 'Admiral Bay Suites', deliveryAddress: 'Brgy. Lapasan, CDO', amount: 156.00, paymentMethod: 'cash' as const, tipped: false, bonus: 0 },
    { id: 'j3', time: '4:46 PM', sellerName: "Juan's Fresh Market", deliveryAddress: 'Brgy. Macasandig, CDO', amount: 320.00, paymentMethod: 'cash' as const, tipped: true, bonus: 50 },
    { id: 'j4', time: '3:20 PM', sellerName: "Ana's Palengke", deliveryAddress: 'Brgy. Bulua, CDO', amount: 180.00, paymentMethod: 'gcash' as const, tipped: false, bonus: 0 },
    { id: 'j5', time: '2:15 PM', sellerName: "Rosa's Veggies", deliveryAddress: 'Brgy. Cogon, CDO', amount: 95.00, paymentMethod: 'cash' as const, tipped: false, bonus: 0 },
  ],
  6: [
    { id: 'j6', time: '2:30 PM', sellerName: "Mario's Fresh Fish", deliveryAddress: 'Brgy. Agusan, CDO', amount: 250.00, paymentMethod: 'cash' as const, tipped: true, bonus: 0 },
    { id: 'j7', time: '1:00 PM', sellerName: "Pedro's Meat Shop", deliveryAddress: 'Brgy. Kauswagan, CDO', amount: 180.00, paymentMethod: 'gcash' as const, tipped: false, bonus: 0 },
    { id: 'j8', time: '11:30 AM', sellerName: 'Fresh Fruits Stand', deliveryAddress: 'Brgy. Camaman-an, CDO', amount: 150.00, paymentMethod: 'cash' as const, tipped: true, bonus: 30 },
    { id: 'j9', time: '10:00 AM', sellerName: 'CDO Palengke Stall 15', deliveryAddress: 'Brgy. Carmen, CDO', amount: 120.00, paymentMethod: 'cash' as const, tipped: false, bonus: 0 },
    { id: 'j10', time: '8:45 AM', sellerName: "Mama's Veggies", deliveryAddress: 'Brgy. Balulang, CDO', amount: 150.00, paymentMethod: 'gcash' as const, tipped: false, bonus: 0 },
  ]
}
