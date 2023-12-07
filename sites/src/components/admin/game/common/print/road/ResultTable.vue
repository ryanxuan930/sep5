<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useGameStore } from '@/stores/game';
import { useRoute } from 'vue-router';
import { lanePhaseToString } from '@/components/library/functions';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const gameStore = useGameStore();
const scheduleId = Number(route.params.scheduleId);
const divisionId = Number(route.params.divisionId);
const eventCode = route.params.eventCode;

const dataList: any = ref([]);
const paramList: any = ref([]);
const eventData: any = ref({});
// const recordList: any = ref({});
const isLoading = ref(false);
(async () => {
  isLoading.value = true;
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/params/${divisionId}/${eventCode}`, paramList);
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/schedule/${scheduleId}`, eventData);
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${divisionId}/${eventCode}`, dataList);
  dataList.value.sort((a: any, b: any) => a.ranking - b.ranking);
  document.title = gameStore.data.game_name_ch + '_' + eventData.value.division_ch + '_' + eventData.value.event_ch + '_' + '成績公告單';
  isLoading.value = false;
  setTimeout(() => {
    window.print();
  }, 100);
})();
</script>

<template>
  <div v-if="isLoading == false">
    <div class="page">
      <div class="text-center font20 font-semibold">{{ gameStore.data.game_name_ch }}</div>
      <div class="text-center font14 font-semibold">{{ gameStore.data.game_name_en }}</div>
      <div class="text-center font16 font-medium">成績公告單 Official Result</div>
      <div class="height1"></div>
      <div class="grid grid-cols-4 header-table">
        <div class="content-box">
          <div class="title">
            <div class="ch-content">時間</div>
            <div class="en-content">Time</div>
          </div>
          <div>
            <div>{{ eventData.timestamp.substring(0,4) }}/{{ eventData.timestamp.substring(5,7) }}/{{ eventData.timestamp.substring(8,10) }}</div>
            <div>{{ eventData.timestamp.substring(11,16) }}</div>
          </div>
        </div>
        <div class="content-box col-span-2">
          <div class="title">
            <div class="ch-content">項目</div>
            <div class="en-content">Event</div>
          </div>
          <div>
            <div class="ch-content">{{ eventData.division_ch }}-{{ eventData.event_ch }}</div>
            <div class="en-content">{{ eventData.division_en }}-{{ eventData.event_en }}</div>
          </div>
        </div>
        <div class="content-box">
          <div class="title">
            <div class="ch-content">場次</div>
            <div class="en-content">Session</div>
          </div>
          <div>{{ eventData.schedule_id.toString().padStart(3, '0') }}</div>
        </div>
      </div>
      <div class="height1"></div>
      <table class="result-table">
        <tr>
          <th>
            <div class="ch-content">排名</div>
            <div class="en-content">Place</div>
          </th>
          <th>
            <div class="ch-content">單位</div>
            <div class="en-content">Organization</div>
          </th>
          <th>
            <div class="ch-content">選手</div>
            <div class="en-content">Athlete</div>
          </th>
          <th>
            <div class="ch-content">成績</div>
            <div class="en-content">Result</div>
          </th>
          <th>
            <div class="ch-content">備註</div>
            <div class="en-content">Remark</div>
          </th>
        </tr>
        <template v-for="(item, index) in dataList" :key="index">
          <tr>
            <td style="width: 5%">{{ item.ranking != 0 ? item.ranking : '' }}</td>
            <td style="width: 30%">
              <div class="ch-content flex items-center gap-2">
                <div>{{ item.org_name_ch }}</div>
                <div v-if="gameStore.data.options.regUnit < 2">{{ item.dept_name_ch }}</div>
              </div>
              <div class="en-content flex items-center gap-2">
                <div>{{ item.org_name_en }}</div>
                <div v-if="gameStore.data.options.regUnit < 2">{{ item.dept_name_en }}</div>
              </div>
            </td>
            <td style="width: 20%">
              <div class="ch-content">
                <span v-if="item.bib != undefined && item.bib != null">{{ item.bib }} </span>
                {{ item.last_name_ch }}{{ item.first_name_ch }}
              </div>
              <div class="en-content" v-if="item.last_name_ch != item.last_name_en && item.first_name_ch != item.first_name_en && item.last_name_en != '' && item.first_name_en != ''"></div>
            </td>
            <td style="width: 10%">{{ item.result }}</td>
            <td style="width: 15%">
              <div class="flex items-center gap-3">
                <div v-if="item.options.qualified != undefined">{{ item.options.qualified }}</div>
              </div>
              <div v-if="item.options.remark != undefined" class="italic">{{ item.options.remark }}</div>
            </td>
          </tr>
        </template>
      </table>
      <div class="font8">DNS：未出賽 DNF：未完賽 DQ：犯規 </div>
      <div class="height2"></div>
      <div class="text-center font8 grid grid-cols-3">
        <div>Official Timekeeper : 
          <img src="@/assets/TechNSport-Logo.svg" class="h-4 inline-block">
        </div>
        <div>{{ $route.params.sportCode.toString().toUpperCase() }}_{{ $route.params.gameId }}_{{ divisionId }}_{{ eventCode.toString().toUpperCase() }}_{{ Date.now() }}</div>
        <div>Timestamp: {{ new Date().toLocaleString('zh-TW', { hour12: false, hc: 'h23'}) }}</div>
        </div>
    </div>
  </div>
</template>

<style lang="scss" scoped src="@/components/admin/game/common/print/lane/page.scss"></style>