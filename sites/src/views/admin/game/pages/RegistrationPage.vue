<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import FullModal from '@/components/FullModal.vue';
  import RegistrationConfig from '@/components/admin/game/registration/RegistrationConfig.vue';
  import RegistrationForm from '@/components/admin/game/registration/RegistrationForm.vue';
  import ConsentForm from '@/components/admin/game/registration/ConsentForm.vue';
  import EventCount from '@/components/admin/game/registration/EventCount.vue';
  import IndividualData from '@/components/admin/game/registration/IndividualData.vue';
  import GroupData from '@/components/admin/game/registration/GroupData.vue';
  import AthleteData from '@/components/admin/game/registration/AthleteData.vue';
  import DownloadData from '@/components/admin/game/registration/DownloadData.vue';

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
      <button class="round-full-button blue" @click="displayModal = 2">依項目別統計</button>
      <button class="round-full-button blue" @click="displayModal = 3">個人報名資訊</button>
      <button class="round-full-button blue" @click="displayModal = 4">團體報名資訊</button>
      <button class="round-full-button blue" @click="displayModal = 5">參賽選手名單</button>
      <button class="round-full-button blue" @click="displayModal = 6">報名資料下載</button>
      <button class="round-full-button blue" @click="displayModal = 7">紙本報名管理</button>
      <button class="round-full-button blue" @click="displayModal = 8">其他表件管理</button>
    </div>
  </div>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal == 1">報名系統設定</div>
          <div v-if="displayModal == 2">依項目別統計</div>
          <div v-if="displayModal == 3">個人報名資訊</div>
          <div v-if="displayModal == 4">團體報名資訊</div>
          <div v-if="displayModal == 5">參賽選手名單</div>
          <div v-if="displayModal == 6">報名資料下載</div>
          <div v-if="displayModal == 7">紙本報名管理</div>
          <div v-if="displayModal == 8">其他表件管理</div>
        </div>
      </template>
      <template v-slot:content>
        <div class="overflow-auto h-full">
          <RegistrationConfig v-if="displayModal == 1" @closeModal="displayModal = 0"></RegistrationConfig>
          <EventCount v-if="displayModal == 2" :input-data="gameData" @closeModal="displayModal = 0"></EventCount>
          <IndividualData v-if="displayModal == 3" :input-data="gameData" @closeModal="displayModal = 0"></IndividualData>
          <GroupData v-if="displayModal == 4" :input-data="gameData" @closeModal="displayModal = 0"></GroupData>
          <AthleteData v-if="displayModal == 5" :input-data="gameData" @closeModal="displayModal = 0"></AthleteData>
          <DownloadData v-if="displayModal == 6" :input-data="gameData" @closeModal="displayModal = 0"></DownloadData>
          <RegistrationForm v-if="displayModal == 7" :input-data="gameData" @closeModal="displayModal = 0"></RegistrationForm>
          <ConsentForm v-if="displayModal == 8" :input-data="gameData" @closeModal="displayModal = 0"></ConsentForm>
        </div>
      </template>
    </FullModal>
</template>

<style scoped lang="scss">

</style>