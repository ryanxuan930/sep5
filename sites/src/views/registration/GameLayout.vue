<script setup lang="ts">
import { ref, provide } from 'vue';
import type { Ref } from 'vue';
import { useUserStore } from '@/stores/user';
import VueRequest from '@/vue-request';
import { useRoute, useRouter } from 'vue-router';
import GameNav from '@/components/registration/console/GameNav.vue';
import AccountBtn from '@/components/registration/console/AccountBtn.vue';
import LogoSection from '@/components/registration/console/LogoSection.vue';
import SpinnerLoading from '@/components/SpinnerLoading.vue';

const store = useUserStore();
const route = useRoute();
const router = useRouter();
const vr = new VueRequest(store.token);
const loadingStatus = ref(false);
const adminOrgId = route.params.adminOrgId;
const systemConfig: any = ref(null);
const paramList = ref(null);
const currentTime = Date.now();
const userList = ref(null);
const divisionList = ref(null);
const gameData: any = ref(null);
const userData: any = ref(null);

interface regConfig {
  reg_config_id: number,
  game_id: number,
  reg_switch: number, //boolean
  deadline_list: {
    seperate: boolean;
    all: {
      start: string;
      end: string;
    };
    division: {
      start: string; 
      end: string;
      division_id: number
    }[];
  };
  options: {
    common: {
      min_reg_permission: number; // 最低報名權限
      allow_grouping: boolean; // 允許自行組隊->限制個人
      allow_cross_dept: boolean; // 允許跨分部組隊
      allow_cross_org: boolean; // 允許跨組織組隊
      individual: {
        max_event_per_athlete: number; // 每項目最多幾人報名
        max_athlete_per_event: number; // 每人最多報名幾項目
      };
      group: {
        max_event_per_team: number; // 每項目最多幾隊報名
        max_team_per_event?: number; // 每隊最多報名幾項目
      };
    };
    division: {
      division_id: number;
      prevent_sport_gifited: boolean; // 限非體優
      student_only: boolean; // 限學生報名
      has_grade: boolean;
      min_grade?: number;
      max_grade?: number;
    }[];
    event: {
      [key: string]: {
        prevent_sport_gifited: boolean;
        student_only: boolean;
        pre_define_member?: boolean;
        has_grade: boolean;
        min_grade?: number;
        max_grade?: number;
      }
    }
  }
}
const regConfig: Ref<regConfig> = ref({
  reg_config_id: 0,
  game_id: 1,
  reg_switch: 1,
  deadline_list: {
    division: [
      {
        start: '2023-02-06 12:00:00',
        end: '2023-02-12 12:00:00',
        division_id: 1,
      }
    ],
    all: {
      start: '2023-02-06 12:00:00',
      end: '2023-02-12 12:00:00',
    },
    seperate: false,
  },
  options: {
    common: {
      min_reg_permission: 0,
      allow_grouping: true,
      allow_cross_dept: true,
      allow_cross_org: true,
      individual: {
        max_event_per_athlete: 3,
        max_athlete_per_event: 2,
      },
      group: {
        max_event_per_team: 2,
      }
    },
    division: [
      {
        division_id: 1,
        prevent_sport_gifited: false,
        student_only: true,
        has_grade: false,
      }
    ],
    event: {
      athl0001: {
        prevent_sport_gifited: true,
        student_only: true,
        has_grade: false,
      },
      athl0024: {
        prevent_sport_gifited: true,
        student_only: true,
        pre_define_member: true,
        has_grade: false,
      },
    }
  }
});
(async () => {
  await vr.Get(`config/${adminOrgId}`, systemConfig);
  await vr.Get(`reg-config-game/${route.params.gameId}`, regConfig);
  await vr.Get(`auth/user/info`, userData, true, true);
  await vr.Get(`${adminOrgId}/game/${route.params.gameId}`, gameData);
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/params/full`, paramList);
  await vr.Get('user-partial', userList, true, true);
  await vr.Get(`game/${route.params.sportCode}/${route.params.gameId}/main/division`, divisionList);
  await check();
  loadingStatus.value = true;
})();
provide('systemConfig', systemConfig);
provide('paramList', paramList);
provide('regConfig', regConfig);
provide('userList', userList);
provide('divisionList', divisionList);
provide('gameData', gameData);
provide('userData', userData);

function backToHome(){
  router.push(`/${adminOrgId}/registration`);
}

async function check(){
  if (regConfig.value.reg_switch == undefined){
    alert('尚未開放 Not in service');
    backToHome();
  }
  if (regConfig.value.reg_switch == 0){
    alert('尚未開放 Not in service');
    backToHome();
    return;
  }
  if (Date.parse(regConfig.value.deadline_list.all.start) > currentTime || currentTime > Date.parse(regConfig.value.deadline_list.all.end)) {
    alert('不在報名期間 Not in registration period');
    backToHome();
    return;
  }
  if (store.userInfo.permission < regConfig.value.options.common.min_reg_permission) {
    alert('權限不足 Access denied');
    backToHome();
    return;
  }
  if (gameData.value.selected_list.length > 0) {
    for (const item of gameData.value.selected_list) {
      if (item.org_id == userData.value.org_id) {
        if (!item.dept_list.includes(userData.value.dept_id)) {
          alert('非可報名此賽事的單位 Not allowed');
          backToHome();
          return;
        }
      }
    }
  }
}

</script>

<template>
  <SpinnerLoading v-show="!loadingStatus"></SpinnerLoading>
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 md:h-screen" v-if="loadingStatus">
    <div class="common-box left-box">
      <div>
        <LogoSection></LogoSection>
      </div>
      <div class="flex-grow overflow-auto h-full">
        <GameNav></GameNav>
      </div>
      <div>
        <AccountBtn></AccountBtn>
      </div>
    </div>
    <div class="common-box right-box">
      <router-view></router-view>
    </div>
  </div>
</template>

<style scoped lang="scss">
.common-box {
  @apply md:h-screen;
}
.left-box {
  @apply bg-white shadow flex flex-col;
}
.right-box {
  @apply p-5 overflow-hidden md:col-span-2 lg:col-span-3 xl:col-span-4 ;
}
</style>