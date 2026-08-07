import { ref } from 'vue'
import { API_BASE } from '@/config/api'
import { api } from '@/utils/apiClient'

export interface ChatEvents {
  onConversationsUpdate?: (conversations: any[]) => void
  onMessagesUpdate?: (conversationId: string | number, messages: any[]) => void
  onNewMessageSound?: () => void
  onConnectionChange?: (connected: boolean) => void
}

export function useChatWebSocket(email: string | null, events: ChatEvents = {}) {
  const isConnected = ref(false)
  const usePolling = ref(false)

  let ws: WebSocket | null = null
  let reconnectTimer: ReturnType<typeof setTimeout> | null = null
  let reconnectAttempts = 0
  const MAX_RECONNECT_ATTEMPTS = 5

  // Polling fallback
  let conversationPollTimer: ReturnType<typeof setInterval> | null = null
  let messagePollTimer: ReturnType<typeof setInterval> | null = null
  let conversationCount = 0
  let activeConversationId: string | number | null = null

  function getWsUrl(): string | null {
    if (!email) return null
    const wsBase = API_BASE.replace(/^http/, 'ws').replace(/\/api\/?$/, '')
    const token = localStorage.getItem('token') || ''
    return `${wsBase}/ws/seller/chat?email=${encodeURIComponent(email)}&token=${encodeURIComponent(token)}`
  }

  function connect() {
    const url = getWsUrl()
    if (!url) return

    try {
      ws = new WebSocket(url)

      ws.onopen = () => {
        isConnected.value = true
        usePolling.value = false
        reconnectAttempts = 0
        events.onConnectionChange?.(true)
        stopPolling()
      }

      ws.onmessage = (event) => {
        try {
          const payload = JSON.parse(event.data)
          handleWsMessage(payload)
        } catch {
          // ignore malformed messages
        }
      }

      ws.onclose = () => {
        isConnected.value = false
        events.onConnectionChange?.(false)
        ws = null
        attemptReconnect()
      }

      ws.onerror = () => {
        ws?.close()
      }
    } catch {
      fallbackToPolling()
    }
  }

  function handleWsMessage(payload: any) {
    switch (payload.type) {
      case 'conversations': {
        const list = payload.conversations || []
        events.onConversationsUpdate?.(list)
        break
      }
      case 'messages': {
        const cid = payload.conversation_id
        const msgs = payload.messages || []
        events.onMessagesUpdate?.(cid, msgs)
        break
      }
      case 'new_message': {
        const cid = payload.conversation_id
        if (cid && activeConversationId === cid) {
          events.onMessagesUpdate?.(cid, payload.messages || [])
        } else {
          events.onNewMessageSound?.()
        }
        break
      }
      case 'typing': {
        // TODO: handle typing indicator
        break
      }
    }
  }

  function attemptReconnect() {
    if (reconnectAttempts >= MAX_RECONNECT_ATTEMPTS) {
      fallbackToPolling()
      return
    }
    reconnectAttempts++
    const delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000)
    reconnectTimer = setTimeout(() => {
      if (!isConnected.value) connect()
    }, delay)
  }

  function fallbackToPolling() {
    if (usePolling.value) return
    usePolling.value = true
    startPolling()
  }

  function startPolling() {
    stopPolling()
    conversationPollTimer = setInterval(async () => {
      if (!email) return
      const prev = conversationCount
      try {
        const data = await api.get(`/seller/conversations?email=${encodeURIComponent(email)}`)
        const list = data.conversations || []
        if (list.length > prev && prev > 0) {
          const hasUnmutedNew = list.some((c: any) => !c.muted)
          if (hasUnmutedNew) events.onNewMessageSound?.()
        }
        events.onConversationsUpdate?.(list)
        conversationCount = list.length
      } catch { /* silent */ }
    }, 5000)

    if (activeConversationId) {
      startMessagePolling(activeConversationId)
    }
  }

  function startMessagePolling(conversationId: string | number) {
    stopMessagePolling()
    messagePollTimer = setInterval(async () => {
      if (!email) return
      try {
        const data = await api.get(`/seller/conversations/${conversationId}?email=${encodeURIComponent(email)}`)
        events.onMessagesUpdate?.(conversationId, data.messages || [])
      } catch { /* silent */ }
    }, 3000)
  }

  function stopPolling() {
    if (conversationPollTimer) {
      clearInterval(conversationPollTimer)
      conversationPollTimer = null
    }
    stopMessagePolling()
  }

  function stopMessagePolling() {
    if (messagePollTimer) {
      clearInterval(messagePollTimer)
      messagePollTimer = null
    }
  }

  function watchConversation(conversationId: string | number | null) {
    activeConversationId = conversationId
    if (usePolling.value) {
      stopMessagePolling()
      if (conversationId) startMessagePolling(conversationId)
    } else if (ws && ws.readyState === WebSocket.OPEN) {
      ws.send(JSON.stringify({ type: 'watch', conversation_id: conversationId }))
    }
  }

  function disconnect() {
    if (reconnectTimer) {
      clearTimeout(reconnectTimer)
      reconnectTimer = null
    }
    stopPolling()
    if (ws) {
      ws.onclose = null
      ws.close()
      ws = null
    }
    isConnected.value = false
  }

  return {
    isConnected,
    usePolling,
    connect,
    disconnect,
    watchConversation,
  }
}
