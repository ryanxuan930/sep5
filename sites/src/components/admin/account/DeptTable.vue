<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useElementSize } from '@vueuse/core'
  import FullModal from '@/components/FullModal.vue';
  import EditDept from '@/components/admin/account/EditDept.vue';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);
  const boxRef: any = ref(null);
  const boxWidth = useElementSize(boxRef).width;

  const selectedData: any = ref(null);
  const dataList: any = ref([]);
  async function getDataList(url = 'department') {
    if (store.userInfo.permission < 8) {
      url = `department/org/${store.userInfo.org_id}`;
    }
    const temp = await vr.Get(url, null, true, true);
    dataList.value = temp;
  }
  getDataList();
  
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
        <th class="w-[10%]">排序</th>
        <th class="w-[30%]">中文名稱</th>
        <th class="w-[25%]">所屬組織</th>
        <th class="w-[10%]">年級</th>
        <th class="w-[15%]">
          <a class="hyperlink blue" @click="open(null)">新增</a>
        </th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr v-if="item.dept_id > 1">
          <td :style="{'width': `${boxWidth*0.1}px`}">{{ item.dept_id }}</td>
          <td :style="{'width': `${boxWidth*0.1}px`}">{{ item.sort_order }}</td>
          <td>
            <div :style="{'width': `${boxWidth*0.3}px`}">{{ item.dept_name_ch }}</div>
          </td>
          <td>
            <div :style="{'width': `${boxWidth*0.25}px`}">{{ item.org_name_full_ch }}</div>
          </td>
          <td>
            <div :style="{'width': `${boxWidth*0.1}px`}">{{ item.has_grade == 1 ? item.grade : '不適用' }}</div>
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
          <div>編輯使用者分部</div>
        </div>
      </template>
      <template v-slot:content>
        <EditDept v-if="displayModal" :input-data="selectedData" @refreshPage="getDataList()" @closeModal="displayModal = false"></EditDept>
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