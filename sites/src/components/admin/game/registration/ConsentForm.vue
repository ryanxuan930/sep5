<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import SmallModal from '@/components/SmallModal.vue';
  import EditConsentForm from '@/components/admin/game/registration/EditConsentForm.vue';
  import EditConsentFormat from './EditConsentFormat.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const props = defineProps(['inputData'])
  const gameData: any = ref(props.inputData);
  const route = useRoute();
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;
  const orgList: any = ref([]);
  const deptList: any = ref([]);
  function getDeptList(orgCode: string) {
    vr.Get(`department/org/code/${orgCode}`, deptList, true, true);
  }
  (async () => {
    await vr.Get('organization', orgList);
    getDeptList(orgList.value[1].org_code);
  })();

  const formList: any = ref(null);
  async function getFormData() {
    await vr.Get(`consent-form-game/${gameId}`, formList);
  }
  getFormData();

  const selectedData: any = ref(null);
  function open(type: number, input: any) {
    selectedData.value = input;
    displayModal.value = type;
  }
</script>

<template>
  <div>
    <div class="grid md:grid-cols-5 gap-3 py-5">
      <button class="round-full-button blue" @click="displayModal = 1">格式設定</button>
    </div>
    <table>
      <tr>
        <th>組織單位</th>
        <th>分部/系所</th>
        <th>姓名</th>
        <th>狀態</th>
        <th>
          <a class="hyperlink blue" @click="open(1, null)">新增</a>
        </th>
      </tr>
      <template v-for="(item, index) in formList">
         <tr>
          <td>{{ item.org_name_full_ch }}</td>
          <td>{{ item.dept_name_ch }}</td>
          <td>{{ item.last_name_ch }}{{ item.first_name_ch }}</td>
          <td>
            <span v-if="item.status == 0">尚未處理</span>
            <span v-if="item.status == 1">已繳交</span>
            <span v-if="item.status == 2">其他</span>
          </td>
          <td>
            <a class="hyperlink blue" @click="open(2, item)">查看</a>
          </td>
         </tr>
      </template>
    </table>
    <SmallModal v-show="displayModal > 0" @closeModal="displayModal = 0">
      <template v-slot:title>
        <div class="text-2xl">
          <div v-if="displayModal == 1">編輯同意書格式</div>
          <div v-if="displayModal == 2">編輯報名表繳交紀錄</div>
        </div>
      </template>
      <template v-slot:content>
        <EditConsentFormat v-if="displayModal == 1" @closeModal="displayModal = 0"></EditConsentFormat>
        <EditConsentForm v-if="displayModal == 2" :game-data="$props.inputData" :input-data="selectedData" @closeModal="displayModal = 0" @refreshPage="getFormData"></EditConsentForm>
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