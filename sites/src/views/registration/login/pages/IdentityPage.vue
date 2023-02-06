<script setup lang="ts">import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div class="h-screen flex flex-col overflow-auto">
    <div class="flex-grow"></div>
    <div class="bg-white sm:shadow p-5 w-full h-screen sm:w-2/3 md:w-96 sm:h-fit mx-auto flex flex-col gap-5">
      <div>
        <router-link :to="`/${$route.params.adminOrgId}/registration/login`" class="hyperlink blue">{{ t('back') }}</router-link>
      </div>
      <hr>
      <div>
        <div class="text-center text-2xl font-medium mb-2">{{ t('line1') }}</div>
        <div class="mb-3 text-xl text-center">{{ t('line2') }}</div>
        <a href="https://sports.nsysu.edu.tw/monkeyid/#/login" target="_blank">
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
    line2: '請以MonkeyID驗證身份後登入'
    line3: '非上述身份'
    line4: '請直接註冊'
    monkeyid: '註冊 MonkeyID'
    signup: '註冊'
</i18n>