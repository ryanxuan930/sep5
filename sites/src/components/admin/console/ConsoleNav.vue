<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useUserStore } from '@/stores/user';

const store = useUserStore();

const navList = reactive([
  {title: '系統首頁', link: '/admin', permission: 0},
  {title: '賽事列表', link: '/admin/games', permission: 0},
  {title: '公告列表', link: '/admin/bulletin', permission: 0},
  {title: '帳號管理', link: '/admin/account', permission: 1},
  {title: '系統管理', link: '/admin/settings', permission: 8},
]);
</script>

<template>
  <template v-for="(item, index) in navList" :key="index">
    <router-link class="nav-link" :to="item.link" v-if="store.userInfo.permission >= item.permission">
      <div>{{ item.title }}</div>
    </router-link>
  </template>
</template>