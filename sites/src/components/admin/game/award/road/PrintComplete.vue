<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useGameStore } from '@/stores/game';
  import { useRoute } from 'vue-router';
  import { TemplateParser } from '@/components/library/functions';

  const store = useUserStore();
  const route = useRoute();
  const vr = new VueRequest(store.token);
  const gameStore = useGameStore();
  const divisionId = Number(route.params.divisionId);
  const eventCode = route.params.eventCode;
  const printFormat = Number(route.params.printFormat);
  const isLoading = ref(false);
  const awardFormat: any = ref({});
  const dataList: any = ref([]);
  const paramList: any = ref(null);
  
  async function getDataList() {
    isLoading.value = true;
    let temp: any = [];
    temp = await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/individual/by/event/${divisionId}/${eventCode}`);
    await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/main/params/${divisionId}/${eventCode}`, paramList);
    for (let i = 0; i < temp.length; i++) {
      if (temp[i].ranking > 0) {
        temp[i].result = temp[i].result;
        temp[i].ranking = temp[i].ranking;
        dataList.value.push(temp[i]);
      }
    }
    dataList.value.sort((a: any, b: any) => a.dept_id - b.dept_id || a.bib - b.bib);
    await vr.Get(`game/${gameStore.data.sport_code}/${gameStore.data.game_id}/common/temp/awardFormat`, awardFormat);
    awardFormat.value = JSON.parse(awardFormat.value.temp_data);
    document.title = gameStore.data.game_name_ch + '獎狀';
    isLoading.value = false;
    setTimeout(() => {
      // window.print();
    }, 1000);
  }
getDataList();
</script>

<template>
  <div id="print-root" v-if="!isLoading">
    <template v-for="(item, index) in dataList" :key="index">
      <div class="page" v-html="TemplateParser(awardFormat[printFormat].format, item)"></div>
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