import { reactive, computed, ref } from 'vue'

import { API_BASE } from '@/services/api';

function getToken(): string | null {
  return localStorage.getItem('driver_token')
}

export interface LicenceState {
  licenceId: string
  trialStartDate: string | null
  trialEndDate: string | null
  isPaid: boolean
  paidDate: string | null
  paidUntil: string | null
  amount: number
  isFree: boolean
}

const defaults = {
  licenceId: '',
  trialStartDate: null,
  trialEndDate: null,
  isPaid: false,
  paidDate: null,
  paidUntil: null,
  amount: 2000,
  isFree: false,
}

const state = reactive<LicenceState>({ ...defaults })
const licenceEnabled = ref(true)

export function useLicenceStore() {
  const trialDaysRemaining = computed(() => {
    if (!state.trialEndDate) return 30
    const end = new Date(state.trialEndDate)
    const now = new Date()
    const diff = end.getTime() - now.getTime()
    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)))
  })

  const trialExpired = computed(() => {
    return trialDaysRemaining.value <= 0
  })

  const licenceExpired = computed(() => {
    if (!state.isPaid || !state.paidUntil) return false
    return new Date(state.paidUntil).getTime() <= Date.now()
  })

  const licenceDaysLeft = computed(() => {
    if (!state.isPaid || !state.paidUntil) return 0
    const end = new Date(state.paidUntil)
    const now = new Date()
    const diff = end.getTime() - now.getTime()
    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)))
  })

  const licenceActive = computed(() => {
    return state.isPaid && !licenceExpired.value
  })

  const canDeliver = computed(() => {
    if (!licenceEnabled.value) return true
    if (state.isFree) return true
    if (licenceActive.value) return true
    return !trialExpired.value
  })

  const isOnTrial = computed(() => {
    if (!licenceEnabled.value) return false
    if (state.isFree) return false
    return !state.isPaid && trialDaysRemaining.value > 0
  })

  const showLicenceFeature = computed(() => {
    return licenceEnabled.value
  })

  async function checkLicenceEnabled() {
    try {
      const res = await fetch(`${API_BASE}/api/settings`)
      if (res.ok) {
        const data = await res.json()
        licenceEnabled.value = data.licence_enabled !== "false"
      }
    } catch { /* ignore */ }
  }

  async function fetchLicence() {
    const token = getToken()
    if (!token) return
    try {
      const res = await fetch(`${API_BASE}/api/driver/licence`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      if (res.ok) {
        const data = await res.json()
        const l = data.licence
        state.licenceId = l.licenceId
        state.trialStartDate = l.trialStartDate
        state.trialEndDate = l.trialEndDate
        state.isPaid = l.isPaid
        state.paidDate = l.paidDate
        state.paidUntil = l.paidUntil
        state.amount = l.amount
        state.isFree = l.isFree || false
      }
    } catch { /* ignore */ }
  }

  async function payLicence(paymentMethodId?: number): Promise<{ success: boolean; message?: string; balance?: number }> {
    const token = getToken()
    if (!token) return { success: false }
    try {
      const res = await fetch(`${API_BASE}/api/driver/licence/pay`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(paymentMethodId ? { payment_method_id: paymentMethodId } : {}),
      })
      if (res.ok) {
        const data = await res.json()
        const l = data.licence
        state.isPaid = l.isPaid
        state.paidDate = l.paidDate
        state.paidUntil = l.paidUntil
        state.amount = l.amount
        return { success: true, balance: data.balance }
      }
      let message = 'Payment could not be processed. Please try again.'
      try {
        const data = await res.json()
        if (data && data.message) message = data.message
      } catch { /* ignore */ }
      return { success: false, message }
    } catch {
      return { success: false }
    }
  }

  function resetLicence() {
    Object.assign(state, { ...defaults })
    licenceEnabled.value = true
  }

  function getLicenceDisplay(): string {
    return state.licenceId
  }

  function getDaysUsed(): number {
    if (!state.trialStartDate) return 0
    const start = new Date(state.trialStartDate)
    const now = new Date()
    return Math.ceil((now.getTime() - start.getTime()) / (1000 * 60 * 60 * 24))
  }

  function getStatusText(): string {
    if (!licenceEnabled.value) return 'Not Required'
    if (state.isFree) return 'Free'
    if (licenceActive.value) return 'Paid'
    if (state.isPaid && licenceExpired.value) return `Expired — Renew ₱${state.amount.toLocaleString()}/yr`
    if (trialExpired.value) return `Expired — Pay ₱${state.amount.toLocaleString()}/yr to continue`
    return `Trial — ${trialDaysRemaining.value} days left`
  }

  function getStatusColor(): string {
    if (!licenceEnabled.value) return '#9ca3af'
    if (state.isFree) return '#3b82f6'
    if (licenceActive.value) return '#4CAF50'
    if (state.isPaid && licenceExpired.value) return '#f44336'
    if (trialExpired.value) return '#f44336'
    return '#FF9800'
  }

  function getPaidUntilDisplay(): string {
    if (!state.paidUntil) return ''
    return new Date(state.paidUntil).toLocaleDateString('en-PH', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    })
  }

  return {
    state,
    licenceEnabled,
    trialDaysRemaining,
    trialExpired,
    licenceExpired,
    licenceDaysLeft,
    licenceActive,
    canDeliver,
    isOnTrial,
    showLicenceFeature,
    checkLicenceEnabled,
    fetchLicence,
    payLicence,
    resetLicence,
    getLicenceDisplay,
    getDaysUsed,
    getStatusText,
    getStatusColor,
    getPaidUntilDisplay,
  }
}
