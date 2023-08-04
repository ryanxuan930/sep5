<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { csvToArrayTable, lanePhaseToString, stringToMilliseconds } from '@/components/library/functions';
const store = useUserStore();
const vr = new VueRequest(store.token);
const gameData: any = inject('gameData');
</script>

<template>
  <div></div>
</template>

<style scoped lang="scss">
    
</style>