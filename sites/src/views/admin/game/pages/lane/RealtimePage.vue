<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useGameStore } from '@/stores/game';
  import { lanePhaseToString } from '@/components/library/functions';
  import Config from '@/assets/config.json';

  const currentTime: any = ref(new Date());
  const store = useUserStore();
  const gameStore = useGameStore();
  const vr = new VueRequest(store.token);
  const statusCh = ['尚未開始', '檢錄中', '進行中', '已完賽', '成績公告', '頒獎', '取消'];

  const scheduleList: any = ref([]);
  vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/schedule/full`, scheduleList, true, true);

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
  const hardRefresh = ref(false);
  
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
    });
  }

  async function submitDisplay() {
    const data = JSON.stringify({
      event: selectedEvent.value,
      heatNum: heatNum.value,
      selectedHeat: selectedHeat.value,
      displayMode: displayMode.value,
      athleteNum: displayMode.value == 3 ? eventData.value.length : eventData.value.reduce((acc: any, cur: any) => { return acc + (cur[`r${selectedEvent.value.round}_heat`] == selectedHeat.value ? 1 : 0) }, 0),
      hardRefresh: hardRefresh.value,
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
</script>

<template>
  <div class="p-5 bg-blue-100 h-screen flex flex-col gap-5 overflow-hidden">
    <div class="text-2xl font-medium item">競賽即時資訊管理系統</div>
    <div class="grid grid-cols-2 gap-5 flex-grow overflow-hidden">
      <div class="flex flex-col gap-5 h-full overflow-hidden">
        <div class="grid grid-cols-2 gap-5">
          <div class="item">
            <div class="title">現在時間</div>
            <div>{{ currentTime.getFullYear() }} / {{ (currentTime.getMonth() + 1).toString().padStart(2,'0')  }} / {{ currentTime.getDate().toString().padStart(2,'0')  }}</div>
            <div class="text-2xl md:text-2xl lg:text-3xl xl:lg:text-4xl font-semibold">{{ currentTime.getHours().toString().padStart(2,'0')  }} : {{ currentTime.getMinutes().toString().padStart(2,'0')  }} : {{ currentTime.getSeconds().toString().padStart(2,'0') }}</div>
          </div>
          <div class="item">
            <div class="title">即時氣象資訊</div>
            <hr class="my-1">
          </div>
        </div>
        <div class="item flex-grow flex flex-col h-full overflow-hidden">
          <div class="title">場次顯示設定</div>
          <div class="flex-grow flex flex-col h-full overflow-hidden" v-if="selectedEvent !== null">
            <div class="text-lg py-1">{{ selectedEvent.division_ch }} {{ selectedEvent.event_ch }} [{{ lanePhaseToString(selectedEvent.round, 'zh-TW') }}]</div>
            <div class="flex items-center gap-3 p-2">
              <label class="mode-selector">
                <input type="radio" value="0" v-model="displayMode">
                <div>計時頁面</div>
              </label>
              <label class="mode-selector">
                <input type="radio" value="1" v-model="displayMode">
                <div>組別道次</div>
              </label>
              <label class="mode-selector">
                <input type="radio" value="2" v-model="displayMode">
                <div>單組成績</div>
              </label>
              <label class="mode-selector">
                <input type="radio" value="3" v-model="displayMode">
                <div>輪播成績</div>
              </label>
              <label>
                重整
                <input type="checkbox" v-model="hardRefresh">
              </label>
              <button class="general-button blue" @click="submitDisplay">發送</button>
            </div>
            <div class="flex">
              <div @click="selectedHeat = item" class="py-1 px-5 bg-blue-400 text-white rounded-t cursor-pointer" v-for="(item, index) in heatNum" :key="index">第{{ item }}組</div>
            </div>
            <div class="overflow-auto">
              <table class="result-table">
                <tr>
                  <th>道次</th>
                  <th>單位</th>
                  <th>姓名/隊名</th>
                  <th>成績</th>
                </tr>
                <template v-for="(item, index) in eventData" :key="index">
                  <tr v-if="selectedHeat == item[`r${selectedEvent.round}_heat`]">
                    <td>{{ item[`r${selectedEvent.round}_lane`] }}</td>
                    <td v-if="!Config.deptAsClass">{{ item.org_name_ch }} {{ item.dept_name_ch }}</td>
                    <td v-else>{{ item.dept_name_ch }}</td>
                    <td v-if="selectedEvent.multiple == 1">{{ item.team_name }}</td>
                    <td v-else>{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
                    <td v-if="item[`r${selectedEvent.round}_result`]!='OK'">{{ item[`r${selectedEvent.round}_result`] }}</td>
                  </tr>
                </template>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="item flex flex-col h-full overflow-hidden">
        <div class="title">目前賽程</div>
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