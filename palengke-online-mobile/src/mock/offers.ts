import type { Offer } from './types'

export const offers: Offer[] = [
  {
    id: 1, title: '₱70 OFF', color: '#FFF3E0',
    description: '₱70 off — choose Fri-Sun 11:00AM-11:59PM delivery to enjoy this',
    available: true
  },
  {
    id: 2, title: '₱50 OFF', color: '#E8F5E9',
    description: '₱50 off — choose Mon-Sun 08:00PM-11:59PM delivery to enjoy this',
    available: true
  },
  {
    id: 3, title: '₱50 OFF', color: '#E3F2FD',
    description: '₱50 off — choose Mon-Sun 08:00AM-10:59AM delivery to enjoy this',
    available: false
  },
  {
    id: 4, title: 'Earn 200 Points (₱20 OFF)', color: '#F3E5F5',
    description: 'Earn PalengkePoints and save on your next order',
    available: true
  },
]
