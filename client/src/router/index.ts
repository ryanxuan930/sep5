import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import MainView from '../views/main/MainView.vue';
import AdminView from '../views/admin/AdminView.vue';
import RegistrationView from '../views/registration/RegistrationView.vue';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'mainRoot',
    component: MainView,
  },
  {
    path: '/registration',
    name: 'registrationRoot',
    component: RegistrationView,
  },
  {
    path: '/admin',
    name: 'adminRoot',
    component: AdminView,
    children: [
      {
        path: 'login',
        name: 'adminLogin',
        component: () => import('../views/admin/login/LoginPage.vue'),
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
