<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const props = defineProps(['inputData']);
  const url = `game/${props.inputData.sport_code}/${props.inputData.game_id}/main/division`;
  interface IData {
    division_id?: number;
    sex: number;
    division_ch: string;
    division_en: string;
  }
  const dataPrototype: IData = {
    division_id: 0,
    sex: 0,
    division_ch: '',
    division_en: '',
  }
  const data:Ref<IData[]> = ref([]);
  async function getData() {
    data.value = await vr.Get(url);
  }
  getData();
  async function submitAll() {
    for(let item of data.value) {
      if (item.division_ch.length == 0) {
        alert('組別中文不得為空');
        return;
      }
      if (item.division_en.length == 0) {
        alert('組別英文不得為空');
        return;
      }
      delete item.division_id;
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
        <th>性別組成*</th>
        <th>組別名稱(中文)</th>
        <th>組別名稱(英文)</th>
        <th>
          <button class="general-button blue" @click="data.push(JSON.parse(JSON.stringify(dataPrototype)))">新增</button>
        </th>
      </tr>
      <template v-for="(item, index) in data" :key="index">
        <tr>
          <td class="index">{{ index+1 }}.</td>
          <td>
            <select class="round-input" v-model="item.sex">
              <option value="0">不分</option>
              <option value="1">男生</option>
              <option value="2">女生</option>
            </select>
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.division_ch">
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.division_en">
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