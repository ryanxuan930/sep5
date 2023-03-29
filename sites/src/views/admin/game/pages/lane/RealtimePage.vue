<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useGameStore } from '@/stores/game';
  import { lanePhaseToString } from '@/components/library/functions';

  const currentTime: any = ref(new Date());
  const store = useUserStore();
  const gameStore = useGameStore();
  const vr = new VueRequest(store.token);
  const statusCh = ['尚未開始', '檢錄中', '進行中', '已完賽', '成績公告', '頒獎', '取消'];

  const scheduleList: any = ref([]);
  vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/schedule/full`, scheduleList, true, true);
  
  const realtimeResult: any = ref(null);

  let counter = 60;
  setInterval(() => {
    if (counter%10 == 0) {
      (async () => {
        const temp: any = await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/temp/realtimeResult`);
        if (temp.temp_id != undefined) {
          realtimeResult.value = JSON.parse(temp.temp_data);
        }
      })()
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
</script>

<template>
  <div class="p-5 bg-blue-100 h-screen flex flex-col gap-5 overflow-hidden">
    <div class="text-2xl font-medium item">競賽即時資訊管理系統</div>
    <div class="grid grid-cols-2 gap-5 flex-grow overflow-hidden">
      <div class="flex flex-col gap-5">
        <div class="grid grid-cols-2 gap-5">
          <div class="item">
            <div class="title">現在時間</div>
            <div>{{ currentTime.getFullYear() }} / {{ (currentTime.getMonth() + 1).toString().padStart(2,'0')  }} / {{ currentTime.getDate().toString().padStart(2,'0')  }}</div>
            <div class="text-2xl md:text-2xl lg:text-3xl xl:lg:text-4xl font-semibold">{{ currentTime.getHours().toString().padStart(2,'0')  }} : {{ currentTime.getMinutes().toString().padStart(2,'0')  }} : {{ currentTime.getSeconds().toString().padStart(2,'0') }}</div>
          </div>
          <div class="item">
            <div class="title">即時成績資訊</div>
            <hr class="my-1">
            <div class="text-lg" v-if="realtimeResult != null">{{ realtimeResult.data.division_ch }}{{ realtimeResult.data.event_ch }}-{{ lanePhaseToString(realtimeResult.data.round, 'zh-TW') }}</div>
            <div class="text-xl" v-if="realtimeResult != null">[第 {{ realtimeResult.group}} 組] 參考成績：{{ realtimeResult.result }}</div>
          </div>
        </div>
        <div class="item flex-grow">
          <div class="title">顯示資訊管理</div>
        </div>
      </div>
      <div class="item flex flex-col h-full overflow-hidden">
        <div class="title">目前賽程</div>
        <div class="overflow-auto">
          <table class="config-table h-full">
            <tr>
              <th>時間</th>
              <th>組別</th>
              <th>項目</th>
              <th>賽別</th>
              <th>狀態</th>
            </tr>
            <template v-for="(item, index) in scheduleList" :key="index">
              <tr :class="{'active': item.status == 1 || item.status == 2}">
                <td>{{ item.timestamp.substring(5,7) }}/{{ item.timestamp.substring(8,10) }} {{ item.timestamp.substring(11,16) }}</td>
                <td>{{ item.division_ch }}</td>
                <td>{{ item.event_ch }}</td>
                <td>{{ lanePhaseToString(item.round, 'zh-TW') }}</td>
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
</style>