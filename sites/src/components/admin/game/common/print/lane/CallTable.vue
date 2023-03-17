<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useGameStore } from '@/stores/game';
import { useRoute } from 'vue-router';
import { lanePhaseToString } from '@/components/library/functions';
import HeatLayout from '@/components/admin/game/common/print/lane/layout/HeatLayout.vue';
import FieldLayout from '@/components/admin/game/common/print/lane/layout/FieldLayout.vue';
import HeightLayout from '@/components/admin/game/common/print/lane/layout/HeightLayout.vue';
import T800Layout from '@/components/admin/game/common/print/lane/layout/T800Layout.vue';
import T1500Layout from '@/components/admin/game/common/print/lane/layout/T1500Layout.vue';
import T3000Layout from '@/components/admin/game/common/print/lane/layout/T3000Layout.vue';
import T5000Layout from '@/components/admin/game/common/print/lane/layout/T5000Layout.vue';
import T10000Layout from '@/components/admin/game/common/print/lane/layout/T10000Layout.vue';

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
  const records = JSON.parse(temp.temp_data);
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
  document.title = gameStore.data.game_name_ch + '_' +paramList.value.division_ch + '_' +paramList.value.event_ch + '_' + lanePhaseToString(round, 'zh-TW') + '_' + '檢錄暨成績紀錄單';
  isLoading.value = false;
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
      <div class="text-center font16 font-medium">檢錄暨成績紀錄單</div>
      <div class="text-center font12 font-medium">Start List and Result Table</div>
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
            <div class="ch-content">大會紀錄</div>
            <div class="en-content">CR</div>
          </div>
          <div>
            <div class="font7">{{ recordList.unit_name_ch }}</div>
            <div class="font10">{{ recordList.last_name_ch }}{{ recordList.first_name_ch }} {{ recordList.result }}</div>
            <div class="font7">{{ recordList.set_date }}</div>
          </div>
        </div>
        <div class="content-box">
          <div class="title">
            <div class="ch-content">場次</div>
            <div class="en-content">Session</div>
          </div>
          <div></div>
        </div>
        <div class="content-box col-span-2">
          <div class="title">
            <div class="ch-content">晉級</div>
            <div class="en-content">Qualified</div>
          </div>
          <div>
            <div class="ch-content">每組(Q)取 {{ paramList[`r${[round]}_aq`] }}，另擇優(q)取 {{ paramList[`r${[round]}_sq`] }}</div>
            <div class="en-content">First {{ paramList[`r${[round]}_aq`] }} of each heat (Q) + {{ paramList[`r${[round]}_sq`] }} fastest times (q) are qualified</div>
          </div>
        </div>
        <div class="content-box">
          <div class="title">
            <div class="ch-content">全國紀錄</div>
            <div class="en-content">NR</div>
          </div>
          <div></div>
        </div>
      </div>
      <div class="height1"></div>
      <HeatLayout v-if="printMode == 'heat'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></HeatLayout>
      <FieldLayout v-if="printMode == 'field'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></FieldLayout>
      <HeightLayout v-if="printMode == 'height'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></HeightLayout>
      <T800Layout v-if="printMode == '800m'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></T800Layout>
      <T1500Layout v-if="printMode == '1500m'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></T1500Layout>
      <T3000Layout v-if="printMode == '3000m'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></T3000Layout>
      <T5000Layout v-if="printMode == '5000m'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></T5000Layout>
      <T10000Layout v-if="printMode == '10000m'" :input-data="dataList" :last-round="phaseArray[getTargetPhase(round, paramList)]"></T10000Layout>
      <div class="height2"></div>
      <div class="text-center font9">{{ $route.params.sportCode.toString().toUpperCase() }}_{{ $route.params.gameId }}_{{ divisionId }}_{{ eventCode.toString().toUpperCase() }}_{{ phaseArray[round].toUpperCase() }} {{ new Date().toLocaleString('zh-TW', { hour12: false}) }}</div>
    </div>
  </div>
</template>

<style lang="scss" scoped src="@/components/admin/game/common/print/lane/page.scss"></style>
<style lang="scss" scoped>
@page {
  margin: 1cm;
  border: 0;
  background-color: white !important;
  -webkit-print-color-adjust:exact !important;
  print-color-adjust:exact !important;
  size: A4 landscape;
}
</style>