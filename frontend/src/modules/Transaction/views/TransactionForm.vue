<template>
    <v-container fluid class="fill-height align-start pa-6">
        <v-row justify="center">
            <v-col cols="12" md="8" lg="6">
                <!-- Header -->
                <div class="d-flex align-center mb-6">
                    <v-btn
                        icon="mdi-arrow-left"
                        variant="text"
                        class="mr-4"
                        @click="router.back()"
                    ></v-btn>
                    <h1 class="text-h4 font-weight-bold">Transaction Details</h1>
                </div>

                <v-card class="rounded-xl pa-4" border elevation="0" v-if="!loading && transaction">
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <!-- Reference -->
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Reference</div>
                                    <v-text-field
                                        :model-value="transaction.reference"
                                        variant="outlined"
                                        density="comfortable"
                                        readonly
                                    ></v-text-field>
                                </v-col>

                                <!-- Amount -->
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Amount</div>
                                    <v-text-field
                                        :model-value="formatAmount(transaction)"
                                        variant="outlined"
                                        density="comfortable"
                                        readonly
                                        :class="transaction.type === 'credit' ? 'text-success' : 'text-error'"
                                    ></v-text-field>
                                </v-col>

                                 <!-- Type -->
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Type</div>
                                    <v-text-field
                                        :model-value="transaction.type"
                                        variant="outlined"
                                        density="comfortable"
                                        readonly
                                        class="text-capitalize"
                                    ></v-text-field>
                                </v-col>

                                <!-- Wallet info will depend on what's available in API response.
                                     Assuming basic fields for now. -->
                                <!-- Wallet Removed as per request -->

                                <v-col cols="12" md="6">
                                     <div class="text-subtitle-2 font-weight-bold mb-2">Related Wallet</div>
                                    <v-text-field
                                        :model-value="transaction.to_wallet?.name || '-'"
                                        variant="outlined"
                                        density="comfortable"
                                        readonly
                                    ></v-text-field>
                                </v-col>

                                <!-- Date -->
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Date</div>
                                    <v-text-field
                                        :model-value="new Date(transaction.created_at).toLocaleString()"
                                        variant="outlined"
                                        density="comfortable"
                                        readonly
                                    ></v-text-field>
                                </v-col>

                                <!-- Description (if any)
                                <v-col cols="12" v-if="transaction.description">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Description</div>
                                    <v-textarea
                                        :model-value="transaction.description"
                                        variant="outlined"
                                        readonly
                                        auto-grow
                                        rows="2"
                                    ></v-textarea>
                                </v-col>
                                -->

                                <!-- Actions -->
                                <v-col cols="12" class="d-flex justify-end pt-4">
                                    <v-btn
                                        variant="outlined"
                                        color="grey-darken-1"
                                        class="px-6 text-capitalize"
                                        size="large"
                                        @click="router.back()"
                                    >
                                        Back
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
                
                <div v-else-if="loading" class="d-flex justify-center pa-12">
                     <v-progress-circular indeterminate color="primary"></v-progress-circular>
                </div>
                
                <v-alert v-else type="error" variant="tonal" class="rounded-xl">
                    Transaction not found or could not be loaded.
                </v-alert>

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
</script>
