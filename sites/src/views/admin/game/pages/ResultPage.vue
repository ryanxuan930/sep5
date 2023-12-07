<script setup lang="ts">
  import { ref, provide } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useGameStore } from '@/stores/game';
  import { useRoute } from 'vue-router';
  import FullModal from '@/components/FullModal.vue';
  import ScheduleList from '@/components/admin/game/common/ScheduleList.vue';
  import CalculateChampion from '@/components/admin/game/result/lane/CalculateChampion.vue';

  const gameStore = useGameStore();
  const displayModal = ref(0);
  const gameData: any = ref(gameStore.data);
  const route = useRoute();
  provide('gameData', gameData);
</script>

<template>
  <div v-if="gameData != null" class="flex flex-col h-full gap-5">
    <div class="section-box flex-shrink-0 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-3 gap-y-4">
      <div class="col-span-4 text-2xl">功能選單</div>
      <hr class="col-span-4">
      <button class="round-full-button blue" @click="displayModal = 1">場次總覽</button>
      <button v-if="gameData.module == 'ln'" class="round-full-button blue" @click="displayModal = 2">總錦標計算</button>
    </div>
    <div class="section-box flex-grow h-full flex flex-col gap-4 overflow-hidden">
      <div class="col-span-4 text-2xl">賽程列表</div>
      <hr class="col-span-4">
      <div class="flex-grow h-full overflow-hidden">
        <ScheduleList v-if="gameData.module == 'ln' || gameData.module == 'rd'" :displayMode="'input'"></ScheduleList>
      </div>
    </div>
  </div>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">場次總覽</div>
        <div v-if="displayModal == 2">總錦標計算</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <ScheduleList v-if="displayModal == 1" :displayMode="'result'"></ScheduleList>
        <CalculateChampion v-if="displayModal == 2"></CalculateChampion>
      </div>
    </template>
  </FullModal>
</template>

<style scoped lang="scss">
    
</style>