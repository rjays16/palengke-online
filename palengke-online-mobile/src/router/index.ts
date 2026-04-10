import { createRouter, createWebHistory } from '@ionic/vue-router';
import type { RouteRecordRaw } from 'vue-router';

import LoginPage from '@/views/LoginPage.vue';
import SignupWelcome from '@/views/registration/SignupWelcome.vue';
import RegisterStep1 from '@/views/registration/steps/RegisterStep1.vue';
import RegisterStep2 from '@/views/registration/steps/RegisterStep2.vue';
import RegisterStep3 from '@/views/registration/steps/RegisterStep3.vue';
import RegisterStep4 from '@/views/registration/steps/RegisterStep4.vue';
import RegisterStep5 from '@/views/registration/steps/RegisterStep5.vue';
import RegisterStep6 from '@/views/registration/steps/RegisterStep6.vue';

// Main app pages (add your existing pages here)
import HomePage from '@/views/HomePage.vue';
import BrowseSellersPage from '@/views/BrowseSellersPage.vue';
import SellerStorePage from '@/views/SellerStorePage.vue';
import CartPage from '@/views/CartPage.vue';
import CheckoutPage from '@/views/CheckoutPage.vue';
import TipPage from '@/views/TipPage.vue';
import OrderTrackingPage from '@/views/OrderTrackingPage.vue';

const routes: Array<RouteRecordRaw> = [
  { path: '/', redirect: '/login' },

  // Auth
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { requiresAuth: false }
  },

  // Registration entry
  {
    path: '/signup',
    name: 'SignupWelcome',
    component: SignupWelcome,
    meta: { requiresAuth: false }
  },

  // Registration steps
  {
    path: '/register/step1',
    name: 'RegisterStep1',
    component: RegisterStep1,
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step2',
    name: 'RegisterStep2',
    component: RegisterStep2,
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step3',
    name: 'RegisterStep3',
    component: RegisterStep3,
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step4',
    name: 'RegisterStep4',
    component: RegisterStep4,
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step5',
    name: 'RegisterStep5',
    component: RegisterStep5,
    meta: { requiresAuth: false }
  },
  {
    path: '/register/step6',
    name: 'RegisterStep6',
    component: RegisterStep6,
    meta: { requiresAuth: false }
  },

  // App pages (authenticated)
  {
    path: '/home',
    name: 'Home',
    component: HomePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/browse',
    name: 'BrowseSellers',
    component: BrowseSellersPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/store/:id',
    name: 'SellerStore',
    component: SellerStorePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/cart',
    name: 'Cart',
    component: CartPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/checkout',
    name: 'Checkout',
    component: CheckoutPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/tip',
    name: 'Tip',
    component: TipPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/order-tracking/:id',
    name: 'OrderTracking',
    component: OrderTrackingPage,
    meta: { requiresAuth: true }
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

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