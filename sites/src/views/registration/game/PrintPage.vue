<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallModal from '@/components/SmallModal.vue';
  import { useI18n } from 'vue-i18n';
  import SelectAthlete from '@/components/registration/settings/SelectAthlete.vue';

  const displayModal = ref(false);

  const { t, locale } = useI18n({
    inheritLocale: true,
    useScope: 'local'
  });
</script>

<template>
  <div class="flex flex-col gap-5">
    <div class="section-box flex flex-col gap-2">
      <div class="text-2xl font-medium">{{ t('reg-form') }}</div>
      <hr>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <router-link :to="`/${$route.params.adminOrgId}/registration/game/${$route.params.sportCode}/${$route.params.gameId}/print/reg-data`" target="_blank"><button class="round-full-button blue">{{ t('print') }}</button></router-link>
      </div>
    </div>
    <div class="section-box flex flex-col gap-2">
      <div class="text-2xl font-medium">{{ t('consent-form') }}</div>
      <hr>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <button class="round-full-button blue" @click="displayModal = true">{{ t('select-athlete') }}</button>
        <router-link :to="`/${$route.params.adminOrgId}/registration/game/${$route.params.sportCode}/${$route.params.gameId}/print/consent-data`" target="_blank"><button class="round-full-button blue">{{ t('print') }}</button></router-link>
      </div>
    </div>
    <SmallModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div>{{ t('athlete-list') }}</div>
        </div>
      </template>
      <template v-slot:content>
        <SelectAthlete v-if="displayModal" @closeModal="displayModal = false"></SelectAthlete>
      </template>
    </SmallModal>
  </div>
</template>

<style scoped lang="scss">
    
</style>

<i18n lang="yaml">
  en-US:
    select-athlete: 'Select Athletes'
    reg-form: 'Registration Form'
    consent-form: 'Consent Form'
    print: 'Print'
    athlete-list: 'Athlete List'
  zh-TW:
    select-athlete: '勾選選手'
    reg-form: '報名表'
    consent-form: '同意書'
    print: '列印'
    athlete-list: '選手列表'
</i18n>