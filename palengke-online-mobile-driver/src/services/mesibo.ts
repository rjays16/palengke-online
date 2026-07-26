import { ref } from 'vue';

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

export interface IncomingCallSignal {
  id: number;
  conversation_id: number;
  caller_email: string;
  caller_name: string;
  caller_type: 'seller' | 'driver';
  status: string;
}

const mesiboInitialized = ref(false);

let mesiboApi: any = null;

function getMesibo(): any {
  return (window as any).Mesibo;
}

export function useMesibo() {
  const callState = ref<'idle' | 'ringing' | 'connecting' | 'connected' | 'ended'>('idle');
  const callDuration = ref(0);
  const activeCallSignalId = ref<number | null>(null);

  let callTimerInterval: ReturnType<typeof setInterval> | null = null;
  let callerPollTimer: ReturnType<typeof setInterval> | null = null;
  let connectedPollTimer: ReturnType<typeof setInterval> | null = null;

  async function initMesibo(email: string, userType: 'seller' | 'driver'): Promise<boolean> {
    if (mesiboInitialized.value) return true;

    const M = getMesibo();
    if (!M) {
      console.warn('Mesibo SDK not loaded');
      return false;
    }

    try {
      const res = await fetch(`${API_BASE}/api/mesibo/token`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ email, user_type: userType }),
      });

      if (!res.ok) {
        console.error('Failed to get Mesibo token:', res.status);
        return false;
      }

      const data = await res.json();

      const api = M.getInstance();
      api.init();
      api.setAccessToken(data.token);
      api.setAppName('com.palengke.online');

      const self = {
        Mesibo_onConnectionStatus(status: number) {
          if (status === 1) {
            console.log('Mesibo connected');
            mesiboInitialized.value = true;
          } else {
            console.warn('Mesibo connection status:', status);
          }
        },
        Mesibo_onMessageStatus() {},
        Mesibo_onPresence() {},
        Mesibo_onMessage() {},
      };

      api.setListener(self);
      api.start();

      mesiboApi = api;
      return true;
    } catch (e) {
      console.error('Mesibo init error:', e);
      return false;
    }
  }

  async function startCall(
    conversationId: number,
    callerEmail: string,
    callerName: string,
    callerType: 'seller' | 'driver',
    receiverEmail: string,
  ): Promise<number | null> {
    try {
      const res = await fetch(`${API_BASE}/api/calls/signal`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({
          conversation_id: conversationId,
          caller_email: callerEmail,
          caller_name: callerName,
          caller_type: callerType,
          receiver_email: receiverEmail,
        }),
      });

      if (!res.ok) return null;

      const data = await res.json();
      const signalId = data.signal.id;
      activeCallSignalId.value = signalId;

      callState.value = 'ringing';
      startCallTimer();
      startCallerPolling(signalId);

      return signalId;
    } catch (e) {
      console.error('Start call error:', e);
      return null;
    }
  }

  function startCallerPolling(signalId: number) {
    stopCallerPolling();
    callerPollTimer = setInterval(async () => {
      try {
        const res = await fetch(`${API_BASE}/api/calls/signal/${signalId}/status`, {
          headers: { Accept: 'application/json' },
        });
        if (!res.ok) return;

        const data = await res.json();
        const status = data.signal?.status;

        if (status === 'accepted') {
          callState.value = 'connected';
          startCallTimer();
          stopCallerPolling();
          startConnectedCallPolling(signalId);
        } else if (status === 'declined' || status === 'ended') {
          callState.value = 'ended';
          stopCallTimer();
          stopCallerPolling();
          setTimeout(() => { callState.value = 'idle'; }, 1500);
        }
      } catch {}
    }, 2000);
  }

  function startConnectedCallPolling(signalId: number) {
    stopConnectedCallPolling();
    connectedPollTimer = setInterval(async () => {
      try {
        const res = await fetch(`${API_BASE}/api/calls/signal/${signalId}/status`, {
          headers: { Accept: 'application/json' },
        });
        if (!res.ok) return;

        const data = await res.json();
        const status = data.signal?.status;

        if (status === 'ended' || status === 'declined') {
          callState.value = 'ended';
          stopCallTimer();
          stopConnectedCallPolling();
          activeCallSignalId.value = null;
          setTimeout(() => { callState.value = 'idle'; }, 1500);
        }
      } catch {}
    }, 2000);
  }

  function stopConnectedCallPolling() {
    if (connectedPollTimer) {
      clearInterval(connectedPollTimer);
      connectedPollTimer = null;
    }
  }

  function stopCallerPolling() {
    if (callerPollTimer) {
      clearInterval(callerPollTimer);
      callerPollTimer = null;
    }
  }

  async function acceptCall(signalId: number) {
    try {
      await fetch(`${API_BASE}/api/calls/signal/${signalId}/accept`, {
        method: 'POST',
        headers: { Accept: 'application/json' },
      });
      callState.value = 'connected';
      activeCallSignalId.value = signalId;
      startCallTimer();
      startConnectedCallPolling(signalId);
    } catch (e) {
      console.error('Accept call error:', e);
    }
  }

  async function declineCall(signalId: number) {
    try {
      await fetch(`${API_BASE}/api/calls/signal/${signalId}/decline`, {
        method: 'POST',
        headers: { Accept: 'application/json' },
      });
      callState.value = 'ended';
      setTimeout(() => { callState.value = 'idle'; }, 1500);
    } catch (e) {
      console.error('Decline call error:', e);
    }
  }

  function endCall() {
    stopCallTimer();
    stopCallerPolling();
    stopConnectedCallPolling();
    callState.value = 'ended';

    const signalId = activeCallSignalId.value;
    activeCallSignalId.value = null;

    if (signalId) {
      fetch(`${API_BASE}/api/calls/signal/${signalId}/end`, {
        method: 'POST',
        headers: { Accept: 'application/json' },
      }).catch(() => {});
    }

    setTimeout(() => { callState.value = 'idle'; }, 1500);
  }

  function startCallTimer() {
    callDuration.value = 0;
    if (callTimerInterval) clearInterval(callTimerInterval);
    callTimerInterval = setInterval(() => {
      callDuration.value++;
    }, 1000);
  }

  function stopCallTimer() {
    if (callTimerInterval) {
      clearInterval(callTimerInterval);
      callTimerInterval = null;
    }
    callDuration.value = 0;
  }

  function formatCallDuration(seconds: number): string {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
  }

  return {
    mesiboInitialized,
    callState,
    callDuration,
    activeCallSignalId,
    initMesibo,
    startCall,
    acceptCall,
    declineCall,
    endCall,
    formatCallDuration,
  };
}
