<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';
import Config from '@/assets/config.json';

const store = useUserStore();

const navList = reactive([
  {title: {'zh-TW': '系統首頁', 'en-US': 'Home'}, link: `/${useRoute().params.adminOrgId}/registration`, permission: 0},
  {title: {'zh-TW': '分部管理', 'en-US': 'Department'}, link: `/${useRoute().params.adminOrgId}/registration/department`, permission: 1},
  {title: {'zh-TW': '組織管理', 'en-US': 'Organization'}, link: `/${useRoute().params.adminOrgId}/registration/organization`, permission: 2},
]);

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <router-link class="nav-link" :to="`/${useRoute().params.adminOrgId}/registration`">
    <div>{{ t('home') }}</div>
  </router-link>
  <router-link class="nav-link" v-if="store.userInfo.permission > 0 && store.userInfo.org_code != 'O0000'" :to="`/${useRoute().params.adminOrgId}/registration/department`">
    <div v-if="Config.deptAsClass">{{ t('class') }}</div>
    <div v-else>{{ t('department') }}</div>
  </router-link>
  <router-link class="nav-link" v-if="store.userInfo.permission > 1 && store.userInfo.org_code != 'O0000'" :to="`/${useRoute().params.adminOrgId}/registration/organization`">
    <div>{{ t('organization') }}</div>
  </router-link>
  <router-link class="nav-link" v-if="store.userInfo.permission > 0 && store.userInfo.org_code != 'O0000'" :to="`/${useRoute().params.adminOrgId}/registration/athlete`">
    <div>{{ t('athlete') }}</div>
  </router-link>
  <router-link class="nav-link" v-if="store.userInfo.permission == 0 && store.userInfo.org_code == 'O0000'" :to="`/${useRoute().params.adminOrgId}/registration/create`">
    <div>{{ t('create') }}</div>
  </router-link>
</template>

<i18n lang="yaml">
  en-US:
    home: 'Home'
    department: 'Department'
    class: 'Class'
    organization: 'Organization'
    athlete: 'User Account'
    create: 'New Organization'
  zh-TW:
    home: '首頁'
    department: '系所/分部'
    class: '班級'
    organization: '組織單位'
    athlete: '帳號管理'
    create: '建立單位'
</i18n>
