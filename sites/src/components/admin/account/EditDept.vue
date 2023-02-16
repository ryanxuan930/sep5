<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import Config from '@/assets/config.json';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);

  const orgList: any = ref([]);
  vr.Get('organization', orgList);

  const data: any = reactive({
    related_org_id: 0,
    dept_name_ch: '',
    dept_name_en: '',
    sort_order: 0,
    has_grade: 0,
    grade: 0,
    options: {
      isClass: Config.deptAsClass, // 是班級
      classType: 0, // 班級類型 0 混合 1 男生 2 女生
    },
  }); 
  if (props.inputData !== null) {
    Object.keys(data).forEach((index: string) => {
      data[index] = props.inputData[index];
    })
    if (data.options === null) {
      data.options = {
        isClass: Config.deptAsClass,
        classType: 0,
      };
    }
  }

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  function submitAll() {
    if (data.dept_name_ch.length === 0) {
      alert('請輸入中文名稱');
      return;
    }
    const temp = JSON.parse(JSON.stringify(data));
    temp.options = JSON.stringify(temp.options);
    if (props.inputData === null) {
      vr.Post('department', temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    } else {
      vr.Patch(`department/${props.inputData.dept_id}`, temp, null, true, true).then( (res: any) => {
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
      <input class="input" type="text" v-model="data.dept_name_ch">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">英文名稱</div>
      <input class="input" type="text" v-model="data.dept_name_en">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">所屬單位</div>
      <select class="select" v-model="data.related_org_id" :disabled="store.userInfo.permission < 3">
        <template v-for="(item, index) in orgList" :key="index">
          <option :value="item.org_id">{{ item.org_name_full_ch }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">排序</div>
      <input class="input" type="number" v-model="data.sort_order">
    </label>
    <label class="round-input-label">
      <div class="title">有無年級</div>
      <select class="select" v-model="data.has_grade">
        <option value="0">無</option>
        <option value="1">有</option>
      </select>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">年級</div>
      <input class="input" type="number" v-model="data.grade" :disabled="data.has_grade == 0">
    </label>
    <template v-if="Config.deptAsClass">
      <label class="round-input-label md:col-span-2">
        <div class="title">是否為班級</div>
        <select class="select" v-model="data.options.isClass">
          <option value="false">否</option>
          <option value="true">是</option>
        </select>
      </label>
      <label class="round-input-label md:col-span-2">
        <div class="title">班級類型</div>
        <select class="select" v-model="data.options.classType">
          <option value="1">男生班</option>
          <option value="2">女生班</option>
          <option value="0">混合班</option>
        </select>
      </label>
    </template>
    <div class="md:col-span-4">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>