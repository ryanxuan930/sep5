<script setup lang="ts">
import { ref, inject, reactive, watch } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import VueRequest from '@/vue-request';
import { useRoute, useRouter } from 'vue-router';
import FullModal from '@/components/FullModal.vue';
import TeamData from '@/components/registration/module/TeamData.vue'

const store = useUserStore();
const route = useRoute();
const router = useRouter();
const displayModal = ref(false);
const vr = new VueRequest(store.token);
const adminOrgId = route.params.adminOrgId;

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});

interface IData {
  member_list: number[];
  event_code: string;
  division_id: number;
  ref_result?: string|null;
  team_name?: string|null;
}

const data: IData = reactive({
  member_list:[],
  event_code: '',
  division_id: 0,
  ref_result: '',
  team_name: '',
});
const selectedUser = ref(store.userInfo.u_id);
const crossUser = ref('');
const crossUserData: any = ref(null);
const crossUserList: any = ref([]);

const paramList: any = inject('paramList');
const regConfig: any = inject('regConfig');
const divisionList: any = inject('divisionList');
const userList: any = inject('userList');
const gameData: any = inject('gameData');
const userData: any = inject('userData');
const groupList: any = ref([]);
const countData: any = ref([]);
const selectedItem: any = ref()
const divisionSex = ref(0);
watch(data, () => {
  for (const item of divisionList.value) {
    if (item.division_id == data.division_id) {
      divisionSex.value = item.sex;
    }
  }
});
async function findUserById(athleteId: string) {
  crossUserData.value = await vr.Get(`user/athlete/${athleteId}`, null, true, true);
}
function getGrpList() {
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/user`, groupList, true, true);
  vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/count/${gameData.value.options.regUnit}`, countData, true, true);
}
getGrpList();

function backToHome(){
  router.push(`/${adminOrgId}/registration/game/${route.params.sportCode}/${route.params.gameId}`);
}

function check(params: any) {
  // check data has individual event
  let groupEventCount = 0;
  params.forEach((element: any) => {
    if (element.multiple == 1) {
      groupEventCount++;
    }
  });
  if (groupEventCount == 0) {
    alert('無團體項目 No any group event');
    backToHome();
  }
}
check(paramList.value);

async function addEvent(input: any) {
  if (input.event_code == '' || input.event_code == null) {
    alert('請選擇項目 Please select an event');
    return;
  }
  for (i = 0; i < paramList.value.length; i++) {
    if (paramList.value[i].event_code == input.event_code) {
      let preDefine = false;
      if (regConfig.value.options.event[input.event_code] != undefined) {
        preDefine = regConfig.value.options.event[input.event_code].pre_define_member;
      }
      if (input.member_list.length < paramList.value[i].player_num && preDefine == false) {
        alert(`本項目需至少${paramList.value[i].player_num}位選手 This event requires at least ${paramList.value[i].player_num} athletes`);
        return;
      }
    }
  }
  const athleteList = userList.value.concat(crossUserList.value);
  for (var i = 0; i < athleteList.length; i++) {
    console.log(athleteList[i].u_id ,input.member_list.includes(athleteList[i].u_id))
    if (input.member_list.includes(athleteList[i].u_id)) {
      const athlete = athleteList[i];
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
        }
      }
      if (regConfig.value.options.event[input.event_code] != undefined) {
        if (regConfig.value.options.event[input.event_code].prevent_sport_gifited && athlete.is_sport_gifited == true) {
          alert('體優生不得報名此項目 Sport gifited student is not allowed');
          return;
        }
        if (regConfig.value.options.event[input.event_code].student_only && athlete.is_student == 0) {
          alert('此組別僅限學生報名 This event is only for students');
          return;
        }
      }
    }
  }
  
  for (const count of countData.value.event) {
    if (count.division_id == input.division_id && count.event_code == input.event_code && count.total >= regConfig.value.options.common.group.max_team_per_event) {
      alert('此項目已超過可報名隊伍數 Exceed max team number of this event');
      return;
    }
  }
  /*
  for (const count of countData.value.team) {
    if (input.team_id == count.team_id && count.total >= regConfig.value.options.common.individual.max_event_per_team) {
      alert('此隊伍已達報名項目上限 Exceed max event number of this athlete');
      return;
    }
  }*/
  for (const data of groupList.value) {
    if (data.division_id == input.division_id && data.event_code == input.event_code) {
      let count = 0;
      for (const item of JSON.parse(data.member_list)) {
        if (input.member_list.includes(item)) {
          count++;
        }
      }
      console.log(count);
      if (count == input.member_list.length) {
        alert('此隊伍已報名此項目 This team has already registerd for this event');
        return;
      }
    }
  }
  const temp = JSON.parse(JSON.stringify(data));
  temp.org_id = userData.value.org_id;
  temp.dept_id = userData.value.dept_id;
  temp.member_list = JSON.stringify(temp.member_list);
  if (temp.ref_result == '') {
    temp.ref_result = '0';
  }
  if (temp.team_name == '') {
    if (locale.value == 'zh-TW') {
      temp.team_name = userData.value.org_name_ch + '-' + userData.value.dept_name_ch;
    } else {
      temp.team_name = userData.value.org_name_en + '-' + userData.value.dept_name_en;
    }
  }
  const response = await vr.Post(`game/${route.params.sportCode}/${route.params.gameId}/common/group`, temp, null, true, true);
  if (response.status == 'A01') {
    data.division_id = 0;
    data.event_code = '';
    data.ref_result = '';
    data.member_list = [];
    selectedUser.value = store.userInfo.u_id;
    alert('已新增 Done');
    getGrpList();
  }
}

async function deleteItem(id: number) {
  const r = confirm('確定刪除? Are you sure you want to delete?');
  if (r) {
    await vr.Delete(`game/${route.params.sportCode}/${route.params.gameId}/common/group/${id}`, null, true, true);
    getGrpList();
  }
}

function addUser(id: number, outside = false) {
  if (!data.member_list.includes(id)) {
    data.member_list.push(id);
  }
  if (outside) {
    for (let i = 0; i < crossUserList.value.length; i++) {
      if (crossUserList.value[i].u_id == id) {
        return;
      }
    }
    crossUserList.value.push(JSON.parse(JSON.stringify(crossUserData.value)));
    crossUserData.value = null;
    crossUser.value = '';
  }
}

function viewItem(item: any) {
  selectedItem.value = item;
  displayModal.value = true;
}

function removeUser(id: number) {
  data.member_list.splice(data.member_list.indexOf(id), 1);
}
</script>

<template>
  <div class="h-full overflow-auto">
    <div class="flex flex-col gap-5 overflow-auto">
      <div class="section-box">
        <div class="section-title">{{ t('group-event') }}</div>
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
                <option :value="item.event_code" v-if="item.division_id == data.division_id && item.multiple == 1">
                  <template v-if="locale == 'zh-TW'">{{ item.event_ch}}</template>
                  <template v-else>{{ item.event_en }}</template>
                </option>
              </template>
            </select>
          </label>
          <label class="round-input-label md:col-span-2">
            <div class="title">{{ t('name') }}</div>
            <div class="flex items-center gap-5">
              <select class="select flex-grow" v-model="selectedUser">
                <template v-for="(item, index) in userList" :key="index">
                  <option :value="item.u_id" v-if="item.sex == divisionSex || divisionSex == 0">
                    <template v-if="locale == 'zh-TW' || item.first_name_en == null || item.last_name_en == null">{{ item.last_name_ch }}{{ item.first_name_ch }} ({{ item.athlete_id }})</template>
                    <template v-else>{{ item.first_name_en }} {{ item.last_name_en }} ({{ item.athlete_id }})</template>
                  </option>
                </template>
              </select>
              <div class="flex-shrink-0">
                <button class="round-full-button blue" @click="addUser(selectedUser)">{{ t('add') }}</button>
              </div>
            </div>
          </label>
          <template v-if="(regConfig.options.common.allow_cross_org || regConfig.options.common.allow_cross_dept) && ((regConfig.options.common.allow_grouping == true && store.userInfo.permission == 0) || store.userInfo.permission > 0)">
            <label class="round-input-label md:col-span-2">
              <div class="title">{{ t('cross-athlete') }}</div>
              <div class="flex items-center gap-5">
                <input class="input" type="text" v-model="crossUser" placeholder="選手代碼或學號 Athlete ID or Student ID">
                <div class="flex-shrink-0">
                  <button class="round-full-button blue" @click="findUserById(crossUser)">{{ t('find') }}</button>
                </div>
              </div>
            </label>
            <div class="round-input-label flex md:col-span-2 gap-2 items-center" v-if="crossUserData != null">
              <div class="title">{{ t('result') }}</div>
              <div class="p-1 flex items-center gap-5">
                <template v-if="crossUserData.u_id != undefined">
                  <div class="input">
                    <span v-if="locale == 'zh-TW'">{{ crossUserData.first_name_ch }}{{ crossUserData.last_name_ch }} | {{ crossUserData.org_name_full_ch }} {{ crossUserData.dept_name_ch }}</span>
                    <span v-else>{{ crossUserData.first_name_ch }}{{ crossUserData.last_name_ch }} | {{ crossUserData.org_name_full_ch }} {{ crossUserData.dept_name_ch }}</span>
                  </div>
                  <div class="flex-shrink-0">
                    <button class="round-full-button blue" v-if="regConfig.options.common.allow_cross_org || regConfig.options.common.allow_cross_dept && crossUserData.org_code == store.userInfo.org_code" @click="addUser(crossUserData.u_id, true)">{{ t('add') }}</button>
                    <div class="round-full-button blue-hollow" v-else>{{ t('not-meet-require') }}</div>
                  </div>
                </template>
                <div v-else>{{ t('not-found') }}</div>
              </div>
            </div>
          </template>
          <div class="round-input-label flex md:col-span-2 gap-2 items-center" v-if="data.member_list.length > 0">
            <div class="title">{{ t('member') }}</div>
            <div class="flex gap-3 py-2">
              <template v-for="(item, index) in userList.concat(crossUserList)" :key="index">
                <div v-if="data.member_list.includes(item.u_id)" class="rounded py-1 px-2 border-2 relative">
                  <span v-if="locale == 'zh-TW'">{{ item.last_name_ch }}{{ item.first_name_ch}}</span>
                  <span v-else>{{ item.first_name_en }} {{ item.last_name_en}}</span>
                  <span class="absolute inline-block -right-2 -top-3 w-5 h-5 text-center text-sm text-white bg-gray-400 hover:bg-opacity-90 duration-150 cursor-pointer rounded-full" @click="removeUser(item.u_id)">
                    <div>×</div>
                  </span>
                </div>
              </template>
            </div>
          </div>
          <label :class="{'round-input-label': true, 'md:col-span-2': gameData.module == 'ge'}">
            <div class="title">{{ t('team') }}</div>
            <input class="input" type="text" v-model="data.team_name">
          </label>
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
            <th>{{ t('department') }}</th>
            <th v-if="gameData.module != 'ge'">{{ t('ref-result') }}</th>
            <th></th>
          </tr>
          <template v-for="(item, index) in groupList" :key="index">
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
              <td class="flex gap-2">
                <button class="general-button blue" @click="viewItem(item)">{{ t('view') }}</button>
                <button class="general-button red" @click="deleteItem(item.grp_id)">{{ t('delete') }}</button>
              </td>
            </tr>
          </template>
        </table>
      </div>
    </div>
    <FullModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal">{{ t('view') }}</div>
        </div>
      </template>
      <template v-slot:content>
        <TeamData v-if="displayModal" :input-data="selectedItem" @closeModal="displayModal = false"></TeamData>
      </template>
    </FullModal>
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
    team: 'Team Name'
    sex: 'Sex'
    male: 'M'
    female: 'F'
    others: 'X'
    add: 'Add'
    find: 'Find'
    cross-athlete: 'Cross Department / Organization Athlete'
    result: 'Search Result'
    not-meet-require: 'Not Allowed'
    member: 'Members'
    not-found: 'Athlete Not Found'
    view: 'View'
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
    team: '隊名'
    sex: '性別'
    male: '男'
    female: '女'
    others: '其他'
    add: '加入'
    find: '查詢'
    cross-athlete: '跨單位選手'
    result: '搜尋結果'
    not-meet-require: '不符合資格'
    member: '成員'
    not-found: '找不到符合的資料'
    view: '查看'
</i18n>