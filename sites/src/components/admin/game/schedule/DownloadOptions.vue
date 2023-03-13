<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useGameStore } from '@/stores/game';
import { useRoute } from 'vue-router';
import { lanePhaseToString, exportCsv } from '@/components/library/functions';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const gameStore = useGameStore();
const sportCode = route.params.sportCode;
const gameId = route.params.gameId;
async function downloadSchedule() {
  const data: any = [];
  const scheduleList = await vr.Get(`game/${sportCode}/${gameId}/common/schedule/full`, null, true, true);
  const paramList = await vr.Get(`game/${sportCode}/${gameId}/main/params`, null, true, true);
  if (gameStore.data.module == 'ln') {
    scheduleList.forEach((item: any) => {
      for(const params of paramList) {
        if (params.division_id == item.division_id && params.event_code == item.event_code) {
          data.push([
            item.timestamp,
            item.division_ch,
            item.event_ch,
            lanePhaseToString(item.round, 'zh-TW'),
            item.division_en,
            item.event_en,
            lanePhaseToString(item.round, 'en-US'),
            params[`r${item.round}_aq`],
            params[`r${item.round}_sq`],
          ]);
        }
      }
    });
    exportCsv(data, `${gameStore.data.game_name_ch}賽程`, ['時間', '組別中文', '項目中文', '賽別中文', '組別英文', '項目英文', '賽別英文', 'Q', 'q']);
  }
}

</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
    <button class="round-full-button blue">組別道次下載</button>
    <button class="round-full-button blue">號碼布下載</button>
    <button class="round-full-button blue" @click="downloadSchedule">賽程表下載</button>
    <button class="round-full-button blue">大會紀錄下載</button>
  </div>
</template>

<style scoped lang="scss">
    
</style>