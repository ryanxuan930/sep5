<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router'

  const store = useUserStore();
  const route = useRoute();
  const vr = new VueRequest(store.token);
  const paramList: any = ref([]);
  const sum = ref(0);
  async function getData() {
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/params/full`, paramList, true, true);
    const ind = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/count`, null, true, true);
    const grp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/count`, null, true, true);
    const temp = ind.concat(grp);
    for (let i = 0; i < paramList.value.length; i++) {
      let flag = true;
      for (let j = 0; j < temp.length; j++) {
        if (paramList.value[i].division_id === temp[j].division_id && paramList.value[i].event_code === temp[j].event_code) {
          paramList.value[i].count = temp[j].total;
          flag = false;
          break;
        }
      }
      if (flag) paramList.value[i].count = 0;
    }
    sum.value = paramList.value.reduce((a: any, b: any) => a + b.count, 0);
  }
  getData();
</script>

<template>
  <div class="overflow-auto">
    <table>
      <tr>
        <th>組別</th>
        <th>項目</th>
        <th>數量</th>
      </tr>
      <template v-for="(item, index) in paramList">
        <tr>
          <td>{{ item.division_ch }}</td>
          <td>{{ item.event_ch }}</td>
          <td>{{ item.count }}</td>
        </tr>
      </template>
      <tr>
        <td colspan="2">總計</td>
        <td>{{ sum }}</td>
      </tr>
    </table>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left overflow-scroll;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium overflow-hidden;
    div {
      @apply whitespace-nowrap overflow-auto w-full py-2;
    }
  }
  th {
    @apply bg-blue-100 sticky top-0;
  }
}  
</style>