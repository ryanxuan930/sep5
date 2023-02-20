<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import FullModal from '@/components/FullModal.vue';
  import RegistrationConfig from '@/components/admin/game/registration/RegistrationConfig.vue';
  import RegistrationForm from '@/components/admin/game/registration/RegistrationForm.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const gameData: any = ref(null);
  const route = useRoute();
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;

  async function getGameData() {
    vr.Get(`${store.userInfo.org_id}/game/${gameId}`, gameData, true, true);
  };
  getGameData();

</script>

<template>
  <div>
    <div class="section-box grid grid-cols-1 md:grid-cols-4 gap-x-3 gap-y-4">
      <div class="col-span-4 text-2xl">報名系統</div>
      <hr class="col-span-4">
      <button class="round-full-button blue" @click="displayModal = 1">報名系統設定</button>
      <button class="round-full-button blue">依項目別統計</button>
      <button class="round-full-button blue">個人項目統計</button>
      <button class="round-full-button blue">團體項目項目</button>
      <button class="round-full-button blue">參賽選手名單</button>
      <button class="round-full-button blue">報名資料下載</button>
      <button class="round-full-button blue" @click="displayModal = 7">紙本報名管理</button>
      <button class="round-full-button blue" @click="displayModal = 8">其他表件管理</button>
    </div>
  </div>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal == 1">報名系統設定</div>
          <div v-if="displayModal == 7">紙本報名管理</div>
          <div v-if="displayModal == 8">其他表件管理</div>
        </div>
      </template>
      <template v-slot:content>
        <div class="overflow-auto h-full">
          <RegistrationConfig v-if="displayModal == 1" @closeModal="displayModal = 0"></RegistrationConfig>
          <RegistrationForm v-if="displayModal == 7" :input-data="gameData" @closeModal="displayModal = 0"></RegistrationForm>
        </div>
      </template>
    </FullModal>
</template>

<style scoped lang="scss">
    
</style>