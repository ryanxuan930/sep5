<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import SmallLoader from '@/components/SmallLoader.vue';

  const store = useUserStore()
  const vr = new VueRequest(store.token);
  const props: any = defineProps(['inputData']);
  const isLoading = ref(false);

  const athleteList: any = ref([]);
  async function getUserData(id: number) {
    return await vr.Get(`user/${id}`);
  }

  (async () => {
    isLoading.value = true;
    const memberList = JSON.parse(props.inputData.member_list);
    for (const member of memberList) {
      const temp = await getUserData(member);
      athleteList.value.push(temp);
    }
    isLoading.value = false;
  })();

  const emit = defineEmits<{(e: 'closeModal'): void}>();
  const close = () => {
    emit('closeModal');
  }
  const { t, locale } = useI18n({
    inheritLocale: true,
    useScope: 'local'
  });
</script>

<template>
  <div class="h-full flex flex-col">
    <hr>
    <div class="grid grid-cols-2 gap-3 text-lg font-medium py-2">
      <div>{{ t('division') }} :</div>
      <div>
        <template v-if="locale == 'zh-TW'">{{ props.inputData.division_ch }}</template>
        <template v-else>{{ props.inputData.division_en }}</template>
      </div>
      <div>{{ t('event') }} :</div>
      <div>
        <template v-if="locale == 'zh-TW'">{{ props.inputData.event_ch }}</template>
        <template v-else>{{ props.inputData.event_en }}</template>
      </div>
      <div>{{ t('team') }} :</div>
      <div>{{ props.inputData.team_name }}</div>
      <div>{{ t('ref-result') }}</div>
      <div>{{ props.inputData.ref_result }}</div>

    </div>
    <div class="w-full h-full overflow-auto">
      <table class="game-table">
        `<tr>
          <th>{{ t('athlete-id') }}</th>
          <th>{{ t('name') }}</th>
          <th>{{ t('organization') }}</th>
          <th>{{ t('department') }}</th>
          <th>{{ t('is-student') }}</th>
          <th>{{ t('is-school-team') }}</th>
          <th>{{ t('is-sport-gifted') }}</th>
        </tr>
        <template v-for="(item, index) in athleteList" :key="index">
          <tr>
            <td>{{ item.athlete_id }}</td>
            <td>
              <template v-if="locale == 'zh-TW'">{{ item.last_name_ch + item.first_name_ch }}</template>
              <template v-else>{{ item.first_name_en }} {{ item.last_name_en }}</template>
            </td>
            <td>
              <template v-if="locale == 'zh-TW'">{{ item.org_name_full_ch }}</template>
              <template v-else>{{ item.org_name_full_en }}</template>
            </td>
            <td>
              <template v-if="locale == 'zh-TW'">{{ item.dept_name_ch }}</template>
              <template v-else>{{ item.dept_name_en }}</template>
            </td>
            <td>
              <span v-if="item.is_student == 0">{{ t('no') }}</span>
              <span v-if="item.is_student == 1">{{ t('yes') }}</span>
            </td>
            <td>
              <span v-if="item.is_school_team == 0">{{ t('no') }}</span>
              <span v-if="item.is_school_team == 1">{{ t('yes') }}</span>
            </td>
            <td>
              <span v-if="item.is_sport_gifited == 0">{{ t('no') }}</span>
              <span v-if="item.is_sport_gifited == 1">{{ t('yes') }}</span>
            </td>
          </tr>
        </template>
      </table>
    </div>
  </div>
</template>

<style scoped lang="scss">
.game-table {
  @apply w-[768px] md:w-full text-left overflow-auto;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium;
  }
  th {
    @apply bg-blue-100 sticky top-0;
  }
}
.general-button.active {
  @apply bg-blue-400;
}
.page-btn {
  @apply flex my-5 gap-3;
}
table {
  @apply w-full text-left border-collapse;
  td, th {
    @apply p-1;
  }
}
</style>

<i18n lang="yaml">
  en-US:
    division: 'Division'
    event: 'Event'
    name: 'Name'
    ref-result: 'Record'
    organization: 'Organization'
    department: 'Department'
    team: 'Team Name'
    sex: 'Sex'
    male: 'M'
    female: 'F'
    others: 'X'
    is-sport-gifted: 'Sport Gifted'
    is-school-team: 'School Team'
    yes: 'Yes'
    no: 'No'
    athlete-id: 'Athlete ID'
    is-student: 'Student'
  zh-TW:
    division: '組別'
    event: '項目'
    name: '姓名'
    ref-result: '參考成績'
    organization: '組織單位'
    department: '分部/系所'
    team: '隊名'
    sex: '性別'
    male: '男'
    female: '女'
    others: '其他'
    is-sport-gifted: '體優身份'
    is-school-team: '校隊身份'
    yes: '是'
    no: '否'
    athlete-id: '選手代碼'
    is-student: '學生身份'
</i18n>