<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import { lanePhaseToString } from '@/components/library/functions';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const props = defineProps(['inputData', 'displayMode']);
const gameData: any = inject('gameData');
const dataList: any = ref([]);
if (props.inputData.multiple == 0){
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
} else {
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
}
async function submitAll() {
  let res: any = null;
  if (props.inputData.multiple == 0){
    res = await vr.Patch(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/update/result`, dataList.value, null, true, true);
  } else {
    res = await vr.Patch(`game/${route.params.sportCode}/${route.params.gameId}/common/group/update/result`, dataList.value, null, true, true);
  }
  if (res.status == 'A01') {
    const r: any = await vr.Patch(`game/${route.params.sportCode}/${route.params.gameId}/common/schedule/update/${props.inputData.schedule_id}`, {status: 1}, null, true, true);
    if (r.status == 'A01') {
      alert('已儲存');
    } else {
      alert('儲存失敗');
    }
  } else {
    alert('儲存失敗');
  }
}
</script>

<template>
  <div>
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
        <th v-if="props.displayMode == 'call'">檢錄</th>
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
          <td v-if="props.displayMode == 'call'">
            <div class="select-box">
              <div :class="{'item': true, 'active': item[`r${props.inputData.round}_result`] == '0'}" @click="item[`r${props.inputData.round}_result`] = '0'">
                <div>正常<br>出賽</div>
              </div>
              <div :class="{'item': true, 'active': item[`r${props.inputData.round}_result`] == 'DNS'}" @click="item[`r${props.inputData.round}_result`] = 'DNS'">
                <div>事前<br>請假</div>
              </div>
              <div :class="{'item': true, 'active': item[`r${props.inputData.round}_result`] == 'DQ'}" @click="item[`r${props.inputData.round}_result`] = 'DQ'">
                <div>無故<br>棄賽</div>
              </div>
            </div>
          </td>
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
      @apply bg-blue-500 text-white;
    }
  }
}
</style>