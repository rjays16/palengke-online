import type { DocumentItem } from './types'

export const mockDocuments: DocumentItem[] = [
  { id: 1, name: 'Identity Card', expiry: 'Jan 2027', status: 'verified' },
  { id: 2, name: 'NBI or Police Clearance', expiry: 'Jun 2026', status: 'verified' },
  { id: 3, name: 'Certificate of Registration (CR)', expiry: null, status: 'verified' },
  { id: 4, name: 'Official Receipt (OR)', expiry: 'Mar 2026', status: 'verified' },
  { id: 5, name: "Driver's License", expiry: 'Aug 2027', status: 'verified' },
  { id: 6, name: 'Business or Delivery Permit', expiry: 'Dec 2025', status: 'verified' },
]
