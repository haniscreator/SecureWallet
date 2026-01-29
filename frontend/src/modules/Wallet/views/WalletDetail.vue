<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-btn variant="text" prepend-icon="mdi-arrow-left" to="/dashboard" class="mb-4">
          Back to Dashboard
        </v-btn>
      </v-col>
    </v-row>

    <div v-if="walletStore.loading" class="text-center">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>

    <div v-else-if="walletStore.currentWallet">
      <v-row>
        <v-col cols="12">
          <v-card elevation="2" class="mb-6">
            <v-card-item>
              <v-card-title class="text-h4">{{ walletStore.currentWallet.name }}</v-card-title>
              <v-card-subtitle class="text-h6">
                 {{ walletStore.currentWallet.currency?.symbol }} {{ Number(walletStore.currentWallet.balance).toFixed(2) }}
                 ({{ walletStore.currentWallet.currency?.code }})
              </v-card-subtitle>
            </v-card-item>
          </v-card>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12">
          <v-card variant="outlined">
            <v-card-title>Transactions</v-card-title>
            <v-data-table
              :headers="headers"
              :items="walletStore.transactions"
              :loading="walletStore.loading"
            >
              <template v-slot:item.type="{ item }">
                 <v-chip :color="item.type === 'credit' ? 'success' : 'error'" size="small" label>
                   {{ item.type.toUpperCase() }}
                 </v-chip>
              </template>
              
              <template v-slot:item.amount="{ item }">
                <span :class="item.type === 'credit' ? 'text-success' : 'text-error'" class="font-weight-bold">
                  {{ item.type === 'credit' ? '+' : '-' }} {{ Number(item.amount).toFixed(2) }}
                </span>
              </template>

              <template v-slot:item.created_at="{ item }">
                {{ new Date(item.created_at).toLocaleString() }}
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>
    </div>
    
    <v-alert v-else type="error">Wallet not found</v-alert>

  </v-container>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useWalletStore } from '@/modules/Wallet/store';

const route = useRoute();
const walletStore = useWalletStore();

const headers = [
  { title: 'Date', key: 'created_at' },
  { title: 'Description', key: 'description' },
  { title: 'Type', key: 'type' },
  { title: 'Amount', key: 'amount', align: 'end' as const },
];

onMounted(() => {
  const id = Number(route.params.id);
  if (id) {
    walletStore.fetchWalletDetails(id);
  }
});
</script>
