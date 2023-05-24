<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useGameStore } from '@/stores/game';
import { useEventListener } from '@vueuse/core';
import { lanePhaseToString } from '@/components/library/functions';

const store = useUserStore();
const gameStore = useGameStore();
const vr = new VueRequest(store.token);

const currentTime: any = ref(new Date());
const timerStatus = ref(false);
const timerText = ref('00:00.00');
const items = ref([1,2,3,4,5,6,7,8,9,10]);

let counter = 60;
let millis = 0;
let startTime = Date.now();

function formatTime(milliseconds: number): string {
    let totalSeconds: number = Math.floor(milliseconds / 1000);
    let minutes: string = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
    let seconds: string = (totalSeconds % 60).toString().padStart(2, '0');
    let ms: string = Math.floor((milliseconds % 1000) / 10).toString().padStart(2, '0');
    return `${minutes}:${seconds}.${ms}`;
}

setInterval(() => {
  if (timerStatus.value) {
    if (millis%10 == 0) {
      timerText.value = formatTime(Date.now() - startTime);
    }
  }
  currentTime.value = new Date();
  if (millis == 1000) {
    millis = 0;
    currentTime.value = new Date();
  } else {
    millis ++;
  }
  if (counter == 0) {
    counter = 60;
  }
}, 1);

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
</script>

<template>
  <div class="h-screen bg-indigo-950 text-white font-medium px-5 py-1 flex flex-col gap-5">
    <div class="flex-grow"></div>
    <div class="flex items-end gap-5">
      <div>
        <div class="text-4xl font-bold">男子組 100公尺 自由式 決賽</div>
        <div class="text-xl font-bold">Men's 100 metres Final</div>
      </div>
      <div class="flex-grow"></div>
      <div class="flex items-end gap-3 bg-white text-indigo-950 px-5 pb-1">
        <div>
          <div class="text-3xl">組別</div>
          <div class="text-xl">Heat</div>
        </div>
        <div class="text-7xl">1</div>
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
          <div>
            <div class="th-ch">參考成績</div>
            <div class="th-en">Result</div>
          </div>
        </th>
        <th class="w-1/6">
          <div>
            <div class="th-ch">備注</div>
            <div class="th-en">Result</div>
          </div>
        </th>
      </tr>
      <tr v-for="(item, index) in items" :key="index">
        <td>
          <div class="lane-box">
            <div class="lane-box-text">{{ item }}</div>
          </div>
        </td>
        <td>
          <div class="content-text">測試單位</div>
        </td>
        <td>
          <div class="content-text">測試姓名</div>
        </td>
        <td>
          <div class="content-text">12:34.56</div>
        </td>
        <td><span class="px-1 py-0.5 bg-blue-600">破紀錄</span></td>
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
      <div class="bg-blue-500 p-2 text-center">
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
    @apply border-b-[1px] border-white text-2xl 3xl:text-3xl p-0;
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