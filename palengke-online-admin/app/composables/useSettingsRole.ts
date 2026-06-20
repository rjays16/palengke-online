export function useSettingsRole() {
  const route = useRoute();
  const role = computed(() => {
    const r = route.query.role as string;
    if (r && ['driver', 'seller'].includes(r)) return r;
    return 'all';
  });

  const isDriver = computed(() => role.value === 'driver' || role.value === 'all');
  const isSeller = computed(() => role.value === 'seller' || role.value === 'all');
  const isAll = computed(() => role.value === 'all');

  return { role, isDriver, isSeller, isAll };
}
