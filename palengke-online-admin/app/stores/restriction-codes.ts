function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem("palengke_auth_token");
}

export const useRestrictionCodesStore = defineStore("restrictionCodes", () => {
  const codes = ref([]);
  const loading = ref(false);

  async function fetchCodes() {
    try {
      const token = getToken();
      const res: any = await $fetch(`${getApiBaseUrl()}/api/admin/restriction-codes`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      codes.value = res.restriction_codes;
    } catch {
      // silent
    }
  }

  async function toggleCode(id: number, enabled: boolean) {
    loading.value = true;
    try {
      const token = getToken();
      await $fetch(`${getApiBaseUrl()}/api/admin/restriction-codes/${id}/toggle`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
        body: { enabled },
      });
    } catch {
      // silent
    } finally {
      loading.value = false;
    }
  }

  return { codes, loading, fetchCodes, toggleCode };
});
