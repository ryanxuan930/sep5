<script setup lang="ts">
  import { ref, inject, watch } from 'vue';
  import type { Ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import Toggle from '@vueform/toggle';
  import { shuffle, stringToMilliseconds } from '@/components/library/functions';
  import LaneLayout from '@/components/admin/game/schedule/arrange/display/LaneLayout.vue';
  import OrderLayout from '@/components/admin/game/schedule/arrange/display/OrderLayout.vue';
  import SmallModal from '@/components/SmallModal.vue';
  import EditLane from '@/components/admin/game/schedule/arrange/EditLane.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const gameData: any = inject('gameData');
  const route = useRoute();
  const isLoading = ref(true);
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;
  const paramsList: any = ref([]);
  const params: any = ref([]);
  const laneList: any = ref([]);
  vr.Get(`game/${sportCode}/${gameId}/main/lane`, laneList, true, true);

  const dataList: any = ref([]);
  const selectedIndex = ref(0);
  const selectedTab = ref(1);
  const groupNum = ref(1);
  const allShffule: Ref<boolean> = ref(false);
  const isFetching = ref(false);
  async function open(index: number) {
    isFetching.value = true;
    dataList.value = null;
    groupNum.value = 1;
    allShffule.value = false;
    selectedIndex.value = index;
    if (params.value[index].r1 == 1){
      selectedTab.value = 1;
    } else if (params.value[index].r2 == 1){
      selectedTab.value = 2;
    } else if (params.value[index].r3 == 1){
      selectedTab.value = 3;
    } else if (params.value[index].r4 == 1){
      selectedTab.value = 4;
    }
    if(paramsList.value[index].multiple == 1){
      await vr.Get(`game/${sportCode}/${gameId}/common/group/by/event/${paramsList.value[index].division_id}/${paramsList.value[index].event_code}`, dataList, true, true);
    } else {
      await vr.Get(`game/${sportCode}/${gameId}/common/individual/by/event/${paramsList.value[index].division_id}/${paramsList.value[index].event_code}`, dataList, true, true);
    }
    isFetching.value = false;
  }
  async function refreshData () {
    isFetching.value = true;
    const index = selectedIndex.value;
    if(paramsList.value[index].multiple == 1){
      await vr.Get(`game/${sportCode}/${gameId}/common/group/by/event/${paramsList.value[index].division_id}/${paramsList.value[index].event_code}`, dataList, true, true);
    } else {
      await vr.Get(`game/${sportCode}/${gameId}/common/individual/by/event/${paramsList.value[index].division_id}/${paramsList.value[index].event_code}`, dataList, true, true);
    }
    isFetching.value = false;
  }

  async function getData() {
    isLoading.value = true;
    await vr.Get(`game/${sportCode}/${gameId}/main/params`, params, true, true);
    await vr.Get(`game/${sportCode}/${gameId}/main/params/full`, paramsList, true, true);
    open(0);
    isLoading.value = false;
  }
  getData();

  const phaseArray = ['ref', 'r1', 'r2', 'r3', 'r4'];
  function getTargetPhase(currentTab: number, params: any) {
    switch(currentTab) {
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

  async function autoArrange() {
    if (!confirm('確定進行自動排列？此動作無法復原')){
      return;
    }
    if (laneList.value.length == 0) {
      alert('請先設定跑道資訊');
      return;
    }
    if (dataList.value.length == 0) {
      alert('無參賽者資料');
      return;
    }
    // set phase
    const targetPhaseIndex = getTargetPhase(selectedTab.value, params.value[selectedIndex.value]);
    const targetPhasePrefix = phaseArray[targetPhaseIndex];
    const currentPhasePrefix = phaseArray[selectedTab.value];
    const currentParams = params.value[selectedIndex.value];
    const currentParamsFull = paramsList.value[selectedIndex.value];
    const timeEvent = ['ts', 'tn', 'tr', 'rr'];
    const distanceEvent = ['fj', 'ft'];
    const notAcceptResult = [null, 'null', 'DQ', 'DNS', 'DNF', 'NM', undefined];
    const data: any = JSON.parse(JSON.stringify(dataList.value));
    /*
    const data: any = [
      {r1_heat: 1, r1_lane: 1, r1_result: '11.21', ref_result: '0', r1_options: JSON.stringify({aq: true, sq: false})},
      {r1_heat: 1, r1_lane: 2, r1_result: '11.20', ref_result: '0', r1_options: JSON.stringify({aq: true, sq: false})},
      {r1_heat: 1, r1_lane: 3, r1_result: '11.28', ref_result: '0', r1_options: JSON.stringify({aq: false, sq: true})},
      {r1_heat: 1, r1_lane: 4, r1_result: '11.29', ref_result: '0', r1_options: JSON.stringify({aq: false, sq: true})},
      {r1_heat: 1, r1_lane: 5, r1_result: '11.30', ref_result: '0', r1_options: JSON.stringify({aq: false, sq: true})},
      {r1_heat: 1, r1_lane: 6, r1_result: '11.31', ref_result: '0', r1_options: JSON.stringify({aq: false, sq: true})},
      {r1_heat: 2, r1_lane: 1, r1_result: '11.21', ref_result: '0', r1_options: JSON.stringify({aq: true, sq: false})},
      {r1_heat: 2, r1_lane: 2, r1_result: '11.40', ref_result: '0', r1_options: JSON.stringify({aq: true, sq: false})},
      {r1_heat: 2, r1_lane: 3, r1_result: '11.48', ref_result: '0', r1_options: JSON.stringify({aq: false, sq: false})},
      {r1_heat: 2, r1_lane: 4, r1_result: '11.49', ref_result: '0', r1_options: JSON.stringify({aq: false, sq: false})},
      {r1_heat: 2, r1_lane: 5, r1_result: '11.50', ref_result: '0', r1_options: JSON.stringify({aq: false, sq: false})},
      {r1_heat: 2, r1_lane: 6, r1_result: '11.51', ref_result: '0', r1_options: JSON.stringify({aq: false, sq: false})},
    ];*/
    const trackArray: number[] = [];

    // convert time or length to milliseconds or centermeters
    if (timeEvent.includes(currentParamsFull.remarks)) {
      for(let i = 0; i < data.length; i++){
        const result = data[i][`${targetPhasePrefix}_result`];
        if (notAcceptResult.includes(result) || result == '0' || result == 0) {
          data[i].temp = stringToMilliseconds('99:59:59');
        } else {
          data[i].temp = stringToMilliseconds(result);
        }
        data[i][`${currentPhasePrefix}_heat`] = 0;
        data[i][`${currentPhasePrefix}_lane`] = 0;
      }
      // track
      if (currentParamsFull.remarks == 'ts') {
        laneList.value.forEach((item: any) => {
          if (item.straight > 0) {
            trackArray.push(item.straight);
          }
        });
      } else {
        laneList.value.forEach((item: any) => {
          if (item.round > 0) {
            trackArray.push(item.round);
          }
        });
      }
    } else {
      for(let i = 0; i < data.length; i++){
        const result = data[i][`${targetPhasePrefix}_result`];
        if (notAcceptResult.includes(result)) {
          data[i].temp = 0;
        } else {
          data[i].temp = Number(result) * 100;
        }
        data[i][`${currentPhasePrefix}_heat`] = 0;
        data[i][`${currentPhasePrefix}_lane`] = 0;
      }
    }

    let participants: any = [];
    if (targetPhaseIndex == 0) {
      participants = JSON.parse(JSON.stringify(data));
    } else {
      for (let i = 0; i < data.length; i++) {
        const temp = JSON.parse(data[i][`${targetPhasePrefix}_options`]);
        if (temp.aq || temp.sq) {
          participants.push(data[i]);
        }
      }
    }
    if (currentParamsFull.remarks == 'ts' || currentParamsFull.remarks == 'tr' || currentParamsFull.remarks == 'rr') {
      participants.sort((a: any, b: any) => a.temp - b.temp);
      const heat = Math.ceil(participants.length / trackArray.length);
      const heatArray = new Array(heat).fill(0);
      let pointer = 0;
      for(let i = 0; i < participants.length; i++){
        heatArray[pointer]++;
        if(pointer == heat - 1){
            pointer = 0;
        }else{
            pointer++;
        }
      }
      if (allShffule.value) {
        shuffle(participants);
      }
      pointer = 0;
      for(let i = 0; i < heat; i++){
        for(let j = 0; j < heatArray[i]; j++){
          participants[pointer][`${currentPhasePrefix}_heat`] = i + 1;
          participants[pointer][`${currentPhasePrefix}_lane`] = trackArray[j];
          pointer++;
        }
      }
    } else {
      participants.sort((a: any, b: any) => b.temp - a.temp);
      const heat = groupNum.value;
      const heatArray = new Array(heat).fill(0);
      let pointer = 0;
      for(let i = 0; i < participants.length; i++){
        heatArray[pointer]++;
        if(pointer == heat - 1){
            pointer=0;
        }else{
            pointer++;
        }
      }
      if (allShffule.value) {
        shuffle(participants);
      }
      pointer = 0;
      for(let i = 0; i < heat; i++){
        for(let j = 0; j < heatArray[i]; j++){
          participants[pointer][`${currentPhasePrefix}_heat`] = i + 1;
          participants[pointer][`${currentPhasePrefix}_lane`] = j + 1;
          pointer++;
        }
      }
    }
    for (let i = 0; i < data.length; i++) {
      data[i][`${currentPhasePrefix}_heat`] = 0;
      data[i][`${currentPhasePrefix}_lane`] = 0;
      for (let j = 0; j < participants.length; j++) {
        if (currentParamsFull.multiple == 1) {
          if (data[i].team_id == participants[j].team_id) {
            data[i][`${currentPhasePrefix}_heat`] = participants[j][`${currentPhasePrefix}_heat`];
            data[i][`${currentPhasePrefix}_lane`] = participants[j][`${currentPhasePrefix}_lane`];
          }
        } else {
          if (data[i].u_id == participants[j].u_id) {
            data[i][`${currentPhasePrefix}_heat`] = participants[j][`${currentPhasePrefix}_heat`];
            data[i][`${currentPhasePrefix}_lane`] = participants[j][`${currentPhasePrefix}_lane`];
          }
        }
      }
    }
    (async () => {
      let dataset: any = [];
      let res: any = null;
      const index = selectedIndex.value;
      if(currentParamsFull.multiple == 1){
        data.forEach((d: any) => {
          dataset.push({
            team_id: d.team_id,
            division_id: d.division_id,
            event_code: d.event_code,
            phase: currentPhasePrefix,
            heat: d[`${currentPhasePrefix}_heat`],
            lane: d[`${currentPhasePrefix}_lane`],
          });
        });
        res = await vr.Patch(`game/${sportCode}/${gameId}/common/group/update/heat-lane`, dataset, null, true, true);
      } else {
        data.forEach((d: any) => {
          dataset.push({
            u_id: d.u_id,
            division_id: d.division_id,
            event_code: d.event_code,
            phase: currentPhasePrefix,
            heat: d[`${currentPhasePrefix}_heat`],
            lane: d[`${currentPhasePrefix}_lane`],
          });
        });
        res = await vr.Patch(`game/${sportCode}/${gameId}/common/individual/update/heat-lane`, dataset, null, true, true);
      }
      if (res.status == 'A01') {
        alert('已儲存');
        refreshData();
      } else {
        alert('儲存失敗');
      }
    })();
  }

  watch(selectedTab, (val) => {
    refreshData();
  });
</script>

<template>
  <div v-if="isLoading == false" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4  gap-5 h-full overflow-hidden">
    <div class="left-box">
      <template v-for="(item, index) in paramsList" :key="index">
        <div :class="{'items': true, 'active': index == selectedIndex}" @click="open(index)">
          <div>{{ item.division_ch }}</div>
          <div>{{ item.event_ch }}</div>
        </div>
      </template>
    </div>
    <div class="right-box">
      <div class="items">
        <div class="text-xl font-medium">[{{ paramsList[selectedIndex].division_ch }}] {{ paramsList[selectedIndex].event_ch }}</div>
        <hr class="my-2">
        <div class="flex gap-3 items-center">
          <button class="general-button blue" @click="autoArrange()">自動編排</button>
          <button class="general-button blue" @click="displayModal = 1">編輯</button>
          <button class="general-button blue">聯合編排</button>
          <div>|</div>
          <span class="flex items-center gap-3">
            <div>全隨機排序</div>
            <div>
              <Toggle class="general-toggle" offLabel="否" onLabel="是" v-model="allShffule"></Toggle>
            </div>
            <div>非道次分組數</div>
            <div>
              <input type="number" class="border-2 py-1 px-4 rounded-full w-24" v-model="groupNum">
            </div>
          </span>
        </div>
      </div>
      <div class="items flex-grow overflow-hidden h-full flex flex-col" v-if="isFetching == false">
        <div class="bookmark">
          <div v-if="params[selectedIndex].r1 == 1" @click="selectedTab = 1" :class="{'active': selectedTab == 1}">預賽</div>
          <div v-if="params[selectedIndex].r2 == 1" @click="selectedTab = 2" :class="{'active': selectedTab == 2}">第二輪</div>
          <div v-if="params[selectedIndex].r3 == 1" @click="selectedTab = 3" :class="{'active': selectedTab == 3}">準決賽</div>
          <div v-if="params[selectedIndex].r4 == 1" @click="selectedTab = 4" :class="{'active': selectedTab == 4}">決賽</div>
          <span class="block hidden-span"></span>
        </div>
        <div class="overflow-hidden flex-grow h-full">
          <LaneLayout :input-data="dataList" :phase-num="selectedTab" :track-data="laneList" :is-multiple="paramsList[selectedIndex].multiple" v-if="paramsList[selectedIndex].remarks == 'ts' || paramsList[selectedIndex].remarks == 'tr' || paramsList[selectedIndex].remarks == 'rr'"></LaneLayout>
          <OrderLayout :input-data="dataList" :phase-num="selectedTab" v-else></OrderLayout>
        </div>
      </div>
    </div>
    <SmallModal v-show="displayModal > 0" @closeModal="displayModal = 0">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal == 1">組別道次編輯</div>
        </div>
      </template>
      <template v-slot:content>
        <div class="overflow-auto h-full">
          <EditLane v-if="displayModal == 1" :input-data="dataList" :phase-num="selectedTab" :param-list="paramsList[selectedIndex]" @closeModal="displayModal = 0" @refreshPage="refreshData()"></EditLane>
        </div>
      </template>
    </SmallModal>
  </div>
</template>

<style scoped lang="scss">
.left-box {
  @apply flex flex-col gap-2 bg-gray-100 p-3 overflow-auto shadow-inner;
  .items {
    @apply p-3 bg-white shadow cursor-pointer duration-150 hover:bg-blue-400 hover:text-white;
  }
  .items.active {
    @apply bg-blue-500 text-white;
  }
}
.right-box {
  @apply md:col-span-2 lg:col-span-3 p-3 bg-gray-100 shadow-inner overflow-hidden flex flex-col gap-3 h-full;
  .items {
    @apply p-3 bg-white shadow;
  }
}
input[type=checkbox] {
  @apply w-5 h-5;
}
.bookmark {
  @apply flex flex-shrink-0 flex-nowrap flex-row overflow-scroll justify-between items-center gap-[1px];
  div {
    @apply py-1 px-2 w-full whitespace-nowrap text-center bg-blue-300 text-white cursor-pointer hover:bg-blue-500 duration-200 rounded-t shadow box-border;
    &.active {
      @apply bg-blue-500;
    }
  }
  .hidden-span {
    @apply bg-transparent flex-grow w-full;
  }
}
</style>