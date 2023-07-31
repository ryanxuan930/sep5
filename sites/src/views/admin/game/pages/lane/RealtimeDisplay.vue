<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useGameStore } from '@/stores/game';
import { useEventListener } from '@vueuse/core';
import { lanePhaseToString, getTargetPhase } from '@/components/library/functions';
import Config from '@/assets/config.json';

const store = useUserStore();
const gameStore: any = useGameStore();
const vr = new VueRequest(store.token);

const currentTime: any = ref(new Date());
const timerStatus = ref(false);
const timerText = ref('00:00.00');
const realtimeData: any = ref(null);
const athleteList: any = ref([]);
const displayList: any = ref([null, null, null, null, null, null, null, null, null, null]);

let counter = 60;
let millis = 0;
let startTime = Date.now();
let currentDivision = 0;
let currentEvent = '';
let params: any = {};
let prePhase = 0;

function formatTime(milliseconds: number): string {
    let totalSeconds: number = Math.floor(milliseconds / 1000);
    let minutes: string = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
    let seconds: string = (totalSeconds % 60).toString().padStart(2, '0');
    let ms: string = Math.floor((milliseconds % 1000) / 10).toString().padStart(2, '0');
    return `${minutes}:${seconds}.${ms}`;
}

async function getStatusData() {
  const res = await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/temp/realtimeDisplay`, null, true, true);
  if (res.temp_key === 'realtimeDisplay') {
    realtimeData.value = JSON.parse(res.temp_data);
  }
  if (currentDivision != realtimeData.value.event.division_id && currentEvent != realtimeData.value.event.event_code) {
    currentDivision = realtimeData.value.event.division_id;
    currentEvent = realtimeData.value.event.event_code;
    if (realtimeData.value.event.multiple) {
      await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/group/by/event/${realtimeData.value.event.division_id}/${realtimeData.value.event.event_code}`, athleteList, true, true);
    } else {
      await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/individual/by/event/${realtimeData.value.event.division_id}/${realtimeData.value.event.event_code}`, athleteList, true, true);
    }
    athleteList.value.forEach((item: any) => {
      item[`r${realtimeData.value.event.round}_options`] = JSON.parse(item[`r${realtimeData.value.event.round}_options`]);
    });
    params = await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/main/params/${realtimeData.value.event.division_id}/${realtimeData.value.event.event_code}`, null, true, true);
  }
  displayList.value = [null, null, null, null, null, null, null, null, null, null];
  athleteList.value.forEach((item: any) => {
    if (item[`r${realtimeData.value.event.round}_heat`] == realtimeData.value.selectedHeat) {
      displayList.value[item[`r${realtimeData.value.event.round}_lane`] - 1] = item;
    }
  });
  prePhase = getTargetPhase(realtimeData.value.event.round, params)
}
getStatusData();

setInterval(() => {
  if (timerStatus.value) {
    if (millis%10 == 0) {
      timerText.value = formatTime(Date.now() - startTime);
    }
  }
  currentTime.value = new Date();
  if (millis == 1000) {
    millis = 0;
  } else {
    millis ++;
  }
}, 1);

setInterval(() => {
  if (counter == 0) {
    counter = 60;
  } else {
    counter --;
  }
  if (counter % 6 === 0) {
    getStatusData();
  }
}, 1000);

function keyupHandler(e: KeyboardEvent) {
  if (e.code == 'Space') {
    timerStatus.value = !timerStatus.value;
    if (timerStatus.value) {
      startTime = Date.now();
    }
  }
  if(e.code == 'KeyR') {
    timerStatus.value = false;
    timerText.value = '00:00.00';
  }
  if (e.code == 'KeyW') {
    timerStatus.value = false;
    setTimeout(() => {
      timerStatus.value = true;
    }, 1500);
  }
}

onMounted(() => {
  useEventListener(window, 'keyup', keyupHandler);
});

onUnmounted(() => {
  useEventListener(window, 'keyup', ()=>{});
});

const roundList = ['ref', 'r1', 'r2', 'r3', 'r4'];
</script>

<template>
  <div class="h-screen bg-indigo-950 text-white font-medium px-5 py-1 flex flex-col gap-5" v-if="realtimeData !== null">
    <div class="flex-grow"></div>
    <div class="flex items-end gap-5">
      <div>
        <div class="text-4xl font-bold">{{ realtimeData.event.division_ch }} {{ realtimeData.event.event_ch }} [{{ lanePhaseToString(realtimeData.event.round, 'zh-TW') }}]</div>
        <div class="text-xl font-bold">{{ realtimeData.event.division_en }} {{ realtimeData.event.event_en }} [{{ lanePhaseToString(realtimeData.event.round, 'en-US')  }}]</div>
      </div>
      <div class="flex-grow"></div>
      <div class="flex items-end gap-3 bg-white text-indigo-950 px-5 pb-1">
        <div>
          <div class="text-3xl">組別</div>
          <div class="text-xl">Heat</div>
        </div>
        <div class="text-7xl">
          {{ realtimeData.selectedHeat }}
        </div>
      </div>
      <div class="pb-1">
        <div class="text-2xl">大會紀錄 CR：12:34.56</div>
        <div class="text-2xl">全國紀錄 NR：不適用</div>
      </div>
    </div>
    <table class="result-table">
      <tr>
        <th class="w-1/6">
          <div>
            <div class="th-ch">道次</div>
            <div class="th-en">Lane</div>
          </div>
        </th>
        <th class="w-1/4">
          <div>
            <div class="th-ch">所屬單位</div>
            <div class="th-en">Affiliation</div>
          </div>
        </th>
        <th class="w-1/4">
          <div>
            <div class="th-ch">姓名</div>
            <div class="th-en">Name</div>
          </div>
        </th>
        <th class="w-1/6">
          <div v-if="realtimeData.displayMode == 0">
            <div class="th-ch">參考成績</div>
            <div class="th-en">Live Result</div>
          </div>
          <div v-if="realtimeData.displayMode == 1">
            <div class="th-ch">晉級成績</div>
            <div class="th-en">P. Result</div>
          </div>
          <div v-if="realtimeData.displayMode == 2">
            <div class="th-ch">成績</div>
            <div class="th-en">Result</div>
          </div>
        </th>
        <th class="w-1/6">
          <div>
            <div class="th-ch">備註</div>
            <div class="th-en">Remarks</div>
          </div>
        </th>
      </tr>
      <tr v-for="(item, index) in displayList" :key="index">
        <template v-if="item !== null">
          <td>
            <div class="lane-box">
              <div class="lane-box-text">{{ index + 1 }}</div>
            </div>
          </td>
          <td>
            <div class="content-text">
              <span v-if="Config.deptAsClass">{{ item.dept_name_ch }}</span>
              <span v-else-if="Config.deptAsClass == false && gameStore.data.options.regUnit == 1">{{ item.org_name_ch }} {{ item.dept_name_ch }}</span>
              <span v-else>{{ item.org_name_ch }}</span>
            </div>
          </td>
          <td>
            <div class="content-text">{{ item.last_name_ch }}{{ item.first_name_ch }}</div>
          </td>
          <td>
            <div class="content-text" v-show="realtimeData.displayMode == 1">{{ item[`${roundList[prePhase]}_result`] }}</div>
            <div class="content-text" v-show="realtimeData.displayMode == 2">{{ item[`r${realtimeData.event.round}_result`] }}</div>
          </td>
          <td>
            <div v-if="realtimeData.displayMode == 2">
              <span v-if="item[`r${realtimeData.event.round}_options`].qualified != undefined" class="px-1 py-0.5">{{ item[`r${realtimeData.event.round}_options`].qualified }}</span>
              <span v-if="item[`r${realtimeData.event.round}_options`].break != null" class="px-1 py-0.5 bg-blue-600">{{ item[`r${realtimeData.event.round}_options`].break }}</span>
            </div>
          </td>
        </template>
      </tr>
    </table>
    <div class="flex items-center">
      <div>
        <div class="py-2 px-10 rounded-xl bg-blue-600 text-center shadow">
          <div class="text-6xl font-semibold">{{ currentTime.getHours().toString().padStart(2,'0')  }} : {{ currentTime.getMinutes().toString().padStart(2,'0')  }} : {{ currentTime.getSeconds().toString().padStart(2,'0') }}</div>
          <div class="text-xl">大會時間 Official Time</div>
        </div>
      </div>
      <div class="flex-grow"></div>
      <div v-show="realtimeData.displayMode == 0" class="bg-blue-500 p-2 text-center">
        <div class="pb-1 text-xl" style="font-family: Audiowide-Regular;">TechNSport</div>
        <div class="text-6xl 2xl:text-8xl bg-black py-2 px-10" style="font-family: Digital;">{{ timerText }}</div>
      </div>
    </div>
    <div class="flex-grow"></div>
  </div>
</template>

<style scoped lang="scss">
.result-table {
  @apply w-full text-left;
  .th-ch {
    @apply text-2xl 2xl:text-3xl;
  }
  .th-en {
    @apply text-xl 2xl:text-2xl;
  }
  th > div {
    @apply flex items-end gap-2 border-0;
  }
  th {
    @apply border-b-2 p-2;
  }
  td {
    @apply border-b-[1px] border-white text-2xl 3xl:text-3xl p-px;
  }
  tr:nth-child(even) {
    @apply bg-blue-950;
  }
  tr:last-child > td {
    @apply border-b-2;
  }
  .content-text {
    @apply p-1 2xl:p-2 3xl:p-3;
  }
  .lane-box {
    @apply p-1 2xl:p-2 3xl:p-3 bg-white text-indigo-950 w-20 text-center relative mt-px rounded-tl-lg;
  }
  .lane-box:after {
    @apply absolute bottom-0 left-3 h-full w-full bg-white rounded-tr-lg;
    content: "";
    transform: skewX(30deg);
    transform-origin: top left;
  }
  .lane-box-text {
    @apply relative z-10;
  }
}
@font-face {
  font-family: Digital;
  src: url("https://ryanxuan930.github.io/cdn/nsysu_athletics/font/digital.ttf");
}
@font-face {
  font-family: Audiowide-Regular;
  src: url("https://ryanxuan930.github.io/cdn/nsysu_athletics/font/Audiowide-Regular.ttf");
}
.list-enter-active,
.list-leave-active {
  transition: all 10s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>