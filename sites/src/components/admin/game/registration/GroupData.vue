<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router'
  import SmallModal from '@/components/SmallModal.vue';
  import EditGroup from '@/components/admin/game/registration/edit/EditGroup.vue';

  const store = useUserStore();
  const route = useRoute();
  const vr = new VueRequest(store.token);
  const dataList: any = ref([]);
  const configData: any = ref([]);
  const displayModal = ref(0);
  function getData() {
    vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group`, dataList, true, true);
    vr.Get(`reg-config-game/${route.params.gameId}`, configData);
  }
  getData();

  const selectedData: any = ref({});

  function openModal(type: number, data: any = null) {
    selectedData.value = data;
    displayModal.value = type;
  }
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
        <th>
          <a class="hyperlink blue" @click="openModal(1)">新增</a>
        </th>
      </tr>
      <template v-for="(item, index) in dataList">
        <tr>
          <td>{{ item.division_ch }}</td>
          <td>{{ item.event_ch }}</td>
          <td>{{ item.org_name_ch }}</td>
          <td>{{ item.dept_name_ch }}</td>
          <td>{{ item.team_name }}</td>
          <td>
            <a class="hyperlink blue" @click="openModal(2, item)">查看</a>
          </td>
        </tr>
      </template>
    </table>
  </div>
  <SmallModal v-show="displayModal" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">新增團體報名</div>
        <div v-if="displayModal == 2">編輯團體報名</div>
      </div>
    </template>
    <template v-slot:content>
      <EditGroup v-if="displayModal == 1 || displayModal == 2" :inputData="selectedData" :gameId="route.params.gameId" :sportCode="route.params.sportCode" :regList="dataList" :regConfig="configData" @closeModal="displayModal = 0" @refreshPage="getData"  />
    </template>
  </SmallModal>
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