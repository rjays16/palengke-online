export const useDriversStore = defineStore("drivers", () => {
  function getApiBaseUrl() {
    const config = useRuntimeConfig();
    return config.public.apiBaseUrl || "http://localhost:8000";
  }

  function getToken() {
    if (typeof localStorage === "undefined") return null;
    return localStorage.getItem("palengke_auth_token");
  }

  function getHeaders() {
    return {
      Authorization: `Bearer ${getToken()}`,
      "Content-Type": "application/json",
    };
  }

  function mapDriver(raw: any) {
    return {
      id: raw.id,
      name: [raw.firstName, raw.middleName, raw.lastName].filter(Boolean).join(" ") || "—",
      phone: raw.contact || "",
      vehicle: [raw.vehicleType, raw.vehicleModel].filter(Boolean).join(" ") || "—",
      plate: raw.plateNumber || "",
      deliveries: 0,
      rating: 0,
      earnings: 0,
      status: raw.status === "approved" ? "active" : raw.status === "pending" ? "idle" : "offline",
      joined: raw.submittedAt || "",
      email: raw.email || "",
      licenseNumber: raw.licenseNumber || "",
      licenseType: raw.licenseType || "",
      restrictionCode: raw.restrictionCode || "",
      expiryDate: raw.expiryDate || "",
      vehicleType: raw.vehicleType || "",
      vehicleModel: raw.vehicleModel || "",
      vehicleColor: raw.vehicleColor || "",
      birthdate: raw.birthdate || "",
      birthplace: raw.birthplace || "",
      currentAddress: raw.currentAddress || "",
      country: raw.country || "",
      licenseFrontPhoto: raw.licenseFrontPhoto || "",
      licenseBackPhoto: raw.licenseBackPhoto || "",
      vehicleRegPhoto: raw.vehicleRegPhoto || "",
      nbiClearancePhoto: raw.nbiClearancePhoto || "",
      birthCertPhoto: raw.birthCertPhoto || "",
      selfiePhoto: raw.selfiePhoto || "",
      rejectReason: raw.rejectReason || null,
    };
  }

  const drivers = ref<any[]>([]);
  const loading = ref(false);
  const saving = ref(false);
  const error = ref<string | null>(null);
  const deliveries = ref<any[]>([]);

  async function fetchDrivers() {
    const token = getToken();
    if (!token) return;
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/drivers`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      if (!res.ok) throw new Error(`Failed to load drivers (${res.status})`);
      const json = await res.json();
      drivers.value = (json.drivers || []).map(mapDriver);
    } catch (e: any) {
      error.value = e.message || "Unknown error";
      drivers.value = [];
    } finally {
      loading.value = false;
    }
  }

  function getDriverById(id: number | string) {
    const key = String(id);
    return drivers.value.find((d) => String(d.id) === key);
  }

  function getDocuments(driver: any) {
    const docs = [
      { key: "licenseFrontPhoto", name: "Driver's License (Front)" },
      { key: "licenseBackPhoto", name: "Driver's License (Back)" },
      { key: "vehicleRegPhoto", name: "Vehicle Registration (OR/CR)" },
      { key: "nbiClearancePhoto", name: "NBI Clearance" },
      { key: "birthCertPhoto", name: "Birth Certificate" },
      { key: "selfiePhoto", name: "Selfie Photo" },
    ];
    return docs.map((d) => ({
      name: d.name,
      status: driver[d.key] ? "verified" : "missing",
      url: driver[d.key] || null,
    }));
  }

  async function addDriver(data: {
    name: string;
    phone: string;
    vehicle: string;
    plate: string;
  }) {
    saving.value = true;
    error.value = null;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/drivers`, {
        method: "POST",
        headers: getHeaders(),
        body: JSON.stringify(data),
      });
      if (!res.ok) {
        const err = await res.json();
        throw new Error(err.message || `Failed to create driver (${res.status})`);
      }
      const json = await res.json();
      drivers.value.unshift(mapDriver(json.driver));
      return true;
    } catch (e: any) {
      error.value = e.message || "Unknown error";
      return false;
    } finally {
      saving.value = false;
    }
  }

  async function updateDriver(
    id: number | string,
    data: {
      name: string;
      phone: string;
      vehicle: string;
      plate: string;
      status: string;
    },
  ) {
    saving.value = true;
    error.value = null;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/drivers/${id}`, {
        method: "PUT",
        headers: getHeaders(),
        body: JSON.stringify(data),
      });
      if (!res.ok) {
        const err = await res.json();
        throw new Error(err.message || `Failed to update driver (${res.status})`);
      }
      const json = await res.json();
      const idx = drivers.value.findIndex((d) => String(d.id) === String(id));
      if (idx !== -1) drivers.value[idx] = mapDriver(json.driver);
      return true;
    } catch (e: any) {
      error.value = e.message || "Unknown error";
      return false;
    } finally {
      saving.value = false;
    }
  }

  async function deleteDriver(id: number | string) {
    saving.value = true;
    error.value = null;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/drivers/${id}`, {
        method: "DELETE",
        headers: { Authorization: `Bearer ${getToken()}` },
      });
      if (!res.ok) throw new Error(`Failed to delete driver (${res.status})`);
      drivers.value = drivers.value.filter((d) => String(d.id) !== String(id));
      return true;
    } catch (e: any) {
      error.value = e.message || "Unknown error";
      return false;
    } finally {
      saving.value = false;
    }
  }

  async function fetchDriver(id: number | string) {
    const token = getToken();
    if (!token) return;
    error.value = null;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/drivers/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      if (!res.ok) throw new Error(`Failed to load driver (${res.status})`);
      const json = await res.json();
      const mapped = mapDriver(json.registration);
      const idx = drivers.value.findIndex((d) => String(d.id) === String(id));
      if (idx !== -1) {
        drivers.value[idx] = mapped;
      } else {
        drivers.value.push(mapped);
      }
    } catch (e: any) {
      error.value = e.message || "Unknown error";
    }
  }

  async function fetchDeliveryHistory(id: number | string) {
    const token = getToken();
    if (!token) return;
    try {
      const res = await fetch(`${getApiBaseUrl()}/api/admin/drivers/${id}/deliveries`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      if (!res.ok) throw new Error(`Failed to load delivery history (${res.status})`);
      const json = await res.json();
      deliveries.value = json.deliveries || [];
    } catch {
      deliveries.value = [];
    }
  }

  return {
    drivers,
    loading,
    saving,
    error,
    deliveries,
    fetchDrivers,
    fetchDriver,
    fetchDeliveryHistory,
    getDriverById,
    getDocuments,
    addDriver,
    updateDriver,
    deleteDriver,
  };
});
