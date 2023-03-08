<script setup lang="ts">
import { ref, inject } from 'vue';
import LaneSchedule from '@/components/main/game/lane/LaneSchedule.vue';

const gameData: any = inject('gameData');
</script>

<template>
  <div>
    <LaneSchedule v-if="gameData.module == 'ln'"></LaneSchedule>
  </div>
</template>

<style scoped lang="scss">
    
</style>