import { createRouter, createWebHistory } from '@ionic/vue-router';
import type { RouteRecordRaw } from 'vue-router';

// ============================================
// COMPONENT IMPORTS
// ============================================
import LoginPage from '@/views/LoginPage.vue';
import SignupPage from '@/views/SignupPage.vue';
import SellerDashboard from '@/views/SellerLandingPage.vue';
import AddProductPage from '@/views/AddProductPage.vue';
import ProductsListPage from '@/views/ProductsListPage.vue';
import EditProductPage from '@/views/EditProductPage.vue';
import OTPVerificationPage from '@/views/OTPVerificationPage.vue';
import OrdersManagementPage from '@/views/OrdersManagementPage.vue';
import OrderDetailsPage from '@/views/OrderDetailsPage.vue';
import MessagesPage from '@/views/MessagesPage.vue';
import AnalyticsPage from '@/views/AnalyticsPage.vue';
import ProfilePage from '@/views/ProfilePage.vue';
import NotificationPage from '@/views/NotificationsPage.vue';
import HelpSupportPage from '@/views/HelpSupportPage.vue';
import AboutPage from '@/views/AboutPage.vue';

// ============================================
// ROUTE DEFINITIONS
// ============================================
const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage
  },
  {
    path: '/otp-verify/:phone?',
    name: 'OTPVerification',
    component: OTPVerificationPage,
    meta: { requiresAuth: false }
  },
  {
    path: '/signup',
    name: 'Signup',
    component: SignupPage
  },
  {
    path: '/home',
    name: 'Home',
    component: SellerDashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/products/add',
    name: 'AddProduct',
    component: AddProductPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/products',
    name: 'Products',
    component: ProductsListPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/products/edit/:id',
    name: 'EditProduct',
    component: EditProductPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/orders',
    name: 'Orders',
    component: OrdersManagementPage,  // ✅ FIXED: Changed from OrdersPage to OrdersManagementPage
    meta: { requiresAuth: true }
  },
  {
    path: '/orders/:id',
    name: 'OrderDetails',
    component: OrderDetailsPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/messages',
    name: 'Messages',
    component: MessagesPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/analytics',
    name: 'Analytics',
    component: AnalyticsPage,
    meta: { requiresAuth: true }
  },
  {
  path: '/notifications',
  name: 'Notifications',
  component: () => NotificationPage,
  meta: { requiresAuth: true }
 },
 {
  path: '/help',
  name: 'Help',
  component: () => HelpSupportPage,
  meta: { requiresAuth: true }
 },

 {
  path: '/about',
  name: 'About',
  component: () => AboutPage,
  meta: { requiresAuth: true }
}
 
];

// ============================================
// ROUTER INSTANCE
// ============================================
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

// ============================================
// AUTHENTICATION GUARD
// ============================================
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true';
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    // Not logged in, redirect to login
    next('/login');
  } else if (to.path === '/login' && isAuthenticated) {
    // Already logged in, redirect to home
    next('/home');
  } else {
    // All good, proceed
    next();
  }
});

export default router;