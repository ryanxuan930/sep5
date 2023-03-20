<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useGameStore } from '@/stores/game';
  import FullModal from '@/components/FullModal.vue';
  import EditGame from '@/components/admin/game/EditGame.vue';
  import DateSetter from '@/components/admin/game/main/DateSetter.vue';
  import DivisionSetter from '@/components/admin/game/main/DivisionSetter.vue';
  import EventSetter from '@/components/admin/game/main/EventSetter.vue';
  import ParamsSetter from '@/components/admin/game/main/ParamsSetter.vue';
  import LaneSetter from '@/components/admin/game/main/LaneSetter.vue';
  import ChampionSetter from '@/components/admin/game/main/ChampionSetter.vue';
  import RecordSetter from '@/components/admin/game/main/RecordSetter.vue';

  const store = useUserStore();
  const gameStore = useGameStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const gameData: any = ref(gameStore.data);
</script>

<template>
  <div v-if="gameData != null">
    <div class="section-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-3 gap-y-4">
      <div class="md:col-span-2 lg:col-span-4 text-2xl">競賽基本設定</div>
      <hr class="md:col-span-2 lg:col-span-4">
      <button class="round-full-button blue" @click="displayModal = 1">競賽資訊管理</button>
      <button class="round-full-button blue" @click="displayModal = 2">競賽日程管理</button>
      <button class="round-full-button blue" @click="displayModal = 3">競賽分組管理</button>
      <button class="round-full-button blue" @click="displayModal = 4">競賽項目管理</button>
      <button class="round-full-button blue" @click="displayModal = 5">分組項目設定</button>
      <button class="round-full-button blue" @click="displayModal = 6">大會紀錄管理</button>
      <button class="round-full-button blue" @click="displayModal = 7">錦標積分設定</button>
      <button class="round-full-button blue" @click="displayModal = 8" v-if="gameData.module == 'ln'">賽道編排設定</button>
      <button class="round-full-button blue" @click="displayModal = 9" v-if="gameData.sport_code == 'athl'">全能項目設定</button>
    </div>
  </div>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal == 1">競賽資訊管理</div>
          <div v-if="displayModal == 2">競賽日程管理</div>
          <div v-if="displayModal == 3">競賽分組管理</div>
          <div v-if="displayModal == 4">競賽項目管理</div>
          <div v-if="displayModal == 5">分組項目設定</div>
          <div v-if="displayModal == 6">大會紀錄管理</div>
          <div v-if="displayModal == 7">錦標積分設定</div>
          <div v-if="displayModal == 8">賽道編排設定</div>
          <div v-if="displayModal == 9">全能項目設定</div>
        </div>
      </template>
      <template v-slot:content>
        <div class="overflow-auto h-full">
          <EditGame v-if="displayModal == 1" :game-data="gameData" @closeModal="displayModal = 0" @refresh-page="gameStore.fetch(store.userInfo.org_id, Number($route.params.gameId))"></EditGame>
          <DateSetter v-if="displayModal == 2" :input-data="gameData" @closeModal="displayModal = 0"></DateSetter>
          <DivisionSetter v-if="displayModal == 3" :input-data="gameData" @closeModal="displayModal = 0"></DivisionSetter>
          <EventSetter v-if="displayModal == 4" :input-data="gameData" @closeModal="displayModal = 0"></EventSetter>
          <ParamsSetter v-if="displayModal == 5" :input-data="gameData" @closeModal="displayModal = 0"></ParamsSetter>
          <RecordSetter v-if="displayModal == 6" :input-data="gameData" @closeModal="displayModal = 0"></RecordSetter>
          <ChampionSetter v-if="displayModal == 7" :input-data="gameData" @closeModal="displayModal = 0"></ChampionSetter>
          <LaneSetter v-if="displayModal == 8" :input-data="gameData" @closeModal="displayModal = 0"></LaneSetter>
        </div>
      </template>
    </FullModal>
</template>

<style scoped lang="scss">
    
</style>