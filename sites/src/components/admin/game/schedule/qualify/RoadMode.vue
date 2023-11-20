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
    qualified: number,
    options: any,
  }
  const paramsList: any = ref([]);
  const paramsData: Ref<IParams[]> = ref([]);
  async function getData() {
    isLoading.value = true;
    await vr.Get(`game/${sportCode}/${gameId}/main/params`, paramsData, true, true);
    await vr.Get(`game/${sportCode}/${gameId}/main/params/full`, paramsList, true, true);
    isLoading.value = false;
  }
  getData();
  async function submitAll() {
    const res: any = await vr.Post(`game/${sportCode}/${gameId}/main/params`, paramsData.value, null, true, true);
    if (res.status == 'A01') {
      alert('已儲存');
      getData();
    }
  }
</script>

<template>
  <div v-if="isLoading == false" class="flex flex-col relative">
    <div class="flex-grow">
      <table class="config-table">
        <thead>
          <tr>
            <th rowspan="2">組別</th>
            <th rowspan="2">項目</th>
            <th colspan="3" class=text-center>取數</th>
          </tr>
        </thead>
        <template v-for="(item, index) in paramsData" :key="index">
          <tr>
            <td class="pin">{{ paramsList[index].division_ch }}</td>
            <td class="pin">{{ paramsList[index].event_ch }}</td>
            <td>
              <input type="number" class="round-input" v-model="item.qualified">
            </td>
          </tr>
        </template>
      </table>
    </div>
    <div class="py-3 relative left-0">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
.config-table {
  @apply w-full text-left overflow-scroll;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium overflow-hidden;
    div {
      @apply whitespace-nowrap overflow-auto w-full py-2;
    }
  }
  thead {
    @apply bg-blue-100 sticky top-0;
  }
}
input[type=checkbox] {
  @apply w-5 h-5;
}
</style>