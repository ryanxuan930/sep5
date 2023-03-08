<script setup lang="ts">
import { ref } from 'vue';
import NewsSection from '@/components/main/game/NewsSection.vue';
import PostContent from '@/components/main/game/PostContent.vue';

</script>

<template>
  <div>
    <NewsSection v-if="$route.params.postId == ''" :display-mode="'news'"></NewsSection>
    <PostContent v-else></PostContent>
  </div>
</template>

<style scoped lang="scss">
    
</style>