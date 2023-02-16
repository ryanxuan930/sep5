<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const props = defineProps(['inputData']);

  const gradeList:({grade_id: number, grade: string}[]) = [
    {grade_id: 1, grade: '小一'},
    {grade_id: 2, grade: '小二'},
    {grade_id: 3, grade: '小三'},
    {grade_id: 4, grade: '小四'},
    {grade_id: 5, grade: '小五'},
    {grade_id: 6, grade: '小六'},
    {grade_id: 7, grade: '國一'},
    {grade_id: 8, grade: '國二'},
    {grade_id: 9, grade: '國三'},
    {grade_id: 10, grade: '高一'},
    {grade_id: 11, grade: '高二'},
    {grade_id: 12, grade: '高三'},
    {grade_id: 21, grade: '大一'},
    {grade_id: 22, grade: '大二'},
    {grade_id: 23, grade: '大三'},
    {grade_id: 24, grade: '大四'},
    {grade_id: 25, grade: '大五'},
    {grade_id: 26, grade: '大六'},
    {grade_id: 27, grade: '大七'},
    {grade_id: 28, grade: '大八'},
    {grade_id: 31, grade: '碩一'},
    {grade_id: 32, grade: '碩二'},
    {grade_id: 33, grade: '碩三'},
    {grade_id: 34, grade: '碩四'},
    {grade_id: 41, grade: '博一'},
    {grade_id: 42, grade: '博二'},
    {grade_id: 43, grade: '博三'},
    {grade_id: 44, grade: '博四'},
    {grade_id: 45, grade: '博五'},
    {grade_id: 46, grade: '博六'},
    {grade_id: 47, grade: '博七'},
    {grade_id: 35, grade: '二專一'},
    {grade_id: 36, grade: '二專二'},
    {grade_id: 37, grade: '二技一'},
    {grade_id: 38, grade: '二技二'},
    {grade_id: 61, grade: '四技一'},
    {grade_id: 62, grade: '四技二'},
    {grade_id: 63, grade: '四技三'},
    {grade_id: 64, grade: '四技四'},
    {grade_id: 51, grade: '五專一'},
    {grade_id: 52, grade: '五專二'},
    {grade_id: 53, grade: '五專三'},
    {grade_id: 54, grade: '五專四'},
    {grade_id: 55, grade: '五專五'},
    {grade_id: 71, grade: '七技一'},
    {grade_id: 72, grade: '七技二'},
    {grade_id: 73, grade: '七技三'},
    {grade_id: 74, grade: '七技四'},
    {grade_id: 75, grade: '七技五'},
    {grade_id: 76, grade: '七技六'},
    {grade_id: 77, grade: '七技七'},
    {grade_id: 98, grade: '延畢'},
    {grade_id: 99, grade: '畢業'},
  ];
  
  const selectedData: Ref<number[]> = ref(props.inputData);
  const emit = defineEmits<{(e: 'returnData', value: number[]): void, (e: 'closeModal'): void}>();
  const submit = () => {
    emit('returnData', selectedData.value);
    emit('closeModal');
  }
</script>

<template>
  <div>
    <table>
      <tr>
        <th></th>
        <th>年級</th>
      </tr>
      <template v-for="(item, index) in gradeList" :key="index">
        <tr>
          <td>
            <input :value="item.grade_id" type="checkbox" v-model="selectedData">
          </td>
          <td>{{ item.grade }}</td>
        </tr>
      </template>
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