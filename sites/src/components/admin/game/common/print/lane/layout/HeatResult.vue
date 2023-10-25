<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { useGameStore } from '@/stores/game';

const props = defineProps(['inputData', 'lastRound']);
const route = useRoute();
const gameStore = useGameStore();
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
      <table class="result-table">
        <tr>
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
            <td style="width: 6%">{{ item[`r${[round]}_heat`] }}</td>
            <td style="width: 5%">{{ item[`r${[round]}_lane`] }}</td>
            <td style="width: 20%">
              <div class="ch-content">{{ item.org_name_ch }}</div>
              <div v-if="gameStore.data.options.regUnit < 2">{{ item.dept_name_ch }}</div>
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
            <td style="width: 5%">{{ item[`r${round}_ranking`] }}</td>
            <td style="width: 15%">
              <div class="flex items-center gap-3">
                <template v-if="item[`r${round}_options`].cr != undefined">
                  <div class="px-2 py-0.5 bg-gray-500 text-white inline-block" v-if="item[`r${round}_options`].cr">CR</div>
                </template>
                <template v-if="item[`r${round}_options`].nr != undefined">
                  <div class="px-2 py-0.5 bg-gray-500 text-white inline-block" v-if="item[`r${round}_options`].nr">NR</div>
                </template>
                <div v-if="item[`r${round}_options`].qualified != undefined">{{ item[`r${round}_options`].qualified }}</div>
              </div>
              <div v-if="item[`r${round}_options`].windspeed != undefined">W: {{ item[`r${round}_options`].windspeed }}</div>
              <div v-if="item[`r${round}_options`].rt != undefined">RT: {{ item[`r${round}_options`].rt }}</div>
              <div v-if="item[`r${round}_options`].sameResult != undefined">({{ item[`r${round}_options`].sameResult }})</div>
              <template v-if="item[`r${round}_options`].break != undefined">
                <div v-if="item[`r${round}_options`].break != null">{{ item[`r${round}_options`].break }}</div>
              </template>
            </td>
          </tr>
        </template>
      </table>
    </template>
  </div>
</template>

<style lang="scss" scoped src="@/components/admin/game/common/print/lane/page.scss"></style>