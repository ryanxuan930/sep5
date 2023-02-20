<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import Config from '@/assets/config.json';

  const store = useUserStore();
  const route = useRoute();
  const vr = new VueRequest(store.token);
  const isLoading = ref(false);
  const userData: any = ref([]);
  const regConfig: any = ref([]);
  const gameData: any = ref([]);
  const individualList: any = ref([]);
  const groupList: any = ref([]);
  async function getDataList() {
    isLoading.value = true;
    await vr.Get(`${route.params.adminOrgId}/game/${route.params.gameId}`, gameData);
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/individual/by/user`, individualList, true, true);
    await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/user`, groupList, true, true);
    await vr.Get(`reg-config-game/${route.params.gameId}`, regConfig);
    await vr.Get(`auth/user/info`, userData, true, true);
    document.title = gameData.value.game_name_ch + '報名表';
    (async () => {
      for (let i = 0; i < groupList.value.length; i++) {
        groupList.value[i].members = await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/common/group/by/team/${groupList.value[i].team_id}`, null, true, true);
      }
    })();
    isLoading.value = false;
  }
getDataList();
</script>

<template>
  <div id="print-root" v-if="!isLoading">
    <div class="page">
      <div class="text-center font20 font-medium">{{ gameData.game_name_ch }}</div>
      <div class="text-center font20 font-medium">{{ gameData.game_name_en }}</div>
      <div class="text-center font16 font-medium">個人項目報名表</div>
      <div class="text-center font14 font-medium">Individual Events Registration Form</div>
      <div class="font16">報名單位基本資訊 Basic Information</div>
      <div class="h-[0.25cm]"></div>
      <div class="font12 font-normal">
        <table class="table1 break-inside-avoid">
          <tr>
            <td>單位名稱<br>Organization</td>
            <td colspan="3">
              <div>
                <div>{{ userData.org_name_full_ch }}</div>
                <div v-if="userData.dept_name_ch != null && regConfig.options.common.min_reg_permission < 2">{{ userData.dept_name_ch }}</div>
              </div>
              <div class="font10">
                <div>{{ userData.org_name_full_en }}</div>
                <div v-if="userData.dept_name_en != null && regConfig.options.common.min_reg_permission < 2">{{ userData.dept_name_en }}</div>
              </div>
            </td>
            <td>代碼<br>Code</td>
            <td>
              <span>{{ userData.org_code }}</span>
              <span v-if="userData.dept_id > 0"> - {{ userData.dept_id }}</span>
            </td>
          </tr>
          <tr>
            <td rowspan="2">聯絡人<br>Contact<br>Person</td>
            <td rowspan="2">{{ userData.last_name_ch }}{{ userData.first_name_ch }}</td>
            <td>連絡信箱<br>Email</td>
            <td colspan="3">{{ userData.account }}</td>
          </tr>
          <tr>
            <td>連絡電話<br>Phone</td>
            <td colspan="3">{{ userData.phone }}</td>
          </tr>
        </table>
        <div class="h-[0.25cm]"></div>
        <div class="font16">報名內容 Registration Content</div>
        <div class="h-[0.25cm]"></div>
        <table class="table2">
          <tr>
            <th class="w-1/12">編號<div class="font10">No.</div></th>
            <th class="w-3/12">組別<div class="font10">Division</div></th>
            <th class="w-3/12">項目<div class="font10">Event</div></th>
            <th class="w-5/12">姓名<div class="font10">Name</div></th>
          </tr>
          <template v-for="(item, index) in individualList">
            <tr>
              <td>{{ index + 1 }}</td>
              <td>
                <div>{{ item.division_ch }}</div>
                <div class="font10 text-gray-400">{{ item.division_en }}</div>
              </td>
              <td>
                <div>{{ item.event_ch }}</div>
                <div class="font10 text-gray-400">{{ item.event_en }}</div>
              </td>
              <td>{{ item.last_name_ch }}{{ item.first_name_ch }}<span v-if="Config.deptAsClass">({{ item.dept_name_ch }}-{{ item.num_in_dept.toString().padStart(2, '0') }})</span></td>
            </tr>
          </template>
          <tr v-if="individualList.length == 0">
            <td colspan="4">未報名個人項目 No data</td>
          </tr>
        </table>
      </div>
      <div class="h-[0.25cm]"></div>
      <div class="font16">承辦人員簽章 Signature</div>
      <div class="h-[0.25cm]"></div>
      <div>
        <table class="table3">
          <tr>
            <th>
              <template v-if="Config.deptAsClass">
                <div>體育組</div>
                <div class="font10">Physical Education Division</div>
              </template>
              <template v-else>
                <div>主辦單位</div>
                <div class="font10">Organizer</div>
              </template>
            </th>
            <th>
              <template v-if="Config.deptAsClass">
                <div>導師</div>
                <div class="font10">Homeroom Teacher</div>
              </template>
              <template v-else>
                <div>單位主管</div>
                <div class="font10">Supervisor</div>
              </template>
            </th>
            <th>
              <template v-if="Config.deptAsClass">
                <div>體育股長</div>
                <div class="font10">Sport Activities Leader</div>
              </template>
              <template v-else>
                <div>承辦人</div>
                <div class="font10">Contact Person</div>
              </template>
            </th>
          </tr>
        </table>
      </div>
    </div>
    <div class="page">
      <div class="text-center font20 font-medium">{{ gameData.game_name_ch }}</div>
      <div class="text-center font20 font-medium">{{ gameData.game_name_en }}</div>
      <div class="text-center font16 font-medium">團體項目報名表</div>
      <div class="text-center font14 font-medium">Group Events Registration Form</div>
      <div class="font16">報名單位基本資訊 Basic Information</div>
      <div class="h-[0.25cm]"></div>
      <div class="font12 font-normal">
        <table class="table1 break-inside-avoid">
          <tr>
            <td>單位名稱<br>Organization</td>
            <td colspan="3">
              <div>
                <div>{{ userData.org_name_full_ch }}</div>
                <div v-if="userData.dept_name_ch != null && regConfig.options.common.min_reg_permission < 2">{{ userData.dept_name_ch }}</div>
              </div>
              <div class="font10">
                <div>{{ userData.org_name_full_en }}</div>
                <div v-if="userData.dept_name_en != null && regConfig.options.common.min_reg_permission < 2">{{ userData.dept_name_en }}</div>
              </div>
            </td>
            <td>代碼<br>Code</td>
            <td>
              <span>{{ userData.org_code }}</span>
              <span v-if="userData.dept_id > 0"> - {{ userData.dept_id }}</span>
            </td>
          </tr>
          <tr>
            <td rowspan="2">聯絡人<br>Contact<br>Person</td>
            <td rowspan="2">{{ userData.last_name_ch }}{{ userData.first_name_ch }}</td>
            <td>連絡信箱<br>Email</td>
            <td colspan="3">{{ userData.account }}</td>
          </tr>
          <tr>
            <td>連絡電話<br>Phone</td>
            <td colspan="3">{{ userData.phone }}</td>
          </tr>
        </table>
        <div class="h-[0.25cm]"></div>
        <div class="font16">報名內容 Registration Content</div>
        <div class="h-[0.25cm]"></div>
        <table class="table2">
          <tr>
            <th class="w-1/12">編號<div class="font10">No.</div></th>
            <th class="w-3/12">組別<div class="font10">Division</div></th>
            <th class="w-3/12">項目<div class="font10">Event</div></th>
            <th class="w-5/12">姓名<div class="font10">Name</div></th>
          </tr>
          <template v-for="(item, index) in groupList">
            <tr>
              <td>{{ index + 1 }}</td>
              <td>
                <div>{{ item.division_ch }}</div>
                <div class="font10 text-gray-400">{{ item.division_en }}</div>
              </td>
              <td>
                <div>{{ item.event_ch }}</div>
                <div class="font10 text-gray-400">{{ item.event_en }}</div>
              </td>
              <td>
                <div>{{ item.team_name }}</div>
                <hr>
                <div class="flex items-center gap-3">
                  <template v-for="member in item.members">
                    <div>{{ member.last_name_ch }}{{ member.first_name_ch }}</div>
                  </template>
                </div>
                <div v-if="item.members.length == 0">本項目不需填寫</div>
              </td>
            </tr>
          </template>
          <tr v-if="groupList.length == 0">
            <td colspan="4">未報名團體項目 No data</td>
          </tr>
        </table>
      </div>
      <div class="h-[0.25cm]"></div>
      <div class="font16">承辦人員簽章 Signature</div>
      <div class="h-[0.25cm]"></div>
      <div>
        <table class="table3">
          <tr>
            <th>
              <template v-if="Config.deptAsClass">
                <div>體育組</div>
                <div class="font10">Physical Education Division</div>
              </template>
              <template v-else>
                <div>主辦單位</div>
                <div class="font10">Organizer</div>
              </template>
            </th>
            <th>
              <template v-if="Config.deptAsClass">
                <div>導師</div>
                <div class="font10">Homeroom Teacher</div>
              </template>
              <template v-else>
                <div>單位主管</div>
                <div class="font10">Supervisor</div>
              </template>
            </th>
            <th>
              <template v-if="Config.deptAsClass">
                <div>體育股長</div>
                <div class="font10">Sport Activities Leader</div>
              </template>
              <template v-else>
                <div>承辦人</div>
                <div class="font10">Contact Person</div>
              </template>
            </th>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
#print-root {
  background-color: white !important;
  min-height: 100vh;
}
.page {
  @apply p-[1cm];
  page-break-after: always;
  min-height: 100vh;
}
.font20 {
  font-size: 20pt;
}
.font16 {
  font-size: 16pt;
}
.font14 {
  font-size: 14pt;
}
.font12 {
  font-size: 12pt;
}
.font10 {
  font-size: 10pt;
}
.font9 {
  font-size: 9pt;
}
table {
  @apply border-collapse w-full;
}
.table1 {
  td {
    @apply border-[1px] border-gray-400 py-1 px-2 w-1/6 break-words;
  }
}
.table2 {
  th, td {
    @apply border-[1px] border-gray-400 py-1 px-2 text-left;
  }
}
.table3 {
  th, td {
    @apply py-1 px-2 text-left w-1/3;
  }
}
@page {
  margin: 0;
  border: 0;
  background-color: white !important;
  -webkit-print-color-adjust:exact !important;
  print-color-adjust:exact !important;
}
</style>