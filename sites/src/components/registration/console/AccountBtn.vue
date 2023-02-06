<script setup lang="ts">
  import { useRouter, useRoute } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
  const router = useRouter();
  const store = useUserStore()
  const route = useRoute();
  const vr = new VueRequest(store.token);
  function logout() {
    store.reset();
    try {
      if (localStorage.getItem('sep5UserTemp')) {
        localStorage.removeItem('sep5UserTemp');
      }
    } catch (e) {
      console.log(e);
    }
    router.push(`/${route.params.adminOrgId}/registration/login`);
  }
  const { t, locale } = useI18n({
    inheritLocale: true,
    useScope: 'local'
  });
</script>

<template>
  <router-link class="nav-link" :to="`/${route.params.adminOrgId}/registration/settings`">
    <div>{{ t('settings') }}</div>
  </router-link>
  <div class="nav-link" @click="logout">
    <div>{{ t('logout') }}</div>
  </div>
</template>

<i18n lang="yaml">
  en-US:
    settings: 'Settings'
    logout: 'Logout'
  zh-TW:
    settings: '設定'
    logout: '登出'
</i18n>