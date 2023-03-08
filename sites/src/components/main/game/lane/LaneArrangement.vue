<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'
import { lanePhaseToString } from '@/components/library/functions';
import LaneLayout from '@/components/main/game/lane/LaneLayout.vue';
import OrderLayout from '@/components/main/game/lane/OrderLayout.vue';

const route = useRoute();
const vr = new VueRequest();
const adminOrgId = useRoute().params.adminOrgId;
const divisionId = useRoute().params.divisionId;
const eventCode = useRoute().params.eventCode;
const gameId = route.params.gameId;
const pageData: any = inject('pageData');
const gameData: any = inject('gameData');
const isLoading = ref(false);
const dataList: any = ref([]);
const laneList: any = ref([]);
const params: any = ref({});
(async () => {
  isLoading.value = true;
  const paramList = await vr.Get(`game/${gameData.value.sport_code}/${gameId}/main/params/full`);
  await vr.Get(`game/${gameData.value.sport_code}/${gameId}/main/lane`, laneList);
  paramList.forEach((element: any) => {
    if (element.division_id == divisionId && element.event_code == eventCode) {
      params.value = element;
    }
  });
  if (params.value.multiple == 0){
    await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/individual/by/event/${divisionId}/${eventCode}`, dataList);
  } else {
    await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/group/by/event/${divisionId}/${eventCode}`, dataList);
  }
  isLoading.value = false;
})();

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
const statusCh = ['尚未開始', '檢錄中', '進行中', '已完賽', '成績公告', '頒獎', '取消'];
const statusEn = ['Not Started', 'Check In', 'In Progress', 'Finished', 'Result Announced', 'Awarding', 'Cancelled'];
</script>

<template>
  <div v-if="isLoading == false">
    <LaneLayout v-if="params.remarks == 'ts' || params.remarks == 'tr' || params.remarks == 'rr'" :input-data="dataList" :phase-num="$route.params.round" :track-data="laneList" :is-multiple="params.multiple"></LaneLayout>
    <OrderLayout v-else :input-data="dataList" :phase-num="$route.params.round" :track-data="laneList" :is-multiple="params.multiple"></OrderLayout>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-full;
  td, th {
    @apply p-2 border-y-[1px] text-left;
  }
}
</style>

<i18n lang="yaml">
  en-US:
    list: 'Start List'
    time: 'Time'
    division: 'Division'
    event: 'Event'
    round: 'Round'
    status: 'Status'
  zh-TW:
    list: '選手編配'
    time: '時間'
    division: '組別'
    event: '項目'
    round: '賽別'
    status: '狀態'
  </i18n>