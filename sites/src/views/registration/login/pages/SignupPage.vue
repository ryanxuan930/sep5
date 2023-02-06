<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import type { Ref } from 'vue';
import VueRequest from '@/vue-request';
import { useRouter, useRoute } from 'vue-router';

const vr = new VueRequest();
const router = useRouter();
const route = useRoute();
const data = reactive({
  account: '',
  password: '',
  first_name_ch: '',
  last_name_ch: '',
  first_name_en: '',
  last_name_en: '',
  org_code: 'O0000',
  dept_id: 0,
  grade: 0,
  unified_id: '',
  birthday: '2000-01-01',
  nationality: 'TW',
  is_indigenous: 0,
  indigenous_tribe_id: 0,
  is_sport_gifited: 0,
  gifited_sport_id: 0,
  is_school_team: 0,
  sex: 1,
  height: 0,
  weight: 0,
  permission: 0,
});
const passwordConfirm: Ref<string> = ref('');
const isProcessing = ref(false);

const countryList: any = ref(null);
vr.Get('country', countryList);

const errorList = reactive({
  account: {
      filled: false,
      format: true,
      unique: true,
  },
  firstNameCh: {
      filled: false,
  },
  lastNameCh: {
      filled: false,
  },
  firstNameEn: {
      filled: false,
  },
  lastNameEn: {
      filled: false,
  },
  password: {
    filled: false,
    format: true,
  },
  passwordConfirm: {
    filled: false,
    same: true,
  },
});
watch(data, () => {
  errorList.account.filled = data.account.length > 0;
  // eslint-disable-next-line
  errorList.account.format = (/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/.test(data.account));
  errorList.firstNameCh.filled = data.first_name_ch.length > 0;
  errorList.lastNameCh.filled = data.last_name_ch.length > 0;
  errorList.firstNameEn.filled = data.first_name_en.length > 0;
  errorList.lastNameEn.filled = data.last_name_en.length > 0;
  errorList.password.filled = data.password.length > 0;
  errorList.password.format = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/.test(data.password);
});
watch(passwordConfirm, () => {
  errorList.passwordConfirm.same = data.password === passwordConfirm.value;
  errorList.passwordConfirm.filled = passwordConfirm.value.length > 0;
})

async function exist() {
  const res = await vr.Get(`auth/user/exist/${data.account}`);
  if (res.message == true) {
    errorList.account.unique = false;
  }
}

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
function submitAll() {
  for (const item of Object.entries(errorList)) {
    for (const error of Object.entries(item[1])) {
      if (error[1] === false) {
        return;
      }
    }
  }
  isProcessing.value = true;
  vr.Post('auth/user/register', data).then( (res: any) => {
    if (res.status == 'A01') {
      alert('已成功註冊');
      isProcessing.value = false;
      router.push(`/${route.params.adminOrgId}/registration/login`);
      return;
    }
  });
}
</script>

<template>
  <div class="h-screen flex flex-col overflow-auto">
    <div class="flex-grow p-5"></div>
    <div class="bg-white sm:shadow p-5 w-full h-screen sm:w-2/3 sm:h-fit mx-auto flex flex-col gap-5">
      <div>
        <router-link :to="`/${$route.params.adminOrgId}/registration/login`" class="hyperlink blue">{{ t('back') }}</router-link>
      </div>
      <hr>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <label class="round-input-label">
          <div class="title">{{ t('account') }}</div>
          <input class="input" type="text" v-model="data.account" @blur="exist">
          <div v-show="errorList.account.filled == false" class="warning">{{ t('require-account') }}</div>
          <div v-show="errorList.account.unique == false" class="warning">{{ t('exist') }}</div>
        </label>
        <label class="round-input-label">
          <div class="title">{{ t('nationality') }}</div>
          <select class="select" v-model="data.nationality">
            <template v-for="(item, index) in countryList" :key="index">
              <option :value="item.country_code">
                <span v-if="locale == 'zh-TW'">{{ item.country_name_ch }}</span>
                <span v-else>{{ item.country_name_en }}</span>
              </option>
            </template>
          </select>
        </label>
        <label class="round-input-label">
          <div class="title">{{ t('last-name-ch') }}</div>
          <input class="input" type="text" v-model="data.last_name_ch">
          <div v-show="errorList.lastNameCh.filled == false" class="warning">{{ t('require-last-name-ch') }}</div>
        </label>
        <label class="round-input-label">
          <div class="title">{{ t('first-name-ch') }}</div>
          <input class="input" type="text" v-model="data.first_name_ch">
          <div v-show="errorList.firstNameCh.filled == false" class="warning">{{ t('require-first-name-ch') }}</div>
        </label>
        <div v-if="locale == 'en-US'" class="md:col-span-2">If your Chinese first / last name is not applicable, please input your English first / last name.</div>
        <label class="round-input-label">
          <div class="title">{{ t('last-name-en') }}</div>
          <input class="input" type="text" v-model="data.last_name_en">
          <div v-show="errorList.lastNameEn.filled == false" class="warning">{{ t('require-last-name-en') }}</div>
        </label>
        <label class="round-input-label">
          <div class="title">{{ t('first-name-en') }}</div>
          <input class="input" type="text" v-model="data.first_name_en">
          <div v-show="errorList.firstNameEn.filled == false" class="warning">{{ t('require-first-name-en') }}</div>
        </label>
        <label class="round-input-label">
          <div class="title">{{ t('sex') }}</div>
          <select class="select" v-model="data.sex">
            <option value="1">{{ t('male') }}</option>
            <option value="2">{{ t('female') }}</option>
            <option value="0">{{ t('others') }}</option>
          </select>
        </label>
        <label class="round-input-label">
          <div class="title">{{ t('birthday') }}</div>
          <input class="input" type="date" v-model="data.birthday">
        </label>
        <label class="round-input-label">
          <div class="title">{{ t('password') }}</div>
          <input class="input" type="password" v-model="data.password">
          <div v-show="errorList.password.filled == false" class="warning">{{ t('require-password') }}</div>
          <div v-show="errorList.password.format == false" class="warning">{{ t('password-format') }}</div>
        </label>
        <label class="round-input-label">
          <div class="title">{{ t('password-confirm') }}</div>
          <input class="input" type="password" v-model="passwordConfirm">
          <div v-show="errorList.passwordConfirm.filled == false" class="warning">{{ t('require-password-confirm') }}</div>
          <div v-show="errorList.passwordConfirm.same == false" class="warning">{{ t('password-same') }}</div>
        </label>
      </div>
      <button class="round-full-button blue" @click="submitAll">{{ t('signup') }}</button>
      <div >傳輸中 Processing...</div>
    </div>
    <div class="flex-grow p-5"></div>
  </div>
</template>

<style lang="scss" scoped>
.warning {
  @apply text-red-600 text-sm my-1;
}
</style>

<i18n lang="yaml">
  en-US:
    back: 'Back'
    account: 'Account(Email)'
    password: 'Password'
    first-name-ch: 'Chinese FIRST Name'
    last-name-ch: 'Chinese LAST Name'
    first-name-en: 'FIRST Name'
    last-name-en: 'LAST Name'
    password-confirm: 'Confirm Password'
    organization: 'Organiazaion'
    sex: 'Sex'
    birthday: 'Birthday'
    signup: 'Sign up'
    male: 'Male'
    female: 'Female'
    others: 'Others'
    nationality: 'Nationality'
    password-format: 'Your password must be at least 8 characters including a lowercase letter, an uppercase letter, and a number'
    exist: 'Account already exists'
    require-first-name-ch: 'Please input Chinese first name'
    require-last-name-ch: 'Please input Chinese last name'
    require-first-name-en: 'Please input English first name'
    require-last-name-en: 'Please input English last name'
    require-account: 'Please input your account'
    require-password: 'Please input your password'
    require-password-confirm: 'Please input your password again'
    password-same: 'Password do not match'
  zh-TW:
    back: '回上頁'
    account: '帳號(Email)'
    password: '密碼'
    first-name-ch: '中文名字'
    last-name-ch: '中文姓氏'
    first-name-en: '名字英文拼音'
    last-name-en: '姓氏英文拼音'
    password-confirm: '確認密碼'
    organization: '所屬組織單位'
    sex: '生理性別'
    birthday: '生日'
    signup: '註冊'
    male: '生理男'
    female: '生理女'
    others: '其他'
    nationality: '國籍'
    password-format: '密碼需有大小寫字母、數字並至少8個字元'
    exist: '帳號已存在'
    require-first-name-ch: '請輸入中文名字'
    require-last-name-ch: '請輸入中文姓氏'
    require-first-name-en: '請輸入名字英文拼音'
    require-last-name-en: '請輸入姓氏英文拼音'
    require-account: '請輸入帳號'
    require-password: '請輸入密碼'
    require-password-confirm: '請再輸入一次密碼'
    password-same: '密碼不相同'
</i18n>