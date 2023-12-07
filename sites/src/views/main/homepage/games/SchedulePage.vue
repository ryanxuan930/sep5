<script setup lang="ts">
import { ref, inject } from 'vue';
import LaneSchedule from '@/components/main/game/lane/LaneSchedule.vue';
import RoadSchedule from '@/components/main/game/road/RoadSchedule.vue';

const gameData: any = inject('gameData');
</script>

<template>
  <div>
    <LaneSchedule v-if="gameData.module == 'ln'"></LaneSchedule>
    <RoadSchedule v-if="gameData.module == 'rd'"></RoadSchedule>
  </div>
</template>

<style scoped lang="scss">
    
</style>