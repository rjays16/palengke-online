<template>
  <div class="page-shell">
    <SettingsSubnav />
  </div>
</template>

<script setup>
definePageMeta({
  layout: "default",
});

onMounted(() => {
  navigateTo("/settings/fees?role=driver", { replace: true });
});
</script>

<style scoped>
.page-shell {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
</style>
