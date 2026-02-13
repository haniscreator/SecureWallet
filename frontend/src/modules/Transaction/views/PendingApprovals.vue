<template>
  <div class="pending-approvals-container">
    <div class="page-header d-flex justify-space-between align-center mb-4">
      <h1 class="text-h4 font-weight-bold primary--text">Pending Approvals</h1>
    </div>

    <v-card class="elevation-2 rounded-lg">
      <v-data-table
        :headers="headers"
        :items="transactions"
        :loading="loading"
        loading-text="Loading pending transactions..."
      >
        <template v-slot:item.amount="{ item }">
          <span class="font-weight-bold warning--text">
            {{ formatCurrency(item.amount, item.from_wallet?.currency?.symbol) }}
          </span>
        </template>
        
        <template v-slot:item.from_wallet="{ item }">
            {{ item.from_wallet?.name }}
        </template>

        <template v-slot:item.to="{ item }">
            <span v-if="item.external_wallet">
                External: {{ item.external_wallet?.address }}
            </span>
            <span v-else>
                Internal
            </span>
        </template>

        <template v-slot:item.created_at="{ item }">
            {{ formatDate(item.created_at) }}
        </template>

        <template v-slot:item.actions="{ item }">
          <div class="d-flex">
            <v-btn
              color="success"
              variant="text"
              size="small"
              prepend-icon="mdi-check"
              @click="approve(item)"
              :loading="processingId === item.id"
              :disabled="!!processingId"
            >
              Approve
            </v-btn>
            <v-btn
              color="error"
              variant="text"
              size="small"
              prepend-icon="mdi-close"
              @click="openRejectDialog(item)"
              :disabled="!!processingId"
            >
              Reject
            </v-btn>
          </div>
        </template>
        
        <template v-slot:no-data>
            <div class="pa-4 text-center">
                <v-icon size="large" color="grey lighten-1">mdi-check-circle-outline</v-icon>
                <div class="subtitle-1 grey--text mt-2">No pending approvals found.</div>
            </div>
        </template>
      </v-data-table>
    </v-card>

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
          <v-btn color="error" text @click="confirmReject" :loading="processingId === selectedTransaction?.id" :disabled="!rejectionReason">Reject</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
        {{ snackbar.message }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue';
import { transactionApi } from '@/modules/Transaction/api';

const loading = ref(false);
const transactions = ref<any[]>([]);
const processingId = ref<number | null>(null);
const rejectDialog = ref(false);
const selectedTransaction = ref<any>(null);
const rejectionReason = ref('');

const snackbar = reactive({
    show: false,
    message: '',
    color: 'success'
});

const headers: any[] = [
  { title: 'Reference', key: 'reference', align: 'start' },
  { title: 'From Wallet', key: 'from_wallet', align: 'start' },
  { title: 'To', key: 'to', align: 'start' },
  { title: 'Amount', key: 'amount', align: 'end' },
  { title: 'Date', key: 'created_at', align: 'end' },
  { title: 'Actions', key: 'actions', align: 'center', sortable: false },
];

onMounted(() => {
  fetchPendingTransactions();
});

const fetchPendingTransactions = async () => {
  loading.value = true;
  try {
     // 1 = Pending
     const response = await transactionApi.getTransactions({ status_id: 1, sort_by: 'created_at', sort_dir: 'desc' });
     const data = (response as any).data.data || (response as any).data;
     transactions.value = data; 
  } catch (e) {
    console.error(e);
    showSnackbar('Failed to load pending transactions', 'error');
  } finally {
    loading.value = false;
  }
};

const approve = async (transaction: any) => {
    if (!confirm('Are you sure you want to approve this transfer?')) return;
    
    processingId.value = transaction.id;
    try {
        await transactionApi.approveTransfer(transaction.id);
        showSnackbar('Transfer approved successfully', 'success');
        await fetchPendingTransactions();
    } catch (e: any) {
        showSnackbar(e.response?.data?.message || 'Failed to approve', 'error');
    } finally {
        processingId.value = null;
    }
};

const openRejectDialog = (transaction: any) => {
    selectedTransaction.value = transaction;
    rejectionReason.value = '';
    rejectDialog.value = true;
};

const confirmReject = async () => {
    if (!selectedTransaction.value) return;
    
    processingId.value = selectedTransaction.value.id;
    try {
        await transactionApi.rejectTransfer(selectedTransaction.value.id, rejectionReason.value);
        showSnackbar('Transfer rejected', 'info');
        rejectDialog.value = false;
        await fetchPendingTransactions();
    } catch (e: any) {
        showSnackbar(e.response?.data?.message || 'Failed to reject', 'error');
    } finally {
        processingId.value = null;
    }
};

const showSnackbar = (msg: string, color: string) => {
    snackbar.message = msg;
    snackbar.color = color;
    snackbar.show = true;
};

const formatCurrency = (amount: number, symbol: string = '$') => {
    return symbol + ' ' + parseFloat(String(amount)).toFixed(2);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleString();
};
</script>

<style scoped>
.pending-approvals-container {
    padding: 24px;
}
</style>
