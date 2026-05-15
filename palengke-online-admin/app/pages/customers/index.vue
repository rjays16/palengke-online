<template>
  <div class="page-shell">
    <div class="page-toolbar">
      <div class="toolbar-left">
        <div class="search-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input v-model="search" placeholder="Search customers..." />
        </div>
      </div>
      <span class="result-count">{{ filteredCustomers.length }} customers</span>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr><th>Customer</th><th>Email</th><th>Phone</th><th>Orders</th><th>Spent</th><th>Joined</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="c in paginatedCustomers" :key="c.id">
            <td>
              <div class="cell-customer">
                <div class="customer-avatar">{{ c.name.charAt(0) }}{{ c.name.split(' ')[1]?.charAt(0) }}</div>
                <div><div class="name-cell">{{ c.name }}</div></div>
              </div>
            </td>
            <td>{{ c.email }}</td>
            <td>{{ c.phone }}</td>
            <td>{{ c.orders }}</td>
            <td>₱{{ c.spent.toLocaleString() }}</td>
            <td class="date-cell">{{ c.joined }}</td>
            <td><button class="action-btn" @click="goToCustomer(c.id)">...</button></td>
          </tr>
        </tbody>
      </table>
      <PaginationBar v-model="customerPage" :total="filteredCustomers.length" :perPage="customerPerPage" />
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
const customersStore = useCustomersStore()

const search = ref('')

const filteredCustomers = computed(() => {
  if (!search.value) return customersStore.customers
  const q = search.value.toLowerCase()
  return customersStore.customers.filter(c => c.name.toLowerCase().includes(q) || c.email.toLowerCase().includes(q))
})

const customerPage = ref(1)
const customerPerPage = ref(10)
const paginatedCustomers = computed(() => {
  const start = (customerPage.value - 1) * customerPerPage.value
  return filteredCustomers.value.slice(start, start + customerPerPage.value)
})
watch(search, () => { customerPage.value = 1 })

const goToCustomer = (id) => navigateTo('/customers/' + id)
</script>

<style scoped>
.page-shell { display: flex; flex-direction: column; gap: 20px; }
.page-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.search-box { display: flex; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; min-width: 260px; }
.search-box:focus-within { border-color: #059669; }
.search-box svg { width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0; }
.search-box input { border: none; background: transparent; font-size: 13px; font-family: 'Inter', sans-serif; color: #111827; outline: none; width: 100%; }
.result-count { font-size: 13px; color: #9ca3af; }
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
.data-table td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f9fafb; }
.cell-customer { display: flex; align-items: center; gap: 10px; }
.customer-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #7c3aed); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: white; flex-shrink: 0; }
.name-cell { font-weight: 600; color: #111827; }
.date-cell { color: #9ca3af; }
.action-btn { background: none; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 10px; cursor: pointer; color: #9ca3af; font-size: 16px; transition: all 0.15s; }
.action-btn:hover { border-color: #059669; color: #059669; }

@media (max-width: 768px) {
  .data-table { min-width: 600px; }
  .search-box { min-width: 160px; }
}
@media (max-width: 480px) {
  .page-toolbar { flex-direction: column; align-items: stretch; }
  .search-box { min-width: 0; width: 100%; }
  .data-table { min-width: 550px; }
}
</style>
