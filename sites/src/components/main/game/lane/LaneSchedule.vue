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
(async () => {
  await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/schedule/full`, scheduleList);
  scheduleList.value.sort((a: any, b: any) => {
    const t1 = new Date(a.timestamp);
    const t2 = new Date(b.timestamp);
    return t1.getTime() - t2.getTime();
  });
})();

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
const statusCh = ['尚未開始', '檢錄中', '進行中', '已完賽', '成績公告', '頒獎', '取消'];
const statusEn = ['Not Started', 'Check In', 'In Progress', 'Finished', 'Result Announced', 'Awarding', 'Cancelled'];
</script>

<template>
  <div class="bg-gray-50">
    <table>
      <tr>
        <th>{{ t('time') }}</th>
        <th>{{ t('division') }}</th>
        <th>{{ t('event') }}</th>
        <th>{{ t('round') }}</th>
        <th>{{ t('status') }}</th>
        <th></th>
      </tr>
      <template v-for="(item, index) in scheduleList" :key="index">
        <tr>
          <td>{{ item.timestamp.substring(0, 16) }}</td>
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
            <span v-if="locale == 'zh-TW'">{{ statusCh[item.status] }}</span>
            <span v-else>{{ statusEn[item.status] }}</span>
          </td>
          <td>
            <router-link v-if="item.division_id != null && item.event_code != null" class="hyperlink blue" to="" target="_blank">{{ t('list') }}</router-link>
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
}
</style>

<i18n lang="yaml">
  en-US:
    list: 'Start List'
    time: 'Time'
    division: 'Division'
    event: 'Event'
    round: 'Round'
    status: 'Status'
  zh-TW:
    list: '選手編配'
    time: '時間'
    division: '組別'
    event: '項目'
    round: '賽別'
    status: '狀態'
  </i18n>