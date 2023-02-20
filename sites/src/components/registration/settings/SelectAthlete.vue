<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';
import { useRoute, useRouter } from 'vue-router';
import VueRequest from '@/vue-request';
import SpinnerLoading from '@/components/SpinnerLoading.vue';

const store = useUserStore();
const vr = new VueRequest(store.token);
const router = useRouter();
const route = useRoute();
const isLoading = ref(false);

</script>

<template>
  <div></div>
</template>

<style scoped lang="scss">
    
</style>