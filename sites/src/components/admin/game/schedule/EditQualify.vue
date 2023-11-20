<script setup lang="ts">
  import { ref, inject } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import LaneMode from '@/components/admin/game/schedule/qualify/LaneMode.vue';
  import RoadMode from '@/components/admin/game/schedule/qualify/RoadMode.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const gameData: any = inject('gameData');
  const route = useRoute();
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;
</script>

<template>
  <div>
    <LaneMode v-if="gameData.module == 'ln'"></LaneMode>
    <RoadMode v-if="gameData.module == 'rd'"></RoadMode>
  </div>
</template>

<style scoped lang="scss">
    
</style>