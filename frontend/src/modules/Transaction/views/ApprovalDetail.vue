<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="rounded-0" elevation="0" border>
          <v-card-title class="pa-6 pb-4 text-h5 font-weight-bold">
            Approval Details
          </v-card-title>
          <v-divider></v-divider>
          
          <v-card-text class="pa-6">
             <div v-if="loading" class="d-flex justify-center pa-12">
                 <v-progress-circular indeterminate color="primary"></v-progress-circular>
             </div>

             <v-form v-else-if="transaction">
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

                    <!-- From/To Info -->
                    <v-col cols="12" md="6">
                         <div class="text-subtitle-2 font-weight-bold mb-2">From Wallet</div>
                        <v-text-field
                            :model-value="transaction.from_wallet?.name || '-'"
                            variant="outlined"
                            density="compact"
                            readonly
                            bg-color="grey-lighten-4"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="6">
                         <div class="text-subtitle-2 font-weight-bold mb-2">To Wallet</div>
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

                <div class="d-flex justify-end">
                    <v-btn
                        color="success"
                        variant="elevated"
                        class="px-6 text-capitalize mr-4"
                        @click="approve"
                        :loading="processing"
                    >
                        Approve
                    </v-btn>

                    <v-btn
                        color="error"
                        variant="elevated"
                        class="px-6 text-capitalize mr-4"
                        @click="openRejectDialog"
                        :disabled="processing"
                    >
                        Reject
                    </v-btn>

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

            <v-alert v-else type="error" variant="tonal" class="rounded-0">
                Transaction not found.
            </v-alert>
          </v-card-text>
        </v-card>
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

    <ConfirmDialog
        v-model="confirmDialog.isOpen"
        :title="confirmDialog.title"
        :message="confirmDialog.message"
        :loading="processing"
        @confirm="handleConfirmApprove"
    />
  </v-container>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useApprovalDetail } from '@/modules/Transaction/composables/useApprovalDetail';
import ConfirmDialog from '@/shared/components/ConfirmDialog.vue';

const router = useRouter();

const {
    loading,
    transaction,
    processing,
    rejectDialog,
    rejectionReason,
    confirmDialog,
    init,
    formatAmount,
    getWalletName,
    approve,
    openRejectDialog,
    confirmReject,
    handleConfirmApprove
} = useApprovalDetail();

onMounted(async () => {
    await init();
});
</script>
