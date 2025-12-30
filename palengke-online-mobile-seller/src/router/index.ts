import { createRouter, createWebHistory } from '@ionic/vue-router';

const routes = [
  {
    path: '/',
    redirect: '/login'  // Redirect to login instead of landing page
  },
  {
    path: '/login',
    component: () => import('@/views/LoginPage.vue')  // Use SellerLoginPage
  },
  {
    path: '/signup',
    component: () => import('@/views/SignupPage.vue')  // Use SellerSignupPage
  },
  {
  path: '/home',
  component: () => import('@/views/SellerLandingPage.vue'),  // Use this instead
  meta: { requiresAuth: true }
  },
  {
    path: '/products/add',
    component: () => import('@/views/AddProductPage.vue'),
    meta: { requiresAuth: true }
  },
  // {
  //   path: '/products',
  //   component: () => import('@/views/ProductsListPage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/orders',
  //   component: () => import('@/views/OrdersPage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/profile',
  //   component: () => import('@/views/ProfilePage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/analytics',
  //   component: () => import('@/views/AnalyticsPage.vue'),
  //   meta: { requiresAuth: true }
  // }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

// Optional: Add authentication guard
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true';
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    // Redirect to login if not authenticated
    next('/login');
  } else if (to.path === '/login' && isAuthenticated) {
    // Redirect to home if already logged in
    next('/home');
  } else {
    next();
  }
});

export default router;