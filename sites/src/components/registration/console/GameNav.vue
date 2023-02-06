<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';

const store = useUserStore();

const navList = reactive([
  {title: {'zh-TW': '回上一頁', 'en-US': 'Back'}, link: `/${useRoute().params.adminOrgId}/registration`, permission: 0},
  {title: {'zh-TW': '賽事資訊', 'en-US': 'Information'}, link: `/${useRoute().params.adminOrgId}/registration/game/${useRoute().params.gameCode}/${useRoute().params.gameId}`, permission: 0},
  {title: {'zh-TW': '個人項目', 'en-US': 'Individual'}, link: `/${useRoute().params.adminOrgId}/registration/game/${useRoute().params.gameCode}/${useRoute().params.gameId}/individual`, permission: 0},
  {title: {'zh-TW': '團體項目', 'en-US': 'Group'}, link: `/${useRoute().params.adminOrgId}/registration/game/${useRoute().params.gameCode}/${useRoute().params.gameId}/group`, permission: 0},
]);

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <template v-for="(item, index) in navList" :key="index">
    <router-link class="nav-link" :to="item.link" v-if="store.userInfo.permission >= item.permission">
      <div>{{ item.title[locale] }}</div>
    </router-link>
  </template>
</template>
