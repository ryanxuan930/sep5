<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import { useRoute, useRouter } from 'vue-router';
import VueRequest from '@/vue-request';
import SpinnerLoading from '@/components/SpinnerLoading.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const router = useRouter();
const route = useRoute();

const isEdit = true;
const isLoading = ref(false);
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
  has_dept: 0,
});

async function submitAll() {
  if (data.org_name_full_ch.length === 0) {
    alert('請輸入中文全名 Please input Chinese organiztion name');
    return;
  }
  if (data.org_name_ch.length === 0) {
    alert('請輸入中文簡稱 Please input Chinese organization abbreviation');
    return;
  }
  isLoading.value = true;
  const temp = JSON.parse(JSON.stringify(data));
  temp.union_id = JSON.stringify(temp.union_id);
  temp.links = JSON.stringify(temp.links);
  await vr.Post('organization-create', temp, null, true, true).then( (res: any) => {
    if (res.status !== 'A01') {
      alert('無法儲存 Failed');
      return;
    }
  });
  store.userInfo = await vr.Get('auth/user/info', null, true, true);
  isLoading.value = false;
  router.push(`/${route.params.adminOrgId}/registration`);
}
const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});
</script>

<template>
  <SpinnerLoading v-show="isLoading"></SpinnerLoading>
  <div class="main-box grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2">
    <div class="md:col-span-4 text-2xl font-medium">{{ t('org-info') }}</div>
    <div class="round-input-label flex flex-col md:col-span-2 md:row-span-4">
      <div class="title">{{ t('logo') }}</div>
      <div v-if="data.logo_image == '' || data.logo_image == null" class="p-5 text-center text-gray-300">目前無照片</div>
    </div>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('org-name-full-ch') }}*</div>
      <input class="input" :disabled="!isEdit" type="text" v-model="data.org_name_full_ch" maxlength="32">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('org-name-ch') }}*</div>
      <input class="input" :disabled="!isEdit" type="text" v-model="data.org_name_ch" maxlength="8">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('org-name-full-en') }}</div>
      <input class="input" :disabled="!isEdit" type="text" v-model="data.org_name_full_en" maxlength="128">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('org-name-en') }}</div>
      <input class="input" :disabled="!isEdit" type="text" v-model="data.org_name_en" maxlength="8">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('type') }}</div>
      <div class="input disabled">
        <span v-if="data.org_type == 0">{{ t('general') }}</span>
        <span v-if="data.org_type == 1">{{ t('university') }}</span>
        <span v-if="data.org_type == 2">{{ t('sr-high') }}</span>
        <span v-if="data.org_type == 3">{{ t('jr-high') }}</span>
        <span v-if="data.org_type == 4">{{ t('elementary') }}</span>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('has-dept') }}</div>
      <div class="input disabled">
        <span v-if="data.has_dept == 0">{{ t('no') }}</span>
        <span v-if="data.has_dept == 1">{{ t('yes') }}</span>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('country') }}</div>
      <select class="select" :disabled="!isEdit" v-model="data.country_code">
        <template v-for="(item, index) in countryList" :key="index">
          <option :value="item.country_code">
            <template v-if="locale == 'zh-TW'">{{ item.country_name_ch }}</template>
            <template v-else>{{ item.country_name_en }}</template>
          </option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('city') }}</div>
      <select class="select" :disabled="!isEdit" v-model="data.city_code">
        <option value="">{{ t('na') }}</option>
        <template v-for="(item, index) in cityList" :key="index">
          <option :value="item.city_code">
            <template v-if="locale == 'zh-TW'">{{ item.city_name_ch }}</template>
            <template v-else>{{ item.city_name_en }}</template>
          </option>
        </template>
      </select>
    </label>
    <div class="md:col-span-3 flex gap-3">
      <label class="round-input-label basis-1/4">
        <div class="title">{{ t('zipcode') }}</div>
        <input class="input" :disabled="!isEdit" type="text" v-model="data.zipcode" maxlength="6">
      </label>
      <label class="round-input-label basis-3/4">
        <div class="title">{{ t('address') }}</div>
        <input class="input" :disabled="!isEdit" type="text" v-model="data.address">
      </label>
    </div>
    <div class="md:col-span-2 flex gap-3">
      <label class="round-input-label basis-3/4">
        <div class="title">{{ t('telephone') }}</div>
        <input class="input" :disabled="!isEdit" type="text" v-model="data.telephone" maxlength="16">
      </label>
      <label class="round-input-label basis-1/4">
        <div class="title">{{ t('ex') }}</div>
        <input class="input" :disabled="!isEdit" type="text" v-model="data.telephone_ex" maxlength="8">
      </label>
    </div>
    <label class="round-input-label">
      <div class="title">{{ t('fax') }}</div>
      <input class="input" :disabled="!isEdit" type="text" v-model="data.fax" maxlength="16">
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('homepage') }}</div>
      <input class="input" :disabled="!isEdit" type="text" v-model="data.homepage" maxlength="256">
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('contact') }}</div>
      <input class="input" :disabled="!isEdit" type="text" v-model="data.contact_name" maxlength="64">
    </label>
    <label class="round-input-label">
      <div class="title">{{ t('contact-phone') }}</div>
      <input class="input" :disabled="!isEdit" type="text" v-model="data.contact_phone" maxlength="16">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('contact-email') }}</div>
      <input class="input" :disabled="!isEdit" type="text" v-model="data.contact_email" maxlength="128">
    </label>
    <div class="round-input-label md:row-span-2">
      <div class="title">{{ t('headman-img') }}</div>
      <div v-if="data.chairman_image == '' || data.chairman_image == null" class="p-5 text-center text-gray-300">目前無照片</div>
    </div>
    <div class="flex flex-col gap-3">
      <label class="round-input-label">
        <div class="title">{{ t('chinese-name') }}</div>
        <input class="input" :disabled="!isEdit" type="text" v-model="data.chairman_ch" maxlength="64">
      </label>
      <label class="round-input-label">
        <div class="title">{{ t('english-name') }}</div>
        <input class="input" :disabled="!isEdit" type="text" v-model="data.chairman_en" maxlength="128">
      </label>
    </div>
    <div class="round-input-label md:row-span-2">
      <div class="title">{{ t('director-img') }}</div>
      <div v-if="data.leader_image == '' || data.leader_image == null" class="p-5 text-center text-gray-300">目前無照片</div>
    </div>
    <div class="flex flex-col gap-3">
      <label class="round-input-label">
        <div class="title">{{ t('chinese-name') }}</div>
        <input class="input" :disabled="!isEdit" type="text" v-model="data.leader_ch" maxlength="64">
      </label>
      <label class="round-input-label">
        <div class="title">{{ t('english-name') }}</div>
        <input class="input" :disabled="!isEdit" type="text" v-model="data.leader_en" maxlength="128">
      </label>
    </div>
    <div class="md:col-span-4">
      <button class="round-full-button blue" v-if="isEdit" @click="submitAll">{{ t('save') }}</button>
      <button class="round-full-button blue-hollow" v-else @click="isEdit = true">{{ t('edit') }}</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
.disabled {
  @apply bg-gray-100;
}
</style>

<i18n lang="yaml">
  en-US:
    org-info: 'Create New Organization'
    org-code: 'Organization Code'
    org-name-full-ch: 'Organization Chinese Full Name'
    org-name-ch: 'Organization Chinese Abbreviation'
    org-name-full-en: 'Organization English Full Name'
    org-name-en: 'Organization English Abbreviation'
    edit: 'Edit'
    logo: 'Logo'
    type: 'Organization Type'
    general: 'Ganeral'
    university: 'University'
    sr-high: 'Sr. High School'
    jr-high: 'Jr. High School'
    elementary: 'Elementary School'
    has-dept: 'Has Department'
    country: 'Country / Territory'
    city: 'City'
    zipcode: 'Zipcode'
    address: 'Address'
    telephone: 'Telephone'
    ex: 'Ex.'
    fax: 'Fax'
    homepage: 'Homepage'
    contact: 'Contact Person'
    contact-phone: 'Contact Phone'
    contact-email: 'Contact Email'
    chinese-name: 'Chinese Name'
    english-name: 'English Name'
    headman-img: 'Headman Avatar'
    director-img: 'Director Avatar'
    tw-only: 'Taiwan Only'
    save: 'Save'
    yes: 'Yes'
    no: 'No'
    na: 'N/A'
  zh-TW:
    org-info: '新增組織單位'
    org-code: '組織代碼'
    org-name-full-ch: '中文全名'
    org-name-ch: '中文簡稱'
    org-name-full-en: '英文全名'
    org-name-en: '英文簡稱'
    edit: '編輯'
    logo: '圖像'
    type: '組織類型'
    general: '一般組織'
    university: '大專院校'
    sr-high: '高級中學'
    jr-high: '國民中學'
    elementary: '國民小學'
    has-dept: '有無下屬分部(如系所)'
    country: '國家'
    city: '城市'
    zipcode: '郵遞區號'
    address: '地址'
    telephone: '電話'
    ex: '分機'
    fax: '傳真'
    homepage: '首頁'
    contact: '聯絡人姓名'
    contact-phone: '聯絡人電話'
    contact-email: '聯絡人Email'
    chinese-name: '中文姓名'
    english-name: '英文姓名'
    headman-img: '首長/校長頭像'
    director-img: '主管/主任頭像'
    tw-only: '限本國'
    save: '儲存'
    yes: '有'
    no: '無'
    na: '不適用'
</i18n>