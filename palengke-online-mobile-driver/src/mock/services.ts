import {
  bicycleOutline, flashOutline, cubeOutline,
  cashOutline, carOutline
} from 'ionicons/icons'
import type { ServiceItem } from './types'

export const mockServices: ServiceItem[] = [
  { id: 1, name: 'Palengke Standard Delivery', description: 'Regular delivery orders', icon: bicycleOutline, color: '#4CAF50', enabled: true, tag: null, alwaysOn: false },
  { id: 2, name: 'Palengke Express Delivery', description: '15% higher earnings than standard', icon: flashOutline, color: '#FF9800', enabled: true, tag: 'Higher Earnings', tagType: 'earnings', alwaysOn: false },
  { id: 3, name: 'Bulky Item Delivery', description: 'Large or heavy orders', icon: cubeOutline, color: '#2196F3', enabled: true, tag: null, alwaysOn: false },
  { id: 4, name: 'Cash on Delivery (COD)', description: null, icon: cashOutline, color: '#4CAF50', enabled: true, tag: null, alwaysOn: true },
  { id: 6, name: 'GCash Payment Orders', description: null, icon: carOutline, color: '#2196F3', enabled: true, tag: null, alwaysOn: false },
]
