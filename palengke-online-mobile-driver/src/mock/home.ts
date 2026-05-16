import type { DeliveryEntry } from './types'

export const mockActiveDelivery = {
  id: '1234',
  customerName: 'Maria Santos',
  address: 'Brgy. Carmen, CDO City',
  amount: 250,
  status: 'delivering'
}

export const mockRecentDeliveries: DeliveryEntry[] = [
  {
    id: '1233',
    customerName: 'Pedro Cruz',
    address: 'Brgy. Lapasan, CDO City',
    amount: 180,
    time: '30 mins ago',
    status: 'completed',
    tipped: true
  },
  {
    id: '1232',
    customerName: 'Ana Lopez',
    address: 'Brgy. Macasandig, CDO City',
    amount: 320,
    time: '1 hour ago',
    status: 'completed',
    tipped: false
  },
  {
    id: '1231',
    customerName: 'Jose Reyes',
    address: 'Brgy. Bulua, CDO City',
    amount: 150,
    time: '2 hours ago',
    status: 'completed',
    tipped: true
  }
]
