<script setup lang="ts">
import { ref, provide } from 'vue';
import { useUserStore } from '@/stores/user';
import VueRequest from '@/vue-request';
import { useRoute } from 'vue-router';
import ConsoleNav from '@/components/registration/console/ConsoleNav.vue';
import AccountBtn from '@/components/registration/console/AccountBtn.vue';
import LogoSection from '@/components/registration/console/LogoSection.vue';
import SpinnerLoading from '@/components/SpinnerLoading.vue';

const store = useUserStore();
const route = useRoute();
const vr = new VueRequest(store.token);
const loadinStatus = ref(false);
const adminOrgId = route.params.adminOrgId;
const systemConfig: any = ref(null);

(async () => {
  await vr.Get(`config/${adminOrgId}`, systemConfig);
  loadinStatus.value = true;
})();
provide('systemConfig', systemConfig);
</script>

<template>
  <SpinnerLoading v-show="loadinStatus == false"></SpinnerLoading>
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 md:h-screen" v-if="loadinStatus">
    <div class="common-box left-box">
      <div>
        <LogoSection></LogoSection>
      </div>
      <div class="flex-grow overflow-auto h-full">
        <ConsoleNav></ConsoleNav>
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