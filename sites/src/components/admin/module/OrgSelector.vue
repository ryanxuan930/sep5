<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallModal from '@/components/SmallModal.vue';
  import SmallLoader from '@/components/SmallLoader.vue';
  import DeptSelector from '@/components/admin/module/DeptSelector.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const props = defineProps(['selectedData']);
  const displayModal = ref(false);

  const orgList: any = ref(null);
  function getOrgList() {
    vr.Get('organization', orgList, true, true).then(() => {
      for (let i = 0; i < orgList.value.length; i++) {
        orgList.value[i].temp = {
          org_id: orgList.value[i].org_id,
          dept_list: [],
        }
        for (let j = 0; j < props.selectedData.length; j++) {
          if (props.selectedData[j].org_id === orgList.value[i].org_id) {
            orgList.value[i].temp.dept_list = props.selectedData[j].dept_list;
          }
        }
      }

    })
  }
  getOrgList();

  const opened: any = ref({});
  const openIndex = ref(0);
  function open(index: number, input: any) {
    openIndex.value = index;
    opened.value = input;
    displayModal.value = true;
  }

  const emit = defineEmits<{(e: 'returnData', value: number[]): void, (e: 'closeModal'): void}>();
  const submit = () => {
    const temp: any = [];
    orgList.value.forEach((item: any) => {
      if (item.temp.dept_list.length > 0) {
        temp.push(item.temp);
      }
    })
    emit('returnData', temp);
    emit('closeModal');
  }
</script>

<template>
  <div>
    <table>
      <tr>
        <th>單位名稱</th>
        <th></th>
      </tr>
      <template v-for="(item, index) in orgList" :key="index">
        <tr v-if="item.org_id > 1">
          <td>[{{ item.org_code }}] {{ item.org_name_full_ch }}</td>
          <td>
            <a class="hyperlink blue" @click="open(index, item.temp)">詳細</a>
          </td>
        </tr>
      </template>
    </table>
    <SmallLoader v-show="orgList == null"></SmallLoader>
    <button class="round-full-button blue mt-3" @click="submit">確定</button>
    <SmallModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal">單位列表</div>
        </div>
      </template>
      <template v-slot:content>
        <DeptSelector v-if="displayModal" :selected-data="opened" @closeModal="displayModal = false" @returnData="(input: any) => { orgList[openIndex].temp = input; }"></DeptSelector>
      </template>
    </SmallModal>
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
input[type=checkbox] {
  @apply border-0 w-full h-6;
}
.page-btn {
  @apply flex my-5 gap-3;
}
.general-button.active {
  @apply bg-blue-400;
}
</style>