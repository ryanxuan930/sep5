<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';

</script>

<template>
  <div class="flex flex-col">
    <div class="section-box flex flex-col gap-2">
      <div class="text-2xl font-medium">報名表列印</div>
      <hr>
      <div class="grid grid-cols-1 md:grid-cols-4">
        <router-link :to="`/${$route.params.adminOrgId}/registration/game/${$route.params.sportCode}/${$route.params.gameId}/print/reg-data`" target="_blank"><button class="round-full-button blue">列印</button></router-link>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>