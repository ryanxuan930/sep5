<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallModal from '@/components/SmallModal.vue';
  import SchoolTeamSelector from '@/components/admin/module/SchoolTeamSelector.vue';
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
    num_in_dept: 0,
  });
  if (props.inputData !== null) {
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
  if (props.inputData !== null) {
    getDeptList(props.inputData.org_code);
  }
  const sportList: any = ref(null);
  vr.Get('sport', sportList);
  const tribeList: any = ref(null);
  vr.Get('tribe', tribeList);
  const teamList: any = ref(null);
  (async () => {
    const userData = await vr.Get('auth/admin/info', null, true, true);
    vr.Get(`${userData.related_user_org_id}/school-team`, teamList, true, true);
  })();

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  function submitAll() {
    if (data.last_name_ch.length === 0) {
      alert('請輸入中文姓氏');
      return;
    }
    if (data.first_name_ch.length === 0) {
      alert('請輸入中文名字');
      return;
    }
    data.height *= 100;
    data.weight *= 100;
    const temp = JSON.parse(JSON.stringify(data));
    temp.school_team_id_list = JSON.stringify(temp.school_team_id_list);
    temp.options = JSON.stringify(temp.options);
    if (props.inputData === null) {
      vr.Post('user', temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    } else {
      vr.Patch(`user/${props.inputData.u_id}`, temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    }
    alert('已儲存');
    close();
  }
</script>

<template>
  <div class="h-full overflow-auto grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2">
    <div class="round-input-label flex flex-col md:row-span-4">
      <div class="title">大頭照</div>
      <div v-if="data.avatar == null" class="p-5 text-center text-gray-300">目前無照片</div>
    </div>
    <label class="round-input-label md:col-span-3">
      <div class="title">中文姓氏*</div>
      <input class="input" type="text" v-model="data.last_name_ch">
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">中文名字*</div>
      <input class="input" type="text" v-model="data.first_name_ch">
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">英文姓氏拼音</div>
      <input class="input" type="text" v-model="data.last_name_en">
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">英文名字拼音</div>
      <input class="input" type="text" v-model="data.first_name_en">
    </label>
    <label v-if="props.inputData==null" class="round-input-label md:col-span-2">
      <div class="title">帳號*</div>
      <input class="input" type="text" v-model="data.account">
    </label>
    <label v-if="props.inputData==null" class="round-input-label md:col-span-2">
      <div class="title">密碼*</div>
      <input class="input" type="password" v-model="data.password">
    </label>
    <label class="round-input-label">
      <div class="title">所屬組織*</div>
      <select class="select" v-model="data.org_code" :disabled="store.userInfo.permission < 3" @change="getDeptList(data.org_code)">
        <template v-for="(item, index) in orgList" :key="index">
          <option :value="item.org_code">{{ item.org_name_full_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">所屬分部</div>
      <select class="select" v-model="data.dept_id" :disabled="store.userInfo.permission < 2">
        <option value="0">未選取</option>
        <template v-for="(item, index) in deptList" :key="index">
          <option :value="item.dept_id">{{ item.dept_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">國籍*</div>
      <select class="select" v-model="data.nationality">
        <template v-for="(item, index) in countryList" :key="index">
          <option :value="item.country_code">{{ item.country_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">身分證/居留證編號</div>
      <input class="input" type="text" placeholder="A123456789" v-model="data.unified_id">
    </label>
    <label class="round-input-label">
      <div class="title">是否為學生</div>
      <select class="select" v-model="data.is_student">
        <option value="0">否</option>
        <option value="1">是</option>
      </select>
    </label>
    <div :class="{'flex gap-3': Config.deptAsClass}">
      <label class="round-input-label flex-grow">
        <div class="title">學號/職員編號</div>
        <input class="input" type="text" v-model="data.student_id">
      </label>
      <label class="round-input-label basis-1/3">
        <div class="title">座號</div>
        <input class="input" type="text" v-model="data.num_in_dept">
      </label>
    </div>
    <label class="round-input-label">
      <div class="title">年級</div>
      <select class="select" v-model="data.grade" :disabled="data.grade == 0">
        <option value="0">無</option>
        <option value="1">小一</option>
        <option value="2">小二</option>
        <option value="3">小三</option>
        <option value="4">小四</option>
        <option value="5">小五</option>
        <option value="6">小六</option>
        <option value="7">國一</option>
        <option value="8">國二</option>
        <option value="9">國三</option>
        <option value="10">高一</option>
        <option value="11">高二</option>
        <option value="12">高三</option>
        <option value="21">大一</option>
        <option value="22">大二</option>
        <option value="23">大三</option>
        <option value="24">大四</option>
        <option value="25">大五</option>
        <option value="26">大六</option>
        <option value="27">大七</option>
        <option value="28">大八</option>
        <option value="31">碩一</option>
        <option value="32">碩二</option>
        <option value="33">碩三</option>
        <option value="34">碩四</option>
        <option value="41">博一</option>
        <option value="42">博二</option>
        <option value="43">博三</option>
        <option value="44">博四</option>
        <option value="45">博五</option>
        <option value="46">博六</option>
        <option value="47">博七</option>
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
        <option value="98">延畢</option>
        <option value="99">畢業</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">生日</div>
      <input class="input" type="date" v-model="data.birthday">
    </label>
    <label class="round-input-label">
      <div class="title">戶籍所在城市</div>
      <select class="select" v-model="data.household_city_code">
        <option value="null">無</option>
        <template v-for="(item, index) in cityList" :key="index">
          <option :value="item.city_code">{{ item.city_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">現居地址</div>
      <input class="input" type="text" v-model="data.address">
    </label>
    <label class="round-input-label basis-3/4">
      <div class="title">手機</div>
      <input class="input" type="text" placeholder="0987654321" v-model="data.cellphone">
    </label>
    <label class="round-input-label basis-3/4">
      <div class="title">家用電話(含區碼)</div>
      <input class="input" type="text" placeholder="(02) 12345678" v-model="data.telephone">
    </label>
    <label class="round-input-label">
      <div class="title">緊急聯絡人姓名</div>
      <input class="input" type="text" v-model="data.emergency_contact">
    </label>
    <label class="round-input-label">
      <div class="title">緊急聯絡人電話</div>
      <input class="input" type="text" placeholder="0987654321" v-model="data.emergency_phone">
    </label>
    <label class="round-input-label">
      <div class="title">生理性別</div>
      <select class="select" v-model="data.sex">
        <option value="0">其他</option>
        <option value="1">生理男</option>
        <option value="2">生理女</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">身高(h.hh)</div>
      <input class="input" type="number" v-model.number="data.height">
    </label>
    <label class="round-input-label">
      <div class="title">體重(w.ww)</div>
      <input class="input" type="number" v-model.number="data.weight">
    </label>
    <label class="round-input-label">
      <div class="title">血型</div>
      <input class="input" type="text" v-model="data.blood_type">
    </label>
    <label class="round-input-label">
      <div class="title">是否為體育績優生</div>
      <select class="select" v-model="data.is_sport_gifited">
        <option value="0">否</option>
        <option value="1">是</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">績優運動項目</div>
      <select class="select" v-model="data.gifited_sport_id" :disabled="data.is_sport_gifited == 0">
        <option value="0">無</option>
        <template v-for="(item, index) in sportList" :key="index">
          <option :value="item.sport_id">{{ item.sport_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">是否為原住民</div>
      <select class="select" v-model="data.is_indigenous">
        <option value="0">否</option>
        <option value="1">是</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">所屬部族</div>
      <select :disabled="data.is_indigenous == 0" class="select" v-model="data.indigenous_tribe_id">
        <option value="0">無</option>
        <template v-for="(item, index) in tribeList" :key="index">
          <option :value="item.tribe_id">{{ item.tribe_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">權限</div>
      <select class="select" v-model="data.permission">
        <option value="0">一般使用者</option>
        <option value="1">分部管理員</option>
        <option value="2">單位管理員</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">是否為校隊</div>
      <select class="select" v-model="data.is_school_team">
        <option value="0">否</option>
        <option value="1">是</option>
      </select>
    </label>
    <div class="md:col-span-2 flex gap-3">
      <div class="round-input-label">
        <div class="title">所屬校隊</div>
        <button class="round-full-button blue" @click="displayModal = true" :disabled="data.is_school_team == 0">選擇</button>
      </div>
      <div class="round-input-label">
        <div class="title">校隊列表</div>
        <div class="team-list">
          <template v-for="(item, index) in teamList" :key="index">
            <div v-if="data.school_team_id_list.includes(item.school_team_id)">{{ item.team_name_ch }}</div>
          </template>
        </div>
      </div>
    </div>
    <div class="md:col-span-4">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
    <SmallModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div>校隊列表</div>
        </div>
      </template>
      <template v-slot:content>
        <SchoolTeamSelector v-if="displayModal" :input-data="data.school_team_id_list" @returnData="(input: number[]) => { data.school_team_id_list = input;}" @closeModal="displayModal = false"></SchoolTeamSelector>
      </template>
    </SmallModal>
  </div>
</template>

<style scoped lang="scss">
.team-list {
  @apply flex gap-3;
  div {
    @apply border-2 rounded p-1;
  }
}
</style>