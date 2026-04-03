import { createRouter, createWebHistory } from '@ionic/vue-router';

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: () => import('@/views/LoginPage.vue') },
  { path: '/signup', component: () => import('@/views/SignupPage.vue') },
  // Phase 2
  { path: '/home', component: () => import('@/views/HomePage.vue') },
  { path: '/browse', component: () => import('@/views/BrowseSellersPage.vue') },
  // Phase 3
  { path: '/seller/:id', component: () => import('@/views/SellerStorePage.vue') },
  { path: '/product/:id', component: () => import('@/views/ProductDetailPage.vue') },
  // Phase 4
  { path: '/cart', component: () => import('@/views/CartPage.vue') },
  { path: '/checkout/:id', component: () => import('@/views/CheckoutPage.vue') },
  { path: '/offers', component: () => import('@/views/OffersPage.vue') },
  { path: '/payment-methods', component: () => import('@/views/PaymentMethodsPage.vue') },
  // Phase 5
  { path: '/tip', component: () => import('@/views/TipPage.vue') },
  { path: '/order-tracking/:id', component: () => import('@/views/OrderTrackingPage.vue') },
  { path: '/order-rating/:id', component: () => import('@/views/OrderRatingPage.vue') },
  // Phase 6 (uncomment when built)
  // { path: '/activity', component: () => import('@/views/ActivityPage.vue') },
  // { path: '/order-history', component: () => import('@/views/OrderHistoryPage.vue') },
  // { path: '/profile', component: () => import('@/views/ProfilePage.vue') },
  // { path: '/addresses', component: () => import('@/views/AddressesPage.vue') },
  // { path: '/messages', component: () => import('@/views/MessagesPage.vue') },
  // { path: '/notifications', component: () => import('@/views/NotificationsPage.vue') },
  // { path: '/help', component: () => import('@/views/HelpSupportPage.vue') },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

router.beforeEach((to, from, next) => {
  const publicPaths = ['/login', '/signup'];
  const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true';
  if (!publicPaths.includes(to.path) && !isAuthenticated) { next('/login'); }
  else if (to.path === '/login' && isAuthenticated) { next('/home'); }
  else { next(); }
});

export default router;