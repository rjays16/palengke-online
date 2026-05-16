import {
  trophyOutline, flashOutline, thumbsUpOutline, heartOutline,
  checkmarkCircleOutline, timeOutline, closeCircleOutline
} from 'ionicons/icons'
import type { ProfileData, Badge, Improvement, DocumentPreview, RatingCounts } from './types'

export const mockProfileData: ProfileData = {
  name: 'Juan Dela Cruz',
  phone: '+63 912 345 6789',
  email: 'juan.delacruz@email.com',
  vehicleType: 'Motorcycle',
  plateNumber: 'ABC 1234',
  vehicleModel: 'Honda Click 125i',
  vehicleColor: 'Black',
  avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
  totalTrips: 142,
  rating: 5.00,
  joinedDays: 45
}

export const mockRatingCounts: RatingCounts = { 5: 100, 4: 0, 3: 0, 2: 0, 1: 0 }

export const mockBadges: Badge[] = [
  { id: 1, name: 'Top Rated', icon: trophyOutline, color: '#FFD700', isNew: true, count: 1 },
  { id: 2, name: 'Fast Delivery', icon: flashOutline, color: '#2196F3', isNew: true, count: 1 },
  { id: 3, name: 'Friendly', icon: thumbsUpOutline, color: '#4CAF50', isNew: true, count: 1 },
  { id: 4, name: 'Helpful', icon: heartOutline, color: '#E91E63', count: 73, isNew: false },
]

export const mockImprovements: Improvement[] = [
  { id: 1, title: 'Acceptance Rate', description: 'Accept more orders to improve', icon: checkmarkCircleOutline, score: '80%', type: 'good' },
  { id: 2, title: 'On-Time Delivery', description: 'Great job delivering on time!', icon: timeOutline, score: '95%', type: 'great' },
  { id: 3, title: 'Cancellation Rate', description: 'Keep cancellations low', icon: closeCircleOutline, score: '2%', type: 'good' },
]

export const mockDocumentsPreview: DocumentPreview[] = [
  { id: 1, name: "Driver's License", status: 'verified' },
  { id: 2, name: 'NBI Clearance', status: 'verified' },
  { id: 3, name: 'ORCR', status: 'verified' },
  { id: 4, name: 'Drug Test', status: 'verified' },
]
