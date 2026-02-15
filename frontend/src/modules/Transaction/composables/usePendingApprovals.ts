import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { transactionApi } from '@/modules/Transaction/api';
import type { Transaction, TransactionFilters } from '@/modules/Transaction/api';
import { useNotificationStore } from '@/shared/stores/notification';

export function usePendingApprovals() {
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

    return {
        // State
        loading, transactions, totalItems, page, itemsPerPage, processingId, rejectDialog, rejectionReason,
        // Methods
        fetchTransactions, handleFilter, handleOptionsUpdate, handleViewDetails, approve, openRejectDialog, confirmReject
    };
}
