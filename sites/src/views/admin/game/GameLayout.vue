<script setup lang="ts">
import GameNav from '@/components/admin/console/GameNav.vue';
import AccountBtn from '@/components/admin/console/AccountBtn.vue';
import LogoSection from '@/components/admin/console/LogoSection.vue';
import { useGameStore } from '@/stores/game';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';

const store = useUserStore();
const gameStore = useGameStore();
const route = useRoute();
const gameId = route.params.gameId;
if (gameStore.data == null) {
  gameStore.data = JSON.parse(String(localStorage.getItem('gameData')));
}
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 md:h-screen">
    <div class="common-box left-box">
      <div>
        <LogoSection></LogoSection>
      </div>
      <div class="flex-grow overflow-auto h-full">
        <GameNav></GameNav>
      </div>
      <div>
        <AccountBtn></AccountBtn>
      </div>
    </div>
    <div class="common-box right-box">
      <router-view></router-view>
    </div>
  </div>
</template>

<style scoped lang="scss">
.common-box {
  @apply md:h-screen;
}
.left-box {
  @apply bg-white shadow flex flex-col;
}
.right-box {
  @apply p-5 overflow-hidden md:col-span-2 lg:col-span-3 xl:col-span-4 ;
}
</style>