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
            <div class="font10">檢錄</div>
            <div class="font9">Check in</div>
          </th>
          <th>
            <div class="font10">順序</div>
            <div class="font9">Ord.</div>
          </th>
          <th>
            <div class="font10">單位</div>
            <div class="font9">Organization</div>
          </th>
          <th>
            <div class="font10">選手</div>
            <div class="font9">Athlete</div>
          </th>
          <th>
            <div class="font10">參考成績</div>
            <div class="font8">Season Best</div>
          </th>
          <th class="text-center">1</th>
          <th class="text-center">2</th>
          <th class="text-center">3</th>
          <th>
            <div class="font10">順序</div>
            <div class="font9">Ord.</div>
          </th>
          <th class="text-center">4</th>
          <th class="text-center">5</th>
          <th class="text-center">6</th>
          <th>
            <div class="font10">最佳成績</div>
            <div class="font9">Result</div>
          </th>
          <th>
            <div class="font10">排名</div>
            <div class="font9">Pla.</div>
          </th>
          <th>
            <div class="font10">備註</div>
            <div class="font9">Remark</div>
          </th>
        </tr>
        <template v-for="(item, index) in data.data" :key="index">
          <tr v-if="item[`r${[round]}_heat`] > 0 && item[`r${[round]}_lane`] > 0">
            <td style="width: 6%">
              <div v-if="item[`r${[round]}_result`] == 'DNS'">{{ item[`r${[round]}_result`] }}</div>
              <div v-if="item[`r${[round]}_result`] == 'DNF'">{{ item[`r${[round]}_result`] }}</div>
              <div v-if="item[`r${[round]}_result`] == 'DQ'">{{ item[`r${[round]}_result`] }}</div>
              <div v-if="item[`r${[round]}_result`] == 'NM'">{{ item[`r${[round]}_result`] }}</div>
            </td>
            <td style="width: 4%">{{ item[`r${[round]}_lane`] }}</td>
            <td style="width: 10%">
              <div class="ch-content">{{ item.org_name_ch }}</div>
              <div v-if="gameStore.data.options.regUnit < 2" class="font9">{{ item.dept_name_ch }}</div>
              <!--
              <div class="en-content">{{ item.org_name_en }}</div>
              <div v-if="gameStore.data.options.regUnit < 2" class="font7">{{ item.dept_name_en }}</div>
              -->
            </td>
            <td style="width: 9%">
              <div class="ch-content">{{ item.last_name_ch }}{{ item.first_name_ch }}</div>
              <div class="en-content" v-if="item.last_name_ch != item.last_name_en && item.first_name_ch != item.first_name_en && item.last_name_en != '' && item.first_name_en != ''"></div>
            </td>
            <td style="width: 8%">{{ item[`${props.lastRound}_result`] }}</td>
            <td style="width: 7%"></td>
            <td style="width: 7%"></td>
            <td style="width: 7%"></td>
            <td style="width: 4%"></td>
            <td style="width: 7%"></td>
            <td style="width: 7%"></td>
            <td style="width: 7%"></td>
            <td style="width: 8%; padding: 0;" class="h-1">
              <div class="flex items-stretch h-full">
                <div class="inline-block pos-line basis-[23%]"></div>
                <div class="inline-block pos-line basis-[23%]"></div>
                <div class="inline-block pos-line basis-[8%]"></div>
                <div class="inline-block pos-line basis-[23%]"></div>
                <div class="inline-block pos-line basis-[23%]"></div>
              </div>
            </td>
            <td style="width: 4%"></td>
            <td style="width: 5%"></td>
          </tr>
        </template>
      </table>
    </template>
    <div class="grid grid-cols-4 gap-2">
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
      <div></div>
      <div class="check-content">
        <div>記錄員：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>裁判長：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>跳擲部主任：</div>
        <div class="flex-grow"></div>
      </div>
      <div class="check-content">
        <div>田賽裁判長：</div>
        <div class="flex-grow"></div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped src="@/components/admin/game/common/print/lane/page.scss"></style>