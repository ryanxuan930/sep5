<script setup lang="ts">
  import { ref } from 'vue';
  import Config from '@/assets/config.json'
  import { useRoute } from 'vue-router';
  import type { RouteLocationNormalizedLoaded } from 'vue-router';
  
  const props = defineProps(['inputData', 'phaseNum', 'isMultiple', 'gameData']);
  const route: RouteLocationNormalizedLoaded = useRoute();
  const dataList: any = ref([]);
  const temp1: any = [];
  const temp2: any = [];
  if (route.query.status != undefined && route.query.status == '4') {
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
  } else {
    props.inputData.forEach((element: any) => {
      element[`r${[props.phaseNum]}_options`] = JSON.parse(element[`r${[props.phaseNum]}_options`]);
      dataList.value.push(element);
    });
    dataList.value.sort((a: any, b: any) => a[`r${[props.phaseNum]}_heat`]- b[`r${[props.phaseNum]}_heat`] || a[`r${[props.phaseNum]}_lane`]- b[`r${[props.phaseNum]}_lane`]);
  }
</script>

<template>
  <div class="w-full h-full overflow-auto">
    <table>
      <tr>
        <th rowspan="2">
          <div>名次</div>
          <div class="text-sm">Place</div>
        </th>
        <th v-if="!Config.deptAsClass" rowspan="2">
          <div>組織單位</div>
          <div class="text-sm">Organization</div>
        </th>
        <template v-if="props.gameData.options.regUnit < 2">
          <th v-if="Config.deptAsClass" rowspan="2">
            <div>班級</div>
            <div class="text-sm">Class</div>
          </th>
          <th v-else rowspan="2">
            <div>分部/系所</div>
            <div class="text-sm">Department</div>
          </th>
        </template>
        <th v-if="Config.deptAsClass && props.isMultiple == 0" rowspan="2">
          <div>座號</div>
          <div class="text-sm">No.</div>
        </th>
        <th v-if="props.isMultiple == 0" rowspan="2">
          <div>姓名</div>
          <div class="text-sm">Name</div>
        </th>
        <th v-else rowspan="2">
          <div>隊名</div>
          <div class="text-sm">Team</div>
        </th>
        <th rowspan="2">
          <div>組別</div>
          <div class="text-sm">Heat</div>
        </th>
        <th rowspan="2">
          <div>道次</div>
          <div class="text-sm">Lane</div>
        </th>
        <th colspan="6">
          <span>詳細記錄</span>
          <span class="text-sm ml-2">Attempts</span>
        </th>
        <th rowspan="2">
          <div>最終成績</div>
          <div class="text-sm">Final Result</div>
        </th>
        <th rowspan="2">
          <div>備註</div>
          <div class="text-sm">Remarks</div>
        </th>
      </tr>
      <tr>
        <th>1</th>
        <th>2</th>
        <th>3</th>
        <th>4</th>
        <th>5</th>
        <th>6</th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr v-if="item[`r${[props.phaseNum]}_heat`] > 0 && item[`r${[props.phaseNum]}_lane`] > 0">
          <td>
            <div v-if="item[`r${[props.phaseNum]}_ranking`] > 0">{{ item[`r${[props.phaseNum]}_ranking`] }}</div>
          </td>
          <td v-if="!Config.deptAsClass">
            <div>{{ item.org_name_full_ch }}</div>
            <div class="text-sm">{{ item.org_name_en }}</div>
          </td>
          <td v-if="props.gameData.options.regUnit < 2">
            <div>{{ item.dept_name_ch }}</div>
            <div class="text-sm">{{ item.dept_name_en }}</div>
          </td>
          <td v-if="Config.deptAsClass && props.isMultiple == 0">{{ item.num_in_dept }}</td>
          <td v-if="props.isMultiple == 0">{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
          <td v-else>{{ item.team_name }}</td>
          <td>{{ item[`r${[props.phaseNum]}_heat`] }}</td>
          <td>{{ item[`r${[props.phaseNum]}_lane`] }}</td>
          <td v-for="(attempt, indexB) in item[`r${[props.phaseNum]}_options`].performance.attempt" :key="indexB">
            <div>{{ attempt }}</div>
            <div class="text-xs">{{ item[`r${[props.phaseNum]}_options`].performance.winds[indexB] }}</div>
          </td>
          <td>{{ item[`r${[props.phaseNum]}_result`] }}</td>
          <td>
            <div class="flex items-center gap-3">
              <template v-if="item[`r${props.phaseNum}_options`].cr != undefined">
                <div class="px-2 py-0.5 bg-blue-400 rounded text-white inline-block" v-if="item[`r${props.phaseNum}_options`].cr">CR</div>
              </template>
              <template v-if="item[`r${props.phaseNum}_options`].nr != undefined">
                <div class="px-2 py-0.5 bg-blue-500 rounded text-white inline-block" v-if="item[`r${props.phaseNum}_options`].nr">NR</div>
              </template>
              <div v-if="item[`r${[props.phaseNum]}_options`].qualified != undefined">{{ item[`r${[props.phaseNum]}_options`].qualified }}</div>
            </div>
            <template v-if="item[`r${props.phaseNum}_options`].break != undefined">
              <div v-if="item[`r${props.phaseNum}_options`].break != null">{{ item[`r${props.phaseNum}_options`].break }}</div>
            </template>
            <div class="italic" v-if="item[`r${[props.phaseNum]}_options`].remark != undefined">{{ item[`r${[props.phaseNum]}_options`].remark }}</div>
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