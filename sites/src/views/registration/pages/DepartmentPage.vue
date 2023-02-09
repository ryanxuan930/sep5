<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import VueRequest from '@/vue-request';
import { useElementSize } from '@vueuse/core'
import FullModal from '@/components/FullModal.vue';
import EditDept from '@/components/registration/module/EditDept.vue';
import SpinnerLoading from '@/components/SpinnerLoading.vue';

const store = useUserStore()
const vr = new VueRequest(store.token);
const displayModal = ref(false);
const boxRef: any = ref(null);
const boxWidth = useElementSize(boxRef).width;

const selectedData: any = ref(null);
const dataList: any = ref([]);
async function getDataList() {
  if (store.userInfo.permission == 2) {
    await vr.Get(`department/org/code/${store.userInfo.org_code}`, dataList, true, true);
  } else {
    const temp = await vr.Get(`department/${store.userInfo.dept_id}`, null, true, true);
    dataList.value = [temp];
  }
}
getDataList();

function open(input: any) {
  selectedData.value = input;
  displayModal.value = true;
}
const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <SpinnerLoading v-show="dataList == null"></SpinnerLoading>
  <div class="main-box overflow-hidden flex flex-col gap-3" id="section-box" ref="boxRef">
    <div class="md:col-span-4 text-2xl font-medium">{{ t('dept-info') }}</div>
    <div class="overflow-auto h-full flex-grow">
      <table>
        <tr>
          <th class="w-[10%]">ID</th>
          <th class="w-[10%]">{{ t('order') }}</th>
          <th class="w-[30%]">{{ t('department') }}</th>
          <th class="w-[25%]">{{ t('organization') }}</th>
          <th class="w-[10%]">{{ t('grade') }}</th>
          <th class="w-[15%]">
            <a class="hyperlink blue" v-if="store.userInfo.permission >= 2 && store.userInfo.org_code.substring(0, 1) != 'O'" @click="open(null)">{{ t('add') }}</a>
          </th>
        </tr>
        <template v-for="(item, index) in dataList" :key="index">
          <tr v-if="item.dept_id > 1">
            <td :style="{'width': `${boxWidth*0.1}px`}">{{ item.dept_id }}</td>
            <td :style="{'width': `${boxWidth*0.1}px`}">{{ item.sort_order }}</td>
            <td>
              <div :style="{'width': `${boxWidth*0.3}px`}">
                <template v-if="locale == 'zh-TW'">{{ item.dept_name_ch }}</template>
                <template v-else>{{ item.dept_name_en }}</template>
              </div>
            </td>
            <td>
              <div :style="{'width': `${boxWidth*0.25}px`}">
                <template v-if="locale == 'zh-TW'">{{ item.org_name_full_ch }}</template>
                <template v-else>{{ item.org_name_full_en }}</template>
              </div>
            </td>
            <td>
              <div :style="{'width': `${boxWidth*0.1}px`}">{{ item.has_grade == 1 ? item.grade : t('na') }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.15}px`}">
              <a class="hyperlink blue" v-if="item.mutable == 1" @click="open(item)">{{ t('view') }}</a>
            </td>
          </tr>
        </template>
      </table>
    </div>
    <FullModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div>{{ t('edit-dept-info') }}</div>
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

<i18n lang="yaml">
  en-US:
    dept-info: 'Department Information'
    edit-dept-info: 'Edit Department Information'
    organization: 'Organization'
    department: 'Department'
    order: 'Order'
    grade: 'Grade'
    yes: 'Yes'
    no: 'No'
    na: 'N/A'
    add: 'Add'
    view: 'View'
  zh-TW:
    dept-info: '分部/系所資訊'
    edit-dept-info: '編輯分部/系所資訊'
    organization: '組織單位'
    department: '分部/系所'
    order: '排序'
    grade: '年級'
    yes: '有'
    no: '無'
    na: '不適用'
    add: '新增'
    view: '查看'
</i18n>