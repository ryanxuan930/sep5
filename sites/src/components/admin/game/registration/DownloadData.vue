<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import { exportCsv } from '@/components/library/functions';

  const store = useUserStore();
  const route = useRoute();
  const vr = new VueRequest(store.token);
  const props = defineProps(['inputData']);
  async function downloadAthlete() {
    const temp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/athlete/list`, null, true, true);
    const header = ['選手編號','組織單位/學校', '分部/系所/班級', '姓名', '性別', '生日', '身分證字號', '學號/教職員編號', '座號', '體優生', '校隊選手'];
    const data: any = [];
    for (const t of temp) {
      data.push([t.athlete_id, t.org_name_full_ch, t.dept_name_ch, t.last_name_ch+t.first_name_ch, t.sex == 1 ? '男':'女', t.birthday, t.unified_id, t.student_id, t.num_in_dept, t.is_sport_gifited == 1 ? '是':'否', t.is_school_team == 1 ? '是':'否'])
    }
    exportCsv(data, `${props.inputData.game_name_ch}_選手列表`, header);
  }
  async function downloadIndividual() {
    const temp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual`, null, true, true);
    const header = ['組別', '項目','組織單位/學校', '分部/系所/班級', '姓名', '性別', '選手代碼', '學號/教職員編號', '座號'];
    const data: any = [];
    for (const t of temp) {
      data.push([t.division_ch, t.event_ch, t.org_name_full_ch, t.dept_name_ch, t.last_name_ch+t.first_name_ch, t.sex == 1 ? '男':'女', t.athlete_id, t.student_id, t.num_in_dept])
    }
    exportCsv(data, `${props.inputData.game_name_ch}_個人項目列表`, header);
  }
  async function downloadGroup() {
    const temp = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group`, null, true, true);
    const header = ['組別', '項目','組織單位/學校', '分部/系所/班級', '隊名'];
    const data: any = [];
    for (const t of temp) {
      data.push([t.division_ch, t.event_ch, t.org_name_full_ch, t.dept_name_ch, t.team_name])
    }
    exportCsv(data, `${props.inputData.game_name_ch}_團體項目列表`, header);
  }
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-x-3 gap-y-4">
    <button class="round-full-button blue" @click="downloadAthlete">選手列表下載</button>
    <button class="round-full-button blue" @click="downloadIndividual">個人報名下載</button>
    <button class="round-full-button blue" @click="downloadGroup">團體報名下載</button>
  </div>
</template>

<style scoped lang="scss">
    
</style>