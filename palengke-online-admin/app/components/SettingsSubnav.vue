<template>
  <div class="settings-nav">
    <div class="role-tabs">
      <button
        v-for="role in roles"
        :key="role.key"
        class="role-tab"
        :class="{ active: activeRole === role.key }"
        @click="setRole(role.key)"
      >
        <span class="role-icon" v-html="role.icon"></span>
        {{ role.label }}
      </button>
    </div>
    <div class="settings-subnav">
      <NuxtLink
        v-for="item in filteredItems"
        :key="item.to"
        :to="item.to + `?role=${activeRole}`"
        class="subnav-item"
        active-class="active"
        :exact-active-class="item.exact ? 'active' : undefined"
      >
        {{ item.label }}
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
interface NavItem {
  to: string;
  label: string;
  exact?: boolean;
  roles: string[];
}

const route = useRoute();
const router = useRouter();

const activeRole = ref<string>('driver');

const roles = [
  {
    key: 'driver',
    label: 'Driver',
    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="16" r="3"/><circle cx="18" cy="16" r="3"/><path d="M9 16h9"/><path d="M4 9h5l2 4"/><path d="M18 5l-3 2v9"/><rect x="13" y="6" width="5" height="4" rx="1"/></svg>',
  },
  {
    key: 'seller',
    label: 'Seller',
    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
  },
];

const allItems: NavItem[] = [
  { to: "/settings/fees", label: "Delivery Fees", roles: ['driver', 'seller'] },
  { to: "/settings/commissions", label: "Commissions", roles: ['seller', 'driver'] },
  { to: "/settings/payments", label: "Payments", roles: ['seller'] },
  { to: "/settings/calling", label: "Calling", roles: ['driver', 'seller'] },
  { to: "/settings/locations", label: "Locations", exact: true, roles: ['driver', 'seller'] },
  { to: "/settings/countries", label: "Countries", roles: ['driver', 'seller'] },
  { to: "/settings/restriction-codes", label: "Restriction Codes", roles: ['driver'] },
  { to: "/settings/contact-support", label: "Contact Support", roles: ['driver', 'seller'] },
  { to: "/settings/help-topics", label: "Help Topics", roles: ['driver', 'seller'] },
  { to: "/settings/faqs", label: "FAQs", roles: ['driver', 'seller'] },
];

const filteredItems = computed(() =>
  allItems.filter(item => item.roles.includes(activeRole.value))
);

function setRole(role: string) {
  activeRole.value = role;
  router.replace({ query: { role } });
}

onMounted(() => {
  const role = route.query.role as string;
  if (role && ['driver', 'seller'].includes(role)) {
    activeRole.value = role;
  }
});
</script>

<style scoped>
.settings-nav {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.role-tabs {
  display: flex;
  gap: 6px;
  background: #fff;
  border-radius: 12px;
  padding: 6px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.role-tab {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  background: none;
  border: none;
  cursor: pointer;
  font-family: "Inter", sans-serif;
  transition: all 0.2s;
}
.role-tab:hover {
  color: #374151;
  background: #f9fafb;
}
.role-tab.active {
  background: #059669;
  color: #fff;
  box-shadow: 0 1px 3px rgba(5, 150, 105, 0.2);
}
.role-icon {
  display: inline-flex;
  align-items: center;
  width: 18px;
  height: 18px;
}
.role-icon :deep(svg) {
  width: 100%;
  height: 100%;
}
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
