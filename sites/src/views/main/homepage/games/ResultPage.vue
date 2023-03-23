<script setup lang="ts">
import { ref, inject } from 'vue';
import LaneResult from '@/components/main/game/lane/LaneResult.vue';

const gameData: any = inject('gameData');
</script>

<template>
  <div>
    <LaneResult v-if="gameData.module == 'ln'"></LaneResult>
  </div>
  <div class="p-2 text-sm">即時成績僅供參考，正式成績以大會公告為準。Real-time results for reference only.</div>
</template>

<style scoped lang="scss">
    
</style>