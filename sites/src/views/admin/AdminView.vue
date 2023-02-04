<script setup lang="ts">
  import { useUserStore } from '@/stores/user';
  import { useRouter } from 'vue-router';

  const router = useRouter();
  const store = useUserStore();
  try {
    if (localStorage.getItem('sep5AdminTemp')) {
      const temp = JSON.parse(decodeURI(window.atob(localStorage.sep5AdminTemp)));
      store.expired = temp.expired;
      store.token = temp.token;
      store.userInfo = temp.userInfo;
      store.loginType = temp.loginType;
    }
  } catch (e) {
    console.log(e);
  }

  function logout() {
    store.reset();
    try {
      if (localStorage.getItem('sep5AdminTemp')) {
        localStorage.removeItem('sep5AdminTemp');
      }
    } catch (e) {
      console.log(e);
    }
    router.push('/admin/login');
  }
  function checkLogin() {
    if (store.expired !== '') {
      const expire = new Date(store.expired).getTime();
      const current = Date.now();
      if (expire <= current) {
        alert('已過期，請重新登入');
        logout();
      }
    } else if (store.expired === '') {
      alert('請先登入');
      logout();
    }
  }
  checkLogin();
</script>

<template>
  <router-view></router-view>
</template>
