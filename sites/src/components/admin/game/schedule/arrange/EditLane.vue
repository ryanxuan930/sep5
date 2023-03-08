<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const sportCode = route.params.sportCode;
const gameId = route.params.gameId;

const props = defineProps(['inputData', 'paramList', 'phaseNum']);
const dataList = ref(props.inputData);
dataList.value.sort((a: any, b: any) => {
  return a[`r${props.phaseNum}_heat`] - b[`r${props.phaseNum}_heat`] || a[`r${props.phaseNum}_lane`] - b[`r${props.phaseNum}_lane`]
});

const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
const close = () => {
  emit('refreshPage');
  emit('closeModal');
}

async function submitAll() {
  const phaseArray = ['ref', 'r1', 'r2', 'r3', 'r4'];
  let dataset: any = [];
  let res: any = null;
  if(props.paramList.multiple == 1){
    dataList.value.forEach((d: any) => {
      dataset.push({
        team_id: d.team_id,
        division_id: d.division_id,
        event_code: d.event_code,
        phase: phaseArray[props.phaseNum],
        heat: d[`${phaseArray[props.phaseNum]}_heat`],
        lane: d[`${phaseArray[props.phaseNum]}_lane`],
      });
    });
    res = await vr.Patch(`game/${sportCode}/${gameId}/common/group/update/heat-lane`, dataset, null, true, true);
  } else {
    dataList.value.forEach((d: any) => {
      dataset.push({
        u_id: d.u_id,
        division_id: d.division_id,
        event_code: d.event_code,
        phase: phaseArray[props.phaseNum],
        heat: d[`${phaseArray[props.phaseNum]}_heat`],
        lane: d[`${phaseArray[props.phaseNum]}_lane`],
      });
    });
    res = await vr.Patch(`game/${sportCode}/${gameId}/common/individual/update/heat-lane`, dataset, null, true, true);
  }
  if (res.status == 'A01') {
    alert('已儲存');
    close();
  } else {
    alert('儲存失敗');
  }
}

</script>

<template>
  <div>
    <table>
      <tr>
        <th>組織單位</th>
        <th>系所/分部</th>
        <th>名稱</th>
        <th>組別</th>
        <th>道次</th>
      </tr>
      <template v-for="(item, index) in dataList">
        <tr>
          <td>{{ item.org_name_ch }}</td>
          <td>{{ item.dept_name_ch }}</td>
          <td v-if="props.paramList.remarks == 'rr'">{{ item.team_name }}</td>
          <td v-else>{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
          <td>
            <input type="number" v-model.trim="item[`r${props.phaseNum}_heat`]">
          </td>
          <td>
            <input type="number" v-model.trim="item[`r${props.phaseNum}_lane`]">
          </td>
        </tr>
      </template>
    </table>
    <div class="pt-5">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-full;
  td, th {
    @apply p-2 text-center border-[1px] w-1/5;
  }
  tr:first-child {
    td, th {
      @apply bg-blue-100;
    }
  }
  input {
    @apply p-1 border-2 rounded w-16;
  }
}
</style>