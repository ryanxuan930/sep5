<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);

  const orgData: any = ref(null);
  vr.Get(`organization/${store.userInfo.org_code}`, orgData);

  const data: any = reactive({
    related_org_id: 0,
    dept_name_ch: '',
    dept_name_en: '',
    sort_order: 0,
    has_grade: 0,
    grade: 0,
    options: {
      isClass: false, // 是班級
      classType: 0, // 班級類型 0 混合 1 男生 2 女生
      numOfPeople: 0, // 人數
    },
  }); 
  if (props.inputData !== null) {
    Object.keys(data).forEach((index: string) => {
      data[index] = props.inputData[index];
    })
    if (data.options === null) {
      data.options = {
        isClass: false,
        classType: 0,
        numOfPeople: 0,
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
    temp.related_org_id = orgData.value.org_id;
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
  const { t, locale } = useI18n({
    inheritLocale: true,
    useScope: 'local'
  });
</script>

<template>
  <div class="h-full overflow-auto grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2" v-if="orgData != null">
    <label class="round-input-label md:col-span-4">
      <div class="title">{{ t('chinese-name') }}</div>
      <input class="input" type="text" v-model="data.dept_name_ch">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">{{ t('english-name') }}</div>
      <input class="input" type="text" v-model="data.dept_name_en">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">{{ t('organization') }}</div>
      <div class="input disabled">
        <template v-if="locale == 'zh-TW'">{{ orgData.org_name_full_ch }}</template>
        <template v-else>{{ orgData.org_name_full_en }}</template>
      </div>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('has-grade') }}</div>
      <select class="select" v-model="data.has_grade">
        <option value="0">{{ t('no') }}</option>
        <option value="1">{{ t('yes') }}</option>
      </select>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">{{ t('grade') }}</div>
      <input class="input" type="number" v-model="data.grade" :disabled="data.has_grade == 0">
    </label>
    <div class="md:col-span-4">
      <button class="round-full-button blue" @click="submitAll">{{ t('save') }}</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
.input.disabled {
  @apply bg-gray-100;
}
</style>

<i18n lang="yaml">
  en-US:
    chinese-name: 'Chinese Name'
    english-name: 'English Name'
    organization: 'Organization'
    has-grade: 'Has Grade'
    grade: 'Grade'
    save: 'Save'
    yes: 'Yes'
    no: 'No'
  zh-TW:
    chinese-name: '中文名稱'
    english-name: '英文名稱'
    organization: '組織單位'
    has-grade: '有無年級資訊'
    grade: '年級'
    save: '儲存'
    yes: '有'
    no: '無'
</i18n>