const AUTH_TOKEN_KEY = "palengke_auth_token";

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem(AUTH_TOKEN_KEY);
}

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

export const useRegistrationsStore = defineStore("registrations", () => {
  const pending = ref([]);
  const history = ref([]);
  const isLoading = ref(false);
  const error = ref("");

  async function fetchPending() {
    isLoading.value = true;
    error.value = "";
    try {
      const token = getToken();
      const data: any = await $fetch(
        `${getApiBaseUrl()}/api/admin/sellers/pending`,
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      );
      pending.value = data.pending;
    } catch (err: any) {
      error.value = err?.data?.message || "Failed to fetch pending registrations";
    } finally {
      isLoading.value = false;
    }
  }

  async function fetchHistory() {
    isLoading.value = true;
    error.value = "";
    try {
      const token = getToken();
      const data: any = await $fetch(
        `${getApiBaseUrl()}/api/admin/sellers/history`,
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      );
      history.value = data.history;
    } catch (err: any) {
      error.value = err?.data?.message || "Failed to fetch history";
    } finally {
      isLoading.value = false;
    }
  }

  async function approve(id) {
    isLoading.value = true;
    error.value = "";
    try {
      const token = getToken();
      await $fetch(`${getApiBaseUrl()}/api/admin/sellers/${id}/approve`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
      });
      await fetchPending();
      await fetchHistory();
    } catch (err: any) {
      error.value = err?.data?.message || "Failed to approve registration";
    } finally {
      isLoading.value = false;
    }
  }

  async function reject(id, reason) {
    isLoading.value = true;
    error.value = "";
    try {
      const token = getToken();
      await $fetch(`${getApiBaseUrl()}/api/admin/sellers/${id}/reject`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: { reason },
      });
      await fetchPending();
      await fetchHistory();
    } catch (err: any) {
      error.value = err?.data?.message || "Failed to reject registration";
    } finally {
      isLoading.value = false;
    }
  }

  const analytics = computed(() => {
    return {
      pending: pending.value.length,
      approved: history.value.filter((r) => r.status === "approved").length,
      rejected: history.value.filter((r) => r.status === "rejected").length,
      total: pending.value.length + history.value.length,
    };
  });

  return { pending, history, isLoading, error, fetchPending, fetchHistory, approve, reject, analytics };
});
