import { createRouter, createWebHistory } from 'vue-router';
import MainView from '../views/main/MainView.vue';
import MainHomeView from '../views/main/homepage/HomePage.vue';
import GameHomeView from '../views/main/homepage/GamePage.vue';
import RegistrationView from '../views/registration/RegistrationView.vue';
import AdminView from '../views/admin/AdminView.vue';
import AdminHomeView from '../views/admin/console/ConsoleLayout.vue';
import AdminGameView from '../views/admin/game/GameLayout.vue';
import RegistrationHomeView from '../views/registration/ConsoleLayout.vue';
import RegistrationGameView from '../views/registration/GameLayout.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Home
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
          component: MainHomeView,
          children: [
            {
              path: '/:adminOrgId',
              name: 'mainMainPage',
              component: () => import('../views/main/homepage/pages/MainPage.vue'),
            },
            {
              path: 'news/:postId?',
              name: 'mainNewsPage',
              component: () => import('../views/main/homepage/pages/NewsPage.vue'),
            },
            {
              path: 'games/:gameId?',
              name: 'mainGamePage',
              component: () => import('../views/main/homepage/pages/GamePage.vue'),
            },
            {
              path: 'gallery',
              name: 'mainGalleryPage',
              component: () => import('../views/main/homepage/pages/GalleryPage.vue'),
            },
            {
              path: 'venues',
              name: 'mainVenuePage',
              component: () => import('../views/main/homepage/pages/VenuePage.vue'),
            },
            {
              path: 'teams',
              name: 'mainTeamPage',
              component: () => import('../views/main/homepage/pages/TeamPage.vue'),
            },
          ]
        },
        {
          path: '/:adminOrgId/game/:gameId',
          name: 'gameHomePage',
          component: GameHomeView,
          children: [
            {
              path: '',
              name: 'gameMainPage',
              component: () => import('../views/main/homepage/games/MainPage.vue'),
            },
            {
              path: 'news/:postId?',
              name: 'gameNewsPage',
              component: () => import('../views/main/homepage/games/NewsPage.vue'),
            },
            {
              path: 'schedule',
              name: 'gameSchedulePage',
              component: () => import('../views/main/homepage/games/TempPage.vue'),
            },
            {
              path: 'result',
              name: 'gameResultPage',
              component: () => import('../views/main/homepage/games/TempPage.vue'),
            },
            {
              path: 'venues',
              name: 'gameVenuesPage',
              component: () => import('../views/main/homepage/games/TempPage.vue'),
            },
          ]
        },
      ]
    },
    // Admin
    {
      path: '/admin',
      name: 'adminRoot',
      component: AdminView,
      children: [
        {
          path: '/admin',
          name: 'adminHome',
          component: AdminHomeView,
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
            {
              path: 'files',
              name: 'adminFilePage',
              component: () => import('../views/admin/console/pages/FilePage.vue'),
            },
            {
              path: 'settings',
              name: 'adminSettingsPage',
              component: () => import('../views/admin/console/pages/SettingsPage.vue'),
            },
          ],
        },
        {
          path: 'game/:sportCode/:gameId',
          name: 'adminGameLayout',
          component: AdminGameView,
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
    // Registration
    {
      path: '/:adminOrgId/registration',
      name: 'registrationRoot',
      component: RegistrationView,
      children: [
        {
          path: '/:adminOrgId/registration',
          name: 'registrationHome',
          component: RegistrationHomeView,
          children: [
            {
              path: '',
              name: 'registrationMainPage',
              component: () => import('../views/registration/pages/MainPage.vue'),
            },
            {
              path: 'department',
              name: 'registrationDepartmentPage',
              component: () => import('../views/registration/pages/DepartmentPage.vue'),
            },
            {
              path: 'organization',
              name: 'registrationOrganizationPage',
              component: () => import('../views/registration/pages/OrganizationPage.vue'),
            },
            {
              path: 'create',
              name: 'registrationCreatePage',
              component: () => import('../views/registration/pages/CreatePage.vue'),
            },
            {
              path: 'athlete',
              name: 'registrationAthletePage',
              component: () => import('../views/registration/pages/AthletePage.vue'),
            },
            {
              path: 'settings',
              name: 'registrationSettingsPage',
              component: () => import('../views/registration/pages/SettingsPage.vue'),
            },
          ],
        },
        {
          path: 'game/:sportCode/:gameId',
          name: 'registrationGameHome',
          component: RegistrationGameView,
          children: [
            {
              path: '',
              name: 'registrationGameMainPage',
              component: () => import('../views/registration/game/MainPage.vue'),
            },
            {
              path: 'individual',
              name: 'registrationGameIndividualPage',
              component: () => import('../views/registration/game/IndividualPage.vue'),
            },
            {
              path: 'group',
              name: 'registrationGameGroupPage',
              component: () => import('../views/registration/game/GroupPage.vue'),
            },
            {
              path: 'print',
              name: 'registrationGamePrintPage',
              component: () => import('../views/registration/game/PrintPage.vue'),
            },
          ],
        },
        {
          path: 'game/:sportCode/:gameId/print/reg-data',
          name: 'registrationGamePrintPageData',
          component: () => import('../components/registration/game/RegistrationForm.vue'),
        },
        {
          path: 'game/:sportCode/:gameId/print/consent-data',
          name: 'registrationGamePrintConsentData',
          component: () => import('../components/registration/game/ConsentForm.vue'),
        },
      ]
    },
    {
      path: '/:adminOrgId/registration/login',
      name: 'registrationLogin',
      component: () => import('../views/registration/login/LoginView.vue'),
      children: [
        {
          path: '',
          name: 'registrationLoginPage',
          component: () => import('../views/registration/login/pages/LoginPage.vue'),
        },
        {
          path: 'signup-identity',
          name: 'registrationIdentityPage',
          component: () => import('../views/registration/login/pages/IdentityPage.vue'),
        },
        {
          path: 'signup',
          name: 'registrationSignupPage',
          component: () => import('../views/registration/login/pages/SignupPage.vue'),
        },
      ]
    },
  ],
});

export default router;
