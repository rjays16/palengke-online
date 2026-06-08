function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem("palengke_auth_token");
}

export const useCategoriesStore = defineStore("categories", () => {
  const categories = ref([]);
  const loading = ref(false);

  async function fetchCategories() {
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/categories`);
      if (res.ok) {
        categories.value = await res.json();
      }
    } catch {
      // silent
    }
  }

  function authHeaders() {
    return {
      Authorization: `Bearer ${getToken()}`,
      Accept: "application/json",
    };
  }

  async function addCategory(formData) {
    loading.value = true;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/categories`, {
        method: "POST",
        headers: authHeaders(),
        body: formData,
      });
      if (res.ok) {
        await fetchCategories();
        return { ok: true };
      }
      const data = await res.json();
      const msg = data.message || Object.values(data.errors || {}).flat().join(", ") || "Failed to create";
      return { ok: false, error: msg };
    } catch {
      return { ok: false, error: "Network error" };
    } finally {
      loading.value = false;
    }
  }

  async function updateCategory(id, formData) {
    loading.value = true;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/categories/${id}`, {
        method: "POST",
        headers: authHeaders(),
        body: formData,
      });
      if (res.ok) {
        await fetchCategories();
        return { ok: true };
      }
      const data = await res.json();
      const msg = data.message || Object.values(data.errors || {}).flat().join(", ") || "Failed to update";
      return { ok: false, error: msg };
    } catch {
      return { ok: false, error: "Network error" };
    } finally {
      loading.value = false;
    }
  }

  async function deleteCategory(id) {
    loading.value = true;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/categories/${id}`, {
        method: "DELETE",
        headers: authHeaders(),
      });
      if (res.ok) await fetchCategories();
    } catch {
      // silent
    } finally {
      loading.value = false;
    }
  }

  return { categories, loading, fetchCategories, addCategory, updateCategory, deleteCategory };
});
