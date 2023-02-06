<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';
import { useI18n } from 'vue-i18n';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const props = defineProps(['inputData', 'orgId']);

  const teamList: any = ref(null);
  vr.Get(`${props.orgId}/school-team`, teamList, true, true);
  const selectedData: Ref<number[]> = ref(props.inputData);
  const emit = defineEmits<{(e: 'returnData', value: number[]): void, (e: 'closeModal'): void}>();
  const submit = () => {
    emit('returnData', selectedData.value);
    emit('closeModal');
  }
  const { t, locale } = useI18n({
    inheritLocale: true,
    useScope: 'local'
  });
</script>

<template>
  <div>
    <table>
      <tr>
        <th></th>
        <th>{{ t('team-name') }}</th>
      </tr>
      <template v-for="(item, index) in teamList" :key="index">
        <tr>
          <td>
            <input :value="item.school_team_id" type="checkbox" v-model="selectedData">
          </td>
          <td>
            <template v-if="locale == 'zh-TW'">{{ item.team_name_ch }}</template>
            <template v-else>{{ item.team_name_en }}</template>
          </td>
        </tr>
      </template>
    </table>
    <button class="round-full-button blue mt-3" @click="submit">{{ t('submit') }}</button>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-full text-left;
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
<i18n lang="yaml">
  en-US:
    team-name: 'Team Name'
    submit: 'Submit'
  zh-TW:
    team-name: '校隊名稱'
    submit: '送出'
</i18n>