<script setup lang="ts">
import NewsSection from '@/components/main/homepage/NewsSection.vue';
import PostContent from '@/components/main/homepage/PostContent.vue';

</script>

<template>
  <div>
    <NewsSection v-if="$route.params.postId == ''" :display-mode="'news'"></NewsSection>
    <PostContent v-else></PostContent>
  </div>
</template>

<style scoped lang="scss">
    
</style>