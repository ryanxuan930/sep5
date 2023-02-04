<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'

const vr = new VueRequest();
const data: any = ref(null);
vr.Get(`${useRoute().params.adminOrgId}/game/${useRoute().params.gameId}`, data);
const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
const gameTagList: any = ref([]);
vr.Get(`${useRoute().params.adminOrgId}/game-tag`, gameTagList);
</script>

<template>
  <div v-if="data != null">
    <div @click="$router.push(`/${$route.params.adminOrgId}/games`)" class="py-5 text-left text-gray-500 hover:text-gray-400 duration-200 cursor-pointer text-xl inline-block">{{ t('back') }}</div>
    <div class="text-3xl font-medium text-left text-blue-500">
      <span v-if="locale == 'zh-TW' || (data.multilingual == 0)">{{ data.game_name_ch }}</span>
      <span v-else>{{ data.game_name_en }}</span>
    </div>
    <hr class="border-blue-500 border-[1px] my-2">
    <div class="flex gap-3 flex-wrap py-5">
      <template v-for="(item, index) in gameTagList" :key="index">
        <div class="py-1 px-3 text-lg bg-blue-500 text-white"  v-if="data.tags.includes(String(item.game_tag_id))">
          <span v-if="locale == 'zh-TW'">{{ item.game_tag_ch }}</span>
          <span v-else>{{ item.game_tag_en }}</span>
        </div>
      </template>
    </div>
    <table class="info-table">
      <tr>
        <td class="head">{{ t('sport') }}</td>
        <td>
          <span v-if="locale == 'zh-TW'">{{ data.sport_name_ch }}</span>
          <span v-else>{{ data.sport_name_en }}</span>
        </td>
      </tr>
      <tr>
        <td class="head">{{ t('start') }}</td>
        <td>{{ data.event_start }}</td>
      </tr>
      <tr>
        <td class="head">{{ t('registration') }}</td>
        <td>
          <router-link v-if="data.use_reg" class="hyperlink blue" to="">{{ t('click-me') }}</router-link>
          <a v-else :href="data.reg_url" target="_blank" class="hyperlink blue">{{ data.reg_url }}</a>
        </td>
      </tr>
      <tr>
        <td class="head">{{ t('website') }}</td>
        <td>
          <router-link v-if="data.use_site" class="hyperlink blue" to="">{{ t('click-me') }}</router-link>
          <a v-else :href="data.site_url" target="_blank" class="hyperlink blue">{{ data.site_url }}</a>
        </td>
      </tr>
    </table>
    <div v-if="data.game_info != null" class="my-5 p-5 border-[1px] rounded text-base font-normal">
      <div v-html="data.game_info"></div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.category {
  @apply text-white px-2 py-1 inline-block text-center;
}
.info-table {
  @apply w-full text-lg font-medium;
  td {
    @apply py-2;
  }
  .head {
    @apply w-48 font-semibold;
  }
}
</style>

<i18n lang="yaml">
  en-US:
    back: 'Back'
    sport: 'Sports'
    start: 'Start Date'
    registration: 'Registration System'
    website: 'Official Website'
    click-me: 'Click me'
  zh-TW:
    back: '回上一頁'
    sport: '運動項目'
    start: '開始日期'
    registration: '報名系統'
    website: '賽事官網'
    click-me: '點我前往'
</i18n>