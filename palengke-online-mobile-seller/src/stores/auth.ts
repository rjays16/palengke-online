import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  const isAuthenticated = ref(false)
  const token = ref<string | null>(null)
  const hasPin = ref(false)
  const userEmail = ref<string | null>(null)
  const deviceId = ref<string | null>(null)

  const isLoggedIn = computed(() => isAuthenticated.value)
  const email = computed(() => userEmail.value)

  function initFromStorage() {
    if (localStorage.getItem('token')) {
      localStorage.removeItem('token')
    }
    isAuthenticated.value = localStorage.getItem('isAuthenticated') === 'true'
    token.value = localStorage.getItem('seller_token')
    hasPin.value = localStorage.getItem('hasPin') === 'true'
    userEmail.value = localStorage.getItem('userEmail')
    deviceId.value = localStorage.getItem('deviceId')

    if (!deviceId.value) {
      deviceId.value = 'device_' + Date.now() + '_' + Math.random().toString(36).substring(2, 10)
      localStorage.setItem('deviceId', deviceId.value)
    }

    if (isAuthenticated.value && !token.value) {
      isAuthenticated.value = false
    }
  }

  function setSession(email: string, authToken: string, pinStatus: boolean, devId: string) {
    userEmail.value = email
    token.value = authToken
    hasPin.value = pinStatus
    deviceId.value = devId
    isAuthenticated.value = true

    localStorage.setItem('userEmail', email)
    localStorage.setItem('seller_token', authToken)
    localStorage.setItem('hasPin', pinStatus ? 'true' : 'false')
    localStorage.setItem('deviceId', devId)
    localStorage.setItem('isAuthenticated', 'true')
  }

  function setHasPin(value: boolean) {
    hasPin.value = value
    localStorage.setItem('hasPin', value ? 'true' : 'false')
  }

  function setToken(value: string) {
    token.value = value
    localStorage.setItem('seller_token', value)
  }

  function clearAuth() {
    isAuthenticated.value = false
    token.value = null
    hasPin.value = false
    userEmail.value = null
    deviceId.value = null

    localStorage.removeItem('isAuthenticated')
    localStorage.removeItem('seller_token')
    localStorage.removeItem('hasPin')
    localStorage.removeItem('userEmail')
    localStorage.removeItem('deviceId')
  }

  return {
    isAuthenticated,
    token,
    hasPin,
    userEmail,
    deviceId,
    isLoggedIn,
    email,
    initFromStorage,
    setSession,
    setHasPin,
    setToken,
    clearAuth,
  }
})
