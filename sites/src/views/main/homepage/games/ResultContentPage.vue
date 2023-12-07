<script setup lang="ts">
import { ref, inject } from 'vue';
import LaneResultContent from '@/components/main/game/lane/LaneResultContent.vue';
import RoadResultContent from '@/components/main/game/road/RoadResultContent.vue';
import { useI18n } from 'vue-i18n'

const gameData: any = inject('gameData');
const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div>
    <div @click="$router.push(`/${$route.params.adminOrgId}/game/${$route.params.gameId}/result`)" class="py-2 text-left text-gray-500 hover:text-gray-400 duration-200 cursor-pointer text-xl inline-block">{{ t('back') }}</div>
    <hr class="border-black my-3">
    <LaneResultContent v-if="gameData.module == 'ln'"></LaneResultContent>
    <RoadResultContent v-if="gameData.module == 'rd'"></RoadResultContent>
    <div class="p-2 text-sm">{{ t('live-result-reference-only') }}</div>
  </div>
</template>

<style scoped lang="scss">
    
</style>

<i18n lang="yaml">
  en-US:
    back: 'Back'
    live-result-reference-only: 'Live result is for reference only. Official result will be announced by the organizer.'
  zh-TW:
    back: '回上一頁'
    live-result-reference-only: '即時成績僅供參考，正式成績以大會公告為準。'
</i18n>