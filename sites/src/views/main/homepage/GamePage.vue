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
const pageData:Ref<IPageData> = ref({
  homepageSlideshow: [
    'https://ryanxuan930.github.io/cdn/nsysu_athletics/ufag/banner.JPG',
  ],
  showCountdown: true,
  countdownTime: '2023-02-13T00:00:00',
  countdownTitle: '<div class="text-xl md:text-2xl">系統要開發完畢</div><div class="text-lg">Deadline</div>',
  registrationUrl: 'https://sports.nsysu.edu.tw/registration',
  orgUrlTitle: {
    'zh-TW': '中山首頁',
    'en-US': 'NSYSU Homepage',
  },
  orgUrl: 'https://www.nsysu.edu.tw/',
  footerContent: '<div>國立中山大學學務處體育發展組</div><div class="text-xs">Physical Development Division, OSA, NSYSU</div>',
  firstNavbarBackgroundColor: 'bg-white',
  secondNavbarBackgroundColor: 'bg-gray-100',
  logoImageUrl: 'https://sports.nsysu.edu.tw/static/img/logo.79973e4d.svg',
  orgId: 10,
});
const gameData: any = ref(null);
vr.Get(`0/game/${route.params.gameId}`, gameData);
provide('pageData', pageData);
provide('gameData', gameData);
</script>

<template>
  <div class="h-screen flex flex-col bg-white" v-if="gameData != null">
    <GameNav></GameNav>
    <div class="flex-grow overflow-auto flex flex-col">
      <div>
        <img :src="gameData.options.bannerUrl">
      </div>
      <div class="mx-auto my-5 w-11/12 sm:w-5/6 md:w-3/4 lg:w-2/3">
        <router-view></router-view>
      </div>
      <div class="flex-grow"></div>
      <HomeFooter>
        <template #content>
          <div v-html="pageData.footerContent"></div>
        </template>
      </HomeFooter>
      </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>