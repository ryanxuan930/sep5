<script setup lang="ts">
  import { ref, watch } from 'vue';
  import { getTargetPhase } from '@/components/library/functions';
  
  const props = defineProps(['inputData', 'phaseNum', 'paramData']);
  const phaseList = ['ref', 'r1', 'r2', 'r3', 'r4'];
  const dataList: any = ref([]);
  const noData = ref(false);
  const displayData: any = ref([]);
  const targetPhase = getTargetPhase(props.phaseNum, props.paramData);

  function getData() {
    dataList.value = [];
    dataList.value = props.inputData;
    dataList.value.sort((a: any, b: any) => a[`r${[props.phaseNum]}_heat`] - b[`r${[props.phaseNum]}_heat`] || a[`r${[props.phaseNum]}_lane`] - b[`r${[props.phaseNum]}_lane`]);
    if (dataList.value.length == 0) {
      noData.value = true;
    } else {
      const maxHeat = dataList.value[dataList.value.length - 1][`r${[props.phaseNum]}_heat`];
      const maxLane = dataList.value.reduce((a: any, b: any) => a[`r${[props.phaseNum]}_lane`] > b[`r${[props.phaseNum]}_lane`] ? a : b)[`r${[props.phaseNum]}_lane`];
      const heatArray: any = new Array(maxLane + 1).fill([]);
      for (let i = 0; i <= maxLane; i++) {
        const temp: any = new Array(maxHeat + 1).fill(null);
        heatArray[i] = temp;
      }
      for (let i = 0; i <= maxLane; i++) {
        for (let j = 0; j <= maxHeat; j++) {
          if (i == 0) {
            if (j > 0) {
              heatArray[i][j] = `第 ${ j } 組<br>Group ${ j }`;
            } else {
              heatArray[i][j] = '序號｜組別<br>No.｜Group';
            }
          } else {
            if (j == 0) {
              heatArray[i][j] = `${ i }`;
            } else {
              const temp = dataList.value.find((item: any) => item[`r${[props.phaseNum]}_lane`] == i && item[`r${[props.phaseNum]}_heat`] == j);
              if (temp) {
                heatArray[i][j] = temp;
              }
            }
          }
        }
      }
      displayData.value = heatArray;
    }
  }
  getData();
  watch(props.phaseNum, () => {
    console.log(1);
    displayData.value = [];
    getData();
  });
  
</script>

<template>
  <div v-if="noData" class="text-lg py-2">目前無資料 No data</div>
   <div v-else class="h-full w-full overflow-auto">
    <table v-if="displayData.length > 0">
      <template v-for="(lane, indexA) in displayData" :key="indexA">
        <tr>
          <template v-for="(heat, indexB) in lane" :key="indexB">
            <template v-if="heat != null">
              <td v-if="indexA == 0 || indexB == 0" v-html="heat"></td>
              <td v-else>
                  <div>
                    <div>{{ heat.last_name_ch }}{{ heat.first_name_ch }}</div>
                    <div class=text-sm>{{ heat.org_name_ch }}<span v-if="heat.dept_name_ch == ''">-{{ heat.dept_name_ch }}</span> <span v-if="heat.num_in_dept > 0">{{ heat.num_in_dept.toString().padStart(2, '0') }}</span></div>
                    <div class="text-sm">({{ heat[`${phaseList[targetPhase]}_result`] }})</div>
                  </div>
              </td>
            </template>
            <td v-else></td>
          </template>
        </tr>
      </template>
    </table>
   </div>
</template>

<style scoped lang="scss">
table {
  @apply min-w-[320px] w-full overflow-auto;
  td {
    @apply p-2 text-left border-[1px];
  }
  tr:first-child {
    td {
      @apply bg-blue-100;
    }
  }
  td:first-child {
    @apply bg-blue-50;
  }
}
</style>