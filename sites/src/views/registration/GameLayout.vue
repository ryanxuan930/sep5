<script setup lang="ts">
import ConsoleNav from '@/components/registration/console/ConsoleNav.vue';
import AccountBtn from '@/components/registration/console/AccountBtn.vue';
import LogoSection from '@/components/registration/console/LogoSection.vue';
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 md:h-screen">
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