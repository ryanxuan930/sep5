<script setup lang="ts">
import { reactive, ref } from 'vue';

const navList = reactive([
  {title: '系統首頁', link: '/admin'},
  {title: '賽事列表', link: '/admin/games'},
  {title: '公告資訊', link: '/admin/bulletin'},
  {title: '帳號管理', link: '/admin/account'},
  {title: '系統管理', link: '/admin/settings'},
]);
</script>

<template>
  <template v-for="(item, index) in navList" :key="index">
    <router-link class="nav-link" :to="item.link">
      <div>{{ item.title }}</div>
    </router-link>
  </template>
</template>