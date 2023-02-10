<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { getUrlParams, paginationText } from '@/components/library/functions';
  import { useElementSize } from '@vueuse/core'
  import FullModal from '@/components/FullModal.vue';
  import EditUser from '@/components/admin/account/EditUser.vue';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);
  const boxRef: any = ref(null);
  const boxWidth = useElementSize(boxRef).width;

  const selectedData: any = ref(null);
  const dataList: any = ref([]);
  const linkUrl: any = ref([]);
  const currentUrl = ref('');
  async function getDataList(url = 'user') {
    const page = getUrlParams(url, 'page');
    if (page !== null) {
      url = `user?page=${page}`;
    }
    const temp = await vr.Get(url, null, true, true);
    currentUrl.value = url;
    dataList.value = temp.data;
    linkUrl.value = temp.links;
  }
  getDataList();
  
  function open(input: any) {
    selectedData.value = input;
    displayModal.value = true;
  }
</script>

<template>
  <div id="section-box" class="overflow-hidden flex flex-col h-full" ref="boxRef">
    <div class="flex-grow overflow-auto">
      <table>
        <tr>
          <th class="w-[10%]">編號</th>
          <th class="w-[15%]">代碼</th>
          <th class="w-[15%]">姓名</th>
          <th class="w-[25%]">帳號</th>
          <th class="w-[20%]">組織</th>
          <th class="w-[15%]">
            <a class="hyperlink blue" @click="open(null)">新增</a>
          </th>
        </tr>
        <template v-for="(item, index) in dataList" :key="index">
          <tr>
            <td :style="{'width': `${boxWidth*0.1}px`}">{{ item.u_id }}</td>
            <td>
              <div :style="{'width': `${boxWidth*0.15}px`}">{{ item.athlete_id }}</div>
            </td>
            <td>
              <div :style="{'width': `${boxWidth*0.15}px`}">{{ item.last_name_ch }}{{ item.first_name_ch }}</div>
            </td>
            <td>
              <div :style="{'width': `${boxWidth*0.25}px`}">{{ item.account }}</div>
            </td>
            <td>
              <div :style="{'width': `${boxWidth*0.20}px`}">{{ item.org_name_full_ch }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.15}px`}">
              <a class="hyperlink blue" @click="open(item)">查看</a>
            </td>
          </tr>
        </template>
      </table>
    </div>
    <div class="page-btn flex-shrink-0">
      <template v-for="(item, index) in linkUrl" :key="index">
        <button :class="{'general-button': true, 'blue': !item.active, 'active': item.active }" :disabled="item.url===null" @click="getDataList(item.url)">{{ paginationText(item.label) }}</button>
      </template>
    </div>
    <FullModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div>編輯使用者</div>
        </div>
      </template>
      <template v-slot:content>
        <EditUser v-if="displayModal" :input-data="selectedData" @refreshPage="getDataList(currentUrl)" @closeModal="displayModal = false"></EditUser>
      </template>
    </FullModal>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium overflow-hidden;
    div {
      @apply whitespace-nowrap overflow-auto w-full py-2;
    }
  }
  th {
    @apply bg-blue-100 sticky top-0;
  }
}
input[type=radio] {
  @apply border-0 w-full h-6;
}
.page-btn {
  @apply flex my-5 gap-3;
}
.general-button.active {
  @apply bg-blue-400;
}
</style>