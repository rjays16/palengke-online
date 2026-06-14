const STORAGE_KEY = 'palengke_disputes';

export interface Dispute {
  id: string;
  orderId: string;
  customerName: string;
  sellerName: string;
  storeName: string;
  reason: string;
  reasonLabel: string;
  description: string;
  image?: string;
  status: 'open' | 'investigating' | 'resolved' | 'dismissed';
  priority: 'high' | 'medium' | 'low';
  date: string;
}

export function getDisputes(): Dispute[] {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
  } catch {
    return [];
  }
}

export function getDisputeByOrderId(orderId: string): Dispute | undefined {
  return getDisputes().find((d) => d.orderId === orderId);
}

export function getDisputesBySeller(sellerName: string): Dispute[] {
  return getDisputes().filter((d) => d.sellerName === sellerName);
}
