const AUTH_TOKEN_KEY = "palengke_auth_token";

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem(AUTH_TOKEN_KEY);
}

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

export const useNotificationsStore = defineStore("notifications", () => {
  const allNotifications = ref([]);
  const unreadCount = ref(0);
  const isLoading = ref(false);

  async function fetchNotifications() {
    isLoading.value = true;
    try {
      const token = getToken();
      const data: any = await $fetch(
        `${getApiBaseUrl()}/api/admin/notifications`,
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      );
      allNotifications.value = data.notifications;
      unreadCount.value = data.unread_count;
    } catch {
      // silent fail — keep existing data
    } finally {
      isLoading.value = false;
    }
  }

  async function markRead(id) {
    try {
      const token = getToken();
      await $fetch(`${getApiBaseUrl()}/api/admin/notifications/${id}/read`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
      });
      const n = allNotifications.value.find((n) => n.id === id);
      if (n) n.read = true;
      unreadCount.value = allNotifications.value.filter((n) => !n.read).length;
    } catch {
      // silent
    }
  }

  async function markAllRead() {
    try {
      const token = getToken();
      await $fetch(`${getApiBaseUrl()}/api/admin/notifications/read-all`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
      });
      allNotifications.value.forEach((n) => (n.read = true));
      unreadCount.value = 0;
    } catch {
      // silent
    }
  }

  async function deleteNotification(id) {
    try {
      const token = getToken();
      await $fetch(`${getApiBaseUrl()}/api/admin/notifications/${id}`, {
        method: "DELETE",
        headers: { Authorization: `Bearer ${token}` },
      });
      allNotifications.value = allNotifications.value.filter((n) => n.id !== id);
      unreadCount.value = allNotifications.value.filter((n) => !n.read).length;
    } catch {
      // silent
    }
  }

  return {
    allNotifications,
    unreadCount,
    isLoading,
    fetchNotifications,
    markRead,
    markAllRead,
    deleteNotification,
  };
});
