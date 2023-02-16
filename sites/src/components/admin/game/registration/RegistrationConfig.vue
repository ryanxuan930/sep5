<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import type { IRegConfig } from '@/components/library/interfaces';
  import Toggle from '@vueform/toggle';
  import '@vueform/toggle/themes/default.css';
  import GradeSelector from '../../module/GradeSelector.vue';
  import SmallModal from '@/components/SmallModal.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);
  const isLoading = ref(false);
  const configData: any = ref(null);
  const route = useRoute();
  const gameId = Number(route.params.gameId);
  const sportCode = route.params.sportCode;
  const configPrototype: IRegConfig = {
    game_id: gameId,
    reg_switch: 1,
    deadline_list: {
      division: [],
      all: {
        start: '2023-01-01T12:00:00',
        end: '2023-01-01T12:00:00',
      },
      seperate: false,
    },
    options: {
      common: {
        min_reg_permission: 0,
        allow_grouping: true,
        allow_cross_dept: true,
        allow_cross_org: true,
        individual: {
          max_event_per_athlete: 1,
          max_athlete_per_event: 1,
        },
        group: {
          max_event_per_team: 1,
          max_team_per_event: 0,
        }
      },
      division: [],
      event: {}
    }
  };
  const gradeList:({grade_id: number, grade: string}[]) = [
    {grade_id: 1, grade: '小一'},
    {grade_id: 2, grade: '小二'},
    {grade_id: 3, grade: '小三'},
    {grade_id: 4, grade: '小四'},
    {grade_id: 5, grade: '小五'},
    {grade_id: 6, grade: '小六'},
    {grade_id: 7, grade: '國一'},
    {grade_id: 8, grade: '國二'},
    {grade_id: 9, grade: '國三'},
    {grade_id: 10, grade: '高一'},
    {grade_id: 11, grade: '高二'},
    {grade_id: 12, grade: '高三'},
    {grade_id: 21, grade: '大一'},
    {grade_id: 22, grade: '大二'},
    {grade_id: 23, grade: '大三'},
    {grade_id: 24, grade: '大四'},
    {grade_id: 25, grade: '大五'},
    {grade_id: 26, grade: '大六'},
    {grade_id: 27, grade: '大七'},
    {grade_id: 28, grade: '大八'},
    {grade_id: 31, grade: '碩一'},
    {grade_id: 32, grade: '碩二'},
    {grade_id: 33, grade: '碩三'},
    {grade_id: 34, grade: '碩四'},
    {grade_id: 41, grade: '博一'},
    {grade_id: 42, grade: '博二'},
    {grade_id: 43, grade: '博三'},
    {grade_id: 44, grade: '博四'},
    {grade_id: 45, grade: '博五'},
    {grade_id: 46, grade: '博六'},
    {grade_id: 47, grade: '博七'},
    {grade_id: 35, grade: '二專一'},
    {grade_id: 36, grade: '二專二'},
    {grade_id: 37, grade: '二技一'},
    {grade_id: 38, grade: '二技二'},
    {grade_id: 61, grade: '四技一'},
    {grade_id: 62, grade: '四技二'},
    {grade_id: 63, grade: '四技三'},
    {grade_id: 64, grade: '四技四'},
    {grade_id: 51, grade: '五專一'},
    {grade_id: 52, grade: '五專二'},
    {grade_id: 53, grade: '五專三'},
    {grade_id: 54, grade: '五專四'},
    {grade_id: 55, grade: '五專五'},
    {grade_id: 71, grade: '七技一'},
    {grade_id: 72, grade: '七技二'},
    {grade_id: 73, grade: '七技三'},
    {grade_id: 74, grade: '七技四'},
    {grade_id: 75, grade: '七技五'},
    {grade_id: 76, grade: '七技六'},
    {grade_id: 77, grade: '七技七'},
    {grade_id: 98, grade: '延畢'},
    {grade_id: 99, grade: '畢業'},
  ];
  
  const divisionList: any = ref(null);
  const eventList: any = ref(null);
  (async () => {
    isLoading.value = true;
    await vr.Get(`game/${sportCode}/${gameId}/main/division`, divisionList, true, true);
    await vr.Get(`game/${sportCode}/${gameId}/main/event/full`, eventList, true, true);
    await vr.Get(`reg-config-game/${gameId}`, configData);
    if (configData.value.reg_switch == undefined) {
      const temp = JSON.parse(JSON.stringify(configPrototype));
      temp.deadline_list = JSON.stringify(temp.deadline_list);
      temp.options = JSON.stringify(temp.options);
      await vr.Post('reg-config', temp, null, true, true);
      await vr.Get(`reg-config-game/${gameId}`, configData);
    }
    for (const division of divisionList.value) {
      let flag = false;
      for (const config of configData.value.options.division) {
        if (config.division_id == division.division_id) {
          flag = true;
          break;
        }
      }
      if (!flag) {
        configData.value.options.division.push(
          {
            division_id: division.division_id,
            prevent_sport_gifited: false,
            student_only: false,
            has_grade: false,
            grade_list: [],
          }
        )
      }
    }
    if (Array.isArray(configData.value.options.event)) {
      configData.value.options.event = {};
    }
    for (const event of eventList.value) {
      if (configData.value.options.event[event.event_code] == undefined) {
        configData.value.options.event[event.event_code] = {
          prevent_sport_gifited: false,
          student_only: false,
          pre_define_member: false,
          has_grade: false,
          grade_list: [],
        }
      }
    }
    divisionList.value.sort((a: any, b: any) => a.division_id - b.division_id);
    configData.value.options.division.sort((a: any, b: any) => a.division_id - b.division_id);
    isLoading.value = false;
  })();
  const opened: any = ref({});
  const openIndex: any = ref(0);
  const targetObject: any = ref(null);
  function open(index: any, input: any, target: any) {
    openIndex.value = index;
    opened.value = input;
    targetObject.value = target;
    displayModal.value = true;
  }

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  function submitAll() {
    const temp = JSON.parse(JSON.stringify(configData.value));
    temp.deadline_list = JSON.stringify(temp.deadline_list);
    temp.options = JSON.stringify(temp.options);
    delete temp.reg_config_id;
    delete temp.created_at;
    delete temp.updated_at;
    vr.Patch(`reg-config/${configData.value.reg_config_id}`, temp, null, true, true).then( (res: any) => {
      if (res.status !== 'A01') {
        alert('無法儲存');
        return;
      }
    });
    close();
  }
</script>

<template>
  <div class="h-full overflow-auto grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2" v-if="isLoading == false">
    <div class="md:col-span-4 text-xl font-medium p-1">共通設定</div>
    <label class="round-input-label">
      <div class="title">賽事報名系統</div>
      <div class="p-1">
        <span class="py-1 px-7 rounded-full bg-green-500 text-white" v-if="configData.reg_switch == 1">開</span>
        <span class="py-1 px-7 rounded-full bg-red-500 text-white" v-if="configData.reg_switch == 0">關</span>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">分階段報名</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="否" onLabel="是" v-model="configData.deadline_list.seperate"></Toggle>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">全部報名開始</div>
      <input type="datetime-local" class="input" step="1" v-model="configData.deadline_list.all.start">
    </label>
    <label class="round-input-label">
      <div class="title">全部報名結束</div>
      <input type="datetime-local" class="input" step="1" v-model="configData.deadline_list.all.end">
    </label>
    <div class="md:col-span-4 text-xl font-medium p-1">分階段報名設定</div>
    <div class="md:col-span-4 text-xl font-medium p-1">共通限制設定</div>
    <label class="round-input-label">
      <div class="title">最低報名權限</div>
      <select class="select" v-model="configData.options.common.min_reg_permission">
        <option value="0">一般使用者</option>
        <option value="1">系所/分部管理員</option>
        <option value="2">組織單位管理員</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">開放自行組隊</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="否" onLabel="是" v-model="configData.options.common.allow_grouping"></Toggle>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">開放跨系所/分部組隊</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="否" onLabel="是" v-model="configData.options.common.allow_cross_dept"></Toggle>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">開放跨組織單位組隊</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="否" onLabel="是" v-model="configData.options.common.allow_cross_org"></Toggle>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">個人-每項目最多幾人報名</div>
      <input type="number" class="input" v-model="configData.options.common.individual.max_event_per_athlete">
    </label>
    <label class="round-input-label">
      <div class="title">個人-每人最多報名幾項目</div>
      <input type="number" class="input" v-model="configData.options.common.individual.max_athlete_per_event">
    </label>
    <label class="round-input-label">
      <div class="title">團體-每項目最多幾隊報名</div>
      <input type="number" class="input" v-model="configData.options.common.group.max_event_per_team">
    </label>
    <label class="round-input-label">
      <div class="title">團體-每隊最多報名幾項目(停用)</div>
      <input type="number" class="input" v-model="configData.options.common.group.max_team_per_event">
    </label>
    <div class="md:col-span-4 text-xl font-medium p-1">組別限制設定</div>
    <div class="md:col-span-4">
      <table>
        <tr>
          <th class="w-[20%]">組別</th>
          <th class="w-[10%] text-center">性別</th>
          <th class="w-[10%] text-center">排除體優</th>
          <th class="w-[10%] text-center">限定學生</th>
          <th class="w-[10%] text-center">年級限制</th>
          <th>年級設定</th>
        </tr>
        <template v-for="(item, index) in divisionList" :key="index">
          <tr>
            <td>{{ item.division_ch }}</td>
            <td class="text-center">
              <span v-if="item.sex == 0">混合</span>
              <span v-if="item.sex == 1">男</span>
              <span v-if="item.sex == 2">女</span>
            </td>
            <td class="text-center">
              <input type="checkbox" v-model="configData.options.division[index].prevent_sport_gifited">
            </td>
            <td class="text-center">
              <input type="checkbox" v-model="configData.options.division[index].student_only">
            </td>
            <td class="text-center">
              <input type="checkbox" v-model="configData.options.division[index].has_grade">
            </td>
            <td class="flex gap-2">
              <button class="general-button blue block flex-shrink-0" :disabled="configData.options.division[index].has_grade==false" @click="open(index, configData.options.division[index].grade_list, configData.options.division)">設定</button>
              <div :class="{'flex gap-3 flex-grow overflow-auto': true, 'disabled': configData.options.division[index].has_grade==false}">
                <template v-for="(grade, id) in gradeList" :key="id">
                  <div class="p-1 flex-shrink-0" v-if="configData.options.division[index].grade_list.includes(grade.grade_id)">{{ grade.grade }}</div>
                </template>
              </div>
            </td>
          </tr>
        </template>
      </table>
    </div>
    <div class="md:col-span-4 text-xl font-medium p-1">項目限制設定</div>
    <div class="md:col-span-4">
      <table>
        <tr>
          <th class="w-[20%]">項目</th>
          <th class="w-[10%] text-center">排除體優</th>
          <th class="w-[10%] text-center">限定學生</th>
          <th class="w-[10%] text-center">年級限制</th>
          <th>年級設定</th>
        </tr>
        <template v-for="(item, index) in eventList" :key="index">
          <tr>
            <td>{{ item.event_ch }}</td>
            <td class="text-center">
              <input type="checkbox" v-model="configData.options.event[item.event_code].prevent_sport_gifited">
            </td>
            <td class="text-center">
              <input type="checkbox" v-model="configData.options.event[item.event_code].student_only">
            </td>
            <td class="text-center">
              <input type="checkbox" v-model="configData.options.event[item.event_code].has_grade">
            </td>
            <td class="flex gap-2">
              <button class="general-button blue block flex-shrink-0" :disabled="configData.options.event[item.event_code].has_grade==false" @click="open(item.event_code, configData.options.event[item.event_code].grade_list, configData.options.event)">設定</button>
              <div :class="{'flex gap-3 flex-grow overflow-auto': true, 'disabled': configData.options.event[item.event_code].has_grade==false}">
                <template v-for="(grade, id) in gradeList" :key="id">
                  <div class="p-1 flex-shrink-0" v-if="configData.options.event[item.event_code].grade_list.includes(grade.grade_id)">{{ grade.grade }}</div>
                </template>
              </div>
            </td>
          </tr>
        </template>
      </table>
    </div>
    <div class="md:col-span-4 p-1">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
    <SmallModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal">單位列表</div>
        </div>
      </template>
      <template v-slot:content>
        <GradeSelector v-if="displayModal" :input-data="opened" @closeModal="displayModal = false" @returnData="(input: any) => { targetObject[openIndex].grade_list = input; }"></GradeSelector>
      </template>
    </SmallModal>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium;
  }
  th {
    @apply bg-blue-100;
  }
}
input[type=checkbox] {
  @apply h-6 w-6 m-auto;
}
.disabled {
  @apply text-gray-400;
}
</style>