<script setup lang="ts">
  import { ref, inject } from 'vue';
  import type { Ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import { shuffle } from '@/components/library/functions';
  import FullModal from '@/components/FullModal.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const gameData: any = inject('gameData');
  const route = useRoute();
  const isLoading = ref(true);
  const sportCode = route.params.sportCode;
  const gameId = route.params.gameId;
  const paramsData: any = ref([]);
  const paramsList: any = ref([]);
  
  // Generate tournament bracket
  // Round robin
  function generateRoundRobin (teamList: number[]){
    const teamCount = teamList.length;
    const roundCount = teamCount - 1;
    const matchCount = teamCount / 2;
    const matches: any = [];
    for (let i = 0; i < roundCount; i++) {
      matches[i] = [];
      for (let j = 0; j < matchCount; j++) {
        matches[i][j] = [];
        matches[i][j][0] = teamList[j];
        matches[i][j][1] = teamList[teamCount - 1 - j];
      }
      teamList.splice(1, 0, teamList.pop() as number);
    }
    return matches;
  }

  // Single elimination
  // calculate byes
  // if there has byes, set id of byes to null
  // byes must at matches[0]
  // id is increments from 1
  // return matches[round]
  // each matches[round] has id, stage
  // if thirdPlace is true, the third place match will be generated
  // stage includes null, quaterfinal, semifinal, final, thirdplace;
  // if thirdPlace is true, stage of third place match will be thirdplace
  function generateSingleElimination (teamList: number[], thirdPlace: boolean = false) {
    const teamNum = teamList.length;
    shuffle(teamList);
    const matches: any = [];
    const byes = Math.pow(2, Math.ceil(Math.log2(teamNum))) - teamNum;
    const round = Math.ceil(Math.log2(teamNum));
    const teamCount = teamNum + byes;
    let id = 1;
    let teamIndex = 0;
    for (let i = 0; i < round; i++) {
      matches[i] = [];
      const matchCount = teamCount / Math.pow(2, i + 1);
      for (let j = 0; j < matchCount; j++) {
        matches[i][j] = {};
        matches[i][j].id = id;
        if (i == 0) {
          if ((matchCount - j - 1) < byes) {
            matches[i][j].id = null;
            continue;
          }
        }
        if (teamIndex < teamNum) {
          matches[i][j].team1 = teamList[teamIndex];
          matches[i][j].team2 = teamList[teamIndex + 1];
          teamIndex += 2;
        } else {
          matches[i][j].team1 = 0;
          matches[i][j].team2 = 0;
        }
        id++;
      }
    }
    for (let i = 0; i < round; i++) {
      for (let j = 0; j < matches[i].length; j++) {
        if (i == round - 1) {
          matches[i][j].stage = 'final';
        } else if (i == round - 2) {
          matches[i][j].stage = 'semifinal';
        } else if (i == round - 3) {
          matches[i][j].stage = 'quaterfinal';
        } else {
          matches[i][j].stage = null;
        }
      }
    }
    if (thirdPlace) {
      matches[round - 1].push({ id: id, stage: 'thirdplace', team1: 0, team2: 0});
    }
    const temp1: any = [];
    const temp2: any = [];
    for (let i = 0; i < matches[0].length; i++) {
      if (matches[0][i].id == null) {
        temp1.push(matches[0][i]);
      } else {
        temp2.push(matches[0][i]);
      }
    }
    const tempArray: any = Array.apply(undefined, Array(matches[0].length));
    for (let i = 0; i < temp1.length; i++) {
      if (i % 2 == 0) {
        tempArray[i/2] = temp1[i];
      } else {
        tempArray[matches[0].length - Math.ceil(i/2)] = temp1[i];
      }
    }
    let startIndex = tempArray.indexOf(undefined);
    for (let i = 0; i < temp2.length; i++) {
      tempArray[i + startIndex] = temp2[i];
    }
    matches[0] = tempArray;
    return matches;
  }
  function generateDoubleElimination(teamList: number[]) {
    const winnerBracket: any = generateSingleElimination(teamList);
    const loserSeeds: any = [];
    for (let i = 0; i < (teamList.length - 1); i++){
      loserSeeds[i] = i + 1;
    }
    const loserBracket: any = generateSingleElimination(loserSeeds);
    loserBracket.push([]);
    loserBracket[loserBracket.length - 1][0] = { id: loserBracket[loserBracket.length - 2][0].id + 1, stage: 'thirdplace', team1: 0, team2: 0 };
    console.log(winnerBracket, loserBracket);
  }

  async function getData() {
    isLoading.value = true;
    await vr.Get(`game/${sportCode}/${gameId}/main/params`, paramsData, true, true);
    await vr.Get(`game/${sportCode}/${gameId}/main/params/full`, paramsList, true, true);
    isLoading.value = false;
  }
  getData();
  async function submitAll() {
    const res: any = vr.Post(`game/${sportCode}/${gameId}/main/params`, paramsData.value, null, true, true);
    if (res.status == 'A01') {
      alert('已儲存');
    }
  }
</script>

<template>
  <div v-if="isLoading == false">
    <div class="py-3">
      <table class="config-table">
        <tr>
          <th>組別</th>
          <th>項目</th>
          <th>操作</th>
        </tr>
        <template v-for="(item, index) in paramsList" :key="index">
          <tr>
            <td>{{ item.division_ch }}</td>
            <td>{{ item.event_ch }}</td>
            <td>
              <a class="hyperlink blue">設定</a>
            </td>
          </tr>
        </template>
      </table>
      <button class="round-full-button blue" @click="submitAll">儲存</button>
    </div>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left overflow-scroll;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium overflow-hidden;
    div {
      @apply whitespace-nowrap overflow-auto w-full py-2;
    }
  }
  th {
    @apply bg-blue-100 sticky top-0;
  }
}
input[type=checkbox] {
  @apply w-5 h-5;
}
</style>