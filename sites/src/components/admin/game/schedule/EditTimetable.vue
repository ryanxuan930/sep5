<script setup lang="ts">
  import { ref, inject, watch } from 'vue';
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
</script>

<template>
  <div class="border-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 h-full overflow-hidden gap-3">
    <div class="left-box">
      <div class="flex gap-2 py-2">
        <button class="general-button blue">儲存</button>
        <button class="general-button blue">智慧預排</button>
        <button class="general-button red">全部清除</button>
      </div>
      <div class="p-2 bg-gray-50 shadow-inner">
        <draggable v-model="fixedList" :group="{ name: 'fixed-list', pull: 'clone', put: false }" :sort="false" item-key="id" :clone="clone">
          <template #item="{element}">
            <div class="drag-item">
              <input type="time" v-model="element.time">
              <div class="whitespace-nowrap">[{{ phaseList[element.round] }}]</div>
              <div class="flex-grow" v-if="element.division_id == 0 && element.event_code == '00000000' && ['開幕式', '閉幕式', '中場休息'].includes(element.options.title)">{{ element.options.title }}</div>
              <div v-else >
                <input class="w-full px-1" type="text" v-model="element.options.title">
              </div>
            </div>
          </template>
        </draggable>
      </div>
      <hr>
      <div class="flex-grow"></div>
    </div>
    <div class="right-box"></div>
  </div>
</template>

<style scoped lang="scss">
.left-box {
  @apply h-full flex flex-col gap-2;
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