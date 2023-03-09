<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';

const store = useUserStore();
const route = useRoute();
const sportCode = route.params.sportCode;
const gameId = route.params.gameId;

const navList = ref([
  {title: '上層選單', link: '/admin/games', permission: 0},
  {title: '賽事首頁', link: `/admin/game/${sportCode}/${gameId}`, permission: 0},
  {title: '報名系統', link: `/admin/game/${sportCode}/${gameId}/registration`, permission: 0},
  {title: '賽程編排', link: `/admin/game/${sportCode}/${gameId}/schedule`, permission: 0},
  {title: '競賽管理', link: `/admin/game/${sportCode}/${gameId}/management`, permission: 0},
  {title: '成績處理', link: `/admin/game/${sportCode}/${gameId}/results`, permission: 0},
  {title: '獎狀處理', link: `/admin/game/${sportCode}/${gameId}/awards`, permission: 0},
]);
</script>

<template>
  <template v-for="(item, index) in navList" :key="index">
    <router-link class="nav-link" :to="item.link" v-if="store.userInfo.permission >= item.permission">
      <div>{{ item.title }}</div>
    </router-link>
  </template>
</template>