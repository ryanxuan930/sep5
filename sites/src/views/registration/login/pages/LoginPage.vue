<script setup lang="ts">
  import { reactive, ref } from 'vue';
  import { useRouter, useRoute } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';
  import { useI18n } from 'vue-i18n';
  const accountInput: any = ref(null);
  const passwordInput: any = ref(null);
  const status: Ref<string|null> = ref(null);
  const router = useRouter();
  const route = useRoute();
  const store = useUserStore();
  const vr = new VueRequest();
  const data = reactive({
    account: '',
    password: '',
  });
  interface ILoginResponse {
    status: string;
    data: {
      expired: string,
      token: string,
      type: string,
      user: any;
    };
    message?: string;
  }
  function submitAll() {
    vr.Post('auth/user/login', data).then((res: ILoginResponse) => {
      if (res.status === 'A02') {
        store.expired = res.data.expired;
        store.token = res.data.token;
        store.userInfo = res.data.user;
        store.loginType = 'user';
        const temp = {
          expired: res.data.expired,
          token: res.data.token,
          userInfo: res.data.user,
          loginType: 'user',
        };
        localStorage.setItem('sep5UserTemp', window.btoa(encodeURI(JSON.stringify(temp))));
        router.push(`/${route.params.adminOrgId}/registration`);
      } else if (res.status !== undefined) {
        status.value = res.status;
      }
    });
  }
  const { t, locale } = useI18n({
    inheritLocale: true,
    useScope: 'local'
  });
  // configs
  const useMonkeyId = true;
</script>

<template>
  <div class="h-screen flex flex-col overflow-auto">
    <div class="flex-grow"></div>
    <div class="bg-white sm:shadow p-5 w-full h-screen sm:w-2/3 md:w-96 sm:h-fit mx-auto">
      <div class="text-2xl text-gray-500">{{ t('registration-login') }}</div>
      <div class="mt-3 text-lg">
        <a class="inline-block hyperlink blue" @click="$i18n.locale = 'zh-TW'">中文</a>
        <span class="mx-3">|</span>
        <a class="inline-block hyperlink blue" @click="$i18n.locale = 'en-US'">English</a>
      </div>
      <label class="round-input-label">
        <div class="title">{{ t('account') }}</div>
        <input type="email" class="input" ref="accountInput" placeholder="abc@xyz.com" v-model="data.account" @keyup.enter="passwordInput.focus()">
      </label>
      <label class="round-input-label">
        <div class="title">{{ t('password') }}</div>
        <input type="password" class="input" ref="passwordInput" placeholder="・・・・・・・・" v-model="data.password" @keyup.enter="submitAll">
        <div v-if="status == 'U03'" class="warning">{{ t('require-account') }}</div>
        <div v-if="status == 'U06'" class="warning">{{ t('require-password') }}</div>
        <div v-if="status == 'U02'" class="warning">{{ t('not-exist') }}</div>
        <div v-if="status == 'U05'" class="warning">{{ t('wrong-message') }}</div>
      </label>
      <router-link to="" class="hyperlink blue">{{ t('forget-password') }}</router-link>
      <button class="round-full-button blue mt-5" @click="submitAll">{{ t('login') }}</button>
      <hr class="my-5">
      <div class="mb-5 text-center">{{ t('no-account') }}</div>
      <button class="round-full-button blue" @click="$router.push(useMonkeyId ? `/${$route.params.adminOrgId}/registration/login/signup-identity` : `/${$route.params.adminOrgId}/registration/login/signup`)">{{ t('signup') }}</button>
    </div>
    <div class="flex-grow"></div>
  </div>
</template>

<style scoped lang="scss">
.round-input-label {
  @apply my-3;
}
.warning {
  @apply text-red-600 text-sm my-1;
}
</style>

<i18n lang="yaml">
  en-US:
    registration-login: 'Registration System Login'
    login: 'Login'
    account: 'Account'
    password: 'Password'
    not-exist: 'Account has not been registered.'
    wrong-message: 'Wrong account or password.'
    forget-password: 'Forget password'
    signup: 'Sign up'
    no-account: "Don't have an account?"
    require-account: 'Please input your account'
    require-password: 'Please input your password'
  zh-TW:
    registration-login: '報名系統登入'
    login: '登入'
    account: '帳號'
    password: '密碼'
    not-exist: '帳號尚未註冊。'
    wrong-message: '帳號或密碼錯誤。'
    forget-password: '忘記密碼'
    signup: '註冊'
    no-account: '還沒有帳號嗎？'
    require-accout: '請輸入帳號'
    require-password: '請輸入密碼'
</i18n>