<script setup lang="ts">
import { ref } from 'vue';
import type { Ref } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import Config from '@/assets/config.json';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const props = defineProps(['inputData', 'playerNum', 'type']);
const dataList: any = ref([]);
const idList: Ref<number[]> = ref([]);
const firstName: any = ref('');
const lastName: any = ref('');
const bib: any = ref('');
const nextRef: any = ref(null);
const searchResult: any = ref([]);
(async () => {
  if (props.inputData.member_list != null && props.inputData.member_list.length > 0) {
    const res = await vr.Post(`user-from-list`, {data: props.inputData.member_list}, null, true, true);
    for (let i = 0; i < props.playerNum; i++) {
      if (res[i] != undefined || res[i] != null) {
        dataList.value.push(res[i]);
        idList.value.push(res[i].u_id);
      } else {
        dataList.value.push(null);
        idList.value.push(0);
      }
    }
  } else {
    for (let i = 0; i < props.playerNum; i++) {
      dataList.value.push(null);
      idList.value.push(0);
    }
  }
})();
async function inputHandler(index: number) {
  const valid = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/athlete/find/${idList.value[index]}`, null, true, true);
  if (valid || props.type !== undefined) {
    const res = await vr.Get(`user/${idList.value[index]}`, null, true, true);
    dataList.value[index] = res;
  } else {
    alert('此選手未報名');
  }
}
async function searchName() {
  if (firstName.value.length > 0 && lastName.value.length > 0) {
    await vr.Get(`user/name/${firstName.value}/${lastName.value}`, searchResult, true, true);
  } else {
    alert('請輸入全名');
  }
}
async function searchBib() {
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/athlete/bib/${bib.value}`, searchResult, true, true);
}
const emit = defineEmits<{(e: 'returnData', value: string): void, (e: 'closeModal'): void, (e: 'returnList', value: any): void}>();
async function submitAll() {
  for(let i = 0; i < idList.value.length; i++) {
    idList.value[i] = Number(idList.value[i]);
  }
  const data = JSON.stringify(idList.value);
  if (props.inputData.member_list != null && props.inputData.member_list.length > 0) {
      const res = await vr.Patch(`game/${route.params.sportCode}/${route.params.gameId}/common/group/update/team/${props.inputData.team_id}`, {member_list: data}, null, true, true);
    if (res.status == 'A01') {
      alert('已儲存');
      emit('returnData', data);
      emit('returnList', dataList.value);
      emit('closeModal');
    } else {
      alert('儲存失敗');
    }
  } else {
    emit('returnData', data);
    emit('returnList', dataList.value);
    emit('closeModal');
  }
}
</script>

<template>
  <div class="relative">
    <div class="py-3 flex gap-3">
      <label class="round-input-label basis-2/5">
        <input type="text" class="input" v-model="lastName" placeholder="姓氏" @keyup.enter="nextRef.focus()">
      </label>
      <label class="round-input-label basis-2/5">
        <input type="text" class="input" v-model="firstName" placeholder="名字" ref="nextRef" @keyup.enter="searchName">
      </label>
      <button class="round-full-button blue basis-1/5" @click="searchName">搜尋</button>
    </div>
    <div class="py-3 flex gap-3">
      <label class="round-input-label basis-4/5">
        <input type="text" class="input" v-model="bib" placeholder="號碼布" @keyup.enter="searchBib">
      </label>
      <button class="round-full-button blue basis-1/5" @click="searchBib">搜尋</button>
    </div>
    <div v-if="searchResult.length > 0" class="bg-white bg-opacity-95 shadow-lg p-5 absolute rounded w-full text-lg border-[1px]">
      <div class="flex items-center">
        <div>搜尋結果</div>
        <div class="flex-grow"></div>
        <a class="hyperlink blue" @click="searchResult = []">清除</a>
      </div>
      <hr class="my-1">
      <div v-for="(item, index) in searchResult" :key="index">
        <div>編號：{{ item.u_id }}</div>
        <div>姓名：{{ item.last_name_ch }}{{ item.first_name_ch }}</div>
        <div>性別：{{ item.sex == 1 ? '男':'女' }}</div>
        <div>組織單位：{{ item.org_name_full_ch }}</div>
        <div>系所/分部：{{ item.dept_name_ch }}</div>
        <hr class="my-1">
      </div>
    </div>
    <table>
      <tr>
        <th class="text-left p-2 bg-blue-100">棒次</th>
        <th class="text-left p-2 bg-blue-100">系統代碼</th>
        <th class="text-left p-2 bg-blue-100">選手</th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr>
          <td>第{{ index+1 }}棒</td>
          <td>
            <input class="border-2 p-1 rounded w-16" v-model="idList[index]" @change="inputHandler(index)" type="text" maxlength="8">
          </td>
          <td v-if="item != null">[{{ item.org_name_ch }}<span v-if="Config.deptAsClass">{{ item.dept_name_ch }}</span>] {{ item.last_name_ch }}{{ item.first_name_ch }} ({{ item.sex == 1? '男':'女' }})</td>
          <td v-else></td>
        </tr>
      </template>
    </table>
    <div class="py-3">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-full;
  td {
    @apply p-2 text-left bg-gray-50;
  }
  tr:nth-child(odd) {
    td {
      @apply bg-blue-50;
    }
  }
}
</style>