<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center mb-6">
        <h1 class="text-h5 font-weight-regular text-grey-darken-1">Team Members - Korporatio</h1>
        <v-btn
            v-if="isAdmin"
            color="primary"
            height="44"
            class="text-capitalize px-6"
            elevation="0"
            @click="router.push('/members/create')"
        >
          + Add Member
        </v-btn>
      </v-col>

      <v-col cols="12">
        <MemberTable
            :members="userStore.members"
            :loading="userStore.loading"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '@/modules/User/store'; 
import MemberTable from '../components/MemberTable.vue';

const router = useRouter();
const userStore = useUserStore();

const isAdmin = computed(() => userStore.currentUser?.role === 'admin');

onMounted(async () => {
  await userStore.fetchCurrentUser();
  userStore.fetchMembers();
});
</script>
