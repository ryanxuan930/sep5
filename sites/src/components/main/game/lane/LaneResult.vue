<script setup lang="ts">
import { ref, inject, watch } from 'vue';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'
import { lanePhaseToString } from '@/components/library/functions';
import Config from '@/assets/config.json';
import SmallLoader from '@/components/SmallLoader.vue';

const route = useRoute();
const vr = new VueRequest();
const adminOrgId = useRoute().params.adminOrgId;
const gameId = route.params.gameId;
const pageData: any = inject('pageData');
const gameData: any = inject('gameData');
const sportCode = gameData.value.sport_code;
const scheduleList: any = ref([]);
const selectedTab = ref(0);
const counter = ref(0);
const championData: any = ref(null);
const isLoading = ref(false);

(async () => {
  const temp = await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/temp/gameChampion`);
  if (temp.temp_id != undefined) {
    championData.value = JSON.parse(temp.temp_data);
  }
})();
async function getData() {
  isLoading.value = true;
  if (selectedTab.value == 0) {
    await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/schedule/full`, scheduleList);
    scheduleList.value.sort((a: any, b: any) => {
      const t1 = new Date(a.timestamp);
      const t2 = new Date(b.timestamp);
      return t1.getTime() - t2.getTime();
    });
  }
  if (selectedTab.value == 1) {
    let orgCode = '';
    let deptId = NaN;
    let index = -1;
    scheduleList.value = [];
    const dataList = await vr.Get(`game/${sportCode}/${gameId}/common/result/ranking`);
    for (const data of dataList) {
      if (data.org_code != orgCode || data.dept_id != deptId) {
        index++;
        orgCode = data.org_code;
        deptId = data.dept_id;
        scheduleList.value[index] = {
          org_code: data.org_code,
          dept_id: data.dept_id,
          org_name_full_ch: data.org_name_full_ch,
          org_name_full_en: data.org_name_full_en,
          org_name_ch: data.org_name_ch,
          org_name_en: data.org_name_en,
          dept_name_ch: data.dept_name_ch,
          dept_name_en: data.dept_name_en,
          ranking: [0, 0, 0, 0, 0, 0, 0, 0],
        }
      }
      scheduleList.value[index].ranking[data.r4_ranking - 1] = data.count;
    }
    // if gameData.value.options.regUnit == 2, merge data by org_code
    if (gameData.value.options.regUnit == 2) {
      const temp: any = [];
      for (const data of scheduleList.value) {
        const index = temp.findIndex((item: any) => item.org_code == data.org_code);
        if (index == -1) {
          temp.push(data);
        } else {
          for (let i = 0; i < 8; i++) {
            temp[index].ranking[i] += data.ranking[i];
          }
        }
      }
      scheduleList.value = temp;
    }
  }
  if (selectedTab.value == 2) {
    let eventId = NaN;
    let divisionId = NaN;
    let index = -1;
    scheduleList.value = [];
    const dataList = await vr.Get(`game/${gameData.value.sport_code}/${gameId}/common/result/all`);
    dataList.sort((a: any, b: any) => {
      return a.division_id - b.division_id || a.event_id - b.event_id;
    });
    for (const data of dataList) {
      if (data.division_id != divisionId || data.event_id != eventId) {
        index++;
        eventId = data.event_id;
        divisionId = data.division_id;
        scheduleList.value[index] = {
          event_ch: data.event_ch,
          event_en: data.event_en,
          division_ch: data.division_ch,
          division_en: data.division_en,
          ranking: [[], [], [], [], [], [], [], []],
        }
      }
      data.r4_options = JSON.parse(data.r4_options);
      scheduleList.value[index].ranking[data.r4_ranking - 1].push(data);
    }
  }
  if (selectedTab.value == 3) {
    if (championData.value != null) {
      if (championData.value.content.length > 0) {
        calculateChampion(championData.value.content[0].type, championData.value.content[0].formula, championData.value.content[0].divisionList);
      }
    }
  }
  isLoading.value = false;
};

const championIndex = ref(0);

async function calculateChampion(type: string, formula: any, divisionList: number[]) {
  isLoading.value = true;
  if (type == 'ranking') {
    let orgCode = '';
    let deptId = NaN;
    let index = -1;
    scheduleList.value = [];
    if (formula.length == 0) {
      alert('尚未設定計算公式');
      return;
    }
    const dataList = await vr.Post(`game/${sportCode}/${gameId}/common/result/champion/${formula.length}`, {divisionList: JSON.stringify(divisionList)});
    dataList.sort((a: any, b: any) => a.division_id - b.division_id || a.org_code - b.org_code || a.dept_id - b.dept_id || a.r4_ranking - b.r4_ranking);
    for (const data of dataList) {
      if (divisionList.includes(data.division_id)) {
        if (data.org_code != orgCode || data.dept_id != deptId) {
          index++;
          orgCode = data.org_code;
          deptId = data.dept_id;
          scheduleList.value[index] = {
            org_code: data.org_code,
            dept_id: data.dept_id,
            org_name_full_ch: data.org_name_full_ch,
            org_name_full_en: data.org_name_full_en,
            org_name_ch: data.org_name_ch,
            org_name_en: data.org_name_en,
            dept_name_ch: data.dept_name_ch,
            dept_name_en: data.dept_name_en,
            ranking: new Array(formula.length).fill(0),
            points: new Array(formula.length).fill(0),
          }
        }
      scheduleList.value[index].ranking[data.r4_ranking - 1] = data.count;
      scheduleList.value[index].points[data.r4_ranking - 1] = data.count * formula[data.r4_ranking - 1];
      } else {
        continue;
      }
    }
    scheduleList.value.forEach((item: any) => {
      item.sum = item.points.reduce((a: number, b: number) => a + b, 0);
    });
    scheduleList.value.sort((a: any, b: any) => b.sum - a.sum || b.ranking.join('') - a.ranking.join(''));
  }
  isLoading.value = false;
}

watch(selectedTab, () => {
  getData();
});
watch(championIndex, (val) => {
  calculateChampion(championData.value.content[val].type, championData.value.content[val].formula, championData.value.content[val].divisionList);
})
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
  <div class="bookmark">
    <button :class="{'item': true, 'active': selectedTab == 0}" @click="selectedTab = 0">{{ t('timetable') }}</button>
    <button :class="{'item': true, 'active': selectedTab == 1}" @click="selectedTab = 1">{{ t('placing-table') }}</button>
    <button :class="{'item': true, 'active': selectedTab == 2}" @click="selectedTab = 2">{{ t('overall-result') }}</button>
    <button v-if="championData != null && championData.hasChampion == true" :class="{'item': true, 'active': selectedTab == 3}" @click="selectedTab = 3">{{ t('champion') }}</button>
  </div>
  <div class="overflow-auto">
    <div class="w-[480px] xs:w-full" v-if="isLoading == false">
      <div class="bg-gray-50" v-if="selectedTab == 0">
        <table>
          <tr>
            <th>{{ t('time') }}</th>
            <th>{{ t('division') }}</th>
            <th>{{ t('event') }}</th>
            <th>{{ t('round') }}</th>
            <th></th>
          </tr>
          <template v-for="(item, index) in scheduleList" :key="index">
            <tr>
              <td>{{ item.timestamp.substring(5, 7) }}/{{ item.timestamp.substring(8, 10) }} {{ item.timestamp.substring(10, 16) }}</td>
              <td colspan="4" v-if="item.division_id == null && item.event_code == null">{{ JSON.parse(item.options).title }}</td>
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
                <td>
                  <div class="flex gap-2 items-center" v-if="item.status > 2">
                    <router-link v-if="item.division_id != null && item.event_code != null" class="hyperlink blue" :to="`/${adminOrgId}/game/${gameId}/result/general/${item.division_id}/${item.event_code}/${item.round}?status=${item.status}`">{{ t('list') }}</router-link>
                    <router-link v-if="item.division_id != null && item.event_code != null && (item.remarks == 'ts' || item.remarks == 'tr' || item.remarks == 'rr')" class="hyperlink blue" :to="`/${adminOrgId}/game/${gameId}/result/heat/${item.division_id}/${item.event_code}/${item.round}?status=${item.status}`">{{ t('list-heat') }}</router-link>
                    <router-link v-if="item.division_id != null && item.event_code != null && (item.remarks == 'ft' || item.remarks == 'fj')" class="hyperlink blue" :to="`/${adminOrgId}/game/${gameId}/result/distance/${item.division_id}/${item.event_code}/${item.round}?status=${item.status}`">{{ t('list-more') }}</router-link>
                  </div>
                  <div v-else>{{ t('not-available') }}</div>
                </td>
              </template>
            </tr>
          </template>
        </table>
      </div>
      <div v-if="selectedTab == 1">
        <table>
          <tr>
            <th v-if="!Config.deptAsClass">{{ t('organization') }}</th>
            <template v-if="gameData.options.regUnit == 1">
              <th v-if="Config.deptAsClass">{{ t('class') }}</th>
              <th v-else>{{ t('department') }}</th>
            </template>
            <th class=text-center>
              <span class="block w-5 h-5 rounded-full m-auto bg-amber-400"></span>
            </th>
            <th class=text-center>
              <span class="block w-5 h-5 rounded-full m-auto bg-gray-300"></span>
            </th>
            <th class=text-center>
              <span class="block w-5 h-5 rounded-full m-auto bg-amber-700"></span>
            </th>
            <th class=text-center>4</th>
            <th class=text-center>5</th>
            <th class=text-center>6</th>
            <th class=text-center>7</th>
            <th class=text-center>8</th>
          </tr>
          <template v-for="(item, index) in scheduleList" :key="index">
            <tr>
              <td v-if="!Config.deptAsClass">
                <div v-if="locale == 'en-US' && (item.org_name_full_en != null || item.org_name_full_en != '')">{{ item.org_name_full_en }}</div>
                <div v-else>{{ item.org_name_full_ch }}</div>
              </td>
              <td v-if="gameData.options.regUnit == 1">
                <div v-if="locale == 'en-US' && (item.dept_name_en != null || item.dept_name_en != '')">{{ item.dept_name_en }}</div>
                <div v-else>{{ item.dept_name_ch }}</div>
              </td>
              <template v-for="(place, index) in item.ranking" :key="index">
                <td class="text-center">
                  <template v-if="place > 0">{{ place }}</template>
                </td>
              </template>
            </tr>
          </template>
        </table>
      </div>
      <div v-if="selectedTab == 2">
        <table>
          <tr>
            <th>{{ t('division') }}</th>
            <th>{{ t('event') }}</th>
            <th class=text-center>
              <span class="block w-5 h-5 rounded-full m-auto bg-amber-400"></span>
            </th>
            <th class=text-center>
              <span class="block w-5 h-5 rounded-full m-auto bg-gray-300"></span>
            </th>
            <th class=text-center>
              <span class="block w-5 h-5 rounded-full m-auto bg-amber-700"></span>
            </th>
            <th class=text-center>4</th>
            <th class=text-center>5</th>
            <th class=text-center>6</th>
            <th class=text-center>7</th>
            <th class=text-center>8</th>
          </tr>
          <template v-for="(item, index) in scheduleList" :key="index">
            <tr>
              <td>
                <span v-if="locale == 'zh-TW'">{{ item.division_ch }}</span>
                <span v-else>{{ item.division_en }}</span>
              </td>
              <td>
                <span v-if="locale == 'zh-TW'">{{ item.event_ch }}</span>
                <span v-else>{{ item.event_en }}</span>
              </td>
              <template v-for="(place, index) in item.ranking" :key="index">
                <td class="text-center">
                  <div class="flex flex-col gap-2">
                    <template v-for="(athlete, index) in place" :key="index">
                      <div>
                        <div class="text-sm" v-if="!Config.deptAsClass">
                          <div v-if="locale == 'en-US' && (athlete.org_name_en != null || athlete.org_name_en != '')">{{ athlete.org_name_en }}</div>
                          <div v-else>{{ athlete.org_name_ch }}</div>
                        </div>
                        <div class="text-sm" v-if="gameData.options.regUnit == 1">
                          <div v-if="locale == 'en-US' && (athlete.dept_name_en != null || athlete.dept_name_en != '')">{{ athlete.dept_name_en }}</div>
                          <div v-else>{{ athlete.dept_name_ch }}</div>
                        </div>
                        <div>
                          <div v-if="locale == 'en-US' && (athlete.last_name_en != null || athlete.last_name_en != '')">{{ athlete.first_name_en }} {{ athlete.last_name_en }}</div>
                          <div v-else>{{ athlete.last_name_ch }}{{ athlete.first_name_ch }}</div>
                        </div>
                        <div>{{ athlete.r4_result }}</div>
                        <div class="flex gap-3 justify-center">
                          <template v-if="athlete.r4_options.cr != undefined">
                            <div class="px-2 py-0.5 bg-blue-400 rounded text-white inline-block" v-if="athlete.r4_options.cr">CR</div>
                          </template>
                          <template v-if="athlete.r4_options.nr != undefined">
                            <div class="px-2 py-0.5 bg-blue-500 rounded text-white inline-block" v-if="athlete.r4_options.nr">NR</div>
                          </template>
                        </div>
                        <template v-if="athlete.r4_options.break != undefined">
                          <div v-if="athlete.r4_options.break != null">{{ athlete.r4_options.break }}</div>
                        </template>
                      </div>
                    </template>
                  </div>
                </td>
              </template>
            </tr>
          </template>
        </table>
      </div>
      <div v-if="selectedTab == 3">
        <div class="flex items-center bg-gray-50">
          <template v-for="(item, index) in championData.content" :key="index">
            <div @click="championIndex = index" :class="{'py-2 px-4 hover:bg-gray-100 hover:text-gray-700 duration-150 cursor-pointer': true, 'bg-gray-400 text-white': index == championIndex}">{{ item.divisionName }}</div>
          </template>
        </div>
        <table v-if="championData.isRelease">
          <tr>
            <th class="w-1/5" v-if="!Config.deptAsClass">{{ t('organization') }}</th>
            <template v-if="gameData.options.regUnit == 1">
              <th class="w-1/5" v-if="Config.deptAsClass">{{ t('class') }}</th>
              <th class="w-1/5" v-else>{{ t('department') }}</th>
            </template>
            <th>{{ t('points') }} ({{ t('official-result') }})</th>
          </tr>
          <template v-for="(item, index) in championData.content[championIndex].payload" :key="index">
            <tr>
              <td v-if="!Config.deptAsClass">
                <div v-if="locale == 'en-US' && (item.org_name_full_en != null || item.org_name_full_en != '')">{{ item.org_name_full_en }}</div>
                <div v-else>{{ item.org_name_full_ch }}</div>
              </td>
              <td v-if="gameData.options.regUnit == 1">
                <div v-if="locale == 'en-US' && (item.dept_name_en != null || item.dept_name_en != '')">{{ item.dept_name_en }}</div>
                <div v-else>{{ item.dept_name_ch }}</div>
              </td>
              <td>
                <div class="flex items-center gap-1">
                  <div class="inline-block bg-blue-400 h-8 text-white text-right" :style="{width: +item.sum>100?'100%':item.sum+'%' }"></div>
                  <div class="text-gray-500 font-medium">{{ item.sum }}</div>
                </div>
              </td>
            </tr>
          </template>
        </table>
        <table v-else>
          <tr>
            <th class="w-1/5" v-if="!Config.deptAsClass">{{ t('organization') }}</th>
            <template v-if="gameData.options.regUnit == 1">
              <th class="w-1/5" v-if="Config.deptAsClass">{{ t('class') }}</th>
              <th class="w-1/5" v-else>{{ t('department') }}</th>
            </template>
            <th>{{ t('points') }} ({{ t('realtime-result') }})</th>
          </tr>
          <template v-for="(item, index) in scheduleList" :key="index">
            <tr>
              <td v-if="!Config.deptAsClass">
                <div v-if="locale == 'en-US' && (item.org_name_full_en != null || item.org_name_full_en != '')">{{ item.org_name_full_en }}</div>
                <div v-else>{{ item.org_name_full_ch }}</div>
              </td>
              <td v-if="gameData.options.regUnit == 1">
                <div v-if="locale == 'en-US' && (item.dept_name_en != null || item.dept_name_en != '')">{{ item.dept_name_en }}</div>
                <div v-else>{{ item.dept_name_ch }}</div>
              </td>
              <td>
                <div class="flex items-center gap-1">
                  <div class="inline-block bg-blue-400 h-8 text-white text-right" :style="{width: +item.sum>100?'100%':item.sum+'%' }"></div>
                  <div class="text-gray-500 font-medium">{{ item.sum }}</div>
                </div>
              </td>
            </tr>
          </template>
        </table>
      </div>
    </div>
    <SmallLoader v-show="isLoading"></SmallLoader>
  </div>

</template>

<style scoped lang="scss">
table {
  @apply w-full;
  td, th {
    @apply p-2 border-y-[1px] text-left break-words;
  }
  .text-center {
    text-align: center;
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
.bookmark {
  @apply flex gap-[1px] items-center;
  .item {
    @apply bg-gray-100 text-lg py-2 px-4 font-medium rounded-t hover:bg-gray-400 hover:text-white duration-150;
    &.active {
      @apply bg-gray-400 text-white;
    }
  }
}
</style>

<i18n lang="yaml">
  en-US:
    list: 'Results'
    list-heat: 'Results (Heats)'
    list-more: 'More Info'
    time: 'Time'
    division: 'Division'
    event: 'Event'
    round: 'Round'
    result-title: 'Results'
    update-in-second: 'Updated in {second} sec.'
    not-available: 'Not Available'
    timetable: 'Timetable'
    placing-table: 'Placing Table'
    overall-result: 'Overall Result'
    champion: 'Champion'
    organization: 'Organization'
    department: 'Department'
    class: 'Class'
    points: 'Points'
    realtime-result: 'Realtime Result, Reference Only'
    official-result: 'Official Result'
  zh-TW:
    list: '成績總表'
    list-heat: '分組成績'
    list-more: '詳細記錄'
    time: '時間'
    division: '組別'
    event: '項目'
    round: '賽別'
    result-title: '成績公告'
    update-in-second: '{second} 秒後更新'
    not-available: '尚未公告'
    timetable: '賽程成績'
    placing-table: '名次統計'
    overall-result: '成績總表'
    champion: '總錦標'
    organization: '組織單位'
    department: '分部/系所'
    class: '班級'
    points: '積分'
    realtime-result: '即時成績僅供參考'
    official-result: '正式成績'
  </i18n>