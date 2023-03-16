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
dataList.value.sort((a: any, b: any) => a[`r${[round]}_ranking`] - b[`r${[round]}_ranking`]);
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
            <div v-if="item[`r${round}_options`].qualified != undefined">{{ item[`r${round}_options`].qualified }}</div>
            <div v-if="item[`r${round}_options`].windspeed != undefined">WS: {{ item[`r${round}_options`].windspeed }}</div>
            <div v-if="item[`r${round}_options`].rt != undefined">RT: {{ item[`r${round}_options`].rt }}</div>
          </td>
        </tr>
      </template>
    </table>
    <div class="height2"></div>
    <div class="grid grid-cols-2 gap-2">
      <div class="check-content">
        <div>紀錄組：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>製表時間：</div>
        <div class="flex-grow"></div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped src="@/components/admin/game/common/print/lane/page.scss"></style>