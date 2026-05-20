<template>
  <div class="settings-subnav">
    <NuxtLink
      v-for="item in items"
      :key="item.to"
      :to="item.to"
      class="subnav-item"
      active-class="active"
      :exact-active-class="item.exact ? 'active' : undefined"
    >
      {{ item.label }}
    </NuxtLink>
  </div>
</template>

<script setup lang="ts">
interface NavItem {
  to: string;
  label: string;
  exact?: boolean;
}

const items: NavItem[] = [
  { to: "/settings", label: "General", exact: true },
  { to: "/settings/fees", label: "Delivery Fees" },
  { to: "/settings/commissions", label: "Commissions" },
  { to: "/settings/payments", label: "Payments" },
  { to: "/settings/locations", label: "Locations", exact: true },
];
</script>

<style scoped>
.settings-subnav {
  display: flex;
  gap: 4px;
  background: #fff;
  border-radius: 12px;
  padding: 6px;
  border: 1px solid #e5e7eb;
  overflow-x: auto;
}
.subnav-item {
  padding: 9px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.15s;
  white-space: nowrap;
}
.subnav-item:hover {
  color: #374151;
  background: #f9fafb;
}
.subnav-item.active {
  background: #f0fdf4;
  color: #059669;
  font-weight: 600;
}
</style>
