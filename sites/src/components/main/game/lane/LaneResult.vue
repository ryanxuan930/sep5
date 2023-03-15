<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'
import { lanePhaseToString } from '@/components/library/functions';

const route = useRoute();
const vr = new VueRequest();
const adminOrgId = useRoute().params.adminOrgId;
const gameId = route.params.gameId;
const pageData: any = inject('pageData');
const gameData: any = inject('gameData');
const scheduleList: any = ref([]);
const counter = ref(0);
async function getData() {
  await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/schedule/full`, scheduleList);
  scheduleList.value.sort((a: any, b: any) => {
    const t1 = new Date(a.timestamp);
    const t2 = new Date(b.timestamp);
    return t1.getTime() - t2.getTime();
  });
};
setInterval(() => {
  if (counter.value == 0){
    getData();
    counter.value = 60;
  } else {
    counter.value--;
  }
}, 1000);

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
const statusCh = ['尚未開始', '檢錄中', '進行中', '已完賽', '成績公告', '頒獎', '取消'];
const statusEn = ['Not Started', 'Check In', 'In Progress', 'Finished', 'Result Announced', 'Awarding', 'Cancelled'];
</script>

<template>
  <div class="text-2xl font-medium text-left mb-2">{{ t('result-title') }}</div>
  <div class="text-sm text-left mb-2">{{ t('update-in-second', {second: counter}) }}</div>
  <div class="bg-gray-50">
    <table>
      <tr>
        <th>{{ t('time') }}</th>
        <th>{{ t('division') }}</th>
        <th>{{ t('event') }}</th>
        <th>{{ t('round') }}</th>
        <th></th>
      </tr>
      <template v-for="(item, index) in scheduleList" :key="index">
        <tr v-if="item.status > 3">
          <td>{{ item.timestamp.substring(5, 7) }}/{{ item.timestamp.substring(8, 10) }} {{ item.timestamp.substring(10, 16) }}</td>
          <td colspan="3" v-if="item.division_id == null && item.event_code == null">{{ JSON.parse(item.options).title }}</td>
          <template v-else>
            <td>
              <span v-if="locale == 'zh-TW'">{{ item.division_ch }}</span>
              <span v-else>{{ item.division_en }}</span>
            </td>
            <td>
              <span v-if="locale == 'zh-TW'">{{ item.event_ch }}</span>
              <span v-else>{{ item.event_en }}</span>
            </td>
            <td>{{ lanePhaseToString(item.round, String(locale)) }}</td>
          </template>
          <td>
            <div class="flex gap-2 items-center">
              <router-link v-if="item.division_id != null && item.event_code != null" class="hyperlink blue" :to="`/${adminOrgId}/game/${gameId}/result/general/${item.division_id}/${item.event_code}/${item.round}`">{{ t('list') }}</router-link>
              <router-link v-if="item.division_id != null && item.event_code != null && (item.remarks == 'ts' || item.remarks == 'tr' || item.remarks == 'rr')" class="hyperlink blue" :to="`/${adminOrgId}/game/${gameId}/result/heat/${item.division_id}/${item.event_code}/${item.round}`">{{ t('list-heat') }}</router-link>
            </div>
          </td>
        </tr>
      </template>
    </table>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-full;
  td, th {
    @apply p-2 border-y-[1px] text-left;
  }
  tr.active {
    animation: blink 4s linear infinite;
    @apply bg-blue-100;
    @keyframes blink{
      0%{
        @apply bg-gray-50;
      }
      50%{
        @apply bg-blue-100 shadow;
      }
      100%{
        @apply bg-gray-50;
      }
    }
  }
}
</style>

<i18n lang="yaml">
  en-US:
    list: 'Results'
    list-heat: 'Results (Heats)'
    time: 'Time'
    division: 'Division'
    event: 'Event'
    round: 'Round'
    result-title: 'Results'
    update-in-second: 'Updated in {second} sec.'
  zh-TW:
    list: '成績總表'
    list-heat: '分組成績'
    time: '時間'
    division: '組別'
    event: '項目'
    round: '賽別'
    result-title: '成績公告'
    update-in-second: '{second} 秒後更新'
  </i18n>