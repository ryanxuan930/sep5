<script setup lang="ts">
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import FullModal from '@/components/FullModal.vue';
  import AddFile from '@/components/admin/file/AddFile.vue';
  import SmallLoader from '@/components/SmallLoader.vue';
  import Config from '@/assets/config.json';

  const router = useRouter();
  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);
  const fileList: any = ref(null);

  function getFileList() {
    vr.Get('file-list', fileList, true, true);
  }
  getFileList();
  async function remove(id: number) {
    const r = confirm('確定刪除？');
    if (r) {
      const res = await vr.Delete(`file-list/${id}`, null, true, true);
      if (res.status == 'A01') {
        alert('已刪除');
        getFileList();
      } else {
        alert('無法刪除');
      }
    }
  }
</script>

<template>
  <div class="main-box">
    <div class="flex">
      <div class="flex-grow text-3xl font-medium mb-5">文檔管理</div>
    </div>
    <table v-if="fileList!= null">
      <tr>
        <th class="w-[15%]">編號</th>
        <th class="w-[50%]">檔名</th>
        <th class="w-[20%]">格式</th>
        <th class="w-[15%]">
          <a class="hyperlink blue" @click="displayModal = true">新增</a>
        </th>
      </tr>
      <template v-if="fileList.length > 0">
        <template v-for="(item, index) in fileList" :key="index">
          <tr>
            <td>{{ index + 1 }}</td>
            <td>{{ item.file_name }}</td>
            <td>{{ item.format }}</td>
            <td class="flex gap-5">
              <a :href="`${Config.resourceUrl}${item.path}`" target="_blank" class="hyperlink blue" rel="noopener noreferrer">開啟</a>
              <a class="hyperlink red" @click="remove(item.file_id)">刪除</a>
            </td>
          </tr>
        </template>
      </template>
      <tr v-else>
        <td colspan="5" class="text-center">目前無檔案</td>
      </tr>
    </table>
    <SmallLoader v-show="fileList == null"></SmallLoader>
    <FullModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal">新增檔案</div>
        </div>
      </template>
      <template v-slot:content>
        <AddFile v-if="displayModal" @refreshPage="getFileList" @closeModal="displayModal = false"></AddFile>
      </template>
    </FullModal>
  </div>
</template>

<style scoped lang="scss">
.page-btn {
  @apply flex my-5 gap-3;
}
.general-button.active {
    @apply bg-blue-400;
  }
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
<style src="@vueform/toggle/themes/default.css"></style>