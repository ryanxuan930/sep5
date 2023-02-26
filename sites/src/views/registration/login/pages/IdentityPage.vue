<script setup lang="ts">import { useI18n } from 'vue-i18n';
import SmallModal from '@/components/SmallModal.vue';

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div class="h-screen-phone md:h-screen flex flex-col">
    <div class="flex-grow"></div>
    <div class="bg-white sm:shadow p-5 w-full h-full md:h-auto sm:w-2/3 md:w-96 mx-auto flex flex-col gap-5">
      <div>
        <router-link :to="`/${$route.params.adminOrgId}/registration/login`" class="hyperlink blue">{{ t('back') }}</router-link>
      </div>
      <hr>
      <div>
        <div class="text-center text-2xl font-medium mb-2">{{ t('line1') }}</div>
        <div class="mb-3 text-xl text-center relative">
          {{ t('line2') }}
          <span class="-top-2 p-1 absolute">
            <span class="material-icons text-base text-gray-300 hover:text-gray-400 duration-150 cursor-pointer">help</span>
          </span>
          
        </div>
        <a href="https://sports.nsysu.edu.tw/monkeyid/#/login/signup" target="_blank">
          <button class="round-full-button blue">{{ t('monkeyid') }}</button>
        </a>
      </div>
      <hr>
      <div>
        <div class="text-center text-2xl font-medium mb-2">{{ t('line3') }}</div>
        <div class="mb-3 text-xl text-center">{{ t('line4') }}</div>
        <button class="round-full-button blue" @click="$router.push(`/${$route.params.adminOrgId}/registration/login/signup`)">{{ t('signup') }}</button>
      </div>
    </div>
    <div class="flex-grow"></div>
  </div>
</template>

<i18n lang="yaml">
  en-US:
    back: 'Back'
    line1: 'Students / Faculties / Alumni'
    line2: 'Please verify your identity in MonkeyID'
    line3: 'Not mentioned above'
    line4: 'Sign up now'
    monkeyid: 'Sign up with MonkeyID'
    signup: 'Sign up'
  zh-TW:
    back: '回上頁'
    line1: '學生、校友、教職員'
    line2: '請先到MonkeyID註冊帳號'
    line3: '非上述身份'
    line4: '請直接註冊'
    monkeyid: '註冊 MonkeyID'
    signup: '註冊'
    info: 'MonkeyID是用來進行學生、校友、教職員的身份驗證系統，經過驗證後才能取得上述身份別。可以用學校發配的信箱或學生證、教職員證與校友證進行驗證。'
</i18n>