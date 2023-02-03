<script setup lang="ts">
import { ref} from 'vue';
import VueCountdown from '@chenfengyuan/vue-countdown';

const props = defineProps(['countdownTime'])
const now:any = new Date();
const targetTime:any = new Date(props.countdownTime);
const timeDiff = targetTime - now;
const time = ref(timeDiff);
</script>

<template>
  <div class="bg-blue-500 py-2 px-3 md:px-5 w-full box-border mb-5">
    <div class="bg-blue-500 text-center text-white mb-2 font-semibold">
        <slot name="title"></slot>
    </div>
    <div class="w-full p-1 text-2xl md:text-3xl lg:text-5xl text-center bg-gray-900 text-white">
      <VueCountdown :time="time" v-slot="{days,hours,minutes,seconds}">
        <span style="font-family: Digital;">
            {{ String(days).padStart(2,'0') }} day {{ String(hours).padStart(2,'0') }} hrs {{ String(minutes).padStart(2,'0') }} min {{ String(seconds).padStart(2,'0') }} sec
        </span>
      </VueCountdown>
    </div>
    <div class="bg-blue-500 text-center text-white mt-2 text-lg" style="font-family: Audiowide-Regular">TechNSport</div>
  </div>
</template>

<style scoped lang="scss">
@font-face {
  font-family: Digital;
  src: url("https://ryanxuan930.github.io/cdn/nsysu_athletics/font/digital.ttf");
}
@font-face {
  font-family: Audiowide-Regular;
  src: url("https://ryanxuan930.github.io/cdn/nsysu_athletics/font/Audiowide-Regular.ttf");
}   
</style>