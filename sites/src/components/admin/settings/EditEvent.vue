<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallLoader from '@/components/SmallLoader.vue';
  import EventSelector from '@/components/admin/module/EventSelector.vue';
  import SmallModal from '@/components/SmallModal.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const isLoading = ref(false);
  const displayModal = ref(false);
  const props: any = defineProps(['inputData']);
  const data: any = reactive({
    event_id: 0,
    sport_id: 1,
    event_code: '',
    event_ch: '',
    event_en: '',
    event_jp: '',
    event_abbr: '',
    unit: '0', // T D P 0
    display: 1,
    built_in: 0,
    multiple: 0,
    combined: 0,
    combined_list: [],
    created_by_dept: store.userInfo.admin_dept_id,
    player_num: 1,
    remarks: '',
  });
  if (props.inputData !== null) {
    Object.keys(data).forEach((index: string) => {
      data[index] = props.inputData[index];
    })
  }

  const deptData: any = ref({});
  const sportList: any = ref([]);
  const eventList: any = ref([]);
  (async () => {
    isLoading.value = true;
    await vr.Get('sport', sportList);
    await vr.Get('event', eventList);
    await vr.Get(`admin/admin-dept/${store.userInfo.admin_dept_id}`, deptData, true, true);
    if (Array.isArray(deptData.value.sport_management_list)) {
      if (deptData.value.sport_management_list.length > 0) {
        data.sport_id = deptData.value.sport_management_list[0];
      }
    }
    if (data.combined_list == null) {
      data.combined_list = [];
    }
    isLoading.value = false;
  })();
  
  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  async function submitAll() {
    if (data.event_ch.length == 0) {
      alert('請輸入項目中文名稱');
      return;
    }
    if (data.event_en.length == 0) {
      alert('請輸入項目英文名稱');
      return;
    }
    if (data.event_abbr.length == 0) {
      alert('請輸入項目縮寫');
      return;
    }
    if (data.player_num <= 0) {
      alert('最低參賽人數須至少一人');
      return;
    }
    let res: any = null;
    const temp = JSON.parse(JSON.stringify(data));
    temp.combined_list = JSON.stringify(temp.combined_list);
    delete temp.event_id;
    if (props.inputData == null) {
      res = await vr.Post('event', temp, null, true, true);
    } else {
      res = await vr.Patch(`event/${data.event_id}`, temp, null, true, true);
    }
    if (res.status == 'A01') {
      alert('已儲存');
      close();
    } else {
      alert('操作失敗');
    }
  }
</script>

<template>
  <div class="overflow-auto h-full grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2" v-if="!isLoading">
    <label class="round-input-label md:col-span-2">
      <div class="title">項目中文*</div>
      <input type="text" class="input" v-model="data.event_ch" :disabled="data.built_in == 1">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">項目英文*</div>
      <input type="text" class="input" v-model="data.event_en" :disabled="data.built_in == 1">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">項目日文</div>
      <input type="text" class="input" v-model="data.event_jp" :disabled="data.built_in == 1">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">項目縮寫*</div>
      <input type="text" class="input" v-model="data.event_abbr" :disabled="data.built_in == 1">
    </label>
    <label class="round-input-label">
      <div class="title">計算單位</div>
      <select class="select" v-model="data.unit" :disabled="data.built_in == 1">
        <option value="0">不適用</option>
        <option value="T">時間</option>
        <option value="D">距離</option>
        <option value="P">積分</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">顯示</div>
      <select class="select" v-model="data.display" :disabled="data.built_in == 1">
        <option value="1">是</option>
        <option value="0">否</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">團體項目</div>
      <select class="select" v-model="data.multiple" :disabled="data.built_in == 1">
        <option value="1">是</option>
        <option value="0">否</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">最低參賽人數</div>
      <input type="number" class="input" min="1" v-model.number="data.player_num" :disabled="data.built_in == 1">
    </label>
    <label class="round-input-label">
      <div class="title">運動類型</div>
      <select class="select" v-model="data.sport_id" :disabled="data.built_in == 1">
        <template v-for="(item, index) in sportList" :key="index">
          <option :disabled="!deptData.sport_management_list.includes(item.sport_id)" :value="item.sport_id">{{ item.sport_name_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">複合項目</div>
      <select class="select" v-model="data.combined" :disabled="data.built_in == 1">
        <option value="1">是</option>
        <option value="0">否</option>
      </select>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">複合項目</div>
      <div class="flex items-center gap-5">
        <button class="general-button blue block flex-shrink-0" @click="displayModal = true">選取</button>
        <div class="flex-grow flex overflow-auto gap-3 p-1 bg-gray-50">
          <template v-for="(item, index) in eventList" :key="index">
            <div class="flex-shrink-0 py-0.5 px-2 border-2 rounded bg-white" v-if="data.combined_list.includes(item.event_code)">{{ item.event_ch }}</div>
          </template>
        </div>
      </div>
    </label>
    <div class="round-input-label md:col-span-4">
      <button class="round-full-button blue mt-5" @click="submitAll" v-if="data.built_in == 0 || store.userInfo.permission == 9">儲存</button>
      <div v-else class="input bg-gray-100">內建項目不可變更</div>
    </div>
  </div>
  <SmallModal v-show="displayModal" @closeModal="displayModal = false">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal">頁面設定</div>
      </div>
    </template>
    <template v-slot:content>
      <EventSelector v-if="displayModal" @closeModal="displayModal = false" :input-data="data.combined_list" @returnData="(input: any) => { data.combined_list = input;}"></EventSelector>
    </template>
  </SmallModal>
  <SmallLoader v-show="isLoading"></SmallLoader>
</template>

<style scoped lang="scss">
    
</style>