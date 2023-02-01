<script setup lang="ts">
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import UserTable from '@/components/admin/account/UserTable.vue';
  import OrgTable from '@/components/admin/account/OrgTable.vue';
  import DeptTable from '@/components/admin/account/DeptTable.vue';

  const store = useUserStore();
  const router = useRouter();
  if (store.userInfo.permission == 0) {
    router.push('/admin');
  }
  const vr = new VueRequest(store.token);
  const currentPage = ref(0);
</script>

<template>
  <div class="main-box flex flex-col overflow-y-hidden">
    <div class="text-3xl font-medium pb-5">帳號管理</div>
    <div class="bookmark">
      <div :class="{'active': currentPage == 0}" @click="currentPage = 0" v-if="store.userInfo.permission >= 1">使用者帳號</div>
      <div :class="{'active': currentPage == 1}" @click="currentPage = 1" v-if="store.userInfo.permission >= 2">使用者組織列表</div>
      <div :class="{'active': currentPage == 2}" @click="currentPage = 2" v-if="store.userInfo.permission >= 1">使用者分部列表</div>
      <div :class="{'active': currentPage == 3}" @click="currentPage = 3" v-if="store.userInfo.permission >= 1">管理員帳號</div>
      <div :class="{'active': currentPage == 4}" @click="currentPage = 4" v-if="store.userInfo.permission >= 8">管理員組織列表</div>
      <div :class="{'active': currentPage == 5}" @click="currentPage = 5" v-if="store.userInfo.permission >= 2">管理員分部列表</div>
    </div>
    <div class="flex-grow overflow-auto">
      <UserTable v-if="currentPage == 0"></UserTable>
      <OrgTable v-if="currentPage == 1"></OrgTable>
      <DeptTable v-if="currentPage == 2"></DeptTable>
    </div>
  </div>
</template>

<style scoped lang="scss">
.bookmark {
  @apply flex flex-shrink-0 flex-nowrap flex-row overflow-scroll justify-between items-center gap-[1px];
  div {
    @apply py-1 px-2 w-full whitespace-nowrap text-center bg-blue-400 text-white cursor-pointer hover:bg-blue-500 duration-200 rounded-t shadow box-border;
    &.active {
      @apply bg-blue-500;
    }
  }
}
</style>