<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row>
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center mb-6">
          <h1 class="text-h5 font-weight-regular text-grey-darken-1">Currencies - Korporatio</h1>
          <v-btn
            v-if="isAdmin"
            color="primary"
            prepend-icon="mdi-plus"
            elevation="2"
            class="text-capitalize"
            :to="{ name: 'CurrencyCreate' }"
          >
            Create Currency
          </v-btn>
        </div>

        <CurrencyTable 
            :currencies="currencyStore.currencies" 
            :loading="currencyStore.loading" 
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue';
import { useCurrencyStore } from '../store';
import { useUserStore } from '@/modules/User/store';
import CurrencyTable from '../components/CurrencyTable.vue';

const currencyStore = useCurrencyStore();
const userStore = useUserStore();

const isAdmin = computed(() => userStore.currentUser?.role === 'admin');

onMounted(async () => {
    // Ensure user loaded
    await userStore.fetchCurrentUser();
    currencyStore.fetchCurrencies();
});
</script>
