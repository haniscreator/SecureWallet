<template>
  <v-container fluid class="fill-height align-start pa-6 bg-grey-lighten-4">
    <v-row>
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center mb-6">
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Wallets</h1>
          <v-btn
            color="primary"
            prepend-icon="mdi-plus"
            elevation="2"
            class="text-capitalize"
            :to="{ name: 'WalletCreate' }"
          >
            Add Wallet
          </v-btn>
        </div>

        <v-card class="rounded-xl" elevation="0" border>
            <v-data-table
                :headers="headers"
                :items="walletStore.wallets"
                :loading="walletStore.loading"
                hover
                class="pa-2"
            >
                <!-- Currency Column -->
                <template v-slot:item.currency="{ item }">
                    <span class="font-weight-medium">{{ item.currency?.code || 'N/A' }}</span>
                </template>

                 <!-- Balance Column -->
                <template v-slot:item.balance="{ item }">
                    <span :class="Number(item.balance) < 0 ? 'text-error' : 'text-success'" class="font-weight-bold">
                        {{ item.currency?.symbol || '' }}{{ Number(item.balance).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                    </span>
                </template>

                <!-- Status Column -->
                <template v-slot:item.status="{ item }">
                    <v-chip
                        :color="item.status ? 'success' : 'grey'"
                        size="small"
                        variant="tonal"
                        class="font-weight-medium"
                    >
                        {{ item.status ? 'Active' : 'Inactive' }}
                    </v-chip>
                </template>

                <!-- Actions Column -->
                <template v-slot:item.actions="{ item }">
                    <div class="d-flex gap-2 justify-center">
                        <v-tooltip text="View Details" location="top">
                          <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                icon
                                variant="text"
                                size="small"
                                color="info"
                                :to="{ name: 'WalletDetails', params: { id: item.id } }"
                            >
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                          </template>
                        </v-tooltip>

                        <v-tooltip text="Edit Wallet" location="top">
                          <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                icon
                                variant="text"
                                size="small"
                                color="primary"
                                :to="{ name: 'WalletEdit', params: { id: item.id } }"
                            >
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                          </template>
                        </v-tooltip>
                    </div>
                </template>
                
                <!-- No Data -->
                <template v-slot:no-data>
                    <div class="pa-4 text-center text-grey">
                        No wallets found.
                    </div>
                </template>
            </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useWalletStore } from '../store';

const walletStore = useWalletStore();

const headers = [
    { title: 'ID', key: 'id', align: 'start' as const },
    { title: 'Name', key: 'name', align: 'start' as const },
    { title: 'Currency', key: 'currency', align: 'start' as const },
    { title: 'Balance', key: 'balance', align: 'end' as const },
    { title: 'Status', key: 'status', align: 'start' as const },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' as const },
];

onMounted(() => {
    walletStore.fetchWallets();
});
</script>
