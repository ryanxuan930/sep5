<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';
  import Toggle from '@vueform/toggle';
  import { QuillEditor } from '@vueup/vue-quill'
  import '@vueup/vue-quill/dist/vue-quill.snow.css';
  import '@vueform/toggle/themes/default.css';
  import type { IPageData } from '@/components/library/interfaces';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const currentTime = new Date();
  const optionsPrototype: IPageData = {
    homepageSlideshow: [],
    showCountdown: true,
    countdownTime: `${currentTime.getFullYear()}-${(currentTime.getMonth() + 1).toString().padStart(2, '0')}-${currentTime.getDate().toString().padStart(2, '0')}T${currentTime.getHours().toString().padStart(2, '0')}:${currentTime.getMinutes().toString().padStart(2, '0')}:${currentTime.getSeconds().toString().padStart(2, '0')}`,
    countdownTitle: '',
    registrationUrl: '',
    orgUrlTitle: {
      'zh-TW': '',
      'en-US': '',
    },
    orgUrl: '',
    footerContent: '',
    firstNavbarBackgroundColor: '#FFFFFF',
    secondNavbarBackgroundColor: '#FFFFFF',
    logoImageUrl: '',
    orgId: 0,
  }
  interface IData {
    reg_switch: number,
    reg_content: string,
    root_page: number,
    multilingual: number,
    options: IPageData,
  }
  const data: IData = reactive({
    reg_switch: 1,
    reg_content: '',
    root_page: 1,
    multilingual: 1,
    options: optionsPrototype,
  });
  const configData: any = ref(null);
  async function getConfigData() {
    await vr.Get(`config/${store.userInfo.admin_org_id}`, configData);
    Object.keys(data).forEach((index: string) => {
      data[index] = configData.value[index];
    });
    if (data.options === null) {
      data.options = optionsPrototype;
    }
  }
  getConfigData();

  const orgList: any = ref([]);
  vr.Get('organization', orgList);

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }

  function submitAll() {
    const temp = JSON.parse(JSON.stringify(data));
    vr.Patch(`config/${store.userInfo.admin_org_id}`, temp, null, true, true).then( (res: any) => {
      if (res.status !== 'A01') {
        alert('無法儲存');
        return;
      }
    });
    close();
  }
</script>

<template>
  <div class="overflow-auto h-full grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2">
    <div class="md:col-span-4 text-xl p-1">官網設定</div>
    <label class="round-input-label">
      <div class="title">組織單位官網</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="關" onLabel="開" :falseValue="0" :trueValue="1" v-model="data.root_page"></Toggle>
      </div>
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">所屬組織單位</div>
      <select class="select" v-model="data.options.orgId" :disabled="store.userInfo.admin_org_id > 1">
        <option value="0" v-if="store.userInfo.admin_org_id == 1">無</option>
        <template v-for="(item, index) in orgList" :key="index">
          <option :value="item.org_id">{{ item.org_name_full_ch }}</option>
        </template>
      </select>
    </label>
    <div class="md:col-span-4 text-xl p-1">報名系統</div>
    <label class="round-input-label">
      <div class="title">報名系統開關</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="關" onLabel="開" :falseValue="0" :trueValue="1" v-model="data.reg_switch"></Toggle>
      </div>
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">報名系統網址</div>
      <input type="text" class="input" v-model="data.options.registrationUrl">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">報名系統公告內容</div>
      <div class="title">
        <QuillEditor theme="snow" toolbar="full" v-model:content="data.reg_content" :contentType="'html'"></QuillEditor>
      </div>
    </label>
    <div class="md:col-span-4 text-xl p-1">頁面設定</div>
    <label class="round-input-label">
      <div class="title">第一層導覽列背景(待修)</div>
      <div class="input">
        <input type="color" v-model="data.options.firstNavbarBackgroundColor">
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">第二層導覽列背景(待修)</div>
      <div class="input">
        <input type="color" v-model="data.options.secondNavbarBackgroundColor">
      </div>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">導覽列Logo網址</div>
      <input type="text" class="input" v-model="data.options.logoImageUrl">
    </label>
    <label class="round-input-label">
      <div class="title">首頁連結中文</div>
      <input type="text" class="input" v-model="data.options.orgUrlTitle['zh-TW']">
    </label>
    <label class="round-input-label">
      <div class="title">首頁連結英文</div>
      <input type="text" class="input" v-model="data.options.orgUrlTitle['en-US']">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">首頁連結網址</div>
      <input type="text" class="input" v-model="data.options.orgUrl">
    </label>
    <div class="md:col-span-4">
      <table>
        <tr>
          <th>順序</th>
          <th>路徑</th>
          <th>
            <a @click="data.options.homepageSlideshow.push('')" class="hyperlink blue">新增</a>
          </th>
        </tr>
        <template v-for="(item, index) in data.options.homepageSlideshow">
          <tr>
            <td>{{ index + 1 }}</td>
            <td class="round-input-label">
              <input class="input" type="text" v-model="data.options.homepageSlideshow[index]">
            </td>
            <td>
              <a @click="data.options.homepageSlideshow.splice(index, 1)" class="hyperlink red">刪除</a>
            </td>
          </tr>
        </template>
      </table>
    </div>
    <label class="round-input-label">
      <div class="title">顯示倒數</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="關" onLabel="開" v-model="data.options.showCountdown"></Toggle>
      </div>
    </label>
    <label class="round-input-label md:col-span-3">
      <div class="title">倒數計時時間</div>
      <input type="datetime-local" class="input" v-model="data.options.countdownTime">
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">倒數計時標題</div>
      <div class="title">
        <QuillEditor theme="snow" toolbar="full" v-model:content="data.options.countdownTitle" :contentType="'html'"></QuillEditor>
      </div>
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">頁尾內容</div>
      <div class="title">
        <QuillEditor theme="snow" toolbar="full" v-model:content="data.options.footerContent" :contentType="'html'"></QuillEditor>
      </div>
    </label>
    <div class="round-input-label md:col-span-4">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium;
  }
  th {
    @apply bg-blue-100;
  }
}
</style>