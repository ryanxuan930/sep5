<script setup lang="ts">
  import { reactive, ref } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';
  const accountInput: any = ref(null);
  const passwordInput: any = ref(null);
  const message: Ref<string|null> = ref(null);
  const router = useRouter();
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
        router.push('/registration');
      } else if (res.message !== undefined) {
        message.value = res.message;
      }
    });
  }
</script>

<template>
  <div class="h-screen flex flex-col overflow-auto">
    <div class="flex-grow"></div>
    <div class="bg-white sm:shadow p-5 w-full h-screen sm:w-2/3 md:w-96 sm:h-fit mx-auto">
      <div class="text-2xl text-gray-500">使用者登入</div>
      <label class="round-input-label">
        <div class="title">帳號</div>
        <input type="email" class="input" ref="accountInput" placeholder="abc@xyz.com" v-model="data.account" @keyup.enter="passwordInput.focus()">
      </label>
      <label class="round-input-label">
        <div class="title">密碼</div>
        <input type="password" class="input" ref="passwordInput" placeholder="・・・・・・・・" v-model="data.password" @keyup.enter="submitAll">
      </label>
      <button class="round-full-button blue mt-6 mb-3" @click="submitAll">登入</button>
    </div>
    <div class="flex-grow"></div>
  </div>
</template>

<style scoped lang="scss">
.round-input-label {
  @apply my-3;
}
</style>