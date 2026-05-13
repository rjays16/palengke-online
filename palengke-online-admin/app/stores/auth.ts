export const useAuthStore = defineStore('auth', () => {
  const user = ref<{
    email: string
    name: string
    role: string
  } | null>(null)
  const isAuthenticated = computed(() => user.value !== null)
  const isLoading = ref(false)
  const loginError = ref('')

  async function login(email: string, password: string) {
    isLoading.value = true
    loginError.value = ''
    await new Promise(resolve => setTimeout(resolve, 1000))
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

  return { user, isAuthenticated, isLoading, loginError, login, logout }
})
