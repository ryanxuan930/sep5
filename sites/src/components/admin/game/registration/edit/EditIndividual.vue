<script setup lang="ts">
import { ref, reactive } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';

const store = useUserStore();
const vr = new VueRequest(store.token);
const props = defineProps(['inputData', 'gameId', 'sportCode', 'regList', 'regConfig']);
const data = reactive({
  u_id: '',
  event_code: '',
  division_id: 0,
  ref_result: '',
});

if (props.inputData != null) {
  data.u_id = props.inputData.u_id;
  data.event_code = props.inputData.event_code;
  data.division_id = props.inputData.division_id;
  data.ref_result = props.inputData.ref_result;
}

const divisionList: any = ref([]);
const paramList: any = ref([]);
const athleteData: any = ref({});

async function getData() {
  await vr.Get(`game/${props.sportCode}/${props.gameId}/main/division`, divisionList);
  await vr.Get(`game/${props.sportCode}/${props.gameId}/main/params/full`, paramList);
}
getData();

async function inputHandler(index: number) {
  if (index > 0) {
    const res = await vr.Get(`user/${index}`, athleteData, true, true);
    if (res.org_name_ch == undefined) {
      alert('此選手不存在');
      data.u_id = '';
    } else {
      athleteData.value = res;
    }
  }
}

const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
const close = () => {
  emit('refreshPage');
  emit('closeModal');
}

const firstName: any = ref('');
const lastName: any = ref('');
const bib: any = ref('');
const searchResult: any = ref(null);

async function searchName() {
  if (firstName.value.length > 0 && lastName.value.length > 0) {
    await vr.Get(`user/name/${firstName.value}/${lastName.value}`, searchResult, true, true);
  } else {
    alert('請輸入全名');
  }
}
async function searchBib() {
  await vr.Get(`game/${props.sportCode}/${props.gameId}/common/athlete/bib/${bib.value}`, searchResult, true, true);
}

function selectHandler(item: any) {
  data.u_id = item.value.u_id;
  athleteData.value = JSON.parse(JSON.stringify(item));
  searchResult.value = null; 
}

async function submitAll(input: any) {
  if (input.u_id == '' || input.u_id == null) {
    alert('請選擇選手 Please select an athlete');
  }
  if (input.event_code == '' || input.event_code == null) {
    alert('請選擇項目 Please select an event');
    return;
  }
  const athlete = athleteData.value;
  for(const division of props.regConfig.options.division) {
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
  if (props.regConfig.options.event[input.event_code] != undefined) {
    if (props.regConfig.options.event[input.event_code].prevent_sport_gifited && athlete.is_sport_gifited == 1) {
      alert('體優生不得報名此項目 Sport gifited student is not allowed');
      return;
    }
    if (props.regConfig.options.event[input.event_code].student_only && athlete.is_student == 0) {
      alert('此項目僅限學生報名 This event is only for students');
      return;
    }
    if (!props.regConfig.options.event[input.event_code].grade_list.includes(athlete.grade) && props.regConfig.options.event[input.event_code].has_grade == true) {
      alert('不是可報名此項目的年級 This grade is not allowed');
      return;
    }
  }
  for (const data of props.regList) {
    if (data.division_id == input.division_id && data.event_code == input.event_code && data.u_id == input.u_id) {
      alert('此選手已報名此項目 This athlete has already registerd for this event');
      return;
    }
  }
  if (data.ref_result == '') {
    data.ref_result = '0';
  }
  if (props.inputData == null) {
    const response = await vr.Post(`game/${props.sportCode}/${props.gameId}/common/individual`, data, null, true, true);
    if (response.status == 'A01') {
      alert('已新增');
      close();
    }
  } else {
    const response = await vr.Patch(`game/${props.sportCode}/${props.gameId}/common/individual/${props.inputData.ind_id}`, data, null, true, true);
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
    <div class="p-3 bg-blue-50 flex flex-col gap-1">
      <div class="text-xl">選手查詢</div>
      <div class="flex items-center gap-2">
        <div class="search-box">
          <div>姓</div>
          <input type="text" v-model="lastName">
        </div>
        <div class="search-box">
          <div>名</div>
          <input type="text" v-model="firstName">
        </div>
        <button class="general-button blue" @click="searchName">搜尋</button>
        <div class="flex-grow"></div>
        <div class="search-box">
          <div>號碼布</div>
          <input type="text" v-model="bib">
        </div>
        <button class="general-button blue" @click="searchBib">搜尋</button>
      </div>
    </div>
    <div v-if="searchResult != null" class="bg-white bg-opacity-95 shadow-lg p-5 absolute rounded w-full text-lg border-[1px]">
      <div class="flex items-center">
        <div>搜尋結果</div>
        <div class="flex-grow"></div>
        <a class="hyperlink red" @click="searchResult = null">清除</a>
      </div>
      <hr class="my-1">
      <div class="flex items-start" v-for="(item, index) in searchResult" :key="index">
        <div class=flex-grow>
          <div>編號：{{ item.u_id }}</div>
          <div>姓名：{{ item.last_name_ch }}{{ searchResult.first_name_ch }}</div>
          <div>性別：{{ item.sex == 1 ? '男':'女' }}</div>
          <div>組織單位：{{ item.org_name_full_ch }}</div>
          <div>系所/分部：{{ item.dept_name_ch }}</div>
        </div>
        <div>
          <button class="hyperlink blue" @click="selectHandler(item)">選擇</button>
        </div>
      </div>
    </div>
    <table>
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
              <option :value="item.event_code" v-if="item.division_id == data.division_id && item.multiple == 0">{{ item.event_ch }}</option>
            </template>
          </select>
        </td>
      </tr>
      <tr>
        <td>選手</td>
        <td>
          <div class="flex flex-col gap-1">
            <input type="text" v-model.number="data.u_id" @change="inputHandler(Number(data.u_id))" />
            <div v-if="athleteData.org_name_ch == undefined"></div>
            <div v-else>選手：{{ athleteData.org_name_ch }} {{ athleteData.last_name_ch }}{{ athleteData.first_name_ch }}</div>
          </div>
        </td>
        <td>參考成績</td>
        <td>
          <input type="text" v-model="data.ref_result" />
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