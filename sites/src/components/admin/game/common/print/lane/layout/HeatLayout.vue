<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { useGameStore } from '@/stores/game';

const props = defineProps(['inputData', 'lastRound']);
const route = useRoute();
const gameStore: any = useGameStore();
const orginalData: any = ref(props.inputData);
const dataList: any = ref([]);
const round = Number(route.params.round);
const multiple = Number(route.params.multiple);
let counter = 0;
let index = 0;
orginalData.value.sort((a: any, b: any) => a[`r${[round]}_heat`] - b[`r${[round]}_heat`] || a[`r${[round]}_lane`] - b[`r${[round]}_lane`]);
for(const item of orginalData.value) {
  if (item[`r${[round]}_heat`] > 0 && item[`r${[round]}_lane`] > 0) {
    if (item[`r${[round]}_heat`] != counter) {
      index ++;
      counter = item[`r${[round]}_heat`];
      dataList.value.push({heat: item[`r${[round]}_heat`], data: []});
    }
    dataList.value[index - 1].data.push(item);
    counter = item[`r${[round]}_heat`];
    
  }
}
</script>

<template>
  <div>
    <template v-for="(data, index) in dataList" :key="index">
      <div :class="{'items-end': true, 'break-after-page': index < dataList.length - 1}">
        <div class="flex">
          <div class="text-xl font-medium">
            <span>第 {{ data.heat }} 組 Heat {{ data.heat }}</span>
          </div>
          <div class="flex-grow"></div>
          <div>第{{ index + 1 }}頁 / 共 {{ dataList.length }} 頁</div>
        </div>
        <div class="height1"></div>
        <table :class="{'result-table': true, 'break-after-page': index < dataList.length - 1}">
          <tr>
            <th>
              <div class="ch-content">檢錄</div>
              <div class="en-content">Check in</div>
            </th>
            <th>
              <div class="ch-content">組別</div>
              <div class="en-content">Heat</div>
            </th>
            <th>
              <div class="ch-content">道次</div>
              <div class="en-content">Lane</div>
            </th>
            <th>
              <div class="ch-content">單位</div>
              <div class="en-content">Organization</div>
            </th>
            <th v-if="multiple == 0">
              <div class="ch-content">選手</div>
              <div class="en-content">Athlete</div>
            </th>
            <th v-else>
              <div class="ch-content">隊伍</div>
              <div class="en-content">Team</div>
            </th>
            <th>
              <div class="ch-content">參考成績</div>
              <div class="en-content">Prev. Result</div>
            </th>
            <th>
              <div class="ch-content">比賽成績</div>
              <div class="en-content">Result</div>
            </th>
            <th>
              <div class="ch-content">排名</div>
              <div class="en-content">Place</div>
            </th>
            <th>
              <div class="ch-content">備註</div>
              <div class="en-content">Remark</div>
            </th>
          </tr>
          <template v-for="(item, index) in data.data" :key="index">
            <tr v-if="item[`r${[round]}_heat`] > 0 && item[`r${[round]}_lane`] > 0">
              <td style="width: 8%">
                <div v-if="item[`r${[round]}_result`] == 'DNS'">{{ item[`r${[round]}_result`] }}</div>
                <div v-if="item[`r${[round]}_result`] == 'DNF'">{{ item[`r${[round]}_result`] }}</div>
                <div v-if="item[`r${[round]}_result`] == 'DQ'">{{ item[`r${[round]}_result`] }}</div>
                <div v-if="item[`r${[round]}_result`] == 'NM'">{{ item[`r${[round]}_result`] }}</div>
              </td>
              <td style="width: 4%">{{ item[`r${[round]}_heat`] }}</td>
              <td style="width: 4%">{{ item[`r${[round]}_lane`] }}</td>
              <td style="width: 20%">
                <div class="flex items-end gap-2">
                  <div class="ch-content flex-shrink-0">{{ item.org_name_ch }}</div>
                  <div v-if="gameStore.data.options.regUnit < 2">{{ item.dept_name_ch }}</div>
                </div>
                <!--
                <div class="flex items-end gap-2">
                  <div class="en-content flex-shrink-0">{{ item.org_name_en }}</div>
                  <div v-if="gameStore.data.options.regUnit < 2" class="font7">{{ item.dept_name_en }}</div>
                </div>
                -->
              </td>
              <td v-if="multiple == 0" style="width: 20%">
                <div class="ch-content">
                  <span v-if="item.bib != undefined && item.bib != null">{{ item.bib }} </span>
                  {{ item.last_name_ch }}{{ item.first_name_ch }}
                </div>
                <div class="en-content" v-if="item.last_name_ch != item.last_name_en && item.first_name_ch != item.first_name_en && item.last_name_en != '' && item.first_name_en != ''"></div>
              </td>
              <td v-else>{{ item.team_name }}</td>
              <td style="width: 10%">{{ item[`${props.lastRound}_result`] }}</td>
              <td style="width: 10%; padding: 0;" class="h-1">
                <div class="flex items-stretch h-full">
                  <div class="inline-block pos-line basis-[15%]"></div>
                  <div class="inline-block pos-line basis-[15%]"></div>
                  <div class="inline-block pos-line basis-[5%]"></div>
                  <div class="inline-block pos-line basis-[15%]"></div>
                  <div class="inline-block pos-line basis-[15%]"></div>
                  <div class="inline-block pos-line basis-[5%]"></div>
                  <div class="inline-block pos-line basis-[15%]"></div>
                  <div class="inline-block pos-line basis-[15%]"></div>
                </div>
              </td>
              <td style="width: 4%"></td>
              <td style="width: 20%"></td>
            </tr>
          </template>
        </table>
      </div>
    </template>
    <div class="grid grid-cols-8 gap-2">
      <div class="check-content">
        <div class="check-box"></div>
        <div>終點</div>
      </div>
      <div class="check-content">
        <div class="check-box"></div>
        <div>發令</div>
      </div>
      <div class="check-content">
        <div class="check-box"></div>
        <div>計時</div>
      </div>
      <div class="check-content">
        <div class="check-box"></div>
        <div>司令台</div>
      </div>
      <div class="check-content">
        <div class="check-box"></div>
        <div>檢查</div>
      </div>
      <div class="check-content">
        <div class="check-box"></div>
        <div>檢錄</div>
      </div>
      <div class="check-content">
        <div class="check-box"></div>
        <div>終點攝影</div>
      </div>
      <div class="check-content">
        <div>備註：</div>
      </div>
    </div>
    <div class="height1"></div>
    <div class="grid grid-cols-6 gap-2" v-if="gameStore.data.sport_code == 'athl'">
      <div class="check-content">
        <div>檢錄員：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>檢錄主任：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>檢錄裁判長：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>終點紀錄：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>終點裁判長：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>徑賽裁判長：</div>
        <div class="flex-grow"></div>
      </div>
    </div>
    <div class="grid grid-cols-5 gap-2" v-else>
      <div class="check-content">
        <div>裁判長：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>檢錄主任：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>計時主任：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>紀錄主任：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>播報：</div>
        <div class="flex-grow"></div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped src="@/components/admin/game/common/print/lane/page.scss"></style>