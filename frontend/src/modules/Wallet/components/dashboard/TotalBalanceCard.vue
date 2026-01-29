<template>
  <v-card class="total-balance-card rounded-xl text-white overflow-hidden" elevation="6">
      <div class="card-content pa-6 d-flex flex-column justify-space-between fill-height">
          <div>
              <div class="d-flex align-center mb-4">
                   <v-avatar color="white" variant="tonal" size="40" class="mr-3 rounded-lg">
                       <v-icon color="white">mdi-wallet-bifold</v-icon>
                   </v-avatar>
                   <div class="text-subtitle-1 font-weight-medium opacity-90">Total Balance</div>
              </div>
              
              <div class="balance-list">
                <template v-if="hasBalances">
                    <div v-for="(amount, currency) in walletStore.totalBalanceByCurrency" :key="currency" class="d-flex align-baseline mb-1">
                        <!-- Simple currency symbol mapping -->
                        <span class="text-h5 font-weight-bold ml-1 mr-2">{{ currency }}</span>
                        <span class="text-h3 font-weight-bold">{{ amount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                    </div>
                </template>
                <template v-else>
                    <div class="text-h3 font-weight-bold">$0.00</div>
                </template>
              </div>
          </div>

          <div class="d-flex justify-space-between align-end mt-4">
              <div class="d-flex gap-2">
                  <v-chip size="small" color="white" variant="outlined" class="font-weight-medium">
                      +12.5% <v-icon size="small" class="ml-1">mdi-arrow-up</v-icon>
                  </v-chip>
                  <span class="text-caption align-self-center opacity-70 ml-2">vs last month</span>
              </div>
              
              <!-- Decorative chart line/vector -->
              <v-icon size="64" class="chart-icon opacity-20">mdi-chart-timeline-variant</v-icon>
          </div>
      </div>
      
      <!-- Background decorators -->
      <div class="circle-1"></div>
      <div class="circle-2"></div>
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
    background: linear-gradient(135deg, #1A237E 0%, #0D47A1 100%);
    position: relative;
    min-height: 200px;
    width: 100%; /* Ensure full width */
}

.chart-icon {
    position: absolute;
    right: -10px;
    bottom: -10px;
    opacity: 0.1;
    font-size: 120px !important;
}

.circle-1, .circle-2 {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    z-index: 0;
}

.circle-1 {
    width: 200px;
    height: 200px;
    top: -50px;
    right: -50px;
}

.circle-2 {
    width: 150px;
    height: 150px;
    bottom: -30px;
    left: 20%;
}

.card-content {
    position: relative;
    z-index: 1;
}
</style>
