<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps(['inputData']);
const emit = defineEmits<{(e: 'returnData', value: string): void, (e: 'closeModal'): void}>();
function submitAll() {
  emit('returnData', props.inputData);
  emit('closeModal');
}
</script>

<template>
  <div>
    <table>
      <tr>
        <th>組別名次</th>
        <th>編號</th>
        <th>道次</th>
        <th>名字 姓氏</th>
        <th>單位</th>
        <th>成績</th>
      </tr>
      <template v-for="(item, index) in props.inputData" :key="index">
        <tr>
          <td>{{ item[0] }}</td>
          <td>{{ item[1] }}</td>
          <td>{{ item[2] }}</td>
          <td>{{ item[3] }}</td>
          <td>{{ item[4] }}</td>
          <td>{{ item[5] }}</td>
        </tr>
      </template>
    </table>
    <div class="py-2">
      <button class="round-full-button blue" @click="submitAll">匯入</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[640px] sm:w-full;
  th, td {
    @apply border border-gray-300 p-2 text-left;
  }
  th {
    @apply bg-blue-100 sticky top-0;
  }
  tr:nth-child(even) {
    @apply bg-gray-50;
  }
}
</style>