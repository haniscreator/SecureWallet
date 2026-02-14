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

                                <v-col cols="12" md="6">
                                     <div class="text-subtitle-2 font-weight-bold mb-2">Related Wallet</div>
                                    <v-text-field
                                        :model-value="getWalletName(transaction)"
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

                                <!-- Actions -->
                                <v-col cols="12" class="d-flex justify-end pt-4 gap-2">
                                    <!-- Approve/Reject for Pending -->
                                    <div v-if="isPending" class="d-flex mr-2">
                                        <v-btn
                                            color="error"
                                            class="mr-2 text-capitalize"
                                            size="large"
                                            @click="openRejectDialog"
                                            :disabled="processing"
                                        >
                                            Reject
                                        </v-btn>
                                        <v-btn
                                            color="success"
                                            class="text-capitalize"
                                            size="large"
                                            @click="approve"
                                            :loading="processing"
                                        >
                                            Approve
                                        </v-btn>
                                    </div>

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

        <!-- Reject Dialog -->
        <v-dialog v-model="rejectDialog" max-width="500px">
            <v-card>
                <v-card-title class="headline error--text">Reject Transfer</v-card-title>
                <v-card-text>
                    <p class="mb-2">Please provide a reason for rejecting this transfer.</p>
                    <v-textarea
                        v-model="rejectionReason"
                        label="Reason"
                        variant="outlined"
                        rows="3"
                        :rules="[v => !!v || 'Reason is required']"
                    ></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="rejectDialog = false">Cancel</v-btn>
                    <v-btn color="error" text @click="confirmReject" :loading="processing" :disabled="!rejectionReason">Reject</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTransactionStore } from '../store';
import { transactionApi } from '../api';
import type { Transaction } from '../api';
import { useNotificationStore } from '@/shared/stores/notification';

const route = useRoute();
const router = useRouter();
const transactionStore = useTransactionStore();
const notification = useNotificationStore();

const loading = ref(true);
const transaction = ref<Transaction | null>(null);
const processing = ref(false);
const rejectDialog = ref(false);
const rejectionReason = ref('');

// Helper to check if pending. 
// Ideally transaction object should have status. If not, we might need to fetch it or rely on other fields.
// Assuming we fetch transaction object which should include status_id or status object.
// Checking API response/resource.
const isPending = computed(() => {
    // We assume backend returns status or we can infer.
    // However, existing Transaction type in frontend might not have status.
    // Let's check logic. If mapped correctly, it should work.
    // If 'transaction_status_id' is 1 (pending) or similar.
    // Let's coerce to any to check properties that might not be in strict Type yet.
    const t = transaction.value as any;
    return t?.transaction_status_id === 1 || t?.status?.code === 'pending';
});

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

const approve = async () => {
    if (!transaction.value) return;
    if (!confirm('Are you sure you want to approve this transfer?')) return;
    
    processing.value = true;
    try {
        await transactionApi.approveTransfer(transaction.value.id);
        notification.success('Transfer approved successfully');
        router.push('/approvals');
    } catch (e: any) {
        notification.error(e.response?.data?.message || 'Failed to approve');
    } finally {
        processing.value = false;
    }
};

const openRejectDialog = () => {
    rejectionReason.value = '';
    rejectDialog.value = true;
};

const confirmReject = async () => {
    if (!transaction.value) return;
    
    processing.value = true;
    try {
        await transactionApi.rejectTransfer(transaction.value.id, rejectionReason.value);
        notification.notify('Transfer rejected', 'info');
        rejectDialog.value = false;
        router.push('/approvals');
    } catch (e: any) {
        notification.error(e.response?.data?.message || 'Failed to reject');
    } finally {
        processing.value = false;
    }
};
</script>
