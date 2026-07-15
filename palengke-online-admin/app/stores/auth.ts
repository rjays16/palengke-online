const AUTH_TOKEN_KEY = "palengke_auth_token";

function getTokenFromStorage() {
  if (typeof localStorage === "undefined") return null;
  try {
    return localStorage.getItem(AUTH_TOKEN_KEY);
  } catch {
    return null;
  }
}

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

export const useAuthStore = defineStore("auth", () => {
  const user = ref<{
    id: number;
    email: string;
    name: string;
    role: string;
  } | null>(null);

  const token = ref<string | null>(getTokenFromStorage());

  const isAuthenticated = computed(() => !!token.value && !!user.value);
  const isLoading = ref(false);
  const loginError = ref("");

  const isSuperAdmin = computed(() => user.value?.role === "superadmin");
  const isAdmin = computed(() => user.value?.role === "admin");

  const canApprove = computed(() => isSuperAdmin.value);
  const canAccessSettings = computed(() => isSuperAdmin.value);

  async function login(email: string, password: string) {
    loginError.value = "";

    try {
      const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/login`, {
        method: "POST",
        body: { email, password },
      });

      token.value = response.token;
      user.value = response.user;

      if (typeof localStorage !== "undefined") {
        localStorage.setItem(AUTH_TOKEN_KEY, response.token);
      }

      return true;
    } catch (err: any) {
      loginError.value = err?.data?.message || err?.data?.email?.[0] || "Invalid email or password";
      return false;
    }
  }

  async function logout() {
    if (token.value) {
      try {
        await $fetch(`${getApiBaseUrl()}/api/admin/logout`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token.value}`,
          },
        });
      } catch {
        // ignore logout errors
      }
    }

    user.value = null;
    token.value = null;

    if (typeof localStorage !== "undefined") {
      localStorage.removeItem(AUTH_TOKEN_KEY);
    }
  }

  async function restore() {
    const savedToken = getTokenFromStorage();
    if (!savedToken) return;

    token.value = savedToken;

    try {
      const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/me`, {
        headers: {
          Authorization: `Bearer ${savedToken}`,
        },
      });

      user.value = response.user;
    } catch {
      token.value = null;
      user.value = null;
      if (typeof localStorage !== "undefined") {
        localStorage.removeItem(AUTH_TOKEN_KEY);
      }
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isLoading,
    loginError,
    isSuperAdmin,
    isAdmin,
    canApprove,
    canAccessSettings,
    login,
    logout,
    restore,
  };
});
