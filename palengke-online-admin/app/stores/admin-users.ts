const AUTH_TOKEN_KEY = "palengke_auth_token";

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem(AUTH_TOKEN_KEY);
}

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

export const useAdminUsersStore = defineStore("adminUsers", () => {
  const admins = ref<
    Array<{
      id: number;
      name: string;
      email: string;
      role: string;
      createdAt: string;
      status: string;
    }>
  >([]);

  const isLoading = ref(false);
  const error = ref("");

  async function fetchAdmins() {
    isLoading.value = true;
    error.value = "";

    try {
      const token = getToken();
      const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/users`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      admins.value = response.users;
    } catch (err: any) {
      error.value = err?.data?.message || "Failed to fetch admin users";
    } finally {
      isLoading.value = false;
    }
  }

  async function addAdmin(data: {
    name: string;
    email: string;
    password: string;
    role: string;
  }) {
    isLoading.value = true;
    error.value = "";

    try {
      const token = getToken();
      const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/users`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
        },
        body: data,
      });

      admins.value.unshift(response.user);
      return true;
    } catch (err: any) {
      error.value = err?.data?.message || "Failed to create admin user";
      return false;
    } finally {
      isLoading.value = false;
    }
  }

  async function updateAdmin(
    id: number,
    data: {
      name: string;
      email: string;
      password?: string;
      role: string;
      status: string;
    }
  ) {
    isLoading.value = true;
    error.value = "";

    try {
      const token = getToken();
      const response: any = await $fetch(`${getApiBaseUrl()}/api/admin/users/${id}`, {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${token}`,
        },
        body: data,
      });

      const index = admins.value.findIndex((a) => a.id === id);
      if (index !== -1) {
        admins.value[index] = response.user;
      }
      return true;
    } catch (err: any) {
      error.value = err?.data?.message || "Failed to update admin user";
      return false;
    } finally {
      isLoading.value = false;
    }
  }

  async function deleteAdmin(id: number) {
    isLoading.value = true;
    error.value = "";

    try {
      const token = getToken();
      await $fetch(`${getApiBaseUrl()}/api/admin/users/${id}`, {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      admins.value = admins.value.filter((a) => a.id !== id);
      return true;
    } catch (err: any) {
      error.value = err?.data?.message || "Failed to delete admin user";
      return false;
    } finally {
      isLoading.value = false;
    }
  }

  function findByEmail(email: string) {
    return admins.value.find((a) => a.email === email);
  }

  return {
    admins,
    isLoading,
    error,
    fetchAdmins,
    addAdmin,
    updateAdmin,
    deleteAdmin,
    findByEmail,
  };
});
