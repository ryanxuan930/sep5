<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import SmallLoader from '@/components/SmallLoader.vue';
import FullModal from '@/components/FullModal.vue';
import SmallModal from '@/components/SmallModal.vue';
import AthleteList from '@/components/admin/game/common/AthleteList.vue';
import ResultList from '@/components/admin/game/common/ResultList.vue';
import { exportCsv, lanePhaseToString } from '@/components/library/functions';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const isLoading = ref(false);
const displayModal = ref(0);
const sportCode = route.params.sportCode;
const gameId = route.params.gameId;
const props = defineProps(['displayMode']);
const gameData: any = inject('gameData');
const counter = ref(0);
const selectedEvent: any = ref({});
const awardFormat: any = ref([]);

const scheduleList: any = ref([]);
async function getData() {
  isLoading.value = true;
  await vr.Get(`game/${sportCode}/${gameId}/common/schedule/full`, scheduleList, true, true);
  if (props.displayMode == 'print' || props.displayMode == 'award') {
    const temp = await vr.Get(`game/${sportCode}/${gameId}/common/temp/awardFormat`);
    if (temp.temp_id == undefined) {
      alert('尚未建立獎狀格式');
    } else {
      awardFormat.value = JSON.parse(temp.temp_data);
    }
  }
  isLoading.value = false;
}

function openEvent(input: any, index: number) {
  selectedEvent.value = input;
  displayModal.value = index;
}

setInterval(() => {
  if (counter.value == 0) {
    getData();
    counter.value = 60;
  }
  counter.value--;
}, 1000);

async function exportData(input: any) {
  const data: any = [];
  let res: any = null;
  if (input.multiple == 0){
    res =  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${input.division_id}/${input.event_code}`);
  } else {
    res =  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/event/${input.division_id}/${input.event_code}`);
  }
  res.sort((a: any, b: any) => a[`r${[input.round]}_heat`] - b[`r${[input.round]}_heat`] || a[`r${[input.round]}_lane`] - b[`r${[input.round]}_lane`]);
  const maxHeat = res[res.length - 1][`r${[input.round]}_heat`];
  for (let i = 0; i < maxHeat; i++) {
    data.push([`${input.division_ch}_${input.event_ch}_${lanePhaseToString(input.round, 'zh-TW')}`, i + 1, input.round, input.schedule_id, '']);
    for (let j = 0; j < res.length; j++) {
      if (res[j][`r${[input.round]}_heat`] == i + 1) {
        if (res[j][`r${[input.round]}_result`] != 'DNS' && res[j][`r${[input.round]}_result`] != 'DNF' && res[j][`r${[input.round]}_result`] != 'NM' && res[j][`r${[input.round]}_result`] != 'DQ') {
          if (input.multiple == 0) {
            data.push([res[j].bib == null ? res[j].u_id : res[j].bib, res[j][`r${[input.round]}_lane`], res[j].first_name_ch, res[j].last_name_ch, res[j].org_name_ch]);
          } else {
            data.push([res[j].team_id, res[j][`r${[input.round]}_lane`], res[j].team_name, '', res[j].org_name_ch]);
          }
        }
      }
    }
    if (i < maxHeat - 1) {
      data.push(['#', '', '', '', '']);
    }
  }
  exportCsv(data, `${input.division_ch}_${input.event_ch}_${lanePhaseToString(input.round, 'zh-TW')}`, null);
}
async function sendResult(id: number) {
  if (!confirm('確定送出成績? ')) {
    return;
  }
  const r: any = await vr.Post(`game/${route.params.sportCode}/${route.params.gameId}/common/schedule/update/${id}`, {status: 4}, null, true, true);
  if (r.status == 'A01') {
    alert('已送出');
    getData();
  } else {
    alert('操作失敗');
  }
}
async function retriveResult(id: number) {
  if (!confirm('確定撤回成績?')) {
    return;
  }
  const r: any = await vr.Post(`game/${route.params.sportCode}/${route.params.gameId}/common/schedule/update/${id}`, {status: 3}, null, true, true);
  if (r.status == 'A01') {
    alert('已撤回');
    getData();
  } else {
    alert('操作失敗');
  }
}
const printFormat = ref(0);

const realtimeGroup = ref(1);
const realtimeResult = ref('');
async function setRealtimeResult() {
  const data = JSON.stringify({
    data: selectedEvent.value,
    group: realtimeGroup.value,
    result: realtimeResult.value,
  });
  const temp = await vr.Get(`game/${sportCode}/${gameId}/common/temp/realtimeResult`);
  let res: any = null;
  if (temp.temp_id == undefined) {
    res = await vr.Post(`game/${sportCode}/${gameId}/common/temp`, {temp_key: 'realtimeResult', temp_data: data}, null, true, true);
  } else {
    res = await vr.Patch(`game/${sportCode}/${gameId}/common/temp/realtimeResult`, { temp_data: data}, null, true, true);
  }
  if (res.status == 'A01') {
    alert('已儲存');
    displayModal.value = 0;
  } else {
    alert('操作失敗');
  }
}
</script>

<template>
  <div class="h-full w-full overflow-auto">
    <table class="data-table">
      <template v-for="(item, index) in scheduleList" :key="index">
        <tr v-if="props.displayMode == 'management' || props.displayMode == 'result'  || props.displayMode == 'award' || (props.displayMode == 'call' && item.status <= 1) || (props.displayMode == 'print' && item.status >= 4) || (props.displayMode == 'input' && item.status > 1 && item.status < 4)">
          <td>{{ item.timestamp.substring(5,7) }}/{{ item.timestamp.substring(8,10) }} {{ item.timestamp.substring(11,16) }}</td>
          <template v-if="item.division_id != null && item.event_ch != null">
            <td>{{ item.division_ch }}</td>
            <td>{{ item.event_ch }}</td>
          </template>
          <td v-else colspan="2">
            <div v-if="gameData.module == 'ln'">{{ JSON.parse(item.options).title }}</div>
          </td>
          <td v-if="gameData.module == 'ln'">{{ lanePhaseToString(item.round, 'zh-TW') }}</td>
          <td>
            <div class="flex gap-2 items-center flex-wrap" v-if="item.division_id != null && item.event_ch != null">
              <button class="general-button blue" @click="openEvent(item, 1)">查看</button>
              <button v-if="(gameData.module == 'ln' || gameData.module == 'rd') && item.status < 2 && (props.displayMode == 'management' || props.displayMode == 'call')" class="general-button blue" @click="openEvent(item, 2)">檢錄</button>
              <button v-if="gameData.module == 'ln'" class="general-button blue" @click="exportData(item)">名單下載</button>
              <button v-if="(gameData.module == 'ln' || gameData.module == 'rd') && item.status > 1 && item.status < 4 && (props.displayMode == 'result' || props.displayMode == 'input')" class="general-button blue" @click="openEvent(item, 3)">成績</button>
              <button v-if="(gameData.module == 'ln' || gameData.module == 'rd') && item.status == 3 && (props.displayMode == 'result' || props.displayMode == 'input')" class="general-button blue" @click="sendResult(item.schedule_id)">送出</button>
              <button v-if="(gameData.module == 'ln' || gameData.module == 'rd') && item.status == 4 && (props.displayMode == 'result' || props.displayMode == 'input')" class="general-button blue" @click="retriveResult(item.schedule_id)">撤回</button>
              <button v-if="(gameData.module == 'ln' || gameData.module == 'rd') && item.status > 3 && (props.displayMode == 'print' || props.displayMode == 'award') && item.round == 4" class="general-button blue" @click="openEvent(item, 4)">獎狀列印</button>
            </div>
          </td>
        </tr>
      </template>
    </table>
    <SmallLoader v-show="isLoading"></SmallLoader>
  </div>
  <FullModal v-show="displayModal > 0 && displayModal < 4" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">查看</div>
        <div v-if="displayModal == 2">檢錄</div>
        <div v-if="displayModal == 3">成績</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <AthleteList v-if="displayModal == 1" :input-data="selectedEvent" display-mode="view"></AthleteList>
        <AthleteList v-if="displayModal == 2" :input-data="selectedEvent" display-mode="call" @close-modal="displayModal = 0" @refresh-page="getData()"></AthleteList>
        <ResultList v-if="displayModal == 3" :input-data="selectedEvent" display-mode="input" @close-modal="displayModal = 0" @refresh-page="getData()"></ResultList>
      </div>
    </template>
  </FullModal>
  <SmallModal v-show="displayModal == 4 || displayModal ==5" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 4">選擇列印模式</div>
        <div v-if="displayModal == 5">參考成績</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full" v-if="displayModal == 4">
        <div class="flex flex-col gap-3">
          <label class="round-input-label">
            <div class="title">選擇列印格式</div>
            <select class="select" v-model="printFormat">
              <template v-for="(item, index) in awardFormat" :key="index">
                <option :value="index">{{ item.title }}</option>
              </template>
              <option v-if="awardFormat.length == 0" value="NaN" disabled>尚未建立格式</option>
            </select>
          </label>
          <router-link class="round-full-button blue block text-center" :to="`/admin/game/${sportCode}/${gameId}/print/lane/${selectedEvent.schedule_id}/${selectedEvent.division_id}/${selectedEvent.event_code}/${selectedEvent.multiple}/${selectedEvent.round}/award/${printFormat}`" target="_blank">列印</router-link>
        </div>
      </div>
      <div v-if="displayModal == 5">
        <label class="round-input-label">
          <div class="title">組別</div>
          <input type="number" class="input" v-model="realtimeGroup">
        </label>
        <label class="round-input-label">
          <div class="title">即時參考成績</div>
          <input type="text" class="input" v-model="realtimeResult">
        </label>
        <button class="round-full-button blue mt-3" @click="setRealtimeResult">送出</button>
      </div>
    </template>
  </SmallModal>
</template>

<style scoped lang="scss">
.data-table {
  @apply w-[768px] md:w-full;
  td, th {
    @apply p-2 border-y-[1px] text-left;
  }
  tr:nth-child(even) {
    @apply bg-gray-100;
  }
}
</style>