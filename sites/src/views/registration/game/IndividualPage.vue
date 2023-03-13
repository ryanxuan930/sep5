<script setup lang="ts">
import { ref, inject, reactive, watch } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import VueRequest from '@/vue-request';
import { useRoute, useRouter } from 'vue-router';
import Config from '@/assets/config.json';

const store = useUserStore();
const route = useRoute();
const router = useRouter();
const vr = new VueRequest(store.token);
const adminOrgId = route.params.adminOrgId;

const data = reactive({
  u_id: 0,
  event_code: '',
  division_id: 0,
  ref_result: '',
});

const paramList: any = inject('paramList');
const regConfig: any = inject('regConfig');
const sportList: any = ref([]);
const selectedSport = ref(0);
vr.Get('sport', sportList, true, true);
const divisionList: any = inject('divisionList');
const userList: any = inject('userList');
const divisionSex = ref(1);
watch(data, () => {
  for (const item of divisionList.value) {
    if (item.division_id == data.division_id) {
      divisionSex.value = item.sex;
    }
  }
});
const gameData: any = inject('gameData');
const individualList: any = ref([]);
const countData: any = ref([]);
function getIndList() {
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/user`, individualList, true, true);
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/count/${gameData.value.options.regUnit}`, countData, true, true);
}
getIndList();

function backToHome(){
  router.push(`/${adminOrgId}/registration/game/${route.params.sportCode}/${route.params.gameId}`);
}

function check(params: any) {
  // check data has individual event
  let individualEventCount = 0;
  params.forEach((element: any) => {
    if (element.multiple == 0) {
      individualEventCount++;
    }
  });
  if (individualEventCount == 0) {
    alert('無個人項目 No any individual event');
    backToHome();
  }
}
check(paramList.value);

async function addEvent(input: any) {
  if (data.u_id == 0) {
    alert('請選擇選手 Please select an athlete');
  }
  if (input.event_code == '' || input.event_code == null) {
    alert('請選擇項目 Please select an event');
    return;
  }
  let athlete: any;
  for (var i = 0; i < userList.value.length; i++) {
    if (userList.value[i].u_id == input.u_id) {
      athlete = userList.value[i];
    }
  }
  for(const division of regConfig.value.options.division) {
    if (division.division_id == input.division_id) {
      if (division.prevent_sport_gifited == true && athlete.is_sport_gifited == 1) {
        alert('體優生不得報名此組別 Sport gifited student is not allowed');
        return;
      }
      if (division.student_only == true && athlete.is_student == 0) {
        alert('此組別僅限學生報名 This division is only for students');
        return;
      }
      if (!division.grade_list.includes(athlete.grade) && division.has_grade == true) {
        alert('不是可報名此組別的年級 This grade is not allowed');
        return;
      }
    }
  }
  if (regConfig.value.options.event[input.event_code] != undefined) {
    if (regConfig.value.options.event[input.event_code].prevent_sport_gifited && athlete.is_sport_gifited == 1) {
      alert('體優生不得報名此項目 Sport gifited student is not allowed');
      return;
    }
    if (regConfig.value.options.event[input.event_code].student_only && athlete.is_student == 0) {
      alert('此項目僅限學生報名 This event is only for students');
      return;
    }
    if (!regConfig.value.options.event[input.event_code].grade_list.includes(athlete.grade) && regConfig.value.options.event[input.event_code].has_grade == true) {
      alert('不是可報名此項目的年級 This grade is not allowed');
      return;
    }
  }
  for (const count of countData.value.event) {
    if (count.division_id == input.division_id && count.event_code == input.event_code && count.total >= regConfig.value.options.common.individual.max_athlete_per_event) {
      alert('此項目已超過可報名人數 Exceed max athlete number of this event');
      return;
    }
  }
  for (const count of countData.value.athlete) {
    if (input.u_id == count.u_id && count.total >= regConfig.value.options.common.individual.max_event_per_athlete) {
      alert('此選手已達報名項目上限 Exceed max event number of this athlete');
      return;
    }
  }
  for (const data of individualList.value) {
    if (data.division_id == input.division_id && data.event_code == input.event_code && data.u_id == input.u_id) {
      alert('此選手已報名此項目 This athlete has already registerd for this event');
      return;
    }
  }
  if (data.ref_result == '') {
    data.ref_result = '0';
  }
  const response = await vr.Post(`game/${route.params.sportCode}/${route.params.gameId}/common/individual`, data, null, true, true);
  if (response.status == 'A01') {
    data.division_id = 0;
    data.event_code = '';
    data.ref_result = '';
    data.u_id = store.userInfo.u_id;
    alert('已新增 Done');
    getIndList();
  }
}

async function deleteItem(id: number) {
  const r = confirm('確定刪除? Are you sure you want to delete?');
  if (r) {
    await vr.Delete(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/${id}`, null, true, true);
    getIndList();
  }
}

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div class="h-full overflow-auto">
    <div class="flex flex-col gap-5 overflow-auto">
      <div class="section-box">
        <div class="section-title">{{ t('individual-event') }}</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <label class="round-input-label">
            <div class="title">{{ t('division') }}</div>
            <select class="select"  v-model="data.division_id">
              <template v-for="(item, index) in divisionList" :key="index">
                <option :value="item.division_id">
                  <template v-if="locale == 'zh-TW'">{{ item.division_ch }}</template>
                  <template v-else>{{ item.division_en }}</template>
                </option>
              </template>
            </select>
          </label>
          <label class="round-input-label">
            <div class="title">{{ t('event') }}</div>
            <select class="select" v-model="data.event_code">
              <template v-for="(item, index) in paramList" :key="index">
                <option :value="item.event_code" v-if="item.division_id == data.division_id && item.multiple == 0">
                  <template v-if="locale == 'zh-TW'">{{ item.event_ch}}</template>
                  <template v-else>{{ item.event_en }}</template>
                </option>
              </template>
            </select>
          </label>
          <div :class="{'md:col-span-2': gameData.module == 'ge', 'flex items-end gap-5': true}">
            <label class="round-input-label flex-grow basis-2/3">
              <div class="title">{{ t('name') }}</div>
              <select class="select" v-model="data.u_id">
                <template v-for="(item, index) in userList" :key="index">
                  <option :value="item.u_id" v-if="(item.sex == divisionSex || divisionSex == 0) && (item.sport_list.includes(selectedSport) || selectedSport == 0)">
                    <template v-if="locale == 'zh-TW' || item.first_name_en == null || item.last_name_en == null">{{ item.last_name_ch }}{{ item.first_name_ch }}</template>
                    <template v-else>{{ item.first_name_en }} {{ item.last_name_en }}</template>
                    <span v-if="Config.deptAsClass"> ({{ item.dept_name_ch }}{{ item.num_in_dept.toString().padStart(2, '0') }})</span>
                    <span v-else> ({{ item.athlete_id }})</span>
                  </option>
                </template>
              </select>
            </label>
            <label class="round-input-label basis-1/3">
              <div class="title">{{ t('sports') }}</div>
              <select class="select" v-model="selectedSport">
                <option :value="0">全選</option>
                <template v-for="(item, index) in sportList" :key="index">
                  <option :value="item.sport_id">
                    <template v-if="locale == 'zh-TW' || item.first_name_en == null || item.last_name_en == null">{{ item.sport_name_ch }}</template>
                    <template v-else>{{ item.sport_name_en }}</template>
                  </option>
                </template>
              </select>
            </label>
          </div>
          <label class="round-input-label" v-if="gameData.module != 'ge'">
            <div class="title">{{ t('ref-result') }}</div>
            <input class="input" type="text" v-model="data.ref_result">
          </label>
          <button class="round-full-button blue md:col-span-2" @click="addEvent(data)">{{ t('save') }}</button>
        </div>
      </div>
      <div class="section-box flex-grow">
        <div class="section-title pb-3">{{ t('registered') }}</div>
        <table class="game-table">
          <tr>
            <th>{{ t('division') }}</th>
            <th>{{ t('event') }}</th>
            <th>{{ t('name') }}</th>
            <th>{{ t('sex') }}</th>
            <th>{{ t('organization') }}</th>
            <th v-if="Config.deptAsClass">{{ t('class') }}</th>
            <th v-else>{{ t('department') }}</th>
            <th v-if="gameData.module != 'ge'">{{ t('ref-result') }}</th>
            <th></th>
          </tr>
          <template v-for="(item, index) in individualList" :key="index">
            <tr>
              <td>
                <template v-if="locale == 'zh-TW'">{{ item.division_ch }}</template>
                <template v-else>{{ item.division_en }}</template>
              </td>
              <td>
                <template v-if="locale == 'zh-TW'">{{ item.event_ch }}</template>
                <template v-else>{{ item.event_en }}</template>
              </td>
              <td>
                <template v-if="locale == 'zh-TW' || item.first_name_en == null || item.last_name_en == null">{{ item.last_name_ch }}{{ item.first_name_ch }}</template>
                <template v-else>{{ item.first_name_en }} {{ item.last_name_en }}</template>
              </td>
              <td>
                <template v-if="item.sex == 0">{{ t('others') }}</template>
                <template v-if="item.sex == 1">{{ t('male') }}</template>
                <template v-if="item.sex == 2">{{ t('female') }}</template>
              </td>
              <td>
                <template v-if="locale == 'zh-TW'">{{ item.org_name_ch }}</template>
                <template v-else>{{ item.org_name_en }}</template>
              </td>
              <td>
                <template v-if="locale == 'zh-TW'">{{ item.dept_name_ch }}</template>
                <template v-else>{{ item.dept_name_en }}</template>
              </td>
              <td v-if="gameData.module != 'ge'">{{ item.ref_result }}</td>
              <td>
                <button class="general-button red" @click="deleteItem(item.ind_id)">{{ t('delete') }}</button>
              </td>
            </tr>
          </template>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.section-title {
  @apply text-2xl font-medium;
}
.section-title + hr {
  @apply my-3 border-[1px];
}
.game-table {
  @apply w-full text-left overflow-auto;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium;
  }
  th {
    @apply bg-blue-100;
  }
}
.general-button.active {
  @apply bg-blue-400;
}
.page-btn {
  @apply flex my-5 gap-3;
}
table {
  @apply w-full text-left border-collapse;
  td, th {
    @apply p-1;
  }
}
</style>

<i18n lang="yaml">
  en-US:
    individual-event: 'Individual Events'
    group-event: 'Group Events'
    division: 'Division'
    event: 'Event'
    name: 'Name'
    ref-result: 'Record'
    registered: 'Registered'
    save: 'Save'
    delete: 'Delete'
    organization: 'Organization'
    department: 'Department'
    team: 'Team'
    sex: 'Sex'
    male: 'M'
    female: 'F'
    others: 'X'
    class: 'Class'
    sports: 'Sports'
  zh-TW:
    individual-event: '個人項目'
    group-event: '團體項目'
    division: '組別'
    event: '項目'
    name: '姓名'
    ref-result: '參考成績'
    registered: '已報名項目'
    save: '儲存'
    delete: '刪除'
    organization: '組織單位'
    department: '分部/系所'
    team: '隊伍'
    sex: '性別'
    male: '男'
    female: '女'
    others: '其他'
    class: '班級'
    sports: '專長項目'
</i18n>