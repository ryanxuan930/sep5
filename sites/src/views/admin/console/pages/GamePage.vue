<script setup lang="ts">
  import { ref, watch } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';
  import { getUrlParams, paginationText } from '@/components/library/functions';
  import Toggle from '@vueform/toggle';
  import FullModal from '@/components/FullModal.vue';
  import EditGame from '@/components/admin/games/EditGame.vue';

  const message: Ref<string|null> = ref(null);
  const router = useRouter();
  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);

  const showAllGame: Ref<boolean> = ref(false);

  interface IObjList {
    [key: string]: any;
  }

  const linkUrl:Ref<IObjList[]|null> = ref(null);
  const gameList:Ref<IObjList|null> = ref(null);
  const currentUrl = ref('');

  function getGameList(url = 'game') {
    let suffix = '';
    if (showAllGame.value === true) {
      suffix = '-all';
    } else {
      suffix = '';
    }
    const page = getUrlParams(url, 'page');
    if (page !== null) {
      url = `${store.userInfo.admin_org_id}/game${suffix}?page=${page}`;
    } else {
      url = `${store.userInfo.admin_org_id}/game${suffix}`;
    }
    currentUrl.value = url;
    vr.Get(url).then( (res: any) => {
      gameList.value = res.data;
      linkUrl.value = res.links;
    });
  }
  getGameList();

  watch(showAllGame, () => {
    getGameList();
  });
  
  const adminDeptList:Ref<IObjList[]|null> = ref(null);
  function getAdminDeptList() {
    vr.Get('admin/admin-dept', adminDeptList, true, true);
  }
  getAdminDeptList();

  const selectedGame: Ref<IObjList|null> = ref(null);
  function openModal(input?: any) {
    if (input === undefined) {
      selectedGame.value = null;
    } else {
      selectedGame.value = input;
    }
    displayModal.value = true;
  }
</script>

<template>
  <div class="main-box">
    <div class="flex">
      <div class="flex-grow text-3xl font-medium mb-5">賽事列表</div>
    </div>
    <table v-if="gameList !== null">
      <tr>
        <th class="w-[30%]">名稱</th>
        <th class="w-[15%]">項目</th>
        <th class="w-[30%]">主辦</th>
        <th class="w-[15%]">日期</th>
        <th class="w-[10%]">
          <a class="hyperlink blue" @click="openModal()">新增</a>
        </th>
      </tr>
      <template v-if="gameList.length > 0">
        <template v-for="(item, index) in gameList" :key="index">
          <tr>
            <td>{{ item.game_name_ch }}</td>
            <td>{{ item.sport_name_ch }}</td>
            <td>
              <template v-for="(host, indexA) in item.host_list" :key="indexA">
                <template v-for="(dept, indexB) in adminDeptList" :key="indexB">
                  <div v-if="host == dept.admin_dept_id">[{{ dept.admin_org_name_ch }}]{{ dept.admin_dept_name_ch }} </div>
                </template>
              </template>
            </td>
            <td>{{ item.event_start }}</td>
            <td>
              <router-link class="hyperlink blue" to="">開啟</router-link>
            </td>
          </tr>
        </template>
      </template>
      <tr v-else>
        <td colspan="5" class="text-center">目前無賽事</td>
      </tr>
    </table>
    <div class="page-btn">
      <template v-for="(item, index) in linkUrl" :key="index">
        <button :class="{'general-button': true, 'blue': !item.active, 'active': item.active }" :disabled="item.url===null" @click="getGameList(item.url)">{{ paginationText(item.label) }}</button>
      </template>
    </div>
    <div>
      <span class="inline-block mr-2">已完成賽事</span>
      <Toggle offLabel="隱藏" onLabel="顯示" v-model="showAllGame"></Toggle>
    </div>
    <FullModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal">賽事資訊</div>
        </div>
      </template>
      <template v-slot:content>
        <EditGame v-if="displayModal" :game-data="selectedGame" @refreshPage="getGameList(currentUrl)" @closeModal="displayModal = false"></EditGame>
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
  @apply w-[768px] md:w-full text-left;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium;
  }
  th {
    @apply bg-blue-100;
  }
}
</style>
<style src="@vueform/toggle/themes/default.css"></style>