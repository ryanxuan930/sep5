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
          <th rowspan="4">
            <div class="ch-content">檢錄</div>
            <div class="en-content">Check in</div>
          </th>
          <th rowspan="4">
            <div class="ch-content">組別</div>
            <div class="en-content">Heat</div>
          </th>
          <th rowspan="4">
            <div class="ch-content">道次</div>
            <div class="en-content">Lane</div>
          </th>
          <th rowspan="4">
            <div class="ch-content">單位</div>
            <div class="en-content">Organization</div>
          </th>
          <th rowspan="4">
            <div class="ch-content">選手</div>
            <div class="en-content">Athlete</div>
          </th>
          <th rowspan="4">
            <div class="ch-content">參考成績</div>
            <div class="en-content">Season Best</div>
          </th>
          <template v-for="item in [24, 23, 22, 21, 20, 19]">
            <th class="text-center">{{ item }}</th>
          </template>
          <th rowspan="4" class="text-center">0</th>
          <th rowspan="4">
            <div class="ch-content">比賽成績</div>
            <div class="en-content">Result</div>
          </th>
          <th rowspan="4">
            <div class="ch-content">排名</div>
            <div class="en-content">Place</div>
          </th>
          <th rowspan="4">
            <div class="ch-content">備註</div>
            <div class="en-content">Remark</div>
          </th>
        </tr>
        <tr>
          <template v-for="item in [18, 17, 16, 15, 14, 13]">
            <th class="text-center">{{ item }}</th>
          </template>
        </tr>
        <tr>
          <template v-for="item in [12, 11, 10, 9, 8, 7]">
            <th class="text-center">{{ item }}</th>
          </template>
        </tr>
        <tr>
          <template v-for="item in [6, 5, 4, 3, 2, 1]">
            <th class="text-center">{{ item }}</th>
          </template>
        </tr>
        <template v-for="(item, index) in data.data" :key="index">
          <tr v-if="item[`r${[round]}_heat`] > 0 && item[`r${[round]}_lane`] > 0">
            <td rowspan="4" style="width: 8%">
              <div v-if="item[`r${[round]}_result`] == 'DNS'">{{ item[`r${[round]}_result`] }}</div>
              <div v-if="item[`r${[round]}_result`] == 'DNF'">{{ item[`r${[round]}_result`] }}</div>
              <div v-if="item[`r${[round]}_result`] == 'DQ'">{{ item[`r${[round]}_result`] }}</div>
              <div v-if="item[`r${[round]}_result`] == 'NM'">{{ item[`r${[round]}_result`] }}</div>
            </td>
            <td rowspan="4" style="width: 4%">{{ item[`r${[round]}_heat`] }}</td>
            <td rowspan="4" style="width: 4%">{{ item[`r${[round]}_lane`] }}</td>
            <td rowspan="4" style="width: 20%">
              <div class="ch-content">{{ item.org_name_ch }}</div>
              <div v-if="gameStore.data.options.regUnit < 2" class="font9">{{ item.dept_name_ch }}</div>
              <!--
              <div class="en-content">{{ item.org_name_en }}</div>
              <div v-if="gameStore.data.options.regUnit < 2" class="font7">{{ item.dept_name_en }}</div>
              -->
            </td>
            <td rowspan="4" style="width: 10%">
              <div class="ch-content">
                <span v-if="item.bib != undefined && item.bib != null">{{ item.bib }} </span>
                {{ item.last_name_ch }}{{ item.first_name_ch }}
              </div>
              <div class="en-content" v-if="item.last_name_ch != item.last_name_en && item.first_name_ch != item.first_name_en && item.last_name_en != '' && item.first_name_en != ''"></div>
            </td>
            <td rowspan="4" style="width: 10%">{{ item[`${props.lastRound}_result`] }}</td>
            <template v-for="item in 6">
              <td style="width: 3%;"></td>
            </template>
            <td rowspan="4" style="width: 3%"></td>
            <td rowspan="4" style="width: 10%; padding: 0;" class="h-1">
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
            <td rowspan="4" style="width: 4%"></td>
            <td rowspan="4" style="width: 9%"></td>
          </tr>
          <tr>
            <template v-for="item in 6">
              <td style="width: 3%" class="bg-gray-50"></td>
            </template>
          </tr>
          <tr>
            <template v-for="item in 6">
              <td style="width: 3%"></td>
            </template>
          </tr>
          <tr>
            <template v-for="item in 6">
              <td style="width: 3%" class="bg-gray-50"></td>
            </template>
          </tr>
        </template>
      </table>
    </template>
    <div class="height2"></div>
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
      <div class="height2"></div>
      <div class="grid grid-cols-3 gap-2">
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
  </div>
</template>

<style lang="scss" scoped src="@/components/admin/game/common/print/lane/page.scss"></style>