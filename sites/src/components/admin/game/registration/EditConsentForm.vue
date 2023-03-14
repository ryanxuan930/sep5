<script setup lang="ts">
  import { ref, reactive, watch } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const route = useRoute();
  const props = defineProps(['gameData', 'inputData']);
  const gameId = route.params.gameId;
  const gameData = props.gameData;

  const data: any = reactive({
    u_id: 0,
    game_id: gameId,
    status: 1,
    remarks: '',
  });
  const formId = ref(0);
  if (props.inputData != null) {
    Object.keys(data).forEach((index: string) => {
      data[index] = props.inputData[index];
    });
    formId.value = props.inputData.consent_form_id;
  }

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }

  async function submitAll() {
    const res = await vr.Patch(`consent-form/${formId.value}`, data, null, true, true);
    if (res.status = 'A01') {
      alert('已儲存');
      close();
    }
  }
</script>

<template>
  <div class="flex flex-col gap-3">
    <label class="round-input-label">
      <div class="title">選手</div>
      <div class="input">{{ props.inputData.last_name_ch }}{{ props.inputData.first_name_ch }}</div>
    </label>
    <label class="round-input-label">
      <div class="title">狀態</div>
      <select class="select" v-model="data.status">
        <option value="1">已繳交</option>
        <option value="0">尚未處理</option>
        <option value="2">其他</option>
      </select>
    </label>
    <label class="round-input-label">
      <div class="title">備註</div>
      <input type="text" class="input" v-model="data.remarks">
    </label>
    <button class="round-full-button blue" @click="submitAll">儲存</button>
  </div>
</template>

<style scoped lang="scss">
    
</style>