<script setup lang="ts">
  import { ref, provide } from 'vue';
  import { useUserStore } from '@/stores/user';
  import { useGameStore } from '@/stores/game';
  import FullModal from '@/components/FullModal.vue';
  import EditPhase from '@/components/admin/game/schedule/EditPhase.vue';
  import EditQualify from '@/components/admin/game/schedule/EditQualify.vue';
  import EditArrange from '@/components/admin/game/schedule/EditArrange.vue';
  import EditTimetable from '@/components/admin/game/schedule/EditTimetable.vue';
  import DownloadOptions from '@/components/admin/game/schedule/DownloadOptions.vue';

  const store = useUserStore();
  const gameStore = useGameStore();
  const displayModal = ref(0);
  const gameData: any = ref(gameStore.data);
  provide('gameData', gameData);
</script>

<template>
  <div v-if="gameData != null" class="flex flex-col gap-5">
    <div class="section-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-3 gap-y-4">
      <div class="md:col-span-2 lg:col-span-4 text-2xl">選手編配</div>
      <hr class="md:col-span-2 lg:col-span-4">
      <button class="round-full-button blue" @click="displayModal = 1">賽別賽制設定</button>
      <button v-if="gameData.module == 'ln' || gameData.module == 'rd'" class="round-full-button blue" @click="displayModal = 2">名次取數設定</button>
      <button v-if="gameData.module == 'ln'" class="round-full-button blue" @click="displayModal = 3">組別道次管理</button>
      <button v-if="gameData.module == 'ln' || gameData.module == 'rd'" class="round-full-button blue" @click="displayModal = 6">號碼布管理</button>
    </div>
    <div class="section-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-3 gap-y-4">
      <div class="md:col-span-2 lg:col-span-4 text-2xl">賽程管理</div>
      <hr class="md:col-span-2 lg:col-span-4">
      <button class="round-full-button blue" @click="displayModal = 4">賽程編排</button>
      <button class="round-full-button blue" @click="displayModal = 5">秩序冊資料</button>
    </div>
  </div>
  <FullModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">賽別賽制設定</div>
        <div v-if="displayModal == 2">名次取數設定</div>
        <div v-if="displayModal == 3">組別道次管理</div>
        <div v-if="displayModal == 4">賽程編排</div>
        <div v-if="displayModal == 5">秩序冊資料</div>
        <div v-if="displayModal == 6">號碼布管理</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <EditPhase v-if="displayModal == 1"></EditPhase>
        <EditQualify v-if="displayModal == 2"></EditQualify>
        <EditArrange v-if="displayModal == 3"></EditArrange>
        <EditTimetable v-if="displayModal == 4"></EditTimetable>
        <DownloadOptions v-if="displayModal == 5"></DownloadOptions>
      </div>
    </template>
  </FullModal>
</template>

<style scoped lang="scss">
    
</style>