export const useRegistrationsStore = defineStore('registrations', () => {
  const pending = ref([
    {
      id: 'SELLER-A1B2C3',
      role: 'seller',
      storeName: "Maria's Fresh Produce",
      category: 'vegetables',
      address: '123 Rizal St., Brgy. Carmen, CDO City',
      contact: '09171234567',
      firstName: 'Maria',
      lastName: 'Santos',
      email: 'maria.santos@email.com',
      validIdPhoto: '',
      businessPermitPhoto: '',
      selfiePhoto: '',
      status: 'pending_review',
      submittedAt: '2026-05-15',
    },
    {
      id: 'SELLER-D4E5F6',
      role: 'seller',
      storeName: 'Fresh Meat Shop',
      category: 'meat',
      address: '456 Mabini St., Brgy. Cogon, CDO City',
      contact: '09189876543',
      firstName: 'Pedro',
      lastName: 'Reyes',
      email: 'pedro.reyes@email.com',
      validIdPhoto: '',
      businessPermitPhoto: '',
      selfiePhoto: '',
      status: 'pending_review',
      submittedAt: '2026-05-14',
    },
    {
      id: 'DRIVER-G7H8I9',
      role: 'driver',
      firstName: 'Juan',
      lastName: 'Dela Cruz',
      email: 'juan.dc@email.com',
      contact: '09151112233',
      serviceArea: 'cdo',
      vehicleType: 'motorcycle',
      plateNumber: 'XYZ 5678',
      vehicleModel: 'Honda Click 125',
      licensePhoto: '',
      validIdPhoto: '',
      vehicleRegPhoto: '',
      status: 'pending_review',
      submittedAt: '2026-05-13',
    },
    {
      id: 'DRIVER-J0K1L2',
      role: 'driver',
      firstName: 'Ana',
      lastName: 'Salvador',
      email: 'ana.salvador@email.com',
      contact: '09174445566',
      serviceArea: 'davao',
      vehicleType: 'tricycle',
      plateNumber: 'TRI 1234',
      vehicleModel: '',
      licensePhoto: '',
      validIdPhoto: '',
      vehicleRegPhoto: '',
      status: 'pending_review',
      submittedAt: '2026-05-12',
    },
    {
      id: 'SELLER-M3N4O5',
      role: 'seller',
      storeName: 'Ana\'s Bakeshop',
      category: 'mixed',
      address: '789 Lapasan, CDO City',
      contact: '09165557788',
      firstName: 'Ana',
      lastName: 'Garcia',
      email: 'ana.garcia@email.com',
      validIdPhoto: '',
      businessPermitPhoto: '',
      selfiePhoto: '',
      status: 'pending_review',
      submittedAt: '2026-05-11',
    },
  ])

  const history = ref([
    {
      id: 'SELLER-P6Q7R8',
      role: 'seller',
      storeName: 'Organic Greens',
      category: 'vegetables',
      firstName: 'Carlos',
      lastName: 'Mendoza',
      status: 'approved',
      reviewedAt: '2026-05-10',
      reviewedBy: 'Super Admin',
    },
    {
      id: 'DRIVER-S9T0U1',
      role: 'driver',
      firstName: 'Rico',
      lastName: 'Suarez',
      vehicleType: 'motorcycle',
      status: 'rejected',
      reviewedAt: '2026-05-09',
      reviewedBy: 'Super Admin',
      rejectReason: 'Blurry license photo. Please resubmit with a clearer image.',
    },
  ])

  function approve(id) {
    const reg = pending.value.find(r => r.id === id)
    if (reg) {
      history.value.unshift({
        id: reg.id,
        role: reg.role,
        storeName: reg.storeName,
        category: reg.category,
        firstName: reg.firstName,
        lastName: reg.lastName,
        vehicleType: reg.vehicleType,
        status: 'approved',
        reviewedAt: new Date().toISOString().slice(0, 10),
        reviewedBy: 'Super Admin',
      })
      pending.value = pending.value.filter(r => r.id !== id)
    }
  }

  function reject(id, reason) {
    const reg = pending.value.find(r => r.id === id)
    if (reg) {
      history.value.unshift({
        id: reg.id,
        role: reg.role,
        storeName: reg.storeName,
        category: reg.category,
        firstName: reg.firstName,
        lastName: reg.lastName,
        vehicleType: reg.vehicleType,
        status: 'rejected',
        reviewedAt: new Date().toISOString().slice(0, 10),
        reviewedBy: 'Super Admin',
        rejectReason: reason || 'Requirements not met.',
      })
      pending.value = pending.value.filter(r => r.id !== id)
    }
  }

  const analytics = computed(() => {
    return {
      pending: pending.value.length,
      approved: history.value.filter(r => r.status === 'approved').length,
      rejected: history.value.filter(r => r.status === 'rejected').length,
      total: pending.value.length + history.value.length,
    }
  })

  return { pending, history, approve, reject, analytics }
})
