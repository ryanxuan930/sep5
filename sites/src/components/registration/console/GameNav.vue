<script setup lang="ts">
import { reactive, inject } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';

const store = useUserStore();
const regConfig: any = inject('regConfig');

const navList = reactive([
  {title: {'zh-TW': '回上一頁', 'en-US': 'Back'}, link: `/${useRoute().params.adminOrgId}/registration`, permission: 0, type: 0},
  {title: {'zh-TW': '賽事資訊', 'en-US': 'Information'}, link: `/${useRoute().params.adminOrgId}/registration/game/${useRoute().params.sportCode}/${useRoute().params.gameId}`, permission: 0, type: 0},
  {title: {'zh-TW': '個人項目', 'en-US': 'Individual'}, link: `/${useRoute().params.adminOrgId}/registration/game/${useRoute().params.sportCode}/${useRoute().params.gameId}/individual`, permission: 0, type: 1},
  {title: {'zh-TW': '團體項目', 'en-US': 'Group'}, link: `/${useRoute().params.adminOrgId}/registration/game/${useRoute().params.sportCode}/${useRoute().params.gameId}/group`, permission: 0, type: 1},
  {title: {'zh-TW': '項目列表', 'en-US': 'Event List'}, link: `/${useRoute().params.adminOrgId}/registration/game/${useRoute().params.sportCode}/${useRoute().params.gameId}/event-list`, permission: 0, type: 2},
  {title: {'zh-TW': '報名列印', 'en-US': 'Print'}, link: `/${useRoute().params.adminOrgId}/registration/game/${useRoute().params.sportCode}/${useRoute().params.gameId}/print`, permission: 0, type: 0},
]);

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
function displayFilter(permission: number, type: number, userInfo = store.userInfo, config = regConfig.value.deadline_list.seperate): boolean {
  if (userInfo.permission < permission) {
    return false;
  }
  if (config == false && type === 2) {
    return false
  }
  if (config == true && type === 1) {
    return false
  }
  return true;
}
</script>

<template>
  <template v-for="(item, index) in navList" :key="index">
    <router-link class="nav-link" :to="item.link" v-if="displayFilter(item.permission, item.type)">
      <div>{{ item.title[locale] }}</div>
    </router-link>
  </template>
</template>
