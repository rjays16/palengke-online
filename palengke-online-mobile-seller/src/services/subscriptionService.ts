const SUB_KEY = 'palengke_subscription'

export interface Plan {
  id: string
  name: string
  months: number
  price: number
  popular?: boolean
  savings?: string
}

export const PLANS: Plan[] = [
  { id: '1m', name: '1 Month', months: 1, price: 600 },
  { id: '2m', name: '2 Months', months: 2, price: 1200, savings: 'Save ₱0' },
  { id: '4m', name: '4 Months', months: 4, price: 2100, popular: true, savings: 'Save ₱300' },
  { id: '8m', name: '8 Months', months: 8, price: 4000, savings: 'Save ₱800' },
]

export interface Subscription {
  trialStart: string
  trialEnd: string
  planId: string | null
  planStart: string | null
  planEnd: string | null
  status: 'trial' | 'active' | 'expired'
}

function defaultSub(): Subscription {
  const start = new Date()
  const end = new Date(start)
  end.setDate(end.getDate() + 25)
  return {
    trialStart: start.toISOString().slice(0, 10),
    trialEnd: end.toISOString().slice(0, 10),
    planId: null,
    planStart: null,
    planEnd: null,
    status: 'trial',
  }
}

export function getSubscription(): Subscription {
  const raw = localStorage.getItem(SUB_KEY)
  if (!raw) {
    const sub = defaultSub()
    localStorage.setItem(SUB_KEY, JSON.stringify(sub))
    return sub
  }
  return JSON.parse(raw)
}

export function daysLeft(sub: Subscription): number {
  const end = sub.status === 'trial' ? new Date(sub.trialEnd) : new Date(sub.planEnd!)
  const now = new Date()
  const diff = Math.ceil((end.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))
  return Math.max(0, diff)
}

export function isExpired(sub: Subscription): boolean {
  return daysLeft(sub) === 0
}

export function subscribe(planId: string): Subscription {
  const plan = PLANS.find(p => p.id === planId)
  if (!plan) throw new Error('Invalid plan')

  const sub = getSubscription()
  const now = new Date()
  const end = new Date(now)
  end.setMonth(end.getMonth() + plan.months)

  sub.planId = planId
  sub.planStart = now.toISOString().slice(0, 10)
  sub.planEnd = end.toISOString().slice(0, 10)
  sub.status = 'active'

  localStorage.setItem(SUB_KEY, JSON.stringify(sub))
  return sub
}
