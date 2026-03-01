import { createRouter, createWebHistory } from 'vue-router';
import ClientLayout from '../layouts/ClientLayout.vue';
import Dashboard from '../views/Dashboard.vue';
import Inbox from '../views/Inbox.vue';
import ConversationDetail from '../views/ConversationDetail.vue';
import Leads from '../views/Leads.vue';
import Employees from '../views/Employees.vue';
import AutomationSettings from '../views/AutomationSettings.vue';
import Statistics from '../views/Statistics.vue';
import Campaigns from '../views/Campaigns.vue';
import Account from '../views/Account.vue';
import Login from '../views/Login.vue';

const router = createRouter({
  history: createWebHistory('/client'),
  routes: [
    { path: '/login', name: 'login', component: Login, meta: { guest: true } },
    {
      path: '/',
      component: ClientLayout,
      meta: { requiresAuth: true },
      children: [
        { path: '', redirect: { name: 'dashboard' } },
        { path: 'dashboard', name: 'dashboard', component: Dashboard },
        { path: 'inbox', name: 'inbox', component: Inbox },
        { path: 'inbox/:id', name: 'conversation', component: ConversationDetail },
        { path: 'leads', name: 'leads', component: Leads },
        { path: 'employees', name: 'employees', component: Employees },
        { path: 'automation', name: 'automation', component: AutomationSettings },
        { path: 'statistics', name: 'statistics', component: Statistics },
        { path: 'campaigns', name: 'campaigns', component: Campaigns },
        { path: 'account', name: 'account', component: Account },
      ],
    },
  ],
});

const agentOnlyRoutes = ['leads', 'employees', 'automation', 'statistics', 'campaigns'];

router.beforeEach(async (to) => {
  const isLoginPage = to.name === 'login';
  try {
    const r = await fetch('/client/api/user', { credentials: 'same-origin' });
    const data = await r.json();
    const authenticated = r.ok && data?.user != null;
    const role = data?.user?.role;

    if (to.meta.requiresAuth && !authenticated) {
      return { name: 'login', query: { redirect: to.fullPath } };
    }
    if (isLoginPage && authenticated) {
      return { name: 'inbox' };
    }
    // صلاحية «رد على الرسائل فقط»: لا يصل إلا لصندوق الوارد والحساب
    if (authenticated && role === 'agent' && to.name && agentOnlyRoutes.includes(to.name as string)) {
      return { name: 'inbox' };
    }
  } catch {
    if (to.meta.requiresAuth) {
      return { name: 'login', query: { redirect: to.fullPath } };
    }
  }
  return true;
});

export default router;
