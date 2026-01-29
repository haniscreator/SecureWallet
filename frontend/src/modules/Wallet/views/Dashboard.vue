<template>
  <v-container>
    <v-row class="mb-4">
      <v-col cols="12">
        <h1 class="text-h4 font-weight-bold">Dashboard</h1>
      </v-col>
    </v-row>

    <!-- Summary Cards -->
    <v-row>
      <v-col cols="12" md="4">
        <v-card color="primary" theme="dark">
          <v-card-text>
            <div class="text-overline mb-1">Total Balance (Est.)</div>
            <div class="text-h4 font-weight-bold">$ {{ totalBalance.toFixed(2) }}</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Wallet List -->
    <v-row class="mt-6">
      <v-col cols="12" class="d-flex justify-space-between align-center">
        <h2 class="text-h5">My Wallets</h2>
        <v-btn color="primary" prepend-icon="mdi-plus" @click="showCreateDialog = true">
          New Wallet
        </v-btn>
      </v-col>

      <v-col v-if="walletStore.loading" cols="12" class="text-center">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
      </v-col>

      <v-col 
        v-else 
        v-for="wallet in walletStore.wallets" 
        :key="wallet.id" 
        cols="12" 
        md="6" 
        lg="4"
      >
        <v-card 
          variant="outlined" 
          hover 
          :to="{ name: 'WalletDetails', params: { id: wallet.id } }"
        >
          <v-card-item>
            <v-card-title class="d-flex justify-space-between align-center">
              {{ wallet.name }}
              <v-chip :color="wallet.status ? 'success' : 'error'" size="small">
                {{ wallet.status ? 'Active' : 'Inactive' }}
              </v-chip>
            </v-card-title>
            <v-card-subtitle>{{ wallet.currency?.code || 'USD' }}</v-card-subtitle>
          </v-card-item>

          <v-card-text class="pt-4">
            <div class="text-h5 font-weight-bold">
              {{ wallet.currency?.symbol || '$' }} {{ Number(wallet.balance).toFixed(2) }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
      
      <v-col v-if="!walletStore.loading && walletStore.wallets.length === 0" cols="12">
        <v-alert type="info" variant="tonal">
          No wallets found. Create one to get started!
        </v-alert>
      </v-col>
    </v-row>

    <!-- Dialog -->
    <CreateWalletDialog v-model="showCreateDialog" @created="walletStore.fetchWallets()" />

  </v-container>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useWalletStore } from '@/modules/Wallet/store';
import CreateWalletDialog from '@/modules/Wallet/components/CreateWalletDialog.vue';

const walletStore = useWalletStore();
const showCreateDialog = ref(false);

const totalBalance = computed(() => {
    // Simple sum for now, assuming 1:1 conversion or just base currency sum
    // In real app we need conversation rates
    return walletStore.wallets.reduce((acc, w) => acc + Number(w.balance), 0);
});

onMounted(() => {
  walletStore.fetchWallets();
});
</script>
