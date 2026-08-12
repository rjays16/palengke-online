export const useSellersStore = defineStore("sellers", () => {
  function getApiBaseUrl() {
    const config = useRuntimeConfig();
    return config.public.apiBaseUrl || "http://localhost:8000";
  }

  function getToken() {
    if (typeof localStorage === "undefined") return null;
    return localStorage.getItem("palengke_auth_token");
  }

  function mapSeller(raw: any) {
    return {
      id: raw.id,
      name: raw.storeName || "",
      owner: [raw.firstName, raw.lastName].filter(Boolean).join(" ") || "—",

      location: [raw.barangay, raw.city].filter(Boolean).join(", ") || raw.completeAddress || "",
      products: raw.productCount || 0,
      revenue: 0,
      rating: 0,
      status: raw.status === "approved" ? "active" : "inactive",
      verificationStatus: raw.verificationStatus || "not_verified",
      verificationChoice: raw.verificationChoice || "",
      joined: raw.submittedAt || "",
      contact: {
        phone: raw.contact || "",
        email: raw.email || "",
      },
      subscription: raw.subscription || { plan: "Trial", amount: 0, start: "", end: "", status: "trial" },
    };
  }

  const sellers = ref<any[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function fetchSellers() {
    const token = getToken();
    if (!token) return;
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/sellers`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      if (!res.ok) throw new Error(`Failed to load sellers (${res.status})`);
      const json = await res.json();
      sellers.value = (json.sellers || []).map(mapSeller);
    } catch (e: any) {
      error.value = e.message || "Unknown error";
      sellers.value = [];
    } finally {
      loading.value = false;
    }
  }

  function getSellerById(id: number | string) {
    const key = String(id);
    return sellers.value.find((s) => String(s.id) === key);
  }

  function getSellerProducts(_id: number | string) {
    return [
      { id: 1, name: "Fresh Tomatoes", price: 60, unit: "kg", stock: 120 },
      { id: 2, name: "Eggplant", price: 45, unit: "kg", stock: 80 },
      { id: 3, name: "Cabbage", price: 180, unit: "pc", stock: 30 },
      { id: 4, name: "Kangkong", price: 25, unit: "bundle", stock: 60 },
      { id: 5, name: "Sili (Green)", price: 30, unit: "kg", stock: 40 },
    ];
  }

  function getSellerOrders(_id: number | string) {
    return [
      { id: "ORD-1042", customer: "Maria Santos", items: 4, total: 1280, status: "delivered", date: "2026-05-14" },
      { id: "ORD-1039", customer: "Pedro Reyes", items: 1, total: 180, status: "pending", date: "2026-05-13" },
      { id: "ORD-1035", customer: "Liza Morales", items: 4, total: 1120, status: "transit", date: "2026-05-12" },
      { id: "ORD-1031", customer: "Tina Rivera", items: 3, total: 890, status: "delivered", date: "2026-05-10" },
      { id: "ORD-1028", customer: "Rosa Mendiola", items: 5, total: 1560, status: "delivered", date: "2026-05-08" },
    ];
  }

  const AVAILABLE_PLANS = [
    { name: "1 Month", months: 1, amount: 800 },
    { name: "2 Months", months: 2, amount: 1500 },
    { name: "4 Months", months: 4, amount: 2800 },
    { name: "8 Months", months: 8, amount: 5200 },
  ];

  function changeSubscriptionPlan(id: number | string, planName: string, amount: number, months: number) {
    const seller = sellers.value.find((s) => String(s.id) === String(id));
    if (!seller) return;
    const now = new Date();
    const end = new Date(now);
    end.setMonth(end.getMonth() + months);
    seller.subscription = { plan: planName, amount, start: now.toISOString().slice(0, 10), end: end.toISOString().slice(0, 10), status: "active" };
  }

  function extendSubscriptionDays(id: number | string, extraDays: number) {
    const seller = sellers.value.find((s) => String(s.id) === String(id));
    if (!seller) return;
    const end = new Date(seller.subscription.end);
    end.setDate(end.getDate() + extraDays);
    seller.subscription.end = end.toISOString().slice(0, 10);
  }

  function cancelSubscription(id: number | string) {
    const seller = sellers.value.find((s) => String(s.id) === String(id));
    if (!seller) return;
    seller.subscription.status = "cancelled";
  }

  function addSeller(_data: { name: string; owner: string; location: string; phone: string; email: string }) {
    // Backend endpoint not yet implemented
  }

  function updateSeller(_id: number | string, _data: { name: string; owner: string; location: string; phone: string; email: string }) {
    // Backend endpoint not yet implemented
  }

  function deleteSeller(_id: number | string) {
    // Backend endpoint not yet implemented
  }

  function toggleStatus(_id: number | string) {
    // Backend endpoint not yet implemented
  }

  return {
    sellers,
    loading,
    error,
    fetchSellers,
    getSellerById,
    getSellerProducts,
    getSellerOrders,
    toggleStatus,
    changeSubscriptionPlan,
    extendSubscriptionDays,
    cancelSubscription,
    AVAILABLE_PLANS,
    addSeller,
    updateSeller,
    deleteSeller,
  };
});
