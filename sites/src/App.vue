<script setup lang="ts">
  try {
    document.cookie = 'accept=true';
    localStorage.setItem('test', 'test');
    localStorage.removeItem('test');
  } catch (e) {
    alert('本網站必須使用Cookie，您必須要啟用Cookie才能使用本網站');
    console.log(e);
  }
</script>
<template>
  <RouterView />
</template>
<style src="@vueform/toggle/themes/default.css"></style>
