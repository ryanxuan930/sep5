<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';

const store = useUserStore();

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div>
    <div class="section-box">
      <div class="text-4xl">{{ t('greeting') }}
        <span v-if="locale == 'zh-TW'">{{ store.userInfo.first_name_ch }}</span>
        <span v-if="locale == 'en-US'">{{ store.userInfo.first_name_en }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>

<i18n lang="yaml">
  en-US:
    greeting: 'Hi!'
  zh-TW:
    greeting: '嗨!'
</i18n>