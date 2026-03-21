import { createRouter, createWebHistory } from '@ionic/vue-router';

const routes = [
  // ─── Default ────────────────────────────────────────
  {
    path: '/',
    redirect: '/login'
  },

  // ─── Auth Flow ──────────────────────────────────────
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/LoginPage.vue'),
    meta: { requiresAuth: false }
  },

  // ─── Driver Requirements Check ───────────────────────
  {
    path: '/signup',
    name: 'DriverRequirements',
    component: () => import('@/views/registration/DriverRequirements.vue'),
    meta: { requiresAuth: false }
  },

  // ─── Registration Flow ───────────────────────────────
  {
    path: '/register/step1',
    name: 'RegisterStep1',
    component: () => import('@/views/registration/steps/RegisterStep1.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step2',
    name: 'RegisterStep2',
    component: () => import('@/views/registration/steps/RegisterStep2.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/register/success',
    name: 'RegisterSuccess',
    component: () => import('@/views/registration/RegisterSuccess.vue'),
    meta: { requiresAuth: false }
  },

  // ─── Main App ────────────────────────────────────────
  {
    path: '/home',
    name: 'Home',
    component: () => import('@/views/HomePage.vue'),
    meta: { requiresAuth: true }
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

// ─── Auth Guard ──────────────────────────────────────
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true';

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
  } else if (to.path === '/login' && isAuthenticated) {
    next('/home');
  } else {
    next();
  }
});

export default router;