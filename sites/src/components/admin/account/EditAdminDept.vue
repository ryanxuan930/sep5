<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallModal from '@/components/SmallModal.vue';
  import SportSelector from '@/components/admin/module/SportSelector.vue';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);
  const displayModal = ref(false);

  const orgList: any = ref(null);
  vr.Get('organization', orgList);
  const sportList: any = ref(null);
  vr.Get('sport', sportList);
  const adminOrgList: any = ref(null);
  vr.Get('admin/admin-org', adminOrgList, true, true);

  const data: any = reactive({
    admin_dept_name_ch: '',
    admin_dept_name_en: '',
    sport_management_list: [],
    admin_org_id: store.userInfo.admin_org_id,
  }); 
  if (props.inputData !== null) {
    Object.keys(data).forEach((index: string) => {
      data[index] = props.inputData[index];
    });
    if (data.sport_management_list === null) {
      data.sport_management_list = [];
    }
  }

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  function submitAll() {
    if (data.admin_dept_name_ch.length === 0) {
      alert('請輸入中文名稱');
      return;
    }
    if (data.admin_dept_name_en.length === 0) {
      alert('請輸入英文名稱');
      return;
    }
    const temp = JSON.parse(JSON.stringify(data));
    temp.sport_management_list = JSON.stringify(temp.sport_management_list);
    if (props.inputData === null) {
      vr.Post('admin/admin-dept', temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    } else {
      vr.Patch(`admin/admin-dept/${props.inputData.admin_dept_id}`, temp, null, true, true).then( (res: any) => {
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
  <div class="overflow-auto grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2">
    <label class="round-input-label md:col-span-4">
      <div class="title">中文名稱*</div>
      <input class="input" type="text" v-model="data.admin_dept_name_ch">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">英文名稱*</div>
      <input class="input" type="text" v-model="data.admin_dept_name_en">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">所屬管理組織</div>
      <select class="select" v-model="data.admin_org_id" :disabled="store.userInfo.permission <= 8">
        <template v-for="(item, index) in adminOrgList">
          <option :value="item.admin_org_id">{{ item.admin_org_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">主責運動項目</div>
      <div class="flex gap-3 items-start">
        <button class="round-full-button blue basis-1/4 flex-shrink-0" @click="displayModal = true">選擇</button>
        <div class="flex-grow flex gap-3 flex-wrap">
          <template v-for="(item, index) in sportList" :key="index">
            <div v-if="data.sport_management_list.includes(item.sport_id)" class="sport-item">{{ item.sport_name_ch }}</div>
          </template>
        </div>
      </div>
    </label>
    <div class="md:col-span-4">
      <button class="round-full-button blue my-3" @click="submitAll">儲存</button>
    </div>
    <SmallModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div>運動項目</div>
        </div>
      </template>
      <template v-slot:content>
        <SportSelector v-if="displayModal" :input-data="data.sport_management_list" @returnData="(input: number[]) => { data.sport_management_list = input;}" @closeModal="displayModal = false"></SportSelector>
      </template>
    </SmallModal>
  </div>
</template>

<style scoped lang="scss">
.sport-item {
  @apply py-1 px-2 border-2 rounded;
}
</style>