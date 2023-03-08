<script setup lang="ts">
  import { ref, watch } from 'vue';
  import type { Ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import draggable from 'vuedraggable';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const route = useRoute();
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;
  const onDrag = ref(false);
  const isLoading = ref(false);
  const phaseList = ['其他', '預賽', '第二輪', '準決賽', '決賽'];
  const fixedList = [
    {
      time: '00:00',
      event_code: '00000000',
      event_ch:'',
      division_ch: '',
      division_id: 0,
      round: 0,
      status: 0,
      options: {
        title: '開幕式'
      }
    },
    {
      time: '00:00',
      event_code: '00000000',
      event_ch:'',
      division_ch: '',
      division_id: 0,
      round: 0,
      status: 0,
      options: {
        title: '閉幕式'
      }
    },
    {
      time: '00:00',
      event_code: '00000000',
      event_ch:'',
      division_ch: '',
      division_id: 0,
      round: 0,
      status: 0,
      options: {
        title: '中場休息'
      }
    },
    {
      time: '00:00',
      event_code: '00000000',
      event_ch:'',
      division_ch: '',
      division_id: 0,
      round: 0,
      status: 0,
      options: {
        title: ''
      }
    },
  ];
  const eventList: any = ref([]);
  const dateList: any = ref([]);
  async function getData() {
    isLoading.value = true;
    const scheduleList = await vr.Get(`game/${sportCode}/${gameId}/common/schedule/full`, null, true, true);
    const paramList = await vr.Get(`game/${sportCode}/${gameId}/main/params`, null, true, true);
    const dateArray = await vr.Get(`game/${sportCode}/${gameId}/main/date`, null, true, true);
    const fullList = await vr.Get(`game/${sportCode}/${gameId}/main/params/full`, null, true, true);
    const phaseArray = ['r1', 'r2', 'r3', 'r4']
    dateArray.forEach((d: any) => {
      dateList.value.push({
        id: d.date_id,
        date: d.date,
        day: d.day_ch,
        items: [],
      });
    });
    // find same division_id and event_code in schedule list
    for(let i = 0; i < fullList.length; i++) {
      for (let j = 0; j < 4; j++){
        if (paramList[i][phaseArray[j]] == 1) {
          if (scheduleList.find((item: any) => item.division_id == fullList[i].division_id && item.event_code == fullList[i].event_code && item.round == fullList[i][phaseArray[j]]) == undefined) {
            eventList.value.push({
              time: '00:00',
              event_code: fullList[i].event_code,
              event_ch: fullList[i].event_ch,
              division_ch: fullList[i].division_ch,
              division_id: fullList[i].division_id,
              round: j + 1,
              status: 0,
              options: {}
            });
          }
        }
      }
    }
    scheduleList.forEach((item: any) => {
      const dateIndex = dateArray.findIndex((d: any) => d.date === item.timestamp.substring(0, 10));
      if (dateIndex !== -1) {
        dateList.value[dateIndex].items.push({
          time: item.timestamp.substring(11, 16),
          event_code: item.event_code == null ? '00000000': item.event_code,
          event_ch: item.event_ch == null ? '': item.event_ch,
          division_ch: item.division_ch == null ? '': item.division_ch,
          division_id: item.division_id == null ? 0: item.division_id,
          round: item.round,
          status: item.status,
          options: JSON.parse(item.options),
        });
      }
    });
    dateList.value.forEach((d: any) => {
      d.items.sort((a: any, b: any) => {
        const t1 = new Date(`${d.date} ${a.time}`);
        const t2 = new Date(`${d.date} ${b.time}`);
        return t1.getTime() - t2.getTime();
      });
    });
    isLoading.value = false;
  }
  getData();
  function clone(input: any) {
    const element: any = {};
    let key: string;
    // eslint-disable-next-line
    for (key in input) {
      if (Object.prototype.hasOwnProperty.call(input, key)) {
        element[key] = input[key];
      }
    }
    return element;
  };
  function moveItem() {
    onDrag.value = true;
  };
  function scanEventList() {
    for (let i = 0; i < eventList.value.length; i++) {
      if (eventList.value[i].round === 0) {
        eventList.value.splice(i, 1);
      }
    }
    eventList.value.sort((a: any, b: any) => a.division_id - b.division_id || a.event_code - b.event_code || a.round - b.round);
    onDrag.value = false;
  }
  watch(eventList, () => {
    scanEventList();
  });
  const emit = defineEmits<{(e: 'refreshPage'): void, (e: 'closeModal'): void}>();
  const close = () => {
    emit('refreshPage');
    emit('closeModal');
  }
  async function submitAll() {
    const data: any = [];
    for (let i = 0; i < dateList.value.length; i++) {
      for (let j = 0; j < dateList.value[i].items.length; j++) {
        data.push({
          timestamp: `${dateList.value[i].date} ${dateList.value[i].items[j].time}`,
          division_id: dateList.value[i].items[j].division_id,
          event_code: dateList.value[i].items[j].event_code,
          round: dateList.value[i].items[j].round,
          status: dateList.value[i].items[j].status,
          options: JSON.stringify(dateList.value[i].items[j].options),
        });
      }
    }
    data.sort((a: any, b: any) => {
      const t1 = new Date(a.timestamp);
      const t2 = new Date(b.timestamp);
      return t1.getTime() - t2.getTime();
    });
    const res = await vr.Post(`game/${sportCode}/${gameId}/common/schedule`, data, null, true, true);
    if (res.status == 'A01') {
      alert('已儲存');
      close();
    }
  }
</script>

<template>
  <div class="border-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 h-full overflow-hidden gap-3">
    <div class="left-box">
      <div class="flex gap-2 py-2">
        <button class="general-button blue" @click="submitAll">儲存</button>
        <button class="general-button blue">智慧預排</button>
        <button class="general-button red">全部清除</button>
      </div>
      <div class="p-2 bg-gray-50 shadow-inner">
        <draggable v-model="fixedList" :group="{ name: 'fixed-list', pull: 'clone', put: false }" :sort="false" item-key="id" :clone="clone">
          <template #item="{element}">
            <div class="drag-item">
              <input type="time" v-model="element.time">
              <div class="whitespace-nowrap">[{{ phaseList[element.round] }}]</div>
              <div v-if="element.division_ch != ''">{{ element.division_ch }}-{{ element.event_ch }}</div>
                <div class="flex-grow" v-else-if="element.division_id == 0 && element.event_code == '00000000' && ['開幕式', '閉幕式', '中場休息'].includes(element.options.title)">{{ element.options.title }}</div>
                <div v-else >
                  <input class="w-full px-1" type="text" v-model="element.options.title">
                </div>
            </div>
          </template>
        </draggable>
      </div>
      <div class="flex-grow p-2 bg-gray-50 shadow-inner h-full overflow-hidden">
        <div class="h-full overflow-auto">
          <div class="bg-gray-200 p-5 border-2 rounded text-gray-700 text-center text-xl" v-show="onDrag">丟到這邊來移除</div>
          <draggable v-model="eventList" :group="{ name: 'dynamic-list', pull: true, put: true }" :sort="false" item-key="id">
            <template #item="{element}">
              <div class="drag-item">
                <input type="time" v-model="element.time">
                <div class="whitespace-nowrap">[{{ phaseList[element.round] }}]</div>
                <div v-if="element.division_ch != ''">{{ element.division_ch }}-{{ element.event_ch }}</div>
                <div class="flex-grow" v-else-if="element.division_id == 0 && element.event_code == '00000000' && ['開幕式', '閉幕式', '中場休息'].includes(element.options.title)">{{ element.options.title }}</div>
                <div v-else >
                  <input class="w-full px-1" type="text" v-model="element.options.title">
                </div>
              </div>
            </template>
          </draggable>
        </div>
      </div>
    </div>
    <div class="right-box">
      <template v-for="(item, dateIndex) in dateList" :key="dateIndex">
          <div class="md:w-full lg:w-1/2 xl:w-1/3 border-2 flex-shrink-0 rounded p-2 mr-2 h-full flex flex-col gap-2 overflow-hidden bg-white">
            <div>{{item.date}} - {{item.day}}</div>
            <hr>
            <div class="flex-grow  h-full overflow-hidden p-2 bg-gray-100 shadow-inner">
              <draggable v-model="item.items" :group="{ name: item.date, pull: true, put: true }" :sort="true" item-key="id" class="h-full overflow-auto" @move="moveItem">
                <template #item="{element}">
                  <div class="drag-item">
                    <input type="time" v-model="element.time">
                    <div class="whitespace-nowrap">[{{ phaseList[element.round] }}]</div>
                    <div v-if="element.division_ch != ''">{{ element.division_ch }}-{{ element.event_ch }}</div>
                    <div class="flex-grow" v-else-if="element.division_id == 0 && element.event_code == '00000000' && ['開幕式', '閉幕式', '中場休息'].includes(element.options.title)">{{ element.options.title }}</div>
                    <div v-else >
                      <input class="w-full px-1" type="text" v-model="element.options.title">
                    </div>
                  </div>
                </template>
              </draggable>
            </div>
          </div>
      </template>
    </div>
  </div>
</template>

<style scoped lang="scss">
.left-box {
  @apply h-full flex flex-col gap-2 box-border overflow-hidden;
}
.right-box {
  @apply md:col-span-1 lg:col-span-2 xl:col-span-3 h-full w-full p-3 box-border flex overflow-x-auto overflow-y-hidden bg-gray-100 shadow-inner;
}
.drag-item {
  @apply bg-white border-2 rounded p-2 my-2 cursor-grab duration-200 flex items-center gap-2 shadow w-full;
  input {
    @apply flex-shrink-0 border-2 rounded;
  }
  div {
    @apply flex-grow w-full;
  }
}
.close-button {
  font-size: 18pt;
  float: right;
  cursor: pointer;
  color: #BDBDBD;
  transform: translateY(-5px);
  &:hover {
    color: #353535;
  }
}
</style>