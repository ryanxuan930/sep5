<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import VueRequest from '@/vue-request';
import FullModal from '@/components/FullModal.vue';
import AccountSettings from '@/components/registration/settings/AccountSettings.vue';
import { openWindow } from '@/components/library/functions';
import Config from '@/assets/config.json';

const store = useUserStore();
const vr = new VueRequest(store.token);
const displayModal = ref(0);

const userData: any = ref(null);
function getUserData() {
  vr.Get('auth/user/info', userData, true, true);
}
getUserData();

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div class="flex flex-col gap-5 h-full overflow-auto" v-if="userData != null">
    <div class="section-box flex-shrink-0">
      <div class="text-2xl font-medium">{{ t('setting') }}</div>
      <hr class="my-2 border-[1px]">
      <label class="round-input-label mb-5">
        <div class="title">{{ t('language') }}</div>
        <select class="select" v-model="$i18n.locale">
          <option value="zh-TW">正體中文</option>
          <option value="en-US">English</option>
        </select>
      </label>
    </div>
    <div class="section-box flex flex-col">
      <div class="text-2xl font-medium">{{ t('account-setting') }}</div>
      <hr class="my-2 border-[1px]">
      <!--
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 items-center overflow-auto shadow-inner">
        <div class="md:row-span-6 lg:grid-cols-4"></div>
        <div class="break-words">{{ t('account') }} : {{ userData.account }}</div>
        <div>{{ t('athlete-id') }} : {{ userData.athlete_id }}</div>
        <div>{{ t('nationality') }} : 
          <span v-if="locale == 'zh-TW'">{{ userData.country_name_ch }}</span>
          <span v-else>{{ userData.country_name_en }}</span>
        </div>
        <div>{{ t('chinese-name') }} : {{ userData.last_name_ch }}{{ userData.first_name_ch }}</div>
        <div>{{ t('english-name') }} : {{ userData.first_name_en }} {{ userData.last_name_en }}</div>
        <div>{{ t('cellphone') }} : {{ userData.phone }}</div>
        <div>{{ t('organization') }} : 
          <span v-if="locale == 'zh-TW'">{{ userData.org_name_full_ch }}</span>
          <span v-else>{{ userData.org_name_full_en }}</span>
        </div>
        <div>{{ t('department') }} : 
          <span v-if="locale == 'zh-TW'">{{ userData.dept_name_ch }}</span>
          <span v-else>{{ userData.dept_name_en }}</span>
        </div>
        <div>{{ t('permission') }} : 
          <span v-if="userData.permission == 0">{{ t('l0') }}</span>
          <span v-if="userData.permission == 1">{{ t('l1') }}</span>
          <span v-if="userData.permission == 2">{{ t('l2') }}</span>
        </div>
        <div>{{ t('sex') }} : 
          <span v-if="userData.sex == 0">{{ t('others') }}</span>
          <span v-if="userData.sex == 1">{{ t('male') }}</span>
          <span v-if="userData.sex == 2">{{ t('female') }}</span>
        </div>
        <div>{{ t('height') }} : {{ userData.height/100 }} cm</div>
        <div>{{ t('weight') }} : {{ userData.weight/100 }} kg</div>
      </div>-->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
        <button class="round-full-button blue mt-5" @click="displayModal = 1">{{ t('view') }}</button>
        <template v-if="userData.monkey_user_id != undefined">
          <button class="round-full-button blue mt-5" v-if="userData.monkey_user_id == null" @click="displayModal = 2">{{ t('reset-password') }}</button>
          <button class="round-full-button blue mt-5" v-if="userData.monkey_user_id != null" @click="openWindow('https://sports.nsysu.edu.tw/monkeyid/#/login')">Monkey ID</button>
        </template>
        <button class="round-full-button blue mt-5" v-else @click="displayModal = 2">{{ t('reset-password') }}</button>
        <button class="round-full-button blue mt-5" @click="displayModal = 3" v-if="!Config.deptAsClass">{{ t('change-unit') }}</button>
      </div>
    </div>
    <FullModal v-show="displayModal" @closeModal="displayModal = 0">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal == 1">{{ t('account-setting') }}</div>
          <div v-if="displayModal == 2">{{ t('reset-password') }}</div>
        </div>
      </template>
      <template v-slot:content>
        <AccountSettings v-if="displayModal == 1" :input-data="userData" @refreshPage="getUserData" @closeModal="displayModal = 0"></AccountSettings>
      </template>
    </FullModal>
  </div>
</template>

<style scoped lang="scss">
    
</style>

<i18n lang="yaml">
  en-US:
    setting: 'Settings'
    language: 'Language'
    account-setting: 'Account Settings'
    chinese-name: 'Chinese Name'
    english-name: 'Name'
    edit: 'Edit'
    avatar: 'Avatar'
    first-name-ch: 'Chinese First Name'
    last-name-ch: 'Chinese Last Name'
    first-name-en: 'First Name'
    last-name-en: 'Last Name'
    account: 'Account'
    athlete-id: 'Athlete ID'
    organization: 'Organization'
    department: 'Department'
    nationality: 'Nationality'
    unified-id: 'National ID / ARC ID'
    is-student: 'Student'
    yes: 'Yes'
    no: 'No'
    school-id: 'Student / Faculty ID'
    grade: 'Grade'
    birthday: 'Birthday'
    city: 'City of Domicile (TW Citizen Only)'
    address: 'Address (Current living place)'
    cellphone: 'Cellphone'
    telephone: 'Telephone'
    emergency-contact: 'Emergency Contact'
    emergency-phone: 'Contact Phone'
    sex: 'Sex'
    height: 'Height'
    weight: 'Weight'
    male: 'Male'
    female: 'Female'
    others: 'Others'
    blood: 'Blood Type'
    sport-gifited: 'Sport Gifited (TW Student Only)'
    sport-gifited-item: 'Sports'
    indigenous: 'Indigenous (TW Citizen Only)'
    tribe: 'Indigenous Tribe'
    permission: 'Permission'
    l0: 'General'
    l1: 'Departmental'
    l2: 'Organizational'
    school-team: 'School Team Member'
    teams: 'Teams'
    view: 'View'
    reset-password: 'Reset Password'
    change-unit: 'Change Unit'
  zh-TW:
    setting: '設定'
    language: '語言'
    account-setting: '帳號設定'
    chinese-name: '中文姓名'
    english-name: '姓名英文拼音'
    edit: '編輯'
    avatar: '大頭照'
    first-name-ch: '中文名字'
    last-name-ch: '中文姓氏'
    first-name-en: '英文名字'
    last-name-en: '英文姓氏'
    account: '帳號'
    athlete-id: '選手代碼'
    organization: '所數組織單位'
    department: '分部/系所'
    nationality: '國籍'
    unified-id: '身分證/居留證號'
    is-student: '學生身份'
    yes: '是'
    no: '否'
    school-id: '學號/教職員編號'
    grade: '年級'
    birthday: '生日'
    city: '戶籍城市 (限本國籍)'
    address: '居住地址'
    cellphone: '手機號碼'
    telephone: '電話號碼'
    emergency-contact: '緊急聯絡人'
    emergency-phone: '聯絡人電話'
    sex: '生理性別'
    height: '身高'
    weight: '體重'
    male: '男'
    female: '女'
    others: '其他'
    blood: '血型'
    sport-gifited: '體優身份 (限本國學生)'
    sport-gifited-item: '體優項目'
    indigenous: '原住民身份 (限本國籍)'
    tribe: '所數族群部落'
    permission: '權限'
    l0: '一般使用者'
    l1: '分部管理員'
    l2: '組織管理員'
    school-team: '校隊身份'
    teams: '所數校隊'
    view: '查看'
    reset-password: '重設密碼'
    change-unit: '變更所屬單位'
</i18n>