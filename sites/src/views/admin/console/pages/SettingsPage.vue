<script setup lang="ts">
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import FullModal from '@/components/FullModal.vue';
  import EditHomepage from '@/components/admin/settings/EditHomepage.vue';
  import EventList from '@/components/admin/settings/EventList.vue';

  const store = useUserStore();
  const router = useRouter();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);

</script>

<template>
  <div>
    <div class="section-box flex flex-col gap-5">
      <div class="flex-grow text-3xl font-medium">系統管理</div>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <button class="round-full-button blue" @click="displayModal = 1" v-if="store.userInfo.permission > 2">頁面設定</button>
        <button class="round-full-button blue" @click="displayModal = 2">項目設定</button>
      </div>
    </div>
    <FullModal v-show="displayModal" @closeModal="displayModal = 0">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal == 1">頁面設定</div>
          <div v-if="displayModal == 2">項目設定</div>
        </div>
      </template>
      <template v-slot:content>
        <EditHomepage v-if="displayModal == 1" @closeModal="displayModal = 0"></EditHomepage>
        <EventList v-if="displayModal == 2" @closeModal="displayModal = 0"></EventList>
      </template>
    </FullModal>
  </div>
</template>

<style scoped lang="scss">
    
</style>