<template>
  <v-card 
    class="total-balance-card rounded-0 overflow-hidden h-100" 
    elevation="0" 
    border
    style="border-left: 6px solid rgb(var(--v-theme-primary)) !important;"
  >
      <div class="card-content pa-6 d-flex flex-column justify-space-between fill-height">
          <div>
              <div class="d-flex align-center">
                   <v-avatar color="primary" variant="tonal" size="40" class="mr-3 rounded-0">
                       <v-icon color="primary">mdi-wallet-bifold</v-icon>
                   </v-avatar>
                   <div class="text-h6 font-weight-bold text-grey-darken-3">Total Balance</div>
              </div>
              <v-divider class="my-4"></v-divider>
              
              <div class="balance-list">
                <template v-if="hasBalances">
                    <div v-for="item in sortedBalances" :key="item.currency" class="d-flex align-baseline mb-2">
                        <span class="text-h5 font-weight-bold text-grey-darken-4 mr-2">
                            {{ item.symbol }}{{ item.amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                        </span>
                        <span class="text-h6 font-weight-medium text-grey-darken-1">{{ item.currency }}</span>
                    </div>
                </template>
                <template v-else>
                    <div class="text-h4 font-weight-bold text-grey-darken-4">$0.00</div>
                </template>
              </div>
          </div>
          
          <!-- Decorative chart line/vector -->
          <v-icon size="64" class="chart-icon text-primary opacity-10">mdi-chart-timeline-variant</v-icon>
      </div>
  </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useWalletStore } from '@/modules/Wallet/store';

const walletStore = useWalletStore();

// Safe computed wrapper to avoid template errors during loading/HMR
const hasBalances = computed(() => {
    return walletStore.totalBalanceByCurrency && Object.keys(walletStore.totalBalanceByCurrency).length > 0;
});

const sortedBalances = computed(() => {
    if (!hasBalances.value) return [];
    
    // Convert dictionary to array
    // Store returns Record<string, { amount: number, symbol: string }>
    const balances = Object.entries(walletStore.totalBalanceByCurrency).map(([currency, data]) => ({
        currency,
        amount: data.amount,
        symbol: data.symbol
    }));

    // Sort by amount descending
    return balances.sort((a, b) => b.amount - a.amount);
});
</script>

<style scoped>
.total-balance-card {
    background: white;
    position: relative;
    min-height: 200px;
    width: 100%; /* Ensure full width */
}

.chart-icon {
    position: absolute;
    right: -10px;
    bottom: -10px;
    font-size: 120px !important;
}

.card-content {
    position: relative;
    z-index: 1;
}
</style>
