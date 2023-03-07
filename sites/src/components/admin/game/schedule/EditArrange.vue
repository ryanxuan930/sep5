<script setup lang="ts">
  import { ref, inject } from 'vue';
  import type { Ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import { shuffle } from '@/components/library/functions';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const gameData: any = inject('gameData');
  const route = useRoute();
  const isLoading = ref(true);
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;
  const paramsList: any = ref([]);

  const dataList: any = ref([]);
  const selectedIndex = ref(0);
  function open(index: number) {
    selectedIndex.value = index;
    if(paramsList.value[index].multiple == 1){
      vr.Get(`game/${sportCode}/${gameId}/common/group/by/event/${paramsList.value[index].division_id}/${paramsList.value[index].event_code}`, dataList, true, true);
    } else {
      vr.Get(`game/${sportCode}/${gameId}/common/individual/by/event/${paramsList.value[index].division_id}/${paramsList.value[index].event_code}`, dataList, true, true);
    }
  }

  async function getData() {
    isLoading.value = true;
    await vr.Get(`game/${sportCode}/${gameId}/main/params/full`, paramsList, true, true);
    open(0);
    isLoading.value = false;
  }
  getData();
</script>

<template>
  <div v-if="isLoading == false" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4  gap-5 h-full overflow-hidden">
    <div class="left-box">
      <template v-for="(item, index) in paramsList" :key="index">
        <div :class="{'items': true, 'active': index == selectedIndex}" @click="open(index)">
          <div>{{ item.division_ch }}</div>
          <div>{{ item.event_ch }}</div>
        </div>
      </template>
    </div>
    <div class="right-box">
      <div class="items text-xl font-medium">[{{ paramsList[selectedIndex].division_ch }}] {{ paramsList[selectedIndex].event_ch }}</div>
      <div></div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.left-box {
  @apply flex flex-col gap-2 bg-gray-100 p-3 overflow-auto shadow-inner;
  .items {
    @apply p-3 bg-white shadow cursor-pointer;
  }
  .items.active {
    @apply bg-blue-500 text-white;
  }
}
.right-box {
  @apply md:col-span-2 lg:col-span-3 p-3 bg-gray-100 shadow-inner overflow-hidden flex flex-col gap-2;
  .items {
    @apply p-3 bg-white shadow;
  }
}
input[type=checkbox] {
  @apply w-5 h-5;
}
</style>