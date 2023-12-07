<script setup lang="ts">
import { inject } from 'vue';
import LaneResult from '@/components/main/game/lane/LaneResult.vue';
import RoadResult from '@/components/main/game/road/RoadResult.vue';
import { useI18n } from 'vue-i18n'

const gameData: any = inject('gameData');
const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div>
    <LaneResult v-if="gameData.module == 'ln'"></LaneResult>
    <RoadResult v-if="gameData.module == 'rd'"></RoadResult>
  </div>
  <div class="p-2 text-sm">{{ t('live-result-reference-only') }}</div>
</template>

<i18n lang="yaml">
  en-US:
    back: 'Back'
    live-result-reference-only: 'Live result is for reference only. Official result will be announced by the organizer.'
  zh-TW:
    back: '回上一頁'
    live-result-reference-only: '即時成績僅供參考，正式成績以大會公告為準。'
</i18n>