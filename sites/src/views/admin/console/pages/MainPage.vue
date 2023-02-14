<script setup lang="ts">
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';

  const store = useUserStore();
  const router = useRouter();
  const vr = new VueRequest(store.token);
  const currentTime = ref(Date.now());
  setInterval(() => {
    currentTime.value = Date.now();
  }, 1000);
</script>

<template>
  <div>
    <div class="section-box text-center flex flex-col gap-2">
      <div>{{ $d(currentTime, 'long') }}</div>
      <div class="text-3xl font-medium">嗨！{{ store.userInfo.name }}，歡迎回來～</div>
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>