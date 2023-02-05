<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import VueRequest from '@/vue-request';
import FullModal from '@/components/FullModal.vue';
import AccountSettings from '@/components/registration/settings/AccountSettings.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const displayModal = ref(false);

const userData: any = ref(null);
vr.Get('auth/user/info', userData, true, true);

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div class="flex flex-col gap-5 h-full" v-if="userData != null">
    <div class="section-box">
      <div class="text-2xl font-medium">{{ t('setting') }}</div>
      <hr class="my-2 border-[1px]">
      <label class="round-input-label">
        <div class="title">{{ t('language') }}</div>
        <select class="select" v-model="$i18n.locale">
          <option value="zh-TW">正體中文</option>
          <option value="en-US">English</option>
        </select>
      </label>
    </div>
    <div class="section-box">
      <div class="text-2xl font-medium">{{ t('account-setting') }}</div>
      <hr class="my-2 border-[1px]">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 items-center ">
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
      </div>
      <button class="round-full-button blue mt-5" @click="displayModal = true">{{ t('edit') }}</button>
    </div>
    <FullModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div>{{ t('account-settins') }}</div>
        </div>
      </template>
      <template v-slot:content>
        <AccountSettings :input-data="userData" @closeModal="displayModal = false"></AccountSettings>
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
    organization: 'Organization'
    department: 'Department'
    nationality: 'Nationality'
    sex: 'Sex'
    account: 'Account'
    height: 'Height'
    weight: 'Weight'
    athlete-id: 'Athlete ID'
    cellphone: 'Phone Number'
    avatar: 'Avatar'
    male: 'Male'
    female: 'Female'
    others: 'Others'
    permission: 'Permission'
    l0: 'General'
    l1: 'Departmental'
    l2: 'Organizational'
    edit: 'Edit'
  zh-TW:
    setting: '系統設定'
    language: '語言'
    account-setting: '帳號設定'
    chinese-name: '中文姓名'
    english-name: '英文姓名'
    organization: '組織單位'
    department: '分部/系所'
    nationality: '國籍'
    sex: '生理性別'
    account: '帳號'
    height: '身高'
    weight: '體重'
    athlete-id: '選手代碼'
    cellphone: '手機號碼'
    avatar: '照片'
    male: '生理男'
    female: '生理女'
    others: '其他'
    permission: '權限'
    l0: '一般使用者'
    l1: '分部管理員'
    l2: '組織管理員'
    edit: '編輯'
</i18n>