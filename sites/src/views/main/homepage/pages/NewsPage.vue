<script setup lang="ts">
import NewsSection from '@/components/main/homepage/NewsSection.vue';

</script>

<template>
  <div>
    <NewsSection v-if="$route.params.postId == undefined" :display-mode="'news'"></NewsSection>
  </div>
</template>

<style scoped lang="scss">
    
</style>