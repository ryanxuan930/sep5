<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';

  const props = defineProps(['inputData', 'inputStatus']);
  const data = ref(props.inputData);
  function setType() {
    if (data.value.type == 'ranking') {
      data.value.formula = [];
      data.value.payload = {};
    } else if (data.value.type == 'grade'){
      data.value.formula = {};
      data.value.payload = {};
    }
  }

</script>

<template>
  <div>
    <div class="grid grid-cols-2 gap-3">
      <label class="round-input-label">
        <div class="title">計算方式</div>
        <select class="select" :disabled="props.inputStatus == 'edit'" v-model="data.type" @change="setType()">
          <option value="ranking">以名次計算</option>
          <option value="grade">以級分計算</option>
        </select>
      </label>
      <label class="round-input-label">
        <div class="title">總錦標組別名稱</div>
        <input type="text" class="input" v-model="data.divisionName">
      </label>
    </div>
    
    <div v-if="data.type == 'ranking'">
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>