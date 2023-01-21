import { createRouter, createWebHistory } from 'vue-router';
import MainView from '../views/main/MainView.vue';
import RegistrationView from '../views/registration/RegistrationView.vue';
import AdminView from '../views/admin/AdminView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
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
          component: () => import('../views/admin/login/LoginView.vue'),
        },
      ],
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
  ],
});

export default router;
