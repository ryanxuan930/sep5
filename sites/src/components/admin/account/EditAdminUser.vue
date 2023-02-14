<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallModal from '@/components/SmallModal.vue';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);
  const displayModal = ref(false);

  const optionsPrototype = {};
  const data: any = reactive({
    account: '',
    name: '',
    permission: 0,
    admin_org_id: 0,
    admin_dept_id: 0,
    options: optionsPrototype,
  });
  if (props.inputData !== null) {
    Object.keys(data).forEach((index: string) => {
      data[index] = props.inputData[index];
    });
    if (data.dept_id === null) {
      data.dept_id = 0;
    }
    if (data.options === null) {
      data.options = optionsPrototype;
    }
  }

  const adminOrgList: any = ref([]);
  vr.Get('admin/admin-org', adminOrgList, true, true);
  const adminDeptList: any = ref([]);
  vr.Get('admin/admin-dept', adminDeptList, true, true);
  (async () => {
    const userData = await vr.Get('auth/admin/info', null, true, true);
  })();

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  function submitAll() {
    if (data.name.length === 0) {
      alert('請輸入名字');
      return;
    }
    const temp = JSON.parse(JSON.stringify(data));
    temp.options = JSON.stringify(temp.options);
    if (props.inputData === null) {
      vr.Post('admin/user', temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    } else {
      vr.Patch(`admin/user/${props.inputData.admin_id}`, temp, null, true, true).then( (res: any) => {
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
  <div class="h-full overflow-auto grid grid-cols-1 md:grid-cols-2 gap-3 shadow-inner p-2">
    <label v-if="props.inputData==null" class="round-input-label">
      <div class="title">帳號</div>
      <input class="input" type="text" v-model="data.account">
    </label>
    <label v-if="props.inputData==null" class="round-input-label">
      <div class="title">密碼</div>
      <input class="input" type="password" v-model="data.password">
    </label>
    <label class="round-input-label">
      <div class="title">姓名</div>
      <input class="input" type="text" v-model="data.name">
    </label>
    <label class="round-input-label">
      <div class="title">權限</div>
      <select class="select" v-model="data.permission">
        <option value="0">一般使用者</option>
        <option value="1">分部管理員</option>
        <option value="2">單位管理員</option>
        <option value="8">系統管理員</option>
        <option value="9">超級管理員</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">所屬管理單位</div>
      <select class="select" v-model="data.admin_org_id" :disabled="store.userInfo.permission < 2">
        <template v-for="(item, index) in adminOrgList" :key="index">
          <option :value="item.admin_org_id">{{ item.admin_org_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">所屬管理分部</div>
      <select class="select" v-model="data.admin_dept_id" :disabled="store.userInfo.permission < 2">
        <template v-for="(item, index) in adminDeptList" :key="index">
          <option :value="item.admin_dept_id">{{ item.admin_dept_name_ch }}</option>
        </template>
      </select>
    </label>
    <div class="md:col-span-2">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
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