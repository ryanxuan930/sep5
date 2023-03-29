<script setup lang="ts">
  import { ref, watch } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useGameStore } from '@/stores/game';

  const store = useUserStore();
  const gameStore = useGameStore();
  const vr = new VueRequest(store.token);
  const sportCode = gameStore.data.sport_code;
  const gameId = gameStore.data.game_id;
  const championData: any = ref(null);
  const selectedTab = ref(0);
  const resultList: any = ref([]);

  async function getData() {
    const temp = await vr.Get(`game/${sportCode}/${gameId}/common/temp/gameChampion`);
    if (temp.temp_id == undefined) {
      alert('請先設定總錦標');
    } else {
      championData.value = JSON.parse(temp.temp_data);
    }
  }
  async function calculateChampion(type: string, formula: any, divisionList: number[]) {
    if (type == 'ranking') {
      let orgCode = '';
      let deptId = NaN;
      let index = -1;
      resultList.value = [];
      if (formula.length == 0) {
        alert('尚未設定計算公式');
        return;
      }
      const dataList = await vr.Get(`game/${sportCode}/${gameId}/common/result/ranking/${formula.length}`);
      dataList.sort((a: any, b: any) => a.division_id - b.division_id || a.org_code - b.org_code || a.dept_id - b.dept_id || a.r4_ranking - b.r4_ranking);
      for (const data of dataList) {
        if (divisionList.includes(data.division_id)) {
          if (data.org_code != orgCode || data.dept_id != deptId) {
            index++;
            orgCode = data.org_code;
            deptId = data.dept_id;
            resultList.value[index] = {
              org_code: data.org_code,
              dept_id: data.dept_id,
              org_name_full_ch: data.org_name_full_ch,
              org_name_full_en: data.org_name_full_en,
              org_name_ch: data.org_name_ch,
              org_name_en: data.org_name_en,
              dept_name_ch: data.dept_name_ch,
              dept_name_en: data.dept_name_en,
              ranking: new Array(formula.length).fill(0),
              points: new Array(formula.length).fill(0),
            }
          }
        resultList.value[index].ranking[data.r4_ranking - 1] = data.count;
        resultList.value[index].points[data.r4_ranking - 1] = data.count * formula[data.r4_ranking - 1];
        } else {
          continue;
        }
      }
      resultList.value.forEach((item: any) => {
        item.sum = item.points.reduce((a: number, b: number) => a + b, 0);
      });
      resultList.value.sort((a: any, b: any) => b.sum - a.sum || b.ranking.join('') - a.ranking.join(''));
    }
  }
  (async () => {
    await getData();
    if (championData.value != null) {
      if (championData.value.content.length > 0) {
        calculateChampion(championData.value.content[0].type, championData.value.content[0].formula, championData.value.content[0].divisionList);
      }
    }
  })();
  watch(selectedTab, (val) => {
    calculateChampion(championData.value.content[val].type, championData.value.content[val].formula, championData.value.content[val].divisionList);
  });
</script>

<template>
  <div v-if="championData != null">
    <hr>
    <div class="my-3 text-xl">即時計算結果</div>
    <div class="bookmark">
      <template v-for="(item, index) in championData.content" :key="index">
        <div>
          <button :class="{'item': true, 'active': selectedTab == index}" @click="selectedTab = index">{{ item.divisionName }}</button>
        </div>
      </template>
    </div>
    <hr>
    <div>
      <div v-if="championData.content[selectedTab].type == 'ranking'">
        <table class="config-table">
          <tr>
            <th>排名</th>
            <th>組織單位</th>
            <th>分部/系所</th>
            <template v-for="(item, index) in championData.content[selectedTab].formula" :key="index">
              <th>{{ index + 1 }}</th>
            </template>
            <th>總計</th>
          </tr>
          <template v-for="(item, index) in resultList" :key="index">
            <tr>
              <td :class="{'text-blue-400': index + 1 <= championData.content[selectedTab].qualified}">{{ index + 1 }}</td>
              <td>{{ item.org_name_full_ch }}</td>
              <td>{{ item.dept_name_ch }}</td>
              <template v-for="point in item.points">
                <td>{{ point }}</td>
              </template>
              <td>{{ item.sum }}</td>
            </tr>
          </template>
        </table>
        <div class="p-2 text-center" v-if="resultList.length == 0">目前無資料</div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.bookmark {
  @apply flex gap-[1px] items-center;
  .item {
    @apply bg-blue-50 text-lg py-2 px-4 font-medium rounded-t hover:bg-blue-400 hover:text-white duration-150;
    &.active {
      @apply bg-blue-400 text-white;
    }
  }
}
</style>