<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import SmallLoader from '@/components/SmallLoader.vue';
import FullModal from '@/components/FullModal.vue';
import ImportAthlete from '@/components/registration/module/ImportAthlete.vue'
import Config from '@/assets/config.json';

const store = useUserStore();
const route = useRoute();
const vr = new VueRequest(store.token);
const displayModal = ref(0);
const adminOrgId = route.params.adminOrgId;
const userList: any = ref(null);

async function getUserList() {
  await vr.Get('user-partial', userList, true, true);
}
getUserList();

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div class="flex flex-col gap-5 h-full">
    <div class="section-box" v-if="Config.allowAddUser">
      <div class="section-title">{{ t('add-athlete') }}</div>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 py-2">
        <button class="round-full-button blue" v-if="Config.changeDeptPermission">{{ t('request') }}</button>
        <button class="round-full-button blue" @click="displayModal = 2">{{ t('import') }}</button>
      </div>
    </div>
    <div class="section-box h-full flex-grow overflow-hidden flex flex-col gap-3">
      <div class="section-title">{{ t('athlete-list') }}</div>
      <div class="flex-grow h-full overflow-auto">
        <table>
          <tr>
            <th>{{ t('athlete-id') }}</th>
            <th v-if="Config.deptAsClass">{{ t('class') }}</th>
            <th v-else>{{ t('department') }}</th>
            <th>{{ t('name') }}</th>
            <th>{{ t('sex') }}</th>
            <th></th>
          </tr>
          <template v-for="(item, index) in userList" :key="index">
            <tr>
              <td>{{ item.athlete_id }}</td>
              <td>
                <template v-if="locale == 'zh-TW'">{{ item.dept_name_ch }}</template>
                <template v-else>{{ item.dept_name_en }}</template>
              </td>
              <td>
                <template v-if="locale == 'zh-TW'">{{ item.last_name_ch }}{{ item.first_name_ch }}</template>
                <template v-else>{{ item.first_name_en }} {{ item.last_name_en }}</template>
              </td>
              <td>
                <span v-show="item.sex == 0">{{ t('others') }}</span>
                <span v-show="item.sex == 1">{{ t('male') }}</span>
                <span v-show="item.sex == 2">{{ t('female') }}</span>
              </td>
              <td></td>
            </tr>
          </template>
        </table>
        <SmallLoader v-show="userList == null"></SmallLoader>
      </div>
    </div>
    <FullModal v-show="displayModal" @closeModal="displayModal = 0">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal == 2">{{ t('import') }}</div>
        </div>
      </template>
      <template v-slot:content>
        <ImportAthlete v-if="displayModal" @refreshPage="getUserList()" @closeModal="displayModal = 0"></ImportAthlete>
      </template>
    </FullModal>
  </div>
</template>

<style scoped lang="scss">
.section-title {
  @apply text-2xl font-medium;
}
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
    class: 'Class'
  zh-TW:
    add-athlete: '加入選手'
    athlete-list: '選手列表'
    request: '請求加入列表'
    import: '批次匯入'
    athlete-id: '選手代碼'
    department: '分部/系所'
    name: '姓名'
    sex: '性別'
    male: '男'
    female: '女'
    others: '其他'
    class: '班級'
</i18n>