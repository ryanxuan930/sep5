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
    hasChampion: false,
    content: [],
  };
  const contentPrototype = {
    type: 'ranking', // ranking, grade
    divisionName: '',
    divisionList: [],
    formula: {},
    payload: {},
  }
  const dataList:any = ref([]);
  async function getData() {
    isLoading.value = true;
    await vr.Get(`game/${sportCode}/${gameId}/main/division`, divisionList);
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
    const res = await vr.Patch(`game/${sportCode}/${gameId}/common/temp/gameChampion`, { temp_data: JSON.stringify(championData.value)}, null, true, true);
    if (res.status != 'A01') {
      alert('儲存失敗');
      return;
    }
    getData();
    alert('已儲存');
  }
  const openType = ref('add');
  function openModal(type: string, input: any = null) {
    openType.value = type;
    if (input == null) {
      selectedData.value = contentPrototype;
    } else {
      selectedData.value = input;
    }
    displayModal.value = 1;
  }
  function remove(index: number) {
    if(confirm('確定刪除？')){
      championData.content.splice(index, 1);
    }
  }
</script>

<template>
  <div class="flex flex-col gap-3">
    <div class="flex items-center gap-3">
      <div>有無總錦標：</div>
      <select class="py-1 px-2 rounded border-2" v-model="championData.hasChampion">
        <option :value="true">有</option>
        <option :value="false">無</option>
      </select>
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
              <div class="flex gap-2">
                <a class="hyperlink blue" @click="openModal('edit', item)">查看</a>
                <a class="hyperlink red" @click="remove(index)">刪除</a>
              </div>
            </td>
          </tr>
        </template>
      </table>
    </div>
  </div>
  <div class="my-3">
    <button class="round-full-button blue" @click="submitAll">儲存</button>
  </div>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">編輯總錦標設定</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <EditChampion v-if="displayModal == 1" :input-data="selectedData" :input-status="openType" @close-modal="displayModal = 0" @return-data="(input: any) => { if (openType == 'add') {championData.content.push(input); } else { selectedData = input; }}"></EditChampion>
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