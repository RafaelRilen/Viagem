import { createRouter, createWebHistory } from 'vue-router';
import DashboardPage from '../pages/DashboardPage.vue';
import LoginPage from '../pages/LoginPage.vue';
import { useAuthStore } from '../stores/auth';
import CreateOrderPage from '../pages/CreateOrderPage.vue';
import RegisterPage from '../pages/RegisterPage.vue';

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: LoginPage },
  { path: '/dashboard', component: DashboardPage, meta: { requiresAuth: true } },
  { path: '/orders/create', component: CreateOrderPage},
  { path: '/cadastrar', component: RegisterPage}
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, _, next) => {
  const auth = useAuthStore();
  
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next('/login');
  }
  next();
});

export default router;
