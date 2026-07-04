import { createRouter, createWebHistory } from '@ionic/vue-router';
import type { RouteRecordRaw } from 'vue-router';

// ============================================
// COMPONENT IMPORTS
// ============================================
import LoginPage from '@/views/LoginPage.vue';
import PinLoginPage from '@/views/PinLoginPage.vue';
import CreatePinPage from '@/views/CreatePinPage.vue';
import OTPVerificationPage from '@/views/OTPVerificationPage.vue';
import AccountPendingPage from '@/views/AccountPendingPage.vue';
import AreaNotAvailablePage from '@/views/AreaNotAvailablePage.vue';
import DriverRequirements from '@/views/registration/DriverRequirements.vue';
import RegisterSuccess from '@/views/registration/RegisterSuccess.vue';
import HomePage from '@/views/HomePage.vue';
import AvailableOrdersPage from '@/views/AvailableOrdersPage.vue';
import ActiveDeliveryPage from '@/views/ActiveDeliveryPage.vue';
import EarningsPage from '@/views/EarningsPage.vue';
import HistoryPage from '@/views/HistoryPage.vue';
import TipReceivedPage from '@/views/TipReceivedPage.vue';
import ProfilePage from '@/views/ProfilePage.vue';
import LicencePaymentPage from '@/views/LicencePaymentPage.vue';
import DocumentsPage from '@/views/DocumentsPage.vue';
import SettingsPage from '@/views/SettingsPage.vue';
import ServiceTypePage from '@/views/ServiceTypePage.vue';
import MyDestinationPage from '@/views/MyDestinationPage.vue';
import MessagesPage from '@/views/MessagesPage.vue';
import NotificationsPage from '@/views/NotificationsPage.vue';
import HelpSupportPage from '@/views/HelpSupportPage.vue';
import AboutPage from '@/views/AboutPage.vue';
import OrderDetailsPage from '@/views/OrderDetailsPage.vue';

// ============================================
// REGISTRATION STEP IMPORTS
// ============================================
import RegisterStep1 from '@/views/registration/steps/RegisterStep1.vue';
import RegisterStep2 from '@/views/registration/steps/RegisterStep2.vue';
import RegisterStep3 from '@/views/registration/steps/RegisterStep3.vue';
import RegisterStep4 from '@/views/registration/steps/RegisterStep4.vue';
import RegisterStep5 from '@/views/registration/steps/RegisterStep5.vue';
import RegisterStep6 from '@/views/registration/steps/RegisterStep6.vue';
import RegisterStep7 from '@/views/registration/steps/RegisterStep7.vue';

// ============================================
// ROUTE DEFINITIONS
// ============================================
const routes: Array<RouteRecordRaw> = [
  // ============================================
  // DEFAULT ROUTE
  // ============================================
  {
    path: '/',
    redirect: '/login'
  },

  // ============================================
  // AUTHENTICATION ROUTES
  // ============================================
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { requiresAuth: false }
  },
  {
    path: '/pin-login',
    name: 'PinLogin',
    component: PinLoginPage,
    meta: { requiresAuth: false }
  },
  {
    path: '/create-pin',
    name: 'CreatePin',
    component: CreatePinPage,
    meta: { requiresAuth: false }
  },
  {
    path: '/otp-verification/:email',
    name: 'OTPVerification',
    component: OTPVerificationPage,
    meta: { requiresAuth: false }
  },
  {
    path: '/account-pending',
    name: 'AccountPending',
    component: AccountPendingPage,
    meta: { requiresAuth: false }
  },
  {
    path: '/area-not-available/:reason',
    name: 'AreaNotAvailable',
    component: AreaNotAvailablePage,
    meta: { requiresAuth: false }
  },
  {
    path: '/signup',
    name: 'DriverRequirements',
    component: DriverRequirements,
    meta: { requiresAuth: false }
  },

  // ============================================
  // REGISTRATION FLOW
  // ============================================
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
  // CORE DRIVER PAGES (Authenticated)
  // ============================================
  {
    path: '/home',
    name: 'Home',
    component: HomePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/orders',
    name: 'Orders',
    component: AvailableOrdersPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/active-delivery',
    name: 'ActiveDelivery',
    component: ActiveDeliveryPage,
    meta: { requiresAuth: true }
  },

  // ============================================
  // EARNINGS & HISTORY (Authenticated)
  // ============================================
  {
    path: '/earnings',
    name: 'Earnings',
    component: EarningsPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/history',
    name: 'History',
    component: HistoryPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/history/:job',
    name: 'OrderDetails',
    component: OrderDetailsPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/tip-received',
    name: 'TipReceived',
    component: TipReceivedPage,
    meta: { requiresAuth: true }
  },

  // ============================================
  // PROFILE & SETTINGS (Authenticated)
  // ============================================
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/licence-payment',
    name: 'LicencePayment',
    component: LicencePaymentPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/documents',
    name: 'Documents',
    component: DocumentsPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: SettingsPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/service-type',
    name: 'ServiceType',
    component: ServiceTypePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/my-destination',
    name: 'MyDestination',
    component: MyDestinationPage,
    meta: { requiresAuth: true }
  },

  // ============================================
  // MESSAGES & SUPPORT (Authenticated)
  // ============================================
  {
    path: '/messages',
    name: 'Messages',
    component: MessagesPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/notifications',
    name: 'Notifications',
    component: NotificationsPage,
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
    next('/login');
  } else if (to.path === '/login' && isAuthenticated) {
    next('/home');
  } else {
    next();
  }
});

export default router;
