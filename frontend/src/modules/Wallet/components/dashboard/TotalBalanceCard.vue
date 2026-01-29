<template>
  <v-card 
    class="total-balance-card rounded-xl overflow-hidden h-100" 
    elevation="0" 
    border
    style="border-left: 6px solid rgb(var(--v-theme-primary)) !important;"
  >
      <div class="card-content pa-6 d-flex flex-column justify-space-between fill-height">
          <div>
              <div class="d-flex align-center mb-4">
                   <v-avatar color="primary" variant="tonal" size="40" class="mr-3 rounded-lg">
                       <v-icon color="primary">mdi-wallet-bifold</v-icon>
                   </v-avatar>
                   <div class="text-subtitle-1 font-weight-medium text-grey-darken-1">Total Balance</div>
              </div>
              
              <div class="balance-list">
                <template v-if="hasBalances">
                    <div v-for="(amount, currency) in walletStore.totalBalanceByCurrency" :key="currency" class="d-flex align-baseline mb-1">
                        <!-- Simple currency symbol mapping -->
                        <span class="text-h5 font-weight-bold ml-1 mr-2 text-grey-darken-3">{{ currency }}</span>
                        <span class="text-h3 font-weight-bold text-grey-darken-4">{{ amount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                    </div>
                </template>
                <template v-else>
                    <div class="text-h3 font-weight-bold text-grey-darken-4">$0.00</div>
                </template>
              </div>
          </div>

          <div class="d-flex justify-space-between align-end mt-4">
              <div class="d-flex gap-2">
                  <v-chip size="small" color="success" variant="tonal" class="font-weight-medium">
                      +12.5% <v-icon size="small" class="ml-1">mdi-arrow-up</v-icon>
                  </v-chip>
                  <span class="text-caption align-self-center text-grey ml-2">vs last month</span>
              </div>
              
              <!-- Decorative chart line/vector -->
              <v-icon size="64" class="chart-icon text-primary opacity-10">mdi-chart-timeline-variant</v-icon>
          </div>
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
