export const useAdminUsersStore = defineStore("adminUsers", () => {
  const admins = ref<
    Array<{
      id: number;
      name: string;
      email: string;
      password: string;
      role: string;
      createdAt: string;
      status: string;
    }>
  >([
    {
      id: 1,
      name: "Super Admin",
      email: "superadmin@palengke.com",
      password: "superadmin123",
      role: "superadmin",
      createdAt: "2024-01-01",
      status: "active",
    },
    {
      id: 2,
      name: "Admin",
      email: "admin@palengke.com",
      password: "admin123",
      role: "admin",
      createdAt: "2024-01-15",
      status: "active",
    },
  ]);

  let nextId = 3;

  function addAdmin(data: {
    name: string;
    email: string;
    password: string;
    role: string;
  }) {
    admins.value.push({
      id: nextId++,
      name: data.name,
      email: data.email,
      password: data.password,
      role: data.role,
      createdAt: new Date().toISOString().slice(0, 10),
      status: "active",
    });
  }

  function updateAdmin(
    id: number,
    data: {
      name: string;
      email: string;
      password?: string;
      role: string;
      status: string;
    },
  ) {
    const a = admins.value.find((a) => a.id === id);
    if (a) {
      a.name = data.name;
      a.email = data.email;
      if (data.password) a.password = data.password;
      a.role = data.role;
      a.status = data.status;
    }
  }

  function deleteAdmin(id: number) {
    admins.value = admins.value.filter((a) => a.id !== id);
  }

  function findByEmail(email: string) {
    return admins.value.find((a) => a.email === email);
  }

  return { admins, addAdmin, updateAdmin, deleteAdmin, findByEmail };
});
