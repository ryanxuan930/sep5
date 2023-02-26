<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import type { Ref } from 'vue';
import type { IPostData } from '@/components/library/interfaces';
import BulletinCategory from '@/assets/BulletinCategory.json';
import { getUrlParams, paginationText } from '@/components/library/functions';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'

const props = defineProps(['displayMode']);
const vr = new VueRequest();
const adminOrgId = useRoute().params.adminOrgId;

const linkUrl:any= ref(null);
const bulletinList:Ref<IPostData[]> = ref([]);
const currentUrl = ref('');
function getBulletinList(url = `${adminOrgId}/bulletin-game/${useRoute().params.gameId}`) {
  currentUrl.value = url;
  const page = getUrlParams(url, 'page');
  if (page !== null) {
    url = `${adminOrgId}/bulletin-game?page=${page}`;
  }
  vr.Get(url).then( (res: any) => {
    bulletinList.value = res.data;
    linkUrl.value = res.links;
  });
}
getBulletinList();

const newsFilter = ref(9);

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div>
    <div class="text-2xl font-medium text-left mb-2">{{ t('news-title') }}</div>
    <hr class="border-gray-400 border-[1px]">
    <div class="my-5 filter-button">
      <button class=" bg-black" @click="newsFilter=9">{{ t('all-post') }}</button>
      <template v-for="(item, index) in BulletinCategory" :key="index">
        <button :class="[item.css]" @click="newsFilter=index">{{ item[locale] }}</button>
      </template>
    </div>
    <table class="bulletin-table">
      <template v-for="(item, index) in bulletinList" :key="index">
        <tbody @click="$router.push(`/${$route.params.adminOrgId}/game/${$route.params.gameId}/news/${item.bulletin_id}`)" v-if="((props.displayMode == 'home' && index < 5)|| props.displayMode == 'news') && (newsFilter==9 || item.category==newsFilter) && item.release!=0">
          <tr>
            <td class="w-[3%]"><span v-if="item.pinned==1" class="material-icons text-3xl text-blue-500">label_important</span></td>
            <td class="text-left w-[40%] md:w-[20%] lg:w-[15%] xl:w-[12%]">
              <span :class="[BulletinCategory[item.category].css, 'category']">{{ BulletinCategory[item.category][locale] }}</span>
            </td>
            <td class="text-left hidden md:table-cell truncate max-w-0">
              <span v-if="locale == 'zh-TW' || (item.multilingual == 0)">{{ item.title_ch }}</span>
              <span v-else>{{ item.title_en }}</span>
            </td>
            <td class="w-1/2 text-right md:text-center md:w-[20%]">[{{item.post_date}}]</td>
          </tr>
          <tr class="md:hidden">
            <td colspan="3" class="text-left border-b-2 w-full truncate max-w-0">
              <span v-if="locale == 'zh-TW'">{{ item.title_ch }}</span>
              <span v-else>{{ item.title_en }}</span>
            </td>
          </tr>
        </tbody>
      </template>
    </table>
    <div v-if="props.displayMode == 'home'" class="bg-gray-100 p-3 tezt-center mb-3 text-center">
      <button class="bg-gray-500 text-white py-2 px-5" @click="$router.push(`/${$route.params.adminOrgId}/game/${$route.params.gameId}/news`)">{{ t('more') }}...</button>
    </div>
    <div v-if="props.displayMode == 'news'" class="page-btn">
      <template v-for="(item, index) in linkUrl" :key="index">
        <button :class="{'general-button': true, 'blue': !item.active, 'active': item.active }" :disabled="item.url===null" @click="getBulletinList(item.url)">{{ paginationText(item.label) }}</button>
      </template>
    </div>
  </div>
</template>

<style scoped lang="scss">
.filter-button {
  @apply sm:flex;
  button {
    @apply text-xl font-medium p-2 w-full hover:bg-opacity-80 text-white duration-100 ;
  }
}
.bulletin-table {
  @apply w-full bg-gray-100;
  tbody {
    @apply hover:bg-gray-200 duration-100 cursor-pointer;
  }
  td, th {
    @apply p-3 md:border-b-2 ;
  }
  .category {
    @apply text-white px-2 py-1 block text-center;
  }
  button {
    @apply inline-block text-white px-2 py-1 rounded m-1 bg-orange-400 hover:bg-orange-300 duration-200;
  }
}
.page-btn {
  @apply flex my-5 gap-3;
}
.general-button.active {
  @apply bg-blue-400;
}
.general-button{
  @apply rounded-none;
}
</style>

<i18n lang="yaml">
en-US:
  news-title: 'Latest News'
  all-post: 'All Posts'
  more: 'More'
zh-TW:
  news-title: '最新消息'
  all-post: '全部公告'
  more: '更多'
</i18n>