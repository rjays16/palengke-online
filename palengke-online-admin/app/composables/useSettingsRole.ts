export function useSettingsRole() {
  const route = useRoute();
  const role = computed(() => {
    const r = route.query.role as string;
    if (r && ['driver', 'seller'].includes(r)) return r;
    return 'driver';
  });

  const isDriver = computed(() => role.value === 'driver');
  const isSeller = computed(() => role.value === 'seller');

  return { role, isDriver, isSeller };
}
