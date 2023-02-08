<script setup lang="ts">
</script>

<template>
  <transition name="modal">
    <div class="spin-content">
      <div id="loader" style="color: 250px;">
        <div class="spin-logo">
          <img src="@/assets/logo_no_bg_white.png" alt="">
        </div>
        <div class="spin-line text-white"></div>
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
.spin-content {
  @apply flex items-center justify-center h-full fixed bg-blue-600 w-full right-0 left-0 bottom-0 top-0 z-[999999];
}
.spin-logo {
  @apply z-[1001] absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center;
}
.spin-logo img {
  @apply w-full max-w-full h-auto;
}
#loader {
  @apply absolute w-[300px] h-[300px] md:w-[400px] md:h-[400px];
}
.spin-line{
  @apply flex relative top-0 items-center justify-center w-full h-full rounded-[50%] z-[1001] text-white;
  border: 3px solid transparent;
  border-top-color: currentColor;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}
.spin-line::after {
  content: "";
  position: absolute;
  top: 15px;
  left: 15px;
  right: 15px;
  bottom: 15px;
  border-radius: 50%;
  border: 3px solid transparent;
  border-top-color: currentColor;
  -webkit-animation: spin 1.5s linear infinite;
  animation: spin 1.5s linear infinite;
}
.spin-line::before {
  content: "";
  position: absolute;
  top: 5px;
  left: 5px;
  right: 5px;
  bottom: 5px;
  border-radius: 50%;
  border: 3px solid transparent;
  border-top-color: currentColor;
  -webkit-animation: spin 3s linear infinite;
  animation: spin 3s linear infinite;
}


@-webkit-keyframes spin {
  0% {
    -webkit-transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
  }
}

@keyframes spin {
  0% {
    -webkit-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
</style>