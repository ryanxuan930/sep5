<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useGameStore } from '@/stores/game';
import { useRoute } from 'vue-router';
import { lanePhaseToString } from '@/components/library/functions';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const gameStore = useGameStore();
const gameData = gameStore.data;
const divisionId = Number(route.params.divisionId);
const eventCode = route.params.eventCode;
const multiple = Number(route.params.multiple);
const round = Number(route.params.round);

const dataList: any = ref([]);
const isLoading = ref(false);
(async () => {
  isLoading.value = true;
  if (multiple == 0){
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/event/${divisionId}/${eventCode}`, dataList);
  } else {
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/event/${divisionId}/${eventCode}`, dataList);
  }
  if (gameData.value.module == 'ln') {
    dataList.value.sort((a: any, b: any) => a[`r${[round]}_heat`] - b[`r${[round]}_heat`] || a[`r${[round]}_lane`] - b[`r${[round]}_lane`]);
    for (let i = 0; i < dataList.value.length; i++) {
      dataList.value[i][`r${[round]}_options`] = JSON.parse(dataList.value[i][`r${[round]}_options`]);
    }
  }
  isLoading.value = false;
})();
  
</script>

<template>
  <div></div>
</template>

<style scoped lang="scss">
    
</style>