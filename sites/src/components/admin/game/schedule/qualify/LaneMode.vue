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
            <th rowspan="2" class="w-1/12">組別</th>
            <th rowspan="2" class="w-1/12">項目</th>
            <th colspan="3" class=text-center>第一輪</th>
            <th colspan="3" class=text-center>預賽</th>
            <th colspan="3" class=text-center>準決賽</th>
            <th colspan="3" class=text-center>決賽</th>
          </tr>
          <tr>
            <template v-for="item in 4">
              <th>Q</th>
              <th>q</th>
              <th>分組</th>
            </template>
          </tr>
        </thead>
        <template v-for="(item, index) in paramsData" :key="index">
          <tr>
            <td class="pin">{{ paramsList[index].division_ch }}</td>
            <td class="pin">{{ paramsList[index].event_ch }}</td>
            <td>
              <input type="number" class="round-input" v-model="item.r1_aq" :disabled="item.r1 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r1_sq" :disabled="item.r1 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r1_split" :disabled="item.r1 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r2_aq" :disabled="item.r2 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r2_sq" :disabled="item.r2 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r2_split" :disabled="item.r2 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r3_aq" :disabled="item.r3 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r3_sq" :disabled="item.r3 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r3_split" :disabled="item.r3 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r4_aq" :disabled="item.r4 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r4_sq" :disabled="item.r4 == 0">
            </td>
            <td>
              <input type="number" class="round-input" v-model="item.r4_split" :disabled="item.r4 == 0">
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
  @apply w-[1536px] 2xl:w-full text-left overflow-scroll;
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