<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import Toggle from '@vueform/toggle';
  import { QuillEditor } from '@vueup/vue-quill'
  import '@vueup/vue-quill/dist/vue-quill.snow.css';
  import SmallModal from '@/components/SmallModal.vue';
  import AdminDeptSelector from '@/components/admin/module/AdminDeptSelector.vue';
  import GameTagSelector from '@/components/admin/module/GameTagSelector.vue';
  import OrgSelector from '@/components/admin/module/OrgSelector.vue';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['gameData']);
  const displayModal = ref(0);

  const optionsPrototype: any = {
    regUnit: 2,
    bannerUrl: '',
    gameLogoUrl: '',
    navHoverColor: '#3B82F6',
    navHoverSubColor: '#60A5FA',
    navTextColor: '#374151',
    regulationUrl: '',
    manualUrl: '',
  }
  const currentTime = new Date();
  const data: any = reactive({
    game_name_ch: '',
    game_name_en: '',
    game_name_jp: '',
    game_info: '',
    host_list: [],
    event_start: `${currentTime.getFullYear()}-${(currentTime.getMonth()+1).toString().padStart(2,'0')}-${currentTime.getDate()}`,
    selected: 0,
    selected_list: [],
    use_reg: 1,
    reg_url: '',
    use_manage: 1,
    manage_url: '',
    use_site: 1,
    site_url: '',
    tags: [],
    sport_code: 'athl',
    archived: 0,
    options: optionsPrototype,
  });

  if (props.gameData !== null) {
    const columnList = ['game_name_ch', 'game_name_en', 'game_name_jp', 'game_info', 'host_list', 'event_start', 'selected', 'selected_list', 'use_reg', 'reg_url', 'use_manage', 'manage_url', 'use_site', 'site_url', 'tags', 'sport_code', 'archived', 'options'];
    columnList.forEach((index: string) => {
      data[index] = props.gameData[index];
    });
    if (data.tags === null) {
      data.tags = [];
    }
    if (data.selected_list === null) {
      data.selected_list = [];
    }
    if (data.options === null) {
      data.options = optionsPrototype;
    }
    /*
    if (data.options !== null) {
      Object.keys(optionsPrototype).forEach((index: string) => {
        data.options[index] = optionsPrototype[index];
      });
    }*/
  }

  const sportList = ref();
  function getSportList() {
    vr.Get('sport', sportList);
  }
  getSportList();

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  function submitAll() {
    if (data.game_name_ch.length === 0) {
      alert('請輸入中文標題');
      return;
    }
    if (data.host_list.length === 0) {
      alert('請至少選擇一個主辦單位');
      return;
    }
    const temp = JSON.parse(JSON.stringify(data));
    temp.host_list = [];
    data.host_list.forEach((host: number) => {
      temp.host_list.push(host.toString());
    })
    temp.host_list = JSON.stringify(temp.host_list);
    temp.selected = temp.selected_list.length > 0 ? 1 : 0;
    temp.selected_list = JSON.stringify(temp.selected_list);
    temp.tags = [];
    data.tags.forEach((tag: number) => {
      temp.tags.push(tag.toString());
    })
    temp.tags = JSON.stringify(temp.tags);
    temp.options = JSON.stringify(temp.options);
    if (props.gameData === null) {
      vr.Post(`${store.userInfo.admin_org_id}/game`, temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    } else {
      vr.Patch(`${store.userInfo.admin_org_id}/game/${props.gameData.game_id}`, temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
      });
    }
    close();
  }
</script>

<template>
  <div class="h-full overflow-auto grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2">
    <label class="round-input-label md:col-span-2">
      <div class="title">中文賽事名稱*</div>
      <input class="input" type="text" v-model="data.game_name_ch">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">英文賽事名稱</div>
      <input class="input" type="text" v-model="data.game_name_en">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">日文賽事名稱</div>
      <input class="input" type="text" v-model="data.game_name_jp">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">開始日期</div>
      <input class="input" type="date" v-model="data.event_start">
    </label>
    <label class="round-input-label">
      <div class="title">運動項目</div>
      <select class="select" v-model="data.sport_code">
        <template v-for="(item, index) in sportList" :key="index">
          <option :value="item.sport_code">{{ item.sport_name_ch }}</option>
        </template>
      </select>
    </label>
    <span class="round-input-label">
      <div class="title">主辦單位</div>
      <button class="round-full-button blue" @click="displayModal = 1">選取</button>
    </span>
    <span class="round-input-label">
      <div class="title">賽事標籤</div>
      <button class="round-full-button blue" @click="displayModal = 2">選取</button>
    </span>
    <span class="round-input-label">
      <div class="title">報名限制</div>
      <button class="round-full-button blue" @click="displayModal = 3">選取</button>
    </span>
    <label class="round-input-label md:col-span-4">
      <div class="title">賽事簡介</div>
      <div class="title">
        <QuillEditor theme="snow" toolbar="full" v-model:content="data.game_info" :contentType="'html'"></QuillEditor>
      </div>
    </label>
    <div class="round-input-label md:col-span-4">
      <div class="title">使用報名系統</div>
      <div class="toggle-box">
        <div>
          <Toggle class="general-toggle" trueValue="1" falseValue="0" offLabel="否" onLabel="是" v-model="data.use_reg"></Toggle>
        </div>
        <input :disabled="data.use_reg == 1" type="text" placeholder="自有報名系統網址" v-model="data.reg_url">
      </div>
    </div>
    <div class="round-input-label md:col-span-4">
      <div class="title">使用管理系統</div>
      <div class="toggle-box">
        <div>
          <Toggle class="general-toggle" trueValue="1" falseValue="0" offLabel="否" onLabel="是" v-model="data.use_manage"></Toggle>
        </div>
        <input :disabled="data.use_manage == 1" type="text" placeholder="自有管理系統網址" v-model="data.manage_url">
      </div>
    </div>
    <div class="round-input-label md:col-span-4">
      <div class="title">使用網站系統</div>
      <div class="toggle-box">
        <div>
          <Toggle class="general-toggle" trueValue="1" falseValue="0" offLabel="否" onLabel="是" v-model="data.use_site"></Toggle>
        </div>
        <input :disabled="data.use_site == 1" type="text" placeholder="自有報名系統網址" v-model="data.site_url">
      </div>
    </div>
    <label class="round-input-label">
      <div class="title">報名/計分計算單位</div>
      <select class="select" v-model="data.options.regUnit">
        <option value="0">以個人計算</option>
        <option value="1">以分部/系所計算</option>
        <option value="2">以組織單位計算</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">主要配色</div>
      <label class="input">
        <input type="color" v-model="data.options.navHoverColor">
      </label>
    </label>
    <label class="round-input-label">
      <div class="title">次要配色</div>
      <label class="input">
        <input type="color" v-model="data.options.navHoverSubColor">
      </label>
    </label>
    <label class="round-input-label">
      <div class="title">文字顏色</div>
      <label class="input">
        <input type="color" v-model="data.options.navTextColor">
      </label>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">頁面橫幅照片</div>
      <input class="input" type="text" v-model="data.options.bannerUrl">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">賽事Logo</div>
      <input class="input" type="text" v-model="data.options.gameLogoUrl">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">競賽規程網址</div>
      <input class="input" type="text" v-model="data.options.regulationUrl">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">秩序冊網址</div>
      <input class="input" type="text" v-model="data.options.manualUrl">
    </label>
    <div class="md:col-span-4">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
    <SmallModal v-show="displayModal > 0" @closeModal="displayModal = 0">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal == 1">單位列表</div>
          <div v-if="displayModal == 2">標籤列表</div>
          <div v-if="displayModal == 3">組織列表</div>
        </div>
      </template>
      <template v-slot:content>
        <AdminDeptSelector v-if="displayModal == 1" :selected-data="data.host_list" @closeModal="displayModal = 0" @returnData="(input: number[]) => { data.host_list = input;}"></AdminDeptSelector>
        <GameTagSelector  v-if="displayModal == 2" :selected-data="data.tags" @closeModal="displayModal = 0" @returnData="(input: number[]) => { data.tags = input;}"></GameTagSelector>
        <OrgSelector  v-if="displayModal == 3" :selected-data="data.selected_list" @closeModal="displayModal = 0" @returnData="(input: any) => { data.selected_list = input;}"></OrgSelector>
      </template>
    </SmallModal>
  </div>
</template>

<style scoped lang="scss">
.toggle-box {
  @apply flex p-2 items-center gap-5;
  input {
    @apply py-1 px-4 rounded-full border-2 flex-grow;
  }
}
</style>