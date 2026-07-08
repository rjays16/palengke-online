export const useDriversStore = defineStore("drivers", () => {
  function getApiBaseUrl() {
    const config = useRuntimeConfig();
    return config.public.apiBaseUrl || "http://localhost:8000";
  }

  function getToken() {
    if (typeof localStorage === "undefined") return null;
    return localStorage.getItem("palengke_auth_token");
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
  const error = ref<string | null>(null);

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

  let nextId = 1;

  function addDriver(data: {
    name: string;
    phone: string;
    vehicle: string;
    plate: string;
  }) {
    drivers.value.push({
      id: `TEMP-${nextId++}`,
      name: data.name,
      phone: data.phone,
      vehicle: data.vehicle,
      plate: data.plate,
      deliveries: 0,
      rating: 0,
      earnings: 0,
      status: "active",
      joined: new Date().toISOString().slice(0, 10),
    });
  }

  function updateDriver(
    id: number | string,
    data: {
      name: string;
      phone: string;
      vehicle: string;
      plate: string;
      status: string;
    },
  ) {
    const d = drivers.value.find((d) => String(d.id) === String(id));
    if (!d) return;
    d.name = data.name;
    d.phone = data.phone;
    d.vehicle = data.vehicle;
    d.plate = data.plate;
    d.status = data.status;
  }

  function deleteDriver(id: number | string) {
    drivers.value = drivers.value.filter((d) => String(d.id) !== String(id));
  }

  function getDeliveryHistory(_id: number | string) {
    return [
      {
        id: "ORD-1042",
        customer: "Maria Santos",
        pickup: "Juan's Farm",
        delivery: "Brgy. San Isidro",
        status: "delivered",
        date: "2026-05-14",
      },
      {
        id: "ORD-1040",
        customer: "Ana Cruz",
        pickup: "Maria's Produce",
        delivery: "Brgy. Maligaya",
        status: "delivered",
        date: "2026-05-13",
      },
      {
        id: "ORD-1038",
        customer: "Luisa Tan",
        pickup: "Meat Shop Inc.",
        delivery: "Brgy. Bagong Silang",
        status: "delivered",
        date: "2026-05-12",
      },
      {
        id: "ORD-1036",
        customer: "Ben Torres",
        pickup: "Fresh Fish Co.",
        delivery: "Brgy. Poblacion",
        status: "delivered",
        date: "2026-05-12",
      },
      {
        id: "ORD-1033",
        customer: "Tina Rivera",
        pickup: "Fresh Fish Co.",
        delivery: "Brgy. Sampa",
        status: "delivered",
        date: "2026-05-11",
      },
    ];
  }

  return {
    drivers,
    loading,
    error,
    fetchDrivers,
    getDriverById,
    getDocuments,
    getDeliveryHistory,
    addDriver,
    updateDriver,
    deleteDriver,
  };
});
