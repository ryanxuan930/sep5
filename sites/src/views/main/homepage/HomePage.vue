<script setup lang="ts">
import { ref, reactive } from 'vue';
import VueRequest from '@/vue-request';
import VueHorizontal from 'vue-horizontal';
import HomeNav from '@/components/main/homepage/HomeNav.vue';
import HomeFooter from '@/components/main/homepage/HomeFooter.vue';
const imgList = reactive([
  'https://ryanxuan930.github.io/cdn/nsysu_athletics/ufag/banner.JPG',
]);
// user content
const footerContent = '<div>國立中山大學學務處體育發展組</div><div class="text-xs">Physical Development Division, OSA, NSYSU</div>';
</script>

<template>
  <div class="h-screen flex flex-col bg-white">
    <HomeNav></HomeNav>
    <div class="flex-grow overflow-auto flex flex-col">
      <VueHorizontal snap="center" ref="horizontal" class="horizontal" :button-between="false">
        <div v-for="(item, index) in imgList" :key="index" class="w-full">
            <img :src="item" class="w-full h-auto" alt="">
        </div>
      </VueHorizontal>
      <div class="mx-auto my-5 w-11/12 sm:w-5/6 md:w-3/4 lg:w-2/3">
        <router-view></router-view>
      </div>
      <div class="flex-grow"></div>
      <HomeFooter>
        <template #content>
          <div v-html="footerContent"></div>
        </template>
      </HomeFooter>
      </div>
  </div>
</template>

<style scoped lang="scss">
</style>