<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { csvToArrayTable, lanePhaseToString, stringToMilliseconds } from '@/components/library/functions';
import DistanceFormat from '@/components/admin/game/result/lane/format/DistanceFormat.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const gameData: any = inject('gameData');
const props: any = defineProps(['inputData', 'athleteData', 'paramList']);

const emit: any = defineEmits<{(e: 'returnData', value: any): void, (e: 'closeModal'): void}>();
</script>

<template>
  <div class="flex flex-col overflow-hidden h-full">
    <div class="py-3 text-xl flex gap-2 items-center">
      <div>{{ props.inputData.timestamp }} {{ props.inputData.division_ch }}-{{ props.inputData.event_ch }}</div>
      <div v-if="gameData.module == 'ln'"> [{{ lanePhaseToString(props.inputData.round, 'zh-TW') }}]</div>
      <div class="flex-grow"></div>
    </div>
    <div class="flex-grow overflow-auto">
      <DistanceFormat :inputData="props.inputData" :athleteData="props.athleteData" :param-list="paramList" @return-data="(res: any) => { emit('returnData', {payload: res, type: 'distance'}); }" @close-modal="emit('closeModal')" />
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>