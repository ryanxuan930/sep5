<script setup lang="ts">
import ConsoleNav from '@/components/admin/console/ConsoleNav.vue';
import AccountBtn from '@/components/admin/console/AccountBtn.vue';
import LogoSection from '@/components/admin/console/LogoSection.vue';
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 h-screen">
    <div class="common-box left-box flex flex-col h-screen">
      <div class="shadow">
        <LogoSection></LogoSection>
      </div>
      <div class="flex-grow overflow-auto h-full">
        <ConsoleNav></ConsoleNav>
      </div>
      <div class="shadow">
        <AccountBtn></AccountBtn>
      </div>
    </div>
    <div class="md:col-span-2 lg:col-span-3 xl:col-span-4 common-box right-box">
      <router-view></router-view>
    </div>
  </div>
</template>

<style scoped lang="scss">
.left-box {
  @apply bg-white shadow;
}
</style>