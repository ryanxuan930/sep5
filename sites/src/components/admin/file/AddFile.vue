<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallLoader from '@/components/SmallLoader.vue';
  import axios from 'axios';
  import Config from '@/assets/config.json';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const isLoading = ref(false);
  const data = reactive({
    admin_org_id: store.userInfo.admin_org_id,
    file_name: '',
    format: '',
  });

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  const fileElement: any = ref(null);
  const sizeError = ref(false);
  function upload() {
    if (data.file_name.length < 1) {
      alert('請輸入檔名');
      return;
    }
    if (fileElement.value.files[0] == undefined) {
      alert('請選擇檔案');
      return;
    }
    sizeError.value = false;
    if (fileElement.value.files[0].size > 10 * 1024 * 1024) {
      sizeError.value = true;
      return;
    }
    isLoading.value = true;
    const formData = new FormData();
    formData.append('file', fileElement.value.files[0]);
    formData.append('file_name', data.file_name);
    formData.append('format', fileElement.value.files[0].type);
    formData.append('admin_org_id', data.admin_org_id);
    axios.post(`${Config.serverUrl}file-list`, formData, {
      headers: {
        Authorization: `Bearer ${store.token}`,
        'Content-Type': 'multipart/form-data',
      },
    }).then((res: any) => {
      if (res.data.status == 'A01') {
        alert('已上傳');
        close();
      } else {
        alert('上傳失敗');
      }
      isLoading.value = false;
    })
  }
</script>

<template>
  <div>
    <label class="round-input-label">
      <div class="title">檔案名稱</div>
      <input type="text" class="input" v-model="data.file_name">
    </label>
    <label class="round-input-label">
      <div class="title">上傳檔案</div>
      <input type="file" class="input" ref="fileElement">
      <div v-if="sizeError" class="text-red-700 text-sm my-1">檔案請小於10MB</div>
    </label>
    <SmallLoader v-show="isLoading"></SmallLoader>
    <button class="round-full-button blue mt-5" @click="upload">上傳</button>
  </div>
</template>

<style scoped lang="scss">
    
</style>