<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import SmallLoader from '@/components/SmallLoader.vue';
import { csvToArray, openWindow } from '@/components/library/functions';
import Grade from '@/assets/grade.json';
import { number } from '@intlify/core-base';

const store = useUserStore();
const route = useRoute();
const vr = new VueRequest(store.token);
const boxWidth = 2300;
const isLoading = ref(false);
const countryList: any = ref(null);
vr.Get('country', countryList);
const cityList: any = ref(null);
vr.Get('city', cityList);
const sportList: any = ref(null);
vr.Get('sport', sportList);

const deptList: any = ref([]);
async function getDeptList() {
  if (store.userInfo.permission == 2) {
    await vr.Get(`department/org/code/${store.userInfo.org_code}`, deptList, true, true);
  } else {
    const temp = await vr.Get(`department/${store.userInfo.dept_id}`, null, true, true);
    deptList.value = [temp];
  }
}
getDeptList();

const uploadEntity: any = ref(null);
const fileName = ref('');
const uploadData: any = ref([]);

function check() {
  console.log(uploadData.value);
  for(let i = 0; i < uploadData.value.length; i++) {
    uploadData.value[i].org_code = store.userInfo.org_code;
    if (store.userInfo.permission == 1 || store.userInfo.org_code.substring(0, 1) == 'O') {
      uploadData.value[i].dept_id = store.userInfo.dept_id;
    }
    const tempAccount = `${store.userInfo.org_code}${Date.now()}`;
    if (uploadData.value[i].account == null || uploadData.value[i].account == '') {
      uploadData.value[i].account = tempAccount;
    }
    if (uploadData.value[i].password == null || uploadData.value[i].password == '') {
      uploadData.value[i].password = tempAccount;
    }
    if (uploadData.value[i].is_student != 1) {
      uploadData.value[i].is_student = 0;
    }
    console.log(uploadData.value[i].dept_id);
    if (Number.isInteger(Number(uploadData.value[i].dept_id)) || uploadData.value[i].dept_id == null) {
      uploadData.value[i].dept_id = store.userInfo.dept_id;
    } else {
      uploadData.value[i].dept_id = Number(uploadData.value[i].dept_id);
    }
    if (uploadData.value[i].nationality == '' || uploadData.value[i].nationality == null) {
      uploadData.value[i].nationality = 'TW';
    }
    if (!Number.isInteger(uploadData.value[i].grade)) {
      let flag = true;
      for(let j = 0; j < Grade.length; j++) {
        if (uploadData.value[i].grade == Grade[j].grade) {
          flag = false;
          uploadData.value[i].grade = Grade[j].grade_id;
        }
      }
      if (flag) {
        uploadData.value[i].grade = 0;
      }
    }
    uploadData.value[i].grade = parseInt(uploadData.value[i].grade);
    if (!Number.isInteger(uploadData.value[i].sex)) {
      if (uploadData.value[i].sex == '男') {
        uploadData.value[i].sex = 1;
      } else if (uploadData.value[i].sex == '女') {
        uploadData.value[i].sex = 2;
      } else {
        uploadData.value[i].sex = 1;
      }
    }
    if (!isNaN(uploadData.value[i].height)) {
      uploadData.value[i].height *= 100;
    } else {
      uploadData.value[i].height = 0;
    }
    if (!isNaN(uploadData.value[i].weight)) {
      uploadData.value[i].weight*=100;
    } else {
      uploadData.value[i].weight = 0;
    }
    if (!Number.isInteger(uploadData.value[i].num_in_dept)) {
      uploadData.value[i].num_in_dept = 0;
    }
    const sportTemp = [];
    if (uploadData.value[i].sports != null || uploadData.value[i].sport != '') {
      for(let j = 0; j < sportList.value.length; j++) {
        if (uploadData.value[i].sports == sportList.value[j].sport_name_ch) {
          sportTemp.push(sportList.value[j].sport_id);
        }
      }
    }
    uploadData.value[i].sport_list = JSON.stringify(sportTemp);
  }
}

function uploadFile(event: any) {
  isLoading.value = true;
  fileName.value = event.target.files[0].name;
  const reader = new FileReader();
  reader.onload = function (e: any) {
    uploadData.value = JSON.parse(JSON.stringify(csvToArray(e.target.result)));
    check();
    isLoading.value = false;
    return;
  };
  reader.readAsText(event.target.files[0]);
}
const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
const close = () => {
  emit('refreshPage');
  emit('closeModal');
}

async function submitAll() {
  isLoading.value = true;
  for(let i = 0; i < uploadData.value.length; i++) {
    delete uploadData.value[i].sports
  }
  const res: any = await vr.Post('user-upload', uploadData.value, null, true, true);
  isLoading.value = false;
  if (res.status == 'A01') {
    alert('上傳完畢');
    close();
  } else {
    alert('上傳失敗，資料格式錯誤');
  }
}

const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <div class="flex flex-col gap-5 py-2 h-full">
    <div>若需批次上傳單位內使用者，請下載csv範例檔填寫後上傳</div>
    <div class="grid grid-cols-1 md:grid-cols-2">
      <div>
        <button class="general-button blue" @click="openWindow('https://docs.google.com/spreadsheets/d/1Cq567AR9usV_wc3jJUihq6ILSgua1_4VfnRqi0ljD4g/edit?usp=sharing')">下載範例檔案</button>
      </div>
      <div class="flex items-center gap-3">
        <label class="general-button blue block">
          <span>上傳檔案</span>
          <input type="file" class="hidden" ref="uploadEntity" accept=".csv" @change="uploadFile">
        </label>
        <div class="flex-grow">檔名 : {{ fileName }}</div>
      </div>
    </div>
    <div class="overflow-auto h-full flex-grow shadow-inner" v-show="!isLoading">
      <table>
        <tr>
          <th>{{ t('account') }}</th>
          <th>{{ t('password') }}</th>
          <th>{{ t('last-name-ch') }}</th>
          <th>{{ t('first-name-ch') }}</th>
          <th>{{ t('last-name-en') }}</th>
          <th>{{ t('first-name-en') }}</th>
          <th>{{ t('nationality') }}</th>
          <th>{{ t('department') }}</th>
          <th>{{ t('is-student') }}</th>
          <th>{{ t('school-id') }}</th>
          <th>{{ t('grade') }}</th>
          <th>{{ t('unified-id') }}</th>
          <th>{{ t('birthday') }}</th>
          <th>{{ t('sex') }}</th>
          <th>{{ t('height') }}</th>
          <th>{{ t('weight') }}</th>
          <th>{{ t('blood') }}</th>
          <th>{{ t('cellphone') }}</th>
          <th>{{ t('telephone') }}</th>
          <th>{{ t('city') }}</th>
          <th>{{ t('address') }}</th>
          <th>{{ t('emergency-contact') }}</th>
          <th>{{ t('emergency-phone') }}</th>
          <th>{{ t('num-in-dept') }}</th>
          <th>{{ t('sport') }}</th>
        </tr>
        <template v-for="(item, index) in uploadData" :key="index">
          <tr>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.account }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.password }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.last_name_ch }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.first_name_ch }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.last_name_en }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.first_name_en }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <template v-for="(country, index) in countryList" :key="index">
                <div v-if="country.country_code == item.nationality">
                  <template v-if="locale == 'zh-TW'">{{ country.country_name_ch }}</template>
                  <template v-else>{{ country.country_name_en }}</template>
                </div>
              </template>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <template v-for="(dept, index) in deptList" :key="index">
                <div v-if="dept.dept_id == item.dept_id">
                  <template v-if="locale == 'zh-TW'">{{ dept.dept_name_ch }}</template>
                  <template v-else>{{ dept.dept_name_en }}</template>
                </div>
              </template>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.is_student }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.student_id }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <template v-for="(grade, index) in Grade" :key="index">
                <div v-if="grade.grade_id == item.grade">
                  <div>{{ grade.grade }}</div>
                </div>
              </template>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.unified_id }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.birthday }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div v-if="item.sex == 1">男</div>
              <div v-else-if="item.sex == 2">女</div>
              <div v-else>其他</div>
              <div></div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.height }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.weight }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.blood_type }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.cellphone }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.telephone }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <template v-for="(city, index) in cityList" :key="index">
                <div v-if="city.city_code == item.household_city_code">
                  <template v-if="locale == 'zh-TW'">{{ city.city_name_ch }}</template>
                  <template v-else>{{ city.city_name_en }}</template>
                </div>
              </template>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.address }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.emergency_contact }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.emergency_phone }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <div>{{ item.num_in_dept }}</div>
            </td>
            <td :style="{'width': `${boxWidth*0.1}px`}">
              <template v-for="(sport, index) in sportList" :key="index">
                <div v-if="JSON.parse(item.sport_list).includes(sport.sport_id)">
                  <div>{{ sport.sport_name_ch }}</div>
                </div>
              </template>
            </td>
          </tr>
        </template>
      </table>
    </div>
    <SmallLoader v-show="isLoading"></SmallLoader>
    <div>
      <button v-if="uploadData.length > 0" class="round-full-button blue" @click="submitAll">{{ t('save') }}</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[2300px] text-left;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium overflow-hidden;
    div {
      @apply whitespace-nowrap overflow-auto w-full py-2;
    }
  }
  th {
    @apply bg-blue-100 sticky top-0;
  }
} 
</style>

<i18n lang="yaml">
  en-US:
    setting: 'Settings'
    language: 'Language'
    account-setting: 'Account Settings'
    chinese-name: 'Chinese Name'
    english-name: 'Name'
    edit: 'Edit'
    avatar: 'Avatar'
    first-name-ch: 'Chinese First Name'
    last-name-ch: 'Chinese Last Name'
    first-name-en: 'First Name'
    last-name-en: 'Last Name'
    account: 'Account'
    athlete-id: 'Athlete ID'
    organization: 'Organization'
    department: 'Department'
    nationality: 'Nationality'
    unified-id: 'National ID / ARC ID'
    is-student: 'Student'
    yes: 'Yes'
    no: 'No'
    school-id: 'Student / Faculty ID'
    grade: 'Grade'
    birthday: 'Birthday'
    city: 'City of Domicile'
    address: 'Address (Current living place)'
    cellphone: 'Cellphone'
    telephone: 'Telephone'
    emergency-contact: 'Emergency Contact'
    emergency-phone: 'Contact Phone'
    sex: 'Sex'
    height: 'Height'
    weight: 'Weight'
    male: 'Male'
    female: 'Female'
    others: 'Others'
    blood: 'Blood Type'
    sport-gifited: 'Sport Gifited'
    sport-gifited-item: 'Sports'
    indigenous: 'Indigenous'
    tribe: 'Indigenous Tribe'
    permission: 'Permission'
    l0: 'General'
    l1: 'Departmental'
    l2: 'Organizational'
    school-team: 'School Team Member'
    team: 'Teams'
    tw-only: Taiwan Citizens Only
    tw-student-only: Taiwan Students Only
    save: 'Save'
    select: 'Select'
    password: 'Password'
    num-in-dept: 'No.'
    sport: 'Sports'
  zh-TW:
    setting: '設定'
    language: '語言'
    account-setting: '帳號設定'
    chinese-name: '中文姓名'
    english-name: '姓名英文拼音'
    edit: '編輯'
    avatar: '大頭照'
    first-name-ch: '中文名字'
    last-name-ch: '中文姓氏'
    first-name-en: '英文名字'
    last-name-en: '英文姓氏'
    account: '帳號'
    athlete-id: '選手代碼'
    organization: '所屬組織單位'
    department: '所屬分部/系所'
    nationality: '國籍'
    unified-id: '身分證/居留證號'
    is-student: '學生身份'
    yes: '是'
    no: '否'
    school-id: '學號/教職員編號'
    grade: '年級'
    birthday: '生日'
    city: '戶籍所在城市'
    address: '居住地址'
    cellphone: '手機號碼'
    telephone: '電話號碼'
    emergency-contact: '緊急聯絡人'
    emergency-phone: '聯絡人電話'
    sex: '生理性別'
    height: '身高'
    weight: '體重'
    male: '男'
    female: '女'
    others: '其他'
    blood: '血型'
    sport-gifited: '體育績優身份'
    sport-gifited-item: '體育績優項目'
    indigenous: '原住民身份'
    tribe: '所屬族群部落'
    permission: '權限'
    l0: '一般使用者'
    l1: '分部管理員'
    l2: '組織管理員'
    school-team: '校隊身份'
    team: '所屬校隊'
    tw-only: 限本國籍
    tw-student-only: 限本國學生
    save: '儲存'
    select: '選擇'
    password: '密碼'
    num-in-dept: '座號'
    sport: '專長項目'
</i18n>