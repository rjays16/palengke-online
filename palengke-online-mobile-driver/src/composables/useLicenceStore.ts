import { reactive, computed } from 'vue'

const STORAGE_KEY = 'palengkeLicenceData'

export interface LicenceState {
  licenceId: string
  trialStartDate: string | null
  trialEndDate: string | null
  isPaid: boolean
  paidDate: string | null
}

function defaults(): LicenceState {
  const now = new Date()
  const trialEnd = new Date(now.getTime() + 30 * 24 * 60 * 60 * 1000)

  return {
    licenceId: 'PL-2026-' + String(Math.floor(100000 + Math.random() * 900000)),
    trialStartDate: now.toISOString(),
    trialEndDate: trialEnd.toISOString(),
    isPaid: false,
    paidDate: null,
  }
}

function load(): LicenceState {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (raw) return { ...defaults(), ...JSON.parse(raw) }
  } catch { /* ignore */ }
  return defaults()
}

function saveToStorage(state: LicenceState) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify({ ...state }))
}

const state = reactive<LicenceState>(load())

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
    if (state.isPaid) return true
    return !trialExpired.value
  })

  const isOnTrial = computed(() => {
    return !state.isPaid && trialDaysRemaining.value > 0
  })

  function payLicence() {
    state.isPaid = true
    state.paidDate = new Date().toISOString()
    saveToStorage(state)
  }

  function resetLicence() {
    const d = defaults()
    Object.assign(state, d)
    saveToStorage(state)
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
    if (state.isPaid) return 'Paid'
    if (trialExpired.value) return 'Expired — Pay ₱1,000 to continue'
    return `Trial — ${trialDaysRemaining.value} days left`
  }

  function getStatusColor(): string {
    if (state.isPaid) return '#4CAF50'
    if (trialExpired.value) return '#f44336'
    return '#FF9800'
  }

  return {
    state,
    trialDaysRemaining,
    trialExpired,
    canDeliver,
    isOnTrial,
    payLicence,
    resetLicence,
    getLicenceDisplay,
    getDaysUsed,
    getStatusText,
    getStatusColor,
  }
}
