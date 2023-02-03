<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const props = defineProps(['inputData']);
  const url = `game/${props.inputData.sport_code}/${props.inputData.game_id}/main/date`;
  interface IData {
    date_id?: number;
    date: string;
    day_ch: string;
    day_en: string;
  }
  const data:Ref<IData[]> = ref([]);
  async function getData() {
    data.value = await vr.Get(url);
  }
  getData();
  async function submitAll() {
    for(let item of data.value) {
      if (item.date.length == 0) {
        alert('日期不得為空');
        return;
      }
      delete item.date_id;
    }
    await vr.Post(url, data.value, null, true, true);
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
        <th>編號</th>
        <th>日期*</th>
        <th>日程(中文)</th>
        <th>日程(英文)</th>
        <th>
          <button class="general-button blue" @click="data.push({
            date: '',
            day_ch: '',
            day_en: '',
          })">新增</button>
        </th>
      </tr>
      <template v-for="(item, index) in data" :key="index">
        <tr>
          <td class="index">{{ index+1 }}.</td>
          <td>
            <input class="round-input" type="date" v-model="item.date">
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.day_ch">
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.day_en">
          </td>
          <td>
            <button class="general-button red" @click="data.splice(index, 1)">刪除</button>
          </td>
        </tr>
      </template>
    </table>
    <div v-if="data.length > 0" class="my-3">
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
    <div v-if="data.length > 0" class="my-3">
      <button class="round-full-button red" @click="clearAll">全部清除</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
    
</style>