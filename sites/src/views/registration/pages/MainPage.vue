<script setup lang="ts">
import { ref, inject } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import { getUrlParams, paginationText } from '@/components/library/functions';

const store = useUserStore();
const currentTime = ref(Date.now());
const route = useRoute();
const vr = new VueRequest(store.token);
let adminOrgId: string|string[] = '';

if (route.params.adminOrgId == undefined) {

} else {
  adminOrgId = route.params.adminOrgId;
}
const systemConfig: any = inject('systemConfig');

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
      <div class="section-box text-center flex flex-col gap-2">
        <div>{{ $d(currentTime, 'long') }}</div>
        <div class="text-4xl font-medium">{{ t('greeting', { name: locale == 'zh-TW' ? store.userInfo.first_name_ch : store.userInfo.first_name_en }) }}
        </div>
      </div>
      <div class="section-box">
        <div class=title>{{ t('notice') }}</div>
        <div class="rounded border-2 p-3" v-html="systemConfig.reg_content"></div>
      </div>
      <div class="section-box flex-grow flex flex-col gap-3">
        <div class=title>{{ t('recent-game') }}</div>
        <table class="game-table" v-if="dataList != null">
          <tr>
            <th>{{ t('game') }}</th>
            <th>{{ t('date') }}</th>
          </tr>
          <template v-if="dataList.length > 0">
            <template v-for="(item, index) in dataList" :key="index">
              <tr>
                <td class="hyperlink blue">
                  <router-link :to="`/${route.params.adminOrgId}/registration/game/${item.sport_code}/${item.game_id}`">
                    <template v-if="locale == 'zh-TW'">[{{ item.sport_name_ch }}] {{ item.game_name_ch }}</template>
                    <template v-else>[{{ item.sport_name_en }}] {{ item.game_name_en }}</template>
                  </router-link>
                </td>
                <td>{{ item.event_start }}</td>
              </tr>
            </template>
          </template>
          <tr v-else>
            <td colspan="5" class="text-center">目前無賽事</td>
          </tr>
        </table>
        <div class="page-btn">
          <template v-for="(item, index) in linkUrl" :key="index">
            <button :class="{'general-button': true, 'blue': !item.active, 'active': item.active }" :disabled="item.url===null" @click="getDataList(item.url)">{{ paginationText(item.label) }}</button>
          </template>
        </div>
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
    greeting: 'Hi {name}, Welcome Back!'
    notice: 'Notice'
    recent-game: 'Recent Games'
    game: 'Game'
    date: 'Date'
  zh-TW:
    greeting: '嗨 {name}，歡迎回來！'
    notice: '系統公告'
    recent-game: '近期賽事'
    game: '賽事名稱'
    date: '日期'
</i18n>