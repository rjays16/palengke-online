export default defineNuxtRouteMiddleware(async (to) => {
  if (process.server) return;

  const auth = useAuthStore();

  // Ensure auth state is restored before checking
  if (!auth.user && auth.token) {
    await auth.restore();
  }

  const publicPaths = ["/login", "/forgot-password", "/reset-password"];

  if (publicPaths.includes(to.path)) {
    if (auth.isAuthenticated && to.path === "/login") {
      return navigateTo("/dashboard");
    }
    return;
  }

  if (!auth.isAuthenticated) {
    return navigateTo("/login");
  }
});
