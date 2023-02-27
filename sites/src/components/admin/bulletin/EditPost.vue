<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';
  import type { IPostData } from '@/components/library/interfaces';
  import Toggle from '@vueform/toggle';
  import { QuillEditor } from '@vueup/vue-quill'
  import '@vueup/vue-quill/dist/vue-quill.snow.css';
  import BulletinCategory from '@/assets/BulletinCategory.json';
  import SmallModal from '@/components/SmallModal.vue';
  import GameSelector from '@/components/admin/module/GameSelector.vue';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['postData']);
  const displayModal = ref(false);

  const currentTime = new Date();
  const data: Ref<IPostData> = ref({
    post_date: `${currentTime.getFullYear()}-${(currentTime.getMonth()+1).toString().padStart(2,'0')}-${currentTime.getDate()}`,
    category: 0,
    pinned: 0,
    created_at: '',
    updated_at: '',
    post_by: 0,
    clicks: 0,
    related_game: 0,
    related_admin_dept: 0,
    multilingual: 0,
    title_ch: '',
    title_en: '',
    content_ch: '',
    content_en: '',
    links: [],
    release: 0,
  });
  const gameData: any = ref(null);
  function getGameData(gameId: number) {
    if (gameId > 0) {
      vr.Get(`${store.userInfo.admin_org_id}/game/${data.value.related_game}`, gameData);
    } else {
      gameData.value = null;
    }
  }

  if (props.postData !== null) {
    const columnList: string[] = ['post_data', 'category', 'pinned', 'created_at', 'updated_at', 'post_by', 'clicks', 'related_game', 'related_admin_dept', 'multilingual', 'title_ch', 'title_en', 'content_ch', 'content_en', 'release'];
    columnList.forEach((index: string) => {
      data.value[index] = props.postData[index];
    });
    data.value.post_date = props.postData.post_date;
    data.value.links = props.postData.links;
    getGameData(data.value.related_game);
  }

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  function submitAll() {
    if (data.value.title_ch.length === 0) {
      alert('請輸入中文標題');
      return;
    }
    const temp = JSON.parse(JSON.stringify(data.value));
    delete temp.bulletin_id;
    delete temp.created_at;
    delete temp.updated_at;
    temp.links = JSON.stringify(temp.links);
    if (props.postData === null) {
      vr.Post(`${store.userInfo.admin_org_id}/bulletin`, temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
        close();
      });
    } else {
      vr.Patch(`${store.userInfo.admin_org_id}/bulletin/${props.postData.bulletin_id}`, temp, null, true, true).then( (res: any) => {
        if (res.status !== 'A01') {
          alert('無法儲存');
          return;
        }
        close();
      });
    }
  }
</script>

<template>
  <div class="h-full overflow-auto grid grid-cols-1 md:grid-cols-4 gap-3 shadow-inner p-2">
    <label class="round-input-label">
      <div class="title">置頂</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="否" onLabel="是" falseValue="0" trueValue="1" v-model="data.pinned"></Toggle>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">顯示</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="否" onLabel="是" falseValue="0" trueValue="1" v-model="data.release"></Toggle>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">多語言</div>
      <div class="toggle-box">
        <Toggle class="general-toggle" offLabel="否" onLabel="是" falseValue="0" trueValue="1" v-model="data.multilingual"></Toggle>
      </div>
    </label>
    <label class="round-input-label">
      <div class="title">類別</div>
      <select class="select" v-model="data.category">
        <template v-for="(item, index) in BulletinCategory" :key="index">
          <option :value="index">{{ item['zh-TW'] }}</option>
        </template>
      </select>
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">中文標題*</div>
      <input class="input" type="text" v-model="data.title_ch">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">英文標題</div>
      <input class="input" type="text" v-model="data.title_en">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">公告日期*</div>
      <input class="input" type="date" v-model="data.post_date">
    </label>
    <label class="round-input-label md:col-span-2">
      <div class="title">相關賽事</div>
      <div class="title">
        <button class="general-button blue" @click="displayModal = true">選取</button>
        <span v-if="gameData !== null" class="inline-block ml-2">[{{ gameData.sport_name_ch }}] {{ gameData.game_name_ch }}</span>
        <span v-else class="inline-block ml-2">尚未選取</span>
      </div>
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">中文內容</div>
      <div class="title">
        <QuillEditor theme="snow" toolbar="full" v-model:content="data.content_ch" :contentType="'html'"></QuillEditor>
      </div>
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">英文內容</div>
      <div class="title">
        <QuillEditor theme="snow" toolbar="full" v-model:content="data.content_en" :contentType="'html'"></QuillEditor>
      </div>
    </label>
    <label class="round-input-label md:col-span-4">
      <div class="title">相關連結</div>
      <div class="title">
        <template v-for="(item, index) in data.links" :key="index">
          <div class="flex items-center mb-2">
            <input placeholder="標題" class="block mr-2 border-2 rounded-full py-1 px-3" type="text" v-model="data.links[index].title">
            <input placeholder="網址" class="block mr-2 flex-grow border-2 rounded-full py-1 px-3" type="text" v-model="data.links[index].url">
            <div class="cursor-pointer text-2xl hover:text-gray-500 duration-200" @click="data.links.splice(index, 1)">✕</div>
          </div>
        </template>
        <div>
          <button class="general-button blue" @click="data.links.push({url:'',title:''})">新增</button>
        </div>
      </div>
    </label>
    <div class="md:col-span-4">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
    <SmallModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal">賽事列表</div>
        </div>
      </template>
      <template v-slot:content>
        <GameSelector v-if="displayModal" @closeModal="displayModal = false" @returnData="(input: number) => { data.related_game = input; getGameData(input)}"></GameSelector>
      </template>
    </SmallModal>
  </div>
</template>