<script setup lang="ts">
import { ref, inject } from 'vue';
import CountDown from '@/components/main/homepage/CountDown.vue';
import VueRequest from '@/vue-request';
import type { Ref } from 'vue';
import { useRoute } from 'vue-router';
import NewsSection from '@/components/main/homepage/NewsSection.vue';
import type { IPageData } from '@/components/library/interfaces';

const vr = new VueRequest();
const adminOrgId = useRoute().params.adminOrgId;

const pageData: IPageData = inject('pageData');
// user content
</script>

<template>
  <div>
    <CountDown :countdown-time="pageData.countdownTime">
      <template #title>
        <div v-html="pageData.countdownTitle"></div>
      </template>
    </CountDown>
    <NewsSection :display-mode="'home'"></NewsSection>
  </div>
</template>

<style scoped lang="scss">
    
</style>