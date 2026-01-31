<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row>
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center mb-6">
          <h1 class="text-h5 font-weight-regular text-grey-darken-1">Wallets - Korporatio</h1>
          <v-btn
            v-if="isAdmin"
            color="primary"
            prepend-icon="mdi-plus"
            elevation="2"
            class="text-capitalize"
            :to="{ name: 'WalletCreate' }"
          >
            Create Wallet
          </v-btn>
        </div>

        <!-- Filters -->
        <WalletFilter 
            :currencies="currencies" 
            @apply-filter="onApplyFilter" 
        />

        <!-- Wallets Table -->
        <WalletTable 
            :wallets="walletStore.wallets" 
            :loading="walletStore.loading" 
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useWalletStore } from '../store';
import { currencyApi, type Currency } from '@/modules/Currency/api';
import { useUserStore } from '@/modules/User/store';
import WalletFilter from '../components/WalletFilter.vue';
import WalletTable from '../components/WalletTable.vue';

const walletStore = useWalletStore();
const userStore = useUserStore();
const currencies = ref<Currency[]>([]);

const isAdmin = computed(() => userStore.currentUser?.role === 'admin');

async function fetchCurrencies() {
    try {
        const res = await currencyApi.getCurrencies();
        const data = res.data as any;
        currencies.value = Array.isArray(data) ? data : (data.data || []);
    } catch (e) {
        console.error('Failed to fetch currencies');
    }
}

function onApplyFilter(filters: any) {
    walletStore.fetchWallets(filters);
}

onMounted(async () => {
    await userStore.fetchCurrentUser();
    walletStore.fetchWallets(); // Initial fetch
    fetchCurrencies();
});
</script>
