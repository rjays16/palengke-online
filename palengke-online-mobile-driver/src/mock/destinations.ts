import type { SavedDestination, RecentDestination } from './types'

export const mockSavedDestinations: SavedDestination[] = [
  { id: 1, name: 'Home', address: 'Brgy. San Isidro, CDO City', type: 'home' },
  { id: 2, name: 'CDO City Palengke', address: 'Cogon Market, CDO City', type: 'work' },
]

export const mockRecentDestinations: RecentDestination[] = [
  { id: 1, name: 'SM City CDO', address: 'Mastersons Ave, CDO City' },
  { id: 2, name: 'Brgy. Carmen', address: 'Carmen, Cagayan de Oro City' },
  { id: 3, name: 'Robinsons CDO', address: 'Capitol, CDO City' },
  { id: 4, name: 'Brgy. Lapasan', address: 'Lapasan, CDO City' },
]
