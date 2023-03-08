<script setup lang="ts">
import { ref, inject } from 'vue';
import LaneArrangement from '@/components/main/game/lane/LaneArrangement.vue';
import { useI18n } from 'vue-i18n'

const gameData: any = inject('gameData');
const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div>
    <div @click="$router.push(`/${$route.params.adminOrgId}/game/${$route.params.gameId}/schedule`)" class="py-2 text-left text-gray-500 hover:text-gray-400 duration-200 cursor-pointer text-xl inline-block">{{ t('back') }}</div>
    <hr class="border-black my-3">
    <LaneArrangement v-if="gameData.module == 'ln'"></LaneArrangement>
  </div>
</template>

<style scoped lang="scss">
    
</style>

<i18n lang="yaml">
  en-US:
    back: 'Back'
  zh-TW:
    back: '回上一頁'
</i18n>