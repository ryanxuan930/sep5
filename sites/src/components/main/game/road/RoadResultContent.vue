<script setup lang="ts">
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'
import Config from '@/assets/config.json'

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
  const res = await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/individual/by/event/${divisionId}/${eventCode}`);
  const temp1: any = [];
  const temp2: any = [];
  res.forEach((element: any) => {
    element.options = JSON.parse(element.options);
    if (element.ranking > 0) {
      temp1.push(element);
    } else {
      temp2.push(element);
    }
  });
  temp1.sort((a: any, b: any) => a.ranking- b.ranking);
  dataList.value = temp1.concat(temp2);
  isLoading.value = false;
};
getData();

let interval: number;

onMounted(() => {
  if (route.query.status == undefined || route.query.status != '4') {
    interval = setInterval(() => {
      getData();
    }, 30000);
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
        <div class="text-xl text-blue-500">{{ params.division_ch }} {{ params.event_ch }}</div>
        <div class="text-base text-blue-500">{{ params.division_en }} {{ params.event_en }}</div>
      </div>
      <div class="flex-grow"></div>
      <div class="text-right">
        <div>總數 Total：{{ dataList.length }}</div>
        <div>q：{{ params.qualified }}</div>
      </div>
    </div>
    <table>
      <tr>
        <th>
          <div>名次</div>
          <div class="text-sm">Place</div>
        </th>
        <th v-if="!Config.deptAsClass">
          <div>組織單位</div>
          <div class="text-sm">Organization</div>
        </th>
        <template v-if="gameData.options.regUnit < 2">
          <th v-if="Config.deptAsClass">
            <div>班級</div>
            <div class="text-sm">Class</div>
          </th>
          <th v-else>
            <div>分部/系所</div>
            <div class="text-sm">Department</div>
          </th>
        </template>
        <th v-if="Config.deptAsClass">
          <div>座號</div>
          <div class="text-sm">No.</div>
        </th>
        <th>
          <div>姓名</div>
          <div class="text-sm">Name</div>
        </th>
        <th>
          <div>成績</div>
          <div class="text-sm">Result</div>
        </th>
        <th>
          <div>備註</div>
          <div class="text-sm">Remarks</div>
        </th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr :class="{'bg-sky-50': item.options.qualified != undefined && item.options.qualified =='q'}">
          <td>
            <div v-if="item.ranking > 0">{{ item.ranking }}</div>
          </td>
          <td v-if="!Config.deptAsClass">
            <div>{{ item.org_name_full_ch }}</div>
            <div class="text-sm">{{ item.org_name_en }}</div>
          </td>
          <td v-if="gameData.options.regUnit < 2">
            <div>{{ item.dept_name_ch }}</div>
            <div class="text-sm">{{ item.dept_name_en }}</div>
          </td>
          <td v-if="Config.deptAsClass">{{ item.num_in_dept }}</td>
          <td>{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
          <td>{{ item.result }}</td>
          <td>
            <div class="flex items-center gap-3">
              <div v-if="item.options.qualified != undefined">{{ item.options.qualified }}</div>
            </div>
            <div class="italic" v-if="item.options.remark != undefined">{{ item.options.remark }}</div>
          </td>
        </tr>
      </template>
    </table>
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