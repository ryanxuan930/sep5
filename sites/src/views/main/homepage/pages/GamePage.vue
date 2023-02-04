<script setup lang="ts">
import GameSection from '@/components/main/homepage/GameSection.vue';
import GameContent from '@/components/main/homepage/GameContent.vue';

</script>

<template>
  <div>
    <GameSection v-if="$route.params.gameId == ''" :display-mode="'game'"></GameSection>
    <GameContent v-else></GameContent>
  </div>
</template>

<style scoped lang="scss">
    
</style>