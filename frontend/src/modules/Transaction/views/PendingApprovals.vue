<template>
  <div class="fill-height pa-6 w-100">
    <div class="d-flex justify-space-between align-center mb-6">
      <h1 class="text-h5 font-weight-regular text-grey-darken-1">Pending Approvals - Korporatio</h1>
    </div>

    <!-- Filters -->
    <ApprovalFilter
      @apply-filter="handleFilter"
      class="mb-6"
    />

    <!-- Transactions Table -->
    <ApprovalTable
      :loading="loading"
      :items="transactions"
      :total-items="totalItems"
      :page="page"
      :items-per-page="itemsPerPage"
      @update:options="handleOptionsUpdate"
      @update:page="page = $event"
      @approve="approve"
      @reject="openRejectDialog"
      @view-details="handleViewDetails"
    />

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
          <v-btn color="error" text @click="confirmReject" :loading="!!processingId" :disabled="!rejectionReason">Reject</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { transactionApi } from '@/modules/Transaction/api';
import type { Transaction, TransactionFilters } from '@/modules/Transaction/api';
import { useNotificationStore } from '@/shared/stores/notification';
import ApprovalFilter from '@/modules/Transaction/components/ApprovalFilter.vue';
import ApprovalTable from '@/modules/Transaction/components/ApprovalTable.vue';

const router = useRouter();
const notification = useNotificationStore();

// State
const loading = ref(false);
const transactions = ref<Transaction[]>([]);
const totalItems = ref(0);
const page = ref(1);
const itemsPerPage = ref(10);
const filters = ref<TransactionFilters>({});
const sortBy = ref('created_at');
const sortDesc = ref(true);

const processingId = ref<number | null>(null);
const rejectDialog = ref(false);
const selectedTransaction = ref<Transaction | null>(null);
const rejectionReason = ref('');

// Methods
async function fetchTransactions() {
  loading.value = true;
  try {
    const payload: TransactionFilters = {
      ...filters.value,
      page: page.value,
      per_page: itemsPerPage.value,
      status_id: 1, // Force Pending status
      sort_by: sortBy.value,
      sort_dir: sortDesc.value ? 'desc' : 'asc',
    };

    const response = await transactionApi.getTransactions(payload);
    // Handle both paginated and non-paginated potential structures (safety)
    const data = (response as any).data.data || (response as any).data || [];
    const meta = (response as any).data.meta || (response as any).meta;

    transactions.value = data;
    totalItems.value = meta ? meta.total : data.length;
  } catch (error) {
    console.error('Failed to fetch pending transactions:', error);
    notification.error('Failed to load pending transactions');
  } finally {
    loading.value = false;
  }
}

function handleFilter(newFilters: TransactionFilters) {
  filters.value = newFilters;
  page.value = 1; // Reset to first page
  fetchTransactions();
}

function handleOptionsUpdate(options: { page: number; itemsPerPage: number; sortBy: any[] }) {
  page.value = options.page;
  
  if (options.sortBy && options.sortBy.length > 0) {
      sortBy.value = options.sortBy[0].key;
      sortDesc.value = options.sortBy[0].order === 'desc';
  } else {
      sortBy.value = 'created_at';
      sortDesc.value = true;
  }
  
  fetchTransactions();
}

function handleViewDetails(item: Transaction) {
  router.push(`/approvals/${item.id}`);
}

const approve = async (transaction: Transaction) => {
    if (!confirm('Are you sure you want to approve this transfer?')) return;
    
    processingId.value = transaction.id;
    try {
        await transactionApi.approveTransfer(transaction.id);
        notification.success('Transfer approved successfully');
        await fetchTransactions();
    } catch (e: any) {
        notification.error(e.response?.data?.message || 'Failed to approve');
    } finally {
        processingId.value = null;
    }
};

const openRejectDialog = (transaction: Transaction) => {
    selectedTransaction.value = transaction;
    rejectionReason.value = '';
    rejectDialog.value = true;
};

const confirmReject = async () => {
    if (!selectedTransaction.value) return;
    
    processingId.value = selectedTransaction.value.id;
    try {
        await transactionApi.rejectTransfer(selectedTransaction.value.id, rejectionReason.value);
        notification.notify('Transfer rejected', 'info'); 
        rejectDialog.value = false;
        await fetchTransactions();
    } catch (e: any) {
        notification.error(e.response?.data?.message || 'Failed to reject');
    } finally {
        processingId.value = null;
    }
};



// Initial Fetch
</script>

<style scoped>
</style>
