import { createRouter, createWebHistory } from 'vue-router';
import AdminLayout from '../layouts/AdminLayout.vue';
import Dashboard from '../views/Dashboard.vue';
import Companies from '../views/Companies.vue';
import EditCompany from '../views/EditCompany.vue';
import Payments from '../views/Payments.vue';
import Login from '../views/Login.vue';

const router = createRouter({
  history: createWebHistory('/admin'),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: { guest: true },
    },
    {
      path: '/',
      component: AdminLayout,
      meta: { requiresAuth: true },
      children: [
        { path: '', redirect: { name: 'dashboard' } },
        { path: 'dashboard', name: 'dashboard', component: Dashboard },
        { path: 'companies', name: 'companies', component: Companies },
        { path: 'companies/:id/edit', name: 'companies.edit', component: EditCompany },
        { path: 'payments', name: 'payments', component: Payments },
      ],
    },
  ],
});

router.beforeEach(async (to) => {
  const isLoginPage = to.name === 'login';
  try {
    const r = await fetch('/admin/api/user', { credentials: 'same-origin' });
    const data = await r.json();
    const authenticated = r.ok && data?.user != null;

    if (to.meta.requiresAuth && !authenticated) {
      return { name: 'login', query: { redirect: to.fullPath } };
    }
    if (isLoginPage && authenticated) {
      return { name: 'dashboard' };
    }
  } catch {
    if (to.meta.requiresAuth) {
      return { name: 'login', query: { redirect: to.fullPath } };
    }
  }
  return true;
});

export default router;
