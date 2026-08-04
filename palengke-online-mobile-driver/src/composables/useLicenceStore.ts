import { reactive, computed, ref } from 'vue'

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000'

function getToken(): string | null {
  return localStorage.getItem('token')
}

export interface LicenceState {
  licenceId: string
  trialStartDate: string | null
  trialEndDate: string | null
  isPaid: boolean
  paidDate: string | null
  amount: number
  isFree: boolean
}

const defaults = {
  licenceId: '',
  trialStartDate: null,
  trialEndDate: null,
  isPaid: false,
  paidDate: null,
  amount: 1000,
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

  const canDeliver = computed(() => {
    if (!licenceEnabled.value) return true
    if (state.isFree) return true
    if (state.isPaid) return true
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
    } catch {}
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
        state.amount = l.amount
        state.isFree = l.isFree || false
      }
    } catch {}
  }

  async function payLicence(paymentMethodId?: number): Promise<boolean> {
    const token = getToken()
    if (!token) return false
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
        return true
      }
      return false
    } catch {
      return false
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
    if (state.isPaid) return 'Paid'
    if (trialExpired.value) return `Expired — Pay ₱${state.amount.toLocaleString()} to continue`
    return `Trial — ${trialDaysRemaining.value} days left`
  }

  function getStatusColor(): string {
    if (!licenceEnabled.value) return '#9ca3af'
    if (state.isFree) return '#3b82f6'
    if (state.isPaid) return '#4CAF50'
    if (trialExpired.value) return '#f44336'
    return '#FF9800'
  }

  return {
    state,
    licenceEnabled,
    trialDaysRemaining,
    trialExpired,
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
  }
}
