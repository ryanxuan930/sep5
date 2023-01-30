<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useAdminStore } from '@/stores/admin';
  import type { Ref } from 'vue';
  import Toggle from '@vueform/toggle';
  import { QuillEditor } from '@vueup/vue-quill'
  import '@vueup/vue-quill/dist/vue-quill.snow.css';
  import SmallModal from '@/components/SmallModal.vue';

  const store = useAdminStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['gameData']);
  const displayModal = ref(false);

  const data: any = reactive({
    game_name_ch: '',
    game_name_en: '',
    game_name_jp: '',
    game_info: '',
    host_list: [],
    created_dept_id: 0,
    event_start: '',
    selected: 0,
    selected_list: [],
    use_reg: 1,
    reg_url: '',
    use_manage: 1,
    manage_url: '',
    use_site: 1,
    site_url: '',
    tags: [],
    sport_code: '',
    archived: 0,
  });

  console.log(props.gameData);
  if (props.gameData !== null) {
    const columnList = ['game_name_ch', 'game_name_en', 'game_name_jp', 'game_info', 'host_list', 'created_dept_id', 'event_start', 'selected', 'selected_list', 'use_reg', 'reg_url', 'use_manage', 'manage_url', 'use_site', 'site_url', 'tags', 'sport_code', 'archived'];
    columnList.forEach((index: string) => {
      data.value[index] = props.gameData[index];
    });
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
    <label class="round-input-label">
      <div class="title">運動項目</div>
      <select class="select">
        <template>
        </template>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">開始日期</div>
      <input class="input" type="date" v-model="data.event_start">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">主辦單位</div>
      
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">賽事標籤</div>
      
    </label>
  </div>
</template>

<style scoped lang="scss">
    
</style>
<style src="@vueform/toggle/themes/default.css"></style>