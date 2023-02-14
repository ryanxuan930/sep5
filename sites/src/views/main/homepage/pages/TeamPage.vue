<script setup lang="ts">
import { ref, inject } from 'vue';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';

const vr = new VueRequest();
const adminOrgId = useRoute().params.adminOrgId;
const pageData: IPageData = inject('pageData');
const teamList: any = ref([]);
vr.Get(`${pageData.value.orgId}/school-team-all`, teamList);
const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div>
    <template v-for="(item, index) in teamList" :key="index">
      <div class="title" v-if="locale == 'zh-TW'">{{ item.team_name_ch }}</div>
      <div class="title" v-else>{{ item.team_name_en }}</div>
      <hr>
      <div class="sub-title" v-if="locale == 'zh-TW'">校隊介紹</div>
      <div class="sub-title" v-else>About</div>
      <div class="content" v-html="item.team_info"></div>
    </template>
  </div>
</template>

<style scoped lang="scss">
.title {
  @apply text-2xl font-medium;
}
.title+hr {
  @apply my-2;
}
.content {
  @apply p-3;
}
.sub-title {
  @apply text-xl font-medium;
}
</style>