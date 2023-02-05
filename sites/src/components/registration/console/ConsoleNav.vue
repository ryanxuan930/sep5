<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';

const store = useUserStore();

const navList = reactive([
  {title: {'zh-TW': '系統首頁', 'en-US': 'Home'}, link: '/registration', permission: 0},
  {title: {'zh-TW': '分部管理', 'en-US': 'Department'}, permission: 1},
  {title: {'zh-TW': '組織管理', 'en-US': 'Organization'}, link: '/registration/settings', permission: 2},
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