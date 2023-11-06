<script setup lang="ts">
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'
import { lanePhaseToString } from '@/components/library/functions';
import ResultGeneral from '@/components/main/game/lane/ResultGeneral.vue';
import ResultHeat from '@/components/main/game/lane/ResultHeat.vue';
import ResultDistance from '@/components/main/game/lane/ResultDistance.vue';

const route = useRoute();
const vr = new VueRequest();
const divisionId = useRoute().params.divisionId;
const eventCode = useRoute().params.eventCode;
const gameId = route.params.gameId;
const gameData: any = inject('gameData');
const isLoading = ref(false);
const dataList: any = ref([]);
const laneList: any = ref([]);
const params: any = ref({});
async function getData() {
  isLoading.value = true;
  await vr.Get(`game/${gameData.value.sport_code}/${gameId}/main/params/${divisionId}/${eventCode}`, params);
  await vr.Get(`game/${gameData.value.sport_code}/${gameId}/main/lane`, laneList);
  if (params.value.multiple == 0){
    await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/individual/by/event/${divisionId}/${eventCode}`, dataList);
  } else {
    await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/group/by/event/${divisionId}/${eventCode}`, dataList);
  }
  isLoading.value = false;
};
getData();

let interval: number;

onMounted(() => {
  if (route.query.status == undefined || route.query.status != '4') {
    interval = setInterval(() => {
      getData();
    }, 10000);
  }
});

onBeforeUnmount(() => {
  clearInterval(interval);
});

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div v-if="isLoading == false">
    <div class="p-2 flex gap-3 mb-3 items-stretch">
      <div>
        <div class="text-xl text-blue-500">{{ params.division_ch }} {{ params.event_ch }} [{{ lanePhaseToString(Number($route.params.round), 'zh-TW') }}]</div>
        <div class="text-base text-blue-500">{{ params.division_en }} {{ params.event_en }} [{{ lanePhaseToString(Number($route.params.round), 'en-US') }}]</div>
      </div>
      <div class="flex-grow"></div>
      <div v-if="route.query.status == undefined || route.query.status != '4'" class="py-1 px-3 text-white bg-blue-500 rounded-md text-center font-medium animate-pulse">
        <div class="text-lg">即時成績</div>
        <div class="text-sm">Live Result</div>
      </div>
      <div class="text-right">
        <div>總數 Total：{{ dataList.length }}</div>
        <div>Q：{{ params[`r${$route.params.round}_aq`] }} / q：{{ params[`r${$route.params.round}_sq`] }}</div>
      </div>
    </div>
    <ResultGeneral v-if="$route.params.displayType == 'general'" :input-data="dataList" :game-data="gameData" :phase-num="$route.params.round" :track-data="laneList" :is-multiple="params.multiple" :param-data="params"></ResultGeneral>
    <ResultHeat v-else-if="$route.params.displayType == 'heat'" :input-data="dataList" :game-data="gameData" :phase-num="$route.params.round" :track-data="laneList" :is-multiple="params.multiple" :param-data="params"></ResultHeat>
    <ResultDistance v-else-if="$route.params.displayType == 'distance'" :input-data="dataList" :game-data="gameData" :phase-num="$route.params.round" :track-data="laneList" :is-multiple="params.multiple" :param-data="params"></ResultDistance>
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