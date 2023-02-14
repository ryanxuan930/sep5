<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { QuillEditor } from '@vueup/vue-quill'
  import '@vueup/vue-quill/dist/vue-quill.snow.css';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);

  const orgList: any = ref([]);
  vr.Get('organization', orgList);
  const sportList: any = ref([]);
  vr.Get('sport', sportList);

  const data: any = reactive({
    team_name_ch: '',
    team_name_en: '',
    sport_id: 1,
    org_id: 0,
    team_info: ''
  }); 

  let userData: any = null;
  async function getUserData() {
    userData = await vr.Get('auth/admin/info', null, true, true);
    if (props.inputData == null) {
      data.org_id = userData.related_user_org_id;
    }
  }
  getUserData();

  if (props.inputData !== null) {
    Object.keys(data).forEach((index: string) => {
      data[index] = props.inputData[index];
    });
    if (data.team_info == null) {
      data.team_info = '';
    }
  }

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  function submitAll() {
    if (data.team_name_ch.length === 0) {
      alert('請輸入中文名稱');
      return;
    }
    if (data.team_name_en.length === 0) {
      alert('請輸入英文名稱');
      return;
    }
    const temp = JSON.parse(JSON.stringify(data));
    temp.options = JSON.stringify(temp.options);
    if (props.inputData === null) {
      vr.Post(`${userData.related_user_org_id}/school-team`, temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    } else {
      vr.Patch(`${userData.related_user_org_id}/school-team/${props.inputData.school_team_id}`, temp, null, true, true).then( (res: any) => {
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
    <label class="round-input-label md:col-span-4">
      <div class="title">中文名稱*</div>
      <input class="input" type="text" v-model="data.team_name_ch">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">英文名稱*</div>
      <input class="input" type="text" v-model="data.team_name_en">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">所屬單位</div>
      <select class="select" v-model="data.org_id" :disabled="store.userInfo.admin_org_id > 1">
        <template v-for="(item, index) in orgList" :key="index">
          <option :value="item.org_id">{{ item.org_name_full_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">運動項目</div>
      <select class="select" v-model="data.sport_id">
        <template v-for="(item, index) in sportList" :key="index">
          <option :value="item.sport_id">{{ item.sport_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">介紹頁面</div>
      <div>
        <QuillEditor theme="snow" toolbar="full" v-model:content="data.team_info" :contentType="'html'"></QuillEditor>
      </div>
    </label>
    <div class="md:col-span-4">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>