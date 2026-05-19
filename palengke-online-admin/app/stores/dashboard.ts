export const useDashboardStore = defineStore("dashboard", () => {
  const stats = ref([
    {
      label: "Total Revenue",
      value: "₱284,500",
      change: "+12.5%",
      up: true,
      period: "vs last month",
    },
    {
      label: "Active Orders",
      value: "156",
      change: "+8.2%",
      up: true,
      period: "vs last month",
    },
    {
      label: "Total Sellers",
      value: "48",
      change: "+3.1%",
      up: true,
      period: "vs last month",
    },
    {
      label: "Total Drivers",
      value: "32",
      change: "-2.4%",
      up: false,
      period: "vs last month",
    },
  ]);

  const barData = ref([
    { label: "Mon", pct: 45, highlight: false },
    { label: "Tue", pct: 65, highlight: false },
    { label: "Wed", pct: 80, highlight: false },
    { label: "Thu", pct: 55, highlight: false },
    { label: "Fri", pct: 90, highlight: true },
    { label: "Sat", pct: 70, highlight: false },
    { label: "Sun", pct: 50, highlight: false },
  ]);

  const orderStatusData = ref([
    { label: "Delivered", count: 145, color: "#059669" },
    { label: "In Transit", count: 68, color: "#3B82F6" },
    { label: "Preparing", count: 32, color: "#F59E0B" },
    { label: "Cancelled", count: 12, color: "#EF4444" },
  ]);

  const recentOrders = ref([
    {
      id: "ORD-1042",
      customer: "Maria Santos",
      items: 4,
      total: 1280,
      status: "delivered",
      date: "2026-05-14",
    },
    {
      id: "ORD-1041",
      customer: "Jose Rizal",
      items: 2,
      total: 450,
      status: "transit",
      date: "2026-05-14",
    },
    {
      id: "ORD-1040",
      customer: "Ana Cruz",
      items: 6,
      total: 2340,
      status: "preparing",
      date: "2026-05-13",
    },
    {
      id: "ORD-1039",
      customer: "Pedro Reyes",
      items: 1,
      total: 180,
      status: "pending",
      date: "2026-05-13",
    },
    {
      id: "ORD-1038",
      customer: "Luisa Tan",
      items: 3,
      total: 890,
      status: "cancelled",
      date: "2026-05-13",
    },
  ]);

  return { stats, barData, orderStatusData, recentOrders };
});
