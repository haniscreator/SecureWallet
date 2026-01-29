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
              <div class="d-flex justify-space-between align-start">
                <div>
                    <v-card-title class="text-h4">{{ walletStore.currentWallet.name }}</v-card-title>
                    <v-card-subtitle class="text-h6">
                        {{ walletStore.currentWallet.currency?.symbol }} {{ Number(walletStore.currentWallet.balance).toFixed(2) }}
                        ({{ walletStore.currentWallet.currency?.code }})
                    </v-card-subtitle>
                </div>
                <v-btn color="secondary" prepend-icon="mdi-account-multiple-plus" @click="showAssignDialog = true">
                    Assign Users
                </v-btn>
              </div>
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

    <AssignUserDialog 
      v-if="walletStore.currentWallet"
      v-model="showAssignDialog" 
      :wallet-id="walletStore.currentWallet.id"
      @assigned="onAssigned"
    />

    <v-snackbar v-model="showSnackbar" color="success">
      Users assigned successfully
    </v-snackbar>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useWalletStore } from '@/modules/Wallet/store';
import AssignUserDialog from '@/modules/Wallet/components/AssignUserDialog.vue';

const route = useRoute();
const walletStore = useWalletStore();
const showAssignDialog = ref(false);
const showSnackbar = ref(false);

const headers = [
  { title: 'Date', key: 'created_at' },
  { title: 'Description', key: 'description' },
  { title: 'Type', key: 'type' },
  { title: 'Amount', key: 'amount', align: 'end' as const },
];

function onAssigned() {
  showSnackbar.value = true;
}

onMounted(() => {
  const id = Number(route.params.id);
  if (id) {
    walletStore.fetchWalletDetails(id);
  }
});
</script>
