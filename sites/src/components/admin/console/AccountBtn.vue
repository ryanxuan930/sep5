<script setup lang="ts">
  import { reactive, ref } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useAdminStore } from '@/stores/admin.js';
  import type { Ref } from 'vue';
  const router = useRouter();
  const store = useAdminStore()
  const vr = new VueRequest(store.token);
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
</script>

<template>
  <div class="nav-link">
    <div>設定</div>
  </div>
  <div class="nav-link" @click="logout">
    <div>登出</div>
  </div>
</template>