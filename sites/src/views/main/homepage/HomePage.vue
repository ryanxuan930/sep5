<script setup lang="ts">
import { ref, provide } from 'vue';
import type { Ref } from 'vue';
import VueRequest from '@/vue-request';
import VueHorizontal from 'vue-horizontal';
import { useRoute, useRouter } from 'vue-router';
import HomeNav from '@/components/main/homepage/HomeNav.vue';
import HomeFooter from '@/components/main/homepage/HomeFooter.vue';
import type { IPageData } from '@/components/library/interfaces';
const vr = new VueRequest();
if (useRoute().params.adminOrgId == undefined) {
  useRouter().push('/2');
}
const adminOrgId = useRoute().params.adminOrgId;

const pageData:Ref<IPageData|null> = ref(null);
(async () => {
  const temp = await vr.Get(`config/${adminOrgId}`);
  pageData.value = temp.options;
  document.title = 'Sports';
})();
provide('pageData', pageData);
</script>

<template>
  <div class="h-full md:h-screen flex flex-col bg-white" v-if="pageData != null">
    <HomeNav></HomeNav>
    <div class="flex-grow overflow-auto flex flex-col">
      <VueHorizontal snap="center" ref="horizontal" class="horizontal" :button-between="false">
        <div v-for="(item, index) in pageData.homepageSlideshow" :key="index" class="w-full">
            <div class="hidden md:block h-16 overflow-hidden">
              <img :src="item" class="w-full h-auto nav-background" alt="">
            </div>
            <img :src="item" class="w-full h-auto" alt="">
        </div>
      </VueHorizontal>
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
.nav-background {
  filter: blur(5px);
}
</style>