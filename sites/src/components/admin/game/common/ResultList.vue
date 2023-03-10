<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import { csvToArrayTable, lanePhaseToString, stringToMilliseconds } from '@/components/library/functions';
import FullModal from '@/components/FullModal.vue';
import UploadPreview from '../result/lane/UploadPreview.vue';
import SmallLoader from '@/components/SmallLoader.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const displayModal = ref(0);
const props = defineProps(['inputData', 'displayMode']);
const gameData: any = inject('gameData');
const dataList: any = ref([]);
const paramsList: any = ref({});
const isLoading = ref(false);
(async () => {
  isLoading.value = true;
  if (props.inputData.multiple == 0){
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
  } else {
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
  }
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/params/${props.inputData.division_id}/${props.inputData.event_code}`, paramsList, true, true);
  for (let i = 0; i < dataList.value.length; i++){
    dataList.value[i][`r${props.inputData.round}_options`] = JSON.parse(dataList.value[i][`r${props.inputData.round}_options`]);
    if (dataList.value[i][`r${props.inputData.round}_options`].rt == undefined) {
      dataList.value[i][`r${props.inputData.round}_options`].rt = 0;
    }
    if (dataList.value[i][`r${props.inputData.round}_options`].windspeed == undefined) {
      dataList.value[i][`r${props.inputData.round}_options`].windspeed = 0;
    }
  }
  dataList.value.sort((a: any, b: any) => a[`r${[props.inputData.round]}_heat`] - b[`r${[props.inputData.round]}_heat`] || a[`r${[props.inputData.round]}_lane`] - b[`r${[props.inputData.round]}_lane`]);
  isLoading.value = false;
})();
const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
const close = () => {
  emit('refreshPage');
  emit('closeModal');
}
async function submitAll() {
  isLoading.value = true;
  let res: any = null;
  const dataset: any = [];
  const timeEvents = ['ts', 'tr', 'tn', 'rr'];
  const notAcceptResult = [null, 'null', 'DQ', 'DNS', 'DNF', 'NM', undefined];
  // result to temp (milliseconds or centimeters)
  for (let i = 0; i < dataList.value.length; i++){
    if (!notAcceptResult.includes(dataList.value[i][`r${props.inputData.round}_result`])) {
      if (timeEvents.includes(props.inputData.remarks)) {
        dataList.value[i].temp = stringToMilliseconds(dataList.value[i][`r${props.inputData.round}_result`]);
      } else {
        dataList.value[i].temp = dataList.value[i][`r${props.inputData.round}_result`] * 100;
      }
    } else {
      dataList.value[i].temp = 0;
    }
    dataList.value[i][`r${props.inputData.round}_options`].qualified = '*';
    dataList.value[i][`r${props.inputData.round}_options`].windspeed = 0;
    dataList.value[i][`r${props.inputData.round}_options`].rt = 0;
    dataList.value[i][`r${props.inputData.round}_options`].break = 'no';
  }
  // sort by heat and temp
  if (timeEvents.includes(props.inputData.remarks)) {
    dataList.value.sort((a: any, b: any) => a[`r${props.inputData.round}_heat`] - b[`r${props.inputData.round}_heat`] || a.temp - b.temp);
  } else {
    dataList.value.sort((a: any, b: any) => a[`r${props.inputData.round}_heat`] - b[`r${props.inputData.round}_heat`] || b.temp - a.temp);
  }
  // Q
  let counter = 0;
  let heat = 0;
  for(let i = 0; i < dataList.value.length; i++){
    if (dataList.value[i][`r${props.inputData.round}_heat`] != heat) {
      heat = dataList.value[i][`r${props.inputData.round}_heat`];
      counter = 0;
    }
    if (dataList.value[i].temp > 0 && counter < paramsList.value[`r${props.inputData.round}_aq`]) {
      dataList.value[i][`r${props.inputData.round}_options`].qualified = 'Q';
      counter++;
    }
  }
  // sort by temp
  if (timeEvents.includes(props.inputData.remarks)) {
    dataList.value.sort((a: any, b: any) => a.temp - b.temp);
  } else {
    dataList.value.sort((a: any, b: any) => b.temp - a.temp);
  }
  /**
   * Put Record handler here
   */

  /**
   * 
   */

  // q, break record and ranking
  let ranking = 1;
  let temp = 0;
  counter = 0;
  for(let i = 0; i < dataList.value.length; i++){
    if (dataList.value[i].temp > 0 && counter < paramsList.value[`r${props.inputData.round}_sq`] && dataList.value[i][`r${props.inputData.round}_options`].qualified != 'Q') {
      dataList.value[i][`r${props.inputData.round}_options`].qualified = 'q';
      counter++;
    }
    if (dataList.value[i].temp == 0) {
      dataList.value[i][`r${props.inputData.round}_ranking`] = 0;
    } else {
      if (dataList.value[i].temp == temp) {
        dataList.value[i][`r${props.inputData.round}_ranking`] = dataList.value[i - 1][`r${props.inputData.round}_ranking`];
      } else {
        dataList.value[i][`r${props.inputData.round}_ranking`] = ranking;
      }
      ranking++;
    }
    if (dataList.value[i][`r${props.inputData.round}_options`].qualified != 'Q' && dataList.value[i][`r${props.inputData.round}_options`].qualified != 'q') {
      dataList.value[i][`r${props.inputData.round}_options`].qualified = '*';
    }
    // put record condition here
    dataList.value[i][`r${props.inputData.round}_options`].record = 'no';
    temp = dataList.value[i].temp;
  }

  if (props.inputData.multiple == 0){
    dataList.value.forEach((item: any) => {
      dataset.push({
        u_id: item.u_id,
        division_id: item.division_id,
        event_code: item.event_code,
        phase: `r${props.inputData.round}`,
        result: item[`r${props.inputData.round}_result`],
        ranking: item[`r${props.inputData.round}_ranking`],
        options: JSON.stringify(item[`r${props.inputData.round}_options`]),
      });
    });
    res = await vr.Patch(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/update/result`, dataset, null, true, true);
  } else {
    dataList.value.forEach((item: any) => {
      dataset.push({
        team_id: item.team_id,
        division_id: item.division_id,
        event_code: item.event_code,
        phase: `r${props.inputData.round}`,
        result: item[`r${props.inputData.round}_result`],
        ranking: item[`r${props.inputData.round}_ranking`],
        options: JSON.stringify(item[`r${props.inputData.round}_options`]),
      });
    });
    res = await vr.Patch(`game/${route.params.sportCode}/${route.params.gameId}/common/group/update/result`, dataset, null, true, true);
  }
  if (res.status == 'A01') {
    const r: any = await vr.Post(`game/${route.params.sportCode}/${route.params.gameId}/common/schedule/update/${props.inputData.schedule_id}`, {status: 3}, null, true, true);
    if (r.status == 'A01') {
      alert('已儲存');
      close();
    } else {
      alert('儲存失敗');
    }
  } else {
    alert('儲存失敗');
  }
}

const uploadEntity: any = ref(null);
const fileName = ref('');
const uploadData: any = ref([]);
function uploadFile(event: any) {
  isLoading.value = true;
  fileName.value = event.target.files[0].name;
  const reader = new FileReader();
  reader.onload = function (e: any) {
    uploadData.value = JSON.parse(JSON.stringify(csvToArrayTable(e.target.result, ',', 2)));
    displayModal.value = 1;
    isLoading.value = false;
    return;
  };
  reader.readAsText(event.target.files[0]);
}
function importHandler(input: any) {
  for(let i = 0; i < input.length; i++) {
    for(let j = 0; j < dataList.value.length; j++) {
      if (props.inputData.multiple == 1) {
        if (Number(dataList.value[j].team_id) == Number(input[i][1])) {
          dataList.value[j][`r${props.inputData.round}_result`] = input[i][5];
        }
      } else {
        if (Number(dataList.value[j].u_id) == Number(input[i][1])) {
          dataList.value[j][`r${props.inputData.round}_result`] = input[i][5];
        }
      }
    }
  }
  alert(`完成匯入${fileName.value}}`);
  fileName.value = '';
  uploadData.value = [];
  uploadEntity.value = null;
}
</script>

<template>
  <div v-if="isLoading == false">
    <div class="py-3 text-xl">
      <span>{{ props.inputData.timestamp }} {{ props.inputData.division_ch }}-{{ props.inputData.event_ch }}</span>
      <span v-if="gameData.module == 'ln'"> [{{ lanePhaseToString(props.inputData.round, 'zh-TW') }}]</span>
    </div>
    <div class="py-3 flex gap-3 items-center">
      <label class="general-button blue">
        電計成績匯入
        <input type="file" class="hidden" ref="uploadEntity" accept=".csv" @change="uploadFile">
      </label>
      <button class="general-button blue">詳細紀錄</button>
      <div class="flex-grow"></div>
      <div class="text-xs text-right">
        <div>成績「務必」以 hh:mm:ss, mm:ss.vvv, ss.vvv, MM.cc 格式輸入</div>
        <div>風速以 +/- 0.00 輸入 反應時間以 0.000 輸入</div>
      </div>
    </div>
    <table>
      <tr>
        <th>組織單位</th>
        <th>系所/分部</th>
        <th v-if="props.inputData.multiple == 0">姓名</th>
        <th v-else>隊名</th>
        <template v-if="gameData.module == 'ln'">
          <th>組別</th>
          <th>道次</th>
        </template>
        <th>成績</th>
        <th>RT (s)</th>
        <th>WS (m/s)</th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr>
          <td>{{ item.org_name_full_ch }}</td>
          <td>{{ item.dept_name_ch }}</td>
          <td v-if="props.inputData.multiple == 0">{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
          <td v-else>{{ item.team_name }}</td>
          <template v-if="gameData.module == 'ln'">
            <td>{{ item[`r${props.inputData.round}_heat`] }}</td>
            <td>{{ item[`r${props.inputData.round}_lane`] }}</td>
          </template>
          <td>
            <input type="text" class="p-1 rounded border-2" v-model="item[`r${props.inputData.round}_result`]">
          </td>
          <td>
            <input type="text" class="p-1 rounded border-2 w-16" v-model="item[`r${props.inputData.round}_options`].rt">
          </td>
          <td>
            <input type="text" class="p-1 rounded border-2 w-16" v-model="item[`r${props.inputData.round}_options`].windspeed">
          </td>
        </tr>
      </template>
    </table>
    <div class="py-3">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
  <SmallLoader v-show="isLoading"></SmallLoader>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">電計成績匯入</div>
        <div v-if="displayModal == 2">詳細紀錄</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <UploadPreview v-if="displayModal == 1" :input-data="uploadData" @closeModal="displayModal = 0" @returnData="(res: any) => {importHandler(res);}"></UploadPreview>
      </div>
    </template>
  </FullModal>
</template>

<style scoped lang="scss">
table {
  @apply w-[640px] sm:w-full;
  th, td {
    @apply border border-gray-300 p-2 text-left;
  }
  th {
    @apply bg-blue-100 sticky top-0;
  }
  tr:nth-child(even) {
    @apply bg-gray-50;
  }
}
.select-box {
  @apply grid grid-cols-3 items-stretch text-center;
  .item {
    @apply p-1 bg-gray-100 hover:bg-blue-400 hover:text-white cursor-pointer duration-150;
    &.active {
      @apply bg-blue-500 text-white shadow;
    }
  }
}
</style>