<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useGameStore } from '@/stores/game';
  import { lanePhaseToString } from '@/components/library/functions';
  import Config from '@/assets/config.json';
  import StartSound from '@/assets/start.mp3';
  import UpSound from '@/assets/up.mp3';
  import DownSound from '@/assets/down.mp3';
  import BellSound from '@/assets/bell.mp3';

  const currentTime: any = ref(new Date());
  const store = useUserStore();
  const gameStore = useGameStore();
  const vr = new VueRequest(store.token);
  const statusCh = ['尚未開始', '檢錄中', '進行中', '已完賽', '成績公告', '頒獎', '取消'];

  const scheduleList: any = ref([]);
  vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/schedule/full`, scheduleList, true, true);
vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/temp/realtimeDisplay`).then((res: any) =>{
  if (res.temp_id != undefined) {
    const data = JSON.parse(res.temp_data);
    textArea.value = data.text;
  }
});

  let counter = 60;
  setInterval(() => {
    if (counter % 15 == 0) {
      openEvent(selectedEvent.value);
    }
    if (counter%30 == 0) {
      vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/schedule/full`, scheduleList, true, true).then(() => {
        let list = document.getElementsByClassName('active');
        if (list.length > 0) {
          list[0].scrollIntoView({behavior: "smooth", block: "center", inline: "center"});
        }
      });
    }
    counter--;
    if (counter == 0) {
      counter = 60;
    }
    currentTime.value = new Date();
  }, 1000);

  const eventData: any = ref([]);
  const selectedEvent: any = ref(null);
  const heatNum = ref(0);
  const selectedHeat = ref(1);
  const displayMode = ref(0);
  
  async function openEvent(input: any) {
    selectedEvent.value = input;
    if (input.multiple) {
      await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/group/by/event/${input.division_id}/${input.event_code}`, eventData, true, true);
    } else {
      await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/individual/by/event/${input.division_id}/${input.event_code}`, eventData, true, true);
    }
    eventData.value.sort((a: any, b: any) => { return a[`r${input.round}_heat`] - b[`r${input.round}_heat`] ||  a[`r${input.round}_lane`] - b[`r${input.round}_lane`] });
    heatNum.value = 0;
    eventData.value.forEach((event: any) => {
      if (event[`r${input.round}_heat`] > heatNum.value) {
        heatNum.value = event[`r${input.round}_heat`];
      }
      event[`r${input.round}_options`] = JSON.parse(event[`r${input.round}_options`]); 
    });
  }

  const textArea = ref('');

  async function submitDisplay() {
    const data = JSON.stringify({
      event: selectedEvent.value,
      heatNum: heatNum.value,
      selectedHeat: selectedHeat.value,
      displayMode: displayMode.value,
      athleteNum: displayMode.value == 3 ? eventData.value.length : eventData.value.reduce((acc: any, cur: any) => { return acc + (cur[`r${selectedEvent.value.round}_heat`] == selectedHeat.value ? 1 : 0) }, 0),
      hardRefresh: Date.now(),
      text: textArea.value,
    });
    const temp = await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/temp/realtimeDisplay`);
    let res: any = null;
    if (temp.temp_id == undefined) {
      res = await vr.Post(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/temp`, {temp_key: 'realtimeDisplay', temp_data: data}, null, true, true);
    } else {
      res = await vr.Patch(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/temp/realtimeDisplay`, { temp_data: data}, null, true, true);
    }
    if (res.status == 'A01') {
      alert('已發送');
    } else {
      alert('操作失敗');
    }
  }

  // sound

  function playStart() {
    const start = new Audio(StartSound);
    start.play();
  }
  function playUp() {
    const announceup = new Audio(UpSound);
    announceup.play();
  }
  function playDown() {
    const announcedown = new Audio(DownSound);
    announcedown.play();
  }
  function playBell() {
    const bell = new Audio(BellSound);
    bell.play();
  }

</script>

<template>
  <div class="p-5 bg-blue-100 h-screen flex flex-col gap-5 overflow-hidden">
    <div class="text-2xl font-medium item flex items-center">
      <div>競賽即時資訊管理系統</div>
      <div class="flex-grow"></div>
      <div>現在時間：
        <span class="font-semibold">
          {{ currentTime.getFullYear() }} / {{ (currentTime.getMonth() + 1).toString().padStart(2,'0')  }} / {{ currentTime.getDate().toString().padStart(2,'0')  }} {{ currentTime.getHours().toString().padStart(2,'0')  }} : {{ currentTime.getMinutes().toString().padStart(2,'0')  }} : {{ currentTime.getSeconds().toString().padStart(2,'0') }}
        </span>
      </div>
      <div class="flex-grow"></div>
      <div class="flex items-center gap-2">
        <button class="general-button blue text-base" @click="playStart">開始比賽</button>
        <button class="general-button blue text-base" @click="playUp">廣播開始</button>
        <button class="general-button blue text-base" @click="playDown">廣播結束</button>
        <button class="general-button blue text-base" @click="playBell">播放鈴聲</button>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-5 flex-grow overflow-hidden">
      <div class="flex flex-col gap-5 h-full overflow-hidden">
        <div class="item flex-grow flex flex-col h-full overflow-hidden">
          <div class="flex-grow flex flex-col h-full overflow-hidden" v-if="selectedEvent !== null">
            <div class="text-lg py-1 title">{{ selectedEvent.division_ch }} {{ selectedEvent.event_ch }} [{{ lanePhaseToString(selectedEvent.round, 'zh-TW') }}]</div>
            <div class="flex items-center gap-3 p-2">
              <div>顯示方式：</div>
              <select v-model="displayMode" class="flex-grow border rounded">
                <option value="0">計時頁面</option>
                <option value="1">組別道次</option>
                <option value="2">單組成績</option>
                <option value="3">輪播成績</option>
                <option value="4">田賽遠度詳細記錄</option>
                <!--<option value="5">田賽高度詳細記錄</option>-->
                <option value="6">田賽即時成績</option>
                <option value="7">公告顯示</option>
              </select>
              <button class="general-button blue" @click="submitDisplay">發送</button>
            </div>
            <div class="flex cursor-pointer">
              <button @click="selectedHeat = item" :class="{'py-1 px-5 bg-blue-300 text-white rounded-t cursor-pointer': true,'bg-blue-400': selectedHeat == item}" v-for="(item, index) in heatNum" :key="index">第{{ item }}組</button>
              <button @click="selectedHeat = 0" :class="{'py-1 px-5 bg-blue-300 text-white rounded-t cursor-pointer': true,'bg-blue-400': selectedHeat == 0}">全部</button>
            </div>
            <div class="overflow-auto">
              <table class="result-table">
                <tr>
                  <th v-if="selectedHeat == 0">組別</th>
                  <th>道次</th>
                  <th>單位</th>
                  <th>姓名/隊名</th>
                  <th>成績</th>
                  <th v-if="selectedHeat == 0">排名</th>
                </tr>
                <template v-for="(item, index) in eventData" :key="index">
                  <tr v-if="(selectedHeat == item[`r${selectedEvent.round}_heat`] || selectedHeat == 0) && item[`r${selectedEvent.round}_heat`] > 0 && item[`r${selectedEvent.round}_lane`] > 0">
                    <td v-if="selectedHeat == 0">{{ item[`r${selectedEvent.round}_heat`] }}</td>
                    <td>{{ item[`r${selectedEvent.round}_lane`] }}</td>
                    <td v-if="!Config.deptAsClass">{{ item.org_name_ch }} {{ item.dept_name_ch }}</td>
                    <td v-else>{{ item.dept_name_ch }}</td>
                    <td v-if="selectedEvent.multiple == 1">{{ item.team_name }}</td>
                    <td v-else>{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
                    <td v-if="item[`r${selectedEvent.round}_result`]!='OK'">{{ item[`r${selectedEvent.round}_result`] }}</td>
                    <td v-if="selectedHeat == 0">{{ item[`r${selectedEvent.round}_ranking`] }}</td>
                  </tr>
                  <tr v-if="selectedEvent.remarks == 'fj' || selectedEvent.remarks == 'ft'">
                    <td :colspan="selectedHeat == 0 ? 6 : 4" class="bg-blue-50 text-xs text-gray-600 border-b text-center">
                      <div v-if="item[`r${selectedEvent.round}_options`].performance != undefined && item[`r${selectedEvent.round}_options`].performance.format != undefined && item[`r${selectedEvent.round}_options`].performance.format == 'distance'" class="grid grid-cols-7">
                        <div>試跳(擲)</div>
                        <div v-for="i in 6">{{ i }}</div>
                        <div>成績</div>
                        <div v-for="(attempt, indexB) in item[`r${selectedEvent.round}_options`].performance.attempt" :key="indexB">{{ attempt }}</div>
                      </div>
                    </td>
                  </tr>
                </template>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-col h-full overflow-hidden gap-5">
        <div class="item">
          <div class="title">公告內容</div>
          <textarea rows="2" class="w-full bg-gray-50 border p-1" v-model="textArea"></textarea>
        </div>
        <div class="item flex-grow flex flex-col overflow-hidden h-full">
          <div class="title flex-grow">目前賽程</div>
          <div class="overflow-auto">
            <table class="config-table h-full">
              <tr>
                <th>時間</th>
                <th>賽程</th>
                <th>狀態</th>
              </tr>
              <template v-for="(item, index) in scheduleList" :key="index">
                <tr @click="openEvent(item)" v-id="item.round > 0" :class="{'cursor-pointer hover:bg-blue-50 duration-150': true, 'active': item.status == 1 || item.status == 2}">
                  <td>{{ item.timestamp.substring(5,7) }}/{{ item.timestamp.substring(8,10) }} {{ item.timestamp.substring(11,16) }}</td>
                  <td>{{ item.division_ch }}{{ item.event_ch }}[{{ lanePhaseToString(item.round, 'zh-TW') }}]</td>
                  <td>{{ statusCh[item.status] }}</td>
                </tr>
              </template>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.item {
  @apply bg-white p-5 shadow rounded;
}
.title {
  @apply text-xl font-medium;
}
tr.active {
  animation: blink 4s linear infinite;
  @apply bg-blue-100;
  @keyframes blink{
    0%{
      @apply bg-white;
    }
    50%{
      @apply bg-blue-100 shadow;
    }
    100%{
      @apply bg-white;
    }
  }
}
.mode-selector {
  @apply flex items-center gap-2;
}
.result-table {
  @apply w-full text-left h-full relative;
  th {
    @apply bg-blue-400 text-white top-0 sticky;
  }
  th, td {
    @apply p-1;
  }
}
</style>