<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import { getUrlParams, paginationText } from '@/components/library/functions';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'

const props = defineProps(['displayMode']);
const vr = new VueRequest();
const adminOrgId = useRoute().params.adminOrgId;

const gameTagList: any = ref([]);
vr.Get(`${adminOrgId}/game-tag`, gameTagList);
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

const newsFilter = ref(9);

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div>
    <div class="text-2xl font-medium text-left mb-2">{{ t('game-title') }}</div>
    <hr class="border-gray-400 border-[1px]">
    <div class="my-5 filter-button">
      <button class=" bg-blue-400" @click="getDataList(currentUrl, 0)">{{ t('all-game') }}</button>
      <template v-for="(item, index) in gameTagList" :key="index">
        <button class="bg-blue-400" @click="getDataList(currentUrl, item.game_tag_id)">
          <span v-if="locale == 'zh-TW'">{{ item.game_tag_ch }}</span>
          <span v-else>{{ item.game_tag_en }}</span>
        </button>
      </template>
    </div>
    <table class="bulletin-table">
      <template v-for="(item, index) in dataList" :key="index">
        <tbody @click="$router.push(`/${$route.params.adminOrgId}/games/${item.game_id}`)" v-if="((props.displayMode == 'home' && index < 5)|| props.displayMode == 'game')">
          <tr>
            <td class="text-left w-[40%] md:w-[20%] lg:w-[15%] xl:w-[15%]">
              <span class="bg-blue-500 text-white rounded py-1 px-2 font-medium" v-if="locale == 'zh-TW' || (item.multilingual == 0)">{{ item.sport_name_ch }}</span>
              <span class="bg-blue-500 text-white rounded py-1 px-2 font-medium" v-else>{{ item.sport_name_en }}</span>
            </td>
            <td class="text-left hidden md:table-cell truncate max-w-0">
              <span v-if="locale == 'zh-TW' || (item.multilingual == 0)">{{ item.game_name_ch }}</span>
              <span v-else>{{ item.game_name_en }}</span>
            </td>
            <td class="w-1/2 text-right md:text-center md:w-[20%]">[{{item.event_start}}]</td>
          </tr>
          <tr class="md:hidden">
            <td colspan="3" class="text-left border-b-2 w-full truncate max-w-0">
              <span v-if="locale == 'zh-TW'">{{ item.game_name_ch }}</span>
              <span v-else>{{ item.game_name_en }}</span>
            </td>
          </tr>
        </tbody>
      </template>
    </table>
    <div v-if="props.displayMode == 'home'" class="bg-gray-100 p-3 tezt-center mb-3 text-center">
      <button class="bg-gray-500 text-white py-2 px-5" @click="$router.push(`/${$route.params.adminOrgId}/games`)">{{ t('more') }}...</button>
    </div>
    <div v-if="props.displayMode == 'game'" class="page-btn">
      <template v-for="(item, index) in linkUrl" :key="index">
        <button :class="{'general-button': true, 'blue': !item.active, 'active': item.active }" :disabled="item.url===null" @click="getDataList(item.url)">{{ paginationText(item.label) }}</button>
      </template>
    </div>
  </div>
</template>

<style scoped lang="scss">
.filter-button {
  @apply flex overflow-auto flex-nowrap gap-5;
  button {
    @apply text-xl font-medium py-2 px-5 hover:bg-opacity-80 text-white duration-100 flex-shrink-0;
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
.general-button.active {
  @apply bg-blue-400;
}
.page-btn {
  @apply flex my-5 gap-3;
}
.general-button{
  @apply rounded-none;
}
</style>

<i18n lang="yaml">
en-US:
  game-title: 'Game List'
  all-game: 'All Games'
  more: 'More'
zh-TW:
  game-title: '賽事列表'
  all-game: '全部賽事'
  more: '更多'
</i18n>