<script setup lang="ts">
  import { ref, watch } from 'vue';
  
  const props = defineProps(['inputData', 'phaseNum']);
  const dataList: any = ref([]);
  const temp1: any = [];
  const temp2: any = [];
  props.inputData.forEach((element: any) => {
    element[`r${[props.phaseNum]}_options`] = JSON.parse(element[`r${[props.phaseNum]}_options`]);
    if (element[`r${[props.phaseNum]}_ranking`] > 0) {
      temp1.push(element);
    } else {
      temp2.push(element);
    }
  });
  temp1.sort((a: any, b: any) => a[`r${[props.phaseNum]}_ranking`]- b[`r${[props.phaseNum]}_ranking`]);
  dataList.value = temp1.concat(temp2);
</script>

<template>
  <div>
    <table>
      <tr>
        <th>
          <div>名次</div>
          <div class="text-sm">Place</div>
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
          <div>組別</div>
          <div class="text-sm">Heat</div>
        </th>
        <th>
          <div>道次</div>
          <div class="text-sm">Lane</div>
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
        <tr v-if="item[`r${[props.phaseNum]}_heat`] > 0 && item[`r${[props.phaseNum]}_lane`] > 0">
          <td>
            <div v-if="item[`r${[props.phaseNum]}_ranking`] > 0">{{ item[`r${[props.phaseNum]}_ranking`] }}</div>
          </td>
          <td>
            <div>{{ item.org_name_full_ch }}</div>
            <div class="text-sm">{{ item.org_name_en }}</div>
          </td>
          <td>
            <div>{{ item.dept_name_ch }}</div>
            <div class="text-sm">{{ item.dept_name_en }}</div>
          </td>
          <td>{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
          <td>{{ item[`r${[props.phaseNum]}_heat`] }}</td>
          <td>{{ item[`r${[props.phaseNum]}_lane`] }}</td>
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
  td:first-child {
    @apply bg-blue-50;
  }
}
</style>