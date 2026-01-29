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
            @click="openDialog()"
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
                    <div class="d-flex gap-2">
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
                                @click="openDialog(item)"
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

    <!-- Form Dialog -->
    <WalletFormDialog
        v-model="dialog"
        :item="editedItem"
        :loading="walletStore.loading"
        @save="saveItem"
    />

  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useWalletStore } from '../store';
import type { Wallet } from '../api';
import WalletFormDialog from '../components/WalletFormDialog.vue';

const walletStore = useWalletStore();

const headers = [
    { title: 'ID', key: 'id', align: 'start' as const },
    { title: 'Name', key: 'name', align: 'start' as const },
    { title: 'Currency', key: 'currency', align: 'start' as const },
    { title: 'Balance', key: 'balance', align: 'end' as const },
    { title: 'Status', key: 'status', align: 'start' as const },
    { title: 'Actions', key: 'actions', sortable: false, align: 'end' as const },
];

const dialog = ref(false);
const editedItem = ref<Wallet | null>(null);

onMounted(() => {
    walletStore.fetchWallets();
});

function openDialog(item?: Wallet) {
    editedItem.value = item || null;
    dialog.value = true;
}

async function saveItem(payload: any) {
    if (editedItem.value) {
        await walletStore.updateWallet(editedItem.value.id, payload);
    } else {
        await walletStore.createWallet(payload);
    }
    dialog.value = false;
}
</script>
