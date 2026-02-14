<template>
    <v-container fluid class="fill-height align-start pa-6">
        <v-row justify="center">
            <v-col cols="12" md="8" lg="6">
                <v-card class="rounded-0" elevation="0" border>
                    <v-card-title class="pa-6 pb-4 text-h5 font-weight-bold">
                        Transaction Details
                    </v-card-title>
                    <v-divider></v-divider>

                    <v-card-text class="pa-6" v-if="!loading && transaction">
                        <v-form>
                            <v-row>
                                <!-- Reference -->
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Reference</div>
                                    <v-text-field
                                        :model-value="transaction.reference"
                                        variant="outlined"
                                        density="compact"
                                        readonly
                                        bg-color="grey-lighten-4"
                                    ></v-text-field>
                                </v-col>

                                <!-- Amount -->
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Amount</div>
                                    <v-text-field
                                        :model-value="formatAmount(transaction)"
                                        variant="outlined"
                                        density="compact"
                                        readonly
                                        bg-color="grey-lighten-4"
                                        :class="transaction.type === 'credit' ? 'text-success' : 'text-error'"
                                    ></v-text-field>
                                </v-col>

                                 <!-- Type -->
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Type</div>
                                    <v-text-field
                                        :model-value="transaction.type"
                                        variant="outlined"
                                        density="compact"
                                        readonly
                                        bg-color="grey-lighten-4"
                                        class="text-capitalize"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12" md="6">
                                     <div class="text-subtitle-2 font-weight-bold mb-2">Related Wallet</div>
                                    <v-text-field
                                        :model-value="getWalletName(transaction)"
                                        variant="outlined"
                                        density="compact"
                                        readonly
                                        bg-color="grey-lighten-4"
                                    ></v-text-field>
                                </v-col>

                                <!-- Date -->
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Date</div>
                                    <v-text-field
                                        :model-value="new Date(transaction.created_at).toLocaleString()"
                                        variant="outlined"
                                        density="compact"
                                        readonly
                                        bg-color="grey-lighten-4"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                            
                            <v-divider class="mt-6 mb-6"></v-divider>

                            <!-- Actions -->
                            <div class="d-flex justify-end gap-2">
                                <v-btn
                                    variant="text"
                                    color="grey-darken-1"
                                    class="px-6 text-capitalize"
                                    @click="router.back()"
                                >
                                    Back
                                </v-btn>
                            </div>
                        </v-form>
                    </v-card-text>
                    
                    <v-card-text v-else-if="loading" class="pa-12 d-flex justify-center">
                         <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    </v-card-text>
                    
                    <v-card-text v-else class="pa-6">
                        <v-alert type="error" variant="tonal" class="rounded-0">
                            Transaction not found or could not be loaded.
                        </v-alert>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTransactionStore } from '../store';
import type { Transaction } from '../api';

const route = useRoute();
const router = useRouter();
const transactionStore = useTransactionStore();

const loading = ref(true);
const transaction = ref<Transaction | null>(null);

onMounted(async () => {
    const id = Number(route.params.id);
    if (!id) return;
    
    try {
        await transactionStore.fetchTransaction(id);
        transaction.value = transactionStore.currentTransaction;
    } catch (e) {
        // Error handled in store
    } finally {
        loading.value = false;
    }
});

function formatAmount(item: Transaction) {
    if(!item) return '';
    const symbol = item.to_wallet?.currency?.symbol || item.from_wallet?.currency?.symbol || '$';
    return `${item.type === 'debit' ? '-' : '+'}${symbol}${Number(item.amount).toLocaleString()}`;
}

function getWalletName(item: Transaction) {
    const wallet = item.to_wallet;
    if (wallet?.is_external && wallet.address) {
        if (wallet.address.length > 13) {
             return `${wallet.address.substring(0, 8)}...${wallet.address.substring(wallet.address.length - 6)}`;
        }
        return wallet.address;
    }
  return item.to_wallet?.name || item.wallet?.name || 'Unknown Wallet';
}

</script>
