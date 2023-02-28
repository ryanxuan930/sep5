<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router'
  import Config from '@/assets/config.json';

  const store = useUserStore();
  const route = useRoute();
  const vr = new VueRequest(store.token);
  const dataList: any = ref([]);
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/athlete/list`, dataList, true, true);
</script>

<template>
  <div class="overflow-auto">
    <table>
      <tr>
        <th>序號</th>
        <th>選手代碼</th>
        <th>組織單位</th>
        <th>分部/系所</th>
        <th>性別</th>
        <th v-if="Config.deptAsClass">座號</th>
        <th>姓名</th>
      </tr>
      <template v-for="(item, index) in dataList">
        <tr>
          <td>{{ index + 1 }}</td>
          <td>{{ item.athlete_id }}</td>
          <td>{{ item.org_name_full_ch }}</td>
          <td>{{ item.dept_name_ch }}</td>
          <td>
            <span v-if="item.sex == 1">男</span>
            <span v-else-if="item.sex == 2">女</span>
            <span v-else>其他</span>
          </td>
          <td v-if="Config.deptAsClass">{{ item.num_in_dept }}</td>
          <td>{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
        </tr>
      </template>
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