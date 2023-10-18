<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import SmallModal from '@/components/SmallModal.vue';
import LegsList from '@/components/admin/game/management/LegsList.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const displayModal = ref(0);
const props = defineProps(['inputData', 'gameId', 'sportCode', 'regList', 'regConfig']);
const data = reactive({
  team_id: '',
  event_code: '',
  division_id: 0,
  ref_result: '',
  team_name: '',
  member_list: [],
  org_id: 0,
  dept_id: 0,
});

if (props.inputData != null) {
  data.team_id = props.inputData.team_id;
  data.event_code = props.inputData.event_code;
  data.division_id = props.inputData.division_id;
  data.ref_result = props.inputData.ref_result;
  data.team_name = props.inputData.team_name;
  data.member_list = props.inputData.member_list;
  data.org_id = props.inputData.org_id;
  data.dept_id = props.inputData.dept_id;
}

const divisionList: any = ref([]);
const paramList: any = ref([]);
const athleteData: any = ref({});
const orgList: any = ref([]);
const deptList: any = ref([]);

async function getData() {
  await vr.Get(`game/${props.sportCode}/${props.gameId}/main/division`, divisionList);
  await vr.Get(`game/${props.sportCode}/${props.gameId}/main/params/full`, paramList);
  if (props.inputData !== null && props.inputData.member_list == '[]') {
    const res = await vr.Post(`user-from-list`, {data: props.inputData.member_list}, null, true, true);
    athleteData.value = res;
  }
  await vr.Get(`organization`, orgList, true, true);
  if (props.inputData !== null && props.inputData.org_id !== undefined && props.inputData.org_id != 0) {
    await vr.Get(`department/org/${props.inputData.org_id}`, deptList, true, true);
  }
}
getData();

const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
const close = () => {
  emit('refreshPage');
  emit('closeModal');
}

async function submitAll(input: any) {
  if (input.event_code == '' || input.event_code == null) {
    alert('請選擇項目 Please select an event');
    return;
  }
  for (let i = 0; i < paramList.value.length; i++) {
    if (paramList.value[i].event_code == input.event_code) {
      let preDefine = false;
      if (props.regConfig.options.event[input.event_code].pre_define_member != undefined) {
        preDefine = props.regConfig.options.event[input.event_code].pre_define_member;
      }
      if (input.member_list.length < paramList.value[i].player_num && preDefine == true) {
        alert(`本項目需至少${paramList.value[i].player_num}位選手 This event requires at least ${paramList.value[i].player_num} athletes`);
        return;
      } else {
        for(const division of props.regConfig.options.division) {
          if (division.division_id == input.division_id) {
            if (division.prevent_sport_gifited == true && store.userInfo.is_sport_gifited == 1) {
              alert('體優生不得報名此組別 Sport gifited student is not allowed');
              return;
            }
            if (division.student_only == true && store.userInfo.is_student == 0) {
              alert('此組別僅限學生報名 This division is only for students');
              return;
            }
            if (!division.grade_list.includes(store.userInfo.grade) && division.has_grade == true) {
              alert('不是可報名此組別的年級 This grade is not allowed');
              return;
            }
          }
        }
        if (props.regConfig.options.event[input.event_code] != undefined) {
          if (props.regConfig.options.event[input.event_code].prevent_sport_gifited && store.userInfo.is_sport_gifited == true) {
            alert('體優生不得報名此項目 Sport gifited student is not allowed');
            console.log(5);
            return;
          }
          if (props.regConfig.options.event[input.event_code].student_only && store.userInfo.is_student == 0) {
            alert('此組別僅限學生報名 This event is only for students');
            console.log(6);
            return;
          }
          if (!props.regConfig.options.event[input.event_code].grade_list.includes(store.userInfo.grade) && props.regConfig.options.event[input.event_code].has_grade == true) {
            alert('不是可報名此項目的年級 This grade is not allowed');
            return;
          }
        }
      }
    }
  }
  const athleteList = athleteData.value;
  for (var i = 0; i < athleteList.length; i++) {
    if (input.member_list.includes(athleteList[i].u_id)) {
      const athlete = athleteList[i];
      for(const division of props.regConfig.options.division) {
        if (division.division_id == input.division_id) {
          if (division.prevent_sport_gifited == true && athlete.is_sport_gifited == 1) {
            alert('體優生不得報名此組別 Sport gifited student is not allowed');
            console.log(3);
            return;
          }
          if (division.student_only == true && athlete.is_student == 0) {
            alert('此組別僅限學生報名 This division is only for students');
            console.log(4);
            return;
          }
          if (!division.grade_list.includes(athlete.grade) && division.has_grade == true) {
            alert('不是可報名此組別的年級 This grade is not allowed');
            return;
          }
        }
      }
      if (props.regConfig.options.event[input.event_code] != undefined) {
        if (props.regConfig.options.event[input.event_code].prevent_sport_gifited && athlete.is_sport_gifited == true) {
          alert('體優生不得報名此項目 Sport gifited student is not allowed');
          console.log(5);
          return;
        }
        if (props.regConfig.options.event[input.event_code].student_only && athlete.is_student == 0) {
          alert('此組別僅限學生報名 This event is only for students');
          console.log(6);
          return;
        }
        if (!props.regConfig.options.event[input.event_code].grade_list.includes(athlete.grade) && props.regConfig.options.event[input.event_code].has_grade == true) {
          alert('不是可報名此項目的年級 This grade is not allowed');
          return;
        }
      }
    }
  }
  for (const data of props.regList) {
    if (data.division_id == input.division_id && data.event_code == input.event_code) {
      let count = 0;
      for (const item of JSON.parse(data.member_list)) {
        if (input.member_list.includes(item)) {
          count++;
        }
      }
      if (count == input.member_list.length) {
        alert('此隊伍已報名此項目 This team has already registerd for this event');
        console.log(8);
        return;
      }
    }
  }
  if (data.ref_result == '') {
    data.ref_result = '0';
  }
  if (props.inputData == null) {
    const response = await vr.Post(`game/${props.sportCode}/${props.gameId}/common/group`, data, null, true, true);
    if (response.status == 'A01') {
      alert('已新增');
      close();
    }
  } else {
    const response = await vr.Patch(`game/${props.sportCode}/${props.gameId}/common/group/${props.inputData.ind_id}`, data, null, true, true);
    if (response.status == 'A01') {
      alert('已編輯');
      close();
    }
  }
}

async function deleteItem(id: number) {
  const r = confirm('確定刪除?');
  if (r) {
    await vr.Delete(`game/${props.sportCode}/${props.gameId}/common/individual/${id}`, null, true, true);
    close();
  }
}
</script>

<template>
  <div class="relative">
    <table>
      <tr>
        <td class="w-1/12">單位</td>
        <td class="w-5/12">
          <select v-model="data.org_id">
            <option value="" disabled>請選擇單位</option>
            <option v-for="item in orgList" :value="item.org_id">{{ item.org_name_ch }}</option>
          </select>
        </td>
        <td class="w-1/12">分部</td>
        <td class="w-5/12">
          <select v-model="data.dept_id">
            <option value="" disabled>請選擇分部</option>
            <template v-for="(item, index) in deptList" :key="index">
              <option :value="item.dept_id">{{ item.dept_name_ch }}</option>
            </template>
          </select>
        </td>
      </tr>
      <tr>
        <td>組別</td>
        <td>
          <select v-model="data.division_id">
            <option value="" disabled>請選擇組別</option>
            <option v-for="item in divisionList" :value="item.division_id">{{ item.division_ch }}</option>
          </select>
        </td>
        <td>項目</td>
        <td>
          <select v-model="data.event_code">
            <option value="" disabled>請選擇項目</option>
            <template v-for="(item, index) in paramList" :key="index">
              <option :value="item.event_code" v-if="item.division_id == data.division_id && item.multiple == 1">{{ item.event_ch }}</option>
            </template>
          </select>
        </td>
      </tr>
      <tr>
        <td>隊名</td>
        <td>
          <div class="flex flex-col gap-1">
            <input type="text" v-model="data.team_name" />
          </div>
        </td>
        <td>參考成績</td>
        <td>
          <input type="text" v-model="data.ref_result" />
        </td>
      </tr>
      <tr>
        <td colspan="4">
          <button class="round-full-button blue" @click="displayModal = 1">編輯棒次</button>
        </td>
      </tr>
      <tr>
        <td colspan="4">
          <button class="round-full-button blue" @click="submitAll(data)">儲存</button>
        </td>
      </tr>
      <tr v-if="props.inputData != null">
        <td colspan="4">
          <button class="round-full-button red" @click="deleteItem(props.inputData.ind_id)">刪除</button>
        </td>
      </tr>
    </table>
    <div class="text-xs">*此處報名不會驗證是否超過報名人數限制與組別資格</div>
  </div>
  <SmallModal v-show="displayModal" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">編輯棒次</div>
      </div>
    </template>
    <template v-slot:content>
      <LegsList v-if="paramList.length > 0 && data.event_code != ''" :inputData="data" :playerNum="paramList.filter((item: any) => item.division_id == data.division_id && item.event_code == data.event_code)[0].player_num" @returnData="(res: any) => data.member_list = res" />
    </template>
  </SmallModal>
</template>

<style scoped lang="scss">
table {
  @apply w-full;
  td {
    @apply p-1 align-top;
    select, input {
      @apply w-full p-1 border rounded;
    }
  }
}
.search-box {
  @apply flex items-center gap-1;
  div {
    @apply whitespace-nowrap;
  }
  input {
    @apply flex-grow p-1 border rounded w-24;
  }
}
</style>