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
// REGISTRATION FLOW IMPORTS
// ============================================
import WelcomePage from '@/views/registration/WelcomePage.vue';
import RegisterStep1 from '@/views/registration/RegisterStep1.vue';
import RegisterStep2 from '@/views/registration/RegisterStep2.vue';
import RegisterStep3 from '@/views/registration/RegisterStep3.vue';
import RegisterStep4 from '@/views/registration/RegisterStep4.vue';
import RegisterStep5 from '@/views/registration/RegisterStep5.vue';
import RegisterStep6 from '@/views/registration/RegisterStep6.vue';
import RegisterStep7 from '@/views/registration/RegisterStep7.vue';
import RegisterSuccess from '@/views/registration/RegisterSuccess.vue';

// ============================================
// ROUTE DEFINITIONS
// ============================================
const routes: Array<RouteRecordRaw> = [
  // ============================================
  // AUTHENTICATION ROUTES
  // ============================================
  {
    path: '/',
    redirect: '/login'  // Login is default (first page)
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { requiresAuth: false }
  },
  {
    path: '/otp-verify/:email?',
    name: 'OTPVerification',
    component: OTPVerificationPage,
    meta: { requiresAuth: false }
  },

  // ============================================
  // WELCOME & REGISTRATION FLOW
  // ============================================
  {
    path: '/welcome',
    name: 'Welcome',
    component: WelcomePage,
    meta: { requiresAuth: false }
  },
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
  {
    path: '/register/step7',
    name: 'RegisterStep7',
    component: RegisterStep7,
    meta: { requiresAuth: false }
  },
  {
    path: '/register/success',
    name: 'RegisterSuccess',
    component: RegisterSuccess,
    meta: { requiresAuth: false }
  },

  // ============================================
  // OLD SIGNUP (Can remove this later if not needed)
  // ============================================
  {
    path: '/signup',
    name: 'Signup',
    component: SignupPage,
    meta: { requiresAuth: false }
  },

  // ============================================
  // SELLER DASHBOARD & FEATURES (Authenticated)
  // ============================================
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
    component: OrdersManagementPage,
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
    component: NotificationPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/help',
    name: 'Help',
    component: HelpSupportPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/about',
    name: 'About',
    component: AboutPage,
    meta: { requiresAuth: true }
  },
  {
  path: '/track-driver/:orderId',
  name: 'TrackDriver',
  component: () => import('@/views/DriverTrackingPage.vue')
  },
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