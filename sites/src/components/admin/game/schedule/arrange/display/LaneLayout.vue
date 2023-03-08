<script setup lang="ts">
  import { ref, watch } from 'vue';
  
  const props = defineProps(['inputData', 'phaseNum', 'trackData', 'isMultiple']);
  const dataList: any = ref([]);
  const noData = ref(false);
  const displayData: any = ref([]);
  function getData() {
    dataList.value = [];
    dataList.value = props.inputData;
    dataList.value.sort((a: any, b: any) => a[`r${[props.phaseNum]}_heat`] - b[`r${[props.phaseNum]}_heat`] || a[`r${[props.phaseNum]}_lane`] - b[`r${[props.phaseNum]}_lane`]);
    if (dataList.value.length == 0) {
      noData.value = true;
    } else {
      const maxHeat = dataList.value[dataList.value.length - 1][`r${[props.phaseNum]}_heat`];
      const heatArray: any = new Array(maxHeat).fill([]);
      for (let i = 0; i <= maxHeat; i++) {
        const temp: any = new Array(props.trackData.length + 1).fill(null);
        heatArray[i] = temp;
      }
      for (let i = 0; i <= maxHeat; i++) {
        for (let j = 0; j <= props.trackData.length; j++) {
          if (i == 0) {
            if (j > 0) {
              heatArray[i][j] = `第 ${ j } 道`;
            } else {
              heatArray[i][j] = '組別｜道次';
            }
          } else {
            if (j == 0) {
              heatArray[i][j] = `第 ${ i } 組`;
            } else {
              const temp = dataList.value.find((item: any) => item[`r${[props.phaseNum]}_heat`] == i && item[`r${[props.phaseNum]}_lane`] == j);
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
  <div v-if="noData" class="text-lg py-2">目前無資料</div>
  <div v-else class="w-full overflow-auto">
    <table v-if="displayData.length > 0">
      <template v-for="(heat, indexA) in displayData" :key="indexA">
        <tr>
          <template v-for="(lane, indexB) in heat" :key="indexB">
            <template v-if="lane != null">
              <td v-if="indexA == 0 || indexB == 0">{{ lane }}</td>
              <td v-else-if="props.isMultiple == 0">
                <div>{{ lane.last_name_ch }}{{ lane.first_name_ch }}</div>
                <div class="text-sm">{{ lane.org_name_ch }}</div>
                <div class="text-xs w-16 m-auto">{{ lane.dept_name_ch }} <span v-if="lane.num_in_dept > 0">{{ lane.num_in_dept.toString().padStart(2, '0') }}</span></div>
              </td>
              <td v-else>
                <div>{{ lane.team_name }}</div>
                <div class="text-sm">{{ lane.org_name_ch }}</div>
                <div class="text-xs w-16 m-auto">{{ lane.dept_name_ch }}</div>
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
  @apply w-[1024px] lg:w-full;
  td {
    @apply p-2 text-center border-[1px];
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