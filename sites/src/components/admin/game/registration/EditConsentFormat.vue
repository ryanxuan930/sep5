<script setup lang="ts">
  import { ref, reactive, watch } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const route = useRoute();

</script>

<template>
  <div></div>
</template>

<style scoped lang="scss">
    
</style>