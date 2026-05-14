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
    if (email === 'superadmin@palengke.com' && password === 'superadmin123') {
      user.value = { email, name: 'Super Admin', role: 'superadmin' }
      return true
    }
    if (email === 'admin@palengke.com' && password === 'admin123') {
      user.value = { email, name: 'Admin', role: 'admin' }
      return true
    }
    loginError.value = 'Invalid email or password'
    isLoading.value = false
    return false
  }

  function logout() {
    user.value = null
  }

  return { user, isAuthenticated, isLoading, loginError, isSuperAdmin, isAdmin, canApprove, login, logout }
})
