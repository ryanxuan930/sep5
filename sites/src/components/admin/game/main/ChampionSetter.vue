<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const props = defineProps(['inputData']);
  const isLoading = ref(false);
  const sportCode = props.inputData.sport_code;
  const gameId = props.inputData.game_id;
  const paramsList: any = ref([]);

  const dataList:any = ref([]);
  async function getData() {
    isLoading.value = true;
    const params = await vr.Get(`game/${sportCode}/${gameId}/main/params/full`, paramsList);
    const temp = await vr.Get(`game/${sportCode}/${gameId}/common/temp/gameRecords`);
    if (temp.temp_id == undefined) {
      const res = await vr.Post(`game/${sportCode}/${gameId}/common/temp`, {temp_key: 'gameRecords', temp_data: JSON.stringify([])}, null, true, true);
      if (res.status != 'A01') {
        alert('建立參數失敗');
      }
      params.forEach((item: any) => {
        dataList.value.push({
          division_id: item.division_id,
          event_code: item.event_code,
          unit_name_ch: '',
          unit_name_en: '',
          last_name_ch: '',
          first_name_ch: '',
          last_name_en: '',
          first_name_en: '',
          result: '',
          set_date: '',
        });
      });
    } else {
      const resultData = JSON.parse(temp.temp_data);
      for(let i = 0; i < params.length; i++) {
        dataList.value[i] = {
          division_id: params[i].division_id,
          event_code: params[i].event_code,
          unit_name_ch: '',
          unit_name_en: '',
          last_name_ch: '',
          first_name_ch: '',
          last_name_en: '',
          first_name_en: '',
          result: '',
          set_date: '',
        };
        for (let j = 0; j < resultData.length; j++) {
          if (params[i].division_id == resultData[j].division_id && params[i].event_code == resultData[j].event_code) {
            dataList.value[i].unit_name_ch = resultData[j].unit_name_ch;
            dataList.value[i].unit_name_en = resultData[j].unit_name_en;
            dataList.value[i].last_name_ch = resultData[j].last_name_ch;
            dataList.value[i].first_name_ch = resultData[j].first_name_ch;
            dataList.value[i].last_name_en = resultData[j].last_name_en;
            dataList.value[i].first_name_en = resultData[j].first_name_en;
            dataList.value[i].result = resultData[j].result;
            dataList.value[i].set_date = resultData[j].set_date;
          }
        }
      }
    }
    isLoading.value = false;
  };
  getData();
  async function submitAll() {
    const res = await vr.Patch(`game/${sportCode}/${gameId}/common/temp/gameRecords`, { temp_data: JSON.stringify(dataList.value)}, null, true, true);
    if (res.status != 'A01') {
      alert('儲存失敗');
      return;
    }
    getData();
    alert('已儲存');
  }
  async function clearAll() {
    const r = confirm('確定清除全部資料？');
    if (r) {
      await vr.Delete(`game/${sportCode}/${gameId}/common/temp/gameRecords`, null, true, true);
      getData();
      alert('已清除');
    }
  }
</script>

<template>
  <div class="overflow-auto w-full">
    <table class="config-table">
      <tr>
        <th>組別</th>
        <th>項目</th>
        <th>所屬單位中文</th>
        <th>所屬單位英文</th>
        <th>中文姓氏</th>
        <th>中文名字</th>
        <th>英文姓氏</th>
        <th>英文名字</th>
        <th>成績</th>
        <th>設立日期</th>
      </tr>
      <template v-for="(item, index) in dataList" :key="index">
        <tr>
          <template v-for="event in paramsList">
            <template v-if="item.division_id == event.division_id && item.event_code == event.event_code">
              <td class="w-36">{{ event.division_ch }}</td>
              <td class="w-36">{{ event.event_ch }}</td>
            </template>
          </template>
          <td>
            <input class="round-input" type="text" v-model="item.unit_name_ch">
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.unit_name_en">
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.last_name_ch">
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.first_name_ch">
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.last_name_en">
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.first_name_en">
          </td>
          <td>
            <input class="round-input" type="text" v-model="item.result">
          </td>
          <td>
            <input class="round-input" type="date" v-model="item.set_date">
          </td>
        </tr>
      </template>
    </table>
  </div>
  <div v-if="dataList.length > 0" class="my-3">
    <button class="round-full-button blue" @click="submitAll">儲存</button>
  </div>
  <div v-if="dataList.length > 0" class="my-3">
    <button class="round-full-button red" @click="clearAll">全部清除</button>
  </div>
</template>

<style scoped lang="scss">
.config-table {
  @apply w-[1920px] 3xl:w-full;
}  
</style>