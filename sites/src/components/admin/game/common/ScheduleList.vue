<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import SmallLoader from '@/components/SmallLoader.vue';
import FullModal from '@/components/FullModal.vue';
import AthleteList from '@/components/admin/game/common/AthleteList.vue';
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

const scheduleList: any = ref([]);
async function getData() {
  isLoading.value = true;
  await vr.Get(`game/${sportCode}/${gameId}/common/schedule/full`, scheduleList, true, true);
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
        data.push([res[j].u_id, res[j][`r${[input.round]}_lane`], res[j].first_name_ch, res[j].last_name_ch, res[j].org_name_ch]);
      }
    }
    if (i < maxHeat - 1) {
      data.push(['#', '', '', '', '']);
    }
  }
  console.log(data);
  exportCsv(data, `${input.division_ch}_${input.event_ch}_${lanePhaseToString(input.round, 'zh-TW')}`, null);
}
</script>

<template>
  <div class="h-full w-full overflow-auto">
    <table class="data-table">
      <template v-for="(item, index) in scheduleList" :key="index">
        <tr v-if="props.displayMode == 'management' || props.displayMode == 'result' || (props.displayMode == 'call' && item.status <= 1) || (props.displayMode == 'input' && item.status > 1 && item.status < 4)">
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
              <button v-if="gameData.module == 'ln' || gameData.module == 'rd' && item.status < 2" class="general-button blue" @click="openEvent(item, 2)">檢錄</button>
              <button v-if="gameData.module == 'ln'" class="general-button blue" @click="exportData(item)">電計下載</button>
            </div>
          </td>
        </tr>
      </template>
    </table>
    <SmallLoader v-show="isLoading"></SmallLoader>
  </div>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">查看</div>
        <div v-if="displayModal == 2">檢錄</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <AthleteList v-if="displayModal == 1" :input-data="selectedEvent" display-mode="view"></AthleteList>
        <AthleteList v-if="displayModal == 2" :input-data="selectedEvent" display-mode="call"></AthleteList>
      </div>
    </template>
  </FullModal>
</template>

<style scoped lang="scss">
.data-table {
  @apply w-[768px] md:w-full h-full;
  td, th {
    @apply p-2 border-y-[1px] text-left;
  }
  tr:nth-child(even) {
    @apply bg-gray-100;
  }
}
</style>