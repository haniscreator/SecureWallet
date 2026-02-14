<template>
  <div class="pending-approvals-container">
    <div class="page-header d-flex justify-space-between align-center mb-4">
      <h1 class="text-h4 font-weight-bold primary--text">Pending Approvals</h1>
    </div>

    <v-card class="elevation-2 rounded-lg" width="100%">
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
            <span v-if="item.to_wallet?.is_external">
                {{ truncateAddress(item.to_wallet?.address) }}
            </span>
            <span v-else>
                {{ item.to_wallet?.name || 'Internal' }}
            </span>
        </template>

        <template v-slot:item.created_at="{ item }">
            {{ formatDate(item.created_at) }}
        </template>

        <template v-slot:item.actions="{ item }">
          <div class="d-flex align-center justify-end">
            <v-btn
              color="success"
              variant="text"
              size="small"
              prepend-icon="mdi-check"
              class="mr-2"
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
              class="mr-2"
              @click="openRejectDialog(item)"
              :disabled="!!processingId"
            >
              Reject
            </v-btn>
            <v-btn
              color="info"
              variant="text"
              size="small"
              prepend-icon="mdi-eye"
              @click="viewDetails(item)"
              :disabled="!!processingId"
            >
              View
            </v-btn>
          </div>
        </template>
        
        <template v-slot:no-data>
            <div class="pa-4 text-center" style="width: 100%;">
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
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { transactionApi } from '@/modules/Transaction/api';
import { useNotificationStore } from '@/shared/stores/notification';

const router = useRouter();
const notification = useNotificationStore();

const loading = ref(false);
const transactions = ref<any[]>([]);
const processingId = ref<number | null>(null);
const rejectDialog = ref(false);
const selectedTransaction = ref<any>(null);
const rejectionReason = ref('');

const headers: any[] = [
  { title: 'Reference', key: 'reference', align: 'start' },
  { title: 'From', key: 'from_wallet', align: 'start' },
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
    notification.error('Failed to load pending transactions');
  } finally {
    loading.value = false;
  }
};

const viewDetails = (item: any) => {
    router.push(`/transactions/${item.id}`);
};

const approve = async (transaction: any) => {
    if (!confirm('Are you sure you want to approve this transfer?')) return;
    
    processingId.value = transaction.id;
    try {
        await transactionApi.approveTransfer(transaction.id);
        notification.success('Transfer approved successfully');
        await fetchPendingTransactions();
    } catch (e: any) {
        notification.error(e.response?.data?.message || 'Failed to approve');
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
        notification.notify('Transfer rejected', 'info'); // Using 'info' or specific 'warning' color if preferred, but user said "notification"
        rejectDialog.value = false;
        await fetchPendingTransactions();
    } catch (e: any) {
        notification.error(e.response?.data?.message || 'Failed to reject');
    } finally {
        processingId.value = null;
    }
};

const formatCurrency = (amount: number, symbol: string = '$') => {
    return symbol + ' ' + parseFloat(String(amount)).toFixed(2);
};

const formatDate = (date: string) => {
    // Only date, no time
    return new Date(date).toLocaleDateString();
};

const truncateAddress = (address: string) => {
    if (!address) return '';
    if (address.length <= 13) return address;
    return `${address.substring(0, 6)}...${address.substring(address.length - 4)}`;
};
</script>

<style scoped>
.pending-approvals-container {
    padding: 24px;
    width: 100%;
}
</style>
