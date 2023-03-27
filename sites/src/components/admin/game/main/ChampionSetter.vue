<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import FullModal from '@/components/FullModal.vue';
  import EditChampion from '@/components/admin/game/main/module/EditChampion.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const props = defineProps(['inputData']);
  const isLoading = ref(false);
  const sportCode = props.inputData.sport_code;
  const gameId = props.inputData.game_id;
  const divisionList: any = ref([]);
  const championData: any = ref({});
  const selectedData: any = ref(null);

  const championPrototype = {
    isRelease: false,
    content: [],
  };
  const contentPrototype = {
    type: 'ranking', // ranking, grade
    divisionName: '',
    formula: {},
    payload: {},
  }
  const dataList:any = ref([]);
  async function getData() {
    isLoading.value = true;
    const divisions = await vr.Get(`game/${sportCode}/${gameId}/main/division`, divisionList);
    const temp = await vr.Get(`game/${sportCode}/${gameId}/common/temp/gameChampion`);
    if (temp.temp_id == undefined) {
      const res = await vr.Post(`game/${sportCode}/${gameId}/common/temp`, {temp_key: 'gameChampion', temp_data: JSON.stringify(championPrototype)}, null, true, true);
      if (res.status != 'A01') {
        alert('建立參數失敗');
      }
    } else {
      championData.value = JSON.parse(temp.temp_data);
    }
    isLoading.value = false;
  };
  getData();
  async function submitAll() {
    const res = await vr.Patch(`game/${sportCode}/${gameId}/common/temp/gameRecords`, { temp_data: JSON.stringify(dataList.value)}, null, true, true);
    if (res.status != 'A01') {
      alert('儲存失敗');
      return;
    }
    getData();
    alert('已儲存');
  }
  async function clearAll() {
    const r = confirm('確定清除全部資料？');
    if (r) {
      await vr.Delete(`game/${sportCode}/${gameId}/common/temp/gameRecords`, null, true, true);
      getData();
      alert('已清除');
    }
  }
  function openModal(type: string, input: any = null) {
    if (type == 'add') {
      selectedData.value = JSON.parse(JSON.stringify(contentPrototype));
    } else {
      selectedData.value = input;
    }
    displayModal.value = 1;
  }
</script>

<template>
  <div class="flex flex-col gap-3">
    <div>
      <button class="general-button blue">公告總錦標</button>
    </div>
    <div class="flex-grow overflow-auto config-table">
      <table>
        <tr>
          <th>組別</th>
          <th>計算方式</th>
          <th>
            <a class="hyperlink blue" @click="openModal('add', null)">新增</a>
          </th>
        </tr>
        <template v-for="(item, index) in championData.content" :key="index">
          <tr>
            <td>{{ item.divisionName }}</td>
            <td>
              <span v-if="item.type == 'ranking'">排名計算</span>
              <span v-if="item.type == 'grade'">級分計算</span>
            </td>
            <td>
              <a class="hyperlink blue">查看</a>
            </td>
          </tr>
        </template>
      </table>
    </div>
  </div>
  <div v-if="dataList.length > 0" class="my-3">
    <button class="round-full-button blue" @click="submitAll">儲存</button>
  </div>
  <div v-if="dataList.length > 0" class="my-3">
    <button class="round-full-button red" @click="clearAll">全部清除</button>
  </div>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">新增總錦標設定</div>
        <div v-if="displayModal == 2">編輯總錦標設定</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <EditChampion v-if="displayModal == 1" :input-data="selectedData" :input-status="'add'"></EditChampion>
        <EditChampion v-if="displayModal == 2" :input-data="selectedData" :input-status="'edit'"></EditChampion>
      </div>
    </template>
  </FullModal>
</template>

<style scoped lang="scss">
.config-table {
  table {
    @apply w-full;
  }
}
</style>