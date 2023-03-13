<script setup lang="ts">
import { ref, watch } from 'vue';
import VueRequest from '@/vue-request';
import { useUserStore } from '@/stores/user';
import { useRoute } from 'vue-router';
import { lanePhaseToString } from '@/components/library/functions';

const store = useUserStore();
const vr = new VueRequest(store.token);
const route = useRoute();
const sportCode = route.params.sportCode;
const gameId = route.params.gameId;

const props = defineProps(['inputData']);
const dataList: any = ref([]);

const emit = defineEmits<{(e: 'returnData', index: number[]): void, (e: 'closeModal'): void}>();
const close = () => {
  emit('returnData', dataList.value);
  emit('closeModal');
}

async function submitAll() {
}

</script>

<template>
  <div>
    <table>
      <tr>
        <th></th>
        <th>組別</th>
        <th>項目</th>
        <th>階段</th>
      </tr>
      <template v-for="(item, index) in props.inputData">
        <tr>
          <td>
            <input type="checkbox" :value="index">
          </td>
          <td>{{ item.division_ch }}</td>
          <td>{{ item.event_ch }}</td>
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
  input {
    @apply p-1 border-2 rounded w-16;
  }
}
</style>