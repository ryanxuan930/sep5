<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);
  const displayModal = ref(false);

  const countryList: any = ref(null);
  vr.Get('country', countryList);
  const cityList: any = ref(null);
  vr.Get('city', cityList);

  const data: any = reactive({
    org_type: 0,
    org_name_full_ch: '',
    org_name_ch: '',
    org_name_full_en: '',
    org_name_en: '',
    logo_image: '',
    union_id: [],
    country_code: 'TW',
    zipcode: '',
    city_code: '',
    address: '',
    telephone: '',
    telephone_ex: '',
    fax: '',
    contact_name: '',
    contact_email: '',
    contact_phone: '',
    homepage: '',
    links: [],
    chairman_ch: '',
    chairman_en: '',
    chairman_image: '',
    leader_ch: '',
    leader_en: '',
    leader_image: '',
    has_dept: 1,
  }); 
  if (props.inputData !== null) {
    Object.keys(data).forEach((index: string) => {
      data[index] = props.inputData[index];
    })
  }

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  function submitAll() {
    if (data.org_name_full_ch.length === 0) {
      alert('請輸入中文全名');
      return;
    }
    if (data.org_name_ch.length === 0) {
      alert('請輸入中文簡稱');
      return;
    }
    const temp = JSON.parse(JSON.stringify(data));
    temp.union_id = JSON.stringify(temp.union_id);
    temp.links = JSON.stringify(temp.links);
    if (props.inputData === null) {
      vr.Post('organization', temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    } else {
      vr.Patch(`organization/${props.inputData.org_code}`, temp, null, true, true).then( (res: any) => {
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
    <div class="round-input-label flex flex-col md:col-span-2 md:row-span-5">
      <div class="title">Logo</div>
      <div v-if="data.logo_image == '' || data.logo_image == null" class="p-5 text-center text-gray-300">目前無照片</div>
    </div>
    <label class="round-input-label md:col-span-2">
      <div class="title">中文全名*</div>
      <input class="input" type="text" v-model="data.org_name_full_ch">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">中文簡稱*</div>
      <input class="input" type="text" v-model="data.org_name_ch">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">英文全名</div>
      <input class="input" type="text" v-model="data.org_name_full_en">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">英文簡稱</div>
      <input class="input" type="text" v-model="data.org_name_en">
    </label>
    <label class="round-input-label">
      <div class="title">單位類型</div>
      <select class="select" v-model="data.org_type">
        <option value="0">一般單位</option>
        <option value="1">大專院校</option>
        <option value="2">高級中學</option>
        <option value="3">國民中學</option>
        <option value="4">國民小學</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">有無下屬分部(例如系所)</div>
      <select class="select" v-model="data.has_dept">
        <option value="0">無</option>
        <option value="1">有</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">國家</div>
      <select class="select" v-model="data.country_code">
        <template v-for="(item, index) in countryList" :key="index">
          <option :value="item.country_code">{{ item.country_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">城市(限本國)</div>
      <select class="select" v-model="data.city_code">
        <option value="">無</option>
        <template v-for="(item, index) in cityList" :key="index">
          <option :value="item.city_code">{{ item.city_name_ch }}</option>
        </template>
      </select>
    </label>
    <div class="md:col-span-2 flex gap-3">
      <label class="round-input-label basis-1/4">
        <div class="title">郵遞區號</div>
        <input class="input" type="text" v-model="data.zipcode">
      </label>
      <label class="round-input-label basis-3/4">
        <div class="title">地址</div>
        <input class="input" type="text" v-model="data.address">
      </label>
    </div>
    <div class="md:col-span-2 flex gap-3">
      <label class="round-input-label basis-3/4">
        <div class="title">電話</div>
        <input class="input" type="text" v-model="data.telephone">
      </label>
      <label class="round-input-label basis-1/4">
        <div class="title">分機</div>
        <input class="input" type="text" v-model="data.telephone_ex">
      </label>
    </div>
    <label class="round-input-label">
      <div class="title">傳真</div>
      <input class="input" type="text" v-model="data.fax">
    </label>
    <label class="round-input-label">
      <div class="title">網站首頁</div>
      <input class="input" type="text" v-model="data.homepage">
    </label>
    <label class="round-input-label">
      <div class="title">聯絡人姓名</div>
      <input class="input" type="text" v-model="data.contact_name">
    </label>
    <label class="round-input-label">
      <div class="title">聯絡人電話</div>
      <input class="input" type="text" v-model="data.contact_phone">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">聯絡人Email</div>
      <input class="input" type="text" v-model="data.contact_email">
    </label>
    <div class="round-input-label md:row-span-2">
      <div class="title">首長/校長照片</div>
      <div v-if="data.chairman_image == '' || data.chairman_image == null" class="p-5 text-center text-gray-300">目前無照片</div>
    </div>
    <div class="flex flex-col gap-3">
      <label class="round-input-label">
        <div class="title">中文姓名</div>
        <input class="input" type="text" v-model="data.chairman_ch">
      </label>
      <label class="round-input-label">
        <div class="title">英文姓名</div>
        <input class="input" type="text" v-model="data.chairman_en">
      </label>
    </div>
    <div class="round-input-label md:row-span-2">
      <div class="title">領導/主任照片</div>
      <div v-if="data.leader_image == '' || data.leader_image == null" class="p-5 text-center text-gray-300">目前無照片</div>
    </div>
    <div class="flex flex-col gap-3">
      <label class="round-input-label">
        <div class="title">中文姓名</div>
        <input class="input" type="text" v-model="data.leader_ch">
      </label>
      <label class="round-input-label">
        <div class="title">英文姓名</div>
        <input class="input" type="text" v-model="data.leader_en">
      </label>
    </div>
    <div class="md:col-span-4">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>