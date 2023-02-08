<script setup lang="ts">
</script>

<template>
  <transition name="modal">
    <div class="flex py-5">
      <div class="flex-grow"></div>
      <div class="loader"></div>
      <div class="flex-grow"></div>
    </div>
  </transition>
</template>

<style scoped lang="scss">
.modal-enter-active,.modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from,.modal-leave-to {
  opacity: 0;
}
.modal-enter-to,.modal-leave-from {
  opacity: 1;
}
.loader{
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 100px;
  height: 100px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}


@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>