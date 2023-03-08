<script setup lang="ts">
import { ref, inject } from 'vue';
import LaneArrangement from '@/components/main/game/lane/LaneArrangement.vue';

const gameData: any = inject('gameData');
</script>

<template>
  <div>
    <LaneArrangement v-if="gameData.module == 'ln'"></LaneArrangement>
  </div>
</template>

<style scoped lang="scss">
    
</style>