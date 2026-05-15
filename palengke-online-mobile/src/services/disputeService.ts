const STORAGE_KEY = 'palengke_disputes'

export interface Dispute {
  id: string
  orderId: string
  customerName: string
  sellerName: string
  storeName: string
  reason: string
  reasonLabel: string
  description: string
  image?: string
  status: 'open' | 'investigating' | 'resolved' | 'dismissed'
  priority: 'high' | 'medium' | 'low'
  date: string
}

export const DISPUTE_REASONS = [
  { value: 'spoiled_rotten', label: 'Spoiled / Rotten', priority: 'high' as const },
  { value: 'wrong_item', label: 'Wrong Item Delivered', priority: 'medium' as const },
  { value: 'damaged', label: 'Damaged Goods', priority: 'high' as const },
  { value: 'missing', label: 'Missing Items', priority: 'medium' as const },
  { value: 'other', label: 'Other Issue', priority: 'low' as const },
]

export function submitDispute(data: Omit<Dispute, 'id' | 'status' | 'date'>) {
  const disputes = getDisputes()
  const dispute: Dispute = {
    ...data,
    id: 'DSP-' + String(Date.now()).slice(-6),
    status: 'open',
    date: new Date().toISOString().slice(0, 10),
  }
  disputes.push(dispute)
  localStorage.setItem(STORAGE_KEY, JSON.stringify(disputes))
  return dispute
}

export function getDisputes(): Dispute[] {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')
  } catch {
    return []
  }
}

export function getDisputeByOrderId(orderId: string): Dispute | undefined {
  return getDisputes().find(d => d.orderId === orderId)
}

export function getDisputesBySeller(sellerName: string): Dispute[] {
  return getDisputes().filter(d => d.sellerName === sellerName)
}

export function updateDisputeStatus(id: string, status: Dispute['status']) {
  const disputes = getDisputes()
  const d = disputes.find(d => d.id === id)
  if (d) {
    d.status = status
    localStorage.setItem(STORAGE_KEY, JSON.stringify(disputes))
  }
}

export function readFileAsDataURL(file: File): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = () => resolve(reader.result as string)
    reader.onerror = reject
    reader.readAsDataURL(file)
  })
}
