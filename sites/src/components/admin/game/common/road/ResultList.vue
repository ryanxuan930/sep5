<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import { csvToArrayTable, lanePhaseToString, stringToMilliseconds } from '@/components/library/functions';
import FullModal from '@/components/FullModal.vue';
import UploadPreview from '../../result/lane/UploadPreview.vue';
import SmallLoader from '@/components/SmallLoader.vue';


console.log('ResultList');
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
  /*
  const temp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/temp/gameRecords`);
  const records = JSON.parse(temp.temp_data);
  records.forEach((item: any) => {
    if (item.division_id == props.inputData.division_id && item.event_code == props.inputData.event_code) {
      recordList.value = item;
    }
  });
  */
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/params/${props.inputData.division_id}/${props.inputData.event_code}`, paramsList, true, true);
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/params/full`, paramsFullList, true, true);
  for (let i = 0; i < dataList.value.length; i++){
    if (dataList.value[i].result == 'OK') {
      dataList.value[i].result = '';
    }
    dataList.value[i].options = JSON.parse(dataList.value[i].options);
    if (dataList.value[i].options.remark == undefined) {
      dataList.value[i].options.remark = '';
    }
  }
  dataList.value.sort((a: any, b: any) => a.bib - b.bib);
  isLoading.value = false;
})();
const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
const close = () => {
  emit('refreshPage');
  emit('closeModal');
}

async function submitAll(rank = true) {
  isLoading.value = true;
  let res: any = null;
  const dataset: any = [];
  const notAcceptResult = [null, 'null', 'DQ', 'DNS', 'DNF', 'NM', undefined, '', ' '];
  // result to temp (milliseconds or centimeters)
  for (let i = 0; i < dataList.value.length; i++){
    if (!notAcceptResult.includes(dataList.value[i].result)) {
      dataList.value[i].temp = stringToMilliseconds(dataList.value[i].result);
    } else {
      dataList.value[i].temp = 0;
    }
    dataList.value[i].options.qualified = '*';
    dataList.value[i].options.break = null;
  }
  // sort by heat and temp
  dataList.value.sort((a: any, b: any) => a.temp - b.temp);

  // q, break record and ranking
  let ranking = 1;
  let temp = 0;
  let counter = 0;
  for(let i = 0; i < dataList.value.length; i++){
    if (dataList.value[i].temp > 0 && counter < paramsList.value.qualified ) {
      dataList.value[i].options.qualified = 'q';
      counter++;
    }
    if (rank == true) {
      if (dataList.value[i].temp == 0) {
        dataList.value[i].ranking = 0;
      } else {
        if (dataList.value[i].temp == temp) {
          dataList.value[i].ranking = dataList.value[i - 1].ranking;
        } else {
          dataList.value[i].ranking = ranking;
        }
        ranking++;
      }
    }
    if (dataList.value[i].options.qualified != 'q') {
      dataList.value[i].options.qualified = '*';
    }
    // put record condition here
    dataList.value[i].options.record = 'no';
    temp = dataList.value[i].temp;
  }

  dataList.value.forEach((item: any) => {
    dataset.push({
      u_id: item.u_id,
      division_id: item.division_id,
      event_code: item.event_code,
      result: item.result,
      ranking: item.ranking,
      options: JSON.stringify(item.options),
    });
  });
  res = await vr.Patch(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/update/road-result`, dataset, null, true, true);
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

function importHandler(input: any) {
  for(let i = 0; i < input.length; i++) {
    for(let j = 0; j < dataList.value.length; j++) {
      if (Number(dataList.value[j].bib) == Number(input[i][1])) {
        dataList.value[j].result = input[i][2];
      }
    }
  }
  alert(`完成匯入${fileName.value}`);
  fileName.value = '';
  uploadEntity.value = null;
}

function uploadFile(event: any) {
  isLoading.value = true;
  fileName.value = event.target.files[0].name;
  const reader = new FileReader();
  reader.onload = function (e: any) {
    importHandler(csvToArrayTable(e.target.result, ',', 1));
    isLoading.value = false;
    return;
  };
  reader.readAsText(event.target.files[0]);
}
const resultRef: any = ref([]);
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
      <label class="general-button blue" v-if="!['fj', 'ft'].includes(props.inputData.remarks)">
        成績匯入
        <input type="file" class="hidden" ref="uploadEntity" accept=".csv" @change="uploadFile">
      </label>
      <div class="flex-grow"></div>
      <div class="text-xs text-right">
        <div>成績「務必」以 hh:mm:ss, mm:ss.vvv, ss.vvv, MM.cc 格式輸入</div>
        <div>輸入數字按Enter會自動格式化，輸入D、F、Q、M再按Enter會分別帶入DNS、DNF、DQ、NM</div>
      </div>
    </div>
    <table>
      <tr>
        <th>組織單位</th>
        <th>系所/分部</th>
        <th>姓名</th>
        <th>成績</th>
        <th>備註</th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr>
          <td>{{ item.org_name_full_ch }}</td>
          <td>{{ item.dept_name_ch }}</td>
          <td>
            <span v-if="item.bib != undefined && item.bib != null">{{ item.bib }} </span>
            {{ item.last_name_ch }}{{ item.first_name_ch }}
          </td>
          <td>
            <input type="text" class="p-1 rounded border-2" @keyup.down="moveTo(index, 'down')" @keyup.up="moveTo(index, 'up')" @keyup.enter="autoFormatter(item, 'result')" ref="resultRef" v-model="item.result">
          </td>
          <td>
            <input type="text" class="p-1 rounded border-2 w-16" ref="remarkRef" v-model="item.options.remark">
          </td>
        </tr>
      </template>
    </table>
    <div class="py-3">
      <button class="round-full-button blue" @click="submitAll(true)">儲存</button>
    </div>
  </div>
  <SmallLoader v-show="isLoading"></SmallLoader>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">成績匯入</div>
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