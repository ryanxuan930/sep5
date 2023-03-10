<script setup lang="ts">
import { ref, inject } from 'vue';
import LaneResult from '@/components/main/game/lane/LaneResult.vue';

const gameData: any = inject('gameData');
</script>

<template>
  <div>
    <LaneResult v-if="gameData.module == 'ln'"></LaneResult>
  </div>
</template>

<style scoped lang="scss">
    
</style>