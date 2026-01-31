<template>
  <v-container fluid class="pa-6">
    <v-row class="mb-2">
      <v-col cols="12" class="d-flex justify-space-between align-center">
        <!-- Breadcrumb / Header -->
        <div class="d-flex align-center">
            <h1 class="text-h5 font-weight-regular text-grey-darken-1">Dashboard - Korporatio</h1>
        </div>
      </v-col>
    </v-row>

    <!-- Top Row: Total Balance (Left) + Wallet Widgets (Right/Below) -->
    <!-- Reference image shows Total Balance taking full width above or side-by-side? 
         Image shows Total Balance is a large card on top, then 3 small cards below.
         Actually looking at the description again: "Total Balance Card... Wallet Cards... Recent Transactions"
         Let's stack them vertically as standard dashboard flow.
    -->
    
    <!-- Top Stats Row -->
    <v-row class="mb-6" align="stretch">
      <v-col cols="12" md="6" lg="5">
        <TotalBalanceCard />
      </v-col>
      <v-col cols="12" md="6" lg="7">
        <CompanyInfoCard />
      </v-col>
    </v-row>

    <!-- Wallet Summary Cards -->
    <v-row class="mb-6">

      <v-col 
        v-for="(wallet, index) in walletStore.recentWallets" 
        :key="wallet.id" 
        cols="12" 
        sm="6"
        md="4"
        lg="4"
        xl="4"
      >
        <WalletWidget 
            :name="wallet.name" 
            :amount="Number(wallet.balance || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })" 
            :currency="wallet.currency?.code || 'USD'"
            :symbol="wallet.currency?.symbol || '$'"
            :icon="getWalletIcon(wallet.currency?.code || 'USD')"
            :color="getWalletColor(index)"
            :users-count="wallet.users_count"
        />
      </v-col>
    </v-row>

    <!-- Recent Transactions -->
    <v-row>
      <v-col cols="12">
        <RecentTransactions />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import TotalBalanceCard from '../components/dashboard/TotalBalanceCard.vue';
import CompanyInfoCard from '../components/dashboard/CompanyInfoCard.vue';
import WalletWidget from '../components/dashboard/WalletWidget.vue';
import RecentTransactions from '../components/dashboard/RecentTransactions.vue';
import { useWalletStore } from '@/modules/Wallet/store';
import { useUserStore } from '@/modules/User/store';

// We import store just to have it ready for future API integration
const walletStore = useWalletStore();
const userStore = useUserStore();

onMounted(() => {
    walletStore.fetchWallets();
    userStore.fetchMembers(); // Fetch members for stats
});

function getWalletIcon(code: string) {
    if (code === 'EUR') return 'mdi-currency-eur';
    if (code === 'GBP') return 'mdi-currency-gbp';
    return 'mdi-currency-usd';
}

function getWalletColor(index: number) {
    const colors = ['blue', 'blue-darken-3', 'green-darken-2', 'purple', 'teal'];
    return colors[index % colors.length];
}
</script>
