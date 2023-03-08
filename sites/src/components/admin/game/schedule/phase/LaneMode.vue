<script setup lang="ts">
  import { ref, inject } from 'vue';
  import type { Ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import FullModal from '@/components/FullModal.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const gameData: any = inject('gameData');
  const route = useRoute();
  const isLoading = ref(true);
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;
  interface IParams {
    division_id: number,
    event_code: string,
    param_id: number,
    r1: number|boolean,
    r2: number|boolean,
    r3: number|boolean,
    r4: number|boolean,
    r1_aq: number,
    r2_aq: number,
    r3_aq: number,
    r4_aq: number,
    r1_sq: number,
    r2_sq: number,
    r3_sq: number,
    r4_sq: number,
    r1_split: number,
    r2_split: number,
    r3_split: number,
    r4_split: number,
  }
  const paramsList: any = ref([]);
  const paramsData: Ref<IParams[]> = ref([]);
  async function getData() {
    isLoading.value = true;
    await vr.Get(`game/${sportCode}/${gameId}/main/params`, paramsData, true, true);
    await vr.Get(`game/${sportCode}/${gameId}/main/params/full`, paramsList, true, true);
    for (let i = 0; i < paramsData.value.length; i++) {
      paramsData.value[i].r1 = paramsData.value[i].r1 == 1 ? true : false;
      paramsData.value[i].r2 = paramsData.value[i].r2 == 1 ? true : false;
      paramsData.value[i].r3 = paramsData.value[i].r3 == 1 ? true : false;
      paramsData.value[i].r4 = paramsData.value[i].r4 == 1 ? true : false;
    }
    isLoading.value = false;
  }
  getData();
  async function submitAll() {
    const res: any = await vr.Post(`game/${sportCode}/${gameId}/main/params`, paramsData.value, null, true, true);
    if (res.status == 'A01') {
      alert('已儲存');
    }
  }
</script>

<template>
  <div v-if="isLoading == false">
    <table>
      <tr>
        <th>組別</th>
        <th>項目</th>
        <th class=text-center>第一輪</th>
        <th class=text-center>第二輪</th>
        <th class=text-center>準決</th>
        <th class=text-center>決賽</th>
      </tr>
      <template v-for="(item, index) in paramsData" :key="index">
        <tr>
          <td>{{ paramsList[index].division_ch }}</td>
          <td>{{ paramsList[index].event_ch }}</td>
          <td class="text-center">
            <input type="checkbox" v-model="item.r1">
          </td>
          <td class="text-center">
            <input type="checkbox" v-model="item.r2">
          </td>
          <td class="text-center">
            <input type="checkbox" v-model="item.r3">
          </td>
          <td class="text-center">
            <input type="checkbox" v-model="item.r4">
          </td>
        </tr>
      </template>
    </table>
    <div class="py-3">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
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
input[type=checkbox] {
  @apply w-5 h-5;
}
</style>