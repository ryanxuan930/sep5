<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useAdminStore } from '@/stores/admin';
  import type { Ref } from 'vue';
  import Toggle from '@vueform/toggle';

  const store = useAdminStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);
  const displayModal = ref(false);

  const countryList: any = ref(null);
  vr.Get('country', countryList);

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
</script>

<template>
  <div class="h-full overflow-auto grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2">
    <div class="round-input-label md:col-span-2 md:row-span-4">
      <div class="title">Logo</div>
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
      <div class="title">國家</div>
      <select class="select" v-model="data.country_code">
        <template v-for="(item, index) in countryList" :key="index">
          <option :value="item.country_code">{{ item.country_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">郵遞區號</div>
      <input class="input" type="text" v-model="data.zipcode">
    </label>
    <label class="round-input-label">
      <div class="title">城市(限本國)</div>
      <select class="select" v-model="data.city_code">
        <template v-for="(item, index) in countryList" :key="index">
          <option :value="item.country_code">{{ item.country_name_ch }}</option>
        </template>
      </select>
    </label>
  </div>
</template>

<style scoped lang="scss">
    
</style>