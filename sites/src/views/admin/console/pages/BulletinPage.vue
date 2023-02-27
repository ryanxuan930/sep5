<script setup lang="ts">
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';
  import type { IPostData } from '@/components/library/interfaces';
  import { getUrlParams, paginationText } from '@/components/library/functions';
  import FullModal from '@/components/FullModal.vue';
  import EditPost from '@/components/admin/bulletin/EditPost.vue';
  import BulletinCategory from '@/assets/BulletinCategory.json';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);

  interface IObjList {
    [key: string]: any;
  }

  const linkUrl:Ref<IObjList[]|null> = ref(null);
  const bulletinList:Ref<IPostData[]|IObjList> = ref([]);
  const currentUrl = ref('');
  function getBulletinList(url = `${store.userInfo.admin_org_id}/bulletin`) {
    currentUrl.value = url;
    const page = getUrlParams(url, 'page');
    if (page !== null) {
      url = `${store.userInfo.admin_org_id}/bulletin?page=${page}`;
    }
    vr.Get(url).then( (res: any) => {
      bulletinList.value = res.data;
      linkUrl.value = res.links;
    });
  }
  getBulletinList();

  const selectedPost: Ref<IPostData|null> = ref(null);
  function openModal(input: IPostData|undefined = undefined) {
    if (input === undefined) {
      selectedPost.value = null;
    } else {
      selectedPost.value = input;
    }
    displayModal.value = true;
  }

  const CategoryColor = [
    'bg-blue-600 text-white',
    'bg-orange-600 text-white',
    'bg-green-600 text-white',
    'bg-red-600 text-white',
  ];
</script>

<template>
  <div class="main-box">
    <div class="flex">
      <div class="flex-grow text-3xl font-medium mb-5">公告資訊</div>
    </div>
    <table v-if="bulletinList !== null">
      <tr>
        <th rowspan="2" class="w-[10%] th-border text-center">置頂</th>
        <th class="w-[15%]">類別</th>
        <th class="w-[30%]">標題</th>
        <th class="w-[15%]">日期</th>
        <th class="w-[20%]">賽事</th>
        <th rowspan="2" class="w-[10%] th-border">
          <a class="hyperlink blue" @click="openModal()">新增</a>
        </th>
      </tr>
      <tr>
        <th class="th-border">狀態</th>
        <th colspan="2" class="th-border">內文</th>
        <th class="th-border">張貼</th>
      </tr>
      <template v-if="bulletinList.length > 0">
        <template v-for="(item, index) in bulletinList" :key="index">
          <tr>
            <td rowspan="2" class="td-border text-center">
              <div v-if="item.pinned">⬤</div>
            </td>
            <td>
              <div :class="['p-1 text-center rounded', CategoryColor[item.category]]">{{ BulletinCategory[item.category]['zh-TW'] }}</div>
            </td>
            <td>{{ item.title_ch }}</td>
            <td>{{ item.post_date }}</td>
            <td>{{ item.game_name_ch }}</td>
            <td rowspan="2" class="td-border">
              <a class="hyperlink blue" @click="openModal(item)">編輯</a>
            </td>
          </tr>
          <tr>
            <td class="td-border">
              <div v-if="item.release" class="text-green-600">顯示</div>
              <div  v-else class="text-red-600">隱藏</div>
            </td>
            <td colspan="2" class="td-border text-gray-400">{{ item.content_ch === null ? '本公告無內文' : '本公告有內文' }}</td>
            <td class="td-border">{{ item.admin_dept_name_ch }}</td>
          </tr>
        </template>
      </template>
      <tr v-else>
        <td colspan="6" class="text-center">目前無公告</td>
      </tr>
    </table>
    <div class="page-btn">
      <template v-for="(item, index) in linkUrl" :key="index">
        <button :class="{'general-button': true, 'blue': !item.active, 'active': item.active }" :disabled="item.url===null" @click="getBulletinList(item.url)">{{ paginationText(item.label) }}</button>
      </template>
    </div>
    <FullModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal">公告資訊</div>
        </div>
      </template>
      <template v-slot:content>
        <EditPost v-if="displayModal" :post-data="selectedPost" @refreshPage="getBulletinList()" @closeModal="displayModal = false"></EditPost>
      </template>
    </FullModal>
  </div>
</template>

<style scoped lang="scss">
.page-btn {
  @apply flex my-5 gap-3;
}
.general-button.active {
    @apply bg-blue-400;
  }
table {
  @apply w-[768px] md:w-full text-left font-medium;
  .td-border, .th-border {
    @apply border-b-[1px] px-2 py-1 border-gray-300 font-medium;
  }
  th, .th-border {
    @apply p-2 bg-blue-100 font-medium;
  }
  td, .td-border {
    @apply p-2 font-medium;
  }
}
</style>