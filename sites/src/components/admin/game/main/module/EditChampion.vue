<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallModal from '@/components/SmallModal.vue';
  import DivisionSelector from '@/components/admin/game/main/module/DivisionSelector.vue';

  const props = defineProps(['inputData', 'inputStatus']);
  const data = ref(props.inputData);
  const displayModal = ref(0);
  function setType() {
    if (data.value.type == 'ranking') {
      data.value.formula = [];
      data.value.payload = {};
    } else if (data.value.type == 'grade'){
      data.value.formula = {};
      data.value.payload = {};
    }
  }
  if (props.inputStatus == 'add') {
    setType();
  }
  const emit = defineEmits<{(e: 'returnData', value: number[]): void, (e: 'closeModal'): void}>();
  const submit = () => {
    emit('returnData', data.value);
    emit('closeModal');
  }
</script>

<template>
  <div>
    <div class="grid grid-cols-4 gap-3">
      <label class="round-input-label">
        <div class="title">計算方式</div>
        <select class="select" :disabled="props.inputStatus == 'edit'" v-model="data.type" @change="setType()">
          <option value="ranking">以名次計算</option>
          <option value="grade">以級分計算</option>
        </select>
      </label>
      <label class="round-input-label">
        <div class="title">適用組別</div>
        <button class="round-full-button blue basis-20" @click="displayModal = 1">選擇</button>
      </label>
      <label class="round-input-label col-span-2">
        <div class="title">總錦標組別名稱</div>
        <input type="text" class="input" v-model="data.divisionName">
      </label>
    </div>
    <div v-if="data.type == 'ranking'">
      <table class="config-table my-5">
        <tr>
          <th>名次</th>
          <th>積分</th>
          <th>
            <a class="hyperlink blue" @click="data.formula.push(0)">新增</a>
          </th>
        </tr>
        <template v-for="(item, index) in data.formula" :key="index">
          <tr>
            <td>{{ index + 1 }}</td>
            <td>
              <input class="round-input" type="number" v-model="data.formula[index]">
            </td>
            <td>
              <a v-if="index == data.formula.length - 1" class="hyperlink red" @click="data.formula.splice(index, 1)">刪除</a>
            </td>
          </tr>
        </template>
      </table>
    </div>
  </div>
  <div>
    <button class="round-full-button blue" @click="submit">完成</button>
  </div>
  <SmallModal v-show="displayModal > 0" @closeModal="displayModal = 0">
    <template v-slot:title>
      <div class="text-2xl">
        <div v-if="displayModal == 1">選擇組別</div>
      </div>
    </template>
    <template v-slot:content>
      <div class="overflow-auto h-full">
        <DivisionSelector v-if="displayModal == 1" :input-data="data.divisionList" @closeModal="displayModal = 0" @returnData="(input: number[]) => { data.divisionList = input;}"></DivisionSelector>
      </div>
    </template>
  </SmallModal>
</template>

<style scoped lang="scss">
    
</style>