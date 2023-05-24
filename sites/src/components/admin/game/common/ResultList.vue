<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import { csvToArrayTable, lanePhaseToString, stringToMilliseconds } from '@/components/library/functions';
import FullModal from '@/components/FullModal.vue';
import UploadPreview from '../result/lane/UploadPreview.vue';
import SmallLoader from '@/components/SmallLoader.vue';
import EditEvent from '../schedule/arrange/EditEvent.vue';
import LegsList from '@/components/admin/game/management/LegsList.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const displayModal = ref(0);
const props = defineProps(['inputData', 'displayMode']);
const gameData: any = inject('gameData');
const dataList: any = ref([]);
const paramsList: any = ref({});
const paramsFullList: any = ref({});
const recordList: any = ref({});
const selectedData: any = ref({});
const isLoading = ref(false);
(async () => {
  isLoading.value = true;
  if (props.inputData.multiple == 0){
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
  } else {
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
  }
  const temp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/temp/gameRecords`);
  const records = JSON.parse(temp.temp_data);
  records.forEach((item: any) => {
    if (item.division_id == props.inputData.division_id && item.event_code == props.inputData.event_code) {
      recordList.value = item;
    }
  });
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/params/${props.inputData.division_id}/${props.inputData.event_code}`, paramsList, true, true);
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/params/full`, paramsFullList, true, true);
  for (let i = 0; i < dataList.value.length; i++){
    if (dataList.value[i][`r${props.inputData.round}_result`] == 'OK') {
      dataList.value[i][`r${props.inputData.round}_result`] = '';
    }
    dataList.value[i][`r${props.inputData.round}_options`] = JSON.parse(dataList.value[i][`r${props.inputData.round}_options`]);
    if (dataList.value[i][`r${props.inputData.round}_options`].rt == undefined) {
      dataList.value[i][`r${props.inputData.round}_options`].rt = 0;
    }
    if (dataList.value[i][`r${props.inputData.round}_options`].windspeed == undefined) {
      dataList.value[i][`r${props.inputData.round}_options`].windspeed = 'NWI';
    }
    if (dataList.value[i][`r${props.inputData.round}_options`].remark == undefined) {
      dataList.value[i][`r${props.inputData.round}_options`].remark = '';
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

async function tempSave() {
  let res: any = null;
  const dataset: any = [];
  const timeEvents = ['ts', 'tr', 'tn', 'rr'];
  const notAcceptResult = [null, 'null', 'DQ', 'DNS', 'DNF', 'NM', undefined, ''];
  // result to temp (milliseconds or centimeters)
  for (let i = 0; i < dataList.value.length; i++){
    if (!notAcceptResult.includes(dataList.value[i][`r${props.inputData.round}_result`])) {
      if (timeEvents.includes(props.inputData.remarks)) {
        console.log(dataList.value[i][`r${props.inputData.round}_result`]);
        dataList.value[i].temp = stringToMilliseconds(dataList.value[i][`r${props.inputData.round}_result`]);
      } else {
        dataList.value[i].temp = dataList.value[i][`r${props.inputData.round}_result`] * 100;
      }
    } else {
      dataList.value[i].temp = 0;
    }
    dataList.value[i][`r${props.inputData.round}_ranking`] = 0;
    dataList.value[i][`r${props.inputData.round}_options`].qualified = '*';
    dataList.value[i][`r${props.inputData.round}_options`].windspeed = 'NWI';
    dataList.value[i][`r${props.inputData.round}_options`].rt = 0;
    dataList.value[i][`r${props.inputData.round}_options`].break = null;
    dataList.value[i][`r${props.inputData.round}_options`].cr = false;
    dataList.value[i][`r${props.inputData.round}_options`].nr = false;
    dataList.value[i][`r${props.inputData.round}_options`].remark = '';
  }

  // record handler
  let flag = false;
  for(let i = 0; i < dataList.value.length; i++){
    if (!notAcceptResult.includes(dataList.value[i][`r${props.inputData.round}_result`])) {
      if (timeEvents.includes(props.inputData.remarks)) {
        if (stringToMilliseconds(recordList.value.result) > dataList.value[i].temp) {
          dataList.value[i][`r${props.inputData.round}_options`].break = '破大會紀錄';
          if (flag == false) {
            dataList.value[i][`r${props.inputData.round}_options`].cr = true;
            flag = true;
          }
        }
        if (recordList.value.result == 0 && flag == false) {
          dataList.value[i][`r${props.inputData.round}_options`].break = '創大會紀錄';
          dataList.value[i][`r${props.inputData.round}_options`].cr = true;
          flag = true;
        }
      } else {
        if (recordList.value.result == 0) {
          if (flag == false) {
            dataList.value[i][`r${props.inputData.round}_options`].break = '創大會紀錄';
            dataList.value[i][`r${props.inputData.round}_options`].cr = true;
            flag = true;
          }
        } else if (Number(recordList.value.result)*100 < dataList.value[i].temp) {
          dataList.value[i][`r${props.inputData.round}_options`].break = '破大會紀錄';
          if (flag == false) {
            dataList.value[i][`r${props.inputData.round}_options`].cr = true;
            flag = true;
          }
        }
      }
    }
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
      alert('已暫存');
    } else {
      alert('暫存失敗');
    }
  } else {
    alert('暫存失敗');
  }
}

async function submitAll() {
  isLoading.value = true;
  let res: any = null;
  const dataset: any = [];
  const timeEvents = ['ts', 'tr', 'tn', 'rr'];
  const notAcceptResult = [null, 'null', 'DQ', 'DNS', 'DNF', 'NM', undefined, ''];
  // result to temp (milliseconds or centimeters)
  for (let i = 0; i < dataList.value.length; i++){
    if (!notAcceptResult.includes(dataList.value[i][`r${props.inputData.round}_result`])) {
      if (timeEvents.includes(props.inputData.remarks)) {
        console.log(dataList.value[i][`r${props.inputData.round}_result`]);
        dataList.value[i].temp = stringToMilliseconds(dataList.value[i][`r${props.inputData.round}_result`]);
      } else {
        dataList.value[i].temp = dataList.value[i][`r${props.inputData.round}_result`] * 100;
      }
    } else {
      dataList.value[i].temp = 0;
    }
    dataList.value[i][`r${props.inputData.round}_options`].qualified = '*';
    dataList.value[i][`r${props.inputData.round}_options`].windspeed = 'NWI';
    dataList.value[i][`r${props.inputData.round}_options`].rt = 0;
    dataList.value[i][`r${props.inputData.round}_options`].break = null;
    dataList.value[i][`r${props.inputData.round}_options`].cr = false;
    dataList.value[i][`r${props.inputData.round}_options`].nr = false;
    dataList.value[i][`r${props.inputData.round}_options`].remark = '';
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
  
  // record handler
  let flag = false;
  for(let i = 0; i < dataList.value.length; i++){
    if (!notAcceptResult.includes(dataList.value[i][`r${props.inputData.round}_result`])) {
      if (timeEvents.includes(props.inputData.remarks)) {
        if (stringToMilliseconds(recordList.value.result) > dataList.value[i].temp) {
          dataList.value[i][`r${props.inputData.round}_options`].break = '破大會紀錄';
          if (flag == false) {
            dataList.value[i][`r${props.inputData.round}_options`].cr = true;
            flag = true;
          }
        }
        if (recordList.value.result == 0 && flag == false) {
          dataList.value[i][`r${props.inputData.round}_options`].break = '創大會紀錄';
          dataList.value[i][`r${props.inputData.round}_options`].cr = true;
          flag = true;
        }
      } else {
        if (recordList.value.result == 0) {
          if (flag == false) {
            dataList.value[i][`r${props.inputData.round}_options`].break = '創大會紀錄';
            dataList.value[i][`r${props.inputData.round}_options`].cr = true;
            flag = true;
          }
        } else if (Number(recordList.value.result)*100 < dataList.value[i].temp) {
          dataList.value[i][`r${props.inputData.round}_options`].break = '破大會紀錄';
          if (flag == false) {
            dataList.value[i][`r${props.inputData.round}_options`].cr = true;
            flag = true;
          }
        }
      }
    }
  }

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

async function multiRanking(input: number[]) {
  dataList.value = [];
  for (let i = 0; i < input.length; i++) {
    const index = input[i];
    let temp: any = null;
    if(paramsFullList.value[index].multiple == 1){
      temp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/event/${paramsFullList.value[index].division_id}/${paramsFullList.value[index].event_code}`, null, true, true);
    } else {
      temp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${paramsFullList.value[index].division_id}/${paramsFullList.value[index].event_code}`, null, true, true);
    }
    dataList.value = dataList.value.concat(temp);
  }
  for (let i = 0; i < dataList.value.length; i++){
    if (dataList.value[i][`r${props.inputData.round}_result`] == 'OK') {
      dataList.value[i][`r${props.inputData.round}_result`] = '';
    }
    dataList.value[i][`r${props.inputData.round}_options`] = JSON.parse(dataList.value[i][`r${props.inputData.round}_options`]);
    if (dataList.value[i][`r${props.inputData.round}_options`].rt == undefined) {
      dataList.value[i][`r${props.inputData.round}_options`].rt = 0;
    }
    if (dataList.value[i][`r${props.inputData.round}_options`].windspeed == undefined) {
      dataList.value[i][`r${props.inputData.round}_options`].windspeed = 'NWI';
    }
    if (dataList.value[i][`r${props.inputData.round}_options`].remark == undefined) {
      dataList.value[i][`r${props.inputData.round}_options`].remark = '';
    }
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
const resultRef: any = ref([]);
const rtRef: any = ref([]);
const wsRef: any = ref([]);
const remarkRef: any = ref([]);
function moveTo(index: number, key: string) {
  if (key == 'down') {
    if ((index + 1) < dataList.value.length) {
      resultRef.value[index + 1].focus();
    }
  }
  if (key == 'up') {
    if ((index - 1) > 0) {
      resultRef.value[index - 1].focus();
    }
  }
}
function autoFormatter(input: any, index: string) {

  const val = input[index];
  if (val.length > 0 && !val.includes('.') && !val.includes(':')) {
    if (val.toUpperCase() == 'D' || val.toUpperCase() == 'S') {
      input[index] = 'DNS';
    } else if (val.toUpperCase() == 'F') {
      input[index] = 'DNF';
    } else if (val.toUpperCase() == 'Q') {
      input[index] = 'DQ';
    } else if (val.toUpperCase() == 'M') {
      input[index] = 'NM';
    } else if (paramsList.value.unit == 'T') {
      if (val.length == 3) {
        input[index] = val.substring(0, 1) + '.' + val.substring(1, 3);
      } else if (val.length == 4) {
        input[index] = val.substring(0, 2) + '.' + val.substring(2, 4);
      } else if (val.length == 5) {
        input[index] = val.substring(0, 1) + ':' + val.substring(1, 3) + '.' + val.substring(3, 5);
      } else if (val.length == 6) {
        input[index] = val.substring(0, 2) + ':' + val.substring(2, 4) + '.' + val.substring(4, 6);
      }
    } else if (paramsList.value.unit == 'D') {
      if (val.length == 3) {
        input[index] = val.substring(0, 1) + '.' + val.substring(1, 3);
      } else if (val.length == 4) {
        input[index] = val.substring(0, 2) + '.' + val.substring(2, 4);
      }
    } else {
      alert('無法自動格式化');
    }
    console.log(input[index]);
    return;
  }
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
      <button class="general-button blue" @click="displayModal = 4">成績聯合處理</button>
      <button class="general-button blue" @click="tempSave">暫存成績</button>
      <div class="flex-grow"></div>
      <div class="text-xs text-right">
        <div>成績「務必」以 hh:mm:ss, mm:ss.vvv, ss.vvv, MM.cc 格式輸入</div>
        <div>風速以 +/- 0.00 輸入，若無風速計則輸入NWI；反應時間以 0.000 輸入</div>
        <div>輸入數字按Enter會自動格式化，輸入D、F、Q、M再按Enter會分別帶入DNS、DNF、DQ、NM</div>
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
        <th>備註</th>
        <th v-if="props.inputData.multiple == 1">棒次</th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr v-show="item[`r${props.inputData.round}_heat`] > 0 && item[`r${props.inputData.round}_lane`] > 0">
          <td>{{ item.org_name_full_ch }}</td>
          <td>{{ item.dept_name_ch }}</td>
          <td v-if="props.inputData.multiple == 0">{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
          <td v-else>{{ item.team_name }}</td>
          <template v-if="gameData.module == 'ln'">
            <td>{{ item[`r${props.inputData.round}_heat`] }}</td>
            <td>{{ item[`r${props.inputData.round}_lane`] }}</td>
          </template>
          <td>
            <input type="text" class="p-1 rounded border-2" @keyup.down="moveTo(index, 'down')" @keyup.up="moveTo(index, 'up')" @keyup.enter="autoFormatter(item, `r${props.inputData.round}_result`)" ref="resultRef" v-model="item[`r${props.inputData.round}_result`]">
          </td>
          <td>
            <input type="text" class="p-1 rounded border-2 w-16" ref="rtRef" v-model="item[`r${props.inputData.round}_options`].rt">
          </td>
          <td>
            <input type="text" class="p-1 rounded border-2 w-16" ref="wsRef" v-model="item[`r${props.inputData.round}_options`].windspeed">
          </td>
          <td>
            <input type="text" class="p-1 rounded border-2 w-16" ref="remarkRef" v-model="item[`r${props.inputData.round}_options`].remark">
          </td>
          <td v-if="props.inputData.multiple == 1">
            <button class="general-button blue" @click="() => { displayModal = 3; selectedData = item;}">棒次</button>
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
        <div v-if="displayModal == 3">棒次</div>
        <div v-if="displayModal == 4">成績聯合處理</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <UploadPreview v-if="displayModal == 1" :input-data="uploadData" @closeModal="displayModal = 0" @returnData="(res: any) => {importHandler(res);}"></UploadPreview>
        <LegsList v-if="displayModal == 3" :input-data="selectedData" :player-num="props.inputData.player_num" @closeModal="displayModal = 0" @returnData="(res: any) => selectedData.member_list = res"></LegsList>
        <EditEvent v-if="displayModal == 4" :input-data="paramsFullList" :current-event="paramsList" :phase-num="props.inputData.round" @returnData="multiRanking" @closeModal="displayModal = 0"></EditEvent>
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