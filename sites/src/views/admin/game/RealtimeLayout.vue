<script setup lang="ts">
import { useGameStore } from '@/stores/game';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';

const store = useUserStore();
const gameStore = useGameStore();
const route = useRoute();
const gameId = route.params.gameId;
if (gameStore.data == null) {
  if (localStorage.getItem('gameData') == undefined) {
    gameStore.fetch(store.userInfo.admin_org_id, Number(gameId));
  } else {
    gameStore.data = JSON.parse(String(localStorage.getItem('gameData')));
  }
}
</script>

<template>
  <div>
    <router-view></router-view>
  </div>
</template>

<style scoped lang="scss">
    
</style>