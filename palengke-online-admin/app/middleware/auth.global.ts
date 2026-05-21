export default defineNuxtRouteMiddleware(async (to) => {
  if (process.server) return;

  const auth = useAuthStore();

  // Ensure auth state is restored before checking
  if (!auth.user && auth.token) {
    await auth.restore();
  }

  if (to.path === "/login") {
    if (auth.isAuthenticated) {
      return navigateTo("/dashboard");
    }
    return;
  }

  if (!auth.isAuthenticated) {
    return navigateTo("/login");
  }
});
