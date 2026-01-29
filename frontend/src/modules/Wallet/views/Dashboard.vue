<template>
  <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
    <v-row class="mb-2">
      <v-col cols="12" class="d-flex justify-space-between align-center">
        <!-- Breadcrumb / Header -->
        <div class="d-flex align-center">
            <h1 class="text-h5 font-weight-regular text-grey-darken-1">Dashboard</h1>
            <span class="mx-2 text-grey-lighten-1">|</span>
            <span class="text-h5 font-weight-bold">Acme Corp</span>
        </div>
      </v-col>
    </v-row>

    <!-- Top Row: Total Balance (Left) + Wallet Widgets (Right/Below) -->
    <!-- Reference image shows Total Balance taking full width above or side-by-side? 
         Image shows Total Balance is a large card on top, then 3 small cards below.
         Actually looking at the description again: "Total Balance Card... Wallet Cards... Recent Transactions"
         Let's stack them vertically as standard dashboard flow.
    -->
    
    <!-- Total Balance Section -->
    <v-row>
      <v-col cols="12">
        <TotalBalanceCard />
      </v-col>
    </v-row>

    <!-- Wallet Summary Cards -->
    <v-row>
      <v-col cols="12" md="4">
        <WalletWidget 
            name="Main Wallet" 
            amount="7,200.00" 
            currency="USD"
            symbol="$"
            icon="mdi-wallet-outline"
            color="blue"
        />
      </v-col>
      <v-col cols="12" md="4">
        <WalletWidget 
            name="EUR Wallet" 
            amount="4,500.00" 
            currency="EUR"
            symbol="€"
            icon="mdi-currency-eur"
            color="blue-darken-3"
        />
      </v-col>
      <v-col cols="12" md="4">
        <WalletWidget 
            name="Marketing Wallet" 
            amount="5,300.00" 
            currency="USD"
            symbol="$"
            icon="mdi-bullhorn-outline"
            color="green-darken-2"
        />
      </v-col>
    </v-row>

    <!-- Recent Transactions -->
    <v-row>
      <v-col cols="12">
        <RecentTransactions />
      </v-col>
    </v-row>
    
    <!-- Floating Action Button for Create Wallet (or keep it in sidebar?) 
         User mentioned "Create Wallet button at bottom of sidebar".
         Let's keep a button here too for convenience? 
         Reference implies sidebar action. I will add a FAB just in case.
    -->
    <CreateWalletDialog v-model="showCreateDialog" @created="handleWalletCreated" />
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import TotalBalanceCard from '../components/dashboard/TotalBalanceCard.vue';
import WalletWidget from '../components/dashboard/WalletWidget.vue';
import RecentTransactions from '../components/dashboard/RecentTransactions.vue';
import CreateWalletDialog from '@/modules/Wallet/components/CreateWalletDialog.vue';
import { useWalletStore } from '@/modules/Wallet/store';

// We import store just to have it ready for future API integration
const walletStore = useWalletStore();
const showCreateDialog = ref(false);

function handleWalletCreated() {
    walletStore.fetchWallets();
    // In future, this would refresh the widgets
}
</script>
