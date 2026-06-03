const AUTH_TOKEN_KEY = "palengke_auth_token";

function getToken() {
  if (typeof localStorage === "undefined") return null;
  return localStorage.getItem(AUTH_TOKEN_KEY);
}

function getApiBaseUrl() {
  const config = useRuntimeConfig();
  return config.public.apiBaseUrl || "http://localhost:8000";
}

export const useReportsStore = defineStore("reports", () => {
  const submissions = ref([]);
  const isLoading = ref(false);
  const error = ref("");

  async function fetchReports() {
    isLoading.value = true;
    error.value = "";
    try {
      const token = getToken();
      const data = await $fetch(
        `${getApiBaseUrl()}/api/admin/reports`,
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      );
      submissions.value = (data.reports || []).map((r) => ({
        id: `RPT-${String(r.id).padStart(3, "0")}`,
        type: r.type,
        submittedBy: r.submitted_by,
        subject: r.subject,
        description: r.description,
        status: r.status,
        priority: r.priority,
        date: r.created_at ? r.created_at.slice(0, 10) : "",
      }));
    } catch (err) {
      error.value = err?.data?.message || "Failed to fetch reports";
      submissions.value = [];
    } finally {
      isLoading.value = false;
    }
  }

  async function updateStatus(id, status) {
    const token = getToken();
    try {
      const numericId = parseInt(id.replace("RPT-", ""), 10);
      await $fetch(`${getApiBaseUrl()}/api/admin/reports/${numericId}`, {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: { status },
      });
      const report = submissions.value.find((r) => r.id === id);
      if (report) report.status = status;
    } catch (err) {
      error.value = err?.data?.message || "Failed to update report status";
    }
  }

  const analytics = computed(() => {
    const total = submissions.value.length;
    const pending = submissions.value.filter(
      (r) => r.status === "pending",
    ).length;
    const inProgress = submissions.value.filter(
      (r) => r.status === "in_progress",
    ).length;
    const resolved = submissions.value.filter(
      (r) => r.status === "resolved",
    ).length;
    const dismissed = submissions.value.filter(
      (r) => r.status === "dismissed",
    ).length;
    const byType = {
      seller: submissions.value.filter((r) => r.type === "seller").length,
      driver: submissions.value.filter((r) => r.type === "driver").length,
      customer: submissions.value.filter((r) => r.type === "customer").length,
    };
    const byPriority = {
      high: submissions.value.filter((r) => r.priority === "high").length,
      medium: submissions.value.filter((r) => r.priority === "medium").length,
      low: submissions.value.filter((r) => r.priority === "low").length,
    };
    return {
      total,
      pending,
      inProgress,
      resolved,
      dismissed,
      byType,
      byPriority,
    };
  });

  function exportCSV() {
    const headers = [
      "ID",
      "Type",
      "Submitted By",
      "Subject",
      "Status",
      "Priority",
      "Date",
    ];
    const rows = submissions.value.map((r) => [
      r.id,
      r.type,
      r.submittedBy,
      r.subject,
      r.status,
      r.priority,
      r.date,
    ]);
    const csv = [headers, ...rows]
      .map((row) => row.map((cell) => `"${cell}"`).join(","))
      .join("\n");
    const blob = new Blob([csv], { type: "text/csv" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `reports-export-${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  }

  return {
    submissions,
    isLoading,
    error,
    fetchReports,
    updateStatus,
    analytics,
    exportCSV,
  };
});
