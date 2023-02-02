<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);

  const orgList: any = ref(null);
  vr.Get('organization', orgList);

  const data: any = reactive({
    admin_org_name_ch: '',
    admin_org_name_en: '',
    related_user_org_id: 0,
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
    if (data.admin_org_name_ch.length === 0) {
      alert('請輸入中文名稱');
      return;
    }
    if (data.admin_org_name_en.length === 0) {
      alert('請輸入英文名稱');
      return;
    }
    const temp = JSON.parse(JSON.stringify(data));
    if (props.inputData === null) {
      vr.Post('admin/admin-org', temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    } else {
      vr.Patch(`admin/admin-org/${props.inputData.admin_org_id}`, temp, null, true, true).then( (res: any) => {
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
      <input class="input" type="text" v-model="data.admin_org_name_ch">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">英文名稱*</div>
      <input class="input" type="text" v-model="data.admin_org_name_en">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">相依使用者單位</div>
      <select class="select" v-model="data.related_user_org_id">
        <template v-for="(item, index) in orgList" :key="index">
          <option :value="item.org_id">{{ item.org_name_full_ch }}</option>
        </template>
      </select>
    </label>
    <div class="md:col-span-4">
      <button class="round-full-button blue my-3" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>