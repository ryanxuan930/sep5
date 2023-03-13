<script setup lang="ts">
  import { stringToMilliseconds } from '@/components/library/functions';
import { ref } from 'vue';
  
  const props = defineProps(['inputData', 'phaseNum']);
  const dataList: any = ref([]);
  const notAcceptResult = [null, 'null', 'DQ', 'DNS', 'DNF', 'NM', undefined];
  props.inputData.forEach((element: any) => {
    if (element[`r${[props.phaseNum]}_heat`] > 0 && element[`r${[props.phaseNum]}_result`] > 0) {
      element[`r${[props.phaseNum]}_options`] = JSON.parse(element[`r${[props.phaseNum]}_options`]);
      if (!notAcceptResult.includes(element[`r${props.inputData.round}_result`])) {
        element.temp = stringToMilliseconds(element[`r${props.inputData.round}_result`]);
      } else {
      element.temp = 0;
      }
      dataList.value.push(element);
    }
  });
  /*
  let temp = 0;
  let tempResult = -1;
  let place = 1;
  for(let i = 0; i < dataList.value.length; i++) {
    if (temp != dataList.value[i][`r${[props.phaseNum]}_heat`]) {
      temp = dataList.value[i][`r${[props.phaseNum]}_heat`];
      place = 1;
    }
    if (tempResult == dataList.value[i].temp) {
      dataList.value[i].pih = dataList.value[i-1].pih;
    } else {
      dataList.value[i].pih = place;
      place++;
    }
    tempResult = dataList.value[i].temp;
  }*/
  dataList.value.sort((a: any, b: any) => a[`r${[props.phaseNum]}_heat`]- b[`r${[props.phaseNum]}_heat`] || a.temp - b.temp);
</script>

<template>
  <div>
    <table>
      <tr>
        <!--
        <th>
          <div>分組排名</div>
          <div class="text-xs">Pla. in heat</div>
        </th>
        -->
        <th>
          <div>組別</div>
          <div class="text-sm">Heat</div>
        </th>
        <th>
          <div>道次</div>
          <div class="text-sm">Lane</div>
        </th>
        <th>
          <div>組織單位</div>
          <div class="text-sm">Organization</div>
        </th>
        <th>
          <div>分部/系所</div>
          <div class="text-sm">Department</div>
        </th>
        <th>
          <div>姓名</div>
          <div class="text-sm">Name</div>
        </th>
        <th>
          <div>成績</div>
          <div class="text-sm">Result</div>
        </th>
        <th>
          <div>備註</div>
          <div class="text-sm">Remarks</div>
        </th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr>
          <!--
          <td>{{ item.pih }}</td>
          -->
          <td>{{ item[`r${[props.phaseNum]}_heat`] }}</td>
          <td>{{ item[`r${[props.phaseNum]}_lane`] }}</td>
          <td>
            <div>{{ item.org_name_full_ch }}</div>
            <div class="text-sm">{{ item.org_name_en }}</div>
          </td>
          <td>
            <div>{{ item.dept_name_ch }}</div>
            <div class="text-sm">{{ item.dept_name_en }}</div>
          </td>
          <td>{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
          <td>{{ item[`r${[props.phaseNum]}_result`] }}</td>
          <td>
            <div v-if="item[`r${[props.phaseNum]}_options`].qualified != undefined">{{ item[`r${[props.phaseNum]}_options`].qualified }}</div>
            <div v-if="item[`r${[props.phaseNum]}_options`].windspeed != undefined">WS：{{ item[`r${[props.phaseNum]}_options`].windspeed }}</div>
            <div v-if="item[`r${[props.phaseNum]}_options`].rt != undefined">RT：{{ item[`r${[props.phaseNum]}_options`].rt }}</div>
          </td>
        </tr>
      </template>
    </table>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[640px] sm:w-full overflow-auto;
  th, td {
    @apply p-2 text-left border-[1px];
  }
  th {
    @apply bg-blue-100;
  }
  /*
  td:first-child {
    @apply bg-blue-50;
  }*/
}
</style>