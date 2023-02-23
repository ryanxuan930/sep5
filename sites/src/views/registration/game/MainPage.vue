<script setup lang="ts">
import { ref, inject } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import { getUrlParams } from '@/components/library/functions';

const store = useUserStore();
const currentTime = ref(Date.now());
const route = useRoute();
const vr = new VueRequest(store.token);
const adminOrgId = route.params.adminOrgId;

const systemConfig: any = inject('systemConfig');
const gameData: any = inject('gameData');

const linkUrl:any= ref(null);
const dataList: any = ref([]);
const currentUrl = ref(`${adminOrgId}/game-by-tag/`);
const currentTag = ref(0);
function getDataList(url = currentUrl.value, tag = 0) {
  currentTag.value = tag;
  const page = getUrlParams(url, 'page');
  if (page !== null) {
    url = `${adminOrgId}/game-by-tag/${tag}?page=${page}`;
  } else {
    url = `${adminOrgId}/game-by-tag/${tag}`;
  }
  currentUrl.value = url;
  vr.Get(url).then( (res: any) => {
    dataList.value = res.data;
    linkUrl.value = res.links;
  });
}
getDataList();

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
setInterval(() => {
  currentTime.value = Date.now();
}, 1000);
</script>

<template>
  <div class="h-full overflow-auto">
    <div class="flex flex-col gap-5 overflow-auto">
      <div class="section-box">
        <div class="title mb-2">{{ t('game-info') }}</div>
        <div class="rounded border-2 p-3" v-html="gameData.game_info"></div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.title {
  @apply text-2xl font-medium;
}
.title + hr {
  @apply my-3 border-[1px];
}
.game-table {
  @apply w-full text-left overflow-auto;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium;
  }
  th {
    @apply bg-blue-100;
  }
}
.general-button.active {
  @apply bg-blue-400;
}
.page-btn {
  @apply flex my-5 gap-3;
}
</style>

<i18n lang="yaml">
  en-US:
    game-info: 'Game Information'
  zh-TW:
    game-info: '賽事資訊'
</i18n>