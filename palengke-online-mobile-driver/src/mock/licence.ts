import type { LicenceData } from './types'

export const mockLicenceData: LicenceData = {
  licenceId: 'PL-2026-087421',
  trialStartDate: new Date(Date.now() - 25 * 24 * 60 * 60 * 1000).toISOString(),
  trialEndDate: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000).toISOString(),
  isPaid: true,
  paidDate: new Date().toISOString(),
  amount: 1000,
}

export const mockTrialDaysTotal = 30
export const mockLicencePrice = 1000
