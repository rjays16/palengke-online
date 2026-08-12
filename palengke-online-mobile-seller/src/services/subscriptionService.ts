import { api, ApiError } from '@/utils/apiClient';
const SUB_KEY = 'palengke_subscription';

export interface Plan {
  id: string;
  name: string;
  months: number;
  price: number;
  popular?: boolean;
  savings?: string;
  features: string[];
}

export const PLANS: Plan[] = [
  {
    id: '1m',
    name: '1 Month',
    months: 1,
    price: 800,
    features: ['Full product listing', 'Order management', 'Real-time analytics'],
  },
  {
    id: '2m',
    name: '2 Months',
    months: 2,
    price: 1500,
    savings: 'Save ₱100',
    features: ['Full product listing', 'Order management', 'Real-time analytics'],
  },
  {
    id: '4m',
    name: '4 Months',
    months: 4,
    price: 2800,
    popular: true,
    savings: 'Save ₱400',
    features: ['Full product listing', 'Order management', 'Real-time analytics', 'Priority support'],
  },
  {
    id: '8m',
    name: '8 Months',
    months: 8,
    price: 5200,
    savings: 'Save ₱1200',
    features: [
      'Full product listing',
      'Order management',
      'Real-time analytics',
      'Priority support',
      'Request promo banner',
    ],
  },
];

export interface Subscription {
  trialStart: string;
  trialEnd: string;
  planId: string | null;
  planStart: string | null;
  planEnd: string | null;
  status: 'trial' | 'active' | 'expired' | 'cancelled';
}

function defaultSub(): Subscription {
  const start = new Date();
  const end = new Date(start);
  end.setDate(end.getDate() + 25);
  return {
    trialStart: start.toISOString().slice(0, 10),
    trialEnd: end.toISOString().slice(0, 10),
    planId: null,
    planStart: null,
    planEnd: null,
    status: 'trial',
  };
}

export function getSubscription(): Subscription {
  const raw = localStorage.getItem(SUB_KEY);
  if (!raw) {
    const sub = defaultSub();
    localStorage.setItem(SUB_KEY, JSON.stringify(sub));
    return sub;
  }
  return JSON.parse(raw);
}

export async function refreshSubscription(): Promise<Subscription> {
  const email = localStorage.getItem('userEmail');
  if (!email) return getSubscription();
  try {
    const data = await api.get(`/seller/subscription?email=${encodeURIComponent(email)}`);
    const sub: Subscription = {
      trialStart: data.trial_start,
      trialEnd: data.trial_end,
      planId: data.plan_id,
      planStart: data.plan_start,
      planEnd: data.plan_end,
      status: data.status,
    };
    localStorage.setItem(SUB_KEY, JSON.stringify(sub));
    return sub;
  } catch {
    // silent — use cached
  }
  return getSubscription();
}

export function daysLeft(sub: Subscription): number {
  if (sub.status === 'cancelled') return 0;
  const end = sub.status === 'trial' ? new Date(sub.trialEnd) : new Date(sub.planEnd!);
  const now = new Date();
  const diff = Math.ceil((end.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
  return Math.max(0, diff);
}

export function isExpired(sub: Subscription): boolean {
  return sub.status === 'cancelled' || sub.status === 'expired' || daysLeft(sub) === 0;
}

export async function subscribePlan(planId: string, paymentMethodId?: number | null): Promise<Subscription> {
  const email = localStorage.getItem('userEmail');
  if (!email) throw new Error('User not logged in');
  const body: Record<string, any> = { email, plan_id: planId };
  if (paymentMethodId) body.payment_method_id = paymentMethodId;
  try {
    await api.post('/seller/subscription/subscribe', body);
  } catch (error) {
    if (error instanceof ApiError && error.data?.error === 'insufficient_balance') {
      const err = new Error((error.data.message as string) || 'Insufficient balance');
      (err as any).code = 'insufficient_balance';
      (err as any).balance = error.data.balance;
      (err as any).required = error.data.required;
      (err as any).shortfall = error.data.shortfall;
      throw err;
    }
    if (error instanceof ApiError) {
      throw new Error((error.data.message as string) || 'Subscription failed');
    }
    throw error;
  }
  return refreshSubscription();
}
