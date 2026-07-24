import { createRouter, createWebHistory } from '@ionic/vue-router';
import type { RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { getSubscription, isExpired } from '@/services/subscriptionService';

// ============================================
// COMPONENT IMPORTS
// ============================================
import LoginPage from '@/views/LoginPage.vue';
import AccountPendingPage from '@/views/AccountPendingPage.vue';
import SignupPage from '@/views/SignupPage.vue';
import SellerDashboard from '@/views/SellerLandingPage.vue';
import AddProductPage from '@/views/AddProductPage.vue';
import ProductsListPage from '@/views/ProductsListPage.vue';
import EditProductPage from '@/views/EditProductPage.vue';
import OTPVerificationPage from '@/views/OTPVerificationPage.vue';
import PinLoginPage from '@/views/PinLoginPage.vue';
import CreatePinPage from '@/views/CreatePinPage.vue';
import OrdersManagementPage from '@/views/OrdersManagementPage.vue';
import OrderDetailsPage from '@/views/OrderDetailsPage.vue';
import MessagesPage from '@/views/MessagesPage.vue';
import AnalyticsPage from '@/views/AnalyticsPage.vue';
import ProfilePage from '@/views/ProfilePage.vue';
import NotificationPage from '@/views/NotificationsPage.vue';
import HelpSupportPage from '@/views/HelpSupportPage.vue';
import DevicesPage from '@/views/DevicesPage.vue';
import AboutPage from '@/views/AboutPage.vue';
import PlansPage from '@/views/PlansPage.vue';
import AreaNotAvailablePage from '@/views/AreaNotAvailablePage.vue';

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
    redirect: '/login', // Login is default (first page)
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { requiresAuth: false },
  },
  {
    path: '/account-pending',
    name: 'AccountPending',
    component: AccountPendingPage,
    meta: { requiresAuth: false },
  },
  {
    path: '/area-not-available/:reason',
    name: 'AreaNotAvailable',
    component: AreaNotAvailablePage,
    meta: { requiresAuth: false },
  },
  {
    path: '/otp-verify/:email?',
    name: 'OTPVerification',
    component: OTPVerificationPage,
    meta: { requiresAuth: false },
  },
  {
    path: '/pin-login',
    name: 'PinLogin',
    component: PinLoginPage,
    meta: { requiresAuth: false },
  },
  {
    path: '/create-pin',
    name: 'CreatePin',
    component: CreatePinPage,
    meta: { requiresAuth: false },
  },

  // ============================================
  // WELCOME & REGISTRATION FLOW
  // ============================================
  {
    path: '/welcome',
    name: 'Welcome',
    component: WelcomePage,
    meta: { requiresAuth: false },
  },
  {
    path: '/register/step1',
    name: 'RegisterStep1',
    component: RegisterStep1,
    meta: { requiresAuth: false },
  },
  {
    path: '/register/step2',
    name: 'RegisterStep2',
    component: RegisterStep2,
    meta: { requiresAuth: false },
  },
  {
    path: '/register/step3',
    name: 'RegisterStep3',
    component: RegisterStep3,
    meta: { requiresAuth: false },
  },
  {
    path: '/register/step4',
    name: 'RegisterStep4',
    component: RegisterStep4,
    meta: { requiresAuth: false },
  },
  {
    path: '/register/step5',
    name: 'RegisterStep5',
    component: RegisterStep5,
    meta: { requiresAuth: false },
  },
  {
    path: '/register/step6',
    name: 'RegisterStep6',
    component: RegisterStep6,
    meta: { requiresAuth: false },
  },
  {
    path: '/register/step7',
    name: 'RegisterStep7',
    component: RegisterStep7,
    meta: { requiresAuth: false },
  },
  {
    path: '/register/success',
    name: 'RegisterSuccess',
    component: RegisterSuccess,
    meta: { requiresAuth: false },
  },

  // ============================================
  // OLD SIGNUP (Can remove this later if not needed)
  // ============================================
  {
    path: '/signup',
    name: 'Signup',
    component: SignupPage,
    meta: { requiresAuth: false },
  },

  // ============================================
  // SELLER DASHBOARD & FEATURES (Authenticated)
  // ============================================
  {
    path: '/home',
    name: 'Home',
    component: SellerDashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/add',
    name: 'AddProduct',
    component: AddProductPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/products',
    name: 'Products',
    component: ProductsListPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/edit/:id',
    name: 'EditProduct',
    component: EditProductPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/orders',
    name: 'Orders',
    component: OrdersManagementPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/orders/:id',
    name: 'OrderDetails',
    component: OrderDetailsPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/messages',
    name: 'Messages',
    component: MessagesPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage,
    meta: { requiresAuth: true },
  },
  {
    path: '/analytics',
    name: 'Analytics',
    component: AnalyticsPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/notifications',
    name: 'Notifications',
    component: NotificationPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/help',
    name: 'Help',
    component: HelpSupportPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/devices',
    name: 'Devices',
    component: DevicesPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/about',
    name: 'About',
    component: AboutPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/track-driver/:orderId',
    name: 'TrackDriver',
    component: () => import('@/views/DriverTrackingPage.vue'),
  },
  {
    path: '/plans',
    name: 'Plans',
    component: PlansPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/wallet',
    name: 'WalletTopUp',
    component: () => import('@/views/WalletTopUpPage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/wallet/transactions',
    name: 'WalletTransactions',
    component: () => import('@/views/WalletTransactionsPage.vue'),
    meta: { requiresAuth: true },
  },
];

// ============================================
// ROUTER INSTANCE
// ============================================
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// ============================================
// AUTHENTICATION GUARD
// ============================================
router.beforeEach((to, from, next) => {
  const auth = useAuthStore();
  auth.initFromStorage();
  const isAuthenticated = auth.isAuthenticated;
  const hasPin = auth.hasPin;
  const userEmail = auth.userEmail;

  if (to.meta.requiresAuth && !isAuthenticated) {
    if (hasPin && userEmail) {
      next('/pin-login');
    } else {
      next('/login');
    }
  } else if (to.path === '/login' && hasPin && userEmail) {
    next('/pin-login');
  } else if (to.path === '/login' && isAuthenticated) {
    next('/home');
  } else if (to.meta.requiresAuth && isAuthenticated && to.path !== '/plans' && to.path !== '/wallet' && to.path !== '/messages') {
    const sub = getSubscription();
    if (isExpired(sub)) {
      next('/plans');
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
