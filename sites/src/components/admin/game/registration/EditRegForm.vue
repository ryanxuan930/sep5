<script setup lang="ts">
  import { ref, reactive, watch } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import SmallLoader from '@/components/SmallLoader.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const route = useRoute();
  const props = defineProps(['gameData', 'inputData']);
  const gameId = route.params.gameId;
  const gameData = props.gameData;

  const data: any = reactive({
    org_id: 0,
    dept_id: 0,
    u_id: 0,
    game_id: gameId,
    status: 1,
    remarks: '',
  });
  const orgList: any = ref([]);
  const deptList: any = ref([]);
  const userList: any = ref([]);
  const formId = ref(0);
  const isLoading = ref(false);
  async function getDeptList(orgId: number) {
    await vr.Get(`department/org/${orgId}`, deptList, true, true);
    return;
  }
  (async () => {
    isLoading.value = true;
    await vr.Get('organization', orgList);
    await getDeptList(orgList.value[1].org_id);
    await vr.Post('user-search-unit', {org_id: data.org_id, dept_id: data.dept_id}, userList, true, true);
    if (props.inputData != null) {
      Object.keys(data).forEach((index: string) => {
        data[index] = props.inputData[index];
      });
      formId.value = props.inputData.reg_form_id;
    } else {
      data.org_id = orgList.value[1].org_id
      if (deptList.value.length > 0) {
        data.dept_id = deptList.value[0].dept_id;
      }
    }
    isLoading.value = false;
  })();

  watch(data, () => {
    vr.Post('user-search-unit', {org_id: data.org_id, dept_id: data.dept_id}, userList, true, true);
  })

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }

  async function submitAll() {
    if (gameData.options.regUnit > 1) {
      data.dept_id = 0;
    }
    if (gameData.options.regUnit > 0) {
      data.u_id = 0;
    }
    if (props.inputData == null) {
      const res = await vr.Post('reg-form', data, null, true, true);
      if (res.status = 'A01') {
        alert('已儲存');
        close();
      }
    } else {
      const res = await vr.Patch(`reg-form/${formId.value}`, data, null, true, true);
      if (res.status = 'A01') {
        alert('已儲存');
        close();
      }
    }
  }
  async function remove() {
    const r = confirm('確定刪除？');
    if (r) {
      const res = await vr.Delete(`reg-form/${formId.value}`, null, true, true);
      if (res.status = 'A01') {
        alert('已刪除');
        close();
      }
    }
  }
</script>

<template>
  <div class="flex flex-col gap-3" v-if="isLoading == false">
    <label class="round-input-label">
      <div class="title">組織單位</div>
      <select class="select" v-model="data.org_id" @change="getDeptList(data.org_id)" :disabled="props.inputData != null">
        <template v-for="(item, index) in orgList" :key="index">
          <option :value="item.org_id">{{ item.org_name_full_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label" v-if="gameData.options.regUnit < 2">
      <div class="title">分部/系所</div>
      <select class="select" v-model="data.dept_id" :disabled="props.inputData != null">
        <template v-for="(item, index) in deptList" :key="index">
          <option :value="item.dept_id">{{ item.dept_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label" v-if="gameData.options.regUnit < 1">
      <div class="title">選手</div>
      <select class="select" v-model="data.u_id" :disabled="props.inputData != null">
        <template v-for="(item, index) in userList" :key="index">
          <option :value="item.u_id">{{ item.last_name_ch }}{{ item.first_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">狀態</div>
      <select class="select" v-model="data.u_id">
        <option value="1">已繳交</option>
        <option value="0">尚未處理</option>
        <option value="2">其他</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">備註</div>
      <input type="text" class="input" v-model="data.remarks">
    </label>
    <button class="round-full-button blue" @click="submitAll">儲存</button>
    <button class="round-full-button red" @click="remove">刪除</button>
  </div>
  <SmallLoader v-show="isLoading"></SmallLoader>
</template>

<style scoped lang="scss">
    
</style>