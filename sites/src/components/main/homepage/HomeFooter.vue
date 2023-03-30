<script setup lang="ts">
const currentYear = new Date().getFullYear();
</script>

<template>
<div class="bg-gray-100 p-10 flex text-center">
  <div class="flex-grow"></div>
  <div class="text-gray-700 font-medium">
    <slot name="content"></slot>
    <hr class="my-2">
    <div class="mt-2 text-xs font-medium">SportEvent Pro 5 by TechNSport</div>
    <div class="text-xs">
      © {{ currentYear }}
      <a class="hover:text-gray-500 duration-150" href="">Ryan P. H. Chang</a>
    </div>
  </div>
  <div class="flex-grow"></div>
</div>
</template>

<style scoped lang="scss">
    
</style>