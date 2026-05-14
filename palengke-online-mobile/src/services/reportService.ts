const STORAGE_KEY = 'palengke_reports'

export interface Report {
  id: string
  type: 'seller' | 'driver' | 'customer' | 'other'
  submittedBy: string
  subject: string
  description: string
  status: 'pending' | 'in_progress' | 'resolved' | 'dismissed'
  priority: 'high' | 'medium' | 'low'
  date: string
}

export function submitReport(report: Omit<Report, 'id' | 'status' | 'date'>) {
  const reports = getReports()
  const newReport: Report = {
    ...report,
    id: 'RPT-' + String(Date.now()).slice(-6),
    status: 'pending',
    date: new Date().toISOString().slice(0, 10),
  }
  reports.push(newReport)
  localStorage.setItem(STORAGE_KEY, JSON.stringify(reports))
  return newReport
}

export function getReports(): Report[] {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')
  } catch {
    return []
  }
}
