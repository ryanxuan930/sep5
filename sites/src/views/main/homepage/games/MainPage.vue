<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import NewsSection from '@/components/main/game/NewsSection.vue';
</script>

<template>
  <div>
    <NewsSection class="mt-5" :display-mode="'home'"></NewsSection>
  </div>
</template>

<style scoped lang="scss">
    
</style>