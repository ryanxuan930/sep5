<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallModal from '@/components/SmallModal.vue';
  import SchoolTeamSelector from '@/components/registration/module/SchoolTeamSelector.vue';
  import { useI18n } from 'vue-i18n';
  import Config from '@/assets/config.json';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);
  const displayModal = ref(false);

  const optionsPrototype = {};
  const data: any = reactive({
    account: '',
    first_name_ch: '',
    last_name_ch: '',
    first_name_en: '',
    last_name_en: '',
    org_code: '',
    is_student: 0,
    student_id: '',
    dept_id: 0,
    grade: 0,
    unified_id: '',
    birthday: '2000-01-01',
    nationality: 'TW',
    is_indigenous: 0,
    indigenous_tribe_id: 0,
    is_sport_gifited: 0,
    gifited_sport_id: 0,
    is_school_team: 0,
    school_team_id_list: [],
    sex: 0,
    height: 0.00,
    weight: 0.00,
    blood_type: '',
    cellphone: '',
    telephone: '',
    household_city_code: '',
    address: '',
    emergency_contact: '',
    emergency_phone: '',
    options: optionsPrototype,
    avatar: '',
    permission: 0,
  });
  Object.keys(data).forEach((index: string) => {
    data[index] = props.inputData[index];
  });
  if (data.dept_id === null) {
    data.dept_id = 0;
  }
  if (data.grade === null) {
    data.grade = 0;
  }
  if (data.school_team_id_list === null){
    data.school_team_id_list = [];
  }
  data.height /= 100;
  data.weight /= 100;
  if (data.options === null) {
    data.options = optionsPrototype;
  }

  const countryList: any = ref(null);
  vr.Get('country', countryList);
  const cityList: any = ref(null);
  vr.Get('city', cityList);
  const orgList: any = ref([]);
  vr.Get('organization', orgList);
  const deptList: any = ref([]);
  function getDeptList(orgCode: string) {
    vr.Get(`department/org/code/${orgCode}`, deptList, true, true);
  }
  vr.Get(`department/org/code/${store.userInfo.org_code}`, deptList, true, true);
  const sportList: any = ref(null);
  vr.Get('sport', sportList);
  const tribeList: any = ref(null);
  vr.Get('tribe', tribeList);
  const teamList: any = ref(null);
  vr.Get(`${props.inputData.org_id}/school-team`, teamList, true, true);

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  async function submitAll() {
    if (data.last_name_ch.length === 0) {
      alert('請輸入中文姓氏');
      return;
    }
    if (data.first_name_ch.length === 0) {
      alert('請輸入中文名字');
      return;
    }
    const temp = JSON.parse(JSON.stringify(data));
    temp.school_team_id_list = JSON.stringify(temp.school_team_id_list);
    temp.options = JSON.stringify(temp.options);
    temp.height *= 100;
    temp.weight *= 100;
    await vr.Patch(`user/${store.userInfo.u_id}`, temp, null, true, true).then( (res: any) => {
      if (res.status !== 'A01') {
        alert('無法儲存 Error');
        return;
      }
    });
    const info = await vr.Get('auth/user/info/pure', null, true, true);
    store.userInfo = info;
    alert('已儲存 Done');
    close();
  }
  const { t, locale } = useI18n({
    inheritLocale: true,
    useScope: 'local'
  });
</script>

<template>
  <div class="h-full overflow-auto grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2">
    <div class="round-input-label flex flex-col md:row-span-4">
      <div class="title">{{ t('avatar') }}</div>
      <div v-if="data.avatar == null" class="p-5 text-center text-gray-300">目前無照片</div>
    </div>
    <label class="round-input-label md:col-span-3">
      <div class="title">{{ t('last-name-ch') }}*</div>
      <input class="input" type="text" v-model="data.last_name_ch" maxlength="64">
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">{{ t('first-name-ch') }}*</div>
      <input class="input" type="text" v-model="data.first_name_ch" maxlength="16">
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">{{ t('last-name-en') }}</div>
      <input class="input" type="text" v-model="data.last_name_en" maxlength="128">
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">{{ t('first-name-en') }}</div>
      <input class="input" type="text" v-model="data.first_name_en" maxlength="128">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('account') }}</div>
      <div class="input disabled">{{ data.account }}</div>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('athlete-id') }}</div>
      <div class="input disabled">{{ store.userInfo.athlete_id }}</div>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('permission') }}</div>
      <select class="select" v-model="data.permission" v-if="store.userInfo.permission >= 2  && store.userInfo.org_code != 'O0000'">
        <option value="0">{{ t('l0')}}</option>
        <option value="1">{{ t('l1') }}</option>
        <option value="2">{{ t('l2') }}</option>
      </select>
      <div class="input disabled" v-else>
        <span v-show="data.permission == 0">{{ t('l0')}}</span>
        <span v-show="data.permission == 1">{{ t('l1') }}</span>
        <span v-show="data.permission == 2">{{ t('l2') }}</span>
      </div>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('organization') }}</div>
      <select class="select" v-model="data.org_code" v-if="store.userInfo.permission >= 2 && store.userInfo.org_code != 'O0000'" @change="getDeptList(data.org_code)">
        <template v-for="(item, index) in orgList" :key="index">
          <option :value="item.org_code">
            <template v-if="locale == 'zh-TW'">{{ item.org_name_full_ch }}</template>
            <template v-else>{{ item.org_name_full_en }}</template>
          </option>
        </template>
      </select>
      <div class="input disabled" v-else>
        <template v-for="(item, index) in orgList" :key="index">
          <span v-if="item.org_code == data.org_code">
            <template v-if="locale == 'zh-TW'">{{ item.org_name_full_ch }}</template>
            <template v-else>{{ item.org_name_full_en }}</template>
          </span>
        </template>
      </div>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title" v-if="Config.deptAsClass">{{ t('class') }}</div>
      <div class="title" v-else>{{ t('department') }}</div>
      <select class="select" v-model="data.dept_id" v-if="store.userInfo.permission >= 1 && Config.deptAsClass == false && store.userInfo.org_code != 'O0000'">
        <option value="0">---</option>
        <template v-for="(item, index) in deptList" :key="index">
          <option :value="item.dept_id">
            <template v-if="locale == 'zh-TW'">{{ item.dept_name_ch }}</template>
            <template v-else>{{ item.dept_name_en }}</template>
          </option>
        </template>
      </select>
      <div class="input disabled">
        <template v-for="(item, index) in deptList" :key="index">
          <span v-if="item.dept_id == data.dept_id">
            <template v-if="locale == 'zh-TW'">{{ item.dept_name_ch }}</template>
            <template v-else>{{ item.dept_name_en }}</template>
          </span>
        </template>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('nationality') }}</div>
      <select class="select" v-model="data.nationality">
        <template v-for="(item, index) in countryList" :key="index">
          <option :value="item.country_code">
            <template v-if="locale == 'zh-TW'">{{ item.country_name_ch }}</template>
            <template v-else>{{ item.country_name_en }}</template>
          </option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('unified-id') }}</div>
      <input class="input" type="text" placeholder="A123456789" v-model="data.unified_id" maxlength="10">
    </label>
    <label class="round-input-label" v-if="data.org_code.substring(0, 1) !== 'O'">
      <div class="title">{{ t('is-student') }}</div>
      <div class="input disabled">{{ data.is_student == 1 ? t('yes') : t('no') }}</div>
    </label>
    <label class="round-input-label" v-if="data.org_code.substring(0, 1) !== 'O'">
      <div class="title">{{ t('school-id') }}</div>
      <input class="input" type="text" v-model="data.student_id" maxlength="16">
    </label>
    <label class="round-input-label" v-if="data.org_code.substring(0, 1) !== 'O'">
      <div class="title">{{ t('grade') }}</div>
      <select class="select" v-model="data.grade" :disabled="data.grade == 0 || Config.deptAsClass == true" >
        <option value="0">無 N/A</option>
        <option value="1">小一 Grade 1</option>
        <option value="2">小二 Grade 2</option>
        <option value="3">小三 Grade 3</option>
        <option value="4">小四 Grade 4 </option>
        <option value="5">小五 Grade 5</option>
        <option value="6">小六 Grade 6</option>
        <option value="7">國一 Grade 7</option>
        <option value="8">國二 Grade 8</option>
        <option value="9">國三 Grade 9</option>
        <option value="10">高一 Grade 10</option>
        <option value="11">高二 Grade 11</option>
        <option value="12">高三 Grade 12</option>
        <option value="21">大一 B.D. 1</option>
        <option value="22">大二 B.D. 2</option>
        <option value="23">大三 B.D. 3</option>
        <option value="24">大四 B.D. 4</option>
        <option value="25">大五 B.D. 5</option>
        <option value="26">大六 B.D. 6</option>
        <option value="27">大七 B.D. 7</option>
        <option value="28">大八 B.D. 8</option>
        <option value="31">碩一 M.D. 1</option>
        <option value="32">碩二 M.D. 2</option>
        <option value="33">碩三 M.D. 3</option>
        <option value="34">碩四 M.D. 4</option>
        <option value="41">博一 Ph.D. 1</option>
        <option value="42">博二 Ph.D. 2</option>
        <option value="43">博三 Ph.D. 3</option>
        <option value="44">博四 Ph.D. 4</option>
        <option value="45">博五 Ph.D. 5</option>
        <option value="46">博六 Ph.D. 6</option>
        <option value="47">博七 Ph.D. 7</option>
        <option value="35">二專一</option>
        <option value="36">二專二</option>
        <option value="37">二技一</option>
        <option value="38">二技二</option>
        <option value="61">四技一</option>
        <option value="62">四技二</option>
        <option value="63">四技三</option>
        <option value="64">四技四</option>
        <option value="51">五專一</option>
        <option value="52">五專二</option>
        <option value="53">五專三</option>
        <option value="54">五專四</option>
        <option value="55">五專五</option>
        <option value="71">七技一</option>
        <option value="72">七技二</option>
        <option value="73">七技三</option>
        <option value="74">七技四</option>
        <option value="75">七技五</option>
        <option value="76">七技六</option>
        <option value="77">七技七</option>
        <option value="98">延畢 Delay graduation</option>
        <option value="99">畢業 Graduate</option>
      </select>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('address') }}</div>
      <input class="input" type="text" v-model="data.address" maxlength="128">
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('city') }}</div>
      <select class="select" v-model="data.household_city_code">
        <option value="null">---</option>
        <template v-for="(item, index) in cityList" :key="index">
          <option :value="item.city_code">
            <template v-if="locale == 'zh-TW'">{{ item.city_name_ch }}</template>
            <template v-else>{{ item.city_name_en }}</template>
          </option>
        </template>
      </select>
      <div class="text-sm mx-4 text-gray-500">{{ t('tw-only') }}</div> 
    </label>
    <label class="round-input-label basis-3/4">
      <div class="title">{{ t('cellphone') }}</div>
      <input class="input" type="text" placeholder="0987654321" v-model="data.cellphone" maxlength="16">
    </label>
    <label class="round-input-label basis-3/4">
      <div class="title">{{ t('telephone') }}</div>
      <input class="input" type="text" placeholder="(02) 12345678" v-model="data.telephone" maxlength="16">
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('emergency-contact') }}</div>
      <input class="input" type="text" v-model="data.emergency_contact" maxlength="64">
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('emergency-phone') }}</div>
      <input class="input" type="text" placeholder="0987654321" v-model="data.emergency_phone" maxlength="16">
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('sex') }}</div>
      <select class="select" v-model="data.sex">
        <option value="0">{{ t('others') }}</option>
        <option value="1">{{ t('male') }}</option>
        <option value="2">{{ t('female') }}</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('height') }} (hhh.hh) cm</div>
      <input class="input" type="number" v-model.number="data.height">
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('weight') }} (w.ww) kg</div>
      <input class="input" type="number" v-model.number="data.weight">
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('birthday') }}</div>
      <input class="input" type="date" v-model="data.birthday">
    </label>
    <label class="round-input-label" v-if="data.org_code.substring(0, 1) !== 'O'">
      <div class="title">{{ t('sport-gifited') }}</div>
      <div class="input disabled">{{ data.is_sport_gifited == 1 ? t('yes') : t('no') }}</div>
    </label>
    <label class="round-input-label" v-if="data.org_code.substring(0, 1) !== 'O'">
      <div class="title">{{ t('sport-gifited-item') }}</div>
      <div class="input disabled">
        <template v-for="(item, index) in sportList" :key="index">
          <span v-if="item.sport_id == data.gifited_sport_id">
            <template v-if="locale == 'zh-TW'">{{ item.sport_name_ch }}</template>
            <template v-else>{{ item.sport_name_en }}</template>
          </span>
        </template>
        <span v-if="data.is_sport_gifited == 0">---</span>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('indigenous') }}</div>
      <select class="select" v-model="data.is_indigenous">
        <option value="0">{{ t('no') }}</option>
        <option value="1">{{ t('yes') }}</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('tribe') }}</div>
      <select :disabled="data.is_indigenous == 0" class="select" v-model="data.indigenous_tribe_id">
        <option value="0">---</option>
        <template v-for="(item, index) in tribeList" :key="index">
          <option :value="item.tribe_id">
            <template v-if="locale == 'zh-TW'">{{ item.tribe_name_ch }}</template>
            <template v-else>{{ item.tribe_name }}</template>
          </option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('blood') }}</div>
      <input class="input" type="text" v-model="data.blood_type" maxlength="4">
    </label>
    <label class="round-input-label" v-if="data.org_code.substring(0, 1) !== 'O'">
      <div class="title">{{ t('school-team') }}</div>
      <div class="input disabled">{{ data.is_school_team == 1 ? t('yes') : t('no') }}</div>
    </label>
    <div class="round-input-label md:col-span-2" v-if="data.org_code.substring(0, 1) !== 'O'">
      <div class="title">{{ t('team') }}</div>
      <div class="team-list">
        <template v-for="(item, index) in teamList" :key="index">
          <div v-if="data.school_team_id_list.includes(item.school_team_id)">
            <template v-if="locale == 'zh-TW'">{{ item.team_name_ch }}</template>
            <template v-else>{{ item.team_name_en }}</template>
          </div>
        </template>
      </div>
    </div>
    <div class="md:col-span-4">
      <button class="round-full-button blue" @click="submitAll">{{ t('save') }}</button>
    </div>
    <SmallModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div>{{ t('team') }}</div>
        </div>
      </template>
      <template v-slot:content>
        <SchoolTeamSelector v-if="displayModal" :input-data="data.school_team_id_list" :org-id="props.inputData.org_id" @returnData="(input: number[]) => { data.school_team_id_list = input;}" @closeModal="displayModal = false"></SchoolTeamSelector>
      </template>
    </SmallModal>
  </div>
</template>

<style scoped lang="scss">
.team-list {
  @apply flex gap-3;
  div {
    @apply border-2 rounded-full py-1 px-4;
  }
}
.input.disabled {
  @apply text-gray-600 bg-gray-100;
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
    class: 'Class'
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
    class: '班級'
</i18n>