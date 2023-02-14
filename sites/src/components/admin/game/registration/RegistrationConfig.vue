<script setup lang="ts">
  import { ref, reactive } from 'vue';
  import VueRequest from '@/vue-request';
  import { useUserStore } from '@/stores/user';
  import { useRoute } from 'vue-router';
  import type { IRegConfig } from '@/components/library/interfaces';
  import FullModal from '@/components/FullModal.vue';

  const store = useUserStore();
  const vr = new VueRequest(store.token);
  const displayModal = ref(0);
  const configData: any = ref(null);
  const route = useRoute();
  const gameId = Number(route.params.gameId);
  const configPrototype: IRegConfig = {
    game_id: gameId,
    reg_switch: 1,
    deadline_list: {
      division: [],
      all: {
        start: '2023-01-01T12:00:00',
        end: '2023-01-01T12:00:00',
      },
      seperate: false,
    },
    options: {
      common: {
        min_reg_permission: 0,
        allow_grouping: true,
        allow_cross_dept: true,
        allow_cross_org: true,
        individual: {
          max_event_per_athlete: 1,
          max_athlete_per_event: 1,
        },
        group: {
          max_event_per_team: 1,
        }
      },
      division: [],
      event: {
      }
    }
  };
  (async () => {
    await vr.Get(`reg-config-game/${gameId}`, configData);
    if (configData.value.length == 0) {
      await vr.Post('reg-config', configPrototype, null, true, true);
      await vr.Get(`reg-config-game/${gameId}`, configData);
    }
  })();
</script>

<template>
  <div></div>
</template>

<style scoped lang="scss">
    
</style>