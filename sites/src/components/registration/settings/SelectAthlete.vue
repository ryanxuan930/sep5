<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import { useRoute, useRouter } from 'vue-router';
import VueRequest from '@/vue-request';
import Config from '@/assets/config.json';
import SpinnerLoading from '@/components/SpinnerLoading.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const router = useRouter();
const route = useRoute();
const isLoading = ref(false);

const selectedAthlete = ref(0);
const athleteList: any = ref(null);
const gameData: any = ref(null);
const consentList: any = ref(null);
const userList: any = ref(null);
const tempList: any = ref([]);
async function getData() {
  isLoading.value = true;
  await vr.Get(`${route.params.adminOrgId}/game/${route.params.gameId}`, gameData);
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/athlete/unit/${gameData.value.options.regUnit}`, athleteList, true, true);
  await vr.Get(`consent-form-game/${route.params.gameId}`, consentList, true, true);
  await vr.Get('user-partial', userList, true, true);
  consentList.value.forEach((el: any) => {
    tempList.value.push(el.u_id);
  });
  for (const athlete of athleteList.value) {
    if (!tempList.value.includes(athlete.u_id)) {
      const data = {
        u_id: athlete.u_id,
        game_id: route.params.gameId,
        status: 0,
        remarks: null,
      };
      await vr.Post(`consent-form`, data, null, true, true);
    }
  }
  await vr.Get(`consent-form-game/${route.params.gameId}`, consentList, true, true);
  isLoading.value = false;
}
getData();

async function submitAll() {
  if (selectedAthlete.value == 0) {
    alert('請選擇選手');
    return;
  }
  if (tempList.value.includes(selectedAthlete.value)) {
    alert('已在名單中');
    return;
  }
  const data = {
    u_id: selectedAthlete.value,
    game_id: route.params.gameId,
    status: 0,
    remarks: null,
  };
  const res = await vr.Post(`consent-form`, data, null, true, true);
  if (res.status == 'A01') {
    alert('已新增');
    selectedAthlete.value = 0;
    vr.Get(`consent-form-game/${route.params.gameId}`, consentList, true, true);
  }
}

async function remove(id: number) {
  const r = confirm('確定刪除？');
  if (r) {
    const res = await vr.Delete(`consent-form/${id}`, null, true, true);
    if (res.status == 'A01') {
      alert('已刪除');
      vr.Get(`consent-form-game/${route.params.gameId}`, consentList, true, true);
    }
  }
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
        <th>{{ t('organization') }}</th>
        <th>
          <span v-if="Config.deptAsClass">{{ t('class') }}</span>
          <span v-else>{{ t('department') }}</span>
        </th>
        <th>{{ t('athlete') }}</th>
        <th>
        </th>
      </tr>
      <tr>
        <th>{{ t('select-athlete') }}</th>
        <th colspan="2">
          <label class="round-input-label">
            <select v-model="selectedAthlete" class="select">
              <template v-for="(item, index) in userList">
                <option :value="item.u_id">
                  <template v-if="locale = 'zh-TW'">{{ item.last_name_ch }}{{ item.first_name_ch }}</template>
                  <template v-else>{{ item.first_name_en }} {{ item.last_name_en }}</template>
                </option>
              </template>
            </select>
          </label>
        </th>
        <th>
          <a class="hyperlink blue" @click="submitAll">{{ t('add') }}</a>
        </th>
      </tr>
      <template v-for="(item, index) in consentList">
        <tr v-if="(gameData.options.regUnit == 2 && item.org_code == store.userInfo.org_code) || (gameData.options.regUnit == 1 && item.dept_id == store.userInfo.dept_id) || (gameData.options.regUnit == 0 && item.u_id == store.userInfo.u_id)">
          <td>
            <span v-if="locale = 'zh-TW'">{{ item.org_name_full_ch }}</span>
            <span v-else>{{ item.org_name_full_en }}</span>
          </td>
          <td>
            <span v-if="locale = 'zh-TW'">{{ item.dept_name_ch }}</span>
            <span v-else>{{ item.dept_name_en }}</span>
          </td>
          <td>
            <span v-if="locale = 'zh-TW'">{{ item.last_name_ch }}{{ item.first_name_ch }}</span>
            <span v-else>{{ item.first_name_en }} {{ item.last_name_en }}</span>
          </td>
          <td>
            <a class="hyperlink red" @click="remove(item.consent_form_id)">{{ t('delete') }}</a>
          </td>
        </tr>
      </template>
    </table>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-full text-left;
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

<i18n lang="yaml">
  en-US:
    organization: 'Organization'
    department: 'Department'
    class: 'Class'
    athlete: 'Athlete'
    add: 'Add'
    delete: 'Delete'
    select-athlete: 'Select an athlete'
  zh-TW:
    organization: '組織單位'
    department: '分部/系所'
    class: '班級'
    athlete: '選手'
    add: '新增'
    delete: '刪除'
    select-athlete: '選擇選手'
</i18n>