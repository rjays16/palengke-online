<template>
  <div></div>
</template>

<script setup>
const auth = useAuthStore();
if (auth.isAuthenticated) {
  await navigateTo("/dashboard");
} else {
  await navigateTo("/login");
}
</script>
