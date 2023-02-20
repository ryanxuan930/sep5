<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import SmallModalVue from '@/components/SmallModal.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(false);
  const props = defineProps(['inputData'])
  const gameData: any = ref(props.inputData);
  const route = useRoute();
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;

  const formList: any = ref(null);
  async function getFormData() {
    await vr.Get(`reg-form-game/${gameId}`, formList);
  }
  getFormData();
</script>

<template>
  <div>
    <table>
      <tr>
        <th>組織單位</th>
        <th>分部/系所</th>
        <th>狀態</th>
        <th>
          <a class="hyperlink blue">新增</a>
        </th>
      </tr>
      <template v-for="(item, index) in formList">
        <td></td>
        <td></td>
        <td></td>
        <td>
          <a class="hyperlink blue">查看</a>
        </td>
      </template>
    </table>
    <SmallModal v-show="displayModal" @closeModal="displayModal = false">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal">新增繳交紀錄</div>
        </div>
      </template>
      <template v-slot:content>
      </template>
    </SmallModal>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left overflow-scroll;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium overflow-hidden;
    div {
      @apply whitespace-nowrap overflow-auto w-full py-2;
    }
  }
  th {
    @apply bg-blue-100 sticky top-0;
  }
}
</style>