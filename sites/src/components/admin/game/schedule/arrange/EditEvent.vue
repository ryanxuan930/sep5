<script setup lang="ts">
import { ref, watch } from 'vue';
import type { Ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import { lanePhaseToString } from '@/components/library/functions';

const store = useUserStore();
const route = useRoute();

const props = defineProps(['inputData', 'phaseNum']);
const dataList: Ref<number[]> = ref([]);
const phase: any = ref(props.phaseNum);

const emit = defineEmits<{(e: 'returnData', index: number[]): void, (e: 'returnPhase', index: number): void, (e: 'closeModal'): void}>();

async function submitAll() {
  console.log(dataList.value);
  emit('returnData', dataList.value);
  emit('returnPhase', phase.value);
  emit('closeModal');
}

</script>

<template>
  <div>
    <div class="py-2 flex gap-2 items-center">
      <div >賽別：</div>
      <select class="border-2 rounded block flex-grow px-1 py-0.5" v-model="phase">
        <option value="1">第一輪</option>
        <option value="2">預賽</option>
        <option value="3">準決賽</option>
        <option value="4">決賽</option>
      </select>
    </div>
    <table>
      <tr>
        <th>選取</th>
        <th>組別</th>
        <th>項目</th>
      </tr>
      <template v-for="(item, index) in props.inputData">
        <tr>
          <td>
            <input type="checkbox" :value="index" v-model="dataList">
          </td>
          <td>{{ item.division_ch }}</td>
          <td>{{ item.event_ch }}</td>
        </tr>
      </template>
    </table>
    <div class="pt-5">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-full;
  td, th {
    @apply p-2 text-center border-[1px] w-1/5;
  }
  tr:first-child {
    td, th {
      @apply bg-blue-100;
    }
  }
  input[type=checkbox] {
    @apply p-1 border-2 rounded w-6 h-6;
  }
}
</style>