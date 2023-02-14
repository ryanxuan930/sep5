<script setup lang="ts">
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';
  import Toggle from '@vueform/toggle';
  import { QuillEditor } from '@vueup/vue-quill'
  import '@vueup/vue-quill/dist/vue-quill.snow.css';
  import '@vueform/toggle/themes/default.css';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
</script>

<template>
  <div></div>
</template>

<style scoped lang="scss">
    
</style>