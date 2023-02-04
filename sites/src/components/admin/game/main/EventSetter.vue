<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const props = defineProps(['inputData']);
  const url = `game/${props.inputData.sport_code}/${props.inputData.game_id}/main/event`;
  interface IData {
    selected_event_id?: number;
    event_id: number;
    event_code: string;
  }
  const data:Ref<number[]> = ref([]);
  async function getData() {
    const temp:IData[] = await vr.Get(url);
    temp.forEach((event: IData) => {
      data.value.push(event.event_id);
    });
  }
  getData();

  const eventList: any = ref([]);
  vr.Get(`event/sport/${props.inputData.sport_id}`, eventList);

  async function submitAll() {
    const dataset:IData[] = [];
    for(let item of eventList.value) {
      if (data.value.includes(item.event_id)) {
        dataset.push({
          event_id: item.event_id,
          event_code: item.event_code,
        });
      }
    }
    await vr.Post(url, dataset, null, true, true);
    getData();
    alert('已儲存');
  }
  async function clearAll() {
    const r = confirm('確定清除全部資料？');
    if (r) {
      await vr.Post(url, [], null, true, true);
      getData();
      alert('已清除');
    }
  }
</script>

<template>
  <div>
    <table class="config-table">
      <tr>
        <th>選取</th>
        <th>項目代碼</th>
        <th>項目名稱</th>
        <th>項目類別</th>
        <th>所需人數</th>
      </tr>
      <template v-for="(item, index) in eventList" :key="index">
        <tr v-if="item.display">
          <td class="index">
            <input :value="item.event_id" type="checkbox" class="checkbox" v-model="data">
          </td>
          <td>{{ item.event_code }}</td>
          <td>{{ item.event_ch }}</td>
          <td>{{ item.multiple == 1 ? '團體' : '個人' }}</td>
          <td>{{ item.player_num }}</td>
        </tr>
      </template>
    </table>
    <div class="my-3">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
    <div class="my-3">
      <button class="round-full-button red" @click="clearAll">全部清除</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>