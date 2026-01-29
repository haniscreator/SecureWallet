<template>
  <v-card class="company-info-card rounded-xl overflow-hidden fill-height" elevation="0" border>
      <div class="d-flex fill-height">
          <!-- Left Side: Hero Stat -->
          <div class="left-panel d-flex flex-column justify-center align-center px-6 py-4 bg-grey-lighten-5">
              <div class="shield-icon-wrapper mb-2">
                 <v-icon color="#FF5252" size="24" class="shield-badge">mdi-shield-check</v-icon>
                 <v-icon color="primary" size="48">mdi-domain</v-icon>
              </div>
              <div class="text-h4 font-weight-bold text-grey-darken-3">{{ totalWallets }}</div>
              <div class="text-caption text-grey-darken-1 mt-1">Total Wallets</div>
          </div>

          <v-divider vertical></v-divider>

          <!-- Right Side: Detailed Stats -->
          <div class="right-panel flex-grow-1 pa-6 d-flex flex-column justify-center bg-white">
              <div class="text-h6 font-weight-bold mb-3 text-grey-darken-3">Company Overview</div>
              
              <div class="stats-grid">
                  <div class="d-flex align-center mb-2">
                       <span class="text-grey-darken-1 text-body-2 mr-2">Total Users:</span>
                       <span class="font-weight-bold text-grey-darken-3">{{ totalUsers }}</span>
                  </div>
                  <div class="d-flex align-center mb-2">
                       <span class="text-grey-darken-1 text-body-2 mr-2">Currencies:</span>
                       <span class="font-weight-bold text-grey-darken-3">{{ totalCurrencies }}</span>
                  </div>
                  <div class="d-flex align-center mb-2">
                       <span class="text-grey-darken-1 text-body-2 mr-2">Tx Volume:</span>
                       <span class="font-weight-bold text-grey-darken-3">{{ totalTxAmount }}</span>
                  </div>
              </div>

              <div class="mt-4 pt-3 border-top-light">
                  <div class="d-flex align-center text-body-2">
                      <v-icon size="small" class="mr-2 text-grey-darken-1">mdi-office-building</v-icon> 
                      <span class="font-weight-medium text-grey-darken-3 text-truncate">{{ companyName }}</span>
                  </div>
                   <div class="d-flex align-center text-caption mt-1">
                      <v-icon size="small" class="mr-2 text-grey-darken-1">mdi-email-outline</v-icon> 
                      <span class="text-grey-darken-1 text-truncate">{{ companyEmail }}</span>
                  </div>
              </div>
          </div>
      </div>
  </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useWalletStore } from '@/modules/Wallet/store';
import { useUserStore } from '@/modules/User/store';
import { useAuthStore } from '@/modules/Auth/store';

const walletStore = useWalletStore();
const userStore = useUserStore();
const authStore = useAuthStore();

// Computed Stats
const totalWallets = computed(() => walletStore.wallets.length);
const totalUsers = computed(() => userStore.members.length); 
const totalCurrencies = computed(() => Object.keys(walletStore.totalBalanceByCurrency).length);
const companyName = computed(() => authStore.user?.company_name || 'Acme Corp');
const companyEmail = computed(() => authStore.user?.email || 'admin@korporatio.com');

const totalTxAmount = computed(() => {
    // Sum absolute amount of all transactions just for a "Volume" stat
    const sum = walletStore.recentGlobalTransactions.reduce((acc, tx) => acc + Math.abs(Number(tx.amount)), 0);
    return sum.toLocaleString('en-US', { style: 'currency', currency: 'USD' }); // Simplified currency
});
</script>

<style scoped>
.company-info-card {
    background: white;
    min-height: 200px;
    position: relative;
}

.left-panel {
    min-width: 120px;
    background: #FAFAFA; /* Light grey */
}

.shield-icon-wrapper {
    background: white;
    border-radius: 50%;
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05); /* Lighter shadow */
}

.shield-badge {
    position: absolute;
    bottom: -4px;
    right: -4px;
    background: white;
    border-radius: 50%;
    padding: 2px;
}

.border-top-light {
    border-top: 1px solid rgba(0,0,0,0.05);
}
</style>
