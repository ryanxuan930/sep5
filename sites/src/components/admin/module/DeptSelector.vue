<script setup lang="ts">
  import { ref, watch } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const props = defineProps(['selectedData']);
  const selectedData: any = ref(props.selectedData);
  const selectAll = ref(false);

  const deptList: any = ref(null);
  async function getDeptList() {
    await vr.Get(`department/org/${props.selectedData.org_id}`, deptList, true, true);
    if (props.selectedData.dept_list.length == deptList.value.length) {
      selectAll.value = true;
    }
  }
  getDeptList();
  
  watch(selectAll, () => {
    if (selectAll.value) {
      const temp: number[] = [];
      deptList.value.forEach((item: any) => {
        temp.push(item.dept_id);
      })
      selectedData.value.dept_list = temp;
    }
  })
  function selectAllHandler() {
    if (!selectAll.value) {
      selectedData.value.dept_list = [];
    }
  }

  const emit = defineEmits<{(e: 'returnData', value: number[]): void, (e: 'closeModal'): void}>();
  const submit = () => {
    emit('returnData', selectedData.value);
    emit('closeModal');
  }
  watch(selectedData, () => {
    if (selectedData.value.dept_list.length !== deptList.value.length && selectAll.value === true) {
      selectAll.value = false;
    }
  }, { deep: true });
</script>

<template>
  <div>
    <table v-if="deptList !== null">
      <tr>
        <th>
          <input v-if="deptList.length > 0" type="checkbox" v-model="selectAll" @change="selectAllHandler">
        </th>
        <th>分部名稱</th>
      </tr>
      <template v-for="(item, index) in deptList" :key="index">
        <tr>
          <td>
            <input :value="item.dept_id" type="checkbox" v-model="selectedData.dept_list">
          </td>
          <td>{{ item.dept_name_ch }}</td>
        </tr>
      </template>
      <tr>
        <td v-if="deptList.length === 0" colspan="2">目前無資料</td>
      </tr>
    </table>
    <button class="round-full-button blue mt-3" @click="submit">確定</button>
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