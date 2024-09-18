<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useGameStore } from '@/stores/game';
import { useRoute } from 'vue-router';
import { lanePhaseToString } from '@/components/library/functions';
import GeneralResult from '@/components/admin/game/common/print/lane/layout/GeneralResult.vue';
import HeatResult from '@/components/admin/game/common/print/lane/layout/HeatResult.vue';
import DistanceResult from '@/components/admin/game/common/print/lane/layout/DistanceResult.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const gameStore = useGameStore();
const scheduleId = Number(route.params.scheduleId);
const divisionId = Number(route.params.divisionId);
const eventCode = route.params.eventCode;
const multiple = Number(route.params.multiple);
const round = Number(route.params.round);
const printMode = route.params.printMode;
const phaseArray = ['ref', 'r1', 'r2', 'r3', 'r4'];

if (printMode == 'distance') {
  import('@/components/admin/game/common/print/lane/pageLand.scss');
} else {
  import('@/components/admin/game/common/print/lane/page.scss');
}

const dataList: any = ref([]);
const paramList: any = ref([]);
const eventData: any = ref({});
const recordList: any = ref({});
const isLoading = ref(false);
(async () => {
  isLoading.value = true;
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/params/${divisionId}/${eventCode}`, paramList);
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/schedule/${scheduleId}`, eventData);
  if (multiple == 0){
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${divisionId}/${eventCode}`, dataList);
  } else {
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/event/${divisionId}/${eventCode}`, dataList);
  }
  const temp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/temp/gameRecords`);
  let records: any = {};
  if (Object.keys(temp).length == 0 && temp.constructor === Object) {
    records = [];
  } else {
    records = JSON.parse(temp.temp_data);
  }
  records.forEach((item: any) => {
    if (item.division_id == divisionId && item.event_code == eventCode) {
      recordList.value = item;
    }
  });
  if (gameStore.data.module == 'ln') {
    dataList.value.sort((a: any, b: any) => a[`r${[round]}_heat`] - b[`r${[round]}_heat`] || a[`r${[round]}_lane`] - b[`r${[round]}_lane`]);
    for (let i = 0; i < dataList.value.length; i++) {
      dataList.value[i][`r${[round]}_options`] = JSON.parse(dataList.value[i][`r${[round]}_options`]);
    }
  }
  document.title = gameStore.data.game_name_ch + '_' + eventData.value.division_ch + '_' + eventData.value.event_ch + '_' + lanePhaseToString(round, 'zh-TW') + '_' + '成績公告單';
  isLoading.value = false;
  setTimeout(() => {
    window.print();
  }, 100);
})();

function getTargetPhase(current: number, params: any) {
  switch(current) {
    case 1:
      return 0;
    case 2:
      if (params.r1 == 0) {
        return 0;
      } else {
        return 1;
      }
    case 3:
      if (params.r1 == 0 && params.r2 == 0) {
        return 0
      } else if (params.r1 == 1 && params.r2 == 0) {
        return 1
      } else if (params.r1 == 1 && params.r2 == 1) {
        return 2
      }
    case 4:
      if (params.r1 == 0 && params.r2 == 0 && params.r3 == 0) {
        return 0
      } else if (params.r1 == 1 && params.r2 == 0 && params.r3 == 0) {
        return 1
      } else if (params.r1 == 1 && params.r2 == 1 && params.r3 == 0) {
        return 2
      } else if (params.r1 == 1 && params.r2 == 1 && params.r3 == 1) {
        return 3
      }
    default:
      return 0;
  }
}
</script>

<template>
  <div v-if="isLoading == false">
    <div class="page">
      <div class="text-center font20 font-semibold">{{ gameStore.data.game_name_ch }}</div>
      <div class="text-center font14 font-semibold">{{ gameStore.data.game_name_en }}</div>
      <div class="text-center font16 font-medium">成績公告單 Official Result</div>
      <div class="height1"></div>
      <div class="grid grid-cols-4 header-table">
        <div class="content-box">
          <div class="title">
            <div class="ch-content">時間</div>
            <div class="en-content">Time</div>
          </div>
          <div>
            <div>{{ eventData.timestamp.substring(0,4) }}/{{ eventData.timestamp.substring(5,7) }}/{{ eventData.timestamp.substring(8,10) }}</div>
            <div>{{ eventData.timestamp.substring(11,16) }}</div>
          </div>
        </div>
        <div class="content-box col-span-2">
          <div class="title">
            <div class="ch-content">項目</div>
            <div class="en-content">Event</div>
          </div>
          <div>
            <div class="ch-content">{{ eventData.division_ch }}-{{ eventData.event_ch }} [{{ lanePhaseToString(round, 'zh-TW') }}]</div>
            <div class="en-content">{{ eventData.division_en }}-{{ eventData.event_en }} ({{ lanePhaseToString(round, 'en-US') }})</div>
          </div>
        </div>
        <div class="content-box">
          <div class="title">
            <div class="ch-content whitespace-nowrap">大會紀錄</div>
            <div class="en-content whitespace-nowrap">CR</div>
          </div>
          <div>
            <div class="font7">{{ recordList.unit_name_ch }}</div>
            <div class="font10 whitespace-break-spaces">{{ recordList.last_name_ch }}{{ recordList.first_name_ch }} {{ recordList.result }}</div>
            <div class="font7">{{ recordList.set_date }}</div>
          </div>
        </div>
        <div class="content-box">
          <div class="title">
            <div class="ch-content">場次</div>
            <div class="en-content">Session</div>
          </div>
          <div>{{ eventData.schedule_id.toString().padStart(3, '0') }}</div>
        </div>
        <div class="content-box col-span-2">
          <div class="title">
            <div class="ch-content">晉級</div>
            <div class="en-content">Qualified</div>
          </div>
          <div>
            <div class="ch-content">每組(Q)取 {{ paramList[`r${[round]}_aq`] }}，另擇優(q)取 {{ paramList[`r${[round]}_sq`] }}</div>
            <div class="font8">First {{ paramList[`r${[round]}_aq`] }} of each heat (Q) + {{ paramList[`r${[round]}_sq`] }} fastest times (q) are qualified</div>
          </div>
        </div>
        <div class="content-box">
          <div class="title">
            <div class="ch-content">全國紀錄</div>
            <div class="en-content">NR</div>
          </div>
          <div>N/A</div>
        </div>
      </div>
      <div class="height1"></div>
      <GeneralResult v-if="printMode == 'general'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></GeneralResult>
      <HeatResult v-if="printMode == 'heat'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></HeatResult>
      <DistanceResult v-if="printMode == 'distance'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></DistanceResult>
      <div class="height1"></div>
      <div v-if="gameStore.data.sport_code=='athl'" class="font8">Q：分組錄取 q：全體擇優 DNS：未出賽 DNF：未完賽 DQ：犯規 NM：無成功試跳(擲)成績 CR：大會紀錄 NR：國家紀錄 W：
      風速 RT：反應時間</div>
      <div v-if="gameStore.data.sport_code=='swim'" class="font8">Q：分組錄取 q：全體擇優 DNS：未出賽 DNF：未完賽 DQ：犯規 CR：大會紀錄 NR：國家紀錄 RT：反應時間</div>
      <div class="height2"></div>
      <div class="text-center font8 grid grid-cols-3">
        <div>Official Timekeeper : 
          <img src="@/assets/TechNSport-Logo.svg" class="h-4 inline-block">
        </div>
        <div>{{ $route.params.sportCode.toString().toUpperCase() }}_{{ $route.params.gameId }}_{{ divisionId }}_{{ eventCode.toString().toUpperCase() }}_{{ phaseArray[round].toUpperCase() }}_{{ Date.now() }}</div>
        <div>Timestamp: {{ new Date().toLocaleString('zh-TW', { hour12: false, hc: 'h23'}) }}</div>
        </div>
    </div>
  </div>
</template>

<style lang="scss" scoped src="@/components/admin/game/common/print/lane/page.scss"></style>