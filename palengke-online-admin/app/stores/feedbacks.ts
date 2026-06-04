const AUTH_TOKEN_KEY = "palengke_auth_token";

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem(AUTH_TOKEN_KEY);
}

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

export const useFeedbacksStore = defineStore("feedbacks", () => {
  const submissions = ref([]);
  const isLoading = ref(false);
  const error = ref("");

  async function fetchFeedbacks() {
    isLoading.value = true;
    error.value = "";
    try {
      const token = getToken();
      const data = await $fetch(
        `${getApiBaseUrl()}/api/admin/feedbacks`,
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      );
      submissions.value = (data.feedbacks || []).map((f) => ({
        id: `FB-${String(f.id).padStart(3, "0")}`,
        type: f.type || "feedback",
        submittedBy: f.submitted_by,
        message: f.message,
        status: f.status,
        date: f.created_at ? f.created_at.slice(0, 10) : "",
      }));
    } catch (err) {
      error.value = err?.data?.message || "Failed to fetch feedbacks";
      submissions.value = [];
    } finally {
      isLoading.value = false;
    }
  }

  async function updateStatus(id, status) {
    const token = getToken();
    try {
      const numericId = parseInt(id.replace("FB-", ""), 10);
      await $fetch(`${getApiBaseUrl()}/api/admin/feedbacks/${numericId}`, {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: { status },
      });
      const item = submissions.value.find((f) => f.id === id);
      if (item) item.status = status;
    } catch (err) {
      error.value = err?.data?.message || "Failed to update feedback status";
    }
  }

  const analytics = computed(() => {
    const total = submissions.value.length;
    const pending = submissions.value.filter((f) => f.status === "pending").length;
    const reviewed = submissions.value.filter((f) => f.status === "reviewed").length;
    const archived = submissions.value.filter((f) => f.status === "archived").length;
    return { total, pending, reviewed, archived };
  });

  return { submissions, isLoading, error, fetchFeedbacks, updateStatus, analytics };
});
