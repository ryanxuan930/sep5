<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const route = useRoute();
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;
  const isLoading = ref(false);
  const data: any = ref(null);
  const formatPrototype = {
    title: '',
    format: '',
  };
  (async () => {
    isLoading.value = true;
    const temp = await vr.Get(`game/${sportCode}/${gameId}/common/temp/awardFormat`);
    if (temp.temp_id == undefined) {
      const res = await vr.Post(`game/${sportCode}/${gameId}/common/temp`, {temp_key: 'awardFormat', temp_data: JSON.stringify([])}, null, true, true);
      if (res.status != 'A01') {
        alert('建立參數失敗');
      }
      data.value = [];
    } else {
      data.value = JSON.parse(temp.temp_data);
    }
    isLoading.value = false;
  })();

  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }

  async function submitAll() {
    const res = await vr.Patch(`game/${sportCode}/${gameId}/common/temp/awardFormat`, {temp_data: JSON.stringify(data.value)}, null, true, true);
    if (res.status == 'A01') {
      alert('已儲存');
      close();
    }else {
      alert('儲存失敗');
    }
  }
</script>

<template>
  <div class="flex flex-col gap-3" v-if="!isLoading">
    <label class="round-input-label">
      <div class="title flex">
        <div class="flex-grow">獎狀格式</div>
        <div>
          <a class="hyperlink blue" @click="data.push(JSON.parse(JSON.stringify(formatPrototype)))">增加</a>
        </div>
      </div>
      <div class="flex flex-col gap-3">
        <template v-for="(item, index) in data">
          <div class="flex items-top gap-2">
            <div class="flex-grow flex flex-col gap-2">
              <input type="text" class="w-full border-2 rounded-full px-4 py-1" v-model="item.title">
              <textarea class="w-full border-2 rounded-3xl px-4 py-1 text-base block flex-grow" rows="10" v-model="item.format"></textarea>
            </div>
            <div class="text-4xl text-gray-400 hover:text-gray-300 duration-150 cursor-pointer" @click="data.splice(index, 1)">✕</div>
          </div>
        </template>
      </div>
    </label>
    <button class="round-full-button blue" @click="submitAll">儲存</button>
  </div>
</template>

<style scoped lang="scss">
    
</style>