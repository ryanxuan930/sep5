import { ref } from 'vue';
import { defineStore } from 'pinia';
import type { Ref } from 'vue';

export const useAdminStore = defineStore('admin', () => {
  const expired: Ref<string> = ref('');
  const token: Ref<string> = ref('');
  const userInfo: Ref<any> = ref('');
  const loginType: Ref<string> = ref('');
  function reset() {
    expired.value = '';
    token.value = '';
    userInfo.value = '';
    loginType.value = '';
  }
  return { expired, token, userInfo, loginType, reset };
});
