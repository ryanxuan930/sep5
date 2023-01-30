<script setup lang="ts">
  import { ref } from 'vue';
  import VueRequest from '@/vue-request';
  import { useAdminStore } from '@/stores/admin';
  import type { Ref } from 'vue';
  import { getUrlParams, paginationText } from '@/components/library/functions';

  const store = useAdminStore();
  const vr = new VueRequest(store.token);

  interface IObjList {
    [key: string]: any;
  }
  const linkUrl:Ref<IObjList[]|null> = ref(null);
  const gameList:Ref<IObjList|null> = ref(null);

  function getGameList(url = 'game') {
    let suffix = '-all';
    const page = getUrlParams(url, 'page');
    if (page !== null) {
      url = `${store.userInfo.admin_org_id}/game${suffix}?page=${page}`;
    } else {
      url = `${store.userInfo.admin_org_id}/game${suffix}`;
    }
    vr.Get(url).then( (res: any) => {
      gameList.value = res.data;
      linkUrl.value = res.links;
    });
  }
  getGameList();

  const selectedGame = ref(0);
  const emit = defineEmits<{(e: 'returnData', value: number): void, (e: 'closeModal'): void}>();
  const submit = () => {
    emit('returnData', selectedGame.value);
    emit('closeModal');
  }
</script>

<template>
  <div>
    <table>
      <tr>
        <th></th>
        <th>名稱</th>
        <th>項目</th>
        <th>日期</th>
      </tr>
      <tr>
        <td>
          <input :value="0" type="radio" name="game_id" v-model="selectedGame">
        </td>
        <td colspan="3">無賽事</td>
      </tr>
      <template v-for="(item, index) in gameList" :key="index">
        <tr>
          <td>
            <input :value="item.game_id" type="radio" name="game_id" v-model="selectedGame">
          </td>
          <td>{{ item.game_name_ch }}</td>
          <td>{{ item.sport_name_ch }}</td>
          <td>{{ item.event_start }}</td>
        </tr>
      </template>
    </table>
    <div class="page-btn">
      <template v-for="(item, index) in linkUrl" :key="index">
        <button :class="{'general-button': true, 'blue': !item.active, 'active': item.active }" :disabled="item.url===null" @click="getGameList(item.url)">{{ paginationText(item.label) }}</button>
      </template>
    </div>
    <button class="round-full-button blue" @click="submit">確定</button>
  </div>
</template>

<style scoped lang="scss">
table {
  @apply w-[768px] md:w-full text-left;
  td, th {
    @apply border-b-[1px] p-2 border-gray-300 font-medium;
  }
  th {
    @apply bg-blue-100;
  }
}
input[type=radio] {
  @apply border-0 w-full h-6;
}
.page-btn {
  @apply flex my-5 gap-3;
}
.general-button.active {
  @apply bg-blue-400;
}
</style>