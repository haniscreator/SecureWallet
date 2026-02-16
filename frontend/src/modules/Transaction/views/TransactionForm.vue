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
                                <!-- From Wallet -->
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">From</div>
                                    <v-text-field
                                        :model-value="getWalletName(transaction, 'from')"
                                        variant="outlined"
                                        density="compact"
                                        readonly
                                        bg-color="grey-lighten-4"
                                    ></v-text-field>
                                </v-col>

                                <!-- To Wallet -->
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">To</div>
                                    <v-text-field
                                        :model-value="getWalletName(transaction, 'to')"
                                        variant="outlined"
                                        density="compact"
                                        readonly
                                        bg-color="grey-lighten-4"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row>
                                <!-- Amount -->
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Amount</div>
                                    <v-text-field
                                        :model-value="formatAmount(transaction)"
                                        variant="outlined"
                                        density="compact"
                                        readonly
                                        bg-color="grey-lighten-4"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row>
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

                                <!-- Status -->
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Status</div>
                                    <v-text-field
                                        :model-value="transaction.status?.name || 'Unknown'"
                                        variant="outlined"
                                        density="compact"
                                        readonly
                                        bg-color="grey-lighten-4"
                                        class="text-capitalize"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

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
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useTransactionForm } from '@/modules/Transaction/composables/useTransactionForm';

const router = useRouter();

const {
    loading,
    transaction,
    init,
    formatAmount,
    getWalletName
} = useTransactionForm();

onMounted(async () => {
    await init();
});
</script>
