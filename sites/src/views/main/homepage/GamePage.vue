<script setup lang="ts">
import { ref, provide } from 'vue';
import type { Ref } from 'vue';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import GameNav from '@/components/main/game/GameNav.vue';
import HomeFooter from '@/components/main/homepage/HomeFooter.vue';
import type { IPageData } from '@/components/library/interfaces';

const route = useRoute();
const vr = new VueRequest();
const adminOrgId = useRoute().params.adminOrgId;
const pageData:Ref<IPageData|null> = ref(null);
const gameData: any = ref(null);
(async () => {
  const temp = await vr.Get(`config/${adminOrgId}`);
  await vr.Get(`0/game/${route.params.gameId}`, gameData);
  pageData.value = temp.options;
  document.title = gameData.value.game_name_ch;
})();

provide('pageData', pageData);
provide('gameData', gameData);
</script>

<template>
  <div class="h-screen flex flex-col bg-white" v-if="gameData != null">
    <GameNav></GameNav>
    <div class="flex-grow overflow-auto flex flex-col">
      <div>
        <div class="hidden md:block h-16 overflow-hidden">
          <img :src="gameData.options.bannerUrl" class="w-full h-auto nav-background" alt="">
        </div>
        <img class="w-full" :src="gameData.options.bannerUrl">
      </div>
      <div class="mx-auto my-5 w-11/12 sm:w-5/6 md:w-3/4 lg:w-2/3">
        <router-view></router-view>
      </div>
      <div class="flex-grow"></div>
      <HomeFooter v-if="pageData != null">
        <template #content>
          <div v-html="pageData.footerContent"></div>
        </template>
      </HomeFooter>
      </div>
  </div>
</template>

<style scoped lang="scss">
.nav-background {
  filter: blur(5px);
}
</style>