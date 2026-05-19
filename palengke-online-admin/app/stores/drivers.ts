export const useDriversStore = defineStore("drivers", () => {
  const drivers = ref([
    {
      id: 1,
      name: "Mark Reyes",
      phone: "0917-123-4567",
      vehicle: "Honda Click 125",
      plate: "ABC-1234",
      deliveries: 342,
      rating: 4.9,
      earnings: 45800,
      status: "active",
      joined: "2024-01-10",
    },
    {
      id: 2,
      name: "Lisa Gonzales",
      phone: "0918-234-5678",
      vehicle: "Yamaha Mio i125",
      plate: "DEF-5678",
      deliveries: 289,
      rating: 4.7,
      earnings: 38200,
      status: "active",
      joined: "2024-02-15",
    },
    {
      id: 3,
      name: "Tommy Cruz",
      phone: "0919-345-6789",
      vehicle: "Suzuki Skydrive",
      plate: "GHI-9012",
      deliveries: 156,
      rating: 4.5,
      earnings: 21400,
      status: "idle",
      joined: "2024-04-20",
    },
    {
      id: 4,
      name: "Nina Santos",
      phone: "0920-456-7890",
      vehicle: "Honda Beat",
      plate: "JKL-3456",
      deliveries: 421,
      rating: 4.8,
      earnings: 52100,
      status: "active",
      joined: "2023-11-05",
    },
    {
      id: 5,
      name: "Ramon Diaz",
      phone: "0921-567-8901",
      vehicle: "Yamaha Nmax",
      plate: "MNO-7890",
      deliveries: 198,
      rating: 4.6,
      earnings: 27500,
      status: "offline",
      joined: "2024-03-12",
    },
    {
      id: 6,
      name: "Cathy Lim",
      phone: "0922-678-9012",
      vehicle: "Honda PCX160",
      plate: "PQR-1234",
      deliveries: 367,
      rating: 4.9,
      earnings: 49300,
      status: "active",
      joined: "2024-01-22",
    },
    {
      id: 7,
      name: "Dennis Alcantara",
      phone: "0923-789-0123",
      vehicle: "Suzuki Burgman",
      plate: "STU-5678",
      deliveries: 112,
      rating: 4.3,
      earnings: 15800,
      status: "idle",
      joined: "2024-06-01",
    },
  ]);

  function getDriverById(id: number) {
    return drivers.value.find((d) => d.id === id);
  }

  function getDocuments() {
    return [
      { name: "Driver's License", status: "verified" },
      { name: "OR/CR", status: "verified" },
      { name: "NBI Clearance", status: "pending" },
      { name: "Birth Certificate", status: "rejected" },
    ];
  }

  let nextId = 8;

  function addDriver(data: {
    name: string;
    phone: string;
    vehicle: string;
    plate: string;
  }) {
    drivers.value.push({
      id: nextId++,
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
    id: number,
    data: {
      name: string;
      phone: string;
      vehicle: string;
      plate: string;
      status: string;
    },
  ) {
    const d = drivers.value.find((d) => d.id === id);
    if (!d) return;
    d.name = data.name;
    d.phone = data.phone;
    d.vehicle = data.vehicle;
    d.plate = data.plate;
    d.status = data.status;
  }

  function deleteDriver(id: number) {
    drivers.value = drivers.value.filter((d) => d.id !== id);
  }

  function getDeliveryHistory(id: number) {
    const allHistory = [
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
    return allHistory;
  }

  return {
    drivers,
    getDriverById,
    getDocuments,
    getDeliveryHistory,
    addDriver,
    updateDriver,
    deleteDriver,
  };
});
