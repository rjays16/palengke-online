export const useReportsStore = defineStore("reports", () => {
  const submissions = ref([
    {
      id: "RPT-001",
      type: "seller",
      submittedBy: "Juan's Farm",
      subject: "Unfair rating deduction",
      description:
        "A customer gave a 1-star rating without proper reason. We request review.",
      status: "pending",
      priority: "high",
      date: "2026-05-14",
    },
    {
      id: "RPT-002",
      type: "driver",
      submittedBy: "Mark Reyes",
      subject: "Hazardous road condition",
      description:
        "Delivery route to Brgy. San Isidro has deep potholes that damaged my motorcycle tire.",
      status: "pending",
      priority: "medium",
      date: "2026-05-14",
    },
    {
      id: "RPT-003",
      type: "customer",
      submittedBy: "Maria Santos",
      subject: "Damaged product received",
      description:
        "Received a delivery with damaged tomatoes. The seller refused to replace.",
      status: "resolved",
      priority: "high",
      date: "2026-05-13",
    },
    {
      id: "RPT-004",
      type: "seller",
      submittedBy: "Fresh Fish Co.",
      subject: "Delivery delay complaints",
      description:
        "Multiple customers complaining about late deliveries from assigned drivers.",
      status: "in_progress",
      priority: "high",
      date: "2026-05-13",
    },
    {
      id: "RPT-005",
      type: "driver",
      submittedBy: "Nina Salvador",
      subject: "Wrong address provided",
      description:
        "Customer provided incorrect address. Had to return undelivered order.",
      status: "resolved",
      priority: "low",
      date: "2026-05-12",
    },
    {
      id: "RPT-006",
      type: "customer",
      submittedBy: "Pedro Reyes",
      subject: "Overcharged on order",
      description:
        "Was charged ₱180 for cabbage but price listed was ₱150. Request refund of difference.",
      status: "pending",
      priority: "medium",
      date: "2026-05-12",
    },
    {
      id: "RPT-007",
      type: "seller",
      submittedBy: "Maria's Produce",
      subject: "Driver misconduct",
      description:
        "Driver was rude to my staff during pickup. Screaming and using profanity.",
      status: "dismissed",
      priority: "high",
      date: "2026-05-11",
    },
    {
      id: "RPT-008",
      type: "customer",
      submittedBy: "Luisa Tan",
      subject: "Missing items in delivery",
      description:
        "Ordered 6 items but only received 4. Missing pork and shrimp.",
      status: "in_progress",
      priority: "high",
      date: "2026-05-10",
    },
    {
      id: "RPT-009",
      type: "driver",
      submittedBy: "Tommy Cruz",
      subject: "Payment dispute",
      description:
        "Customer claimed COD payment was made but I did not receive any payment.",
      status: "pending",
      priority: "medium",
      date: "2026-05-10",
    },
    {
      id: "RPT-010",
      type: "seller",
      submittedBy: "Meat Shop Inc.",
      subject: "System glitch on inventory",
      description:
        "Inventory count not updating after sales. Showing incorrect stock levels.",
      status: "resolved",
      priority: "low",
      date: "2026-05-09",
    },
  ]);

  function updateStatus(id: string, status: string) {
    const report = submissions.value.find((r) => r.id === id);
    if (report) report.status = status;
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

  return { submissions, updateStatus, analytics, exportCSV };
});
