<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import SmallLoader from '@/components/SmallLoader.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const props = defineProps(['inputData']);
  const url = `game/${props.inputData.sport_code}/${props.inputData.game_id}/main/params`;
  interface IData {
    param_id?: number;
    division_id: number;
    event_code: string;
    locked_ip?: string;
    locked_admin?: number;
    data?: string;
    r1?: number;
    r1_aq?: number;
    r1_sq?: number;
    r1_split?: number;
    r2?: number;
    r2_aq?: number;
    r2_sq?: number;
    r2_split?: number;
    r3?: number;
    r3_aq?: number;
    r3_sq?: number;
    r3_split?: number;
    r4?: number;
    r4_aq?: number;
    r4_sq?: number;
    r4_split?: number;
    qulified?: number;
    options?: string;
  }
  const displayArray: any = ref(null);
  const eventList: any = ref([]);
  const divisionList: any = ref([]);
  async function getData() {
    const originalRes: any = await vr.Get(url);
    const divisionRes: any = await vr.Get(`game/${props.inputData.sport_code}/${props.inputData.game_id}/main/division`);
    const eventRes: any = await vr.Get(`game/${props.inputData.sport_code}/${props.inputData.game_id}/main/event/full`);
    const temp: any = [];
    for(let i = 0; i < eventRes.length; i++) {
      temp[i] = [];
      for(let j = 0; j < divisionRes.length; j++) {
        let flag = false;
        for (let k = 0; k < originalRes.length; k++){
          if (originalRes[k].division_id == divisionRes[j].division_id && originalRes[k].event_code == eventRes[i].event_code) {
            flag = true;
          }
        }
        temp[i].push({
          event_code: eventRes[i].event_code,
          division_id: divisionRes[j].division_id,
          selected: flag,
        });
      }
    }
    divisionList.value = divisionRes;
    eventList.value = eventRes;
    displayArray.value = temp;
  };
  getData();
  async function submitAll() {
    const temp: any = [];
    for (let i = 0; i < displayArray.value.length; i++) {
      for (let j = 0; j < displayArray.value[i].length; j++){
        if (displayArray.value[i][j].selected) {
          temp.push({
            division_id: displayArray.value[i][j].division_id,
            event_code: displayArray.value[i][j].event_code,
          });
        }
      }
    }
    await vr.Post(url, temp, null, true, true);
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
  <div class="overflow-auto" v-if="displayArray != null">
    <table class="config-table">
      <tr>
        <th class="whitespace-nowrap">項目\組別</th>
        <template v-for="(item, index) in divisionList" :key="index">
          <th class="whitespace-nowrap">{{ item.division_ch }}</th>
        </template>
      </tr>
      <template v-for="(item, index) in displayArray" :key="index">
        <tr>
          <td class="index">{{ eventList[index].event_ch }}</td>
          <template v-for="(element, index) in item" :key="index">
            <td>
              <input type="checkbox" v-model="element.selected">
            </td>
          </template>
        </tr>
      </template>
    </table>
  </div>
  <div class="my-3">
    <button class="round-full-button blue" @click="submitAll">儲存</button>
  </div>
  <div class="my-3">
    <button class="round-full-button red" @click="clearAll">全部清除</button>
  </div>
  <SmallLoader v-show="displayArray == null"></SmallLoader>
</template>

<style scoped lang="scss">
input[type=checkbox] {
  @apply border-0 w-8 h-8;
} 
.config-table {
  td, th {
    @apply text-center;
  }
}
</style>