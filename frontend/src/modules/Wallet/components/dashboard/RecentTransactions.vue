<template>
  <v-card class="rounded-0 mt-6" elevation="0" border>
    <v-card-title class="pa-4 d-flex align-center justify-space-between text-h6 font-weight-bold">
      Recent Transactions
      <v-btn
        variant="text"
        color="primary"
        class="text-capitalize text-body-2"
        @click="router.push('/transactions')"
      >
        View All
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    
    <TransactionTable
      :loading="walletStore.dashboardLoading"
      :items="walletStore.dashboardTransactions"
      :total-items="walletStore.dashboardTotalItems"
      :page="walletStore.dashboardPage"
      :items-per-page="walletStore.dashboardItemsPerPage"
      @update:options="handleOptionsUpdate"
      @update:page="handlePageChange"
      @view-details="handleViewDetails"
    />
  </v-card>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWalletStore } from '@/modules/Wallet/store';
import TransactionTable from '@/modules/Transaction/components/TransactionTable.vue';
import type { Transaction } from '@/modules/Transaction/api';

const router = useRouter();
const walletStore = useWalletStore();

onMounted(() => {
    // Initial fetch for dashboard is triggered by the table's update:options or we can call it here if needed.
    // However, TransactionTable's v-data-table-server usually triggers an initial load via update:options.
    // But since we want to be sure and consistent with previous behavior:
    walletStore.fetchDashboardTransactions(1, 10);
});

function handlePageChange(newPage: number) {
    walletStore.fetchDashboardTransactions(newPage, 10);
}

function handleOptionsUpdate(options: { page: number; itemsPerPage: number }) {
    // Ensure we don't double-fetch if specific logic prevents it, but usually safe to call store action
    walletStore.fetchDashboardTransactions(options.page, 10);
}

function handleViewDetails(item: Transaction) {
    router.push(`/transactions/${item.id}`);
}
</script>

<style scoped>
/* Reuse styles from TransactionTable or keep specific overrides if needed */
</style>
