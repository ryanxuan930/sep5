<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);
  const isLoading = ref(false);

  const eventList: any = ref(null);
  vr.Get('event', eventList);
</script>

<template>
  <div>
    <table v-if="eventList != null">
      <tr>
        <th>運動類型</th>
        <th>項目名稱</th>
        <th>項目代碼</th>
        <th>顯示</th>
        <th>多人</th>
        <th>人數</th>
        <th>混合項目</th>
      </tr>
      <template v-for="(item, index) in eventList">
        <tr>
          <td></td>
          <td>{{ item.event_ch }}</td>
          <td>{{ item.event_code }}</td>
        </tr>
      </template>
    </table>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium;
  }
  th {
    @apply bg-blue-100;
  }
}
</style>