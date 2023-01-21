import { ref } from 'vue';
import { defineStore } from 'pinia';
import type { Ref } from 'vue';

export const useCounterStore = defineStore('user', () => {
  const expired: Ref<Date|null> = ref(null);
  const token: Ref<string|null> = ref(null);
  const userInfo: Ref<any> = ref(null);
  return { expired, token, userInfo };
});
