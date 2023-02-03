import { createRouter, createWebHistory } from 'vue-router';
import MainView from '../views/main/MainView.vue';
import RegistrationView from '../views/registration/RegistrationView.vue';
import AdminView from '../views/admin/AdminView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'mainSelection',
      component: MainView,
    },
    {
      path: '/:adminOrgId',
      name: 'mainRoot',
      component: MainView,
      children: [
        {
          path: '/:adminOrgId',
          name: 'mainHomePage',
          component: () => import('../views/main/homepage/HomePage.vue'),
          children: [
            {
              path: '/:adminOrgId',
              name: 'mainMainPage',
              component: () => import('../views/main/homepage/pages/MainPage.vue'),
            },
            {
              path: '/:adminOrgId/news',
              name: 'mainNewsPage',
              component: () => import('../views/main/homepage/pages/NewsPage.vue'),
            },
            {
              path: '/:adminOrgId/news/:postId',
              name: 'mainPostPage',
              component: () => import('../views/main/homepage/pages/NewsPage.vue'),
            }
          ]
        },
      ]
    },
    {
      path: '/admin',
      name: 'adminRoot',
      component: AdminView,
      children: [
        {
          path: '/admin',
          name: 'adminHome',
          component: () => import('../views/admin/console/ConsoleLayout.vue'),
          children: [
            {
              path: '/admin',
              name: 'adminMainPage',
              component: () => import('../views/admin/console/pages/MainPage.vue'),
            },
            {
              path: 'games',
              name: 'adminGamePage',
              component: () => import('../views/admin/console/pages/GamePage.vue'),
            },
            {
              path: 'bulletin',
              name: 'adminBulletinPage',
              component: () => import('../views/admin/console/pages/BulletinPage.vue'),
            },
            {
              path: 'account',
              name: 'adminAccountPage',
              component: () => import('../views/admin/console/pages/AccountPage.vue'),
            },
          ],
        },
        {
          path: 'game/:sportCode/:gameId',
          name: 'adminGameLayout',
          component: () => import('../views/admin/game/GameLayout.vue'),
          children: [
            {
              path: '',
              name: 'adminGameHomePage',
              component: () => import('../views/admin/game/pages/MainPage.vue'),
            },
            {
              path: 'registration',
              name: 'adminGameRegistrationPage',
              component: () => import('../views/admin/game/pages/RegistrationPage.vue'),
            },
          ]
        },
      ],
    },
    {
      path: '/admin/login',
      name: 'adminLogin',
      component: () => import('../views/admin/login/LoginView.vue'),
    },
    {
      path: '/registration',
      name: 'registrationRoot',
      component: RegistrationView,
    },
  ],
});

export default router;
