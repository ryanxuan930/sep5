<script lang="ts" setup>
  const emit = defineEmits<{(e: 'closeModal'): void}>();
  const close = () => {
    emit('closeModal');
  }
</script>

<template>
  <transition name="modal">
    <div class="fixed z-10 left-0 top-0 w-full h-full overflow-scroll bg-black bg-opacity-40">
      <div class="box-border w-inherit sm:w-sm bg-white relative mx-auto w-11/12 sm:w-3/4 md:w-2/3 lg:w-1/2  my-12 p-5 sm:px-10 sm:py-5 rounded" style="min-height: 100px">
        <div class="flex items-center">
          <div class="flex-grow">
            <slot name="title"></slot>
          </div>
          <div class="text-4xl text-gray-300 hover:text-gray-500 cursor-pointer duration-300 font-light" @click="close">&times;</div>
        </div>
        <slot name="content"></slot>
      </div>
    </div>
  </transition>
</template>

<style scoped lang="scss">
.modal-enter-active,.modal-leave-active {
    transition: opacity 0.5s ease;
}
.modal-enter-from,.modal-leave-to {
    opacity: 0;
}
.modal-enter-to,.modal-leave-from {
    opacity: 1;
}
</style>
