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
  const consentConfig: any = ref({
    useForm: false,
    formData: [
      '<div v-for="i in list_len" class="page nondisplay consent_form">\
            <div style="height: 1cm"></div>\
            <div style="font-size: 36pt; text-align: center;">家長通知書</div>\
            <div style="height: 2cm"></div>\
            <div style="font-size: 18pt">　　本校於&nbsp;2023年03月30日至2023年03月31日&nbsp;(共 2 天)&nbsp;舉行&nbsp;111學年度運動會田徑錦標賽，貴子弟身體若有安全顧慮 (如心臟病、癲癇、氣喘、感冒等)，請敦促其勿勉強參賽。\
            </div>\
            <div style="height: 2cm"></div>\
            <div style="font-size: 12pt; text-align: right;">國立臺灣師範大學附屬高級中學</div>\
            <div style="font-size: 16pt; text-align: right;">\
                <span style="font-size: 14pt;">學生事務處</span>　體育運動組　敬啟\
            </div>\
            <div style="height: 1cm"></div>\
            <div style="font-size: 12pt; text-align: center ">\
                ---------------------------------------------------請沿虛線撕下---------------------------------------------------\
            </div>\
            <div style="height: 1cm"></div>\
            <div style="font-size: 36pt; text-align: center;">家長同意書</div>\
            <div style="height: 1cm"></div>\
            <div style="font-size: 18pt">　　本人同意敝子弟 {{dept_name_ch}} 班 {{num_in_dept}} 號 {{last_name_ch}}{{first_name_ch}} 報名參加學校於&nbsp;2023年03月30日至2023年03月31日&nbsp;(共 2 天)&nbsp;舉行&nbsp;111學年度運動會田徑錦標賽</div>\
            <div style="height: 1cm"></div>\
            <div style="font-size: 18pt">　　此致</div>\
            <div style="height: 1cm"></div>\
            <div style="font-size: 18pt">導師</div>\
            <div style="height: 1cm"></div>\
            <div style="font-size: 16pt; text-align: right;">學生家長：　　　　　　　(簽章)</div>\
            <div style="height: 0.5cm"></div>\
            <div style="font-size: 18pt; text-align: center;"><strong>[未按時繳交視同未報名不得參賽]</strong></div>\
        </div>\
        '
    ],
  });
  const consentList: any = ref(null);
  async function getDataList() {
    isLoading.value = true;
    await vr.Get(`${route.params.adminOrgId}/game/${route.params.gameId}`, gameData);
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/user`, individualList, true, true);
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/user`, groupList, true, true);
    await vr.Get(`reg-config-game/${route.params.gameId}`, regConfig);
    await vr.Get(`auth/user/info`, userData, true, true);
    await vr.Get(`consent-form-game/${route.params.gameId}`, consentList, true, true);
    console.log(consentList.value);
    document.title = gameData.value.game_name_ch + '同意書';
    (async () => {
      for (let i = 0; i < groupList.value.length; i++) {
        groupList.value[i].members = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/team/${groupList.value[i].team_id}`, null, true, true);
      }
    })();
    isLoading.value = false;
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