import { createRouter, createWebHistory } from '@ionic/vue-router';

const routes = [
  // ─── Default ────────────────────────────────────────
  {
    path: '/',
    redirect: '/login'
  },

  // ─── Auth Flow ───────────────────────────────────────
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/LoginPage.vue'),
    meta: { requiresAuth: false }
  },

  // ─── Driver Requirements / Signup ────────────────────
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
    path: '/register/step3',
    name: 'RegisterStep3',
    component: () => import('@/views/registration/steps/RegisterStep3.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step4',
    name: 'RegisterStep4',
    component: () => import('@/views/registration/steps/RegisterStep4.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step5',
    name: 'RegisterStep5',
    component: () => import('@/views/registration/steps/RegisterStep5.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step6',
    name: 'RegisterStep6',
    component: () => import('@/views/registration/steps/RegisterStep6.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step7',
    name: 'RegisterStep7',
    component: () => import('@/views/registration/steps/RegisterStep7.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/register/success',
    name: 'RegisterSuccess',
    component: () => import('@/views/registration/RegisterSuccess.vue'),
    meta: { requiresAuth: false }
  },

  // ─── Phase 2 - Core Driver Pages (DONE) ──────────────
  {
    path: '/home',
    name: 'Home',
    component: () => import('@/views/HomePage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/orders',
    name: 'Orders',
    component: () => import('@/views/AvailableOrdersPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/active-delivery',
    name: 'ActiveDelivery',
    component: () => import('@/views/ActiveDeliveryPage.vue'),
    meta: { requiresAuth: true }
  },

  // ─── Phase 3 - Earnings & History (uncomment when built)
  {
    path: '/earnings',
    name: 'Earnings',
    component: () => import('@/views/EarningsPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/history',
    name: 'History',
    component: () => import('@/views/HistoryPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/tip-received',
    name: 'TipReceived',
    component: () => import('@/views/TipReceivedPage.vue'),
    meta: { requiresAuth: true }
  },

  // ─── Phase 4 - Profile & Settings (uncomment when built)
  // {
  //   path: '/profile',
  //   name: 'Profile',
  //   component: () => import('@/views/ProfilePage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/documents',
  //   name: 'Documents',
  //   component: () => import('@/views/DocumentsPage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/settings',
  //   name: 'Settings',
  //   component: () => import('@/views/SettingsPage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/service-type',
  //   name: 'ServiceType',
  //   component: () => import('@/views/ServiceTypePage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/my-destination',
  //   name: 'MyDestination',
  //   component: () => import('@/views/MyDestinationPage.vue'),
  //   meta: { requiresAuth: true }
  // },

  // ─── Phase 5 - Messages & Support (uncomment when built)
  // {
  //   path: '/messages',
  //   name: 'Messages',
  //   component: () => import('@/views/MessagesPage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/notifications',
  //   name: 'Notifications',
  //   component: () => import('@/views/NotificationsPage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/help',
  //   name: 'Help',
  //   component: () => import('@/views/HelpSupportPage.vue'),
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/about',
  //   name: 'About',
  //   component: () => import('@/views/AboutPage.vue'),
  //   meta: { requiresAuth: true }
  // },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

// ─── Auth Guard ──────────────────────────────────────
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true';

  if (to.meta.requiresAuth && !isAuthenticated) {
    // Not logged in → redirect to login
    next('/login');
  } else if (to.path === '/login' && isAuthenticated) {
    // Already logged in → redirect to home
    next('/home');
  } else {
    next();
  }
});

export default router;