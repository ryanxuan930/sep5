<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router'

  const store = useUserStore();
  const route = useRoute();
  const vr = new VueRequest(store.token);
  const dataList: any = ref([]);
  async function getData() {
    const ind = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/athlete`, null, true, true);
    const grp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/athlete`, null, true, true);
  }
  getData();
</script>

<template>
  <div class="overflow-auto">
    <table>
      <tr>
        <th>組別</th>
        <th>項目</th>
        <th>組織單位</th>
        <th>分部/系所</th>
        <th>隊名</th>
      </tr>
      <template v-for="(item, index) in dataList">
        <tr>
          <td>{{ item.division_ch }}</td>
          <td>{{ item.event_ch }}</td>
          <td>{{ item.org_name_ch }}</td>
          <td>{{ item.dept_name_ch }}</td>
          <td>{{ item.team_name }}</td>
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