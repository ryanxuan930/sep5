<script lang="ts" setup>
  const emit = defineEmits<{(e: 'closeModal'): void}>();
  const close = () => {
    emit('closeModal');
  }
</script>

<template>
  <transition name="modal">
  <div class="fixed z-10 left-0 top-0 w-full h-screen bg-black bg-opacity-40 p-5 box-border overflow-hidden">
    <div class="box-border w-full 4xl:w-[2048px] 4xl:m-auto h-full bg-white relative p-5 sm:p-10 flex flex-col overflow-hidden rounded">
      <div class="flex items-center box-border">
        <div class="flex-grow font-medium">
          <slot name="title"></slot>
        </div>
        <div class="text-4xl text-gray-300 hover:text-gray-500 cursor-pointer duration-300 font-light box-border" @click="close">&times;</div>
        </div>
        <div style="box-sizing: border-box;" class="flex-grow h-full overflow-hidden p-0 box-border">
          <slot name="content"></slot>
        </div>
      </div>
    </div>
  </transition>
</template>

<style lang="scss" scoped>
.modal-enter-active,.modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from,.modal-leave-to {
  opacity: 0;
}
.modal-enter-to,.modal-leave-from {
  opacity: 1;
}
</style>
