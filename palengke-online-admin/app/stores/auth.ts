const AUTH_KEY = 'palengke_auth_user'

function restoreFromStorage() {
  if (typeof localStorage === 'undefined') return null
  try {
    const saved = localStorage.getItem(AUTH_KEY)
    return saved ? JSON.parse(saved) : null
  } catch {
    return null
  }
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<{
    email: string
    name: string
    role: string
  } | null>(null)

  const isAuthenticated = computed(() => user.value !== null)
  const isLoading = ref(false)
  const loginError = ref('')

  const isSuperAdmin = computed(() => user.value?.role === 'superadmin')
  const isAdmin = computed(() => user.value?.role === 'admin')

  const canApprove = computed(() => isSuperAdmin.value)
  const canAccessSettings = computed(() => isSuperAdmin.value)

  async function login(email: string, password: string) {
    isLoading.value = true
    loginError.value = ''
    await new Promise(resolve => setTimeout(resolve, 1000))
    const adminUsersStore = useAdminUsersStore()
    const found = adminUsersStore.findByEmail(email)
    if (found && found.password === password && found.status === 'active') {
      user.value = { email: found.email, name: found.name, role: found.role }
      localStorage.setItem(AUTH_KEY, JSON.stringify(user.value))
      isLoading.value = false
      return true
    }
    loginError.value = 'Invalid email or password'
    isLoading.value = false
    return false
  }

  function logout() {
    user.value = null
    localStorage.removeItem(AUTH_KEY)
  }

  function restore() {
    const saved = restoreFromStorage()
    if (saved) user.value = saved
  }

  if (typeof localStorage !== 'undefined') {
    setTimeout(() => restore(), 0)
  }

  return { user, isAuthenticated, isLoading, loginError, isSuperAdmin, isAdmin, canApprove, login, logout, restore }
})
