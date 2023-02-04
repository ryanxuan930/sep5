<script setup lang="ts">
import { ref } from 'vue';
import VueRequest from '@/vue-request';
import BulletinCategory from '@/assets/BulletinCategory.json';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'

const vr = new VueRequest();
const data: any = ref(null);
vr.Get(`${useRoute().params.adminOrgId}/bulletin/${useRoute().params.postId}`, data);
const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div v-if="data != null">
    <div @click="$router.push(`/${$route.params.adminOrgId}/news`)" class="py-5 text-left text-gray-500 hover:text-gray-400 duration-200 cursor-pointer text-xl inline-block">{{ t('back') }}</div>
    <hr class="border-black">
    <div class="py-4">
        <div class="flex">
            <div class="text-lg basis-1/2 text-left">
              <span :class="[BulletinCategory[data.category].css, 'category']">{{ BulletinCategory[data.category][locale] }}</span>
            </div>
            <div class="text-lg basis-1/2 text-right">[{{data.post_date}}]</div>
        </div>
        <div class="text-2xl mt-4">
          <span v-if="locale == 'zh-TW' || (data.multilingual == 0)">{{ data.title_ch }}</span>
          <span v-else>{{ data.title_en }}</span>
        </div>
    </div>
    <hr>
    <template v-if="data.content_ch || data.content_en">
      <div class="py-4 text-left">
        <div v-if="locale == 'zh-TW' || (data.multilingual == 0)" v-html="data.content_ch"></div>
        <div v-else v-html="data.content_en"></div>
      </div>
      <hr>
    </template>
    <div class="py-4" v-if="data.links.length>0">
        <div class="mb-3 text-xl">檔案連結</div>
        <template v-for="(item, index) in data.links" :key="index">
            <div class="text-blue-400">
                <a :href="item.url" target="_blank">{{item.title}}</a>
            </div>
        </template>
    </div>
  </div>
</template>

<style scoped lang="scss">
.category {
  @apply text-white px-2 py-1 inline-block text-center;
}
</style>

<i18n lang="yaml">
  en-US:
    back: 'Back'
  zh-TW:
    back: '回上一頁'
</i18n>