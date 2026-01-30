<template>
  <v-card class="rounded-lg mt-6" elevation="0" border>
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
    
    <v-table class="pa-2 recent-transactions-table">
      <thead>
        <tr>
          <th class="text-left text-grey-darken-1 font-weight-medium">Date</th>
          <th class="text-left text-grey-darken-1 font-weight-medium">From/To</th>
          <th class="text-left text-grey-darken-1 font-weight-medium">Type</th>
          <th class="text-left text-grey-darken-1 font-weight-medium">Amount</th>
          <th class="text-left text-grey-darken-1 font-weight-medium">Reference</th>
        </tr>
      </thead>
      <tbody>
          <tr v-for="item in transactions" :key="item.id">
            <td class="text-body-2 text-grey-darken-2">{{ item.date }}</td>
            <td>
              <div class="d-flex align-center">
                <v-avatar 
                  size="36" 
                  rounded="lg" 
                  :color="getWalletColor(item.wallet)" 
                  variant="tonal"
                  class="mr-3"
                >
                  <!-- Changed to wallet icon as requested -->
                  <v-icon size="small" :color="getWalletColor(item.wallet)">mdi-wallet-bifold</v-icon>
                </v-avatar>
                <span class="text-body-2 font-weight-medium">{{ item.wallet }}</span>
              </div>
            </td>
            <td>
              <span class="text-body-2">{{ item.type }}</span>
            </td>
            <td>
              <!-- Color logic: Teal for credit, Red for debit -->
              <span :class="['text-body-2 font-weight-bold', item.amount.startsWith('-') ? 'text-red-darken-2' : 'text-teal-darken-2']">
                {{ item.amount }}
              </span>
            </td>
            <td class="text-body-2 text-grey-darken-1">{{ item.reference }}</td>
          </tr>
          <tr v-if="transactions.length === 0">
              <td colspan="5" class="text-center pa-6 text-grey text-body-2">No recent transactions</td>
          </tr>
      </tbody>
    </v-table>
  </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useWalletStore } from '@/modules/Wallet/store';

const router = useRouter();
const walletStore = useWalletStore();

// Map store transactions to display format
const transactions = computed(() => {
  const txs = walletStore.recentGlobalTransactions || [];
  return txs.map(tx => ({
    id: tx.id,
    date: new Date(tx.created_at).toLocaleDateString(),
    wallet: tx.wallet_name || 'Unknown Wallet',
    type: tx.type === 'credit' ? 'Credit' : 'Debit',
    amount: `${tx.type === 'debit' ? '-' : ''}${getCurrencySymbol(tx)}${Number(tx.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`, 
    reference: tx.reference
  })).slice(0, 10); // Limit to 10
});

function getCurrencySymbol(item: any) {
    return item.to_wallet?.currency?.symbol || item.from_wallet?.currency?.symbol || '$';
}

function getWalletColor(name: string) {
    if (name.includes('Main')) return 'blue-darken-2';
    if (name.includes('EUR')) return 'blue-darken-4';
    return 'green-darken-3'; // Default/Others
}
</script>

<style scoped>
.recent-transactions-table :deep(tbody tr:nth-of-type(odd)) {
    background-color: #FAFAFA; /* Very light grey for odd rows */
}
.recent-transactions-table :deep(tbody tr:hover) {
    background-color: #F5F5F5 !important;
}
</style>
