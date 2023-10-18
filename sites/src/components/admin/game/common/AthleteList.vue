<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import { lanePhaseToString } from '@/components/library/functions';
import SmallModal from '@/components/SmallModal.vue';
import LegsList from '@/components/admin/game/management/LegsList.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const displayModal = ref(0);
const props = defineProps(['inputData', 'displayMode']);
const gameData: any = inject('gameData');
const dataList: any = ref([]);
const printMode = ref('heat');
const isLoading = ref(false);
(async () => {
  isLoading.value = true;
  if (props.inputData.multiple == 0){
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
  } else {
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/event/${props.inputData.division_id}/${props.inputData.event_code}`, dataList);
  }
  if (gameData.value.module == 'ln') {
    dataList.value.sort((a: any, b: any) => a[`r${[props.inputData.round]}_heat`] - b[`r${[props.inputData.round]}_heat`] || a[`r${[props.inputData.round]}_lane`] - b[`r${[props.inputData.round]}_lane`]);
    for (let i = 0; i < dataList.value.length; i++) {
      dataList.value[i][`r${[props.inputData.round]}_options`] = JSON.parse(dataList.value[i][`r${[props.inputData.round]}_options`]);
    }
  }
  isLoading.value = false;
})();
async function submitAll() {
  let res: any = null;
  const dataset: any = [];
  let flag = false;
  if (props.inputData.multiple == 0){
    dataList.value.forEach((item: any) => {
      if (item[`r${props.inputData.round}_heat`] > 0 && item[`r${props.inputData.round}_lane`] > 0 && item[`r${props.inputData.round}_result`] == 0) {
        flag = true;
      }
      dataset.push({
        u_id: item.u_id,
        division_id: item.division_id,
        event_code: item.event_code,
        phase: `r${props.inputData.round}`,
        result: item[`r${props.inputData.round}_result`],
        ranking: 0,
        options: JSON.stringify({}),
      });
    });
    if (flag) {
      alert('所有選手皆需選擇出賽狀況');
      return;
    }
    res = await vr.Patch(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/update/result`, dataset, null, true, true);
  } else {
    dataList.value.forEach((item: any) => {
      if (item[`r${props.inputData.round}_heat`] > 0 && item[`r${props.inputData.round}_lane`] > 0 && item[`r${props.inputData.round}_result`] == 0) {
        flag = true;
      }
      dataset.push({
        team_id: item.team_id,
        division_id: item.division_id,
        event_code: item.event_code,
        phase: `r${props.inputData.round}`,
        result: item[`r${props.inputData.round}_result`],
        ranking: 0,
        options: JSON.stringify({}),
      });
    });
    if (flag) {
      alert('所有選手皆需選擇出賽狀況');
      return;
    }
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
const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
const close = () => {
  emit('refreshPage');
  emit('closeModal');
}
async function sendNext() {
  const r: any = await vr.Post(`game/${route.params.sportCode}/${route.params.gameId}/common/schedule/update/${props.inputData.schedule_id}`, {status: 2}, null, true, true);
  if (r.status == 'A01') {
    alert('已送出');
    close();
  } else {
    alert('操作失敗');
  }
}
const selectedData: any = ref([]);
function setLegs(input: any) {
  selectedData.value = input;
  displayModal.value = 1;
}
</script>

<template>
  <div v-if="isLoading == false">
    <div class="py-3 text-xl">
      <span>{{ props.inputData.timestamp }} {{ props.inputData.division_ch }}-{{ props.inputData.event_ch }}</span>
      <span v-if="gameData.module == 'ln'"> [{{ lanePhaseToString(props.inputData.round, 'zh-TW') }}]</span>
    </div>
    <div class="flex gap-2 pb-3 items-center">
      <div>報表列印：</div>
      <div>
        <select class="border-2 rounded px-1 py-0.5" v-model="printMode">
          <option value="heat">徑賽分組</option>
          <option value="800m">800M</option>
          <option value="1500m">1500M</option>
          <option value="3000m">3000M</option>
          <option value="5000m">5000M</option>
          <option value="10000m">10000M</option>
          <option value="field">跳遠</option>
          <option value="height">跳高</option>
          <option value="field">投擲</option>
        </select>
      </div>
      <router-link class="general-button blue cursor-pointer" :to="`/admin/game/${route.params.sportCode}/${route.params.gameId}/print/lane/${props.inputData.schedule_id}/${props.inputData.division_id}/${props.inputData.event_code}/${props.inputData.multiple}/${props.inputData.round}/record/${printMode}`" target="_blank">成績記錄表</router-link>
      <router-link class="general-button blue cursor-pointer" :to="`/admin/game/${route.params.sportCode}/${route.params.gameId}/print/lane/${props.inputData.schedule_id}/${props.inputData.division_id}/${props.inputData.event_code}/${props.inputData.multiple}/${props.inputData.round}/result/general`" target="_blank">成績總表</router-link>
      <router-link v-if="['ts', 'tr', 'rr'].includes(props.inputData.remarks)" class="general-button blue cursor-pointer" :to="`/admin/game/${route.params.sportCode}/${route.params.gameId}/print/lane/${props.inputData.schedule_id}/${props.inputData.division_id}/${props.inputData.event_code}/${props.inputData.multiple}/${props.inputData.round}/result/heat`" target="_blank">分組成績</router-link>
      <router-link v-if="['fj', 'ft'].includes(props.inputData.remarks)" class="general-button blue cursor-pointer" :to="`/admin/game/${route.params.sportCode}/${route.params.gameId}/print/lane/${props.inputData.schedule_id}/${props.inputData.division_id}/${props.inputData.event_code}/${props.inputData.multiple}/${props.inputData.round}/result/distance`" target="_blank">詳細成績</router-link>
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
        <th v-if="gameData.module == 'ln' && props.inputData.multiple == 1 && props.displayMode == 'call'">棒次</th>
        <template v-if="(gameData.module == 'ln' || gameData.module == 'rd') && props.displayMode == 'view'">
          <th>成績</th>
          <th>排名</th>
          <th>備註</th>
        </template>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr v-if="item[`r${props.inputData.round}_heat`] > 0 && item[`r${props.inputData.round}_lane`]">
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
                <div>尚未<br>選擇</div>
              </div>
              <div :class="{'item': true, 'active': item[`r${props.inputData.round}_result`] == 'OK'}" @click="item[`r${props.inputData.round}_result`] = 'OK'">
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
          <td v-if="gameData.module == 'ln' && props.inputData.multiple == 1 && props.displayMode == 'call'">
            <button class="general-button blue" @click="setLegs(item)">設定</button>
          </td>
          <template v-if="(gameData.module == 'ln' || gameData.module == 'rd') && props.displayMode == 'view'">
            <td>
              <div v-if="item[`r${props.inputData.round}_result`] == '0'">尚未處理</div>
              <div v-else-if="item[`r${props.inputData.round}_result`] == 'OK'">完成檢錄</div>
              <div v-else>{{ item[`r${props.inputData.round}_result`] }}</div>
            </td>
            <td>{{ item[`r${props.inputData.round}_ranking`] }}</td>
            <td v-if="gameData.module == 'ln'">
              <div v-if="item[`r${props.inputData.round}_options`].qualified != undefined">{{ item[`r${props.inputData.round}_options`].qualified }}</div>
              <div v-if="item[`r${props.inputData.round}_options`].windspeed != undefined">WS: {{ item[`r${props.inputData.round}_options`].windspeed }}</div>
              <div v-if="item[`r${props.inputData.round}_options`].rt != undefined">RT: {{ item[`r${props.inputData.round}_options`].rt }}</div>
              <div v-if="item[`r${props.inputData.round}_options`].break != undefined">{{ item[`r${props.inputData.round}_options`].break == null? '':item[`r${props.inputData.round}_options`].break }}</div>
              <div v-if="item[`r${props.inputData.round}_options`].cr != undefined">{{ item[`r${props.inputData.round}_options`].cr ? 'CR':'' }}</div>
              <div v-if="item[`r${props.inputData.round}_options`].cr != undefined" class="italic">{{ item[`r${props.inputData.round}_options`].remark }}</div>
            </td>
          </template>
        </tr>
      </template>
    </table>
    <div class="py-3 flex flex-col gap-3" v-if="props.displayMode == 'call'">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
      <button class="round-full-button blue" @click="sendNext">送出</button>
    </div>
  </div>
  <SmallModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">棒次</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <LegsList v-if="displayModal == 1" :input-data="selectedData" :player-num="props.inputData.player_num" @closeModal="displayModal = 0" @returnData="(res: any) => selectedData.member_list = res"></LegsList>
      </div>
    </template>
  </SmallModal>
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
  @apply grid grid-cols-4 items-stretch text-center;
  .item {
    @apply py-1 px-0.5 bg-gray-100 hover:bg-blue-400 hover:text-white cursor-pointer duration-150;
    &.active {
      @apply bg-blue-500 text-white shadow;
    }
  }
}
</style>