<script setup lang="ts">
  import { reactive, ref } from 'vue';
  import { useRouter } from 'vue-router';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import type { Ref } from 'vue';
  const message: Ref<string|null> = ref(null);
  const router = useRouter();
  const store = useUserStore()
  const vr = new VueRequest(store.token);

  interface IGameList {
    [key: string]: any;
  }

  const linkUrl:Ref<string[]|null> = ref(null);
  const gameList:Ref<IGameList|null> = ref(null);

  function getGameList(url = 'game') {
    vr.Get(url).then( (res: any) => {
      gameList.value = res.data;
      linkUrl.value = res.links;
    });
  }
  getGameList();
</script>

<template>
  <div class="main-box">
    <table>
      <tr>
        <th>名稱</th>
        <th>項目</th>
        <th>主辦</th>
        <th>日期</th>
        <th>操作</th>
      </tr>
      <template v-for="(item, index) in gameList" :key="index">
        <tr>
          <td>{{ item.game_name_ch }}</td>
          <td></td>
          <td></td>
          <td>{{ item.event_start }}</td>
          <td></td>
        </tr>
      </template>
    </table>
  </div>
</template>

<style scoped lang="scss">
.main-box {
  @apply bg-white p-5 shadow h-full overflow-auto;
}
table {
  @apply w-full text-left;
  tr, th {
    @apply border-b-[1px] p-1 border-gray-300;
  }
}
</style>