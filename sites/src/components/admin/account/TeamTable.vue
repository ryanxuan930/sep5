<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useElementSize } from '@vueuse/core'
  import FullModal from '@/components/FullModal.vue';
  import EditTeam from '@/components/admin/account/EditTeam.vue';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);
  const boxRef: any = ref(null);
  const boxWidth = useElementSize(boxRef).width;

  const selectedData: any = ref(null);
  const dataList: any = ref([]);
  let userData: any = null;
  async function getDataList(url = `${userData.related_user_org_id}/school-team`) {
    dataList.value = await vr.Get(url, null, true, true);
  }

  async function getUserData() {
    userData = await vr.Get('auth/admin/info', null, true, true);
    getDataList();
  }
  getUserData();
  
  function open(input: any) {
    selectedData.value = input;
    displayModal.value = true;
  }
</script>

<template>
  <div id="section-box" ref="boxRef">
    <table>
      <tr>
        <th class="w-[10%]">編號</th>
        <th class="w-[30%]">中文名稱</th>
        <th class="w-[25%]">所屬組織</th>
        <th class="w-[20%]">運動項目</th>
        <th class="w-[15%]">
          <a class="hyperlink blue" @click="open(null)">新增</a>
        </th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr>
          <td :style="{'width': `${boxWidth*0.1}px`}">{{ item.school_team_id }}</td>
          <td>
            <div :style="{'width': `${boxWidth*0.3}px`}">{{ item.team_name_ch }}</div>
          </td>
          <td>
            <div :style="{'width': `${boxWidth*0.25}px`}">{{ item.org_name_full_ch }}</div>
          </td>
          <td>
            <div :style="{'width': `${boxWidth*0.1}px`}">{{ item.sport_name_ch }}</div>
          </td>
          <td :style="{'width': `${boxWidth*0.15}px`}">
            <a class="hyperlink blue" @click="open(item)">查看</a>
          </td>
        </tr>
      </template>
    </table>
    <FullModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div>編輯校隊資訊</div>
        </div>
      </template>
      <template v-slot:content>
        <EditTeam v-if="displayModal" :input-data="selectedData" @refreshPage="getDataList()" @closeModal="displayModal = false"></EditTeam>
      </template>
    </FullModal>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left overflow-scroll;
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