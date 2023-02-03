<script setup lang="ts">
import { ref } from 'vue';
import CountDown from '@/components/main/homepage/CountDown.vue';
import VueRequest from '@/vue-request';
import type { Ref } from 'vue';
import { useRoute } from 'vue-router';
import NewsSection from '@/components/main/homepage/NewsSection.vue';

const vr = new VueRequest();
const adminOrgId = useRoute().params.adminOrgId;

const countdownTime = ref('2023-10-29T09:00:00');
// user content
const countdownTitle = '<div class="text-xl md:text-2xl">111學年度中山・高科・高大聯合新生田徑賽</div><div class="text-lg">UAAK Freshman Athletics Game NSYSU 2022</div>';
</script>

<template>
  <div>
    <CountDown :countdown-time="countdownTime">
      <template #title>
        <div v-html="countdownTitle"></div>
      </template>
    </CountDown>
    <NewsSection :display-mode="'home'"></NewsSection>
  </div>
</template>

<style scoped lang="scss">
    
</style>