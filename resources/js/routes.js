import { useAuthStore } from '@/stores/auth';

// Import components
import Login from '@/Pages/Auth/Login.vue';
import Register from '@/Pages/Auth/Register.vue';
import ForgotPassword from '@/Pages/Auth/ForgotPassword.vue';
import ResetPassword from '@/Pages/Auth/ResetPassword.vue';
import Dashboard from '@/Pages/Dashboard.vue';
import Profile from '@/Pages/Profile.vue';
import WeatherDashboard from '@/Pages/Weather/Dashboard.vue';

// Onboarding
const FarmOnboarding = () => import('@/Pages/Onboarding/FarmProfile.vue');

// Farm Management
import FieldsList from '@/Pages/Farm/Field/Index.vue';
// import FieldDetail from '@/Pages/Farm/Field/Show.vue';
// import PlantingsList from '@/Pages/Farm/Plantings/Index.vue';
// import PlantingDetail from '@/Pages/Farm/Plantings/Show.vue';
// import TasksList from '@/Pages/Farm/Tasks/Index.vue';
// import TaskDetail from '@/Pages/Farm/Tasks/Show.vue';

// Inventory Management
import InventoryList from '@/Pages/Inventory/InventoryItems/Index.vue';
// import InventoryDetail from '@/Pages/Inventory/Show.vue';

// Weather
// import FieldWeather from '@/Pages/Weather/FieldWeather.vue';

// Marketplace
import Marketplace from '@/Pages/Marketplace/Index.vue';
import ProductDetail from '@/Pages/Marketplace/ProductDetail.vue';
import Cart from '@/Pages/Marketplace/Cart.vue';
// import OrdersList from '@/Pages/Marketplace/Orders/Index.vue';
// import OrderDetail from '@/Pages/Marketplace/Orders/Show.vue';

// Admin
// import AdminDashboard from '@/Pages/Admin/Dashboard.vue';
// import UsersList from '@/Pages/Admin/Users/Index.vue';
// import SystemStats from '@/Pages/Admin/SystemStats.vue';

// Reports
// import FinancialReports from '@/Pages/Reports/Financial.vue';
// import CropYieldReports from '@/Pages/Reports/CropYield.vue';
// import WeatherReports from '@/Pages/Reports/Weather.vue';

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: ForgotPassword,
    meta: { requiresGuest: true }
  },
  {
    path: '/reset-password',
    name: 'reset-password',
    component: ResetPassword,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/onboarding',
    name: 'onboarding',
    component: FarmOnboarding,
    meta: { requiresAuth: true, roles: ['farmer'] }
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: { requiresAuth: true }
  },
  
  // Farm Management Routes
  {
    path: '/fields',
    name: 'fields',
    component: FieldsList,
    meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  },
  // {
  //   path: '/fields/:id',
  //   name: 'field-detail',
  //   component: FieldDetail,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
  // {
  //   path: '/plantings',
  //   name: 'plantings',
  //   component: PlantingsList,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
  // {
  //   path: '/plantings/:id',
  //   name: 'planting-detail',
  //   component: PlantingDetail,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
  // {
  //   path: '/tasks',
  //   name: 'tasks',
  //   component: TasksList,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
  // {
  //   path: '/tasks/:id',
  //   name: 'task-detail',
  //   component: TaskDetail,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
  
  // Inventory Routes
  {
    path: '/inventory',
    name: 'inventory',
    component: InventoryList,
    meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  },
  // {
  //   path: '/inventory/:id',
  //   name: 'inventory-detail',
  //   component: InventoryDetail,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
  
  // Weather Routes
  {
    path: '/weather',
    name: 'weather',
    component: WeatherDashboard,
    meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  },
  // {
  //   path: '/weather/fields/:id',
  //   name: 'field-weather',
  //   component: FieldWeather,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
  
  // Marketplace Routes
  {
    path: '/marketplace',
    name: 'marketplace',
    component: Marketplace,
    meta: { requiresAuth: false }
  },
  {
    path: '/marketplace/products/:id',
    name: 'product-detail',
    component: ProductDetail,
    meta: { requiresAuth: true }
  },
  {
    path: '/cart',
    name: 'cart',
    component: Cart,
    meta: { requiresAuth: true, roles: ['buyer'] }
  },
  // {
  //   path: '/orders',
  //   name: 'orders',
  //   component: OrdersList,
  //   meta: { requiresAuth: true }
  // },
  // {
  //   path: '/orders/:id',
  //   name: 'order-detail',
  //   component: OrderDetail,
  //   meta: { requiresAuth: true }
  // },
  
  // Admin Routes
  // {
  //   path: '/admin',
  //   name: 'admin',
  //   component: AdminDashboard,
  //   meta: { requiresAuth: true, roles: ['admin'] }
  // },
  // {
  //   path: '/admin/users',
  //   name: 'admin-users',
  //   component: UsersList,
  //   meta: { requiresAuth: true, roles: ['admin'] }
  // },
  // {
  //   path: '/admin/stats',
  //   name: 'admin-stats',
  //   component: SystemStats,
  //   meta: { requiresAuth: true, roles: ['admin'] }
  // },
  
  // Reports Routes
  // {
  //   path: '/reports/financial',
  //   name: 'financial-reports',
  //   component: FinancialReports,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
  // {
  //   path: '/reports/crop-yield',
  //   name: 'crop-yield-reports',
  //   component: CropYieldReports,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
  // {
  //   path: '/reports/weather',
  //   name: 'weather-reports',
  //   component: WeatherReports,
  //   meta: { requiresAuth: true, roles: ['farmer', 'admin'] }
  // },
];

export default routes;