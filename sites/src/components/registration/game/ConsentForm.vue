<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import Config from '@/assets/config.json';
  import { TemplateParser } from '@/components/library/functions';

  const store = useUserStore();
  const route = useRoute();
  const vr = new VueRequest(store.token);
  const isLoading = ref(false);
  const userData: any = ref([]);
  const regConfig: any = ref([]);
  const gameData: any = ref([]);
  const individualList: any = ref([]);
  const groupList: any = ref([]);
  const consentConfig: any = ref({});
  const consentList: any = ref(null);
  async function getDataList() {
    isLoading.value = true;
    await vr.Get(`${route.params.adminOrgId}/game/${route.params.gameId}`, gameData);
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/user`, individualList, true, true);
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/user`, groupList, true, true);
    await vr.Get(`reg-config-game/${route.params.gameId}`, regConfig);
    await vr.Get(`auth/user/info`, userData, true, true);
    await vr.Get(`consent-form-game/${route.params.gameId}`, consentList, true, true);
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/temp/consentForm`, consentConfig);
    consentConfig.value = JSON.parse(consentConfig.value.temp_data);
    document.title = gameData.value.game_name_ch + '同意書';
    (async () => {
      for (let i = 0; i < groupList.value.length; i++) {
        groupList.value[i].members = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/team/${groupList.value[i].team_id}`, null, true, true);
      }
    })();
    isLoading.value = false;
    setTimeout(() => {
      window.print();
    }, 1000);
  }
getDataList();
</script>

<template>
  <div id="print-root" v-if="!isLoading">
    <template v-for="(item, index) in consentList" :key="index">
      <template v-if="(gameData.options.regUnit == 2 && item.org_code == store.userInfo.org_code) || (gameData.options.regUnit == 1 && item.dept_id == store.userInfo.dept_id) || (gameData.options.regUnit == 0 && item.u_id == store.userInfo.u_id)">
        <template v-for="template in consentConfig.formData">
          <div class="page" v-html="TemplateParser(template, item)"></div>
        </template>
      </template>
    </template>
  </div>
</template>

<style scoped lang="scss">
#print-root {
  background-color: white !important;
  min-height: 100vh;
}
.page {
  @apply p-[1cm];
  page-break-after: always;
  min-height: 100vh;
}
.font20 {
  font-size: 20pt;
}
.font16 {
  font-size: 16pt;
}
.font14 {
  font-size: 14pt;
}
.font12 {
  font-size: 12pt;
}
.font10 {
  font-size: 10pt;
}
.font9 {
  font-size: 9pt;
}
table {
  @apply border-collapse w-full;
}
.table1 {
  td {
    @apply border-[1px] border-gray-400 py-1 px-2 w-1/6 break-words;
  }
}
.table2 {
  th, td {
    @apply border-[1px] border-gray-400 py-1 px-2 text-left;
  }
}
.table3 {
  th, td {
    @apply py-1 px-2 text-left w-1/3;
  }
}
@page {
  margin: 0;
  border: 0;
  background-color: white !important;
  -webkit-print-color-adjust:exact !important;
  print-color-adjust:exact !important;
}
</style>