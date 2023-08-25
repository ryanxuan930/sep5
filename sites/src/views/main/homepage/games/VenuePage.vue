<script setup lang="ts">
import { inject } from 'vue';
import { useI18n } from 'vue-i18n'

const gameData: any = inject('gameData');
const { t, locale } = useI18n();
</script>

<template>
  <div>
    <div class="text-2xl font-medium text-left">{{ t('venue-title') }}</div>
    <hr class="border-gray-400 border-[1px] my-2">
    <div v-html="gameData.options.venueContent"></div>
  </div>
</template>

<style scoped lang="scss">
</style>

<i18n lang="yaml">
  en-US:
    venue-title: 'Venue'
  zh-TW:
    venue-title: '場館資訊'
  </i18n>