<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { useGameStore } from '@/stores/game';

const props = defineProps(['inputData', 'lastRound']);
const route = useRoute();
const gameStore = useGameStore();
const dataList: any = ref(props.inputData);
const round = Number(route.params.round);
const multiple = Number(route.params.multiple);
const temp1: any = [];
const temp2: any = [];
props.inputData.forEach((element: any) => {
  if (element[`r${[round]}_ranking`] > 0) {
    temp1.push(element);
  } else {
    temp2.push(element);
  }
});
temp1.sort((a: any, b: any) => a[`r${[round]}_ranking`]- b[`r${[round]}_ranking`]);
dataList.value = temp1.concat(temp2);
</script>

<template>
  <div>
    <table class="result-table">
      <tr>
        <th>
          <div class="ch-content">排名</div>
          <div class="en-content">Place</div>
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
          <div class="en-content">Season Best</div>
        </th>
        <th>
          <div class="ch-content">比賽成績</div>
          <div class="en-content">Result</div>
        </th>
        <th>
          <div class="ch-content">備註</div>
          <div class="en-content">Remark</div>
        </th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr v-if="item[`r${[round]}_heat`] > 0 && item[`r${[round]}_lane`] > 0">
          <td style="width: 5%">{{ item[`r${round}_ranking`] }}</td>
          <td style="width: 6%">{{ item[`r${[round]}_heat`] }}</td>
          <td style="width: 5%">{{ item[`r${[round]}_lane`] }}</td>
          <td style="width: 20%">
            <div class="ch-content">{{ item.org_name_ch }}</div>
            <div v-if="gameStore.data.options.regUnit < 2" class="font9">{{ item.dept_name_ch }}</div>
            <div class="en-content">{{ item.org_name_en }}</div>
            <div v-if="gameStore.data.options.regUnit < 2" class="font7">{{ item.dept_name_en }}</div>
          </td>
          <td v-if="multiple == 0" style="width: 20%">
            <div class="ch-content">{{ item.last_name_ch }}{{ item.first_name_ch }}</div>
            <div class="en-content" v-if="item.last_name_ch != item.last_name_en && item.first_name_ch != item.first_name_en && item.last_name_en != '' && item.first_name_en != ''"></div>
          </td>
          <td v-else>{{ item.team_name }}</td>
          <td style="width: 12%">{{ item[`${props.lastRound}_result`] }}</td>
          <td style="width: 10%">{{ item[`r${round}_result`] }}</td>
          <td style="width: 15%">
            <div class="flex items-center gap-3">
              <template v-if="item[`r${round}_options`].break != undefined">
                <div class="px-2 py-0.5 bg-gray-500 text-white inline-block" v-if="item[`r${round}_options`].break != 'no'">{{ item[`r${round}_options`].break }}</div>
              </template>
              <div v-if="item[`r${round}_options`].qualified != undefined">{{ item[`r${round}_options`].qualified }}</div>
            </div>
            <div v-if="item[`r${round}_options`].windspeed != undefined">WS: {{ item[`r${round}_options`].windspeed }}</div>
            <div v-if="item[`r${round}_options`].rt != undefined">RT: {{ item[`r${round}_options`].rt }}</div>
          </td>
        </tr>
      </template>
    </table>
    <div class="height2"></div>
    <div class="font8">Q：分組錄取 q：全體擇優 DNS：未出賽 DNF：未完賽 DQ：犯規 NM：無成功試跳(擲)成績 CR：大會紀錄 NR：國家紀錄 WD：
      風速 RT：反應時間
    </div>
    <div class="grid grid-cols-2 gap-2">
      <div class="check-content">
        <div>紀錄：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>製表時間：</div>
        <div class="flex-grow">{{ new Date().toLocaleString('zh-TW', { hour12: false}) }}</div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped src="@/components/admin/game/common/print/lane/page.scss"></style>