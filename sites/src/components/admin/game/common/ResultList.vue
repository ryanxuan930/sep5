<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import { lanePhaseToString } from '@/components/library/functions';
import FullModal from '@/components/FullModal.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const displayModal = ref(0);
const props = defineProps(['inputData', 'displayMode']);
const gameData: any = inject('gameData');
const dataList: any = ref([]);
const isLoading = ref(false);
(async () => {
  isLoading.value = true;
  if (props.inputData.multiple == 0){
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
  } else {
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
  }
  dataList.value.sort((a: any, b: any) => a[`r${[props.inputData.round]}_heat`] - b[`r${[props.inputData.round]}_heat`] || a[`r${[props.inputData.round]}_lane`] - b[`r${[props.inputData.round]}_lane`]);
  isLoading.value = false;
})();
async function submitAll() {
  let res: any = null;
  const dataset: any = [];
  if (props.inputData.multiple == 0){
    dataList.value.forEach((item: any) => {
      dataset.push({
        u_id: item.u_id,
        division_id: item.division_id,
        event_code: item.event_code,
        phase: `r${props.inputData.round}`,
        result: item[`r${props.inputData.round}_result`],
        ranking: item[`r${props.inputData.round}_ranking`],
        options: item[`r${props.inputData.round}_options`],
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
        options: item[`r${props.inputData.round}_options`],
      });
    });
    res = await vr.Patch(`game/${route.params.sportCode}/${route.params.gameId}/common/group/update/result`, dataset, null, true, true);
  }
  if (res.status == 'A01') {
    const r: any = await vr.Post(`game/${route.params.sportCode}/${route.params.gameId}/common/schedule/update/${props.inputData.schedule_id}`, {status: 1}, null, true, true);
    if (r.status == 'A01') {
      alert('已儲存');
    } else {
      alert('儲存失敗');
    }
  } else {
    alert('儲存失敗');
  }
}
const selectedData: any = ref([]);
</script>

<template>
  <div v-if="isLoading == false">
    <div class="py-3 text-xl">
      <span>{{ props.inputData.timestamp }} {{ props.inputData.division_ch }}-{{ props.inputData.event_ch }}</span>
      <span v-if="gameData.module == 'ln'"> [{{ lanePhaseToString(props.inputData.round, 'zh-TW') }}]</span>
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
        </tr>
      </template>
    </table>
    <div class="py-3">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">詳細紀錄</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
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