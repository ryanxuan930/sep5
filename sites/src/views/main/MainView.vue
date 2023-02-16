<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router';
if (useRoute().params.adminOrgId == undefined) {
  useRouter().push('/2');
}
</script>

<template>
  <router-view></router-view>
</template>
