import { ref } from 'vue';
import { defineStore } from 'pinia';
import VueRequest from '@/vue-request';

export const useGameStore = defineStore('game', () => {
  const data: any = ref(null);
  async function fetch(adminOrgId: number, gameId: number) {
    const vr = new VueRequest();
    await vr.Get(`${adminOrgId}/game/${gameId}`, data, true, true);
    localStorage.gameData = JSON.stringify(data.value);
  }
  return { data, fetch };
});
