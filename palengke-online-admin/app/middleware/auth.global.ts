export default defineNuxtRouteMiddleware((to) => {
  if (typeof localStorage === 'undefined') return

  const saved = localStorage.getItem('palengke_auth_user')

  if (to.path === '/login') {
    if (saved) return navigateTo('/dashboard')
    return
  }

  if (!saved) {
    return navigateTo('/login')
  }

  const auth = useAuthStore()
  auth.restore()
})
