<script setup lang="ts">
import { ref, inject, watch } from 'vue';
import type { Ref } from 'vue';
import { useUserStore } from '@/stores/user';

const store = useUserStore();
const gameData: any = inject('gameData');
const props: any = defineProps(['inputData', 'athleteData', 'paramList']);
const items: any = ref(props.athleteData);
const qualified: Ref<number> = ref(8);

let hasData = true;
items.value.forEach((item:any ) => {
  if (item[`r${props.inputData.round}_options`].performance.format == undefined || item[`r${props.inputData.round}_options`].performance.format != 'distance') {
    console.log(1);
    item[`r${props.inputData.round}_options`].performance = {
      format: 'distance',
      attempt: ['', '', '', '', '', ''],
      winds: ['', '', '', '', '', ''],
      first_result: '',
      first_rank: 0,
      final_order: 0,
      final_result: '',
      final_rank: 0,
      final_wind: '',
    };
    hasData = false;
  }
});

if ( hasData ) {
  sessionStorage.setItem(`${props.inputData.division_id}${props.inputData.event_code}${props.inputData.round}`, JSON.stringify({items: items.value, qualified: qualified.value}));
}
const data = sessionStorage.getItem(`${props.inputData.division_id}${props.inputData.event_code}${props.inputData.round}`);
if (data) {
  items.value = JSON.parse(data).items;
  qualified.value = JSON.parse(data).qualified;
} else {
  sessionStorage.setItem(`${props.inputData.division_id}${props.inputData.event_code}${props.inputData.round}`, JSON.stringify({items: items.value, qualified: qualified.value}));
}

function top3Handler(input: string[]) {
  const result = input.map((item: string) => {
    const num = Number(item);
    return isNaN(num) ? 0 : num;
  });
  return Math.max(result[0], result[1], result[2]).toFixed(2);
}
function topHandler(input: string[]) {
  const result = input.map((item: string) => {
    const num = Number(item);
    return isNaN(num) ? 0 : num;
  });
  return Math.max(...result).toFixed(2);
}

function autoFormatter(input: any, index: string|number) {
  const val = input[index];
  if (val.length > 0 && !val.includes('.') && !val.includes(':')) {
    if (val.toUpperCase() == 'D' || val.toUpperCase() == 'S') {
      input[index] = 'DNS';
    } else if (val.toUpperCase() == 'F') {
      input[index] = 'DNF';
    } else if (val.toUpperCase() == 'Q') {
      input[index] = 'DQ';
    } else if (val.toUpperCase() == 'M') {
      input[index] = 'NM';
    }else if (val.toUpperCase() == 'X') {
      input[index] = 'X';
    } else {
      if (val.length == 3) {
        input[index] = val.substring(0, 1) + '.' + val.substring(1, 3);
      } else if (val.length == 4) {
        input[index] = val.substring(0, 2) + '.' + val.substring(2, 4);
      } else {
        alert('無法自動格式化');
      }
    }
    return;
  }
}

function tempRank(num: number) {
  const temps:any = [];
  let flag = false;
  items.value.map((item: any) => {
    const tempList = item[`r${props.inputData.round}_options`].performance.attempt.slice(0,num);
    // if tempList has '' then return
    if (tempList.includes('')) {
      flag = true;
    }
    tempList.sort((a: string, b: string) => {
      const aNum = Number(a);
      const bNum = Number(b);
      if (isNaN(aNum) && isNaN(bNum)) {
        return 0;
      } else if (isNaN(aNum)) {
        return 1;
      } else if (isNaN(bNum)) {
        return -1;
      } else {
        return bNum - aNum;
      }
    });
    temps.push({
      id: item.u_id,
      attempt: tempList,
    });
  });
  if (flag) {
    alert('請確認是否都填入成績');
    return;
  }
  temps.sort((a: any, b: any) => {
    for (let i = 0; i < num; i++) {
      const aNum = Number(a.attempt[i]);
      const bNum = Number(b.attempt[i]);
      if (isNaN(aNum) && isNaN(bNum)) {
          continue;
      } else if (isNaN(aNum)) {
          return 1;
      } else if (isNaN(bNum)) {
          return -1;
      } else if (aNum !== bNum) {
          return bNum - aNum;
      }
    }
    const aX = a.filter((item: string) => item === 'X').length;
    const bX = b.filter((item: string) => item === 'X').length;
    return aX - bX;
  });
  /* set performance.first_rank from temps by u_id */
  temps.forEach((item: any, index: number) => {
    if (num == 3) {
      const athlete = items.value.find((athlete: any) => athlete.u_id == item.id);
      athlete[`r${props.inputData.round}_options`].performance.first_rank = Number(top3Handler(item.attempt)) == 0 ? 0 : index + 1;
      athlete[`r${props.inputData.round}_options`].performance.final_order = athlete[`r${props.inputData.round}_options`].performance.first_rank <= qualified.value && athlete[`r${props.inputData.round}_options`].performance.first_rank != 0 ? athlete[`r${props.inputData.round}_options`].performance.first_rank : '-';
    } else {
      const athlete = items.value.find((athlete: any) => athlete.u_id == item.id);
      athlete[`r${props.inputData.round}_options`].performance.final_rank = Number(topHandler(item.attempt)) == 0 ? 0 : index + 1;
      athlete[`r${props.inputData.round}_options`].performance.final_result = Number(topHandler(item.attempt)) == 0 ? 'NM' : topHandler(item.attempt);
      athlete[`r${props.inputData.round}_options`].performance.final_wind = athlete[`r${props.inputData.round}_options`].performance.winds[item.attempt.indexOf(topHandler(item.attempt))] == '' ? 'NWI' : athlete[`r${props.inputData.round}_options`].performance.winds[item.attempt.indexOf(topHandler(item.attempt))];
    }
  });
}

// watch items and save data to sessionStorage
watch(items, () => {
  sessionStorage.setItem(`${props.inputData.division_id}${props.inputData.event_code}${props.inputData.round}`, JSON.stringify({items: items.value, qualified: qualified.value}));
}, { deep: true });
watch(qualified, () => {
  sessionStorage.setItem(`${props.inputData.division_id}${props.inputData.event_code}${props.inputData.round}`, JSON.stringify({items: items.value, qualified: qualified.value}));
});

const emit = defineEmits<{(e: 'returnData', value: any): any, (e: 'closeModal'): void}>();

function save() {
  emit('returnData', items.value);
}
</script>

<template>
  <div class="flex items-center gap-2 p-2 bg-blue-50">
    <div>取</div>
    <div>
      <input class="rounded px-1 py-0.5 border w-16" type="number" v-model="qualified">
    </div>
    <div>位晉級</div>
    <div class="flex gap-2">
      <button @click="tempRank(3)" class="general-button blue">前三排序</button>
      <button @click="tempRank(6)" class="general-button blue">最終排序</button>
      <button @click="save" class="general-button blue">暫存成績</button>
    </div>
  </div>
  <table>
    <thead>
      <tr>
        <th>選手</th>
        <th>組別</th>
        <th>順序</th>
        <th class="w-20">1</th>
        <th class="w-20">2</th>
        <th class="w-20">3</th>
        <th>前三最佳</th>
        <th>前三排名</th>
        <th>最終順序</th>
        <th class="w-20">4</th>
        <th class="w-20">5</th>
        <th class="w-20">6</th>
        <th>最佳成績</th>
        <th>名次</th>
      </tr>
    </thead>
    <tbody>
      <template v-for="(item, index) in items" :key="index">
        <tr>
          <td>
            <div>{{ item.last_name_ch }}{{ item.first_name_ch }}</div>
            <div class="text-sm">{{ item.org_name_ch }}</div>
            <div class="text-sm"></div>
          </td>
          <td>{{ item[`r${props.inputData.round}_heat`] }}</td>
          <td>{{ item[`r${props.inputData.round}_lane`] }}</td>
          <td>
            <input type="text" class="input-blank" @keyup.enter="autoFormatter(item[`r${props.inputData.round}_options`].performance.attempt, 0)" placeholder="成績" v-model="item[`r${props.inputData.round}_options`].performance.attempt[0]">
            <input type="text" class="input-blank" placeholder="風速" v-model="item[`r${props.inputData.round}_options`].performance.winds[0]">
          </td>
          <td>
            <input type="text" class="input-blank" @keyup.enter="autoFormatter(item[`r${props.inputData.round}_options`].performance.attempt, 1)" placeholder="成績" v-model="item[`r${props.inputData.round}_options`].performance.attempt[1]">
            <input type="text" class="input-blank" placeholder="風速" v-model="item[`r${props.inputData.round}_options`].performance.winds[1]">
          </td>
          <td>
            <input type="text" class="input-blank" @keyup.enter="autoFormatter(item[`r${props.inputData.round}_options`].performance.attempt, 2)" placeholder="成績" v-model="item[`r${props.inputData.round}_options`].performance.attempt[2]">
            <input type="text" class="input-blank" placeholder="風速" v-model="item[`r${props.inputData.round}_options`].performance.winds[2]">
          </td>
          <td>{{ top3Handler(item[`r${props.inputData.round}_options`].performance.attempt) }}</td>
          <td>{{ item[`r${props.inputData.round}_options`].performance.first_rank }}</td>
          <td>{{ item[`r${props.inputData.round}_options`].performance.final_order }}</td>
          <td>
            <input type="text" class="input-blank" @keyup.enter="autoFormatter(item[`r${props.inputData.round}_options`].performance.attempt, 3)" placeholder="成績" v-model="item[`r${props.inputData.round}_options`].performance.attempt[3]">
            <input type="text" class="input-blank" placeholder="風速" v-model="item[`r${props.inputData.round}_options`].performance.winds[3]">
          </td>
          <td>
            <input type="text" class="input-blank" @keyup.enter="autoFormatter(item[`r${props.inputData.round}_options`].performance.attempt, 4)" placeholder="成績" v-model="item[`r${props.inputData.round}_options`].performance.attempt[4]">
            <input type="text" class="input-blank" placeholder="風速" v-model="item[`r${props.inputData.round}_options`].performance.winds[4]">
          </td>
          <td>
            <input type="text" class="input-blank" @keyup.enter="autoFormatter(item[`r${props.inputData.round}_options`].performance.attempt, 5)" placeholder="成績" v-model="item[`r${props.inputData.round}_options`].performance.attempt[5]">
            <input type="text" class="input-blank" placeholder="風速" v-model="item[`r${props.inputData.round}_options`].performance.winds[5]">
          </td>
          <td>{{ item[`r${props.inputData.round}_options`].performance.final_result }}</td>
          <td>{{ item[`r${props.inputData.round}_options`].performance.final_rank }}</td>
        </tr>
      </template>
    </tbody>
  </table>
</template>

<style scoped lang="scss">
table {
  @apply w-full bg-gray-50 border-collapse;
  thead {
    @apply bg-blue-100 top-0 sticky;
  }
  th, td {
    @apply p-1 text-center border;
  }
  .input-blank {
    @apply w-16 py-0.5 px-1 border my-0.5;
  }
}
</style>