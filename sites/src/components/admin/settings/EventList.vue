<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import EditEvent from '@/components/admin/settings/EditEvent.vue';
  import FullModal from '@/components/FullModal.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);
  const isLoading = ref(false);
  const eventList: any = ref(null);
  async function getEventList() {
    await vr.Get('event', eventList);
  }

  const userData: any = ref(null);
  (async () => {
    isLoading.value = true;
    await vr.Get('auth/admin/info', userData, true, true);
    await getEventList();
    isLoading.value = false;
  })();

  const selectedData = ref(null);
  function open(input: any) {
    selectedData.value = input;
    displayModal.value = true;
  }
</script>

<template>
  <div class="h-full overflow-auto" v-if="isLoading == false">
    <table>
      <tr>
        <th>運動類型</th>
        <th>項目名稱</th>
        <th>項目代碼</th>
        <th>顯示</th>
        <th>多人</th>
        <th>人數</th>
        <th>混合項目</th>
        <th>
          <a class="hyperlink blue" @click="open(null)">增加</a>
        </th>
      </tr>
      <template v-for="(item, index) in eventList">
        <tr v-if="store.userInfo.permission > 2 || JSON.parse(userData.sport_management_list).includes(item.sport_id)">
          <td>{{ item.sport_name_ch }}</td>
          <td>{{ item.event_ch }}</td>
          <td>{{ item.event_code }}</td>
          <td>
            <span class="text-green-500" v-if="item.display == 1">顯示</span>
            <span class="text-red-500" v-else>隱藏</span>
          </td>
          <td>
            <span class="text-green-500" v-if="item.multiple == 1">是</span>
            <span class="text-red-500" v-else>否</span>
          </td>
          <td>{{ item.player_num }}</td>
          <td>
            <span class="text-green-500" v-if="item.combined == 1">是</span>
            <span class="text-red-500" v-else>否</span>
          </td>
          <td>
            <a class="hyperlink blue" @click="open(item)">查看</a>
          </td>
        </tr>
      </template>
    </table>
    <FullModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal">頁面設定</div>
        </div>
      </template>
      <template v-slot:content>
        <EditEvent v-if="displayModal" @closeModal="displayModal = false" :input-data="selectedData" @refreshPage="getEventList"></EditEvent>
      </template>
    </FullModal>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium;
  }
  th {
    @apply bg-blue-100 sticky top-0;
  }
}
</style>